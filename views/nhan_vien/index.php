<?php
$employees = $employees ?? [];
$filters = $filters ?? ['q' => '', 'status' => 'all'];
$totalEmployees = count($employees);
$activeEmployees = 0;
$disabledEmployees = 0;
$employeesWithAccount = 0;

foreach ($employees as $employee) {
    $hasAccount = !empty($employee['ma_tai_khoan']);
    $isActive = ($employee['trang_thai_tai_khoan'] ?? '') === 'HOAT_DONG';

    if ($hasAccount) {
        $employeesWithAccount++;
    }

    if ($isActive) {
        $activeEmployees++;
    } elseif ($hasAccount) {
        $disabledEmployees++;
    }
}

$withoutAccount = max(0, $totalEmployees - $employeesWithAccount);
?>
<section class="page-hero">
    <div>
        <h1>Quản lý nhân viên</h1>
        <p>Quản lý thông tin cơ bản và trạng thái vận hành của nhân viên theo bố cục dashboard.</p>
    </div>
    <div class="page-actions">
        <a class="button" href="<?= e(url_for('nhan-vien', 'form')) ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Thêm nhân viên
        </a>
    </div>
</section>

<section class="stat-overview stat-overview--compact">
    <article class="stat-card tone-blue">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 11a4 4 0 1 0-3.999-4A4 4 0 0 0 16 11zm-8 1a4 4 0 1 0-4-4 4 4 0 0 0 4 4zm8 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zM8 14c-.29 0-.62.02-.97.05C4.56 14.29 0 15.39 0 18v2h6v-2c0-1.45.75-2.68 2.03-3.59A8.7 8.7 0 0 0 8 14z"/></svg></span>
        <div class="stat-card__content">
            <span class="stat-card__label">Tổng nhân viên</span>
            <strong class="stat-card__value"><?= e((string) $totalEmployees) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-green">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="6"/></svg></span>
        <div class="stat-card__content">
            <span class="stat-card__label">Đang hoạt động</span>
            <strong class="stat-card__value"><?= e((string) $activeEmployees) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-amber">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8v5m0 4h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg></span>
        <div class="stat-card__content">
            <span class="stat-card__label">Tài khoản tạm khóa</span>
            <strong class="stat-card__value"><?= e((string) $disabledEmployees) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-violet">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 15.5a1.5 1.5 0 1 1 1.5-1.5 1.5 1.5 0 0 1-1.5 1.5zm1.2-5.5h-2.4V7h2.4z"/></svg></span>
        <div class="stat-card__content">
            <span class="stat-card__label">Chưa có tài khoản</span>
            <strong class="stat-card__value"><?= e((string) $withoutAccount) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
</section>

<section class="search-toolbar">
    <form class="search-toolbar__main">
        <div class="search-box">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="text" name="q" id="search-q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Tìm kiếm theo tên, mã nhân viên, email..."
                   hx-get="<?= e(url_for('nhan-vien', 'index')) ?>"
                   hx-trigger="keyup changed delay:400ms"
                   hx-target="#table-container"
                   hx-select="#table-container"
                   hx-include="#filter-status"
                   hx-push-url="true">
        </div>
    </form>
    <form class="search-toolbar__filters">
        <select name="status" id="filter-status"
          hx-get="<?= e(url_for('nhan-vien', 'index')) ?>"
          hx-trigger="change"
          hx-target="#table-container"
          hx-select="#table-container"
          hx-include="#search-q"
          hx-push-url="true">
            <option value="all" <?= ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>Tất cả trạng thái</option>
            <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
            <option value="disabled" <?= ($filters['status'] ?? '') === 'disabled' ? 'selected' : '' ?>>Tạm khóa</option>
            <option value="no-account" <?= ($filters['status'] ?? '') === 'no-account' ? 'selected' : '' ?>>Chưa có tài khoản</option>
        </select>
    </form>
</section>

