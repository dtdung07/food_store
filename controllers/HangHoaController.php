<?php
require_once BASE_PATH . 'models/HangHoaModel.php';
require_once BASE_PATH . 'models/DanhMucModel.php';
require_once BASE_PATH . 'models/NhaCungCapModel.php';

class HangHoaController {
    private $hangHoaModel;
    private $danhMucModel;
    private $nhaCungCapModel;
    private $db;

    public function __construct() {
        $this->hangHoaModel    = new HangHoaModel();
        $this->danhMucModel    = new DanhMucModel();
        $this->nhaCungCapModel = new NhaCungCapModel();
        $this->db              = db();
    }

    /* ══════════════════════════════════════════════
       INDEX
    ══════════════════════════════════════════════ */
    public function index() {
        checkLogin();
        checkRole(['ADMIN', 'QUAN_LY', 'THU_KHO']);

        $page      = max(1, (int)($_GET['page']       ?? 1));
        $limit     = 15;
        $offset    = ($page - 1) * $limit;
        $keyword   = trim($_GET['keyword']    ?? '');
        $maDanhMuc = trim($_GET['ma_danh_muc'] ?? '');
        $trangThai = trim($_GET['trang_thai']  ?? '');

        $hangHoas   = $this->hangHoaModel->getFiltered($keyword, $maDanhMuc, $trangThai, $limit, $offset);
        $total      = $this->hangHoaModel->countFiltered($keyword, $maDanhMuc, $trangThai);
        $totalPages = max(1, (int)ceil($total / $limit));
        $page       = min($page, $totalPages);
        $stats      = $this->_getStats();

        $danhMucs    = $this->danhMucModel->getAll();
        $nhaCungCaps = $this->nhaCungCapModel->getAllActive();

        include BASE_PATH . 'views/layout/header.php';
        include BASE_PATH . 'views/hang_hoa/index.php';
        include BASE_PATH . 'views/layout/footer.php';
    }

    /* ══════════════════════════════════════════════
       CREATE
    ══════════════════════════════════════════════ */
    public function create() {
        checkLogin();
        checkRole(['ADMIN', 'QUAN_LY']);

        $error   = null;
        $hangHoa = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->_collectPost(true);
            $err  = $this->_validate($data, true);

            if ($err) {
                $error   = $err;
                $hangHoa = $data; // giữ lại giá trị vừa nhập
            } elseif ($this->hangHoaModel->create($data)) {
                $_SESSION['success'] = "Thêm hàng hóa «{$data['ten_hang_hoa']}» thành công!";
                header('Location: ' . BASE_URL . 'index.php?controller=hang_hoa&action=index');
                exit();
            } else {
                $error   = 'Thêm thất bại! Mã hàng hóa «' . htmlspecialchars($data['ma_hang_hoa']) . '» có thể đã tồn tại.';
                $hangHoa = $data;
            }
        }

        $danhMucs    = $this->danhMucModel->getAll();
        $nhaCungCaps = $this->nhaCungCapModel->getAllActive();
        $loHangs     = []; // create mode: chưa có lô

