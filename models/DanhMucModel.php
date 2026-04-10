<?php
require_once BASE_PATH . 'config/database.php';

class DanhMucModel {
    private $db;
    
    public function __construct() {
        $this->db = db();
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