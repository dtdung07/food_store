<?php
require_once BASE_PATH . 'config/database.php';

class DanhMucModel {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // Lấy tất cả danh mục + số lượng sản phẩm
    public function getAllWithProductCount($limit = null, $offset = null) {
        $sql = "SELECT dm.*, 
                       COUNT(hh.ma_hang_hoa) AS so_luong_sp
                FROM danh_muc dm
                LEFT JOIN hang_hoa hh ON hh.ma_danh_muc = dm.ma_danh_muc
                GROUP BY dm.ma_danh_muc
                ORDER BY dm.ten_danh_muc";
        
        if ($limit !== null) {
            $sql .= " LIMIT " . intval($limit);
            if ($offset !== null) {
                $sql .= " OFFSET " . intval($offset);
            }
        }
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getAll($limit = null, $offset = null) {
        $sql = "SELECT * FROM danh_muc ORDER BY ten_danh_muc";
        if ($limit !== null) {
            $sql .= " LIMIT " . intval($limit);
            if ($offset !== null) {
                $sql .= " OFFSET " . intval($offset);
            }
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function findById($ma_danh_muc) {
        $stmt = $this->db->prepare("SELECT * FROM danh_muc WHERE ma_danh_muc = ?");
        $stmt->execute([$ma_danh_muc]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $sql = "INSERT INTO danh_muc (ma_danh_muc, ten_danh_muc, mo_ta) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['ma_danh_muc'], $data['ten_danh_muc'], $data['mo_ta']]);
    }
    
    public function update($ma_danh_muc, $data) {
        $sql = "UPDATE danh_muc SET ten_danh_muc = ?, mo_ta = ? WHERE ma_danh_muc = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['ten_danh_muc'], $data['mo_ta'], $ma_danh_muc]);
    }
    
    public function delete($ma_danh_muc) {
        $stmt = $this->db->prepare("DELETE FROM danh_muc WHERE ma_danh_muc = ?");
        return $stmt->execute([$ma_danh_muc]);
    }
    
    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM danh_muc");
        return $stmt->fetchColumn();
    }
}