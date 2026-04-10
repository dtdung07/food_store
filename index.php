<?php
// Bật hiển thị lỗi để debug (tắt khi deploy)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';
require_once 'config/database.php';

// Định nghĩa BASE_URL nếu chưa có
if (!defined('BASE_URL')) {
    define('BASE_URL', '/food_store/');
}

// Xác định controller và action
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'auth';
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Chuyển đổi tên controller từ dạng cách_ nhau thành dạng viết hoa chữ cái đầu
$controllerParts = explode('_', $controller);
$controllerClassName = '';
foreach ($controllerParts as $part) {
    $controllerClassName .= ucfirst($part);
}
$controllerClass = $controllerClassName . 'Controller';
$controllerFile = 'controllers/' . $controllerClass . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerObj = new $controllerClass();
    
    if (method_exists($controllerObj, $action)) {
        $controllerObj->$action();
    } else {
        die("Không tìm thấy action: $action trong controller $controllerClass");
    }
} else {
    die("Không tìm thấy controller file: $controllerFile");
}
?>