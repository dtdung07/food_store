<?php
declare(strict_types=1);

require_once APP_ROOT . '/models/HangHoaModel.php';

class HoaDonModel
{
    private const EPSILON = 0.0001;

    private PDO $pdo;
    private HangHoaModel $hangHoaModel;

    public function __construct()
    {
        $this->pdo = db();
        $this->hangHoaModel = new HangHoaModel();
    }

    //Xử lý mã vạch
    public function resolveBarcode(string $barcode): array
    {
        //Xóa khoảng trắng ở đầu và cuối
        $rawBarcode = trim($barcode);
        //Xóa các ký tự không phải số
        $numericBarcode = preg_replace('/\D+/', '', $rawBarcode) ?? '';

        //Kiểm tra mã vạch rỗng
        if ($numericBarcode === '' && $rawBarcode === '') {
            throw new RuntimeException('Vui lòng nhập mã vạch.');
        }

        //Kiểm tra mã vạch tem cân (13 số bắt đầu bằng 20)
        if (preg_match('/^20\d{11}$/', $numericBarcode) === 1) {
            if (!$this->isValidEan13($numericBarcode)) {
                throw new RuntimeException("Mã tem cân '{$numericBarcode}' không hợp lệ.");
            }

            //Lấy mã tem cân
            $scaleCode = substr($numericBarcode, 2, 5);
            $product = $this->hangHoaModel->findByScaleCode($scaleCode);

            if (!$product) {
                throw new RuntimeException("Không tìm thấy mã tem cân cho '{$scaleCode}'.");
            }

            //Lấy đơn giá từ CSDL
            $unitPrice = (float) $product['gia_ban'];
            if ($unitPrice <= 0) {
                throw new RuntimeException('Đơn giá hàng cân không hợp lệ.');
            }

            //Lấy trọng lượng (giá trị trên tem cân chia 1000 để chuyển đổi từ g sang kg)
            $weight = (float) substr($numericBarcode, 7, 5) / 1000;
            if ($weight <= 0) {
                throw new RuntimeException('Trọng lượng tem cân không hợp lệ.');
            }

            //Tính tổng tiền (làm tròn về 0 chữ số thập phân cho tiền VND)
            $amount = round($weight * $unitPrice, 0);

            //Trả về kết quả
            return [
                'loai_ma_vach' => 'TEM_CAN',
                'ma_hang_hoa' => $product['ma_hang_hoa'],
                'ten_hang_hoa' => $product['ten_hang_hoa'],
                'don_vi_tinh' => $product['don_vi_tinh'] ?? '',
                'so_luong' => $weight,
                'trong_luong' => $weight,
                'gia_ban' => $unitPrice,
                'tong_tien' => $amount,
                'ma_vach_quet' => $numericBarcode,
            ];
        }

        //Kiểm tra mã vạch từ NSX
        $lookupBarcode = ctype_digit($rawBarcode) ? $numericBarcode : $rawBarcode;
        $product = $this->hangHoaModel->findByBarcode($lookupBarcode);

        if (!$product) {
            throw new RuntimeException("Mã vạch '{$lookupBarcode}' không tìm thấy.");
        }

        //Lấy đơn giá
        $unitPrice = (float) $product['gia_ban'];

        //Trả về kết quả
        return [
            'loai_ma_vach' => 'CO_DINH',
            'ma_hang_hoa' => $product['ma_hang_hoa'],
            'ten_hang_hoa' => $product['ten_hang_hoa'],
            'don_vi_tinh' => $product['don_vi_tinh'] ?? '',
            'so_luong' => 1.0,
            'trong_luong' => null,
            'gia_ban' => $unitPrice,
            'tong_tien' => $unitPrice,
            'ma_vach_quet' => $lookupBarcode,
        ];
    }

