<?php
declare(strict_types=1);
$exports = $exports ?? [];
$filters = $filters ?? ['q' => '', 'date_from' => '', 'date_to' => ''];
$stats = $stats ?? ['total_count' => 0];

$formatQty = static function (mixed $value): string {
    $number = (float) $value;
    $formatted = number_format($number, 3, ',', '.');
    return rtrim(rtrim($formatted, '0'), ',');
};
?>
<section class="page-hero">
    <div>
        <h1>Xuất hàng ra quầy</h1>
        <p>Quản lý và lập phiếu xuất hàng hóa từ kho chính ra quầy trưng bày.</p>
    </div>
    <div class="page-actions">
        <a class="button" href="<?= e(url_for('kho', 'xuat_form')) ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Lập phiếu xuất
        </a>
    </div>
</section>

<!-- Thẻ thống kê -->
<section class="stat-overview stat-overview--compact" style="grid-template-columns: repeat(1, minmax(0, 1fr));">
    <article class="stat-card tone-blue">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2z" fill="currentColor"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Tổng phiếu xuất</span>
            <strong class="stat-card__value"><?= e((string) ($stats['total_count'] ?? 0)) ?> phiếu</strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
</section>

<!-- Bộ lọc -->
<section class="search-toolbar">
    <form class="search-toolbar__main" method="GET" action="index.php">
        <input type="hidden" name="c" value="kho">
        <input type="hidden" name="a" value="xuat_index">
        <div class="search-box">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="text" name="q" id="search-q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Tìm mã phiếu..."
                   hx-get="<?= e(url_for('kho', 'xuat_index')) ?>"
                   hx-trigger="keyup changed delay:400ms"
                   hx-target="#table-container"
                   hx-select="#table-container"
                   hx-include="#filter-date-from, #filter-date-to"
                   hx-push-url="true">
        </div>
    </form>
    <form class="search-toolbar__filters" method="GET" action="index.php">
        <input type="hidden" name="c" value="kho">
        <input type="hidden" name="a" value="xuat_index">
        
        <input type="date" name="date_from" id="filter-date-from" value="<?= e($filters['date_from'] ?? '') ?>" title="Từ ngày"
               style="width: auto; padding: 10px 14px;"
               hx-get="<?= e(url_for('kho', 'xuat_index')) ?>"
               hx-trigger="change"
               hx-target="#table-container"
               hx-select="#table-container"
               hx-include="#search-q, #filter-date-to"
               hx-push-url="true">

        <input type="date" name="date_to" id="filter-date-to" value="<?= e($filters['date_to'] ?? '') ?>" title="Đến ngày"
               style="width: auto; padding: 10px 14px;"
               hx-get="<?= e(url_for('kho', 'xuat_index')) ?>"
               hx-trigger="change"
               hx-target="#table-container"
               hx-select="#table-container"
               hx-include="#search-q, #filter-date-from"
               hx-push-url="true">
               
        <a href="<?= e(url_for('kho', 'xuat_index')) ?>" class="button button--ghost" style="padding: 10px 18px;">Xóa lọc</a>
    </form>
</section>

<!-- Bảng danh sách -->
<div id="table-container">
    <section class="table-card table-card--flush">
        <div class="table-card__header">
            <div>
                <h3>Danh sách phiếu xuất</h3>
            </div>
        </div>
        <?php if (empty($exports)): ?>
            <div class="empty-state" style="margin: 24px;">Chưa có phiếu xuất nào phù hợp.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Mã phiếu</th>
                            <th>Ngày tạo</th>
                            <th style="text-align: right;">Tổng số lượng</th>
                            <th>Người lập</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exports as $ex): ?>
                            <tr>
                                <td><code><?= e($ex['ma_phieu_xuat']) ?></code></td>
                                <td><?= e(date('d/m/Y', strtotime($ex['ngay_tao']))) ?></td>
                                <td style="text-align: right;"><span class="badge badge--neutral"><?= e($formatQty($ex['tong_so_luong'])) ?></span></td>
                                <td><?= e($ex['ten_nhan_vien'] ?? '—') ?></td>
                                <td>
                                    <div class="inline-actions">
                                        <a class="text-link" href="<?= e(url_for('kho', 'xuat_detail', ['id' => $ex['ma_phieu_xuat']])) ?>">Xem chi tiết</a>
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
