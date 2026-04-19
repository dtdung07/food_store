<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= e(asset_url('css/style.css')) ?>?v=<?= e(APP_VERSION) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('css/smooth.css')) ?>?v=<?= e(APP_VERSION) ?>">
    <!-- Nâng cấp UX Mượt (Smoothness) bằng HTMX & SweetAlert2 -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body hx-boost="true">
    <div class="app-shell<?= $hideNav ? ' app-shell--auth' : '' ?>">
        <?php if (!$hideNav && $currentUser): ?>
            <aside class="sidebar">
                <div class="brand">
                    <h1>Food Store</h1>
                    <p>Quản lý siêu thị thực phẩm</p>
                </div>

                <nav class="sidebar__nav">
                    <div class="sidebar__section">
                        <span class="sidebar__section-label">Tổng quan</span>
                        <?php if (can_access('dashboard')): ?>
                            <a class="nav-link <?= $route['controller'] === 'dashboard' ? 'is-active' : '' ?>" href="<?= e(url_for('dashboard', 'index')) ?>">
                                <span class="nav-link__icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 13h1v7c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-7h1a1 1 0 0 0 .707-1.707l-9-9a.999.999 0 0 0-1.414 0l-9 9A1 1 0 0 0 3 13zm7 7v-5h4v5h-4z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <span>Dashboard</span>
                            </a>
                        <?php endif; ?>
                        <?php if (can_access('bao-cao')): ?>
                            <a class="nav-link <?= $route['controller'] === 'bao-cao' ? 'is-active' : '' ?>" href="<?= e(url_for('bao-cao', 'index')) ?>">
                                <span class="nav-link__icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h6v6H4zm10 0h6v10h-6zM4 14h6v6H4zm10 0h6v6h-6z"/></svg>
                                </span>
                                <span>Báo cáo</span>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="sidebar__section">
                        <span class="sidebar__section-label">Người dùng</span>
                        <?php if (can_access('nhan-vien')): ?>
                            <a class="nav-link <?= $route['controller'] === 'nhan-vien' ? 'is-active' : '' ?>" href="<?= e(url_for('nhan-vien', 'index')) ?>">
                                <span class="nav-link__icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3.5 19c0-2.8 2.4-5 5.5-5s5.5 2.2 5.5 5"/><circle cx="17.5" cy="9" r="2.5"/><path d="M15.5 19c.3-2 1.9-3.5 4-3.9"/></svg>
                                </span>
                                <span>Nhân viên</span>
                            </a>
                        <?php endif; ?>
                        <?php if (can_access('tai-khoan')): ?>
                            <a class="nav-link <?= $route['controller'] === 'tai-khoan' ? 'is-active' : '' ?>" href="<?= e(url_for('tai-khoan', 'index')) ?>">
                                <span class="nav-link__icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6"/></svg>
                                </span>
                                <span>Tài khoản</span>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="sidebar__section">
                        <span class="sidebar__section-label">Quản lý</span>
                        <?php if (can_access('danh-muc')): ?>
                            <a class="nav-link <?= $route['controller'] === 'danh-muc' ? 'is-active' : '' ?>" href="<?= e(url_for('danh-muc', 'index')) ?>">
                                <span class="nav-link__icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h18M3 12h18M3 17h18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                </span>
                                <span>Danh mục</span>
                            </a>
                        <?php endif; ?>
                        <?php if (can_access('hang-hoa')): ?>
                            <a class="nav-link <?= $route['controller'] === 'hang-hoa' ? 'is-active' : '' ?>" href="<?= e(url_for('hang-hoa', 'index')) ?>">
                                <span class="nav-link__icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7l-8-4-8 4 8 4 8-4zm0 6l-8 4-8-4m20 6l-8 4-8-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <span>Hàng hóa</span>
                            </a>
                        <?php endif; ?>
                        <?php if (can_access('nha-cung-cap')): ?>
                            <a class="nav-link <?= $route['controller'] === 'nha-cung-cap' ? 'is-active' : '' ?>" href="<?= e(url_for('nha-cung-cap', 'index')) ?>">
                                <span class="nav-link__icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 17h2a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h2m2 4h6M9 17v4m6-4v4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <span>Nhà cung cấp</span>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (can_access('kho')): ?>
                        <div class="sidebar__section">
                            <span class="sidebar__section-label">Kho hàng</span>
                            <a class="nav-link <?= $route['controller'] === 'kho' && str_contains($route['action'], 'nhap') ? 'is-active' : '' ?>" href="<?= e(url_for('kho', 'nhap_index')) ?>">
                                <span class="nav-link__icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z"/></svg>
                                </span>
                                <span>Nhập kho</span>
                            </a>
                            <a class="nav-link <?= $route['controller'] === 'kho' && str_contains($route['action'], 'xuat') ? 'is-active' : '' ?>" href="<?= e(url_for('kho', 'xuat_index')) ?>">
                                <span class="nav-link__icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2z"/></svg>
                                </span>
                                <span>Xuất kho</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </nav>

                <div class="sidebar__footer">
                    <div class="profile-card">
                        <div class="profile-card__avatar"><?= e(user_initials($currentUser['ten_nhan_vien'] ?? $currentUser['ten_dang_nhap'] ?? '')) ?></div>
                        <div>
                            <strong><?= e($currentUser['ten_nhan_vien'] ?? $currentUser['ten_dang_nhap'] ?? 'Người dùng') ?></strong>
                            <span><?= e($currentUser['ten_chuc_vu'] ?? $currentUser['ma_chuc_vu'] ?? '') ?></span>
                        </div>
                    </div>
                    <a class="button button--ghost button--full" href="<?= e(url_for('auth', 'logout')) ?>">Đăng xuất</a>
                </div>
            </aside>
        <?php endif; ?>

        <main class="content">
            <?php if (!$hideNav && $currentUser): ?>
                <header class="topbar">
                    <div class="topbar__title"><?= e($route['controller'] === 'bao-cao' ? 'Báo cáo tổng quan' : $pageTitle) ?></div>
                    <div class="topbar__meta">
                        <span><?= e(app_date_label()) ?></span>
                        <span class="topbar__divider"></span>
                        <span><?= e(current_shift_label()) ?></span>
                        <span class="topbar__alert">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2 1 21h22L12 2zm0 5.75 5.74 10.25H6.26L12 7.75zM11 10h2v4h-2zm0 5h2v2h-2z"/></svg>
                            <?= e((string) expiring_product_count()) ?> sản phẩm sắp hết hạn
                        </span>
                    </div>
                </header>
            <?php endif; ?>

            <div class="content__inner">
            <?php foreach ($flashes as $flashMessage): ?>
                <div class="alert alert--<?= e($flashMessage['type']) ?>">
                    <?= e($flashMessage['message']) ?>
                </div>
            <?php endforeach; ?>
