<?php
$filters = $filters ?? [];
$summary = $summary ?? [];
$revenueByDay = $revenueByDay ?? [];
$revenueByEmployee = $revenueByEmployee ?? [];
$expiringLots = $expiringLots ?? [];
$lossReports = $lossReports ?? [];
$inventoryLots = $inventoryLots ?? [];

$chartRows = $revenueByDay;
usort($chartRows, static fn(array $a, array $b): int => strcmp((string) $a['ngay'], (string) $b['ngay']));

if ($chartRows === []) {
    for ($offset = 6; $offset >= 0; $offset--) {
        $chartRows[] = [
            'ngay' => date('Y-m-d', strtotime('-' . $offset . ' day')),
            'so_hoa_don' => 0,
            'doanh_thu' => 0,
        ];
    }
}

$maxRevenue = 1.0;
foreach ($chartRows as $row) {
    $maxRevenue = max($maxRevenue, (float) ($row['doanh_thu'] ?? 0));
}

$validLots = max(0, (int) ($summary['tong_lo'] ?? 0) - (int) ($summary['lo_sap_het_han'] ?? 0) - (int) ($summary['lo_het_han'] ?? 0));
$distribution = [
    ['label' => 'Sắp hết hạn', 'value' => (int) ($summary['lo_sap_het_han'] ?? 0), 'color' => '#5a7df0'],
    ['label' => 'Còn hạn', 'value' => $validLots, 'color' => '#69be85'],
    ['label' => 'Hết hạn', 'value' => (int) ($summary['lo_het_han'] ?? 0), 'color' => '#efaa06'],
    ['label' => 'Phiếu hủy', 'value' => count($lossReports), 'color' => '#7f8797'],
];
$distributionTotal = max(1, array_sum(array_column($distribution, 'value')));
$donutStops = [];
$progress = 0.0;
foreach ($distribution as $item) {
    $portion = $item['value'] / $distributionTotal;
    $start = $progress;
    $progress += $portion;
    $donutStops[] = $item['color'] . ' ' . number_format($start, 4, '.', '') . 'turn ' . number_format($progress, 4, '.', '') . 'turn';
}
$donutStyle = '--donut: conic-gradient(' . implode(', ', $donutStops) . ');';
?>
<section class="page-hero">
    <div>
        <h1>Báo cáo &amp; Phân tích</h1>
        <p>Thống kê và phân tích dữ liệu kinh doanh theo giao diện dashboard.</p>
    </div>
    <form method="get" action="<?= e(url_for('bao-cao', 'index')) ?>" class="page-actions">
        <input type="hidden" name="from" value="<?= e($filters['from'] ?? '') ?>">
        <input type="hidden" name="to" value="<?= e($filters['to'] ?? '') ?>">
        <select name="expiring_days">
            <option value="7" <?= (int) ($filters['expiring_days'] ?? 30) === 7 ? 'selected' : '' ?>>7 ngày qua</option>
            <option value="15" <?= (int) ($filters['expiring_days'] ?? 30) === 15 ? 'selected' : '' ?>>15 ngày</option>
            <option value="30" <?= (int) ($filters['expiring_days'] ?? 30) === 30 ? 'selected' : '' ?>>30 ngày</option>
        </select>
        <button type="submit" class="button" style="padding: 0 16px; height: 42px; font-size: 14px; min-width: unset;">
            <svg viewBox="0 0 24 24" aria-hidden="true" style="width: 18px; height: 18px;"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z" stroke-width="1.8" fill="none" stroke="currentColor" stroke-linejoin="round"/></svg>
            Lọc dữ liệu
        </button>
    </form>
</section>

<section class="stat-overview">
    <article class="stat-card tone-green">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7H15a3.5 3.5 0 0 1 0 7H6"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Doanh thu tuần này</span>
            <strong class="stat-card__value"><?= e(currency($summary['doanh_thu'] ?? 0)) ?></strong>
            <!-- <span class="stat-card__hint">+15.3%</span> -->
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-blue">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h10l1 13H6L7 7zm2-3h6l1 3H8l1-3z"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Đơn hàng hoàn tất</span>
            <strong class="stat-card__value"><?= e(compact_number($summary['so_hoa_don'] ?? 0)) ?></strong>
            <!-- <span class="stat-card__hint">+8.7%</span> -->
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-amber">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 16 5-5 4 4 7-8"/><path d="M20 7h-5v5"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Giá trị TB / đơn</span>
            <strong class="stat-card__value"><?= e(currency($summary['trung_binh_hoa_don'] ?? 0)) ?></strong>
            <!-- <span class="stat-card__hint">+6.2%</span> -->
        </div>
        <span class="stat-card__accent"></span>
    </article>
    <article class="stat-card tone-violet">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19h16M7 16V8m5 8V5m5 11v-6"/></svg>
        </span>
        <div class="stat-card__content">
            <span class="stat-card__label">Tồn kho (SKU)</span>
            <strong class="stat-card__value"><?= e(compact_number($summary['tong_so_luong_ton'] ?? 0)) ?></strong>
            <!-- <span class="stat-card__hint">+11.5%</span> -->
        </div>
        <span class="stat-card__accent"></span>
    </article>
</section>

