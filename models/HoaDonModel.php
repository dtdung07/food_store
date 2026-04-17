<?php
declare(strict_types=1);

class HoaDonModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
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
