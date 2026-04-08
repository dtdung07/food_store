<?php
declare(strict_types=1);

class BaoCaoModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function revenueSummary(string $from, string $to, int $expiringDays): array
    {
        $statement = $this->pdo->prepare(
            "SELECT
                COALESCE(SUM(CASE WHEN hd.trang_thai = 'HOAN_TAT' THEN hd.tong_tien ELSE 0 END), 0) AS doanh_thu,
                COALESCE(SUM(CASE WHEN hd.trang_thai = 'HOAN_TAT' THEN 1 ELSE 0 END), 0) AS so_hoa_don
             FROM hoa_don hd
             WHERE DATE(hd.ngay_tao) BETWEEN :from AND :to"
        );
        $statement->execute([
            'from' => $from,
            'to' => $to,
        ]);

        $revenue = $statement->fetch() ?: ['doanh_thu' => 0, 'so_hoa_don' => 0];
        $inventory = $this->inventorySummary($expiringDays);

        $invoiceCount = (int) ($revenue['so_hoa_don'] ?? 0);
        $totalRevenue = (float) ($revenue['doanh_thu'] ?? 0);

        return [
            'doanh_thu' => $totalRevenue,
            'so_hoa_don' => $invoiceCount,
            'trung_binh_hoa_don' => $invoiceCount > 0 ? $totalRevenue / $invoiceCount : 0,
            'lo_sap_het_han' => (int) ($inventory['lo_sap_het_han'] ?? 0),
            'lo_het_han' => (int) ($inventory['lo_het_han'] ?? 0),
            'tong_lo' => (int) ($inventory['tong_lo'] ?? 0),
            'tong_so_luong_ton' => (int) ($inventory['tong_so_luong_ton'] ?? 0),
        ];
    }

    public function revenueByDay(string $from, string $to): array
    {
        $statement = $this->pdo->prepare(
            "SELECT
                DATE(hd.ngay_tao) AS ngay,
                COUNT(*) AS so_hoa_don,
                COALESCE(SUM(hd.tong_tien), 0) AS doanh_thu
             FROM hoa_don hd
             WHERE hd.trang_thai = 'HOAN_TAT'
               AND DATE(hd.ngay_tao) BETWEEN :from AND :to
             GROUP BY DATE(hd.ngay_tao)
             ORDER BY ngay DESC"
        );
        $statement->execute([
            'from' => $from,
            'to' => $to,
        ]);

        return $statement->fetchAll();
    }

    public function revenueByEmployee(string $from, string $to): array
    {
        $statement = $this->pdo->prepare(
            "SELECT
                COALESCE(nv.ten_nhan_vien, 'Chưa gắn nhân viên') AS ten_nhan_vien,
                COUNT(hd.ma_hoa_don) AS so_hoa_don,
                COALESCE(SUM(hd.tong_tien), 0) AS doanh_thu
             FROM hoa_don hd
             LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = hd.ma_nhan_vien
             WHERE hd.trang_thai = 'HOAN_TAT'
               AND DATE(hd.ngay_tao) BETWEEN :from AND :to
             GROUP BY COALESCE(nv.ten_nhan_vien, 'Chưa gắn nhân viên')
             ORDER BY doanh_thu DESC, ten_nhan_vien ASC"
        );
        $statement->execute([
            'from' => $from,
            'to' => $to,
        ]);

        return $statement->fetchAll();
    }

    public function expiringLots(int $days): array
    {
        $statement = $this->pdo->prepare(
            "SELECT
                lh.ma_lo_hang,
                hh.ma_hang_hoa,
                hh.ten_hang_hoa,
                hh.don_vi_tinh,
                lh.han_su_dung,
                lh.ngay_san_xuat,
                lh.so_luong_trong_kho,
                lh.so_luong_tren_ke,
                (lh.so_luong_trong_kho + lh.so_luong_tren_ke) AS tong_so_luong,
                DATEDIFF(lh.han_su_dung, CURDATE()) AS so_ngay_con_lai
             FROM lo_hang lh
             INNER JOIN hang_hoa hh ON hh.ma_hang_hoa = lh.ma_hang_hoa
             WHERE (lh.so_luong_trong_kho + lh.so_luong_tren_ke) > 0
               AND DATEDIFF(lh.han_su_dung, CURDATE()) <= :days
             ORDER BY lh.han_su_dung ASC, hh.ten_hang_hoa ASC"
        );
        $statement->bindValue(':days', $days, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function lossReports(string $from, string $to): array
    {
        $statement = $this->pdo->prepare(
            "SELECT
                ph.ma_phieu_huy,
                ph.ngay_tao,
                ph.ly_do_huy,
                ph.trang_thai,
                COALESCE(nv.ten_nhan_vien, 'Chưa gắn nhân viên') AS ten_nhan_vien,
                COALESCE(SUM(chl.so_luong), 0) AS tong_so_luong_huy,
                COALESCE(SUM(chl.so_luong * ctpn.don_gia_nhap), 0) AS gia_tri_that_thoat
             FROM phieu_huy_hang ph
             LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = ph.ma_nhan_vien
             LEFT JOIN chi_tiet_phieu_huy ctph ON ctph.ma_phieu_huy = ph.ma_phieu_huy
             LEFT JOIN chi_tiet_huy_lo chl ON chl.ma_chi_tiet_huy = ctph.ma_chi_tiet_huy
             LEFT JOIN chi_tiet_phieu_nhap ctpn
                    ON ctpn.ma_lo_hang = chl.ma_lo_hang
                   AND ctpn.ma_hang_hoa = ctph.ma_hang_hoa
             WHERE ph.ngay_tao BETWEEN :from AND :to
             GROUP BY
                ph.ma_phieu_huy,
                ph.ngay_tao,
                ph.ly_do_huy,
                ph.trang_thai,
                COALESCE(nv.ten_nhan_vien, 'Chưa gắn nhân viên')
             ORDER BY ph.ngay_tao DESC, ph.ma_phieu_huy DESC"
        );
        $statement->execute([
            'from' => $from,
            'to' => $to,
        ]);

        return $statement->fetchAll();
    }

    public function inventoryLots(): array
    {
        $statement = $this->pdo->query(
            "SELECT
                lh.ma_lo_hang,
                hh.ma_hang_hoa,
                hh.ten_hang_hoa,
                hh.don_vi_tinh,
                lh.ngay_san_xuat,
                lh.han_su_dung,
                lh.so_luong_trong_kho,
                lh.so_luong_tren_ke,
                (lh.so_luong_trong_kho + lh.so_luong_tren_ke) AS tong_so_luong,
                DATEDIFF(lh.han_su_dung, CURDATE()) AS so_ngay_con_lai
             FROM lo_hang lh
             INNER JOIN hang_hoa hh ON hh.ma_hang_hoa = lh.ma_hang_hoa
             ORDER BY lh.han_su_dung ASC, hh.ten_hang_hoa ASC"
        );

        return $statement->fetchAll();
    }

    public function inventorySummary(int $days): array
    {
        $statement = $this->pdo->prepare(
            "SELECT
                COUNT(*) AS tong_lo,
                COALESCE(SUM(lh.so_luong_trong_kho + lh.so_luong_tren_ke), 0) AS tong_so_luong_ton,
                COALESCE(SUM(CASE
                    WHEN DATEDIFF(lh.han_su_dung, CURDATE()) BETWEEN 0 AND :days THEN 1
                    ELSE 0
                END), 0) AS lo_sap_het_han,
                COALESCE(SUM(CASE
                    WHEN DATEDIFF(lh.han_su_dung, CURDATE()) < 0 THEN 1
                    ELSE 0
                END), 0) AS lo_het_han
             FROM lo_hang lh"
        );
        $statement->bindValue(':days', $days, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch() ?: [
            'tong_lo' => 0,
            'tong_so_luong_ton' => 0,
            'lo_sap_het_han' => 0,
            'lo_het_han' => 0,
        ];
    }
}
