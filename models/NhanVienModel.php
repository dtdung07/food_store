<?php
declare(strict_types=1);

class NhanVienModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function all(): array
    {
        return $this->search('', 'all');
    }

    public function search(string $keyword = '', string $status = 'all'): array
    {
        $sql = "SELECT
                    nv.*,
                    cv.ten_chuc_vu,
                    tk.ma_tai_khoan,
                    tk.ten_dang_nhap,
                    tk.trang_thai AS trang_thai_tai_khoan
                 FROM nhan_vien nv
                 LEFT JOIN chuc_vu cv ON cv.ma_chuc_vu = nv.ma_chuc_vu
                 LEFT JOIN tai_khoan tk ON tk.ma_nhan_vien = nv.ma_nhan_vien
                 WHERE 1=1";

        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (nv.ten_nhan_vien LIKE :kw1 OR nv.ma_nhan_vien LIKE :kw2 OR nv.email LIKE :kw3 OR tk.ten_dang_nhap LIKE :kw4)";
            $val = '%' . $keyword . '%';
            $params['kw1'] = $val;
            $params['kw2'] = $val;
            $params['kw3'] = $val;
            $params['kw4'] = $val;
        }

        if ($status !== 'all') {
            if ($status === 'active') {
                $sql .= " AND tk.trang_thai = 'HOAT_DONG'";
            } elseif ($status === 'disabled') {
                $sql .= " AND (tk.trang_thai IS NOT NULL AND tk.trang_thai != 'HOAT_DONG')";
            } elseif ($status === 'no-account') {
                $sql .= " AND tk.ma_tai_khoan IS NULL";
            }
        }

        $sql .= " ORDER BY nv.ma_nhan_vien ASC";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function find(string $id): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT
                nv.*,
                cv.ten_chuc_vu,
                tk.ma_tai_khoan,
                tk.ten_dang_nhap,
                tk.trang_thai AS trang_thai_tai_khoan
             FROM nhan_vien nv
             LEFT JOIN chuc_vu cv ON cv.ma_chuc_vu = nv.ma_chuc_vu
             LEFT JOIN tai_khoan tk ON tk.ma_nhan_vien = nv.ma_nhan_vien
             WHERE nv.ma_nhan_vien = :id
             LIMIT 1"
        );
        $statement->execute(['id' => $id]);
        $employee = $statement->fetch();

        return $employee ?: null;
    }

    public function create(array $data): void
    {
        $this->pdo->beginTransaction();

        try {
            $statement = $this->pdo->prepare(
                "INSERT INTO nhan_vien (
                    ma_nhan_vien,
                    ten_nhan_vien,
                    gioi_tinh,
                    so_dien_thoai,
                    email,
                    dia_chi,
                    ngay_sinh,
                    ma_chuc_vu
                ) VALUES (
                    :ma_nhan_vien,
                    :ten_nhan_vien,
                    :gioi_tinh,
                    :so_dien_thoai,
                    :email,
                    :dia_chi,
                    :ngay_sinh,
                    :ma_chuc_vu
                )"
            );
            $statement->execute($this->normalizePayload($data));

            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function update(string $id, array $data): void
    {
        $this->pdo->beginTransaction();

        try {
            $statement = $this->pdo->prepare(
                "UPDATE nhan_vien
                 SET
                    ten_nhan_vien = :ten_nhan_vien,
                    gioi_tinh = :gioi_tinh,
                    so_dien_thoai = :so_dien_thoai,
                    email = :email,
                    dia_chi = :dia_chi,
                    ngay_sinh = :ngay_sinh,
                    ma_chuc_vu = :ma_chuc_vu
                 WHERE ma_nhan_vien = :ma_nhan_vien"
            );
            $statement->execute($this->normalizePayload($data));

            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function delete(string $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM nhan_vien WHERE ma_nhan_vien = :id');
        $statement->execute(['id' => $id]);
    }

    public function generateNextEmployeeCode(): string
    {
        $statement = $this->pdo->query(
            "SELECT ma_nhan_vien 
             FROM nhan_vien 
             WHERE ma_nhan_vien REGEXP '^NV[0-9]+$' 
             ORDER BY ma_nhan_vien DESC 
             LIMIT 1"
        );
        $lastCode = $statement->fetchColumn();

        if (!$lastCode) {
            return 'NV001';
        }

        $numberPart = (int) substr((string) $lastCode, 2);
        $nextNumber = $numberPart + 1;

        return 'NV' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    private function normalizePayload(array $data): array
    {
        return [
            'ma_nhan_vien' => $data['ma_nhan_vien'],
            'ten_nhan_vien' => $data['ten_nhan_vien'],
            'gioi_tinh' => $data['gioi_tinh'] !== '' ? $data['gioi_tinh'] : null,
            'so_dien_thoai' => $data['so_dien_thoai'] !== '' ? $data['so_dien_thoai'] : null,
            'email' => $data['email'] !== '' ? $data['email'] : null,
            'dia_chi' => $data['dia_chi'] !== '' ? $data['dia_chi'] : null,
            'ngay_sinh' => $data['ngay_sinh'] !== '' ? $data['ngay_sinh'] : null,
            'ma_chuc_vu' => $data['ma_chuc_vu'] !== '' ? $data['ma_chuc_vu'] : null,
        ];
    }
}
