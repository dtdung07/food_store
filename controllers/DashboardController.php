<?php
require_once BASE_PATH . 'models/HangHoaModel.php';
require_once BASE_PATH . 'models/HoaDonModel.php';
require_once BASE_PATH . 'models/LoHangModel.php';
require_once BASE_PATH . 'models/NhaCungCapModel.php';
require_once BASE_PATH . 'models/PhieuNhapModel.php';
require_once BASE_PATH . 'models/DanhMucModel.php';

class DashboardController {
    private $hangHoaModel;
    private $hoaDonModel;
    private $loHangModel;
    private $nhaCungCapModel;
    private $phieuNhapModel;
    private $danhMucModel;
    private $db;

    public function __construct() {
        $this->hangHoaModel   = new HangHoaModel();
        $this->hoaDonModel    = new HoaDonModel();
        $this->loHangModel    = new LoHangModel();
        $this->nhaCungCapModel= new NhaCungCapModel();
        $this->phieuNhapModel = new PhieuNhapModel();
        $this->danhMucModel   = new DanhMucModel();
        $this->db             = db();
    }

    public function index() {
        checkLogin();

        /* ── 1. KPI CARDS ─────────────────────────────────── */
        $revenueToday     = $this->hoaDonModel->getRevenueToday();
        $revenueYesterday = $this->_getRevenueYesterday();
        $revChange        = ($revenueYesterday > 0)
            ? round(($revenueToday - $revenueYesterday) / $revenueYesterday * 100, 1)
            : 0;

        $invoicesToday     = $this->hoaDonModel->countToday();
        $invoicesYesterday = $this->_countInvoicesYesterday();
        $invoiceDiff       = $invoicesToday - $invoicesYesterday;

        $totalSkus          = $this->hangHoaModel->countAll();
        $expiringSoonCount  = $this->loHangModel->countExpiringSoon(7);
        $pendingCancel      = $this->_countPendingCancellations();

        $stats = [
            'revenue_today'         => $revenueToday,
            'revenue_yesterday'     => $revenueYesterday,
            'rev_change_pct'        => $revChange,
            'total_invoices_today'  => $invoicesToday,
            'invoice_diff'          => $invoiceDiff,
            'total_skus'            => $totalSkus,
            'expiring_soon_count'   => $expiringSoonCount,
            'pending_cancellations' => $pendingCancel,
        ];

        /* ── 2. DOANH THU 7 NGÀY ──────────────────────────── */
        $revenue7days = $this->_getRevenue7Days();

        /* ── 3. NGƯỜI DÙNG TRỰC TUYẾN ────────────────────── */
        // Lấy danh sách tài khoản đang hoạt động (online = session còn tồn tại).
        // Nếu hệ thống chưa có bảng session riêng, fallback lấy 5 tài khoản gần nhất đăng nhập.
        $onlineUsers = $this->_getOnlineUsers(5);

        /* ── 4. HÀNG SẮP HẾT HẠN (tối đa 10) ───────────── */
        $expiringItems = $this->loHangModel->getExpiringSoon(7, 10);

        /* ── 5. TỶ TRỌNG DANH MỤC ────────────────────────── */
        $categoryDistribution = $this->_getCategoryDistribution();

        /* ── 6. HOẠT ĐỘNG GẦN ĐÂY (tối đa 5) ───────────── */
        $activityItems = $this->_getRecentActivity(5);

        /* ── 7. TÌNH TRẠNG KHO THEO DANH MỤC (tối đa 5) ── */
        $stockCategories = $this->_getStockByCategory(5);

        include BASE_PATH . 'views/layout/header.php';
        include BASE_PATH . 'views/dashboard/index.php';
        include BASE_PATH . 'views/layout/footer.php';
    }

    /* ══════════════════════════════════════════════════════
       PRIVATE HELPERS
    ══════════════════════════════════════════════════════ */

