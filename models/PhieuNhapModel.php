<?php
require_once BASE_PATH . 'config/database.php';

class PhieuNhapModel {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    public function getTotalImportToday() {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(tong_tien), 0) FROM phieu_nhap_hang WHERE ngay_tao = CURDATE()");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}