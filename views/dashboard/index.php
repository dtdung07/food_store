<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap');

/* ── RESET / BASE ─────────────────────────────────────── */
.db-root { font-family: 'Inter', sans-serif; color: #0f172a; }

/* ── TOP BAR ──────────────────────────────────────────── */
.db-topbar {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px;
    margin-bottom: 24px;
}
.db-topbar h1 {
    margin: 0; font-size: 22px; font-weight: 700; color: #0f172a;
}
.db-topbar-meta {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
}
.db-pill {
    display: inline-flex; align-items: center; gap: 7px;
    font-size: 13px; font-weight: 500; color: #475569;
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 9999px; padding: 7px 14px;
    white-space: nowrap;
}
.db-pill-warn {
    background: #fff7ed; border-color: #fed7aa; color: #c2410c;
    font-weight: 600;
}

/* ── KPI CARDS 4-col ─────────────────────────────────── */
.db-kpi-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0,1fr));
    gap: 16px;
    margin-bottom: 20px;
}
.kpi-card {
    background: #fff; border-radius: 20px;
    border: 1px solid #e8edf4;
    padding: 24px 26px 20px;
    position: relative; overflow: hidden;
    display: flex; flex-direction: column; gap: 6px;
    min-height: 170px;
}
/* colored bottom stripe */
.kpi-card::after {
    content: ''; position: absolute;
    bottom: 0; left: 0; right: 0; height: 4px;
    border-radius: 0 0 20px 20px;
}
.kpi-green::after  { background: #16a34a; }
.kpi-blue::after   { background: #2563eb; }
.kpi-amber::after  { background: #d97706; }
.kpi-rose::after   { background: #e11d48; }

/* soft bg blob */
.kpi-card::before {
    content: ''; position: absolute;
    top: -20px; right: -20px;
    width: 100px; height: 100px;
    border-radius: 50%;
    opacity: .07;
}
.kpi-green::before  { background: #16a34a; }
.kpi-blue::before   { background: #2563eb; }
.kpi-amber::before  { background: #d97706; }
.kpi-rose::before   { background: #e11d48; }

.kpi-label {
    font-size: 12px; font-weight: 600; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .05em;
}
.kpi-value {
    font-size: 30px; font-weight: 700; color: #0f172a;
    line-height: 1.15;
}
.kpi-change {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 13px; font-weight: 600;
    margin-top: 2px;
}
.kpi-change.up   { color: #16a34a; }
.kpi-change.down { color: #e11d48; }
.kpi-change.neutral { color: #d97706; }
.kpi-badge {
    display: inline-flex; align-items: center; gap: 6px;
    margin-top: auto; padding: 6px 12px;
    border-radius: 9999px; font-size: 12px; font-weight: 700;
    width: fit-content;
}
.badge-green  { background: #dcfce7; color: #166534; }
.badge-blue   { background: #dbeafe; color: #1e40af; }
.badge-amber  { background: #fef3c7; color: #92400e; }
.badge-rose   { background: #ffe4e6; color: #9f1239; }

/* ── SECTION GRID ─────────────────────────────────────── */
.db-row { display: grid; gap: 16px; margin-bottom: 20px; }
.db-row-2-1 { grid-template-columns: 2fr 1fr; }
.db-row-3   { grid-template-columns: repeat(3, minmax(0,1fr)); }

/* ── PANEL ────────────────────────────────────────────── */
.panel {
    background: #fff; border-radius: 20px;
    border: 1px solid #e8edf4;
    overflow: hidden;
}
.panel-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 22px; border-bottom: 1px solid #f1f5f9;
}
.panel-head h3 { margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; }
.panel-head a, .panel-head-link {
    font-size: 13px; font-weight: 600; color: #2563eb;
    text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
}
.panel-head a:hover { text-decoration: underline; }
.panel-body { padding: 18px 22px; }

/* ── ONLINE USERS COUNTER ────────────────────────────── */
.online-counter {
    display: flex; align-items: center; gap: 16px;
    padding: 14px 22px; border-bottom: 1px solid #f1f5f9;
}
.counter-bubble {
    display: flex; flex-direction: column; align-items: center;
    gap: 2px;
}
.counter-num {
    font-size: 28px; font-weight: 700; line-height: 1;
}
.counter-num.c-green { color: #16a34a; }
.counter-num.c-gray  { color: #94a3b8; }
.counter-label { font-size: 10px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }
.counter-divider { width: 1px; height: 40px; background: #e2e8f0; }
.online-sub-label { font-size: 12px; color: #64748b; line-height: 1.5; }
.live-dot {
    display: inline-block; width: 8px; height: 8px;
    border-radius: 50%; background: #16a34a;
    animation: pulse-ring 1.8s ease-out infinite;
}
@keyframes pulse-ring {
    0%   { box-shadow: 0 0 0 0   rgba(22,163,74,.6); }
    70%  { box-shadow: 0 0 0 6px rgba(22,163,74,0);  }
    100% { box-shadow: 0 0 0 0   rgba(22,163,74,0);  }
}

/* user rows */
.u-list { display: flex; flex-direction: column; }
.u-item {
    display: flex; align-items: center; gap: 12px;
    padding: 11px 22px; border-bottom: 1px solid #f8fafc;
}
.u-item:last-child { border-bottom: none; }
.u-av {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; flex-shrink: 0;
}
.u-name { font-size: 13px; font-weight: 600; color: #0f172a; }
.u-role { font-size: 11px; color: #94a3b8; margin-top: 1px; }
.u-status {
    margin-left: auto; font-size: 11px; font-weight: 700;
    padding: 3px 9px; border-radius: 9999px; white-space: nowrap;
}
.st-online  { background: #dcfce7; color: #166534; }
.st-offline { background: #f1f5f9; color: #94a3b8; }

/* ── REVENUE CHART ────────────────────────────────────── */
.chart-area { height: 260px; position: relative; padding: 0 4px; }

/* chart legend */
.rev-legend {
    display: flex; align-items: center; gap: 16px;
    padding: 0 22px 14px; flex-wrap: wrap;
}
.rev-legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #64748b; }
.rev-legend-dot  { width: 10px; height: 10px; border-radius: 3px; }
.rev-summary {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 0; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;
}
.rev-sum-item {
    padding: 12px 18px; border-right: 1px solid #f1f5f9;
}
.rev-sum-item:last-child { border-right: none; }
.rev-sum-label { font-size: 11px; color: #94a3b8; font-weight: 500; }
.rev-sum-val   { font-size: 16px; font-weight: 700; color: #0f172a; margin-top: 2px; }
.rev-sum-val.positive { color: #16a34a; }
.rev-sum-val.negative { color: #e11d48; }

/* ── EXPIRY LIST ──────────────────────────────────────── */
.exp-list { display: flex; flex-direction: column; }
.exp-item {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 0; border-bottom: 1px solid #f8fafc;
}
.exp-item:last-child { border-bottom: none; }
.exp-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.exp-name { font-size: 13px; font-weight: 600; color: #0f172a; }
.exp-batch { font-size: 11px; color: #94a3b8; }
.exp-qty   { font-size: 12px; color: #64748b; margin-left: auto; white-space: nowrap; }
.exp-badge {
    font-size: 11px; font-weight: 700; padding: 3px 9px;
    border-radius: 9999px; white-space: nowrap; flex-shrink: 0;
}
.exp-1 { background: #fee2e2; color: #b91c1c; }
.exp-2 { background: #fef3c7; color: #92400e; }
.exp-3 { background: #fef9c3; color: #713f12; }
.exp-ok{ background: #f0fdf4; color: #166534; }

/* ── DONUT CHART ──────────────────────────────────────── */
.donut-wrap {
    position: relative; height: 190px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 10px;
}
.donut-center-label {
    position: absolute; text-align: center; pointer-events: none;
}
.donut-center-num  { font-size: 24px; font-weight: 700; color: #0f172a; }
.donut-center-text { font-size: 11px; color: #94a3b8; }
.cat-legend { display: flex; flex-direction: column; gap: 8px; margin-top: 6px; }
.cat-row    { display: flex; align-items: center; gap: 8px; }
.cat-dot    { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
.cat-name   { font-size: 12px; color: #64748b; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cat-bar-bg { width: 80px; height: 4px; background: #f1f5f9; border-radius: 2px; overflow: hidden; flex-shrink: 0; }
.cat-fill   { height: 100%; border-radius: 2px; }
.cat-pct    { font-size: 12px; font-weight: 700; color: #0f172a; min-width: 32px; text-align: right; }
.cat-note { font-size: 11px; color: #94a3b8; margin-top: 10px; line-height: 1.5; padding: 8px 10px; background: #f8fafc; border-radius: 10px; }

/* ── ACTIVITY FEED ────────────────────────────────────── */
.act-list { display: flex; flex-direction: column; }
.act-item {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 11px 0; border-bottom: 1px solid #f8fafc;
}
.act-item:last-child { border-bottom: none; }
.act-icon {
    width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 14px;
    margin-top: 1px;
}
.act-title  { font-size: 13px; font-weight: 600; color: #0f172a; line-height: 1.4; }
.act-detail { font-size: 11px; color: #94a3b8; margin-top: 2px; }
.act-time   { margin-left: auto; font-size: 12px; font-weight: 500; color: #94a3b8; white-space: nowrap; padding-top: 2px; }

/* ── STOCK TABLE ──────────────────────────────────────── */
.stock-table { width: 100%; border-collapse: collapse; }
.stock-table th {
    padding: 10px 14px; text-align: left;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .05em; color: #94a3b8;
    border-bottom: 1px solid #f1f5f9;
}
.stock-table td {
    padding: 13px 14px; font-size: 13px; color: #0f172a;
    border-bottom: 1px solid #f8fafc;
}
.stock-table tbody tr:last-child td { border-bottom: none; }
.stock-table tbody tr:hover td { background: #fafbff; }
.st-tag {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 9999px;
    font-size: 11px; font-weight: 700;
}
.st-ok    { background: #dcfce7; color: #166534; }
.st-watch { background: #fef3c7; color: #92400e; }
.st-alert { background: #fee2e2; color: #b91c1c; }
.alert-num { color: #e11d48; font-weight: 700; }

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 1200px) {
    .db-kpi-row { grid-template-columns: repeat(2, 1fr); }
    .db-row-2-1, .db-row-3 { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
    .db-kpi-row { grid-template-columns: 1fr 1fr; }
    .rev-summary { grid-template-columns: 1fr; }
}
</style>

<?php
/* ── PHP helpers ─────────────────────────────────────── */
$vi_days = ['Monday'=>'Thứ Hai','Tuesday'=>'Thứ Ba','Wednesday'=>'Thứ Tư',
            'Thursday'=>'Thứ Năm','Friday'=>'Thứ Sáu','Saturday'=>'Thứ Bảy','Sunday'=>'Chủ Nhật'];
$today_vi = $vi_days[date('l')] ?? date('l');

/* Tính tổng, max doanh thu 7 ngày */
$revVals   = array_column($revenue7days ?? [], 'val');
$revTotal  = array_sum($revVals);
$revMax    = max(array_merge([1], $revVals));
$revToday  = end($revVals);
$revYest   = count($revVals) >= 2 ? $revVals[count($revVals)-2] : 0;
$revDiff   = $revToday - $revYest;

/* Online / offline count */
$onlineCnt  = 0; $offlineCnt = 0;
foreach (($onlineUsers ?? []) as $u) {
    if (($u['status'] ?? '') === 'online') $onlineCnt++;
    else $offlineCnt++;
}
$onlineList = array_filter($onlineUsers ?? [], fn($u) => ($u['status'] ?? '') === 'online');
$onlineList = array_slice($onlineList, 0, 5);

/* Avatar colors */
$avColors = [
    ['bg'=>'#dbeafe','c'=>'#1d4ed8'],['bg'=>'#fce7f3','c'=>'#9d174d'],
    ['bg'=>'#d1fae5','c'=>'#065f46'],['bg'=>'#fef3c7','c'=>'#92400e'],
    ['bg'=>'#ede9fe','c'=>'#5b21b6'],
];

/* Category distribution total */
$catTotal = array_sum(array_column($categoryDistribution ?? [], 'count'));
$catColors = ['#2563eb','#16a34a','#f59e0b','#0ea5e9','#a855f7','#ec4899','#14b8a6'];

/* revenue change sign */
$revChangePct = $stats['rev_change_pct'] ?? 0;
?>

<div class="db-root">

<!-- ── TOP BAR ──────────────────────────────────── -->
<div class="db-topbar">
    <h1>Dashboard tổng quan</h1>
    <div class="db-topbar-meta">
        <span class="db-pill">
            <i class="fas fa-calendar-alt" style="color:#94a3b8;font-size:13px;"></i>
            <?= $today_vi ?>, <?= date('d/m/Y') ?> | Ca sáng
        </span>
        <?php if (($stats['expiring_soon_count'] ?? 0) > 0): ?>
        <span class="db-pill db-pill-warn">
            <i class="fas fa-exclamation-triangle" style="font-size:13px;"></i>
            <?= number_format($stats['expiring_soon_count']) ?> sản phẩm sắp hết hạn
        </span>
        <?php endif; ?>
    </div>
</div>

<!-- ── KPI 4 CARDS ──────────────────────────────── -->
<div class="db-kpi-row">

    <!-- Doanh thu -->
    <div class="kpi-card kpi-green">
        <div class="kpi-label">Doanh thu hôm nay</div>
        <div class="kpi-value"><?= number_format(($stats['revenue_today'] ?? 0)/1_000_000, 1) ?>M đ</div>
        <?php if ($revChangePct >= 0): ?>
            <div class="kpi-change up"><i class="fas fa-arrow-trend-up"></i> ↑ <?= abs($revChangePct) ?>% so hôm qua</div>
        <?php else: ?>
            <div class="kpi-change down"><i class="fas fa-arrow-trend-down"></i> ↓ <?= abs($revChangePct) ?>% so hôm qua</div>
        <?php endif; ?>
        <div class="kpi-badge badge-green">● Đang hoạt động</div>
    </div>

    <!-- Hóa đơn -->
    <div class="kpi-card kpi-blue">
        <div class="kpi-label">Hóa đơn hôm nay</div>
        <div class="kpi-value"><?= number_format($stats['total_invoices_today'] ?? 0) ?></div>
        <?php $diff = $stats['invoice_diff'] ?? 0; ?>
        <div class="kpi-change <?= $diff >= 0 ? 'up' : 'down' ?>">
            <?= $diff >= 0 ? '↑' : '↓' ?> <?= ($diff >= 0 ? '+' : '') . $diff ?> so hôm qua
        </div>
        <div class="kpi-badge badge-blue">● Bình thường</div>
    </div>

    <!-- Tồn kho -->
    <div class="kpi-card kpi-amber">
        <div class="kpi-label">Tồn kho (SKU)</div>
        <div class="kpi-value"><?= number_format($stats['total_skus'] ?? 0) ?></div>
        <div class="kpi-change neutral">
            <i class="fas fa-clock"></i>
            <?= number_format($stats['expiring_soon_count'] ?? 0) ?> sản phẩm sắp HSD
        </div>
        <div class="kpi-badge badge-amber">● Cần xem xét</div>
    </div>

    <!-- Phiếu hủy -->
    <div class="kpi-card kpi-rose">
        <div class="kpi-label">Phiếu hủy chờ duyệt</div>
        <div class="kpi-value"><?= number_format($stats['pending_cancellations'] ?? 0) ?></div>
        <div class="kpi-change neutral"><i class="fas fa-circle"></i> Chờ xử lý</div>
        <div class="kpi-badge badge-rose">● Chờ xử lý</div>
    </div>

</div>

<!-- ── ROW: Revenue + Online users ──────────────── -->
<div class="db-row db-row-2-1">

    <!-- Revenue chart -->
    <div class="panel">
        <div class="panel-head">
            <h3>Doanh thu 7 ngày gần nhất</h3>
            <a href="index.php?controller=bao_cao&action=doanh_thu">Xem chi tiết ↗</a>
        </div>

        <!-- Summary row -->
        <div class="rev-summary">
            <div class="rev-sum-item">
                <div class="rev-sum-label">Tổng 7 ngày</div>
                <div class="rev-sum-val"><?= number_format($revTotal, 1) ?>M đ</div>
            </div>
            <div class="rev-sum-item">
                <div class="rev-sum-label">Hôm nay</div>
                <div class="rev-sum-val"><?= number_format($revToday, 1) ?>M đ</div>
            </div>
            <div class="rev-sum-item">
                <div class="rev-sum-label">So hôm qua</div>
                <div class="rev-sum-val <?= $revDiff >= 0 ? 'positive' : 'negative' ?>">
                    <?= ($revDiff >= 0 ? '+' : '') . number_format($revDiff, 1) ?>M đ
                </div>
            </div>
        </div>

        <div class="rev-legend" style="padding-top:14px;">
            <div class="rev-legend-item">
                <div class="rev-legend-dot" style="background:#2563eb;"></div>
                Doanh thu (triệu đồng)
            </div>
            <div class="rev-legend-item">
                <div class="rev-legend-dot" style="background:#bfdbfe;"></div>
                Các ngày trước
            </div>
        </div>

        <div class="panel-body" style="padding-top:0;">
            <div class="chart-area">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Online users -->
    <div class="panel">
        <div class="panel-head">
            <h3>Người dùng trực tuyến</h3>
            <a href="index.php?controller=nhan_vien&action=index">Quản lý ↗</a>
        </div>

        <div class="online-counter">
            <div class="counter-bubble">
                <div class="counter-num c-green"><?= $onlineCnt ?></div>
                <div class="counter-label"><span class="live-dot"></span> Online</div>
            </div>
            <div class="counter-divider"></div>
            <div class="counter-bubble">
                <div class="counter-num c-gray"><?= $offlineCnt ?></div>
                <div class="counter-label">Offline</div>
            </div>
            <div class="counter-divider"></div>
            <div class="online-sub-label">
                Hiển thị tối đa<br>5 người online
            </div>
        </div>

        <div class="u-list">
            <?php
            if (empty($onlineList)):
            ?>
            <div style="padding:20px 22px; font-size:13px; color:#94a3b8; text-align:center;">
                <i class="fas fa-wifi-slash"></i> Chưa có người dùng trực tuyến
            </div>
            <?php
            else:
                foreach ($onlineList as $i => $u):
                    $av = $avColors[$i % count($avColors)];
                    $initials = mb_strtoupper(mb_substr($u['name'] ?? '?', 0, 2));
            ?>
            <div class="u-item">
                <div class="u-av" style="background:<?= $av['bg'] ?>;color:<?= $av['c'] ?>;">
                    <?= htmlspecialchars($initials) ?>
                </div>
                <div>
                    <div class="u-name"><?= htmlspecialchars($u['name'] ?? '') ?></div>
                    <div class="u-role"><?= htmlspecialchars($u['role'] ?? '') ?></div>
                </div>
                <span class="u-status st-online">Online</span>
            </div>
            <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>

</div>

<!-- ── ROW: Expiry + Donut + Activity ──────────── -->
<div class="db-row db-row-3">

    <!-- Hàng sắp hết hạn (tối đa 10) -->
    <div class="panel">
        <div class="panel-head">
            <h3>Hàng sắp hết hạn</h3>
            <a href="index.php?controller=kho&action=lo_hang">Chi tiết ↗</a>
        </div>
        <div class="panel-body" style="padding:0 22px;">
            <div class="exp-list">
                <?php
                $expList = array_slice($expiringItems ?? [], 0, 10);
                if (empty($expList)):
                ?>
                <div style="padding:20px 0; text-align:center; color:#94a3b8; font-size:13px;">
                    <i class="fas fa-check-circle" style="color:#16a34a;"></i> Không có hàng sắp hết hạn
                </div>
                <?php
                else:
                    foreach ($expList as $item):
                        $daysLeft = (int)ceil((strtotime($item['han_su_dung'] ?? 'now') - time()) / 86400);
                        $daysLeft = max(0, $daysLeft);
                        if ($daysLeft <= 1) {
                            $badgeCls = 'exp-1'; $dotColor = '#e11d48';
                            $dayLabel = $daysLeft <= 0 ? 'Hết hạn' : '1 ngày';
                        } elseif ($daysLeft <= 3) {
                            $badgeCls = 'exp-2'; $dotColor = '#f59e0b';
                            $dayLabel = "{$daysLeft} ngày";
                        } elseif ($daysLeft <= 5) {
                            $badgeCls = 'exp-3'; $dotColor = '#eab308';
                            $dayLabel = "{$daysLeft} ngày";
                        } else {
                            $badgeCls = 'exp-ok'; $dotColor = '#16a34a';
                            $dayLabel = "{$daysLeft} ngày";
                        }
                        $qty = ($item['so_luong_trong_kho'] ?? 0) + ($item['so_luong_tren_ke'] ?? 0);
                ?>
                <div class="exp-item">
                    <span class="exp-dot" style="background:<?= $dotColor ?>;"></span>
                    <div style="flex:1; min-width:0;">
                        <div class="exp-name" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($item['ten_hang_hoa'] ?? '') ?>">
                            <?= htmlspecialchars($item['ten_hang_hoa'] ?? '') ?>
                        </div>
                        <div class="exp-batch"><?= htmlspecialchars($item['ma_lo_hang'] ?? '') ?></div>
                    </div>
                    <div class="exp-qty"><?= number_format($qty) ?> <?= htmlspecialchars($item['don_vi_tinh'] ?? '') ?></div>
                    <span class="exp-badge <?= $badgeCls ?>"><?= $dayLabel ?></span>
                </div>
                <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>

    <!-- Tỷ trọng danh mục -->
    <div class="panel">
        <div class="panel-head">
            <h3>Tỷ trọng danh mục</h3>
            <span class="st-tag st-ok" style="font-size:11px;">SKU</span>
        </div>
        <div class="panel-body">
            <div class="donut-wrap">
                <canvas id="categoryChart"></canvas>
                <div class="donut-center-label">
                    <div class="donut-center-num"><?= $catTotal ?></div>
                    <div class="donut-center-text">sản phẩm</div>
                </div>
            </div>

            <div class="cat-legend">
                <?php
                $catDist = $categoryDistribution ?? [];
                foreach ($catDist as $ci => $cat):
                    $pct = $catTotal > 0 ? round($cat['count'] / $catTotal * 100) : 0;
                    $col = $catColors[$ci % count($catColors)];
                ?>
                <div class="cat-row">
                    <div class="cat-dot" style="background:<?= $col ?>;"></div>
                    <span class="cat-name" title="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></span>
                    <div class="cat-bar-bg">
                        <div class="cat-fill" style="width:<?= $pct ?>%;background:<?= $col ?>;"></div>
                    </div>
                    <span class="cat-pct"><?= $pct ?>%</span>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="cat-note">
                <i class="fas fa-info-circle" style="color:#94a3b8;"></i>
                Tỷ lệ tính theo số SKU đang kinh doanh trong mỗi danh mục. Số liệu cập nhật theo thời gian thực từ cơ sở dữ liệu.
            </div>
        </div>
    </div>

    <!-- Hoạt động gần đây (tối đa 5) -->
    <div class="panel">
        <div class="panel-head">
            <h3>Hoạt động gần đây</h3>
            <a href="index.php?controller=bao_cao&action=hoat_dong">Xem tất cả ↗</a>
        </div>
        <div class="panel-body" style="padding:0 22px;">
            <div class="act-list">
                <?php
                $actList = array_slice($activityItems ?? [], 0, 5);
                if (empty($actList)):
                ?>
                <div style="padding:20px 0; text-align:center; color:#94a3b8; font-size:13px;">
                    Chưa có hoạt động hôm nay
                </div>
                <?php
                else:
                    $actIconMap = [
                        'green' => ['bg'=>'#dcfce7','c'=>'#166534','icon'=>'fa-receipt'],
                        'amber' => ['bg'=>'#fef3c7','c'=>'#92400e','icon'=>'fa-truck'],
                        'red'   => ['bg'=>'#fee2e2','c'=>'#b91c1c','icon'=>'fa-ban'],
                        'blue'  => ['bg'=>'#dbeafe','c'=>'#1e40af','icon'=>'fa-tag'],
                        'gray'  => ['bg'=>'#f1f5f9','c'=>'#475569','icon'=>'fa-circle-info'],
                    ];
                    foreach ($actList as $act):
                        $ic = $actIconMap[$act['color'] ?? 'gray'] ?? $actIconMap['gray'];
                ?>
                <div class="act-item">
                    <div class="act-icon" style="background:<?= $ic['bg'] ?>;color:<?= $ic['c'] ?>;">
                        <i class="fas <?= $ic['icon'] ?>" style="font-size:13px;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="act-title" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($act['title']) ?>">
                            <?= htmlspecialchars($act['title']) ?>
                        </div>
                        <div class="act-detail"><?= htmlspecialchars($act['detail'] ?? '') ?></div>
                    </div>
                    <div class="act-time"><?= htmlspecialchars($act['time'] ?? '') ?></div>
                </div>
                <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>

</div>

<!-- ── TÌNH TRẠNG KHO THEO DANH MỤC (tối đa 5) ─ -->
<div class="panel" style="margin-bottom: 8px;">
    <div class="panel-head">
        <h3>Tình trạng kho theo danh mục</h3>
        <a href="index.php?controller=kho&action=index">Báo cáo tồn kho chi tiết ↗</a>
    </div>
    <div class="panel-body" style="padding:0;">
        <div style="overflow-x:auto;">
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Danh mục</th>
                        <th>Tổng SKU</th>
                        <th>Lô đang hoạt động</th>
                        <th>Sắp HSD (&le;7 ngày)</th>
                        <th>Giá trị tồn kho</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stockList = array_slice($stockCategories ?? [], 0, 5);
                    if (empty($stockList)):
                    ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:30px; color:#94a3b8; font-size:13px;">
                            Chưa có dữ liệu danh mục
                        </td>
                    </tr>
                    <?php
                    else:
                        foreach ($stockList as $row):
                            $stCls  = $row['status'] === 'ok' ? 'st-ok' : ($row['status'] === 'watch' ? 'st-watch' : 'st-alert');
                            $stDot  = $row['status'] === 'ok' ? '#16a34a' : ($row['status'] === 'watch' ? '#d97706' : '#e11d48');
                            $stTxt  = $row['status'] === 'ok' ? 'Bình thường' : ($row['status'] === 'watch' ? 'Theo dõi' : 'Cần xử lý');
                            $alertCls = ($row['exp'] ?? 0) > 0 ? 'alert-num' : '';
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td><?= number_format($row['sku']) ?></td>
                        <td><?= number_format($row['lots']) ?> lô</td>
                        <td class="<?= $alertCls ?>"><?= htmlspecialchars($row['alert']) ?></td>
                        <td><?= htmlspecialchars($row['value']) ?></td>
                        <td>
                            <span class="st-tag <?= $stCls ?>">
                                <span style="width:6px;height:6px;border-radius:50%;background:<?= $stDot ?>;display:inline-block;"></span>
                                <?= $stTxt ?>
                            </span>
                        </td>
                    </tr>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div><!-- end .db-root -->

<!-- ── CHARTS ─────────────────────────────────────────── -->
<script>
(function(){
    var ctx = document.getElementById('revenueChart').getContext('2d');
    var labels = <?= json_encode(array_column($revenue7days ?? [], 'label')) ?>;
    var values = <?= json_encode(array_column($revenue7days ?? [], 'val')) ?>;
    var todayIdx = values.length - 1;
    var bgColors = values.map(function(_,i){
        return i === todayIdx ? '#2563eb' : '#bfdbfe';
    });

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    data: values,
                    backgroundColor: bgColors,
                    borderRadius: 10,
                    borderSkipped: false,
                    maxBarThickness: 36,
                    order: 2
                },
                {
                    type: 'line',
                    data: values,
                    borderColor: '#2563eb',
                    borderWidth: 2,
                    pointBackgroundColor: values.map(function(_,i){ return i===todayIdx?'#2563eb':'#93c5fd'; }),
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    tension: 0.4,
                    fill: false,
                    order: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#64748b',
                    bodyColor: '#0f172a',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: function(c){ return ' ' + c.parsed.y.toFixed(1) + ' triệu đ'; }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { size: 12 } }
                },
                y: {
                    grid: { color: '#f1f5f9', lineWidth: 1 },
                    ticks: { color: '#94a3b8', font: { size: 12 }, callback: function(v){ return v + 'M'; } },
                    beginAtZero: true
                }
            }
        }
    });
})();

(function(){
    var ctx = document.getElementById('categoryChart').getContext('2d');
    var raw   = <?= json_encode($categoryDistribution ?? []) ?>;
    var total = <?= $catTotal ?: 1 ?>;
    var colors= <?= json_encode($catColors) ?>;
    var labels= raw.map(function(d){ return d.name; });
    var data  = raw.map(function(d){ return parseInt(d.count); });

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#64748b',
                    bodyColor: '#0f172a',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: function(c){
                            var pct = Math.round(c.parsed / total * 100);
                            return ' ' + c.parsed + ' SKU (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
})();
</script>