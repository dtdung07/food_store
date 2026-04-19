<?php

//Model quản lý Phiếu nhập kho: Xlý logic tạo phiếu nhập, tạo lô hàng, cập nhật tồn kho.
class PhieuNhapModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    //Lấy danh sách phiếu nhập kho (có bộ lọc)
    public function all(string $keyword = '', string $dateFrom = '', string $dateTo = ''): array
    {
        $sql = "SELECT pn.*, nv.ten_nhan_vien, ncc.ten_nha_cung_cap
                FROM phieu_nhap_hang pn
                LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = pn.ma_nhan_vien
                LEFT JOIN nha_cung_cap ncc ON ncc.ma_nha_cung_cap = pn.ma_nha_cung_cap
                WHERE 1=1";

        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (pn.ma_phieu_nhap LIKE :kw1 OR ncc.ten_nha_cung_cap LIKE :kw2)";
            $val = '%' . $keyword . '%';
            $params['kw1'] = $val;
            $params['kw2'] = $val;
        }

        if ($dateFrom !== '') {
            $sql .= " AND pn.ngay_tao >= :date_from";
            $params['date_from'] = $dateFrom;
        }

        if ($dateTo !== '') {
            $sql .= " AND pn.ngay_tao <= :date_to";
            $params['date_to'] = $dateTo;
        }

        $sql .= " ORDER BY pn.ngay_tao DESC, pn.ma_phieu_nhap DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    //Lấy chi tiết 1 phiếu nhập (header + details)
    public function find(string $maPhieu): ?array
    {
        //Header (lấy thông tin chung của phiếu, nhà cung cấp, nhân viên)
        $stmt = $this->pdo->prepare(
            "SELECT pn.*, nv.ten_nhan_vien, ncc.ten_nha_cung_cap
             FROM phieu_nhap_hang pn
             LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = pn.ma_nhan_vien
             LEFT JOIN nha_cung_cap ncc ON ncc.ma_nha_cung_cap = pn.ma_nha_cung_cap
             WHERE pn.ma_phieu_nhap = :id LIMIT 1"
        );
        $stmt->execute(['id' => $maPhieu]);
        $header = $stmt->fetch();

        if (!$header) {
            return null;
        }

        //Details (lấy danh sách chi tiết các mặt hàng trong phiếu)
        $detailStmt = $this->pdo->prepare(
            "SELECT ct.*, hh.ten_hang_hoa, hh.don_vi_tinh, hh.gia_ban,
                    lh.ngay_san_xuat, lh.han_su_dung
             FROM chi_tiet_phieu_nhap ct
             JOIN hang_hoa hh ON hh.ma_hang_hoa = ct.ma_hang_hoa
             LEFT JOIN lo_hang lh ON lh.ma_lo_hang = ct.ma_lo_hang
             WHERE ct.ma_phieu_nhap = :id
             ORDER BY ct.ma_chi_tiet_nhap ASC"
        );
        $detailStmt->execute(['id' => $maPhieu]);
        $header['chi_tiet'] = $detailStmt->fetchAll();

        return $header;
    }

    // Tạo 1 phiếu nhập kho mới
    // Quy trình: Tạo thông tin chung -> tạo/cập nhật lô -> tạo chi tiết phiếu nhập -> cập nhật tồn kho
    // $header [ma_nha_cung_cap, ghi_chu, ma_nhan_vien]
    // $details Mảng các dòng chi tiết, mỗi dòng chứa: [ma_hang_hoa, so_luong, don_gia_nhap, ma_lo_hang, ngay_san_xuat, han_su_dung]
    public function create(array $header, array $details): string
    {
        //Mở transaction, nếu xảy ra lỗi thì dữ liệu được rollback
        $this->pdo->beginTransaction();

        try {
            $maPhieu = $this->generateId();
            $tongSoLuong = 0;
            $tongTien = 0.0;

            //1. Tạo header (thông tin chung) phiếu nhập
            $stmt = $this->pdo->prepare(
                "INSERT INTO phieu_nhap_hang (ma_phieu_nhap, ngay_tao, tong_so_luong, tong_tien, ghi_chu, ma_nhan_vien, ma_nha_cung_cap)
                 VALUES (:ma_phieu_nhap, CURDATE(), 0, 0, :ghi_chu, :ma_nhan_vien, :ma_nha_cung_cap)"
            );
            $stmt->execute([
                'ma_phieu_nhap'  => $maPhieu,
                'ghi_chu'        => $header['ghi_chu'] ?: null,
                'ma_nhan_vien'   => $header['ma_nhan_vien'],
                'ma_nha_cung_cap' => $header['ma_nha_cung_cap'],
            ]);

            //2. Xử lý từng sản phẩm trong details (chi tiết phiếu nhập)
            foreach ($details as $detail) {
                $soLuong = (int) $detail['so_luong'];
                $donGia = (float) $detail['don_gia_nhap'];
                $maLoHang = trim($detail['ma_lo_hang']);
                $maHangHoa = trim($detail['ma_hang_hoa']);

                //2a. Tạo lô hàng mới nếu chưa tồn tại, hoặc cộng thêm nếu đã tồn tại
                $existsStmt = $this->pdo->prepare("SELECT 1 FROM lo_hang WHERE ma_lo_hang = :id LIMIT 1");
                $existsStmt->execute(['id' => $maLoHang]);

                if ($existsStmt->fetchColumn() !== false) {
                    //Lô hàng tồn tại -> cộng thêm số lượng
                    $updateStmt = $this->pdo->prepare(
                        "UPDATE lo_hang SET so_luong_trong_kho = so_luong_trong_kho + :qty WHERE ma_lo_hang = :id"
                    );
                    $updateStmt->execute(['qty' => $soLuong, 'id' => $maLoHang]);
                } else {
                    // Chưa tồn tại, tạo lô mới
                    $insertLoStmt = $this->pdo->prepare(
                        "INSERT INTO lo_hang (ma_lo_hang, ngay_san_xuat, han_su_dung, so_luong_trong_kho, so_luong_tren_ke, ma_hang_hoa)
                         VALUES (:ma_lo_hang, :ngay_san_xuat, :han_su_dung, :so_luong, 0, :ma_hang_hoa)"
                    );
                    $insertLoStmt->execute([
                        'ma_lo_hang'    => $maLoHang,
                        'ngay_san_xuat' => $detail['ngay_san_xuat'],
                        'han_su_dung'   => $detail['han_su_dung'],
                        'so_luong'      => $soLuong,
                        'ma_hang_hoa'   => $maHangHoa,
                    ]);
                }

                //2b. Tạo chi tiết phiếu nhập
                $ctStmt = $this->pdo->prepare(
                    "INSERT INTO chi_tiet_phieu_nhap (so_luong, don_gia_nhap, ma_phieu_nhap, ma_hang_hoa, ma_lo_hang)
                     VALUES (:so_luong, :don_gia_nhap, :ma_phieu_nhap, :ma_hang_hoa, :ma_lo_hang)"
                );
                $ctStmt->execute([
                    'so_luong'      => $soLuong,
                    'don_gia_nhap'  => $donGia,
                    'ma_phieu_nhap' => $maPhieu,
                    'ma_hang_hoa'   => $maHangHoa,
                    'ma_lo_hang'    => $maLoHang,
                ]);

                $tongSoLuong += $soLuong;
                $tongTien += $soLuong * $donGia;
            }

            //3. Cập nhật tổng số lượng, tổng tiền vào phiếu nhập
            $updateHeader = $this->pdo->prepare(
                "UPDATE phieu_nhap_hang SET tong_so_luong = :tsl, tong_tien = :tt WHERE ma_phieu_nhap = :id"
            );
            $updateHeader->execute([
                'tsl' => $tongSoLuong,
                'tt'  => $tongTien,
                'id'  => $maPhieu,
            ]);

            //Xác nhận transaction
            $this->pdo->commit();

            return $maPhieu;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    //Sinh mã phiếu nhập tự động: PN-YYYYMMDD-XXX
    public function generateId(): string
    {
        //Tiền tố phiếu nhập trong ngày hôm nay
        $prefix = 'PN-' . date('Ymd') . '-';

        //Lấy các phiếu nhập trong ngày (theo tiền tố), sắp xếp giảm dần
        $stmt = $this->pdo->prepare(
            "SELECT ma_phieu_nhap FROM phieu_nhap_hang
             WHERE ma_phieu_nhap LIKE :prefix
             ORDER BY ma_phieu_nhap DESC LIMIT 1"
        );
        $stmt->execute(['prefix' => $prefix . '%']);
        $last = $stmt->fetchColumn(); //lấy phiếu lớn nhất (số thứ tự XXX)

        //có phiếu nhập thì tăng 1 đơn vị, chưa có thì = 1
        if ($last) {
            $seq = (int) substr($last, -3); //lấy 3 ký tự cuối (stt XXX)
            $seq++;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad((string) $seq, 3, '0', STR_PAD_LEFT); //str_pad: Bổ sung 0 bên trái
    }

    //Đếm tổng số phiếu nhập
    public function count(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM phieu_nhap_hang")->fetchColumn();
    }

    //Lấy tổng tiền nhập
    public function totalAmount(): float
    {
        return (float) $this->pdo->query("SELECT COALESCE(SUM(tong_tien), 0) FROM phieu_nhap_hang")->fetchColumn();
    }
}
