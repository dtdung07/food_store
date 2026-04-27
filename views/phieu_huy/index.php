<?php
declare(strict_types=1);
$slips = $slips ?? [];
$filters = $filters ?? ['q' => '', 'status' => 'all', 'date_from' => '', 'date_to' => ''];
$stats = $stats ?? ['total' => 0, 'cho_duyet' => 0, 'da_duyet' => 0, 'tu_choi' => 0];
?>
<section class="page-hero">
    <div>
        <h1>Phiếu hủy hàng</h1>
        <p>Quản lý và duyệt phiếu hủy hàng hóa bị hỏng, hết hạn.</p>
    </div>
    <?php 
    $currentRole = current_user()['ma_chuc_vu'] ?? '';
    if (in_array($currentRole, ['ADMIN', 'THU_KHO', 'NV_QUAY_CAN'], true)): 
    ?>
        <div class="page-actions">
            <a class="button" href="<?= e(url_for('phieu-huy', 'form')) ?>">
                <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" style="width:16px; height:16px;"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Lập phiếu hủy
            </a>
        </div>
    <?php endif; ?>
</section>

<!-- Thẻ thống kê -->
<section class="stat-overview stat-overview--compact">
    <article class="stat-card tone-blue">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" fill="currentColor"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Tổng số phiếu</span>
            <strong class="stat-card__value"><?= e((string) ($stats['total'] ?? 0)) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-amber">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" fill="currentColor"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Chờ duyệt</span>
            <strong class="stat-card__value"><?= e((string) ($stats['cho_duyet'] ?? 0)) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-green">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" fill="currentColor"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Đã duyệt</span>
            <strong class="stat-card__value"><?= e((string) ($stats['da_duyet'] ?? 0)) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-pink">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" fill="currentColor"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Từ chối</span>
            <strong class="stat-card__value"><?= e((string) ($stats['tu_choi'] ?? 0)) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
</section>

<!-- Bộ lọc -->
<form class="search-toolbar" method="GET" action="index.php" style="display: flex; gap: 12px; align-items: center; justify-content: flex-start; flex-wrap: wrap;">
    <input type="hidden" name="c" value="phieu-huy">
    <input type="hidden" name="a" value="index">
    <select name="status" id="filter-status"
            style="width: 200px; padding: 15px 16px;"
            hx-get="<?= e(url_for('phieu-huy', 'index')) ?>"
            hx-trigger="change"
            hx-target="#table-container"
            hx-select="#table-container"
            hx-push-url="true">
        <option value="all" <?= ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>Tất cả trạng thái</option>
        <option value="CHO_DUYET" <?= ($filters['status'] ?? '') === 'CHO_DUYET' ? 'selected' : '' ?>>Chờ duyệt</option>
        <option value="DA_DUYET" <?= ($filters['status'] ?? '') === 'DA_DUYET' ? 'selected' : '' ?>>Đã duyệt</option>
        <option value="TU_CHOI" <?= ($filters['status'] ?? '') === 'TU_CHOI' ? 'selected' : '' ?>>Từ chối</option>
    </select>
    
    <div class="search-box" style="flex: 1; min-width: 250px;">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input type="text" name="q" id="search-q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Tìm mã phiếu, người lập..."
               hx-get="<?= e(url_for('phieu-huy', 'index')) ?>"
               hx-trigger="keyup changed delay:400ms"
               hx-target="#table-container"
               hx-select="#table-container"
               hx-push-url="true">
    </div>

    <input type="date" name="date_from" id="filter-date-from" value="<?= e($filters['date_from'] ?? '') ?>" title="Từ ngày"
           style="width: 170px; padding: 15px 16px;"
           hx-get="<?= e(url_for('phieu-huy', 'index')) ?>"
           hx-trigger="change"
           hx-target="#table-container"
           hx-select="#table-container"
           hx-push-url="true">

    <input type="date" name="date_to" id="filter-date-to" value="<?= e($filters['date_to'] ?? '') ?>" title="Đến ngày"
           style="width: 170px; padding: 15px 16px;"
           hx-get="<?= e(url_for('phieu-huy', 'index')) ?>"
           hx-trigger="change"
           hx-target="#table-container"
           hx-select="#table-container"
           hx-push-url="true">
           
    <a href="<?= e(url_for('phieu-huy', 'index')) ?>" class="button button--ghost" style="padding: 15px 22px;">Xóa lọc</a>
</form>

<!-- Bảng danh sách -->
<div id="table-container">
    <section class="table-card table-card--flush">
        <div class="table-card__header">
            <div>
                <h3>Danh sách phiếu hủy</h3>
            </div>
        </div>
        <?php if (empty($slips)): ?>
            <div class="empty-state" style="margin: 24px;">Chưa có phiếu hủy nào phù hợp.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Mã phiếu</th>
                            <th>Ngày tạo</th>
                            <th style="text-align: right;">Tổng số lượng</th>
                            <th>Trạng thái</th>
                            <th>Người lập</th>
                            <th>Người duyệt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($slips as $s): ?>
                            <tr>
                                <td><code><?= e($s['ma_phieu_huy']) ?></code></td>
                                <td><?= e(date('d/m/Y', strtotime($s['ngay_tao']))) ?></td>
                                <td style="text-align: right;"><span class="badge badge--neutral"><?= e(number_format((int) $s['tong_so_luong'])) ?></span></td>
                                <td>
                                    <?php 
                                    $statusTone = badge_tone($s['trang_thai']);
                                    $statusLabelText = status_label($s['trang_thai']);
                                    ?>
                                    <span class="badge badge--<?= $statusTone ?>"><?= e($statusLabelText) ?></span>
                                </td>
                                <td><?= e($s['ten_nhan_vien'] ?? '—') ?></td>
                                <td><?= e($s['ten_nguoi_duyet'] ?? '—') ?></td>
                                <td>
                                    <div class="inline-actions">
                                        <a class="text-link" href="<?= e(url_for('phieu-huy', 'detail', ['id' => $s['ma_phieu_huy']])) ?>">Xem chi tiết</a>
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