<section class="analytics-grid">
    <article class="table-card table-card--flush chart-card">
        <div class="table-card__header">
            <h3>Doanh thu 7 ngày gần nhất</h3>
            <a class="text-link" href="<?= e(url_for('bao-cao', 'index')) ?>">Xem chi tiết</a>
        </div>
        <div class="chart-card__body">
            <div class="bar-chart">
                <?php foreach ($chartRows as $row): ?>
                    <?php
                    $revenue = (float) ($row['doanh_thu'] ?? 0);
                    $height = max(10, (int) round(($revenue / $maxRevenue) * 100));
                    $dayOfWeek = (int) date('N', strtotime((string) $row['ngay']));
                    $label = $dayOfWeek === 7 ? 'CN' : 'T' . ($dayOfWeek + 1);
                    ?>
                    <div class="bar-chart__item">
                        <span class="bar-chart__value"><?= e(compact_number($revenue)) ?>đ</span>
                        <div class="bar-chart__track">
                            <div class="bar-chart__fill" style="height: <?= e((string) $height) ?>%;"></div>
                        </div>
                        <span class="bar-chart__label"><?= e($label) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </article>

    <article class="table-card">
        <div class="table-card__header">
            <h3>Tình trạng tồn kho</h3>
        </div>
        <div class="donut-card__wrap">
            <div class="donut" style="<?= e($donutStyle) ?>">
                <div class="donut__center"><?= e((string) ($summary['tong_lo'] ?? 0)) ?></div>
            </div>
            <div class="legend-list">
                <?php foreach ($distribution as $item): ?>
                    <div class="legend-item">
                        <span class="legend-item__label">
                            <span class="legend-dot" style="background: <?= e($item['color']) ?>"></span>
                            <?= e($item['label']) ?>
                        </span>
                        <strong><?= e((string) $item['value']) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </article>
</section>

<section class="cards-grid split">
    <article class="table-card table-card--flush">
        <div class="table-card__header">
            <div>
                <h3>Hàng sắp hết hạn</h3>
                <p class="section-subtitle">Theo dõi các lô cần ưu tiên xử lý trong <?= e((string) ($filters['expiring_days'] ?? 30)) ?> ngày.</p>
            </div>
        </div>
        <?php if ($expiringLots === []): ?>
            <div class="empty-state" style="margin: 24px;">Chưa có lô nào cảnh báo theo ngưỡng hiện tại.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Lô hàng</th>
                            <th>Hàng hóa</th>
                            <th>HSD</th>
                            <th>Tồn</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($expiringLots, 0, 6) as $lot): ?>
                            <?php $statusCode = ((int) $lot['so_ngay_con_lai']) < 0 ? 'HET_HAN' : 'SAP_HET_HAN'; ?>
                            <tr>
                                <td><?= e($lot['ma_lo_hang']) ?></td>
                                <td>
                                    <strong><?= e($lot['ten_hang_hoa']) ?></strong><br>
                                    <span class="meta-text"><?= e($lot['ma_hang_hoa']) ?></span>
                                </td>
                                <td><?= e($lot['han_su_dung']) ?></td>
                                <td><?= e((string) $lot['tong_so_luong']) ?></td>
                                <td><span class="badge badge--<?= e(badge_tone($statusCode)) ?>"><?= e(status_label($statusCode)) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>

    <article class="table-card table-card--flush">
        <div class="table-card__header">
            <div>
                <h3>Phiếu hủy &amp; thất thoát</h3>
                <p class="section-subtitle">Tổng hợp nhanh các phiếu hủy trong kỳ.</p>
            </div>
        </div>
        <?php if ($lossReports === []): ?>
            <div class="empty-state" style="margin: 24px;">Chưa có phiếu hủy trong khoảng thời gian đã chọn.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Phiếu</th>
                            <th>Nhân viên</th>
                            <th>Thất thoát</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($lossReports, 0, 6) as $report): ?>
                            <?php $statusCode = (string) $report['trang_thai']; ?>
                            <tr>
                                <td>
                                    <strong><?= e($report['ma_phieu_huy']) ?></strong><br>
                                    <span class="meta-text"><?= e($report['ngay_tao']) ?></span>
                                </td>
                                <td><?= e($report['ten_nhan_vien']) ?></td>
                                <td><?= e(currency($report['gia_tri_that_thoat'])) ?></td>
                                <td><span class="badge badge--<?= e(badge_tone($statusCode)) ?>"><?= e(status_label($statusCode)) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>
</section>

<section class="table-card table-card--flush">
    <div class="table-card__header">
        <div>
            <h3>Tồn kho chi tiết theo lô / HSD</h3>
            <p class="section-subtitle">Bảng dữ liệu đầy đủ phục vụ kiểm soát tồn kho và hạn sử dụng.</p>
        </div>
    </div>
    <?php if ($inventoryLots === []): ?>
        <div class="empty-state" style="margin: 24px;">Chưa có dữ liệu lô hàng trong kho.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Lô</th>
                        <th>Sản phẩm</th>
                        <th>Trong kho</th>
                        <th>Trên kệ</th>
                        <th>Tổng tồn</th>
                        <th>HSD</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventoryLots as $lot): ?>
                        <?php
                        $days = (int) $lot['so_ngay_con_lai'];
                        $statusCode = 'CON_HAN';
                        if ($days < 0) {
                            $statusCode = 'HET_HAN';
                        } elseif ($days <= (int) ($filters['expiring_days'] ?? 30)) {
                            $statusCode = 'SAP_HET_HAN';
                        }
                        ?>
                        <tr>
                            <td><?= e($lot['ma_lo_hang']) ?></td>
                            <td>
                                <strong><?= e($lot['ten_hang_hoa']) ?></strong><br>
                                <span class="meta-text"><?= e($lot['ma_hang_hoa']) ?> / <?= e($lot['don_vi_tinh']) ?></span>
                            </td>
                            <td><?= e((string) $lot['so_luong_trong_kho']) ?></td>
                            <td><?= e((string) $lot['so_luong_tren_ke']) ?></td>
                            <td><?= e((string) $lot['tong_so_luong']) ?></td>
                            <td><?= e($lot['han_su_dung']) ?></td>
                            <td><span class="badge badge--<?= e(badge_tone($statusCode)) ?>"><?= e(status_label($statusCode)) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
