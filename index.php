<?php
declare(strict_types=1);

require_once realpath(__DIR__ . '/config/app.php');

$controllerMap = [
    'auth' => 'AuthController',
    'bao-cao' => 'BaoCaoController',
    'nhan-vien' => 'NhanVienController',
    'tai-khoan' => 'TaiKhoanController',
    'dashboard' => 'DashboardController',
    'danh-muc' => 'DanhMucController',
    'hang-hoa' => 'HangHoaController',
    'nha-cung-cap' => 'NhaCungCapController',
    'kho' => 'KhoController',
    'phieu-huy' => 'PhieuHuyController',
    'ban-hang' => 'BanHangController'
];

$route = current_route();
$controllerKey = $route['controller'];
$action = $route['action'];

if (!isset($controllerMap[$controllerKey])) {
    http_response_code(404);
    echo '404 - Controller not found.';
    exit;
}

$controllerClass = $controllerMap[$controllerKey];
$controllerFile = APP_ROOT . '/controllers/' . $controllerClass . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(500);
    echo 'Controller file is missing.';
    exit;
}

require_once $controllerFile;

$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    echo '404 - Action not found.';
    exit;
}

$controller->{$action}();
