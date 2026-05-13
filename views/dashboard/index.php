<?php
declare(strict_types=1);
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<section class="page-hero">
    <div>
        <h1>Tổng quan hệ thống</h1>
        <p>Thống kê hiệu năng bán hàng, trạng thái kho bãi và hoạt động ca trực.</p>
    </div>
</section>

<section class="stat-overview">
    <article class="stat-card tone-blue">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Doanh thu hôm nay</span>
            <strong class="stat-card__value"><?= currency($stats['revenue_today']) ?></strong>
            <span class="stat-card__hint" style="color: <?= $stats['rev_change_pct'] >= 0 ? '#3b915e' : '#d92e67' ?>;">
                <?= $stats['rev_change_pct'] >= 0 ? '↑' : '↓' ?> <?= abs($stats['rev_change_pct']) ?>% so với hôm qua
            </span>
        </div>
        <span class="stat-card__accent"></span>
    </article>

    <article class="stat-card tone-green">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 12l2 2 4-4M3 12a9 9 0 1118 0 9 9 0 01-18 0z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Đơn hàng hôm nay</span>
            <strong class="stat-card__value"><?= e((string)$stats['total_invoices_today']) ?> hóa đơn</strong>
            <span class="stat-card__hint">
                <?= $stats['invoice_diff'] >= 0 ? '▲' : '▼' ?> <?= abs($stats['invoice_diff']) ?> đơn so với hôm qua
            </span>
        </div>
        <span class="stat-card__accent"></span>
    </article>

    <article class="stat-card tone-violet">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7l-8-4-8 4 8 4 8-4zm0 6l-8 4-8-4m20 6l-8 4-8-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Hàng hóa</span>
            <strong class="stat-card__value"><?= e((string)$stats['total_skus']) ?> SP</strong>
            <span class="stat-card__hint" style="color: var(--muted);">Có trong hệ thống</span>
        </div>
        <span class="stat-card__accent"></span>
    </article>

    <article class="stat-card <?= $stats['expiring_soon_count'] > 0 ? 'tone-pink' : 'tone-amber' ?>">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Cảnh báo hạn sử dụng</span>
            <strong class="stat-card__value"><?= e((string)$stats['expiring_soon_count']) ?> lô hàng</strong>
            <span class="stat-card__hint" style="color: #d92e67;">Đã hết hạn hoặc dưới 7 ngày</span>
        </div>
        <span class="stat-card__accent"></span>
    </article>
</section>

<div class="analytics-grid">
    <!-- Chart 7 ngày -->
    <div class="widget-card chart-card">
        <h3 class="section-title">Doanh thu 7 ngày qua</h3>
        <p class="section-subtitle">Đơn vị tính: Triệu VND</p>
        <div class="chart-card__body">
            <canvas id="revenueChart" style="max-height: 260px;"></canvas>
        </div>
    </div>

    <!-- Cơ cấu danh mục -->
    <div class="widget-card">
        <h3 class="section-title">Tỷ trọng danh mục</h3>
        <p class="section-subtitle">Dựa trên số lượng sản phẩm</p>
        <div style="display: flex; justify-content: center; align-items: center; margin-top: 24px;">
            <canvas id="categoryChart" style="max-width: 200px; max-height: 200px;"></canvas>
        </div>
    </div>
</div>

