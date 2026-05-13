<?php
declare(strict_types=1);

class HangHoaModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function getFiltered(
        string $keyword   = '',
        string $maDanhMuc = '',
        string $trangThai = '',
        ?int   $limit     = null,
        ?int   $offset    = null
    ): array {
        [$inner, $params] = $this->_buildQuery($keyword, $maDanhMuc, $trangThai);

        $sql = "SELECT base.*, 
                       COALESCE(tk.total_stock, 0) AS ton_kho,
                       COALESCE(tk.ton_trong_kho, 0) AS ton_trong_kho,
                       COALESCE(tk.ton_ke, 0) AS ton_ke
                FROM ({$inner}) AS base
                LEFT JOIN (
                    SELECT ma_hang_hoa,
                           SUM(so_luong_trong_kho + so_luong_tren_ke) AS total_stock,
                           SUM(so_luong_trong_kho) AS ton_trong_kho,
                           SUM(so_luong_tren_ke) AS ton_ke
                    FROM lo_hang
                    GROUP BY ma_hang_hoa
                ) tk ON tk.ma_hang_hoa = base.ma_hang_hoa
                ORDER BY base.ten_hang_hoa";

        if ($limit !== null) {
            $sql .= ' LIMIT ' . (int) $limit;
            if ($offset !== null) {
                $sql .= ' OFFSET ' . (int) $offset;
            }
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countFiltered(
        string $keyword   = '',
        string $maDanhMuc = '',
        string $trangThai = ''
    ): int {
        [$inner, $params] = $this->_buildQuery($keyword, $maDanhMuc, $trangThai);
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM ({$inner}) AS sub");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function findById(string $ma_hang_hoa): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT h.*,
                    d.ten_danh_muc,
                    n.ten_nha_cung_cap,
                    COALESCE(tk.ton_kho, 0) AS ton_kho
             FROM hang_hoa h
             LEFT JOIN danh_muc d     ON d.ma_danh_muc     = h.ma_danh_muc
             LEFT JOIN nha_cung_cap n ON n.ma_nha_cung_cap = h.ma_nha_cung_cap
             LEFT JOIN (
                 SELECT ma_hang_hoa,
                        SUM(so_luong_trong_kho + so_luong_tren_ke) AS ton_kho
                 FROM lo_hang
                 GROUP BY ma_hang_hoa
             ) tk ON tk.ma_hang_hoa = h.ma_hang_hoa
             WHERE h.ma_hang_hoa = ?"
        );
        $stmt->execute([$ma_hang_hoa]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getLoHang(string $ma_hang_hoa): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM lo_hang
             WHERE ma_hang_hoa = ?
             ORDER BY han_su_dung ASC"
        );
        $stmt->execute([$ma_hang_hoa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateLoHangBatch(array $lots): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE lo_hang
             SET so_luong_trong_kho = ?,
                 so_luong_tren_ke   = ?
             WHERE ma_lo_hang = ?"
        );
        foreach ($lots as $lot) {
            $kho = max(0.0, (float) ($lot['so_luong_trong_kho'] ?? 0.0));
            $ke  = max(0.0, (float) ($lot['so_luong_tren_ke'] ?? 0.0));
            $stmt->execute([$kho, $ke, $lot['ma_lo_hang']]);
        }
        return true;
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO hang_hoa
                 (ma_hang_hoa, ten_hang_hoa, don_vi_tinh, gia_ban,
                  ma_vach, ma_tem_can, trang_thai, ma_danh_muc, ma_nha_cung_cap)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['ma_hang_hoa'],
            $data['ten_hang_hoa'],
            $data['don_vi_tinh'],
            $data['gia_ban'],
            $data['ma_vach']        ?? null,
            $data['ma_tem_can']     ?? null,
            $data['trang_thai'],
            $data['ma_danh_muc']    ?? null,
            $data['ma_nha_cung_cap'] ?? null,
        ]);
    }

    public function update(string $ma_hang_hoa, array $data): bool
    {
        $newCode = strtoupper(trim($data['ma_hang_hoa'] ?? $ma_hang_hoa));

        $stmt = $this->pdo->prepare(
            "UPDATE hang_hoa
             SET ma_hang_hoa     = ?,
                 ten_hang_hoa    = ?,
                 don_vi_tinh     = ?,
                 gia_ban         = ?,
                 ma_vach         = ?,
                 ma_tem_can      = ?,
                 trang_thai      = ?,
                 ma_danh_muc     = ?,
                 ma_nha_cung_cap = ?
             WHERE ma_hang_hoa   = ?"
         );
        return $stmt->execute([
            $newCode,
            $data['ten_hang_hoa'],
            $data['don_vi_tinh'],
            $data['gia_ban'],
            $data['ma_vach']        ?? null,
            $data['ma_tem_can']     ?? null,
            $data['trang_thai'],
            $data['ma_danh_muc']    ?? null,
            $data['ma_nha_cung_cap'] ?? null,
            $ma_hang_hoa,
        ]);
    }

    public function getNextScaleCode(): string
    {
        $stmt = $this->pdo->query("SELECT MAX(CAST(ma_tem_can AS UNSIGNED)) FROM hang_hoa WHERE ma_tem_can IS NOT NULL AND ma_tem_can REGEXP '^[0-9]+$'");
        $max = $stmt->fetchColumn();
        $nextVal = $max ? ((int) $max) + 1 : 1;
        return str_pad((string) $nextVal, 5, '0', STR_PAD_LEFT);
    }

    public function isScaleCodeExists(string $scaleCode, ?string $excludeMaHangHoa = null): bool
    {
        if ($scaleCode === '') {
            return false;
        }
        $sql = "SELECT COUNT(*) FROM hang_hoa WHERE ma_tem_can = ?";
        $params = [$scaleCode];
        if ($excludeMaHangHoa !== null) {
            $sql .= " AND ma_hang_hoa != ?";
            $params[] = $excludeMaHangHoa;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return ((int) $stmt->fetchColumn()) > 0;
    }

    public function isNameExists(string $name, ?string $excludeMaHangHoa = null): bool
    {
        $sql = "SELECT COUNT(*) FROM hang_hoa WHERE TRIM(LOWER(ten_hang_hoa)) = TRIM(LOWER(?))";
        $params = [$name];
        if ($excludeMaHangHoa !== null) {
            $sql .= " AND ma_hang_hoa != ?";
            $params[] = $excludeMaHangHoa;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return ((int) $stmt->fetchColumn()) > 0;
    }

    public function delete(string $ma_hang_hoa): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM hang_hoa WHERE ma_hang_hoa = ?");
        return $stmt->execute([$ma_hang_hoa]);
    }

    public function generateId(): string
    {
        $stmt = $this->pdo->query("SELECT ma_hang_hoa FROM hang_hoa WHERE ma_hang_hoa LIKE 'HH%' ORDER BY ma_hang_hoa DESC LIMIT 1");
        $last = $stmt->fetchColumn();
        if ($last) {
            $seq = (int) substr($last, 2);
            $seq++;
        } else {
            $seq = 1;
        }
        return 'HH' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    public function getAll(?int $limit = null, ?int $offset = null): array
    {
        return $this->getFiltered('', '', '', $limit, $offset);
    }

    public function countAll(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM hang_hoa");
        return (int) $stmt->fetchColumn();
    }

    public function search(string $keyword, ?int $limit = null, ?int $offset = null): array
    {
        return $this->getFiltered($keyword, '', '', $limit, $offset);
    }

    public function countSearch(string $keyword): int
    {
        return $this->countFiltered($keyword, '', '');
    }

    private function _buildQuery(string $keyword, string $maDanhMuc, string $trangThai): array
    {
        $sql = "SELECT h.*, d.ten_danh_muc, n.ten_nha_cung_cap
                FROM hang_hoa h
                LEFT JOIN danh_muc d     ON d.ma_danh_muc     = h.ma_danh_muc
                LEFT JOIN nha_cung_cap n ON n.ma_nha_cung_cap = h.ma_nha_cung_cap
                WHERE 1=1";
        $params = [];

        if ($keyword !== '') {
            $sql     .= " AND (h.ma_hang_hoa LIKE ? OR h.ten_hang_hoa LIKE ? OR h.ma_vach LIKE ?)";
            $k        = "%{$keyword}%";
            $params[] = $k;
            $params[] = $k;
            $params[] = $k;
        }
        if ($maDanhMuc !== '') {
            $sql     .= " AND h.ma_danh_muc = ?";
            $params[] = $maDanhMuc;
        }
        if ($trangThai !== '') {
            $sql     .= " AND h.trang_thai = ?";
            $params[] = $trangThai;
        }

        return [$sql, $params];
    }


    //Tìm hàng hóa theo mã vạch từ NSX
    public function findByBarcode(string $barcode): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT hh.*, dm.ten_danh_muc
             FROM hang_hoa hh
             LEFT JOIN danh_muc dm ON dm.ma_danh_muc = hh.ma_danh_muc
             WHERE hh.ma_vach = :barcode AND hh.trang_thai = 'DANG_KINH_DOANH'
             LIMIT 1"
        );
        $stmt->execute(['barcode' => $barcode]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    //Tìm hàng hóa theo mã tem cân điện tử (20-xxxxx-yyyyy)
    public function findByScaleCode(string $code): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT hh.*, dm.ten_danh_muc
             FROM hang_hoa hh
             LEFT JOIN danh_muc dm ON dm.ma_danh_muc = hh.ma_danh_muc
             WHERE hh.ma_tem_can = :code AND hh.trang_thai = 'DANG_KINH_DOANH'
             LIMIT 1"
        );
        $stmt->execute(['code' => $code]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function hasRelations(string $ma_hang_hoa): bool
    {
        $tables = [
            'lo_hang' => 'ma_hang_hoa',
            'chi_tiet_hoa_don' => 'ma_hang_hoa',
            'chi_tiet_phieu_nhap' => 'ma_hang_hoa',
            'chi_tiet_phieu_xuat' => 'ma_hang_hoa',
            'chi_tiet_phieu_huy' => 'ma_hang_hoa'
        ];

        foreach ($tables as $table => $column) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?");
            $stmt->execute([$ma_hang_hoa]);
            if (((int) $stmt->fetchColumn()) > 0) {
                return true;
            }
        }

        return false;
    }
}
