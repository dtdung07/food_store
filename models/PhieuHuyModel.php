<?php
declare(strict_types=1);

//Model quản lý Phiếu hủy hàng: Lập phiếu hủy -> Chờ duyệt -> Duyệt (trừ kho) hoặc Từ chối
class PhieuHuyModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }


    //:ấy danh sách phiếu hủy (có bộ lọc)
    public function all(string $keyword = '', string $status = '', string $dateFrom = '', string $dateTo = ''): array
    {
        $sql = "SELECT ph.*, nv.ten_nhan_vien, nd.ten_nhan_vien AS ten_nguoi_duyet
                FROM phieu_huy_hang ph
                LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = ph.ma_nhan_vien
                LEFT JOIN nhan_vien nd ON nd.ma_nhan_vien = ph.ma_nguoi_duyet
                WHERE 1=1";

        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (ph.ma_phieu_huy LIKE :kw1 OR nv.ten_nhan_vien LIKE :kw2)";
            $val = '%' . $keyword . '%';
            $params['kw1'] = $val;
            $params['kw2'] = $val;
        }

        if ($status !== '' && $status !== 'all') {
            $sql .= " AND ph.trang_thai = :status";
            $params['status'] = $status;
        }

        if ($dateFrom !== '') {
            $sql .= " AND ph.ngay_tao >= :date_from";
            $params['date_from'] = $dateFrom;
        }

        if ($dateTo !== '') {
            $sql .= " AND ph.ngay_tao <= :date_to";
            $params['date_to'] = $dateTo;
        }

        $sql .= " ORDER BY ph.ngay_tao DESC, ph.ma_phieu_huy DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    //Chi tiết 1 phiếu hủy (header + chi tiết)
    public function find(string $maPhieu): ?array
    {
        //Header (lấy thông tin chung của phiếu, nhân viên lập, nhân viên duyệt)
        $stmt = $this->pdo->prepare(
            "SELECT ph.*, nv.ten_nhan_vien, nd.ten_nhan_vien AS ten_nguoi_duyet
             FROM phieu_huy_hang ph
             LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = ph.ma_nhan_vien
             LEFT JOIN nhan_vien nd ON nd.ma_nhan_vien = ph.ma_nguoi_duyet
             WHERE ph.ma_phieu_huy = :id LIMIT 1"
        );
        $stmt->execute(['id' => $maPhieu]);
        $header = $stmt->fetch();

        if (!$header) {
            return null;
        }

        //Details (lấy danh sách chi tiết các mặt hàng trong phiếu)
        $detailStmt = $this->pdo->prepare(
            "SELECT ct.*, hh.ten_hang_hoa, hh.don_vi_tinh, hh.gia_ban
             FROM chi_tiet_phieu_huy ct
             JOIN hang_hoa hh ON hh.ma_hang_hoa = ct.ma_hang_hoa
             WHERE ct.ma_phieu_huy = :id
             ORDER BY ct.ma_chi_tiet_huy ASC"
        );
        $detailStmt->execute(['id' => $maPhieu]);
        $details = $detailStmt->fetchAll();

        //Lấy chi tiết hủy lô (nếu có)
        foreach ($details as &$detail) {
            $loStmt = $this->pdo->prepare(
                "SELECT chl.*, lh.han_su_dung, lh.ngay_san_xuat
                 FROM chi_tiet_huy_lo chl
                 JOIN lo_hang lh ON lh.ma_lo_hang = chl.ma_lo_hang
                 WHERE chl.ma_chi_tiet_huy = :id
                 ORDER BY lh.han_su_dung ASC"
            );
            $loStmt->execute(['id' => $detail['ma_chi_tiet_huy']]);
            $detail['huy_lo'] = $loStmt->fetchAll();
        }

        $header['chi_tiet'] = $details;

        return $header;
    }

    //Đếm phiếu chờ duyệt (cho badge sidebar)
    public function countPending(): int
    {
        return (int) $this->pdo->query(
            "SELECT COUNT(*) FROM phieu_huy_hang WHERE trang_thai = 'CHO_DUYET'"
        )->fetchColumn();
    }

    //Tạo phiếu hủy hàng mới (trạng thái Chờ duyệt thì không trừ kho)
    // $header [ly_do_huy, ma_nhan_vien]
    // $details [[ma_hang_hoa, so_luong, ly_do_huy], ...]
    public function create(array $header, array $details): string
    {
        $this->pdo->beginTransaction();

        try {
            $maPhieu = $this->generateId();
            $tongSoLuong = 0.0;

            //Insert header (thông tin chung) phiếu hủy
            $stmt = $this->pdo->prepare(
                "INSERT INTO phieu_huy_hang (ma_phieu_huy, ngay_tao, tong_so_luong, ly_do_huy_chung, trang_thai, ma_nhan_vien)
                 VALUES (:ma_phieu_huy, CURDATE(), 0, :ly_do_huy_chung, 'CHO_DUYET', :ma_nhan_vien)"
            );
            $stmt->execute([
                'ma_phieu_huy' => $maPhieu,
                'ly_do_huy_chung' => $header['ly_do_huy_chung'] ?? null,
                'ma_nhan_vien' => $header['ma_nhan_vien'],
            ]);

            //Xử lý từng sản phẩm trong chi tiết phiếu hủy
            foreach ($details as $detail) {
                $soLuong = round((float) $detail['so_luong'], 3);

                $ctStmt = $this->pdo->prepare(
                    "INSERT INTO chi_tiet_phieu_huy (so_luong, ly_do_huy, ma_phieu_huy, ma_hang_hoa)
                     VALUES (:so_luong, :ly_do_huy, :ma_phieu_huy, :ma_hang_hoa)"
                );
                $ctStmt->execute([
                    'so_luong'     => $soLuong,
                    'ly_do_huy'    => $detail['ly_do_huy'] ?? 'HET_HAN',
                    'ma_phieu_huy' => $maPhieu,
                    'ma_hang_hoa'  => trim($detail['ma_hang_hoa']),
                ]);

                $tongSoLuong += $soLuong;
            }

            //Cập nhật header (tổng số lượng)
            $updateHeader = $this->pdo->prepare(
                "UPDATE phieu_huy_hang SET tong_so_luong = :tsl WHERE ma_phieu_huy = :id"
            );
            $updateHeader->execute(['tsl' => $tongSoLuong, 'id' => $maPhieu]);

            $this->pdo->commit();

            return $maPhieu;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    //Duyệt phiếu hủy: trừ kho + ghi nhận chi tiết hủy lô
    public function approve(string $maPhieu, string $maNguoiDuyet): void
    {
        $this->pdo->beginTransaction();

        try {
            require_once APP_ROOT . '/models/LoHangModel.php';
            $loHangModel = new LoHangModel();
            //Kiểm tra phiếu còn ở trạng thái chờ duyệt
            $checkStmt = $this->pdo->prepare(
                "SELECT trang_thai FROM phieu_huy_hang WHERE ma_phieu_huy = :id LIMIT 1"
            );
            $checkStmt->execute(['id' => $maPhieu]);
            $currentStatus = $checkStmt->fetchColumn();

            if ($currentStatus !== 'CHO_DUYET') {
                throw new RuntimeException('Phiếu hủy không ở trạng thái Chờ duyệt.');
            }

            //Cập nhật trạng thái
            $updateStmt = $this->pdo->prepare(
                "UPDATE phieu_huy_hang
                 SET trang_thai = 'DA_DUYET', ma_nguoi_duyet = :nguoi_duyet, ngay_duyet = CURDATE()
                 WHERE ma_phieu_huy = :id"
            );
            $updateStmt->execute(['nguoi_duyet' => $maNguoiDuyet, 'id' => $maPhieu]);

            //Lấy chi tiết phiếu hủy
            $detailStmt = $this->pdo->prepare(
                "SELECT * FROM chi_tiet_phieu_huy WHERE ma_phieu_huy = :id"
            );
            $detailStmt->execute(['id' => $maPhieu]);
            $details = $detailStmt->fetchAll();

            //Trừ kho theo FIFO cho từng mặt hàng
            foreach ($details as $detail) {
                $maHangHoa = $detail['ma_hang_hoa'];
                $soLuongHuy = round((float) $detail['so_luong'], 3);
                $maChiTietHuy = (int) $detail['ma_chi_tiet_huy'];

                //Lấy lô theo FIFO
                $loStmt = $this->pdo->prepare(
                    "SELECT ma_lo_hang, so_luong_trong_kho, so_luong_tren_ke
                     FROM lo_hang
                     WHERE ma_hang_hoa = :ma_hang_hoa
                       AND (so_luong_trong_kho > 0 OR so_luong_tren_ke > 0)
                     ORDER BY han_su_dung ASC"
                );
                $loStmt->execute(['ma_hang_hoa' => $maHangHoa]);
                $lots = $loStmt->fetchAll();

                // Tính tổng tồn thực tế của tất cả các lô
                $totalAvailable = 0;
                foreach ($lots as $lot) {
                    $totalAvailable += (float) $lot['so_luong_trong_kho'] + (float) $lot['so_luong_tren_ke'];
                }

                if ($soLuongHuy > $totalAvailable) {
                    $hhStmt = $this->pdo->prepare("SELECT ten_hang_hoa FROM hang_hoa WHERE ma_hang_hoa = ?");
                    $hhStmt->execute([$maHangHoa]);
                    $tenHang = $hhStmt->fetchColumn() ?: $maHangHoa;
                    throw new RuntimeException("Sản phẩm '{$tenHang}' không đủ tồn kho thực tế để thực hiện duyệt hủy. Yêu cầu hủy: {$soLuongHuy}, Tồn kho khả dụng: {$totalAvailable}. Vui lòng thực hiện kiểm kê điều chỉnh tồn kho trước khi duyệt.");
                }

                $remaining = $soLuongHuy;

                foreach ($lots as $lot) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $totalLot = (float) $lot['so_luong_trong_kho'] + (float) $lot['so_luong_tren_ke'];
                    $take = min($remaining, $totalLot);

                    //Trừ ưu tiên từ kho trước, rồi kệ
                    $fromKho = min($take, (float) $lot['so_luong_trong_kho']);
                    $fromKe = $take - $fromKho;

                    if ($fromKho > 0) {
                        $loHangModel->updateKhoQty($lot['ma_lo_hang'], -$fromKho);
                    }

                    if ($fromKe > 0) {
                        $loHangModel->updateKeQty($lot['ma_lo_hang'], -$fromKe);
                    }

                    //Ghi chi tiết hủy lô
                    $chlStmt = $this->pdo->prepare(
                        "INSERT INTO chi_tiet_huy_lo (so_luong, ma_chi_tiet_huy, ma_lo_hang)
                         VALUES (:so_luong, :ma_chi_tiet_huy, :ma_lo_hang)"
                    );
                    $chlStmt->execute([
                        'so_luong'       => $take,
                        'ma_chi_tiet_huy' => $maChiTietHuy,
                        'ma_lo_hang'     => $lot['ma_lo_hang'],
                    ]);

                    $remaining -= $take;
                }
            }

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    //Từ chối phiếu hủy (không trừ kho)
    public function reject(string $maPhieu, string $maNguoiDuyet, string $lyDo): void
    {
        //Kiểm tra phiếu còn ở trạng thái chờ duyệt
        $checkStmt = $this->pdo->prepare(
            "SELECT trang_thai FROM phieu_huy_hang WHERE ma_phieu_huy = :id LIMIT 1"
        );
        $checkStmt->execute(['id' => $maPhieu]);
        $currentStatus = $checkStmt->fetchColumn();

        if ($currentStatus !== 'CHO_DUYET') {
            throw new RuntimeException('Phiếu hủy không ở trạng thái chờ duyệt');
        }

        //Cập nhật trạng thái sang từ chối
        $stmt = $this->pdo->prepare(
            "UPDATE phieu_huy_hang
             SET trang_thai = 'TU_CHOI',
                 ma_nguoi_duyet = :nguoi_duyet,
                 ly_do_tu_choi = :ly_do,
                 ngay_duyet = CURDATE()
             WHERE ma_phieu_huy = :id"
        );
        $stmt->execute([
            'nguoi_duyet' => $maNguoiDuyet,
            'ly_do'       => $lyDo,
            'id'          => $maPhieu,
        ]);
    }

    //Sinh mã phiếu hủy: PH-YYYYMMDD-XXX
    public function generateId(): string
    {
        $prefix = 'PH-' . date('Ymd') . '-';

        $stmt = $this->pdo->prepare(
            "SELECT ma_phieu_huy FROM phieu_huy_hang
             WHERE ma_phieu_huy LIKE :prefix
             ORDER BY ma_phieu_huy DESC LIMIT 1"
        );
        $stmt->execute(['prefix' => $prefix . '%']);
        $last = $stmt->fetchColumn();

        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return $prefix . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }
}