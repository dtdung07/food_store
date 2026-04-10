<?php
/* ═══════════════════════════════════════════════
   SETUP
═══════════════════════════════════════════════ */
$isEdit  = ($_GET['action'] ?? '') === 'edit';
$loHangs = $loHangs ?? [];

function hf_v($row, $k, $d = '') { return htmlspecialchars(($row[$k] ?? $d), ENT_QUOTES); }

$curCode = hf_v($hangHoa, 'ma_hang_hoa');
$curName = hf_v($hangHoa, 'ten_hang_hoa');
$curDVT  = $hangHoa['don_vi_tinh'] ?? '';
$curGia  = $hangHoa['gia_ban']     ?? '';
$curVach = hf_v($hangHoa, 'ma_vach');
$curTT   = $hangHoa['trang_thai']  ?? 'DANG_KINH_DOANH';
$curDM   = $hangHoa['ma_danh_muc']     ?? '';
$curNCC  = $hangHoa['ma_nha_cung_cap'] ?? '';
$curTon  = (int)($hangHoa['ton_kho']   ?? 0);

$donViList = ['kg','g','Lít','ml','Hộp','Gói','Thùng','Cái','Chai','Bịch','Túi','gói','chai','cốc','hộp'];
$donViList = array_unique(array_map('mb_strtolower', $donViList));
sort($donViList);

// tính ngày còn lại của lô
function daysLeft(string $han): int {
    return max(0, (int)ceil((strtotime($han) - time()) / 86400));
}
function lotBadgeClass(int $days): string {
    if ($days <= 3)  return 'lb-red';
    if ($days <= 7)  return 'lb-amber';
    if ($days <= 30) return 'lb-yellow';
    return 'lb-green';
}
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

