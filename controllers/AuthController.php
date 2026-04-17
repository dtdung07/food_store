<?php
declare(strict_types=1);

require_once APP_ROOT . '/models/TaiKhoanModel.php';

class AuthController
{
    private TaiKhoanModel $taiKhoanModel;

    public function __construct()
    {
        $this->taiKhoanModel = new TaiKhoanModel();
    }

    public function login(): void
    {
        if (is_logged_in()) {
            redirect_to('dashboard', 'index');
        }

        $errors = [];
        $input = [
            'ten_dang_nhap' => '',
        ];

        if (is_post()) {
            $input['ten_dang_nhap'] = trim((string) ($_POST['ten_dang_nhap'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            if ($input['ten_dang_nhap'] === '' || $password === '') {
                $errors[] = 'Nhập đầy đủ tên đăng nhập và mật khẩu.';
            } else {
                $account = $this->taiKhoanModel->findForLogin($input['ten_dang_nhap']);

                if ($account === null || !password_verify($password, (string) $account['password'])) {
                    $errors[] = 'Thông tin đăng nhập không đúng.';
                } elseif (($account['trang_thai'] ?? '') !== 'HOAT_DONG') {
                    $errors[] = 'Tài khoản đang bị vô hiệu hóa.';
                } else {
                    unset($account['password']);
                    session_regenerate_id(true);
                    login_user($account);
                    flash('success', 'Đăng nhập thành công.');
                    redirect_to('dashboard', 'index');
                }
            }
        }

        render('auth/login', [
            'pageTitle' => 'Đăng nhập',
            'hideNav' => true,
            'errors' => $errors,
            'input' => $input,
        ]);
    }

    public function logout(): void
    {
        logout_user();
        session_regenerate_id(true);
        flash('success', 'Đã đăng xuất.');
        redirect_to('auth', 'login');
    }
}