    //Thanh toán hóa đơn
    public function checkout(array $payload, ?string $maNhanVien): string
    {
        $this->pdo->beginTransaction();

        try {
            $items = $this->normalizeAndRepriceItems($payload['items'] ?? []);

            //Kiểm tra giỏ hàng trống
            if ($items === []) {
                throw new RuntimeException('Giỏ hàng trống.');
            }

            //Tính tổng tiền
            $total = 0.0;
            foreach ($items as $item) {
                $total += (float) $item['tong_tien'];
            }

            //Lấy tiền khách đưa
            $customerPaid = $this->normalizeNumber($payload['tien_khach_dua'] ?? 0);
            //Kiểm tra tiền khách đưa
            if ($customerPaid + self::EPSILON < $total) {
                throw new RuntimeException('Tiền khách đưa chưa đủ để thanh toán.');
            }

            //Tạo hóa đơn
            $invoiceId = $this->generateIdForUpdate();
            $this->insertInvoice($invoiceId, $total, $customerPaid, $payload, $maNhanVien);

            //Thêm chi tiết hóa đơn và trừ tồn kho theo FIFO
            foreach ($items as $item) {
                $detailId = $this->insertInvoiceLine($invoiceId, $item);
                $this->deductFifoShelfThenWarehouse(
                    (string) $item['ma_hang_hoa'],
                    (float) $item['so_luong'],
                    $detailId
                );
            }

            $this->pdo->commit();

            return $invoiceId;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    //Tìm hóa đơn theo mã
    public function find(string $invoiceId): ?array
    {
        //Lấy thông tin hóa đơn
        $headerStatement = $this->pdo->prepare(
            "SELECT hd.*, nv.ten_nhan_vien
             FROM hoa_don hd
             LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = hd.ma_nhan_vien
             WHERE hd.ma_hoa_don = :id
             LIMIT 1"
        );
        $headerStatement->execute(['id' => $invoiceId]);
        $invoice = $headerStatement->fetch();

        if (!$invoice) {
            return null;
        }

        //Lấy chi tiết hóa đơn
        $detailStatement = $this->pdo->prepare(
            "SELECT ct.*, hh.ten_hang_hoa, hh.don_vi_tinh
             FROM chi_tiet_hoa_don ct
             INNER JOIN hang_hoa hh ON hh.ma_hang_hoa = ct.ma_hang_hoa
             WHERE ct.ma_hoa_don = :id
             ORDER BY ct.ma_chi_tiet_hd ASC"
        );
        $detailStatement->execute(['id' => $invoiceId]);
        $details = $detailStatement->fetchAll();

        //Lấy thông tin lô hàng
        $lotStatement = $this->pdo->prepare(
            "SELECT ctbl.*, lh.han_su_dung, lh.ngay_san_xuat
             FROM chi_tiet_ban_lo ctbl
             INNER JOIN lo_hang lh ON lh.ma_lo_hang = ctbl.ma_lo_hang
             WHERE ctbl.ma_chi_tiet_hd = :detail_id
             ORDER BY lh.han_su_dung ASC, lh.ma_lo_hang ASC"
        );

        //Lấy thông tin lô hàng cho từng chi tiết hóa đơn
        foreach ($details as &$detail) {
            $lotStatement->execute(['detail_id' => $detail['ma_chi_tiet_hd']]);
            $detail['ban_lo'] = $lotStatement->fetchAll();
        }
        unset($detail);

        $invoice['chi_tiet'] = $details;

        return $invoice;
    }

    //Chuẩn hóa và tính lại giá cho các mặt hàng
    private function normalizeAndRepriceItems(mixed $rawItems): array
    {
        if (!is_array($rawItems)) {
            throw new RuntimeException('Dữ liệu giỏ hàng không hợp lệ.');
        }

        $items = [];

        foreach ($rawItems as $index => $rawItem) {
            if (!is_array($rawItem)) {
                continue;
            }

            //Lấy mã vạch
            $barcode = trim((string) ($rawItem['ma_vach_quet'] ?? ''));
            $type = strtoupper(trim((string) ($rawItem['loai_ma_vach'] ?? '')));

            //Nếu là tem cân thì xử lý riêng (bắt buộc mã vạch phải đúng cấu trúc tem cân EAN-13 bắt đầu bằng 20)
            if ($type === 'TEM_CAN' && !$this->looksLikeScaleBarcode($barcode)) {
                throw new RuntimeException("Mã vạch '{$barcode}' không khớp định dạng tem cân.");
            }

            if ($this->looksLikeScaleBarcode($barcode)) {
                $resolved = $this->resolveBarcode($barcode);
                if ($resolved['loai_ma_vach'] !== 'TEM_CAN') {
                    throw new RuntimeException("Mã vạch '{$barcode}' không phải là mã tem cân hợp lệ.");
                }
                $items[] = $resolved;
                continue;
            }

            //Lấy số lượng
            $quantity = round($this->normalizeNumber($rawItem['so_luong'] ?? 0), 3);
            if ($quantity <= 0) {
                throw new RuntimeException('Dòng ' . ($index + 1) . ': Số lượng phải lớn hơn 0.');
            }

            //Lấy mã hàng hóa
            $productId = trim((string) ($rawItem['ma_hang_hoa'] ?? ''));

            if ($productId === '' && $barcode !== '') {
                //Nếu là mã vạch cố định thì xử lý riêng
                $resolved = $this->resolveBarcode($barcode);

                if ($resolved['loai_ma_vach'] !== 'CO_DINH') {
                    $items[] = $resolved;
                    continue;
                }

                $productId = (string) $resolved['ma_hang_hoa'];
            }

            //Tìm hàng hóa
            $product = $this->findActiveProduct($productId);
            if (!$product) {
                throw new RuntimeException('Dòng ' . ($index + 1) . ': Không tìm thấy hàng hóa.');
            }

            $unitPrice = (float) $product['gia_ban'];

            //Thêm vào giỏ hàng
            $items[] = [
                'loai_ma_vach' => 'CO_DINH',
                'ma_hang_hoa' => $product['ma_hang_hoa'],
                'ten_hang_hoa' => $product['ten_hang_hoa'],
                'don_vi_tinh' => $product['don_vi_tinh'] ?? '',
                'so_luong' => $quantity,
                'trong_luong' => null,
                'gia_ban' => $unitPrice,
                'tong_tien' => round($quantity * $unitPrice, 0),
                'ma_vach_quet' => $barcode !== '' ? $barcode : ($product['ma_vach'] ?? null),
            ];
        }

        return $items;
    }

    //Thêm hóa đơn
    private function insertInvoice(
        string $invoiceId,
        float $total,
        float $customerPaid,
        array $payload,
        ?string $maNhanVien
    ): void {
        //Lấy phương thức thanh toán
        $paymentMethod = trim((string) ($payload['phuong_thuc_thanh_toan'] ?? ''));
        if ($paymentMethod === '') {
            $paymentMethod = 'Tiền mặt';
        }

        //Thêm hóa đơn vào DB
        $statement = $this->pdo->prepare(
            "INSERT INTO hoa_don
                (ma_hoa_don, ngay_tao, tong_tien, trang_thai, phuong_thuc_thanh_toan, tien_khach_dua, ma_nhan_vien)
             VALUES
                (:ma_hoa_don, NOW(), :tong_tien, 'HOAN_TAT', :phuong_thuc_thanh_toan, :tien_khach_dua, :ma_nhan_vien)"
        );
        $statement->execute([
            'ma_hoa_don' => $invoiceId,
            'tong_tien' => $total,
            'phuong_thuc_thanh_toan' => mb_substr($paymentMethod, 0, 50),
            'tien_khach_dua' => $customerPaid,
            'ma_nhan_vien' => $maNhanVien !== '' ? $maNhanVien : null,
        ]);
    }

    //Thêm chi tiết hóa đơn
    private function insertInvoiceLine(string $invoiceId, array $item): int
    {
        //Thêm chi tiết hóa đơn vào DB
        $statement = $this->pdo->prepare(
            "INSERT INTO chi_tiet_hoa_don
                (so_luong, gia_ban, tong_tien, ma_hoa_don, ma_hang_hoa, loai_ma_vach, ma_vach_quet, trong_luong)
             VALUES
                (:so_luong, :gia_ban, :tong_tien, :ma_hoa_don, :ma_hang_hoa, :loai_ma_vach, :ma_vach_quet, :trong_luong)"
        );
        $statement->execute([
            'so_luong' => (float) $item['so_luong'],
            'gia_ban' => (float) $item['gia_ban'],
            'tong_tien' => (float) $item['tong_tien'],
            'ma_hoa_don' => $invoiceId,
            'ma_hang_hoa' => $item['ma_hang_hoa'],
            'loai_ma_vach' => $item['loai_ma_vach'],
            'ma_vach_quet' => $item['ma_vach_quet'] ?? null,
            'trong_luong' => $item['trong_luong'] ?? null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    //Trừ số lượng hàng hóa trong lô hàng theo FIFO
    private function deductFifoShelfThenWarehouse(string $productId, float $quantity, int $detailId): void
    {
        $lots = $this->lockLots($productId);
        $available = 0.0;

        //Tính tổng số lượng hàng hóa
        foreach ($lots as $lot) {
            $available += (float) $lot['so_luong_tren_ke'] + (float) $lot['so_luong_trong_kho'];
        }

        //Kiểm tra tồn kho
        if ($available + self::EPSILON < $quantity) {
            $product = $this->findActiveProduct($productId);
            $productName = $product ? $product['ten_hang_hoa'] : $productId;

            // Lấy tổng tồn kho thực tế (bao gồm cả các lô đã hết hạn)
            $stmt = $this->pdo->prepare(
                "SELECT COALESCE(SUM(so_luong_tren_ke + so_luong_trong_kho), 0.0)
                 FROM lo_hang
                 WHERE ma_hang_hoa = :product_id"
            );
            $stmt->execute(['product_id' => $productId]);
            $totalPhysicalStock = (float) $stmt->fetchColumn();

            if ($available <= self::EPSILON) {
                if ($totalPhysicalStock >= $quantity) {
                    throw new RuntimeException(
                        "Sản phẩm '{$productName}' ({$productId}) vẫn còn tồn kho thực tế ({$this->formatQty($totalPhysicalStock)}) nhưng tất cả các lô hàng đều đã hết hạn sử dụng. Không thể bán!"
                    );
                } elseif ($totalPhysicalStock > 0.0) {
                    throw new RuntimeException(
                        "Sản phẩm '{$productName}' ({$productId}) đã hết hạn sử dụng cho tất cả các lô hàng hiện tại (tồn kho còn {$this->formatQty($totalPhysicalStock)} đã hết hạn). Không thể bán!"
                    );
                } else {
                    throw new RuntimeException(
                        "Sản phẩm '{$productName}' ({$productId}) đã hết hàng."
                    );
                }
            } else {
                if ($totalPhysicalStock >= $quantity) {
                    throw new RuntimeException(
                        "Sản phẩm '{$productName}' ({$productId}) chỉ còn {$this->formatQty($available)} sản phẩm chưa hết hạn sử dụng (tổng tồn thực tế bao gồm cả lô hết hạn là {$this->formatQty($totalPhysicalStock)}). Không đủ số lượng cần bán!"
                    );
                } else {
                    throw new RuntimeException(
                        "Tồn kho không đủ cho sản phẩm '{$productName}' ({$productId}). Cần {$this->formatQty($quantity)}, còn {$this->formatQty($available)} khả dụng."
                    );
                }
            }
        }

        $remaining = $quantity;
        $allocations = [];

        //Trừ số lượng hàng hóa theo FIFO
        foreach (['so_luong_tren_ke', 'so_luong_trong_kho'] as $column) {
            foreach ($lots as $index => $lot) {
                if ($remaining <= self::EPSILON) {
                    break 2;
                }

                //Tính số lượng hàng hóa cần trừ
                $availableInLot = (float) $lot[$column];
                $take = min($remaining, $availableInLot);

                //Nếu không thể trừ thì bỏ qua
                if ($take <= self::EPSILON) {
                    continue;
                }

                //Cập nhật số lượng hàng hóa
                $updateStatement = $this->pdo->prepare(
                    "UPDATE lo_hang
                     SET {$column} = {$column} - :quantity
                     WHERE ma_lo_hang = :lot_id"
                );
                $updateStatement->execute([
                    'quantity' => $take,
                    'lot_id' => $lot['ma_lo_hang'],
                ]);

                //Cập nhật lại giá trị trong mảng $lots để dùng cho vòng lặp cột sau
                $lots[$index][$column] = $availableInLot - $take;

                $lotId = (string) $lot['ma_lo_hang'];
                $allocations[$lotId] = ($allocations[$lotId] ?? 0.0) + $take;
                $remaining = round($remaining - $take, 3);
            }
        }

        //Kiểm tra tồn kho
        if ($remaining > self::EPSILON) {
            $product = $this->findActiveProduct($productId);
            $productName = $product ? $product['ten_hang_hoa'] : $productId;
            throw new RuntimeException(
                "Không thể trừ đủ tồn kho cho sản phẩm '{$productName}' ({$productId}). Còn thiếu {$this->formatQty($remaining)}."
            );
        }

        //Thêm chi tiết bán lô
        $insertStatement = $this->pdo->prepare(
            "INSERT INTO chi_tiet_ban_lo (so_luong, ma_chi_tiet_hd, ma_lo_hang)
             VALUES (:so_luong, :ma_chi_tiet_hd, :ma_lo_hang)"
        );

        foreach ($allocations as $lotId => $soldQty) {
            $insertStatement->execute([
                'so_luong' => round((float) $soldQty, 3),
                'ma_chi_tiet_hd' => $detailId,
                'ma_lo_hang' => $lotId,
            ]);
        }
    }

    //Khóa các lô hàng
    private function lockLots(string $productId): array
    {
        //Lấy danh sách các lô hàng theo FIFO còn hạn sử dụng
        $statement = $this->pdo->prepare(
            "SELECT ma_lo_hang, han_su_dung, so_luong_tren_ke, so_luong_trong_kho
             FROM lo_hang
             WHERE ma_hang_hoa = :product_id
               AND han_su_dung > CURDATE()
               AND (so_luong_tren_ke > 0 OR so_luong_trong_kho > 0)
             ORDER BY han_su_dung ASC, ma_lo_hang ASC
             FOR UPDATE"
        );
        $statement->execute(['product_id' => $productId]);

        return $statement->fetchAll();
    }

    //Tìm hàng hóa đang kinh doanh theo mã
    private function findActiveProduct(string $productId): ?array
    {
        if ($productId === '') {
            return null;
        }

        $statement = $this->pdo->prepare(
            "SELECT *
             FROM hang_hoa
             WHERE ma_hang_hoa = :id AND trang_thai = 'DANG_KINH_DOANH'
             LIMIT 1"
        );
        $statement->execute(['id' => $productId]);
        $row = $statement->fetch();

        return $row ?: null;
    }

    //Tạo mã hóa đơn
    private function generateIdForUpdate(): string
    {
        $prefix = 'HD-' . date('Ymd') . '-';
        $statement = $this->pdo->prepare(
            "SELECT ma_hoa_don
             FROM hoa_don
             WHERE ma_hoa_don LIKE :prefix
             ORDER BY ma_hoa_don DESC
             LIMIT 1
             FOR UPDATE"
        );
        $statement->execute(['prefix' => $prefix . '%']);
        $lastId = $statement->fetchColumn();
        $sequence = $lastId ? ((int) substr((string) $lastId, -4)) + 1 : 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    //Kiểm tra mã vạch có phải là EAN13 không
    private function isValidEan13(string $barcode): bool
    {
        if (preg_match('/^\d{13}$/', $barcode) !== 1) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $barcode[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return $checkDigit === (int) $barcode[12];
    }

    //Kiểm tra mã vạch có phải là mã vạch của cân không
    private function looksLikeScaleBarcode(string $barcode): bool
    {
        $numericBarcode = preg_replace('/\D+/', '', $barcode) ?? '';

        return preg_match('/^20\d{11}$/', $numericBarcode) === 1;
    }

    //Chuẩn hóa số
    private function normalizeNumber(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $normalized = str_replace(',', '.', trim((string) $value));

        return is_numeric($normalized) ? (float) $normalized : 0.0;
    }

    //Định dạng số lượng
    private function formatQty(float $quantity): string
    {
        return rtrim(rtrim(number_format($quantity, 3, '.', ''), '0'), '.');
    }

    public function countToday(): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM hoa_don WHERE DATE(ngay_tao) = CURDATE() AND trang_thai = 'HOAN_TAT'");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getRevenueToday(): float
    {
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(tong_tien), 0) FROM hoa_don WHERE DATE(ngay_tao) = CURDATE() AND trang_thai = 'HOAN_TAT'");
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    public function getTopProducts(int $limit = 5): array
    {
        $sql = "SELECT h.ma_hang_hoa, h.ten_hang_hoa, SUM(cthd.so_luong) as total_sold, SUM(cthd.tong_tien) as total_revenue
                FROM chi_tiet_hoa_don cthd
                JOIN hang_hoa h ON cthd.ma_hang_hoa = h.ma_hang_hoa
                JOIN hoa_don hd ON cthd.ma_hoa_don = hd.ma_hoa_don
                WHERE hd.trang_thai = 'HOAN_TAT' AND DATE(hd.ngay_tao) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY h.ma_hang_hoa, h.ten_hang_hoa
                ORDER BY total_sold DESC
                LIMIT " . (int) $limit;
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
