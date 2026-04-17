<?php
declare(strict_types=1);

class NhaCungCapModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function getAllWithProductCount(?int $limit = null, ?int $offset = null): array
    {
        $sql = "SELECT ncc.*, 
                       COUNT(hh.ma_hang_hoa) AS so_san_pham
                FROM nha_cung_cap ncc
                LEFT JOIN hang_hoa hh ON hh.ma_nha_cung_cap = ncc.ma_nha_cung_cap
                GROUP BY ncc.ma_nha_cung_cap
                ORDER BY ncc.ten_nha_cung_cap";
        
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
            if ($offset !== null) {
                $sql .= " OFFSET " . (int) $offset;
            }
        }
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll(?int $limit = null, ?int $offset = null): array
    {
        $sql = "SELECT * FROM nha_cung_cap ORDER BY ten_nha_cung_cap";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
            if ($offset !== null) {
                $sql .= " OFFSET " . (int) $offset;
            }
        }
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllActive(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM nha_cung_cap WHERE trang_thai = 'HOAT_DONG' ORDER BY ten_nha_cung_cap");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(string $ma_nha_cung_cap): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM nha_cung_cap WHERE ma_nha_cung_cap = ?");
        $stmt->execute([$ma_nha_cung_cap]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO nha_cung_cap (ma_nha_cung_cap, ten_nha_cung_cap, dia_chi, email, so_dien_thoai, ten_nguoi_lien_he, trang_thai) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['ma_nha_cung_cap'],
            $data['ten_nha_cung_cap'],
            $data['dia_chi'] ?? null,
            $data['email'] ?? null,
            $data['so_dien_thoai'] ?? null,
            $data['ten_nguoi_lien_he'] ?? null,
            $data['trang_thai'] ?? 'HOAT_DONG'
        ]);
    }

    public function update(string $ma_nha_cung_cap, array $data): bool
    {
        $sql = "UPDATE nha_cung_cap SET ten_nha_cung_cap = ?, dia_chi = ?, email = ?, so_dien_thoai = ?, ten_nguoi_lien_he = ?, trang_thai = ? 
                WHERE ma_nha_cung_cap = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['ten_nha_cung_cap'],
            $data['dia_chi'] ?? null,
            $data['email'] ?? null,
            $data['so_dien_thoai'] ?? null,
            $data['ten_nguoi_lien_he'] ?? null,
            $data['trang_thai'] ?? 'HOAT_DONG',
            $ma_nha_cung_cap
        ]);
    }

    public function delete(string $ma_nha_cung_cap): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM nha_cung_cap WHERE ma_nha_cung_cap = ?");
        return $stmt->execute([$ma_nha_cung_cap]);
    }

    public function countAll(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM nha_cung_cap");
        return (int) $stmt->fetchColumn();
    }

    public function countActive(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM nha_cung_cap WHERE trang_thai = 'HOAT_DONG'");
        return (int) $stmt->fetchColumn();
    }

    public function search(string $keyword, ?int $limit = null, ?int $offset = null): array
    {
        $sql = "SELECT ncc.*, COUNT(hh.ma_hang_hoa) AS so_san_pham
                FROM nha_cung_cap ncc
                LEFT JOIN hang_hoa hh ON hh.ma_nha_cung_cap = ncc.ma_nha_cung_cap
                WHERE ncc.ma_nha_cung_cap LIKE ? OR ncc.ten_nha_cung_cap LIKE ? OR ncc.so_dien_thoai LIKE ?
                GROUP BY ncc.ma_nha_cung_cap
                ORDER BY ncc.ten_nha_cung_cap";
        
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
            if ($offset !== null) {
                $sql .= " OFFSET " . (int) $offset;
            }
        }
        
        $stmt = $this->pdo->prepare($sql);
        $kw = "%$keyword%";
        $stmt->execute([$kw, $kw, $kw]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countSearch(string $keyword): int
    {
        $sql = "SELECT COUNT(*) FROM nha_cung_cap 
                WHERE ma_nha_cung_cap LIKE ? OR ten_nha_cung_cap LIKE ? OR so_dien_thoai LIKE ?";
        $stmt = $this->pdo->prepare($sql);
        $kw = "%$keyword%";
        $stmt->execute([$kw, $kw, $kw]);
        return (int) $stmt->fetchColumn();
    }
}
