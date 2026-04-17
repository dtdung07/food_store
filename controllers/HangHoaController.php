<?php
declare(strict_types=1);

require_once APP_ROOT . '/models/HangHoaModel.php';
require_once APP_ROOT . '/models/DanhMucModel.php';
require_once APP_ROOT . '/models/NhaCungCapModel.php';

class HangHoaController
{
    private HangHoaModel $hangHoaModel;
    private DanhMucModel $danhMucModel;
    private NhaCungCapModel $nhaCungCapModel;
    private PDO $db;

    public function __construct()
    {
        $this->hangHoaModel    = new HangHoaModel();
        $this->danhMucModel    = new DanhMucModel();
        $this->nhaCungCapModel = new NhaCungCapModel();
        $this->db              = db();
    }

    public function index(): void
    {
        require_permission('hang-hoa');

        $keyword   = trim((string) ($_GET['q'] ?? ''));
        $maDanhMuc = trim((string) ($_GET['ma_danh_muc'] ?? ''));
        $trangThai = trim((string) ($_GET['trang_thai'] ?? ''));

        $hangHoas = $this->hangHoaModel->getFiltered($keyword, $maDanhMuc, $trangThai);
        $danhMucs = $this->danhMucModel->getAll();
        $stats = $this->_getStats();

        render('hang_hoa/index', [
            'pageTitle' => 'Quản lý hàng hóa',
            'hangHoas' => $hangHoas,
            'danhMucs' => $danhMucs,
            'stats' => $stats,
            'filters' => [
                'q' => $keyword,
                'ma_danh_muc' => $maDanhMuc,
                'trang_thai' => $trangThai,
            ]
        ]);
    }

    public function form(): void
    {
        require_permission('hang-hoa');

        $id = trim((string) ($_GET['id'] ?? ''));
        $hangHoa = $id !== '' ? $this->hangHoaModel->findById($id) : null;

        if ($id !== '' && $hangHoa === null) {
            flash('error', 'Không tìm thấy hàng hóa cần sửa.');
            redirect_to('hang-hoa', 'index');
        }

        $loHangs = $id !== '' ? $this->hangHoaModel->getLoHang($id) : [];
        $danhMucs = $this->danhMucModel->getAll();
        $nhaCungCaps = $this->nhaCungCapModel->getAllActive();

        render('hang_hoa/form', [
            'pageTitle' => $hangHoa !== null ? 'Cập nhật hàng hóa' : 'Thêm hàng hóa',
            'hangHoa' => $hangHoa,
            'loHangs' => $loHangs,
            'danhMucs' => $danhMucs,
            'nhaCungCaps' => $nhaCungCaps,
            'errors' => [],
            'isEdit' => $hangHoa !== null,
        ]);
    }

    public function save(): void
    {
        require_permission('hang-hoa');

        if (!is_post()) {
            redirect_to('hang-hoa', 'index');
        }

        $isEdit = (string) ($_POST['is_edit'] ?? '0') === '1';
        $originalId = trim((string) ($_POST['original_id'] ?? ''));

        $input = [
            'ma_hang_hoa' => strtoupper(trim((string) ($_POST['ma_hang_hoa'] ?? ''))),
            'ten_hang_hoa' => trim((string) ($_POST['ten_hang_hoa'] ?? '')),
            'don_vi_tinh' => trim((string) ($_POST['don_vi_tinh'] ?? '')),
            'gia_ban' => trim((string) ($_POST['gia_ban'] ?? '')),
            'ma_vach' => trim((string) ($_POST['ma_vach'] ?? '')) ?: null,
            'trang_thai' => trim((string) ($_POST['trang_thai'] ?? 'DANG_KINH_DOANH')),
            'ma_danh_muc' => trim((string) ($_POST['ma_danh_muc'] ?? '')) ?: null,
            'ma_nha_cung_cap' => trim((string) ($_POST['ma_nha_cung_cap'] ?? '')) ?: null,
        ];

        $errors = $this->_validate($input, !$isEdit);

        if ($errors !== []) {
            $danhMucs = $this->danhMucModel->getAll();
            $nhaCungCaps = $this->nhaCungCapModel->getAllActive();
            $loHangs = $isEdit ? $this->hangHoaModel->getLoHang($originalId) : [];

            render('hang_hoa/form', [
                'pageTitle' => $isEdit ? 'Cập nhật hàng hóa' : 'Thêm hàng hóa',
                'hangHoa' => $input,
                'loHangs' => $loHangs,
                'danhMucs' => $danhMucs,
                'nhaCungCaps' => $nhaCungCaps,
                'errors' => $errors,
                'isEdit' => $isEdit,
            ]);
            return;
        }

        try {
            if ($isEdit) {
                $lots = [];
                foreach ($_POST['lo'] ?? [] as $maLo => $vals) {
                    $lots[] = [
                        'ma_lo_hang' => $maLo,
                        'so_luong_trong_kho' => (int) ($vals['so_luong_trong_kho'] ?? 0),
                        'so_luong_tren_ke' => (int) ($vals['so_luong_tren_ke'] ?? 0),
                    ];
                }
                if ($lots !== []) {
                    $this->hangHoaModel->updateLoHangBatch($lots);
                }

                $this->hangHoaModel->update($originalId, $input);
                flash('success', 'Cập nhật hàng hóa thành công.');
            } else {
                if ($this->hangHoaModel->findById($input['ma_hang_hoa']) !== null) {
                    throw new Exception('Mã hàng hóa đã tồn tại.');
                }
                $this->hangHoaModel->create($input);
                flash('success', 'Thêm hàng hóa thành công.');
            }
            redirect_to('hang-hoa', 'index');
        } catch (Throwable $exception) {
            $danhMucs = $this->danhMucModel->getAll();
            $nhaCungCaps = $this->nhaCungCapModel->getAllActive();
            $loHangs = $isEdit ? $this->hangHoaModel->getLoHang($originalId) : [];

            $errors[] = $exception->getMessage();
            render('hang_hoa/form', [
                'pageTitle' => $isEdit ? 'Cập nhật hàng hóa' : 'Thêm hàng hóa',
                'hangHoa' => $input,
                'loHangs' => $loHangs,
                'danhMucs' => $danhMucs,
                'nhaCungCaps' => $nhaCungCaps,
                'errors' => $errors,
                'isEdit' => $isEdit,
            ]);
        }
    }