<div class="split">
    <!-- Hàng sắp hết hạn -->
    <div class="table-card table-card--flush">
        <div class="table-card__header">
            <div>
                <h3>Cảnh báo hạn sử dụng</h3>
                <p class="section-subtitle">Các lô hàng đã hết hạn sử dụng (cần hủy) hoặc sắp hết hạn dưới 7 ngày.</p>
            </div>
        </div>
        <?php if ($expiringItems === []): ?>
            <div class="empty-state" style="margin: 24px;">Không có lô hàng nào cảnh báo.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Mã lô</th>
                            <th>Hạn sử dụng</th>
                            <th>Số lượng (Kệ / Kho)</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expiringItems as $item): ?>
                            <tr>
                                <td>
                                    <strong><?= e($item['ten_hang_hoa']) ?></strong>
                                    <div class="meta-text"><?= e($item['don_vi_tinh']) ?></div>
                                </td>
                                <td><code><?= e($item['ma_lo_hang']) ?></code></td>
                                <td>
                                    <span style="color: var(--red); font-weight: 700;">
                                        <?= date('d/m/Y', strtotime((string)$item['han_su_dung'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= e((string)$item['so_luong_tren_ke']) ?></strong> / <?= e((string)$item['so_luong_trong_kho']) ?>
                                </td>
                                <td>
                                    <?php
                                    $isExpired = strtotime((string)$item['han_su_dung']) < strtotime(date('Y-m-d'));
                                    $statusCode = $isExpired ? 'HET_HAN' : 'SAP_HET_HAN';
                                    ?>
                                    <span class="badge badge--<?= e(badge_tone($statusCode)) ?>"><?= e(status_label($statusCode)) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Widgets hoạt động gần đây -->
    <div class="widget-card list-card" style="margin-bottom: 0;">
        <div class="table-card__header" style="border-bottom: 1px solid var(--line);">
            <div>
                <h3 class="section-title">Nhật ký hoạt động hôm nay</h3>
                <p class="section-subtitle">Cập nhật thời gian thực các giao dịch.</p>
            </div>
        </div>
        <?php if ($activityItems === []): ?>
            <div style="padding: 24px; text-align: center; color: var(--muted);">Không có hoạt động nào hôm nay.</div>
        <?php else: ?>
            <ul>
                <?php foreach ($activityItems as $act): ?>
                    <li style="display: flex; gap: 14px; align-items: flex-start; border-bottom: 1px solid var(--line);">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--<?= e($act['color']) ?>); margin-top: 6px; flex-shrink: 0;"></span>
                        <div style="flex: 1;">
                            <strong style="display: block; font-size: 15px;"><?= e($act['title']) ?></strong>
                            <span class="meta-text"><?= e($act['detail']) ?></span>
                        </div>
                        <span class="meta-text" style="white-space: nowrap;"><?= e($act['time']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<script>
function initDashboardCharts() {
    var revCanvas = document.getElementById('revenueChart');
    var catCanvas = document.getElementById('categoryChart');
    if (!revCanvas || !catCanvas) return;

    if (window._revenueChart) window._revenueChart.destroy();
    if (window._categoryChart) window._categoryChart.destroy();

    var revCtx = revCanvas.getContext('2d');
    var revData = <?= json_encode($revenue7days) ?>;

    window._revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: revData.map(function(item) { return item.label; }),
            datasets: [{
                label: 'Doanh thu',
                data: revData.map(function(item) { return item.val; }),
                borderColor: '#3f62ea',
                backgroundColor: 'rgba(63, 98, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#3f62ea',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    grid: { color: '#eef2f7' },
                    ticks: { font: { family: 'Segoe UI' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Segoe UI' } }
                }
            }
        }
    });

    var catCtx = catCanvas.getContext('2d');
    var catData = <?= json_encode($categoryDistribution) ?>;

    var labels = [];
    var data = [];
    if (catData.length > 5) {
        var top5 = catData.slice(0, 4);
        labels = top5.map(function(item) { return item.name; });
        data = top5.map(function(item) { return item.count; });

        var otherCount = catData.slice(4).reduce(function(sum, item) { return sum + parseInt(item.count); }, 0);
        labels.push('Khác');
        data.push(otherCount);
    } else {
        labels = catData.map(function(item) { return item.name; });
        data = catData.map(function(item) { return item.count; });
    }

    window._categoryChart = new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#3f62ea',
                    '#54b87a',
                    '#8b5cf6',
                    '#efaa06',
                    '#e73f73',
                    '#a0aec0'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: { size: 12, family: 'Segoe UI' }
                    }
                }
            },
            cutout: '65%'
        }
    });
}

document.addEventListener('DOMContentLoaded', initDashboardCharts);
document.addEventListener('htmx:afterSwap', function(e) {
    if (document.getElementById('revenueChart') || document.getElementById('categoryChart')) {
        initDashboardCharts();
    }
});
</script>
