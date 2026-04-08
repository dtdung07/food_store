<?php
declare(strict_types=1);

require_once APP_ROOT . '/models/NhanVienModel.php';
require_once APP_ROOT . '/models/ChucVuModel.php';
require_once APP_ROOT . '/models/TaiKhoanModel.php';

class NhanVienController
{
    private NhanVienModel $nhanVienModel;
    private ChucVuModel $chucVuModel;
    private TaiKhoanModel $taiKhoanModel;

    public function __construct()
    {
        $this->nhanVienModel = new NhanVienModel();
        $this->chucVuModel = new ChucVuModel();
        $this->taiKhoanModel = new TaiKhoanModel();
    }

    public function index(): void
    {
        require_permission('nhan-vien');

        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? 'all')),
        ];

        // Fetch using SQL directly to save memory and increase speed significantly
        $employees = $this->nhanVienModel->search($filters['q'], $filters['status']);

        render('nhan_vien/index', [
            'pageTitle' => 'Quản lý nhân viên',
            'employees' => $employees,
            'filters' => $filters,
        ]);
    }

    public function form(): void
    {
        require_permission('nhan-vien');

        $id = trim((string) ($_GET['id'] ?? ''));
        $employee = $id !== '' ? $this->nhanVienModel->find($id) : null;

        if ($id !== '' && $employee === null) {
            flash('error', 'Không tìm thấy nhân viên cần sửa.');
            redirect_to('nhan-vien', 'index');
        }

        $this->renderForm($employee, [], $employee !== null);
    }

    public function save(): void
    {
        require_permission('nhan-vien');

        if (!is_post()) {
            redirect_to('nhan-vien', 'index');
        }

        $input = $this->collectInput();
        $isEdit = (string) ($_POST['is_edit'] ?? '0') === '1';
        $errors = $this->validate($input);

        if ($errors !== []) {
            $this->renderForm($input, $errors, $isEdit);
            return;
        }

        try {
            if ($isEdit) {
                $this->nhanVienModel->update($input['ma_nhan_vien'], $input);
                flash('success', 'Cập nhật nhân viên thành công.');
            } else {
                $this->nhanVienModel->create($input);
                flash('success', 'Thêm nhân viên thành công.');
            }

            $currentUser = current_user();
            if ($currentUser !== null && ($currentUser['ma_nhan_vien'] ?? '') === $input['ma_nhan_vien']) {
                $freshUser = $this->taiKhoanModel->sessionPayloadByEmployeeId($input['ma_nhan_vien']);
                if ($freshUser !== null) {
                    login_user($freshUser);
                }
            }

            redirect_to('nhan-vien', 'index');
        } catch (Throwable $exception) {
            $errors[] = db_error_message($exception);
            $this->renderForm($input, $errors, $isEdit);
        }
    }

    public function delete(): void
    {
        require_permission('nhan-vien');

        if (!is_post()) {
            redirect_to('nhan-vien', 'index');
        }

        $id = trim((string) ($_POST['ma_nhan_vien'] ?? ''));
        $currentUser = current_user();

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($id === '') {
            if ($isAjax) { echo json_encode(['success' => false, 'message' => 'Không có mã nhân viên để xóa.']); exit; }
            flash('error', 'Không có mã nhân viên để xóa.');
            redirect_to('nhan-vien', 'index');
        }

        if ($currentUser !== null && ($currentUser['ma_nhan_vien'] ?? '') === $id) {
            if ($isAjax) { echo json_encode(['success' => false, 'message' => 'Không thể xóa nhân viên đang đăng nhập.']); exit; }
            flash('error', 'Không thể xóa nhân viên đang đăng nhập.');
            redirect_to('nhan-vien', 'index');
        }

        try {
            $this->nhanVienModel->delete($id);
            if ($isAjax) { echo json_encode(['success' => true, 'message' => 'Đã xóa nhân viên.']); exit; }
            flash('success', 'Đã xóa nhân viên.');
        } catch (Throwable $exception) {
            if ($isAjax) { echo json_encode(['success' => false, 'message' => db_error_message($exception)]); exit; }
            flash('error', db_error_message($exception));
        }

        redirect_to('nhan-vien', 'index');
    }

    private function collectInput(): array
    {
        return [
            'ma_nhan_vien' => trim((string) ($_POST['ma_nhan_vien'] ?? '')),
            'ten_nhan_vien' => trim((string) ($_POST['ten_nhan_vien'] ?? '')),
            'gioi_tinh' => trim((string) ($_POST['gioi_tinh'] ?? '')),
            'so_dien_thoai' => trim((string) ($_POST['so_dien_thoai'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'dia_chi' => trim((string) ($_POST['dia_chi'] ?? '')),
            'ngay_sinh' => trim((string) ($_POST['ngay_sinh'] ?? '')),
            'ma_chuc_vu' => trim((string) ($_POST['ma_chuc_vu'] ?? '')),
        ];
    }

    private function validate(array $input): array
    {
        $errors = [];

        if ($input['ma_nhan_vien'] === '') {
            $errors[] = 'Mã nhân viên là bắt buộc.';
        }

        if ($input['ten_nhan_vien'] === '') {
            $errors[] = 'Tên nhân viên là bắt buộc.';
        }

        if ($input['email'] !== '' && filter_var($input['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Email không đúng định dạng.';
        }

        if ($input['ngay_sinh'] !== '' && DateTimeImmutable::createFromFormat('Y-m-d', $input['ngay_sinh']) === false) {
            $errors[] = 'Ngày sinh không đúng định dạng.';
        }

        if ($input['ma_chuc_vu'] === '') {
            $errors[] = 'Hãy chọn chức vụ.';
        }

        return $errors;
    }

    private function renderForm(array|null $employee, array $errors, bool $isEdit): void
    {
        render('nhan_vien/form', [
            'pageTitle' => $isEdit ? 'Cập nhật nhân viên' : 'Thêm nhân viên',
            'employee' => $employee,
            'errors' => $errors,
            'roles' => $this->chucVuModel->all(),
            'isEdit' => $isEdit,
        ]);
    }
}
