<?php
$accounts = $accounts ?? [];
$filters = $filters ?? ['q' => '', 'status' => 'all'];
$totalAccounts = count($accounts);
$activeAccounts = count(array_filter($accounts, static fn(array $account): bool => ($account['trang_thai'] ?? '') === 'HOAT_DONG'));
$disabledAccounts = max(0, $totalAccounts - $activeAccounts);
$adminAccounts = count(array_filter($accounts, static fn(array $account): bool => ($account['ma_chuc_vu'] ?? '') === 'ADMIN'));
?>
<section class="page-hero">
    <div>
        <h1>Tài khoản</h1>
        <p>Quản lý tài khoản, vai trò và trạng thái hoạt động.</p>
    </div>
    <div class="page-actions">
        <a class="button" href="<?= e(url_for('tai-khoan', 'form')) ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Thêm tài khoản
        </a>
    </div>
</section>

<section class="stat-overview stat-overview--compact">
    <article class="stat-card tone-blue">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm0 2c-4.42 0-8 1.79-8 4v2h16v-2c0-2.21-3.58-4-8-4z"/></svg></span>
        <div class="stat-card__content"><span class="stat-card__label">Tổng tài khoản</span><strong class="stat-card__value"><?= e((string) $totalAccounts) ?></strong></div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-green">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="6"/></svg></span>
        <div class="stat-card__content"><span class="stat-card__label">Hoạt động</span><strong class="stat-card__value"><?= e((string) $activeAccounts) ?></strong></div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-amber">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8v5m0 4h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg></span>
        <div class="stat-card__content"><span class="stat-card__label">Tạm khóa</span><strong class="stat-card__value"><?= e((string) $disabledAccounts) ?></strong></div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-violet">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2 4 7v5c0 5 3.4 9.74 8 11 4.6-1.26 8-6 8-11V7l-8-5z"/></svg></span>
        <div class="stat-card__content"><span class="stat-card__label">Tài khoản Admin</span><strong class="stat-card__value"><?= e((string) $adminAccounts) ?></strong></div>
        <span class="stat-card__accent"></span>
    </article>
</section>

<section class="search-toolbar">
    <form class="search-toolbar__main">
        <div class="search-box">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="text" name="q" id="search-q-account" value="<?= e($filters['q'] ?? '') ?>" placeholder="Tìm kiếm theo tên đăng nhập, nhân viên, vai trò..."
                   hx-get="<?= e(url_for('tai-khoan', 'index')) ?>"
                   hx-trigger="keyup changed delay:400ms"
                   hx-target="#table-container"
                   hx-select="#table-container"
                   hx-include="#filter-status-account"
                   hx-push-url="true">
        </div>
    </form>
    <form class="search-toolbar__filters">
        <select name="status" id="filter-status-account"
            hx-get="<?= e(url_for('tai-khoan', 'index')) ?>"
            hx-trigger="change"
            hx-target="#table-container"
            hx-select="#table-container"
            hx-include="#search-q-account"
            hx-push-url="true">
            <option value="all" <?= ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>Tất cả trạng thái</option>
            <option value="HOAT_DONG" <?= ($filters['status'] ?? '') === 'HOAT_DONG' ? 'selected' : '' ?>>Hoạt động</option>
            <option value="VO_HIEU_HOA" <?= ($filters['status'] ?? '') === 'VO_HIEU_HOA' ? 'selected' : '' ?>>Vô hiệu hóa</option>
        </select>
    </form>
</section>

<div id="table-container">

<section class="table-card table-card--flush">
    <div class="table-card__header">
        <div>
            <h3>Danh sách tài khoản</h3>
        </div>
    </div>
    <?php if ($accounts === []): ?>
        <div class="empty-state" style="margin: 24px;">Không tìm thấy tài khoản phù hợp với bộ lọc hiện tại.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Người dùng</th>
                        <th>Nhân viên</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $account): ?>
                        <?php $statusCode = (string) ($account['trang_thai'] ?? ''); ?>
                        <tr>
                            <td>
                                <div class="table-person">
                                    <span class="avatar-circle"><?= e(user_initials($account['ten_nhan_vien'] ?? $account['ten_dang_nhap'] ?? '')) ?></span>
                                    <div>
                                        <div class="table-person__name"><?= e($account['ten_dang_nhap']) ?></div>
                                        <div class="table-person__meta">ID #<?= e((string) $account['ma_tai_khoan']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong><?= e($account['ten_nhan_vien'] ?? 'Chưa gán') ?></strong><br>
                                <span class="meta-text"><?= e($account['ma_nhan_vien'] ?? '') ?></span>
                            </td>
                            <td><?= e($account['ten_chuc_vu'] ?? $account['ma_chuc_vu'] ?? '') ?></td>
                            <td><span class="badge badge--<?= e(badge_tone($statusCode)) ?>"><?= e(status_label($statusCode)) ?></span></td>
                            <td>
                                <div class="inline-actions">
                                    <a class="text-link" href="<?= e(url_for('tai-khoan', 'form', ['id' => $account['ma_tai_khoan']])) ?>">Sửa</a>
                                    <button type="button" class="button button--ghost" onclick="deleteAccount(this, <?= e((string) $account['ma_tai_khoan']) ?>)">Xóa</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
</div>

<script>
function deleteAccount(btn, id) {
    Swal.fire({
        title: 'Xóa tài khoản này?',
        text: "Hành động này không thể hoàn tác!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f44336',
        cancelButtonColor: '#7f8797',
        confirmButtonText: 'Đúng, xóa ngay!',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            let row = btn.closest('tr');
            row.classList.add('htmx-settling'); // CSS animation class
            
            fetch("<?= e(url_for('tai-khoan', 'delete')) ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ ma_tai_khoan: id })
            })
            .then(res => {
                const contentType = res.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    throw new Error('SERVER_HTML'); // Server trả về HTML (redirect/error page)
                }
                return res.json();
            })
            .then(data => {
                if(data.success) {
                    Swal.fire('Đã xóa!', data.message, 'success');
                    setTimeout(() => row.remove(), 400);
                } else {
                    row.classList.remove('htmx-settling');
                    Swal.fire('Lỗi!', data.message || 'Không thể xóa tài khoản này.', 'error');
                }
            })
            .catch(err => {
                row.classList.remove('htmx-settling');
                const msg = err.message === 'SERVER_HTML'
                    ? 'Phiên đăng nhập đã hết hoặc bạn không có quyền thực hiện thao tác này.'
                    : 'Không thể kết nối máy chủ. Vui lòng thử lại.';
                Swal.fire('Lỗi!', msg, 'error');
            });
        }
    });
}
</script>
