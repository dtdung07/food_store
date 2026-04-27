<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

if (!headers_sent()) {
    $sessionPath = APP_ROOT . '/storage/sessions';

    if (!is_dir($sessionPath)) {
        @mkdir($sessionPath, 0777, true);
    }

    if (is_dir($sessionPath) && is_writable($sessionPath)) {
        session_save_path($sessionPath);
    }
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

foreach (array_keys($_GET) as $key) {
    if (!str_starts_with((string) $key, 'amp;')) {
        continue;
    }

    $normalizedKey = substr((string) $key, 4);
    if ($normalizedKey === '') {
        continue;
    }

    if (!array_key_exists($normalizedKey, $_GET)) {
        $_GET[$normalizedKey] = $_GET[$key];
    }

    unset($_GET[$key]);
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

const MODULE_ACCESS = [
    'dashboard' => ['ADMIN', 'QUAN_LY', 'THU_KHO', 'THU_NGAN', 'NV_QUAY_CAN'],
    'bao-cao' => ['ADMIN', 'QUAN_LY', 'THU_KHO', 'THU_NGAN', 'NV_QUAY_CAN'],
    'nhan-vien' => ['ADMIN', 'QUAN_LY'],
    'tai-khoan' => ['ADMIN'],
    'danh-muc' => ['ADMIN', 'QUAN_LY'],
    'hang-hoa' => ['ADMIN', 'QUAN_LY', 'THU_KHO'],
    'nha-cung-cap' => ['ADMIN', 'QUAN_LY', 'THU_KHO'],
    'kho' => ['ADMIN', 'QUAN_LY', 'THU_KHO'],
    'phieu-huy' => ['ADMIN', 'QUAN_LY', 'THU_KHO', 'NV_QUAY_CAN']
];

function current_route(): array
{
    static $route = null;

    if ($route !== null) {
        return $route;
    }

    if (!empty($_GET['url'])) {
        $segments = array_values(array_filter(explode('/', trim((string) $_GET['url'], '/'))));
        $controller = sanitize_route_segment($segments[0] ?? '');
        $action = sanitize_route_segment($segments[1] ?? '');
        $route = [
            'controller' => $controller !== '' ? $controller : (is_logged_in() ? 'dashboard' : 'auth'),
            'action' => $action !== '' ? $action : 'index',
        ];

        return $route;
    }

    $route = [
        'controller' => sanitize_route_segment((string) ($_GET['c'] ?? (is_logged_in() ? 'dashboard' : 'auth'))) ?: 'auth',
        'action' => sanitize_route_segment((string) ($_GET['a'] ?? 'index')) ?: 'index',
    ];

    if ($route['controller'] === 'auth' && !isset($_GET['a']) && !isset($_GET['url'])) {
        $route['action'] = 'login';
    }

    return $route;
}

function sanitize_route_segment(string $value): string
{
    return strtolower((string) preg_replace('/[^a-zA-Z0-9_-]/', '', $value));
}

function url_for(string $controller, string $action = 'index', array $params = []): string
{
    $query = http_build_query(array_merge([
        'c' => $controller,
        'a' => $action,
    ], $params));

    return BASE_URL . '/index.php?' . $query;
}

function asset_url(string $path): string
{
    return ASSET_URL . '/' . ltrim($path, '/');
}

function redirect_to(string $controller, string $action = 'index', array $params = []): void
{
    header('Location: ' . url_for($controller, $action, $params));
    exit;
}

function render(string $view, array $data = []): void
{
    $viewFile = APP_ROOT . '/views/' . $view . '.php';

    if (!file_exists($viewFile)) {
        http_response_code(404);
        echo 'View not found.';
        exit;
    }

    $pageTitle = $data['pageTitle'] ?? APP_NAME;
    $hideNav = (bool) ($data['hideNav'] ?? false);
    $flashes = pull_flashes();
    $currentUser = current_user();
    $route = current_route();

    //Truyền các biến cho views, nếu trong views có tồn tại biến đó rồi thì bỏ qua, không ghi đè giá trị (EXTR_SKIP)
    extract($data, EXTR_SKIP);

    $isHtmx = isset($_SERVER['HTTP_HX_REQUEST']) && !isset($_SERVER['HTTP_HX_BOOSTED']);
    
    if (!$isHtmx) {
        require APP_ROOT . '/views/layout/header.php';
    }
    
    require $viewFile;
    
    if (!$isHtmx) {
        require APP_ROOT . '/views/layout/footer.php';
    }
}

function render_partial(string $view, array $data = []): void
{
    $viewFile = APP_ROOT . '/views/' . $view . '.php';
    if (!file_exists($viewFile)) {
        return;
    }
    extract($data, EXTR_SKIP);
    require $viewFile;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message,
    ];
}

function pull_flashes(): array
{
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);

    return $messages;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function is_post(): bool
{
    return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST';
}

function current_user(): ?array
{
    return $_SESSION['auth_user'] ?? null;
}

function login_user(array $user): void
{
    $_SESSION['auth_user'] = $user;
}

function logout_user(): void
{
    unset($_SESSION['auth_user']);
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function can_access(string $module): bool
{
    $user = current_user();

    if ($user === null) {
        return false;
    }

    $role = (string) ($user['ma_chuc_vu'] ?? '');
    $allowedRoles = MODULE_ACCESS[$module] ?? [];

    return in_array($role, $allowedRoles, true);
}



function is_ajax_request(): bool
{
    // Detect fetch/XMLHttpRequest hoặc HTMX request
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || isset($_SERVER['HTTP_HX_REQUEST']);
}

function expects_json_response(): bool
{
    $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));

    return is_ajax_request() || str_contains($accept, 'application/json');
}

