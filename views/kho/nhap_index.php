<?php
declare(strict_types=1);
$receipts = $receipts ?? [];
$filters = $filters ?? ['q' => '', 'date_from' => '', 'date_to' => ''];
$stats = $stats ?? ['total_count' => 0, 'total_amount' => 0];
?>
<section class="page-hero">
    <div>
        <h1>Nhập kho</h1>
        <p>Quản lý và lập phiếu nhập hàng hóa từ các Nhà cung cấp vào kho siêu thị.</p>
    </div>
    <div class="page-actions">
        <a class="button" href="<?= e(url_for('kho', 'nhap_form')) ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Lập phiếu nhập
        </a>
    </div>
</section>

<!-- Thẻ thống kê -->
<section class="stat-overview stat-overview--compact" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
    <article class="stat-card tone-blue">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7H4a1 1 0 00-1 1v10a1 1 0 001 1h16a1 1 0 001-1V8a1 1 0 00-1-1zM7 5h10v2H7V5z" fill="currentColor"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Tổng phiếu nhập</span>
            <strong class="stat-card__value"><?= e((string) ($stats['total_count'] ?? 0)) ?> phiếu</strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-green">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" fill="currentColor"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Tổng giá trị nhập</span>
            <strong class="stat-card__value"><?= currency($stats['total_amount'] ?? 0) ?></strong>
        </div>
        <span class="stat-card__accent"></span>
    </article>
</section>

<!-- Bộ lọc -->
<section class="search-toolbar">
    <form class="search-toolbar__main" method="GET" action="index.php">
        <input type="hidden" name="c" value="kho">
        <input type="hidden" name="a" value="nhap_index">
        <div class="search-box">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="text" name="q" id="search-q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Tìm mã phiếu, nhà cung cấp..."
                   hx-get="<?= e(url_for('kho', 'nhap_index')) ?>"
                   hx-trigger="keyup changed delay:400ms"
                   hx-target="#table-container"
                   hx-select="#table-container"
                   hx-include="#filter-date-from, #filter-date-to"
                   hx-push-url="true">
        </div>
    </form>
    <form class="search-toolbar__filters" method="GET" action="index.php">
        <input type="hidden" name="c" value="kho">
        <input type="hidden" name="a" value="nhap_index">
        
        <input type="date" name="date_from" id="filter-date-from" value="<?= e($filters['date_from'] ?? '') ?>" title="Từ ngày"
               style="width: auto; padding: 10px 14px;"
               hx-get="<?= e(url_for('kho', 'nhap_index')) ?>"
               hx-trigger="change"
               hx-target="#table-container"
               hx-select="#table-container"
               hx-include="#search-q, #filter-date-to"
               hx-push-url="true">

        <input type="date" name="date_to" id="filter-date-to" value="<?= e($filters['date_to'] ?? '') ?>" title="Đến ngày"
               style="width: auto; padding: 10px 14px;"
               hx-get="<?= e(url_for('kho', 'nhap_index')) ?>"
               hx-trigger="change"
               hx-target="#table-container"
               hx-select="#table-container"
               hx-include="#search-q, #filter-date-from"
               hx-push-url="true">
               
        <a href="<?= e(url_for('kho', 'nhap_index')) ?>" class="button button--ghost" style="padding: 10px 18px;">Xóa lọc</a>
    </form>
</section>

<!-- Bảng danh sách -->
<div id="table-container">
    <section class="table-card table-card--flush">
        <div class="table-card__header">
            <div>
                <h3>Danh sách phiếu nhập</h3>
            </div>
        </div>
        <?php if (empty($receipts)): ?>
            <div class="empty-state" style="margin: 24px;">Chưa có phiếu nhập nào phù hợp.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Mã phiếu</th>
                            <th>Ngày tạo</th>
                            <th>Nhà cung cấp</th>
                            <th style="text-align: right;">Tổng số lượng</th>
                            <th style="text-align: right;">Tổng tiền</th>
                            <th>Người lập</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receipts as $r): ?>
                            <tr>
                                <td><code><?= e($r['ma_phieu_nhap']) ?></code></td>
                                <td><?= e(date('d/m/Y H:i', strtotime($r['ngay_tao']))) ?></td>
                                <td><strong><?= e($r['ten_nha_cung_cap'] ?? '—') ?></strong></td>
                                <td style="text-align: right;"><span class="badge badge--neutral"><?= e(number_format((int) $r['tong_so_luong'])) ?></span></td>
                                <td style="text-align: right; color: var(--red); font-weight: 700;"><?= currency($r['tong_tien']) ?></td>
                                <td><?= e($r['ten_nhan_vien'] ?? '—') ?></td>
                                <td>
                                    <div class="inline-actions">
                                        <a class="text-link" href="<?= e(url_for('kho', 'nhap_detail', ['id' => $r['ma_phieu_nhap']])) ?>">Xem chi tiết</a>
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
