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
        $nextId = $hangHoa === null ? $this->hangHoaModel->generateId() : '';

        render('hang_hoa/form', [
            'pageTitle' => $hangHoa !== null ? 'Cập nhật hàng hóa' : 'Thêm hàng hóa',
            'hangHoa' => $hangHoa,
            'loHangs' => $loHangs,
            'danhMucs' => $danhMucs,
            'nhaCungCaps' => $nhaCungCaps,
            'errors' => [],
            'isEdit' => $hangHoa !== null,
            'nextId' => $nextId,
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

        $isScale = (string) ($_POST['is_scale'] ?? '0') === '1';
        $scaleCodeInput = trim((string) ($_POST['ma_tem_can'] ?? ''));
        $maTemCan = $isScale && $scaleCodeInput !== '' ? $scaleCodeInput : null;

        $input = [
            'ma_hang_hoa' => strtoupper(trim((string) ($_POST['ma_hang_hoa'] ?? ''))),
            'ten_hang_hoa' => trim((string) ($_POST['ten_hang_hoa'] ?? '')),
            'don_vi_tinh' => trim((string) ($_POST['don_vi_tinh'] ?? '')),
            'gia_ban' => trim((string) ($_POST['gia_ban'] ?? '')),
            'ma_vach' => trim((string) ($_POST['ma_vach'] ?? '')) ?: null,
            'ma_tem_can' => $maTemCan,
            'trang_thai' => trim((string) ($_POST['trang_thai'] ?? 'DANG_KINH_DOANH')),
            'ma_danh_muc' => trim((string) ($_POST['ma_danh_muc'] ?? '')) ?: null,
            'ma_nha_cung_cap' => trim((string) ($_POST['ma_nha_cung_cap'] ?? '')) ?: null,
        ];

        $errors = $this->_validate($input, $isEdit, $isEdit ? $originalId : null);

        if ($errors !== []) {
            $danhMucs = $this->danhMucModel->getAll();
            $nhaCungCaps = $this->nhaCungCapModel->getAllActive();
            $loHangs = $isEdit ? $this->hangHoaModel->getLoHang($originalId) : [];
            $nextId = !$isEdit ? $this->hangHoaModel->generateId() : '';

            render('hang_hoa/form', [
                'pageTitle' => $isEdit ? 'Cập nhật hàng hóa' : 'Thêm hàng hóa',
                'hangHoa' => $input,
                'loHangs' => $loHangs,
                'danhMucs' => $danhMucs,
                'nhaCungCaps' => $nhaCungCaps,
                'errors' => $errors,
                'isEdit' => $isEdit,
                'nextId' => $nextId,
            ]);
            return;
        }

        try {
            if ($isEdit) {
                $hangHoa = $this->hangHoaModel->findById($originalId);
                $isHangDongGoi = ($hangHoa !== null && empty($hangHoa['ma_tem_can']));

                $lots = [];
                foreach ($_POST['lo'] ?? [] as $maLo => $vals) {
                    $trongKho = (float) ($vals['so_luong_trong_kho'] ?? 0.0);
                    $trenKe = (float) ($vals['so_luong_tren_ke'] ?? 0.0);

                    if ($isHangDongGoi) {
                        if (fmod($trongKho, 1.0) !== 0.0 || fmod($trenKe, 1.0) !== 0.0) {
                            throw new Exception("Sản phẩm là hàng đóng gói, số lượng tồn kho/tồn kệ phải là số nguyên.");
                        }
                    }

                    $lots[] = [
                        'ma_lo_hang' => $maLo,
                        'so_luong_trong_kho' => $trongKho,
                        'so_luong_tren_ke' => $trenKe,
                    ];
                }
                if ($lots !== []) {
                    $this->hangHoaModel->updateLoHangBatch($lots);
                }

                $this->hangHoaModel->update($originalId, $input);
                flash('success', 'Cập nhật hàng hóa thành công.');
            } else {
                if (empty($input['ma_hang_hoa']) || $input['ma_hang_hoa'] === '(TỰ ĐỘNG SINH)' || $input['ma_hang_hoa'] === '(TỰ ĐỘNG SINH)' || stripos($input['ma_hang_hoa'], 'tự động sinh') !== false) {
                    $input['ma_hang_hoa'] = $this->hangHoaModel->generateId();
                }
                if ($this->hangHoaModel->findById($input['ma_hang_hoa']) !== null) {
                    $input['ma_hang_hoa'] = $this->hangHoaModel->generateId();
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
        $role = $user['ma_chuc_vu'] ?? '';
        if ($role !== 'ADMIN' && $role !== 'QUAN_LY') {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Chỉ tài khoản ADMIN hoặc Quản lý mới được phép xoá sản phẩm.']);
                exit;
            }
            flash('error', 'Chỉ tài khoản ADMIN hoặc Quản lý mới được phép xoá sản phẩm.');
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

        if ($this->hangHoaModel->hasRelations($id)) {
            $msg = 'Không thể xóa hàng hóa này vì đã phát sinh lịch sử giao dịch (lô hàng, hóa đơn hoặc phiếu nhập/xuất/hủy). Bạn nên chuyển sang trạng thái \'Ngừng kinh doanh\'.';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            flash('error', $msg);
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
            $msg = $exception->getMessage();
            if (stripos($msg, 'foreign key') !== false) {
                $msg = 'Không thể xóa hàng hóa này vì đã phát sinh lịch sử giao dịch (hóa đơn, lô hàng, phiếu nhập/xuất/hủy). Bạn nên chuyển sang trạng thái \'Ngừng kinh doanh\'.';
            }
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            flash('error', $msg);
        }

        redirect_to('hang-hoa', 'index');
    }

    public function next_scale_code(): void
    {
        require_permission('hang-hoa');
        header('Content-Type: application/json');
        try {
            $code = $this->hangHoaModel->getNextScaleCode();
            echo json_encode(['success' => true, 'scale_code' => $code]);
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    private function _validate(array $data, bool $checkCode, ?string $originalId = null): array
    {
        $errors = [];
        if ($checkCode && empty($data['ma_hang_hoa'])) {
            $errors[] = 'Mã hàng hóa không được để trống.';
        }
        if (empty($data['ten_hang_hoa'])) {
            $errors[] = 'Tên hàng hóa không được để trống.';
        } else {
            if ($this->hangHoaModel->isNameExists($data['ten_hang_hoa'], $originalId)) {
                $errors[] = "Tên hàng hóa '{$data['ten_hang_hoa']}' đã được sử dụng cho hàng hóa khác.";
            }
        }
        if (empty($data['don_vi_tinh'])) {
            $errors[] = 'Đơn vị tính không được để trống.';
        }
        if ($data['gia_ban'] === '') {
            $errors[] = 'Giá bán không được để trống.';
        } elseif (!is_numeric($data['gia_ban'])) {
            $errors[] = 'Giá bán phải là số.';
        } elseif ((float) $data['gia_ban'] <= 0) {
            $errors[] = 'Giá bán phải lớn hơn 0.';
        }
        if ($data['ma_tem_can'] !== null && $data['ma_tem_can'] !== '') {
            if (preg_match('/^\d{5}$/', $data['ma_tem_can']) !== 1) {
                $errors[] = 'Mã tem cân phải gồm đúng 5 chữ số.';
            } elseif ($this->hangHoaModel->isScaleCodeExists($data['ma_tem_can'], $originalId)) {
                $errors[] = "Mã tem cân '{$data['ma_tem_can']}' đã được sử dụng cho hàng hóa khác.";
            }
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
                $q = (float) $r['ton_kho'];
                if ($q === 0.0) {
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