function require_login(): void
{
    if (is_logged_in()) {
        return;
    }

    if (expects_json_response()) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.']);
        exit;
    }

    flash('error', 'Vui lòng đăng nhập để tiếp tục.');
    redirect_to('auth', 'login');
}

function require_permission(string $module): void
{
    require_login();

    if (can_access($module)) {
        return;
    }

    if (expects_json_response()) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Tài khoản không có quyền thực hiện thao tác này.']);
        exit;
    }

    flash('error', 'Tài khoản không có quyền truy cập chức năng này.');
    redirect_to('bao-cao', 'index');
}

function currency(float|int|string|null $amount): string
{
    return number_format((float) $amount, 0, ',', '.') . ' VND';
}

function db_error_message(Throwable $exception): string
{
    $message = $exception->getMessage();

    if (stripos($message, 'Duplicate entry') !== false) {
        return 'Dữ liệu bị trùng. Hãy kiểm tra mã, tên đăng nhập hoặc liên kết nhân viên.';
    }

    if (stripos($message, 'foreign key constraint') !== false) {
        return 'Bản ghi đang được sử dụng trong dữ liệu khác, không thể xóa hoặc cập nhật theo cách này.';
    }

    return 'Đã xảy ra lỗi khi thao tác với cơ sở dữ liệu.';
}

function role_access_matrix(): array
{
    return [
        'ADMIN' => ['Báo cáo', 'Nhân viên', 'Tài khoản'],
        'QUAN_LY' => ['Báo cáo', 'Nhân viên'],
        'THU_KHO' => ['Báo cáo'],
        'THU_NGAN' => ['Báo cáo'],
        'NV_QUAY_CAN' => ['Báo cáo'],
    ];
}

function badge_tone(string $status): string
{
    return match ($status) {
        'HOAT_DONG', 'HOAN_TAT', 'DA_DUYET', 'CON_HAN' => 'success',
        'CHO_DUYET', 'DANG_XU_LY', 'SAP_HET_HAN' => 'warning',
        'VO_HIEU_HOA', 'HUY', 'TU_CHOI', 'HET_HAN' => 'danger',
        default => 'neutral',
    };
}

function status_label(string $status): string
{
    return match ($status) {
        'HOAT_DONG' => 'Hoạt động',
        'HOAN_TAT' => 'Hoàn tất',
        'DA_DUYET' => 'Đã duyệt',
        'CON_HAN' => 'Còn hạn',
        'CHO_DUYET' => 'Chờ duyệt',
        'DANG_XU_LY' => 'Đang xử lý',
        'SAP_HET_HAN' => 'Sắp hết hạn',
        'VO_HIEU_HOA' => 'Vô hiệu hóa',
        'HUY' => 'Hủy',
        'TU_CHOI' => 'Từ chối',
        'HET_HAN' => 'Hết hạn',
        default => $status,
    };
}

function app_date_label(): string
{
    $days = [
        1 => 'Thứ Hai',
        2 => 'Thứ Ba',
        3 => 'Thứ Tư',
        4 => 'Thứ Năm',
        5 => 'Thứ Sáu',
        6 => 'Thứ Bảy',
        7 => 'Chủ Nhật',
    ];

    return $days[(int) date('N')] . ', ' . date('d/m/Y');
}

function current_shift_label(): string
{
    $hour = (int) date('G');

    return match (true) {
        $hour < 12 => 'Ca sáng',
        $hour < 18 => 'Ca chiều',
        default => 'Ca tối',
    };
}

function expiring_product_count(int $days = 30): int
{
    static $cache = [];

    if (isset($cache[$days])) {
        return $cache[$days];
    }

    try {
        $statement = db()->prepare(
            "SELECT COUNT(DISTINCT lh.ma_hang_hoa)
             FROM lo_hang lh
             WHERE (lh.so_luong_trong_kho + lh.so_luong_tren_ke) > 0
               AND DATEDIFF(lh.han_su_dung, CURDATE()) BETWEEN 0 AND :days"
        );
        $statement->bindValue(':days', $days, PDO::PARAM_INT);
        $statement->execute();
        $cache[$days] = (int) $statement->fetchColumn();
    } catch (Throwable) {
        $cache[$days] = 0;
    }

    return $cache[$days];
}

function pending_disposal_count(): int
{
    try {
        $stmt = db()->query("SELECT COUNT(*) FROM phieu_huy_hang WHERE trang_thai = 'CHO_DUYET'");
        return (int) $stmt->fetchColumn();
    } catch (Throwable) {
        return 0;
    }
}

function user_initials(?string $name): string
{
    $name = trim((string) $name);

    if ($name === '') {
        return 'ND';
    }

    $parts = preg_split('/\s+/', $name) ?: [];
    $initials = '';

    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
    }

    return $initials !== '' ? $initials : 'ND';
}

function compact_number(float|int|string|null $value): string
{
    $number = (float) $value;
    $absolute = abs($number);

    if ($absolute >= 1000000000) {
        return number_format($number / 1000000000, 1, ',', '.') . 'B';
    }

    if ($absolute >= 1000000) {
        return number_format($number / 1000000, 1, ',', '.') . 'M';
    }

    if ($absolute >= 1000) {
        return number_format($number / 1000, 1, ',', '.') . 'K';
    }

    return number_format($number, 0, ',', '.');
}
