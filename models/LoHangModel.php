<?php
declare(strict_types=1);

class LoHangModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function countExpiringSoon(int $days = 7): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM lo_hang WHERE han_su_dung <= DATE_ADD(CURDATE(), INTERVAL ? DAY) AND (so_luong_trong_kho > 0 OR so_luong_tren_ke > 0)");
        $stmt->execute([$days]);
        return (int) $stmt->fetchColumn();
    }

    public function countOutOfStock(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM lo_hang WHERE so_luong_trong_kho = 0 AND so_luong_tren_ke = 0");
        return (int) $stmt->fetchColumn();
    }

    public function getExpiringSoon(int $days = 7, int $limit = 10): array
    {
        $sql = "SELECT l.*, h.ten_hang_hoa, h.don_vi_tinh
                FROM lo_hang l
                JOIN hang_hoa h ON l.ma_hang_hoa = h.ma_hang_hoa
                WHERE l.han_su_dung <= DATE_ADD(CURDATE(), INTERVAL ? DAY) 
                AND (l.so_luong_trong_kho > 0 OR l.so_luong_tren_ke > 0)
                ORDER BY l.han_su_dung ASC
                LIMIT " . (int) $limit;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastLotCodeWithPrefix(string $prefix): ?string
    {
        $stmt = $this->pdo->prepare(
            "SELECT ma_lo_hang FROM lo_hang 
             WHERE ma_lo_hang LIKE :prefix 
             ORDER BY ma_lo_hang DESC LIMIT 1"
        );
        $stmt->execute(['prefix' => $prefix . '%']);
        $result = $stmt->fetchColumn();
        return $result ? (string) $result : null;
    }
}