        include BASE_PATH . 'views/layout/header.php';
        include BASE_PATH . 'views/hang_hoa/form.php';
        include BASE_PATH . 'views/layout/footer.php';
    }

    /* ══════════════════════════════════════════════
       EDIT  (thông tin hàng hóa + số lượng lô)
    ══════════════════════════════════════════════ */
    public function edit() {
        checkLogin();
        checkRole(['ADMIN', 'QUAN_LY']);

        $ma_hang_hoa = trim($_GET['id'] ?? '');
        $hangHoa     = $this->hangHoaModel->findById($ma_hang_hoa);

        if (!$hangHoa) {
            $_SESSION['error'] = 'Không tìm thấy hàng hóa!';
            header('Location: ' . BASE_URL . 'index.php?controller=hang_hoa&action=index');
            exit();
        }

        $error   = null;
        $loHangs = $this->hangHoaModel->getLoHang($ma_hang_hoa);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // ── 1. Cập nhật thông tin hàng hóa ──────────────
            $data = $this->_collectPost(true); // withCode = true (cho sửa mã)
            $err  = $this->_validate($data, false);

            if ($err) {
                $error   = $err;
                $hangHoa = array_merge($hangHoa, $data);
            } else {
                // ── 2. Cập nhật số lượng các lô ─────────────
                $lots = [];
                foreach ($_POST['lo'] ?? [] as $maLo => $vals) {
                    $lots[] = [
                        'ma_lo_hang'          => $maLo,
                        'so_luong_trong_kho'  => $vals['so_luong_trong_kho'] ?? 0,
                        'so_luong_tren_ke'    => $vals['so_luong_tren_ke']   ?? 0,
                    ];
                }
                if (!empty($lots)) {
                    $this->hangHoaModel->updateLoHangBatch($lots);
                }

                // ── 3. Cập nhật hàng hóa ────────────────────
                if ($this->hangHoaModel->update($ma_hang_hoa, $data)) {
                    $_SESSION['success'] = "Cập nhật «{$data['ten_hang_hoa']}» thành công!";
                    header('Location: ' . BASE_URL . 'index.php?controller=hang_hoa&action=index');
                    exit();
                } else {
                    $error   = 'Cập nhật thất bại! Vui lòng thử lại.';
                    $hangHoa = array_merge($hangHoa, $data);
                }
            }

            // Reload lô hàng sau khi đã cập nhật số lượng
            $loHangs = $this->hangHoaModel->getLoHang($ma_hang_hoa);
        }

        $danhMucs    = $this->danhMucModel->getAll();
        $nhaCungCaps = $this->nhaCungCapModel->getAllActive();

        include BASE_PATH . 'views/layout/header.php';
        include BASE_PATH . 'views/hang_hoa/form.php';
        include BASE_PATH . 'views/layout/footer.php';
    }

    /* ══════════════════════════════════════════════
       DELETE
    ══════════════════════════════════════════════ */
    public function delete() {
        checkLogin();
        checkRole(['ADMIN']);

        $ma = trim($_GET['id'] ?? '');
        if ($this->hangHoaModel->delete($ma)) {
            $_SESSION['success'] = 'Xóa hàng hóa thành công!';
        } else {
            $_SESSION['error'] = 'Xóa thất bại! Hàng hóa có thể đang được sử dụng trong lô hàng hoặc hóa đơn.';
        }
        header('Location: ' . BASE_URL . 'index.php?controller=hang_hoa&action=index');
        exit();
    }

    /* ══════════════════════════════════════════════
       PRIVATE HELPERS
    ══════════════════════════════════════════════ */

    private function _collectPost(bool $withCode = true): array {
        $data = [
            'ten_hang_hoa'    => trim($_POST['ten_hang_hoa']    ?? ''),
            'don_vi_tinh'     => trim($_POST['don_vi_tinh']     ?? ''),
            'gia_ban'         => trim($_POST['gia_ban']         ?? ''),
            'ma_vach'         => trim($_POST['ma_vach']         ?? '') ?: null,
            'trang_thai'      => in_array($_POST['trang_thai'] ?? '', ['DANG_KINH_DOANH','NGUNG_KINH_DOANH'])
                                    ? $_POST['trang_thai']
                                    : 'DANG_KINH_DOANH',
            'ma_danh_muc'     => trim($_POST['ma_danh_muc']     ?? '') ?: null,
            'ma_nha_cung_cap' => trim($_POST['ma_nha_cung_cap'] ?? '') ?: null,
        ];
        if ($withCode) {
            $data['ma_hang_hoa'] = strtoupper(trim($_POST['ma_hang_hoa'] ?? ''));
        }
        return $data;
    }

    private function _validate(array $data, bool $checkCode): ?string {
        if ($checkCode && empty($data['ma_hang_hoa'])) return 'Mã hàng hóa không được để trống.';
        if (empty($data['ten_hang_hoa']))              return 'Tên hàng hóa không được để trống.';
        if (empty($data['don_vi_tinh']))               return 'Vui lòng chọn đơn vị tính.';
        if ($data['gia_ban'] === '')                   return 'Giá bán không được để trống.';
        if (!is_numeric($data['gia_ban']))             return 'Giá bán phải là số.';
        if ((float)$data['gia_ban'] < 0)               return 'Giá bán không được âm.';
        return null;
    }

    private function _getStats(): array {
        try {
            $total = (int)$this->db->query("SELECT COUNT(*) FROM hang_hoa")->fetchColumn();

            $stmt = $this->db->query(
                "SELECT h.ma_hang_hoa,
                        COALESCE(SUM(lh.so_luong_trong_kho + lh.so_luong_tren_ke), 0) AS ton_kho
                 FROM hang_hoa h
                 LEFT JOIN lo_hang lh ON lh.ma_hang_hoa = h.ma_hang_hoa
                 WHERE h.trang_thai = 'DANG_KINH_DOANH'
                 GROUP BY h.ma_hang_hoa"
            );
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $inStock = $lowStock = $outStock = 0;
            foreach ($rows as $r) {
                $q = (int)$r['ton_kho'];
                if ($q === 0)    $outStock++;
                elseif ($q < 10) $lowStock++;
                else             $inStock++;
            }
            return compact('total', 'inStock', 'lowStock', 'outStock');
        } catch (\Throwable $e) {
            return ['total' => 0, 'inStock' => 0, 'lowStock' => 0, 'outStock' => 0];
        }
    }
}