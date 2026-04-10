<?php
require_once BASE_PATH . 'config/database.php';

class TaiKhoanModel {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    public function findByUsername($username) {
        $sql = "SELECT tk.*, nv.ten_nhan_vien, cv.ten_chuc_vu 
                FROM tai_khoan tk
                LEFT JOIN nhan_vien nv ON tk.ma_nhan_vien = nv.ma_nhan_vien
                LEFT JOIN chuc_vu cv ON tk.ma_chuc_vu = cv.ma_chuc_vu
                WHERE tk.ten_dang_nhap = ? AND tk.trang_thai = 'HOAT_DONG'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    public function updatePassword($ma_tai_khoan, $password) {
        $stmt = $this->db->prepare("UPDATE tai_khoan SET password = ? WHERE ma_tai_khoan = ?");
        return $stmt->execute([$password, $ma_tai_khoan]);
    }
}