    private function _getRevenueYesterday(): float {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(tong_tien),0)
             FROM hoa_don
             WHERE DATE(ngay_tao) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
               AND trang_thai = 'HOAN_TAT'"
        );
        $stmt->execute();
        return (float)$stmt->fetchColumn();
    }

    private function _countInvoicesYesterday(): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*)
             FROM hoa_don
             WHERE DATE(ngay_tao) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
               AND trang_thai = 'HOAN_TAT'"
        );
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    private function _countPendingCancellations(): int {
        // Thử bảng phieu_huy — nếu bảng chưa tồn tại trả về 0
        try {
            $stmt = $this->db->query(
                "SELECT COUNT(*) FROM phieu_huy WHERE trang_thai = 'CHO_DUYET'"
            );
            return (int)$stmt->fetchColumn();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function _getRevenue7Days(): array {
        $stmt = $this->db->prepare(
            "SELECT DATE(ngay_tao) AS ngay, COALESCE(SUM(tong_tien),0) AS tong
             FROM hoa_don
             WHERE ngay_tao >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
               AND trang_thai = 'HOAN_TAT'
             GROUP BY DATE(ngay_tao)
             ORDER BY ngay ASC"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Điền đủ 7 ngày kể cả ngày không có doanh thu
        $map = [];
        foreach ($rows as $r) $map[$r['ngay']] = (float)$r['tong'];

        $vi = ['Monday'=>'T2','Tuesday'=>'T3','Wednesday'=>'T4',
               'Thursday'=>'T5','Friday'=>'T6','Saturday'=>'T7','Sunday'=>'CN'];
        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date  = date('Y-m-d', strtotime("-{$i} days"));
            $label = $vi[date('l', strtotime($date))] ?? date('d/m', strtotime($date));
            $result[] = [
                'label' => $label,
                'val'   => round(($map[$date] ?? 0) / 1_000_000, 2),   // triệu đồng
                'date'  => $date,
            ];
        }
        return $result;
    }

    private function _getOnlineUsers(int $limit): array {
        // Ưu tiên bảng user_sessions nếu có; nếu không lấy tài khoản hoạt động
        try {
            $stmt = $this->db->prepare(
                "SELECT tk.ten_dang_nhap, nv.ten_nhan_vien AS name,
                        cv.ten_chuc_vu AS role,
                        CASE WHEN us.last_active >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                             THEN 'online' ELSE 'offline' END AS status,
                        us.last_active
                 FROM user_sessions us
                 JOIN tai_khoan tk ON us.ma_tai_khoan = tk.ma_tai_khoan
                 JOIN nhan_vien nv ON tk.ma_nhan_vien = nv.ma_nhan_vien
                 JOIN chuc_vu   cv ON tk.ma_chuc_vu   = cv.ma_chuc_vu
                 WHERE us.last_active >= DATE_SUB(NOW(), INTERVAL 8 HOUR)
                 ORDER BY us.last_active DESC
                 LIMIT :lim"
            );
            $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if ($rows) return $rows;
        } catch (\Throwable $e) { /* bảng chưa có */ }

        // Fallback: lấy tài khoản hoạt động gần đây nhất
        try {
            $stmt = $this->db->prepare(
                "SELECT nv.ten_nhan_vien AS name,
                        cv.ten_chuc_vu   AS role,
                        'online'         AS status
                 FROM tai_khoan tk
                 JOIN nhan_vien nv ON tk.ma_nhan_vien = nv.ma_nhan_vien
                 JOIN chuc_vu   cv ON tk.ma_chuc_vu   = cv.ma_chuc_vu
                 WHERE tk.trang_thai = 'HOAT_DONG'
                 ORDER BY tk.ma_tai_khoan DESC
                 LIMIT :lim"
            );
            $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function _getCategoryDistribution(): array {
        $stmt = $this->db->query(
            "SELECT d.ten_danh_muc AS name, COUNT(h.ma_hang_hoa) AS count
             FROM danh_muc d
             LEFT JOIN hang_hoa h ON h.ma_danh_muc = d.ma_danh_muc
                                  AND h.trang_thai = 'DANG_KINH_DOANH'
             GROUP BY d.ma_danh_muc, d.ten_danh_muc
             ORDER BY count DESC"
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function _getRecentActivity(int $limit): array {
        $activities = [];

        // Hóa đơn hoàn tất hôm nay
        try {
            $stmt = $this->db->prepare(
                "SELECT hd.ma_hoa_don AS code,
                        hd.tong_tien  AS amount,
                        hd.ngay_tao   AS created_at,
                        nv.ten_nhan_vien AS staff,
                        'hoa_don'     AS type
                 FROM hoa_don hd
                 LEFT JOIN tai_khoan tk ON hd.ma_tai_khoan = tk.ma_tai_khoan
                 LEFT JOIN nhan_vien nv ON tk.ma_nhan_vien = nv.ma_nhan_vien
                 WHERE DATE(hd.ngay_tao) = CURDATE()
                   AND hd.trang_thai = 'HOAN_TAT'
                 ORDER BY hd.ngay_tao DESC
                 LIMIT 3"
            );
            $stmt->execute();
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $r) {
                $activities[] = [
                    'title'  => 'Hóa đơn #' . $r['code'] . ' hoàn tất — ' . number_format($r['amount']) . 'đ',
                    'detail' => $r['staff'] ?? 'Thu ngân',
                    'time'   => date('H:i', strtotime($r['created_at'])),
                    'color'  => 'green',
                    'ts'     => strtotime($r['created_at']),
                ];
            }
        } catch (\Throwable $e) {}

        // Phiếu nhập hôm nay
        try {
            $stmt = $this->db->prepare(
                "SELECT pn.ma_phieu_nhap AS code, pn.ngay_tao AS created_at,
                        pn.tong_tien AS amount,
                        ncc.ten_nha_cung_cap AS supplier,
                        'phieu_nhap' AS type
                 FROM phieu_nhap_hang pn
                 LEFT JOIN nha_cung_cap ncc ON pn.ma_nha_cung_cap = ncc.ma_nha_cung_cap
                 WHERE DATE(pn.ngay_tao) = CURDATE()
                 ORDER BY pn.ngay_tao DESC
                 LIMIT 2"
            );
            $stmt->execute();
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $r) {
                $activities[] = [
                    'title'  => 'Nhập hàng #' . $r['code'],
                    'detail' => $r['supplier'] ?? 'NCC',
                    'time'   => date('H:i', strtotime($r['created_at'])),
                    'color'  => 'amber',
                    'ts'     => strtotime($r['created_at']),
                ];
            }
        } catch (\Throwable $e) {}

        // Phiếu hủy hôm nay
        try {
            $stmt = $this->db->prepare(
                "SELECT ph.ma_phieu_huy AS code, ph.ngay_tao AS created_at,
                        ph.ly_do AS reason, 'phieu_huy' AS type
                 FROM phieu_huy ph
                 WHERE DATE(ph.ngay_tao) = CURDATE()
                 ORDER BY ph.ngay_tao DESC
                 LIMIT 2"
            );
            $stmt->execute();
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $r) {
                $activities[] = [
                    'title'  => 'Phiếu hủy #' . $r['code'] . ' chờ duyệt',
                    'detail' => mb_strimwidth($r['reason'] ?? '', 0, 40, '…'),
                    'time'   => date('H:i', strtotime($r['created_at'])),
                    'color'  => 'red',
                    'ts'     => strtotime($r['created_at']),
                ];
            }
        } catch (\Throwable $e) {}

        // Sắp xếp theo thời gian mới nhất
        usort($activities, fn($a,$b) => $b['ts'] - $a['ts']);
        return array_slice($activities, 0, $limit);
    }

    private function _getStockByCategory(int $limit): array {
        try {
            $stmt = $this->db->prepare(
                "SELECT d.ten_danh_muc AS name,
                        COUNT(DISTINCT h.ma_hang_hoa) AS sku,
                        COUNT(DISTINCT lh.ma_lo_hang) AS lots,
                        SUM(CASE WHEN lh.han_su_dung <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                                  AND (lh.so_luong_trong_kho > 0 OR lh.so_luong_tren_ke > 0)
                             THEN 1 ELSE 0 END) AS expiring_count,
                        COALESCE(SUM((lh.so_luong_trong_kho + lh.so_luong_tren_ke) * h.gia_ban), 0) AS stock_value
                 FROM danh_muc d
                 LEFT JOIN hang_hoa h ON h.ma_danh_muc = d.ma_danh_muc
                 LEFT JOIN lo_hang lh ON lh.ma_hang_hoa = h.ma_hang_hoa
                 GROUP BY d.ma_danh_muc, d.ten_danh_muc
                 ORDER BY sku DESC
                 LIMIT :lim"
            );
            $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return array_map(function($r) {
                $exp   = (int)$r['expiring_count'];
                $status = $exp >= 3 ? 'alert' : ($exp >= 1 ? 'watch' : 'ok');
                $val   = (float)$r['stock_value'];
                return [
                    'name'   => $r['name'],
                    'sku'    => (int)$r['sku'],
                    'lots'   => (int)$r['lots'],
                    'alert'  => $exp > 0 ? "{$exp} mặt hàng" : '0 mặt hàng',
                    'exp'    => $exp,
                    'value'  => ($val >= 1_000_000)
                        ? number_format($val/1_000_000, 1) . 'M đ'
                        : number_format($val) . 'đ',
                    'status' => $status,
                ];
            }, $rows);
        } catch (\Throwable $e) {
            return [];
        }
    }
}