<?php

//Model quản lý Phiếu xuất kho ra quầy: Xử lý theo logic FIFO (lô nào HSD gần nhất xuất trước)
class PhieuXuatModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    //Lấy tất cẩ danh sách phiếu xuất kho (có bộ lọc)
    public function all(string $keyword = '', string $dateFrom = '', string $dateTo = ''): array
    {
        $sql = "SELECT px.*, nv.ten_nhan_vien
                FROM phieu_xuat_hang px
                LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = px.ma_nhan_vien
                WHERE 1=1";

        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (px.ma_phieu_xuat LIKE :kw1)";
            $params['kw1'] = '%' . $keyword . '%';
        }

        if ($dateFrom !== '') {
            $sql .= " AND px.ngay_tao >= :date_from";
            $params['date_from'] = $dateFrom;
        }

        if ($dateTo !== '') {
            $sql .= " AND px.ngay_tao <= :date_to";
            $params['date_to'] = $dateTo;
        }

        $sql .= " ORDER BY px.ngay_tao DESC, px.ma_phieu_xuat DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    //Lấy chi tiết 1 phiếu xuất (header + chi tiết + chi tiết xuất lô)
    public function find(string $maPhieu): ?array
    {
        //Header (lấy thông tin chung của phiếu, nhân viên)
        $stmt = $this->pdo->prepare(
            "SELECT px.*, nv.ten_nhan_vien
             FROM phieu_xuat_hang px
             LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = px.ma_nhan_vien
             WHERE px.ma_phieu_xuat = :id LIMIT 1"
        );
        $stmt->execute(['id' => $maPhieu]);
        $header = $stmt->fetch();

        if (!$header) {
            return null;
        }

        //Chi tiết phiếu xuất (lấy danh sách chi tiết các mặt hàng trong phiếu)
        $detailStmt = $this->pdo->prepare(
            "SELECT ct.*, hh.ten_hang_hoa, hh.don_vi_tinh, hh.gia_ban
             FROM chi_tiet_phieu_xuat ct
             JOIN hang_hoa hh ON hh.ma_hang_hoa = ct.ma_hang_hoa
             WHERE ct.ma_phieu_xuat = :id
             ORDER BY ct.ma_chi_tiet_xuat ASC"
        );
        $detailStmt->execute(['id' => $maPhieu]);
        $details = $detailStmt->fetchAll();

        //Chi tiết xuất lô (lấy danh sách chi tiết các lô hàng trong phiếu)
        foreach ($details as &$detail) {
            $loStmt = $this->pdo->prepare(
                "SELECT cxl.*, lh.han_su_dung, lh.ngay_san_xuat
                 FROM chi_tiet_xuat_lo cxl
                 JOIN lo_hang lh ON lh.ma_lo_hang = cxl.ma_lo_hang
                 WHERE cxl.ma_chi_tiet_xuat = :id
                 ORDER BY lh.han_su_dung ASC"
            );
            $loStmt->execute(['id' => $detail['ma_chi_tiet_xuat']]);
            $detail['xuat_lo'] = $loStmt->fetchAll();
        }

        $header['chi_tiet'] = $details;

        return $header;
    }

    //Gợi ý xuất hàng theo FIFO cho 1 mặt hàng. Trả về mảng các lô cần xuất và số lượng từ mỗi lô
    //return array ['suggestions' => [mang_cac_lo_xuat], 'total_available' => int, 'enough' => bool]
    public function suggestFIFO(string $maHangHoa, float $qty): array
    {
        // Lấy các lô theo FIFO (HSD tăng dần), chỉ lô còn trong kho
        $stmt = $this->pdo->prepare(
            "SELECT ma_lo_hang, han_su_dung, ngay_san_xuat, so_luong_trong_kho
             FROM lo_hang
             WHERE ma_hang_hoa = :ma_hang_hoa AND so_luong_trong_kho > 0
             ORDER BY han_su_dung ASC"
        );
        $stmt->execute(['ma_hang_hoa' => $maHangHoa]);
        $lots = $stmt->fetchAll();

        $suggestions = []; //Mảng chứa các lô được chọn để xuất
        $remaining = $qty; //Số lượng còn lại cần xuất
        $totalAvailable = 0; //Tổng số lượng có sẵn trong các lô

        //Tính tổng số lượng có sẵn
        foreach ($lots as $lot) {
            $totalAvailable += (float) $lot['so_luong_trong_kho'];
        }

        //Duyệt qua các lô theo thứ tự FIFO
        foreach ($lots as $lot) {
            if ($remaining <= 0) {
                break;
            }

            $available = (float) $lot['so_luong_trong_kho'];
            $take = min($remaining, $available);

            //Thêm lô vào danh sách gợi ý
            $suggestions[] = [
                'ma_lo_hang'        => $lot['ma_lo_hang'], //Mã lô hàng
                'han_su_dung'       => $lot['han_su_dung'], //Hạn sử dụng
                'ngay_san_xuat'     => $lot['ngay_san_xuat'],
                'so_luong_trong_kho' => $available,
                'so_luong_xuat'     => $take,
            ];

            $remaining -= $take;
        }

        //Trả về mảng
        return [
            'suggestions'     => $suggestions, //Mảng các lô được chọn để xuất
            'total_available'  => $totalAvailable, //Tổng số lượng có sẵn trong các lô
            'enough'           => $remaining <= 0, //Có đủ số lượng để xuất không
            'requested'        => $qty, //Số lượng yêu cầu xuất
        ];
    }

    //Tạo 1 phiếu xuất kho mới với logic FIFO
    // $header [ghi_chu, ma_nhan_vien]
    // $details Mảng các dòng [ma_hang_hoa, so_luong, xuất_từ_lô: [{ma_lo_hang, so_luong_xuat}]]
    //Ví dụ: $details = [[ma_hang_hoa => "HH001", so_luong => 50, xuất_từ_lô => [[ma_lo_hang => "LH001", so_luong_xuat => 30]], ...]
    public function create(array $header, array $details): string
    {
        $this->pdo->beginTransaction();

        try {
            $maPhieu = $this->generateId();
            $tongSoLuong = 0.0;

            //1. Tạo header phiếu xuất
            $stmt = $this->pdo->prepare(
                "INSERT INTO phieu_xuat_hang (ma_phieu_xuat, ngay_tao, tong_so_luong, ghi_chu, ma_nhan_vien)
                 VALUES (:ma_phieu_xuat, CURDATE(), 0, :ghi_chu, :ma_nhan_vien)"
            );
            $stmt->execute([
                'ma_phieu_xuat' => $maPhieu,
                'ghi_chu'       => $header['ghi_chu'] ?: null,
                'ma_nhan_vien'  => $header['ma_nhan_vien'],
            ]);

            //2. Xử lý từng sản phẩm trong details
            foreach ($details as $detail) {
                $maHangHoa = trim($detail['ma_hang_hoa']);
                $soLuong = round((float) $detail['so_luong'], 3);

                //2a. Tạo chi tiết phiếu xuất
                $ctStmt = $this->pdo->prepare(
                    "INSERT INTO chi_tiet_phieu_xuat (so_luong, ma_phieu_xuat, ma_hang_hoa)
                     VALUES (:so_luong, :ma_phieu_xuat, :ma_hang_hoa)"
                );
                $ctStmt->execute([
                    'so_luong'      => $soLuong,
                    'ma_phieu_xuat' => $maPhieu,
                    'ma_hang_hoa'   => $maHangHoa,
                ]);
                $maChiTietXuat = (int) $this->pdo->lastInsertId();

                //2b. Logic FIFO: lấy lô theo HSD tăng dần, trừ dần
                $fifo = $this->suggestFIFO($maHangHoa, $soLuong);

                if (!$fifo['enough']) {
                    throw new RuntimeException(
                        "Tồn kho không đủ cho sản phẩm {$maHangHoa}. Yêu cầu: {$soLuong}, Tồn kho: {$fifo['total_available']}"
                    );
                }

                //2c. Tạo chi tiết xuất lô
                foreach ($fifo['suggestions'] as $suggestion) {
                    $cxlStmt = $this->pdo->prepare(
                        "INSERT INTO chi_tiet_xuat_lo (so_luong, ma_chi_tiet_xuat, ma_lo_hang)
                         VALUES (:so_luong, :ma_chi_tiet_xuat, :ma_lo_hang)"
                    );
                    $cxlStmt->execute([
                        'so_luong'        => $suggestion['so_luong_xuat'],
                        'ma_chi_tiet_xuat' => $maChiTietXuat,
                        'ma_lo_hang'      => $suggestion['ma_lo_hang'],
                    ]);

                    //Giảm kho, tăng kệ
                    $updateLo = $this->pdo->prepare(
                        "UPDATE lo_hang
                         SET so_luong_trong_kho = so_luong_trong_kho - :qty,
                             so_luong_tren_ke = so_luong_tren_ke + :qty2
                         WHERE ma_lo_hang = :id"
                    );
                    $updateLo->execute([
                        'qty'  => $suggestion['so_luong_xuat'],
                        'qty2' => $suggestion['so_luong_xuat'],
                        'id'   => $suggestion['ma_lo_hang'],
                    ]);
                }

                $tongSoLuong += $soLuong;
            }

            //3. Cập nhật tổng số lượng
            $updateHeader = $this->pdo->prepare(
                "UPDATE phieu_xuat_hang SET tong_so_luong = :tsl WHERE ma_phieu_xuat = :id"
            );
            $updateHeader->execute(['tsl' => $tongSoLuong, 'id' => $maPhieu]);

            $this->pdo->commit();

            return $maPhieu;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    //Sinh mã phiếu xuất: định dạng PX-YYYYMMDD-XXX
    public function generateId(): string
    {
        $prefix = 'PX-' . date('Ymd') . '-';

        $stmt = $this->pdo->prepare(
            "SELECT ma_phieu_xuat FROM phieu_xuat_hang
             WHERE ma_phieu_xuat LIKE :prefix
             ORDER BY ma_phieu_xuat DESC LIMIT 1"
        );
        $stmt->execute(['prefix' => $prefix . '%']);
        $last = $stmt->fetchColumn();

        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return $prefix . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }

    //Đếm tổng phiếu xuất
    public function count(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM phieu_xuat_hang")->fetchColumn();
    }
}
