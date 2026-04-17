<?php
declare(strict_types=1);
$hangHoas = $hangHoas ?? [];
$danhMucs = $danhMucs ?? [];
$stats = $stats ?? ['total' => 0, 'inStock' => 0, 'lowStock' => 0, 'outStock' => 0];
$filters = $filters ?? ['q' => '', 'ma_danh_muc' => '', 'trang_thai' => ''];
?>
<section class="page-hero">
    <div>
        <h1>Quản lý hàng hóa</h1>
        <p>Quản lý dữ liệu sản phẩm, đơn giá bán, mã vạch và theo dõi số lượng tồn kho theo lô.</p>
    </div>
    <?php if (can_access('hang-hoa')): ?>
        <div class="page-actions">
            <a class="button" href="<?= e(url_for('hang-hoa', 'form')) ?>">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Thêm hàng hóa
            </a>
        </div>
    <?php endif; ?>
</section>

<section class="stat-overview stat-overview--compact">
    <article class="stat-card tone-blue">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7l-8-4-8 4 8 4 8-4zm0 6l-8 4-8-4m20 6l-8 4-8-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <div class="stat-card__content">
            <span class="stat-card__label">Tổng mặt hàng</span>
            <strong class="stat-card__value"><?= e((string)$stats['total']) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-green">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 12l2 2 4-4M3 12a9 9 0 1118 0 9 9 0 01-18 0z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <div class="stat-card__content">
            <span class="stat-card__label">Còn hàng</span>
            <strong class="stat-card__value"><?= e((string)$stats['inStock']) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-amber">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <div class="stat-card__content">
            <span class="stat-card__label">Sắp hết (<100)</span>
            <strong class="stat-card__value"><?= e((string)$stats['lowStock']) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-pink">
        <span class="stat-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.36 6.64a9 9 0 11-12.73 0M12 2v10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <div class="stat-card__content">
            <span class="stat-card__label">Hết hàng</span>
            <strong class="stat-card__value"><?= e((string)$stats['outStock']) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
</section>

<section class="search-toolbar">
    <form class="search-toolbar__main" method="GET" action="index.php">
        <input type="hidden" name="c" value="hang-hoa">
        <input type="hidden" name="a" value="index">
        <div class="search-box">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="text" name="q" id="search-q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Tìm tên, mã sản phẩm, mã vạch..."
                   hx-get="<?= e(url_for('hang-hoa', 'index')) ?>"
                   hx-trigger="keyup changed delay:400ms"
                   hx-target="#table-container"
                   hx-select="#table-container"
                   hx-include="#filter-category, #filter-status"
                   hx-push-url="true">
        </div>
    </form>
    <form class="search-toolbar__filters">
        <select name="ma_danh_muc" id="filter-category"
                hx-get="<?= e(url_for('hang-hoa', 'index')) ?>"
                hx-trigger="change"
                hx-target="#table-container"
                hx-select="#table-container"
                hx-include="#search-q, #filter-status"
                hx-push-url="true">
            <option value="">Tất cả danh mục</option>
            <?php foreach ($danhMucs as $dm): ?>
                <option value="<?= e($dm['ma_danh_muc']) ?>" <?= ($filters['ma_danh_muc'] ?? '') === $dm['ma_danh_muc'] ? 'selected' : '' ?>>
                    <?= e($dm['ten_danh_muc']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="trang_thai" id="filter-status"
                hx-get="<?= e(url_for('hang-hoa', 'index')) ?>"
                hx-trigger="change"
                hx-target="#table-container"
                hx-select="#table-container"
                hx-include="#search-q, #filter-category"
                hx-push-url="true">
            <option value="">Tất cả trạng thái</option>
            <option value="DANG_KINH_DOANH" <?= ($filters['trang_thai'] ?? '') === 'DANG_KINH_DOANH' ? 'selected' : '' ?>>Đang kinh doanh</option>
            <option value="NGUNG_KINH_DOANH" <?= ($filters['trang_thai'] ?? '') === 'NGUNG_KINH_DOANH' ? 'selected' : '' ?>>Ngừng kinh doanh</option>
        </select>
    </form>
</section>

<section class="table-card table-card--flush" id="table-container">
    <div class="table-card__header">
        <div>
            <h3>Danh sách hàng hóa</h3>
        </div>
    </div>
    <?php if ($hangHoas === []): ?>
        <div class="empty-state" style="margin: 24px;">Không tìm thấy sản phẩm nào phù hợp.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Mã hàng</th>
                        <th>Danh mục</th>
                        <th>Giá bán</th>
                        <th>Tổng tồn kho</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hangHoas as $hh): ?>
                        <tr id="row-<?= e($hh['ma_hang_hoa']) ?>">
                            <td>
                                <strong><?= e($hh['ten_hang_hoa']) ?></strong>
                                <div class="meta-text">Mã vạch: <?= e($hh['ma_vach'] ?: '-') ?></div>
                            </td>
                            <td><code><?= e($hh['ma_hang_hoa']) ?></code></td>
                            <td><?= e($hh['ten_danh_muc'] ?: 'Chưa phân loại') ?></td>
                            <td><strong><?= currency($hh['gia_ban']) ?></strong></td>
                            <td>
                                <?php 
                                $stock = (int)($hh['ton_kho'] ?? 0);
                                $stockTone = 'success';
                                if ($stock === 0) {
                                    $stockTone = 'danger';
                                } elseif ($stock < 100) {
                                    $stockTone = 'warning';
                                }
                                ?>
                                <span class="badge badge--<?= $stockTone ?>"><?= e((string)$stock) ?> <?= e($hh['don_vi_tinh']) ?></span>
                            </td>
                            <td>
                                <?php 
                                $statusTone = $hh['trang_thai'] === 'DANG_KINH_DOANH' ? 'success' : 'neutral';
                                $statusLabel = $hh['trang_thai'] === 'DANG_KINH_DOANH' ? 'Đang KD' : 'Ngừng KD';
                                ?>
                                <span class="badge badge--<?= $statusTone ?>"><?= e($statusLabel) ?></span>
                            </td>
                            <td>
                                <div class="inline-actions">
                                    <?php if (can_access('hang-hoa')): ?>
                                        <a class="text-link" href="<?= e(url_for('hang-hoa', 'form', ['id' => $hh['ma_hang_hoa']])) ?>">Sửa</a>
                                    <?php endif; ?>
                                    <?php if (current_user()['ma_chuc_vu'] === 'ADMIN'): ?>
                                        <button type="button" class="button button--ghost" onclick="deleteProduct('<?= e($hh['ma_hang_hoa']) ?>', '<?= e(addslashes($hh['ten_hang_hoa'])) ?>')">Xóa</button>
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
function deleteProduct(id, name) {
    Swal.fire({
        title: 'Bạn có chắc chắn?',
        html: `Bạn chuẩn bị xoá sản phẩm <strong>${name}</strong>.<br>Hành động này không thể hoàn tác!`,
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
            fetch(`<?= e(url_for('hang-hoa', 'delete')) ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ma_hang_hoa: id})
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
                        text: 'Sản phẩm đã được xoá thành công.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Lỗi', data.message || 'Không thể xóa sản phẩm này!', 'error');
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
