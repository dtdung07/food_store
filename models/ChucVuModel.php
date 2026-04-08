<?php
declare(strict_types=1);

class ChucVuModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function all(): array
    {
        $statement = $this->pdo->query(
            'SELECT ma_chuc_vu, ten_chuc_vu FROM chuc_vu ORDER BY ten_chuc_vu ASC'
        );

        return $statement->fetchAll();
    }
}
