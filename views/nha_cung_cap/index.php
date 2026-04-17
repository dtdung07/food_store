<?php
declare(strict_types=1);
$suppliers = $suppliers ?? [];
$totalCount = $totalCount ?? 0;
$totalActive = $totalActive ?? 0;
$filters = $filters ?? ['q' => '', 'trang_thai' => ''];
?>
<section class="page-hero">
    <div>
        <h1>Quản lý nhà cung cấp</h1>
        <p>Danh sách các đối tác, nhà cung cấp sản phẩm và quản lý thông tin liên hệ.</p>
    </div>
    <?php if (can_access('nha-cung-cap')): ?>
        <div class="page-actions">
            <a class="button" href="<?= e(url_for('nha-cung-cap', 'form')) ?>">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Thêm nhà cung cấp
            </a>
        </div>
    <?php endif; ?>
</section>

<section class="stat-overview stat-overview--compact" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
    <article class="stat-card tone-blue">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <div class="stat-card__content">
            <span class="stat-card__label">Tổng số nhà cung cấp</span>
            <strong class="stat-card__value"><?= e((string)$totalCount) ?> đối tác</strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-green">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 12l2 2 4-4M3 12a9 9 0 1118 0 9 9 0 01-18 0z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <div class="stat-card__content">
            <span class="stat-card__label">Đang hoạt động</span>
            <strong class="stat-card__value"><?= e((string)$totalActive) ?> nhà cung cấp</strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
</section>

<section class="search-toolbar">
    <form class="search-toolbar__main" method="GET" action="index.php">
        <input type="hidden" name="c" value="nha-cung-cap">
        <input type="hidden" name="a" value="index">
        <div class="search-box">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="text" name="q" id="search-q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Tìm tên nhà cung cấp, mã đối tác, số điện thoại..."
                   hx-get="<?= e(url_for('nha-cung-cap', 'index')) ?>"
                   hx-trigger="keyup changed delay:400ms"
                   hx-target="#table-container"
                   hx-select="#table-container"
                   hx-include="#filter-status"
                   hx-push-url="true">
        </div>
    </form>
    <form class="search-toolbar__filters">
        <select name="trang_thai" id="filter-status"
                hx-get="<?= e(url_for('nha-cung-cap', 'index')) ?>"
                hx-trigger="change"
                hx-target="#table-container"
                hx-select="#table-container"
                hx-include="#search-q"
                hx-push-url="true">
            <option value="">Tất cả trạng thái</option>
            <option value="HOAT_DONG" <?= ($filters['trang_thai'] ?? '') === 'HOAT_DONG' ? 'selected' : '' ?>>Hoạt động</option>
            <option value="VO_HIEU_HOA" <?= ($filters['trang_thai'] ?? '') === 'VO_HIEU_HOA' ? 'selected' : '' ?>>Vô hiệu hóa</option>
        </select>
    </form>
</section>

<section class="table-card table-card--flush" id="table-container">
    <div class="table-card__header">
        <div>
            <h3>Danh sách nhà cung cấp</h3>
        </div>
    </div>
    <?php if ($suppliers === []): ?>
        <div class="empty-state" style="margin: 24px;">Không tìm thấy nhà cung cấp nào phù hợp.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nhà cung cấp</th>
                        <th>Mã đối tác</th>
                        <th>Số điện thoại</th>
                        <th>Email / Địa chỉ</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppliers as $ncc): ?>
                        <tr id="row-<?= e($ncc['ma_nha_cung_cap']) ?>">
                            <td>
                                <strong><?= e($ncc['ten_nha_cung_cap']) ?></strong>
                                <div class="meta-text">Người liên hệ: <?= e($ncc['ten_nguoi_lien_he'] ?: '-') ?></div>
                            </td>
                            <td><code><?= e($ncc['ma_nha_cung_cap']) ?></code></td>
                            <td><?= e($ncc['so_dien_thoai'] ?: '-') ?></td>
                            <td>
                                <div><?= e($ncc['email'] ?: '-') ?></div>
                                <div class="meta-text"><?= e($ncc['dia_chi'] ?: '-') ?></div>
                            </td>
                            <td>
                                <?php 
                                $statusTone = $ncc['trang_thai'] === 'HOAT_DONG' ? 'success' : 'neutral';
                                $statusLabel = $ncc['trang_thai'] === 'HOAT_DONG' ? 'Hoạt động' : 'Vô hiệu hóa';
                                ?>
                                <span class="badge badge--<?= $statusTone ?>"><?= e($statusLabel) ?></span>
                            </td>
                            <td>
                                <div class="inline-actions">
                                    <?php if (can_access('nha-cung-cap')): ?>
                                        <a class="text-link" href="<?= e(url_for('nha-cung-cap', 'form', ['id' => $ncc['ma_nha_cung_cap']])) ?>">Sửa</a>
                                    <?php endif; ?>
                                    <?php if (current_user()['ma_chuc_vu'] === 'ADMIN'): ?>
                                        <button type="button" class="button button--ghost" onclick="deleteSupplier('<?= e($ncc['ma_nha_cung_cap']) ?>', '<?= e(addslashes($ncc['ten_nha_cung_cap'])) ?>')">Xóa</button>
                                    <?php endif; ?>
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
function deleteSupplier(id, name) {
    Swal.fire({
        title: 'Bạn có chắc chắn?',
        html: `Hành động này sẽ xoá thông tin nhà cung cấp <strong>${name}</strong>.<br>Hành động này không thể hoàn tác!`,
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
            fetch(`<?= e(url_for('nha-cung-cap', 'delete')) ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ma_nha_cung_cap: id})
            })
            .then(response => {
                const contentType = response.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    throw new Error('SERVER_HTML');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    let row = document.querySelector(`#row-${id}`);
                    if (row) {
                        row.style.transition = "all 0.4s ease";
                        row.style.opacity = "0";
                        row.style.transform = "translateX(-20px)";
                        setTimeout(() => row.remove(), 400);
                    }
                    Swal.fire({
                        title: 'Đã xoá!',
                        text: 'Nhà cung cấp đã được xoá thành công.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Lỗi', data.message || 'Không thể xóa nhà cung cấp này!', 'error');
                }
            })
            .catch(err => {
                const msg = err.message === 'SERVER_HTML'
                    ? 'Bạn không có quyền thực hiện thao tác này.'
                    : 'Không thể kết nối máy chủ. Vui lòng thử lại.';
                Swal.fire('Lỗi', msg, 'error');
            });
        }
    })
}
</script>