    public function delete(): void
    {
        require_permission('hang-hoa');

        $user = current_user();
        $isAjax = expects_json_response();
        if (($user['ma_chuc_vu'] ?? '') !== 'ADMIN') {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Chỉ tài khoản ADMIN mới được phép xoá sản phẩm.']);
                exit;
            }
            flash('error', 'Chỉ tài khoản ADMIN mới được phép xoá sản phẩm.');
            redirect_to('hang-hoa', 'index');
        }

        if (!is_post()) {
            redirect_to('hang-hoa', 'index');
        }

        $id = trim((string) ($_POST['ma_hang_hoa'] ?? ''));

        if ($id === '') {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy mã hàng hóa để xóa.']);
                exit;
            }
            flash('error', 'Không tìm thấy mã hàng hóa để xóa.');
            redirect_to('hang-hoa', 'index');
        }

        try {
            if ($this->hangHoaModel->delete($id)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Xóa hàng hóa thành công.']);
                    exit;
                }
                flash('success', 'Xóa hàng hóa thành công.');
            } else {
                throw new Exception('Không thể xóa hàng hóa này. Có thể sản phẩm đang tồn tại trong lô hàng hoặc hóa đơn.');
            }
        } catch (Throwable $exception) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $exception->getMessage()]);
                exit;
            }
            flash('error', $exception->getMessage());
        }

        redirect_to('hang-hoa', 'index');
    }

    private function _validate(array $data, bool $checkCode): array
    {
        $errors = [];
        if ($checkCode && empty($data['ma_hang_hoa'])) {
            $errors[] = 'Mã hàng hóa không được để trống.';
        }
        if (empty($data['ten_hang_hoa'])) {
            $errors[] = 'Tên hàng hóa không được để trống.';
        }
        if (empty($data['don_vi_tinh'])) {
            $errors[] = 'Đơn vị tính không được để trống.';
        }
        if ($data['gia_ban'] === '') {
            $errors[] = 'Giá bán không được để trống.';
        } elseif (!is_numeric($data['gia_ban'])) {
            $errors[] = 'Giá bán phải là số.';
        } elseif ((float) $data['gia_ban'] < 0) {
            $errors[] = 'Giá bán không được âm.';
        }
        return $errors;
    }

    private function _getStats(): array
    {
        try {
            $total = (int) $this->db->query("SELECT COUNT(*) FROM hang_hoa")->fetchColumn();

            $stmt = $this->db->query(
                "SELECT h.ma_hang_hoa,
                        COALESCE(SUM(lh.so_luong_trong_kho + lh.so_luong_tren_ke), 0) AS ton_kho
                 FROM hang_hoa h
                 LEFT JOIN lo_hang lh ON lh.ma_hang_hoa = h.ma_hang_hoa
                 WHERE h.trang_thai = 'DANG_KINH_DOANH'
                 GROUP BY h.ma_hang_hoa"
            );
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $inStock = $lowStock = $outStock = 0;
            foreach ($rows as $r) {
                $q = (int) $r['ton_kho'];
                if ($q === 0) {
                    $outStock++;
                } elseif ($q < 100) {
                    $lowStock++;
                } else {
                    $inStock++;
                }
            }
            return compact('total', 'inStock', 'lowStock', 'outStock');
        } catch (Throwable $e) {
            return ['total' => 0, 'inStock' => 0, 'lowStock' => 0, 'outStock' => 0];
        }
    }
}
