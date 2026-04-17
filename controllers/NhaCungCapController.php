<?php
declare(strict_types=1);

require_once APP_ROOT . '/models/NhaCungCapModel.php';

class NhaCungCapController
{
    private NhaCungCapModel $nhaCungCapModel;

    public function __construct()
    {
        $this->nhaCungCapModel = new NhaCungCapModel();
    }

    public function index(): void
    {
        require_permission('nha-cung-cap');

        $keyword   = trim((string) ($_GET['q'] ?? ''));
        $trangThai = trim((string) ($_GET['trang_thai'] ?? ''));

        if ($keyword !== '' || $trangThai !== '') {
            $suppliers = $this->nhaCungCapModel->search($keyword);
            if ($trangThai !== '') {
                $suppliers = array_filter($suppliers, fn($s) => $s['trang_thai'] === $trangThai);
            }
        } else {
            $suppliers = $this->nhaCungCapModel->getAllWithProductCount();
        }

        $totalActive = $this->nhaCungCapModel->countActive();
        $totalCount  = $this->nhaCungCapModel->countAll();

        render('nha_cung_cap/index', [
            'pageTitle' => 'Quản lý nhà cung cấp',
            'suppliers' => $suppliers,
            'totalActive' => $totalActive,
            'totalCount' => $totalCount,
            'filters' => [
                'q' => $keyword,
                'trang_thai' => $trangThai,
            ]
        ]);
    }

    public function form(): void
    {
        require_permission('nha-cung-cap');

        $id = trim((string) ($_GET['id'] ?? ''));
        $supplier = $id !== '' ? $this->nhaCungCapModel->findById($id) : null;

        if ($id !== '' && $supplier === null) {
            flash('error', 'Không tìm thấy nhà cung cấp cần sửa.');
            redirect_to('nha-cung-cap', 'index');
        }

        render('nha_cung_cap/form', [
            'pageTitle' => $supplier !== null ? 'Cập nhật nhà cung cấp' : 'Thêm nhà cung cấp',
            'supplier' => $supplier,
            'errors' => [],
            'isEdit' => $supplier !== null,
        ]);
    }

    public function save(): void
    {
        require_permission('nha-cung-cap');

        if (!is_post()) {
            redirect_to('nha-cung-cap', 'index');
        }

        $isEdit = (string) ($_POST['is_edit'] ?? '0') === '1';
        $originalId = trim((string) ($_POST['original_id'] ?? ''));

        $input = [
            'ma_nha_cung_cap' => strtoupper(trim((string) ($_POST['ma_nha_cung_cap'] ?? ''))),
            'ten_nha_cung_cap' => trim((string) ($_POST['ten_nha_cung_cap'] ?? '')),
            'dia_chi' => trim((string) ($_POST['dia_chi'] ?? '')) ?: null,
            'email' => trim((string) ($_POST['email'] ?? '')) ?: null,
            'so_dien_thoai' => trim((string) ($_POST['so_dien_thoai'] ?? '')) ?: null,
            'ten_nguoi_lien_he' => trim((string) ($_POST['ten_nguoi_lien_he'] ?? '')) ?: null,
            'trang_thai' => trim((string) ($_POST['trang_thai'] ?? 'HOAT_DONG')),
        ];

        $errors = [];
        if (!$isEdit && empty($input['ma_nha_cung_cap'])) {
            $errors[] = 'Mã nhà cung cấp không được để trống.';
        }
        if (empty($input['ten_nha_cung_cap'])) {
            $errors[] = 'Tên nhà cung cấp không được để trống.';
        }

        if ($errors !== []) {
            render('nha_cung_cap/form', [
                'pageTitle' => $isEdit ? 'Cập nhật nhà cung cấp' : 'Thêm nhà cung cấp',
                'supplier' => $input,
                'errors' => $errors,
                'isEdit' => $isEdit,
            ]);
            return;
        }

        try {
            if ($isEdit) {
                $this->nhaCungCapModel->update($originalId, $input);
                flash('success', 'Cập nhật nhà cung cấp thành công.');
            } else {
                if ($this->nhaCungCapModel->findById($input['ma_nha_cung_cap']) !== null) {
                    throw new Exception('Mã nhà cung cấp đã tồn tại.');
                }
                $this->nhaCungCapModel->create($input);
                flash('success', 'Thêm nhà cung cấp thành công.');
            }
            redirect_to('nha-cung-cap', 'index');
        } catch (Throwable $exception) {
            $errors[] = $exception->getMessage();
            render('nha_cung_cap/form', [
                'pageTitle' => $isEdit ? 'Cập nhật nhà cung cấp' : 'Thêm nhà cung cấp',
                'supplier' => $input,
                'errors' => $errors,
                'isEdit' => $isEdit,
            ]);
        }
    }

    public function delete(): void
    {
        require_permission('nha-cung-cap');

        $user = current_user();
        $isAjax = expects_json_response();
        if (($user['ma_chuc_vu'] ?? '') !== 'ADMIN') {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Chỉ tài khoản ADMIN mới được phép xoá nhà cung cấp.']);
                exit;
            }
            flash('error', 'Chỉ tài khoản ADMIN mới được phép xoá nhà cung cấp.');
            redirect_to('nha-cung-cap', 'index');
        }

        if (!is_post()) {
            redirect_to('nha-cung-cap', 'index');
        }

        $id = trim((string) ($_POST['ma_nha_cung_cap'] ?? ''));

        if ($id === '') {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy mã nhà cung cấp để xóa.']);
                exit;
            }
            flash('error', 'Không tìm thấy mã nhà cung cấp để xóa.');
            redirect_to('nha-cung-cap', 'index');
        }

        try {
            if ($this->nhaCungCapModel->delete($id)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Xóa nhà cung cấp thành công.']);
                    exit;
                }
                flash('success', 'Xóa nhà cung cấp thành công.');
            } else {
                throw new Exception('Không thể xóa nhà cung cấp này. Có thể nhà cung cấp đang tồn tại trong lô hàng hoặc hóa đơn.');
            }
        } catch (Throwable $exception) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => db_error_message($exception)]);
                exit;
            }
            flash('error', db_error_message($exception));
        }

        redirect_to('nha-cung-cap', 'index');
    }
}
