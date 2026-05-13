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
    
    //Tìm lô hàng theo mã
    public function find(string $maLoHang): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT lh.*, hh.ten_hang_hoa, hh.don_vi_tinh
             FROM lo_hang lh
             JOIN hang_hoa hh ON hh.ma_hang_hoa = lh.ma_hang_hoa
             WHERE lh.ma_lo_hang = :id LIMIT 1"
        );
        $stmt->execute(['id' => $maLoHang]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    //Lấy tất cả lô hàng của 1 mặt hàng (sắp xếp theo HSD tăng dần)
    public function findByProduct(string $maHangHoa): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM lo_hang
             WHERE ma_hang_hoa = :ma_hang_hoa
             ORDER BY han_su_dung ASC"
        );
        $stmt->execute(['ma_hang_hoa' => $maHangHoa]);

        return $stmt->fetchAll();
    }

    //Lấy lô hàng theo FIFO: chỉ lô còn tồn kho > 0, sắp xếp HSD tăng dần
    //Dùng cho xuất kho và bán hàng
    public function findByProductFIFO(string $maHangHoa): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM lo_hang
             WHERE ma_hang_hoa = :ma_hang_hoa
               AND so_luong_trong_kho > 0
             ORDER BY han_su_dung ASC"
        );
        $stmt->execute(['ma_hang_hoa' => $maHangHoa]);

        return $stmt->fetchAll();
    }

    //Tạo lô hàng mới
    public function create(array $data): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO lo_hang (ma_lo_hang, ngay_san_xuat, han_su_dung, so_luong_trong_kho, so_luong_tren_ke, ma_hang_hoa)
             VALUES (:ma_lo_hang, :ngay_san_xuat, :han_su_dung, :so_luong_trong_kho, :so_luong_tren_ke, :ma_hang_hoa)"
        );
        $stmt->execute([
            'ma_lo_hang'        => $data['ma_lo_hang'],
            'ngay_san_xuat'     => $data['ngay_san_xuat'],
            'han_su_dung'       => $data['han_su_dung'],
            'so_luong_trong_kho' => (float) ($data['so_luong_trong_kho'] ?? 0.0),
            'so_luong_tren_ke'  => (float) ($data['so_luong_tren_ke'] ?? 0.0),
            'ma_hang_hoa'       => $data['ma_hang_hoa'],
        ]);
    }

    //Kiểm tra lô hàng đã tồn tại hay chưa
    public function exists(string $maLoHang): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM lo_hang WHERE ma_lo_hang = :id LIMIT 1");
        $stmt->execute(['id' => $maLoHang]);

        return $stmt->fetchColumn() !== false;
    }

    //Cập nhật số lượng trong kho (tăng hoặc giảm)
    //Số lượng thay đổi (dương = tăng, âm =  giảm)
    public function updateKhoQty(string $maLoHang, float $delta): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE lo_hang SET so_luong_trong_kho = so_luong_trong_kho + :delta
             WHERE ma_lo_hang = :id"
        );
        $stmt->execute(['delta' => $delta, 'id' => $maLoHang]);
    }

    //Cập nhật số lượng trên kệ (tăng hoặc giảm)
    public function updateKeQty(string $maLoHang, float $delta): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE lo_hang SET so_luong_tren_ke = so_luong_tren_ke + :delta
             WHERE ma_lo_hang = :id"
        );
        $stmt->execute(['delta' => $delta, 'id' => $maLoHang]);
    }

    //Tổng tồn kho chính của 1 mặt hàng
    public function getTotalStockInKho(string $maHangHoa): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(so_luong_trong_kho), 0.0) FROM lo_hang WHERE ma_hang_hoa = :id"
        );
        $stmt->execute(['id' => $maHangHoa]);

        return (float) $stmt->fetchColumn();
    }

    //Tổng tồn trên kệ của 1 mặt hàng
    public function getTotalStockOnKe(string $maHangHoa): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(so_luong_tren_ke), 0.0) FROM lo_hang WHERE ma_hang_hoa = :id"
        );
        $stmt->execute(['id' => $maHangHoa]);

        return (float) $stmt->fetchColumn();
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