<section class="table-card table-card--flush" id="table-container">
    <div class="table-card__header">
        <div>
            <h3>Danh sách nhân viên</h3>
            <p class="section-subtitle">Bảng dữ liệu được hiển thị siêu mượt bằng HTMX Live-Search.</p>
        </div>
    </div>
    <?php if ($employees === []): ?>
        <div class="empty-state" style="margin: 24px;">Không tìm thấy nhân viên phù hợp với bộ lọc hiện tại.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Mã NV</th>
                        <th>Chức vụ</th>
                        <th>Thông tin</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <?php
                        $statusLabel = 'Chưa có tài khoản';
                        $statusTone = 'neutral';
                        if (!empty($employee['ma_tai_khoan'])) {
                            if (($employee['trang_thai_tai_khoan'] ?? '') === 'HOAT_DONG') {
                                $statusLabel = 'Đang hoạt động';
                                $statusTone = 'success';
                            } else {
                                $statusLabel = 'Tạm khóa';
                                $statusTone = 'warning';
                            }
                        }
                        ?>
                        <tr id="emp-row-<?= e($employee['ma_nhan_vien']) ?>">
                            <td>
                                <div class="table-person">
                                    <span class="avatar-circle"><?= e(user_initials($employee['ten_nhan_vien'] ?? '')) ?></span>
                                    <div>
                                        <div class="table-person__name"><?= e($employee['ten_nhan_vien']) ?></div>
                                        <div class="table-person__meta"><?= e($employee['ten_dang_nhap'] ?? 'Chưa cấp tài khoản') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= e($employee['ma_nhan_vien']) ?></td>
                            <td><?= e($employee['ten_chuc_vu'] ?? $employee['ma_chuc_vu'] ?? 'Chưa gán') ?></td>
                            <td>
                                <div><?= e($employee['so_dien_thoai'] ?: '-') ?></div>
                                <div class="meta-text"><?= e($employee['email'] ?: '-') ?></div>
                            </td>
                            <td><span class="badge badge--<?= e($statusTone) ?>"><?= e($statusLabel) ?></span></td>
                            <td>
                                <div class="inline-actions">
                                    <a class="text-link" href="<?= e(url_for('nhan-vien', 'form', ['id' => $employee['ma_nhan_vien']])) ?>">Sửa</a>
                                    <button type="button" class="button button--ghost" onclick="deleteEmployee('<?= e($employee['ma_nhan_vien']) ?>')">Xóa</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<script>
function deleteEmployee(id) {
    Swal.fire({
        title: 'Bạn có chắc chắn?',
        text: "Hành động này không thể hoàn tác",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'var(--red)',
        cancelButtonColor: 'var(--muted)',
        confirmButtonText: 'Đồng ý xóa',
        cancelButtonText: 'Thoát',
        customClass: {
            confirmButton: 'button button--danger',
            cancelButton: 'button button--ghost'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?= e(url_for('nhan-vien', 'delete')) ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ma_nhan_vien: id})
            })
            .then(response => {
                const contentType = response.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    throw new Error('SERVER_HTML'); // Server trả về HTML thay vì JSON
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    let row = document.querySelector(`#emp-row-${id}`);
                    if (row) {
                        row.style.transition = "all 0.4s ease";
                        row.style.opacity = "0";
                        row.style.transform = "translateX(-20px)";
                        setTimeout(() => row.remove(), 400);
                    }
                    Swal.fire({
                        title: 'Đã xoá!',
                        text: 'Dữ liệu nhân viên đã được xoá thành công.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Lỗi', data.message || 'Không thể xóa nhân viên này!', 'error');
                }
            })
            .catch(err => {
                const msg = err.message === 'SERVER_HTML'
                    ? 'Phiên đăng nhập đã hết hoặc bạn không có quyền thực hiện thao tác này.'
                    : 'Không thể kết nối máy chủ. Vui lòng thử lại.';
                Swal.fire('Lỗi', msg, 'error');
            });
        }
    })
}
</script>
