<?php
require_once BASE_PATH . 'config/database.php';

class HoaDonModel {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    public function countToday() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM hoa_don WHERE DATE(ngay_tao) = CURDATE() AND trang_thai = 'HOAN_TAT'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function getRevenueToday() {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(tong_tien), 0) FROM hoa_don WHERE DATE(ngay_tao) = CURDATE() AND trang_thai = 'HOAN_TAT'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function getTopProducts($limit = 5) {
        $sql = "SELECT h.ma_hang_hoa, h.ten_hang_hoa, SUM(cthd.so_luong) as total_sold, SUM(cthd.tong_tien) as total_revenue
                FROM chi_tiet_hoa_don cthd
                JOIN hang_hoa h ON cthd.ma_hang_hoa = h.ma_hang_hoa
                JOIN hoa_don hd ON cthd.ma_hoa_don = hd.ma_hoa_don
                WHERE hd.trang_thai = 'HOAN_TAT' AND DATE(hd.ngay_tao) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY h.ma_hang_hoa, h.ten_hang_hoa
                ORDER BY total_sold DESC
                LIMIT " . intval($limit);
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}