<?php
require_once BASE_PATH . 'config/database.php';

class NhanVienModel {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    public function findById($ma_nhan_vien) {
        $stmt = $this->db->prepare("SELECT * FROM nhan_vien WHERE ma_nhan_vien = ?");
        $stmt->execute([$ma_nhan_vien]);
        return $stmt->fetch();
    }
}