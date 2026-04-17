<?php
declare(strict_types=1);

class PhieuNhapModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function getTotalImportToday(): float
    {
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(tong_tien), 0) FROM phieu_nhap_hang WHERE ngay_tao = CURDATE()");
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }
}
