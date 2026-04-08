<?php
declare(strict_types=1);

class TaiKhoanModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function all(): array
    {
        $statement = $this->pdo->query(
            "SELECT
                tk.ma_tai_khoan,
                tk.ten_dang_nhap,
                tk.trang_thai,
                tk.ma_nhan_vien,
                tk.ma_chuc_vu,
                nv.ten_nhan_vien,
                cv.ten_chuc_vu
             FROM tai_khoan tk
             LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = tk.ma_nhan_vien
             LEFT JOIN chuc_vu cv ON cv.ma_chuc_vu = tk.ma_chuc_vu
             ORDER BY tk.ma_tai_khoan ASC"
        );

        return $statement->fetchAll();
    }

    public function search(string $keyword = '', string $status = 'all'): array
    {
        $sql = "SELECT
                    tk.ma_tai_khoan,
                    tk.ten_dang_nhap,
                    tk.trang_thai,
                    tk.ma_nhan_vien,
                    tk.ma_chuc_vu,
                    nv.ten_nhan_vien,
                    cv.ten_chuc_vu
                 FROM tai_khoan tk
                 LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = tk.ma_nhan_vien
                 LEFT JOIN chuc_vu cv ON cv.ma_chuc_vu = tk.ma_chuc_vu
                 WHERE 1=1";

        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (tk.ten_dang_nhap LIKE :kw1 OR nv.ten_nhan_vien LIKE :kw2 OR tk.ma_nhan_vien LIKE :kw3 OR cv.ten_chuc_vu LIKE :kw4)";
            $val = '%' . $keyword . '%';
            $params['kw1'] = $val;
            $params['kw2'] = $val;
            $params['kw3'] = $val;
            $params['kw4'] = $val;
        }

        if ($status !== 'all') {
            $sql .= " AND tk.trang_thai = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY tk.ma_tai_khoan ASC";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT
                tk.ma_tai_khoan,
                tk.ten_dang_nhap,
                tk.trang_thai,
                tk.ma_nhan_vien,
                tk.ma_chuc_vu,
                nv.ten_nhan_vien,
                cv.ten_chuc_vu
             FROM tai_khoan tk
             LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = tk.ma_nhan_vien
             LEFT JOIN chuc_vu cv ON cv.ma_chuc_vu = tk.ma_chuc_vu
             WHERE tk.ma_tai_khoan = :id
             LIMIT 1"
        );
        $statement->execute(['id' => $id]);
        $account = $statement->fetch();

        return $account ?: null;
    }

    public function findForLogin(string $username): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT
                tk.ma_tai_khoan,
                tk.ten_dang_nhap,
                tk.password,
                tk.trang_thai,
                tk.ma_nhan_vien,
                tk.ma_chuc_vu,
                nv.ten_nhan_vien,
                cv.ten_chuc_vu
             FROM tai_khoan tk
             LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = tk.ma_nhan_vien
             LEFT JOIN chuc_vu cv ON cv.ma_chuc_vu = tk.ma_chuc_vu
             WHERE tk.ten_dang_nhap = :username
             LIMIT 1"
        );
        $statement->execute(['username' => $username]);
        $account = $statement->fetch();

        return $account ?: null;
    }

    public function create(array $data): int
    {
        $this->pdo->beginTransaction();

        try {
            $statement = $this->pdo->prepare(
                "INSERT INTO tai_khoan (
                    ten_dang_nhap,
                    password,
                    trang_thai,
                    ma_nhan_vien,
                    ma_chuc_vu
                ) VALUES (
                    :ten_dang_nhap,
                    :password,
                    :trang_thai,
                    :ma_nhan_vien,
                    :ma_chuc_vu
                )"
            );
            $statement->execute([
                'ten_dang_nhap' => $data['ten_dang_nhap'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'trang_thai' => $data['trang_thai'],
                'ma_nhan_vien' => $data['ma_nhan_vien'],
                'ma_chuc_vu' => $data['ma_chuc_vu'],
            ]);

            $newId = (int) $this->pdo->lastInsertId();
            $this->syncEmployeeRole($data['ma_nhan_vien'], $data['ma_chuc_vu']);

            $this->pdo->commit();

            return $newId;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function update(int $id, array $data): void
    {
        $this->pdo->beginTransaction();

        try {
            $fields = [
                'ten_dang_nhap = :ten_dang_nhap',
                'trang_thai = :trang_thai',
                'ma_nhan_vien = :ma_nhan_vien',
                'ma_chuc_vu = :ma_chuc_vu',
            ];
            $params = [
                'id' => $id,
                'ten_dang_nhap' => $data['ten_dang_nhap'],
                'trang_thai' => $data['trang_thai'],
                'ma_nhan_vien' => $data['ma_nhan_vien'],
                'ma_chuc_vu' => $data['ma_chuc_vu'],
            ];

            if ($data['password'] !== '') {
                $fields[] = 'password = :password';
                $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $statement = $this->pdo->prepare(
                'UPDATE tai_khoan SET ' . implode(', ', $fields) . ' WHERE ma_tai_khoan = :id'
            );
            $statement->execute($params);

            $this->syncEmployeeRole($data['ma_nhan_vien'], $data['ma_chuc_vu']);

            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM tai_khoan WHERE ma_tai_khoan = :id');
        $statement->execute(['id' => $id]);
    }

    public function availableEmployees(?string $includeEmployeeId = null): array
    {
        $sql = "SELECT
                    nv.ma_nhan_vien,
                    nv.ten_nhan_vien,
                    cv.ten_chuc_vu
                FROM nhan_vien nv
                LEFT JOIN chuc_vu cv ON cv.ma_chuc_vu = nv.ma_chuc_vu
                LEFT JOIN tai_khoan tk ON tk.ma_nhan_vien = nv.ma_nhan_vien
                WHERE tk.ma_tai_khoan IS NULL";
        $params = [];

        if ($includeEmployeeId !== null && $includeEmployeeId !== '') {
            $sql .= ' OR nv.ma_nhan_vien = :include_employee_id';
            $params['include_employee_id'] = $includeEmployeeId;
        }

        $sql .= ' ORDER BY nv.ten_nhan_vien ASC';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function sessionPayloadByAccountId(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT
                tk.ma_tai_khoan,
                tk.ten_dang_nhap,
                tk.trang_thai,
                tk.ma_nhan_vien,
                tk.ma_chuc_vu,
                nv.ten_nhan_vien,
                cv.ten_chuc_vu
             FROM tai_khoan tk
             LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = tk.ma_nhan_vien
             LEFT JOIN chuc_vu cv ON cv.ma_chuc_vu = tk.ma_chuc_vu
             WHERE tk.ma_tai_khoan = :id
             LIMIT 1"
        );
        $statement->execute(['id' => $id]);
        $account = $statement->fetch();

        return $account ?: null;
    }

    public function sessionPayloadByEmployeeId(string $employeeId): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT
                tk.ma_tai_khoan,
                tk.ten_dang_nhap,
                tk.trang_thai,
                tk.ma_nhan_vien,
                tk.ma_chuc_vu,
                nv.ten_nhan_vien,
                cv.ten_chuc_vu
             FROM tai_khoan tk
             LEFT JOIN nhan_vien nv ON nv.ma_nhan_vien = tk.ma_nhan_vien
             LEFT JOIN chuc_vu cv ON cv.ma_chuc_vu = tk.ma_chuc_vu
             WHERE tk.ma_nhan_vien = :employee_id
             LIMIT 1"
        );
        $statement->execute(['employee_id' => $employeeId]);
        $account = $statement->fetch();

        return $account ?: null;
    }

    private function syncEmployeeRole(string $employeeId, string $roleId): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE nhan_vien SET ma_chuc_vu = :ma_chuc_vu WHERE ma_nhan_vien = :ma_nhan_vien'
        );
        $statement->execute([
            'ma_chuc_vu' => $roleId,
            'ma_nhan_vien' => $employeeId,
        ]);
    }
}
