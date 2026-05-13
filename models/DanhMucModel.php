<?php
declare(strict_types=1);

class DanhMucModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function getAllWithProductCount(?int $limit = null, ?int $offset = null): array
    {
        $sql = "SELECT dm.*, 
                       COUNT(hh.ma_hang_hoa) AS so_luong_sp
                FROM danh_muc dm
                LEFT JOIN hang_hoa hh ON hh.ma_danh_muc = dm.ma_danh_muc
                GROUP BY dm.ma_danh_muc
                ORDER BY dm.ten_danh_muc";
        
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
            if ($offset !== null) {
                $sql .= " OFFSET " . (int) $offset;
            }
        }
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getAll(?int $limit = null, ?int $offset = null): array
    {
        $sql = "SELECT * FROM danh_muc ORDER BY ten_danh_muc";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
            if ($offset !== null) {
                $sql .= " OFFSET " . (int) $offset;
            }
        }
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function findById(string $ma_danh_muc): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM danh_muc WHERE ma_danh_muc = ?");
        $stmt->execute([$ma_danh_muc]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO danh_muc (ma_danh_muc, ten_danh_muc, mo_ta) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$data['ma_danh_muc'], $data['ten_danh_muc'], $data['mo_ta'] ?? null]);
    }

    public function update(string $ma_danh_muc, array $data): bool
    {
        $sql = "UPDATE danh_muc SET ten_danh_muc = ?, mo_ta = ? WHERE ma_danh_muc = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$data['ten_danh_muc'], $data['mo_ta'] ?? null, $ma_danh_muc]);
    }

    public function delete(string $ma_danh_muc): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM danh_muc WHERE ma_danh_muc = ?");
        return $stmt->execute([$ma_danh_muc]);
    }

    public function generateId(): string
    {
        $stmt = $this->pdo->query("SELECT ma_danh_muc FROM danh_muc WHERE ma_danh_muc LIKE 'DM%' ORDER BY ma_danh_muc DESC LIMIT 1");
        $last = $stmt->fetchColumn();
        if ($last) {
            $seq = (int) substr($last, 2);
            $seq++;
        } else {
            $seq = 1;
        }
        return 'DM' . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }

    public function hasProducts(string $ma_danh_muc): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM hang_hoa WHERE ma_danh_muc = ?");
        $stmt->execute([$ma_danh_muc]);
        return ((int) $stmt->fetchColumn()) > 0;
    }

    public function countAll(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM danh_muc");
        return (int) $stmt->fetchColumn();
    }
}
