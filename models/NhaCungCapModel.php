<?php
require_once BASE_PATH . 'config/database.php';

class NhaCungCapModel {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    public function getAll($limit = null, $offset = null) {
        $sql = "SELECT * FROM nha_cung_cap ORDER BY ten_nha_cung_cap";
        if ($limit !== null) {
            $sql .= " LIMIT " . intval($limit);
            if ($offset !== null) {
                $sql .= " OFFSET " . intval($offset);
            }
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getAllActive() {
        $stmt = $this->db->prepare("SELECT * FROM nha_cung_cap WHERE trang_thai = 'HOAT_DONG' ORDER BY ten_nha_cung_cap");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function findById($ma_nha_cung_cap) {
        $stmt = $this->db->prepare("SELECT * FROM nha_cung_cap WHERE ma_nha_cung_cap = ?");
        $stmt->execute([$ma_nha_cung_cap]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $sql = "INSERT INTO nha_cung_cap (ma_nha_cung_cap, ten_nha_cung_cap, dia_chi, email, so_dien_thoai, ten_nguoi_lien_he, trang_thai) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['ma_nha_cung_cap'], $data['ten_nha_cung_cap'], $data['dia_chi'], $data['email'],
            $data['so_dien_thoai'], $data['ten_nguoi_lien_he'], $data['trang_thai']
        ]);
    }
    
    public function update($ma_nha_cung_cap, $data) {
        $sql = "UPDATE nha_cung_cap SET ten_nha_cung_cap = ?, dia_chi = ?, email = ?, so_dien_thoai = ?, ten_nguoi_lien_he = ?, trang_thai = ? 
                WHERE ma_nha_cung_cap = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['ten_nha_cung_cap'], $data['dia_chi'], $data['email'], $data['so_dien_thoai'],
            $data['ten_nguoi_lien_he'], $data['trang_thai'], $ma_nha_cung_cap
        ]);
    }
    
    public function delete($ma_nha_cung_cap) {
        $stmt = $this->db->prepare("DELETE FROM nha_cung_cap WHERE ma_nha_cung_cap = ?");
        return $stmt->execute([$ma_nha_cung_cap]);
    }
    
    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM nha_cung_cap");
        return $stmt->fetchColumn();
    }
    
    public function countActive() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM nha_cung_cap WHERE trang_thai = 'HOAT_DONG'");
        return $stmt->fetchColumn();
    }
    
    public function search($keyword, $limit = null, $offset = null) {
        $sql = "SELECT * FROM nha_cung_cap WHERE ma_nha_cung_cap LIKE ? OR ten_nha_cung_cap LIKE ? OR so_dien_thoai LIKE ?
                ORDER BY ten_nha_cung_cap";
        if ($limit !== null) {
            $sql .= " LIMIT " . intval($limit);
            if ($offset !== null) {
                $sql .= " OFFSET " . intval($offset);
            }
        }
        $stmt = $this->db->prepare($sql);
        $keyword = "%$keyword%";
        $stmt->execute([$keyword, $keyword, $keyword]);
        return $stmt->fetchAll();
    }
    
    public function countSearch($keyword) {
        $sql = "SELECT COUNT(*) FROM nha_cung_cap WHERE ma_nha_cung_cap LIKE ? OR ten_nha_cung_cap LIKE ? OR so_dien_thoai LIKE ?";
        $stmt = $this->db->prepare($sql);
        $keyword = "%$keyword%";
        $stmt->execute([$keyword, $keyword, $keyword]);
        return $stmt->fetchColumn();
    }
}