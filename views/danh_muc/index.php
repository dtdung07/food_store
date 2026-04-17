<?php
declare(strict_types=1);
$categories = $categories ?? [];
$q = $q ?? '';
$totalCategories = count($categories);
$totalProducts = array_sum(array_column($categories, 'so_luong_sp'));
?>
<section class="page-hero">
    <div>
        <h1>Quản lý danh mục</h1>
        <p>Quản lý phân loại sản phẩm và xem số lượng mặt hàng thuộc từng danh mục.</p>
    </div>
    <?php if (can_access('danh-muc')): ?>
        <div class="page-actions">
            <a class="button" href="<?= e(url_for('danh-muc', 'form')) ?>">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Thêm danh mục
            </a>
        </div>
    <?php endif; ?>
</section>

<section class="stat-overview stat-overview--compact" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
    <article class="stat-card tone-blue">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Tổng số danh mục</span>
            <strong class="stat-card__value"><?= e((string)$totalCategories) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>

    <article class="stat-card tone-green">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7l-8-4-8 4 8 4 8-4zm0 6l-8 4-8-4m20 6l-8 4-8-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Tổng số sản phẩm</span>
            <strong class="stat-card__value"><?= e((string)$totalProducts) ?> mặt hàng</strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
</section>

<section class="search-toolbar">
    <form class="search-toolbar__main" method="GET" action="index.php">
        <input type="hidden" name="c" value="danh-muc">
        <input type="hidden" name="a" value="index">
        <div class="search-box">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="text" name="q" id="search-q" value="<?= e($q) ?>" placeholder="Tìm kiếm theo tên hoặc mã danh mục..."
                   hx-get="<?= e(url_for('danh-muc', 'index')) ?>"
                   hx-trigger="keyup changed delay:400ms"
                   hx-target="#table-container"
                   hx-select="#table-container"
                   hx-push-url="true">
        </div>
    </form>
</section>

<section class="table-card table-card--flush" id="table-container">
    <div class="table-card__header">
        <div>
            <h3>Danh sách danh mục</h3>
        </div>
    </div>
    <?php if ($categories === []): ?>
        <div class="empty-state" style="margin: 24px;">Không tìm thấy danh mục phù hợp.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mã danh mục</th>
                        <th>Tên danh mục</th>
                        <th>Mô tả</th>
                        <th>Số lượng mặt hàng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr id="cat-row-<?= e($cat['ma_danh_muc']) ?>">
                            <td><code><?= e($cat['ma_danh_muc']) ?></code></td>
                            <td><strong><?= e($cat['ten_danh_muc']) ?></strong></td>
                            <td class="meta-text"><?= e($cat['mo_ta'] ?: '-') ?></td>
                            <td>
                                <span class="badge badge--neutral"><?= e((string)($cat['so_luong_sp'] ?? 0)) ?> sản phẩm</span>
                            </td>
                            <td>
                                <div class="inline-actions">
                                    <?php if (can_access('danh-muc')): ?>
                                        <a class="text-link" href="<?= e(url_for('danh-muc', 'form', ['id' => $cat['ma_danh_muc']])) ?>">Sửa</a>
                                    <?php endif; ?>
                                    <?php if (current_user()['ma_chuc_vu'] === 'ADMIN'): ?>
                                        <button type="button" class="button button--ghost" onclick="deleteCategory('<?= e($cat['ma_danh_muc']) ?>', '<?= e(addslashes($cat['ten_danh_muc'])) ?>')">Xóa</button>
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
function deleteCategory(id, name) {
    Swal.fire({
        title: 'Bạn có chắc chắn?',
        html: `Hành động này sẽ xóa danh mục <strong>${name}</strong>.<br>Những mặt hàng thuộc danh mục này sẽ chuyển về chưa phân loại.`,
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
            fetch(`<?= e(url_for('danh-muc', 'delete')) ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ma_danh_muc: id})
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
                    let row = document.querySelector(`#cat-row-${id}`);
                    if (row) {
                        row.style.transition = "all 0.4s ease";
                        row.style.opacity = "0";
                        row.style.transform = "translateX(-20px)";
                        setTimeout(() => row.remove(), 400);
                    }
                    Swal.fire({
                        title: 'Đã xoá!',
                        text: 'Danh mục đã được xoá thành công.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Lỗi', data.message || 'Không thể xóa danh mục này!', 'error');
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