.hf-root { font-family:'Inter',sans-serif; color:#0f172a; max-width:920px; }

/* ── Head ── */
.hf-head { display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:22px;flex-wrap:wrap; }
.hf-head h2 { margin:0 0 4px;font-size:20px;font-weight:700; }
.hf-head p  { margin:0;font-size:13px;color:#94a3b8; }
.hf-btn-back {
    display:inline-flex;align-items:center;gap:7px;
    font-family:'Inter',sans-serif;font-size:13px;font-weight:500;
    padding:8px 16px;border-radius:10px;
    border:1px solid #e2e8f0;background:#fff;color:#64748b;
    text-decoration:none;transition:all .15s;white-space:nowrap;
}
.hf-btn-back:hover { background:#f8fafc;color:#0f172a; }

/* ── Alert ── */
.hf-alert {
    display:flex;align-items:center;gap:10px;
    padding:12px 16px;border-radius:10px;margin-bottom:18px;font-size:13px;
}
.hf-alert.err { background:#fef2f2;border:1px solid #fecaca;color:#991b1b; }

/* ── Card ── */
.hf-card { background:#fff;border:1px solid #e8edf4;border-radius:16px;overflow:hidden;margin-bottom:16px; }
.hf-card-head { display:flex;align-items:center;gap:10px;padding:14px 22px;border-bottom:1px solid #f1f5f9; }
.hf-sec-ico { width:32px;height:32px;border-radius:8px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:14px; }
.hf-card-head h3 { margin:0;font-size:14px;font-weight:600;color:#0f172a; }
.hf-card-head p  { margin:0;font-size:12px;color:#94a3b8; }
.hf-card-body    { padding:20px 22px; }

/* ── Grid ── */
.hf-g2 { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
.hf-g3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px; }
.hf-g4 { display:grid;grid-template-columns:repeat(4,1fr);gap:16px; }

/* ── Field ── */
.hf-field { display:flex;flex-direction:column;gap:5px; }
.hf-label { font-size:12px;font-weight:600;color:#374151;display:flex;align-items:center;gap:5px; }
.hf-label .req  { color:#dc2626; }
.hf-label .hint { font-weight:400;color:#94a3b8;font-size:11px; }
.hf-input,.hf-select {
    font-family:'Inter',sans-serif;font-size:13px;color:#0f172a;
    padding:9px 12px;border:1px solid #e2e8f0;border-radius:10px;
    background:#f8fafc;outline:none;transition:all .15s;box-sizing:border-box;width:100%;
}
.hf-input:focus,.hf-select:focus {
    border-color:#2563eb;background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,.1);
}
.hf-input::placeholder { color:#cbd5e1; }
.hf-hint { font-size:11px;color:#94a3b8; }
.mono-input { font-family:'DM Mono',monospace;text-transform:uppercase;letter-spacing:.05em; }

/* price group */
.hf-price-group { display:flex; }
.hf-price-group .hf-input { border-radius:10px 0 0 10px;border-right:none; }
.hf-price-suffix {
    display:flex;align-items:center;padding:9px 12px;
    background:#f1f5f9;border:1px solid #e2e8f0;border-radius:0 10px 10px 0;
    font-size:13px;color:#64748b;white-space:nowrap;
}

/* status toggle */
.hf-status-row { display:flex;gap:8px; }
.hf-status-opt {
    flex:1;padding:10px 12px;border-radius:10px;cursor:pointer;
    border:1.5px solid #e2e8f0;background:#f8fafc;
    display:flex;align-items:center;gap:8px;
    font-size:13px;font-weight:500;color:#64748b;transition:all .15s;user-select:none;
}
.hf-status-opt input[type=radio] { display:none; }
.hf-status-opt.s-on  { border-color:#16a34a;background:#f0fdf4;color:#166534;font-weight:600; }
.hf-status-opt.s-off { border-color:#64748b;background:#f8fafc;color:#64748b;font-weight:600; }
.hf-sdot { width:8px;height:8px;border-radius:50%;flex-shrink:0; }

/* quick info (edit) */
.hf-qinfo { display:grid;grid-template-columns:repeat(4,1fr);border-top:1px solid #f1f5f9; }
.hf-qi { padding:14px 22px;border-right:1px solid #f1f5f9; }
.hf-qi:last-child { border-right:none; }
.hf-qi-lbl { font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px; }
.hf-qi-val { font-size:16px;font-weight:700;font-family:'DM Mono',monospace;color:#0f172a; }
.hf-qi-val.green { color:#059669; }
.hf-qi-val.amber { color:#d97706; }
.hf-qi-val.red   { color:#dc2626; }
.hf-note { display:flex;align-items:flex-start;gap:8px;padding:12px 22px;background:#fffbeb;border-top:1px solid #fef3c7;font-size:12px;color:#92400e; }

/* ══════════════════════════════════════════════
   LÔ HÀNG TABLE
══════════════════════════════════════════════ */
.lot-table { width:100%;border-collapse:collapse;min-width:620px; }
.lot-table thead th {
    padding:10px 14px;text-align:left;
    font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;
    color:#94a3b8;background:#f8fafc;border-bottom:1px solid #f1f5f9;white-space:nowrap;
}
.lot-table tbody td { padding:10px 14px;border-bottom:1px solid #f8fafc;font-size:13px;vertical-align:middle; }
.lot-table tbody tr:last-child td { border-bottom:none; }
.lot-table tbody tr:hover td { background:#fafbff; }

.lot-code { font-family:'DM Mono',monospace;font-size:11px;font-weight:500;color:#4338ca;background:#ede9fe;padding:3px 8px;border-radius:6px; }
.lot-date { font-family:'DM Mono',monospace;font-size:12px;color:#64748b; }

/* days badge */
.lb { display:inline-block;font-size:11px;font-weight:700;padding:3px 9px;border-radius:9999px;font-family:'DM Mono',monospace; }
.lb-red    { background:#fee2e2;color:#b91c1c; }
.lb-amber  { background:#fef3c7;color:#92400e; }
.lb-yellow { background:#fefce8;color:#854d0e; }
.lb-green  { background:#dcfce7;color:#166534; }

/* qty inputs inside table */
.lot-qty-wrap { display:flex;align-items:center;gap:6px; }
.lot-qty-btn {
    width:26px;height:26px;border-radius:7px;border:1px solid #e2e8f0;
    background:#fff;cursor:pointer;font-size:14px;font-weight:700;
    display:flex;align-items:center;justify-content:center;
    color:#64748b;transition:all .15s;flex-shrink:0;line-height:1;
}
.lot-qty-btn:hover { background:#f1f5f9;border-color:#94a3b8; }
.lot-qty-input {
    width:72px;text-align:center;
    font-family:'DM Mono',monospace;font-size:13px;font-weight:500;
    padding:5px 8px;border:1px solid #e2e8f0;border-radius:8px;
    background:#f8fafc;outline:none;transition:all .15s;
}
.lot-qty-input:focus { border-color:#2563eb;background:#fff;box-shadow:0 0 0 2px rgba(37,99,235,.1); }
.lot-qty-total {
    font-family:'DM Mono',monospace;font-size:12px;font-weight:600;
    padding:4px 10px;border-radius:8px;background:#eff6ff;color:#1d4ed8;
    white-space:nowrap;
}

/* empty lot state */
.lot-empty { text-align:center;padding:32px;color:#94a3b8;font-size:13px; }
.lot-empty i { font-size:28px;color:#e2e8f0;display:block;margin-bottom:8px; }

/* lot summary bar */
.lot-summary {
    display:flex;align-items:center;gap:16px;flex-wrap:wrap;
    padding:10px 22px;background:#f8fafc;border-top:1px solid #f1f5f9;font-size:12px;color:#64748b;
}
.lot-sum-item { display:flex;align-items:center;gap:5px; }
.lot-sum-item strong { color:#0f172a;font-family:'DM Mono',monospace; }

/* ── Actions ── */
.hf-actions { display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:16px 22px;background:#f8fafc;border-top:1px solid #f1f5f9; }
.hf-btn-cancel {
    display:inline-flex;align-items:center;gap:7px;
    font-family:'Inter',sans-serif;font-size:13px;font-weight:500;
    padding:9px 18px;border-radius:10px;
    border:1px solid #e2e8f0;background:#fff;color:#64748b;
    text-decoration:none;cursor:pointer;transition:all .15s;
}
.hf-btn-cancel:hover { background:#f8fafc;color:#0f172a; }
.hf-btn-save {
    display:inline-flex;align-items:center;gap:8px;
    font-family:'Inter',sans-serif;font-size:13px;font-weight:600;
    padding:9px 24px;border-radius:10px;border:none;cursor:pointer;
    background:#2563eb;color:#fff;transition:background .15s;
}
.hf-btn-save:hover { background:#1d4ed8; }

@media (max-width:700px) {
    .hf-g2,.hf-g3,.hf-g4 { grid-template-columns:1fr; }
    .hf-qinfo { grid-template-columns:1fr 1fr; }
    .hf-qi:nth-child(2n) { border-right:none; }
    .hf-qi:nth-child(n+3) { border-top:1px solid #f1f5f9; }
}
</style>

<div class="hf-root">

<!-- PAGE HEADER -->
<div class="hf-head">
    <div>
        <h2>
            <i class="fas <?= $isEdit ? 'fa-pencil-alt' : 'fa-plus-circle' ?>"
               style="color:#2563eb;font-size:17px;vertical-align:-1px;margin-right:8px;"></i>
            <?= $isEdit ? 'Chỉnh sửa hàng hóa' : 'Thêm hàng hóa mới' ?>
        </h2>
        <p><?= $isEdit
            ? 'Cập nhật thông tin sản phẩm, giá bán, mã vạch và số lượng từng lô hàng'
            : 'Nhập đầy đủ thông tin để thêm sản phẩm mới vào hệ thống' ?></p>
    </div>
    <a href="index.php?controller=hang_hoa&action=index" class="hf-btn-back">
        <i class="fas fa-arrow-left" style="font-size:11px;"></i> Quay lại
    </a>
</div>

<!-- ERROR -->
<?php if (!empty($error)): ?>
<div class="hf-alert err">
    <i class="fas fa-exclamation-circle"></i>
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" autocomplete="off">

<!-- ══════════════════════════════════════════
     CARD 1 – Định danh
══════════════════════════════════════════ -->
<div class="hf-card">
    <div class="hf-card-head">
        <div class="hf-sec-ico" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-id-badge"></i></div>
        <div>
            <h3>Thông tin định danh</h3>
            <p>Mã hàng và tên sản phẩm</p>
        </div>
    </div>
    <div class="hf-card-body">
        <div class="hf-g2">
            <div class="hf-field">
                <label class="hf-label">
                    Mã hàng hóa <span class="req">*</span>
                    <?php if ($isEdit): ?>
                    <span class="hint">· Thay đổi sẽ cập nhật toàn bộ liên kết</span>
                    <?php else: ?>
                    <span class="hint">· Tự động viết hoa</span>
                    <?php endif; ?>
                </label>
                <input type="text" name="ma_hang_hoa" class="hf-input mono-input"
                       required
                       placeholder="VD: HH001, SP-MILK-01"
                       value="<?= $curCode ?>"
                       oninput="this.value=this.value.toUpperCase().replace(/\s/g,'')">
                <span class="hf-hint">Chữ in hoa, không dấu, không khoảng trắng</span>
            </div>
            <div class="hf-field">
                <label class="hf-label">Tên hàng hóa <span class="req">*</span></label>
                <input type="text" name="ten_hang_hoa" class="hf-input"
                       required
                       placeholder="VD: Sữa tươi Vinamilk 1L"
                       value="<?= $curName ?>">
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     CARD 2 – Kinh doanh
══════════════════════════════════════════ -->
<div class="hf-card">
    <div class="hf-card-head">
        <div class="hf-sec-ico" style="background:#f0fdf4;color:#16a34a;"><i class="fas fa-tags"></i></div>
        <div>
            <h3>Thông tin kinh doanh</h3>
            <p>Đơn vị tính, giá bán, mã vạch và trạng thái</p>
        </div>
    </div>
    <div class="hf-card-body">
        <div class="hf-g4">
            <!-- Đơn vị tính -->
            <div class="hf-field">
                <label class="hf-label">Đơn vị tính <span class="req">*</span></label>
                <select name="don_vi_tinh" class="hf-select" required>
                    <option value="">-- Chọn --</option>
                    <?php foreach ($donViList as $dv): ?>
                    <option value="<?= htmlspecialchars($dv) ?>"
                            <?= mb_strtolower($curDVT) === mb_strtolower($dv) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dv) ?>
                    </option>
                    <?php endforeach; ?>
                    <?php if ($curDVT !== '' && !in_array(mb_strtolower($curDVT), array_map('mb_strtolower', $donViList))): ?>
                    <option value="<?= htmlspecialchars($curDVT) ?>" selected><?= htmlspecialchars($curDVT) ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Giá bán -->
            <div class="hf-field">
                <label class="hf-label">Giá bán <span class="req">*</span></label>
                <div class="hf-price-group">
                    <input type="number" name="gia_ban" id="gia_ban_input"
                           class="hf-input"
                           required min="0" step="500"
                           placeholder="0"
                           value="<?= htmlspecialchars((string)$curGia) ?>"
                           oninput="updatePriceHint(this.value)">
                    <span class="hf-price-suffix">đ</span>
                </div>
                <span class="hf-hint" id="price-hint">
                    <?php if ($curGia !== ''): ?>≈ <?= number_format((float)$curGia) ?> đồng<?php endif; ?>
                </span>
            </div>

            <!-- Mã vạch -->
            <div class="hf-field">
                <label class="hf-label">Mã vạch <span class="hint">· Không bắt buộc</span></label>
                <input type="text" name="ma_vach" class="hf-input mono-input"
                       placeholder="8 hoặc 13 chữ số"
                       value="<?= $curVach ?>">
            </div>

            <!-- Trạng thái -->
            <div class="hf-field">
                <label class="hf-label">Trạng thái</label>
                <div class="hf-status-row">
                    <label class="hf-status-opt <?= $curTT === 'DANG_KINH_DOANH' ? 's-on' : '' ?>"
                           id="opt-on" onclick="toggleStatus('DANG_KINH_DOANH')">
                        <input type="radio" name="trang_thai" value="DANG_KINH_DOANH"
                               <?= $curTT === 'DANG_KINH_DOANH' ? 'checked' : '' ?>>
                        <span class="hf-sdot" style="background:#16a34a;"></span> Đang KD
                    </label>
                    <label class="hf-status-opt <?= $curTT === 'NGUNG_KINH_DOANH' ? 's-off' : '' ?>"
                           id="opt-off" onclick="toggleStatus('NGUNG_KINH_DOANH')">
                        <input type="radio" name="trang_thai" value="NGUNG_KINH_DOANH"
                               <?= $curTT === 'NGUNG_KINH_DOANH' ? 'checked' : '' ?>>
                        <span class="hf-sdot" style="background:#94a3b8;"></span> Ngừng KD
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     CARD 3 – Phân loại
══════════════════════════════════════════ -->
<div class="hf-card">
    <div class="hf-card-head">
        <div class="hf-sec-ico" style="background:#fef3c7;color:#d97706;"><i class="fas fa-folder-open"></i></div>
        <div>
            <h3>Phân loại &amp; Nhà cung cấp</h3>
            <p>Danh mục và đơn vị cung ứng</p>
        </div>
    </div>
    <div class="hf-card-body">
        <div class="hf-g2">
            <div class="hf-field">
                <label class="hf-label">
                    <i class="fas fa-folder" style="color:#d97706;font-size:11px;"></i>
                    Danh mục <span class="hint">· Không bắt buộc</span>
                </label>
                <select name="ma_danh_muc" class="hf-select">
                    <option value="">-- Không thuộc danh mục --</option>
                    <?php foreach ($danhMucs as $dm): ?>
                    <option value="<?= htmlspecialchars($dm['ma_danh_muc']) ?>"
                            <?= $curDM == $dm['ma_danh_muc'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="hf-field">
                <label class="hf-label">
                    <i class="fas fa-truck" style="color:#2563eb;font-size:11px;"></i>
                    Nhà cung cấp <span class="hint">· Không bắt buộc</span>
                </label>
                <select name="ma_nha_cung_cap" class="hf-select">
                    <option value="">-- Chưa có nhà cung cấp --</option>
                    <?php foreach ($nhaCungCaps as $ncc): ?>
                    <option value="<?= htmlspecialchars($ncc['ma_nha_cung_cap']) ?>"
                            <?= $curNCC == $ncc['ma_nha_cung_cap'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ncc['ten_nha_cung_cap']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     CARD 4 – Lô hàng & số lượng (chỉ edit)
══════════════════════════════════════════ -->
<?php if ($isEdit): ?>
<div class="hf-card">
    <div class="hf-card-head">
        <div class="hf-sec-ico" style="background:#ede9fe;color:#6366f1;"><i class="fas fa-layer-group"></i></div>
        <div>
            <h3>Số lượng tồn kho theo lô</h3>
            <p>Chỉnh trực tiếp số lượng trong kho và trên kệ của từng lô hàng</p>
        </div>
        <div style="margin-left:auto;">
            <span style="font-size:12px;color:#94a3b8;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:4px 10px;">
                <?= count($loHangs) ?> lô hàng
            </span>
        </div>
    </div>

    <?php if (empty($loHangs)): ?>
    <div class="lot-empty">
        <i class="fas fa-inbox"></i>
        Chưa có lô hàng nào cho sản phẩm này.<br>
        <small>Vào <strong>Phiếu nhập hàng</strong> để nhập lô đầu tiên.</small>
    </div>
    <?php else: ?>

    <?php
    $tongKho = array_sum(array_column($loHangs, 'so_luong_trong_kho'));
    $tongKe  = array_sum(array_column($loHangs, 'so_luong_tren_ke'));
    $tongAll = $tongKho + $tongKe;
    ?>

    <!-- Summary bar -->
    <div class="lot-summary">
        <div class="lot-sum-item">
            <i class="fas fa-warehouse" style="color:#6366f1;"></i>
            Kho: <strong id="sum-kho"><?= number_format($tongKho) ?></strong>
        </div>
        <div class="lot-sum-item">
            <i class="fas fa-store" style="color:#16a34a;"></i>
            Trên kệ: <strong id="sum-ke"><?= number_format($tongKe) ?></strong>
        </div>
        <div class="lot-sum-item" style="margin-left:auto;">
            <i class="fas fa-boxes" style="color:#d97706;"></i>
            Tổng tồn kho: <strong id="sum-total" style="color:#d97706;"><?= number_format($tongAll) ?></strong>
            <span style="color:#94a3b8;"><?= htmlspecialchars($curDVT) ?></span>
        </div>
    </div>

    <div style="overflow-x:auto;">
    <table class="lot-table">
        <thead>
            <tr>
                <th>Mã lô</th>
                <th>Ngày SX</th>
                <th>Hạn SD</th>
                <th>Còn lại</th>
                <th>
                    <i class="fas fa-warehouse" style="margin-right:4px;color:#6366f1;"></i>
                    Trong kho
                </th>
                <th>
                    <i class="fas fa-store" style="margin-right:4px;color:#16a34a;"></i>
                    Trên kệ
                </th>
                <th>Tổng lô</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($loHangs as $lot):
            $days    = daysLeft($lot['han_su_dung']);
            $badgeCls = lotBadgeClass($days);
            $maLo    = htmlspecialchars($lot['ma_lo_hang']);
            $slKho   = (int)$lot['so_luong_trong_kho'];
            $slKe    = (int)$lot['so_luong_tren_ke'];
            $slTong  = $slKho + $slKe;
        ?>
        <tr data-lot="<?= $maLo ?>">
            <td><span class="lot-code"><?= $maLo ?></span></td>
            <td class="lot-date"><?= date('d/m/Y', strtotime($lot['ngay_san_xuat'])) ?></td>
            <td class="lot-date"><?= date('d/m/Y', strtotime($lot['han_su_dung'])) ?></td>
            <td>
                <span class="lb <?= $badgeCls ?>">
                    <?= $days > 0 ? $days.'n' : 'HSD' ?>
                </span>
            </td>

            <!-- Số lượng trong kho -->
            <td>
                <div class="lot-qty-wrap">
                    <button type="button" class="lot-qty-btn"
                            onclick="adjustQty('kho_<?= $lot['ma_lo_hang'] ?>', -1)">−</button>
                    <input type="number"
                           name="lo[<?= $lot['ma_lo_hang'] ?>][so_luong_trong_kho]"
                           id="kho_<?= $lot['ma_lo_hang'] ?>"
                           class="lot-qty-input"
                           value="<?= $slKho ?>"
                           min="0"
                           oninput="recalcTotal('<?= $lot['ma_lo_hang'] ?>')">
                    <button type="button" class="lot-qty-btn"
                            onclick="adjustQty('kho_<?= $lot['ma_lo_hang'] ?>', 1)">+</button>
                </div>
            </td>

            <!-- Số lượng trên kệ -->
            <td>
                <div class="lot-qty-wrap">
                    <button type="button" class="lot-qty-btn"
                            onclick="adjustQty('ke_<?= $lot['ma_lo_hang'] ?>', -1)">−</button>
                    <input type="number"
                           name="lo[<?= $lot['ma_lo_hang'] ?>][so_luong_tren_ke]"
                           id="ke_<?= $lot['ma_lo_hang'] ?>"
                           class="lot-qty-input"
                           value="<?= $slKe ?>"
                           min="0"
                           oninput="recalcTotal('<?= $lot['ma_lo_hang'] ?>')">
                    <button type="button" class="lot-qty-btn"
                            onclick="adjustQty('ke_<?= $lot['ma_lo_hang'] ?>', 1)">+</button>
                </div>
            </td>

            <!-- Tổng lô -->
            <td>
                <span class="lot-qty-total" id="total_<?= $lot['ma_lo_hang'] ?>">
                    <?= number_format($slTong) ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>

    <!-- Quick info 4 ô -->
    <div class="hf-qinfo">
        <?php
        $tkClass = $curTon > 10 ? 'green' : ($curTon > 0 ? 'amber' : 'red');
        $tkLabel = $curTon > 0 ? number_format($curTon).' '.htmlspecialchars($curDVT) : 'Hết hàng';
        ?>
        <div class="hf-qi">
            <div class="hf-qi-lbl">Tồn kho</div>
            <div class="hf-qi-val <?= $tkClass ?>" id="qi-total"><?= $tkLabel ?></div>
        </div>
        <div class="hf-qi">
            <div class="hf-qi-lbl">Giá hiện tại</div>
            <div class="hf-qi-val green"><?= $curGia !== '' ? number_format((float)$curGia).'đ' : '—' ?></div>
        </div>
        <div class="hf-qi">
            <div class="hf-qi-lbl">Số lô hàng</div>
            <div class="hf-qi-val"><?= count($loHangs) ?></div>
        </div>
        <div class="hf-qi">
            <div class="hf-qi-lbl">Trạng thái</div>
            <div class="hf-qi-val <?= $curTT==='DANG_KINH_DOANH' ? 'green' : '' ?>">
                <?= $curTT==='DANG_KINH_DOANH' ? '● Đang KD' : '○ Ngừng KD' ?>
            </div>
        </div>
    </div>
    <div class="hf-note">
        <i class="fas fa-info-circle" style="margin-top:1px;flex-shrink:0;"></i>
        <span>Thay đổi số lượng sẽ được lưu ngay khi bấm <strong>Lưu thay đổi</strong>. Để nhập thêm hàng mới, hãy tạo phiếu nhập hàng.</span>
    </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════
     ACTIONS
══════════════════════════════════════════ -->
<div class="hf-card">
    <div class="hf-actions">
        <a href="index.php?controller=hang_hoa&action=index" class="hf-btn-cancel">
            <i class="fas fa-times" style="font-size:11px;"></i> Hủy
        </a>
        <button type="submit" class="hf-btn-save">
            <i class="fas fa-save" style="font-size:12px;"></i>
            <?= $isEdit ? 'Lưu thay đổi' : 'Thêm hàng hóa' ?>
        </button>
    </div>
</div>

</form>
</div><!-- .hf-root -->

<script>
/* ── Trạng thái toggle ── */
function toggleStatus(val) {
    document.getElementById('opt-on').classList.toggle('s-on', val === 'DANG_KINH_DOANH');
    document.getElementById('opt-on').classList.remove('s-off');
    document.getElementById('opt-off').classList.toggle('s-off', val === 'NGUNG_KINH_DOANH');
    document.getElementById('opt-off').classList.remove('s-on');
    document.querySelector('input[name="trang_thai"][value="'+val+'"]').checked = true;
}

/* ── Giá hint ── */
function updatePriceHint(raw) {
    var v = parseFloat(raw);
    document.getElementById('price-hint').textContent =
        (raw !== '' && !isNaN(v) && v >= 0) ? '≈ ' + v.toLocaleString('vi-VN') + ' đồng' : '';
}
(function(){
    var inp = document.getElementById('gia_ban_input');
    if (inp && inp.value) updatePriceHint(inp.value);
})();

/* ── Lot quantity helpers ── */
function adjustQty(inputId, delta) {
    var inp = document.getElementById(inputId);
    if (!inp) return;
    var v = parseInt(inp.value) || 0;
    inp.value = Math.max(0, v + delta);
    // Trigger recalc – extract lot key from inputId (kho_LOTKEY or ke_LOTKEY)
    var parts = inputId.split('_');
    parts.shift(); // remove 'kho' or 'ke'
    recalcTotal(parts.join('_'));
}

function recalcTotal(lotKey) {
    var khoInp = document.getElementById('kho_' + lotKey);
    var keInp  = document.getElementById('ke_'  + lotKey);
    var totEl  = document.getElementById('total_' + lotKey);
    if (!khoInp || !keInp || !totEl) return;

    var kho = Math.max(0, parseInt(khoInp.value) || 0);
    var ke  = Math.max(0, parseInt(keInp.value)  || 0);
    totEl.textContent = (kho + ke).toLocaleString('vi-VN');

    recalcSummary();
}

function recalcSummary() {
    var khoInputs = document.querySelectorAll('[name^="lo["][name$="[so_luong_trong_kho]"]');
    var keInputs  = document.querySelectorAll('[name^="lo["][name$="[so_luong_tren_ke]"]');

    var totalKho = 0, totalKe = 0;
    khoInputs.forEach(function(el){ totalKho += Math.max(0, parseInt(el.value) || 0); });
    keInputs.forEach(function(el) { totalKe  += Math.max(0, parseInt(el.value) || 0); });

    var skho = document.getElementById('sum-kho');
    var ske  = document.getElementById('sum-ke');
    var stot = document.getElementById('sum-total');
    if (skho) skho.textContent = totalKho.toLocaleString('vi-VN');
    if (ske)  ske.textContent  = totalKe.toLocaleString('vi-VN');
    if (stot) stot.textContent = (totalKho + totalKe).toLocaleString('vi-VN');
}
</script>