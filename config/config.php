<?php
session_start();

define('BASE_URL', 'http://localhost/food_store/');
define('BASE_PATH', dirname(__DIR__) . '/');

// Cấu hình thời gian
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Kiểm tra đăng nhập
function checkLogin() {
    if (!isset($_SESSION['user'])) {
        header('Location: ' . BASE_URL . 'index.php?controller=auth&action=login');
        exit();
    }
}

// Kiểm tra quyền
function checkRole($allowedRoles = []) {
    if (empty($allowedRoles)) return true;
    
    $userRole = $_SESSION['user']['ma_chuc_vu'] ?? '';
    if (!in_array($userRole, $allowedRoles)) {
        header('Location: ' . BASE_URL . 'index.php?controller=dashboard&action=index');
        exit();
    }
}

// Lấy thông tin người dùng hiện tại
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}