<?php
declare(strict_types=1);

require_once APP_ROOT . '/models/TaiKhoanModel.php';
require_once APP_ROOT . '/models/ChucVuModel.php';

class TaiKhoanController
{
    private TaiKhoanModel $taiKhoanModel;
    private ChucVuModel $chucVuModel;

    public function __construct()
    {
        $this->taiKhoanModel = new TaiKhoanModel();
        $this->chucVuModel = new ChucVuModel();
    }

    public function index(): void
    {
        require_permission('tai-khoan');

        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? 'all')),
        ];
        
        $accounts = $this->taiKhoanModel->search($filters['q'], $filters['status']);

        render('tai_khoan/index', [
            'pageTitle' => 'Quản lý người dùng',
            'accounts' => $accounts,
            'filters' => $filters,
        ]);
    }

    public function form(): void
    {
        require_permission('tai-khoan');

        $id = (int) ($_GET['id'] ?? 0);
        $account = $id > 0 ? $this->taiKhoanModel->findById($id) : null;

        if ($id > 0 && $account === null) {
            flash('error', 'Không tìm thấy tài khoản cần sửa.');
            redirect_to('tai-khoan', 'index');
        }

        $this->renderForm($account, [], $account !== null);
    }

    public function save(): void
    {
        require_permission('tai-khoan');

        if (!is_post()) {
            redirect_to('tai-khoan', 'index');
        }

        $input = $this->collectInput();
        $isEdit = $input['ma_tai_khoan'] > 0;
        $errors = $this->validate($input, $isEdit);
        $currentUser = current_user();

        if ($currentUser !== null && (int) ($currentUser['ma_tai_khoan'] ?? 0) === $input['ma_tai_khoan'] && $input['trang_thai'] !== 'HOAT_DONG') {
            $errors[] = 'Không thể tự vô hiệu hóa tài khoản đang đăng nhập.';
        }

        if ($errors !== []) {
            $this->renderForm($input, $errors, $isEdit);
            return;
        }

        try {
            if ($isEdit) {
                $this->taiKhoanModel->update($input['ma_tai_khoan'], $input);
                flash('success', 'Cập nhật tài khoản thành công.');
            } else {
                $newId = $this->taiKhoanModel->create($input);
                $input['ma_tai_khoan'] = $newId;
                flash('success', 'Thêm tài khoản thành công.');
            }

            if ($currentUser !== null && (int) ($currentUser['ma_tai_khoan'] ?? 0) === $input['ma_tai_khoan']) {
                $freshUser = $this->taiKhoanModel->sessionPayloadByAccountId($input['ma_tai_khoan']);
                if ($freshUser !== null) {
                    login_user($freshUser);
                }
            }

            redirect_to('tai-khoan', 'index');
        } catch (Throwable $exception) {
            $errors[] = db_error_message($exception);
            $this->renderForm($input, $errors, $isEdit);
        }
    }

    public function delete(): void
    {
        require_permission('tai-khoan');

        if (!is_post()) {
            redirect_to('tai-khoan', 'index');
        }

        $id = (int) ($_POST['ma_tai_khoan'] ?? 0);
        $currentUser = current_user();

        $isAjax = isset($_SERVER['HTTP_HX_REQUEST']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if ($id <= 0) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Không có tài khoản để xóa.']);
                exit;
            }
            flash('error', 'Không có tài khoản để xóa.');
            redirect_to('tai-khoan', 'index');
        }

        if ($currentUser !== null && (int) ($currentUser['ma_tai_khoan'] ?? 0) === $id) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Không thể xóa tài khoản đang đăng nhập.']);
                exit;
            }
            flash('error', 'Không thể xóa tài khoản đang đăng nhập.');
            redirect_to('tai-khoan', 'index');
        }

        try {
            $this->taiKhoanModel->delete($id);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Đã xóa tài khoản.']);
                exit;
            }
            flash('success', 'Đã xóa tài khoản.');
        } catch (Throwable $exception) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => db_error_message($exception)]);
                exit;
            }
            flash('error', db_error_message($exception));
        }

        redirect_to('tai-khoan', 'index');
    }

    private function collectInput(): array
    {
        return [
            'ma_tai_khoan' => (int) ($_POST['ma_tai_khoan'] ?? 0),
            'ten_dang_nhap' => trim((string) ($_POST['ten_dang_nhap'] ?? '')),
            'password' => (string) ($_POST['password'] ?? ''),
            'trang_thai' => trim((string) ($_POST['trang_thai'] ?? 'HOAT_DONG')),
            'ma_nhan_vien' => trim((string) ($_POST['ma_nhan_vien'] ?? '')),
            'ma_chuc_vu' => trim((string) ($_POST['ma_chuc_vu'] ?? '')),
        ];
    }

    private function validate(array $input, bool $isEdit): array
    {
        $errors = [];

        if ($input['ten_dang_nhap'] === '') {
            $errors[] = 'Tên đăng nhập là bắt buộc.';
        }

        if (!$isEdit && $input['password'] === '') {
            $errors[] = 'Mật khẩu là bắt buộc khi tạo tài khoản.';
        }

        if ($input['password'] !== '' && strlen($input['password']) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        }

        if ($input['ma_nhan_vien'] === '') {
            $errors[] = 'Hãy chọn nhân viên gắn với tài khoản.';
        }

        if ($input['ma_chuc_vu'] === '') {
            $errors[] = 'Hãy chọn vai trò phân quyền.';
        }

        if (!in_array($input['trang_thai'], ['HOAT_DONG', 'VO_HIEU_HOA'], true)) {
            $errors[] = 'Trạng thái tài khoản không hợp lệ.';
        }

        return $errors;
    }

    private function renderForm(array|null $account, array $errors, bool $isEdit): void
    {
        $currentEmployeeId = is_array($account) ? (string) ($account['ma_nhan_vien'] ?? '') : null;

        render('tai_khoan/form', [
            'pageTitle' => $isEdit ? 'Cập nhật tài khoản' : 'Thêm tài khoản',
            'account' => $account,
            'errors' => $errors,
            'roles' => $this->chucVuModel->all(),
            'employees' => $this->taiKhoanModel->availableEmployees($currentEmployeeId),
            'isEdit' => $isEdit,
        ]);
    }
}
