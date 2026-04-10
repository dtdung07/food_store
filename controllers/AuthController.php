<?php
require_once BASE_PATH . 'models/NhanVienModel.php';
require_once BASE_PATH . 'models/TaiKhoanModel.php';

class AuthController {
    private $nhanVienModel;
    private $taiKhoanModel;
    
    public function __construct() {
        $this->nhanVienModel = new NhanVienModel();
        $this->taiKhoanModel = new TaiKhoanModel();
    }
    
    public function login() {
        // Nếu đã đăng nhập thì chuyển đến dashboard
        if (isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=dashboard&action=index');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $error = $this->validateLogin($username, $password);
            
            if (empty($error)) {
                $user = $this->taiKhoanModel->findByUsername($username);
                
                // Mật khẩu mặc định là 123456
                if ($user && ($password === '123456' || password_verify($password, $user['password']))) {
                    // Cập nhật mật khẩu thành hash nếu đang dùng plain text
                    if ($password === '123456' && $user['password'] !== password_hash('123456', PASSWORD_DEFAULT)) {
                        $this->taiKhoanModel->updatePassword($user['ma_tai_khoan'], password_hash('123456', PASSWORD_DEFAULT));
                    }
                    
                    $_SESSION['user'] = [
                        'ma_nhan_vien' => $user['ma_nhan_vien'],
                        'ten_nhan_vien' => $user['ten_nhan_vien'],
                        'ma_chuc_vu' => $user['ma_chuc_vu'],
                        'ten_chuc_vu' => $user['ten_chuc_vu'],
                        'username' => $user['ten_dang_nhap']
                    ];
                    
                    header('Location: ' . BASE_URL . 'index.php?controller=dashboard&action=index');
                    exit();
                } else {
                    $error = 'Sai tên đăng nhập hoặc mật khẩu!';
                }
            }
            
            include BASE_PATH . 'views/auth/login.php';
        } else {
            include BASE_PATH . 'views/auth/login.php';
        }
    }
    
    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . 'index.php?controller=auth&action=login');
        exit();
    }
    
    private function validateLogin($username, $password) {
        if (empty($username)) return 'Vui lòng nhập tên đăng nhập!';
        if (empty($password)) return 'Vui lòng nhập mật khẩu!';
        return '';
    }
}