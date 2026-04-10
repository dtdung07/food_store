<?php
require_once BASE_PATH . 'config/database.php';

class LoHangModel {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    public function countExpiringSoon($days = 7) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM lo_hang WHERE han_su_dung <= DATE_ADD(CURDATE(), INTERVAL ? DAY) AND (so_luong_trong_kho > 0 OR so_luong_tren_ke > 0)");
        $stmt->execute([$days]);
        return $stmt->fetchColumn();
    }
    
    public function countOutOfStock() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM lo_hang WHERE so_luong_trong_kho = 0 AND so_luong_tren_ke = 0");
        return $stmt->fetchColumn();
    }
    
    public function getExpiringSoon($days = 7, $limit = 10) {
        $sql = "SELECT l.*, h.ten_hang_hoa, h.don_vi_tinh
                FROM lo_hang l
                JOIN hang_hoa h ON l.ma_hang_hoa = h.ma_hang_hoa
                WHERE l.han_su_dung <= DATE_ADD(CURDATE(), INTERVAL ? DAY) 
                AND (l.so_luong_trong_kho > 0 OR l.so_luong_tren_ke > 0)
                ORDER BY l.han_su_dung ASC
                LIMIT " . intval($limit);
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
}