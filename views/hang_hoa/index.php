<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

.hh-root { font-family: 'Inter', sans-serif; color: #0f172a; }

.hh-page-head {
    display: flex; align-items: flex-end; justify-content: space-between;
    flex-wrap: wrap; gap: 12px; margin-bottom: 22px;
}
.hh-page-head h2 { margin: 0 0 3px; font-size: 20px; font-weight: 700; color: #0f172a; }
.hh-page-head p  { margin: 0; font-size: 13px; color: #94a3b8; }
.hh-btn-add {
    display: inline-flex; align-items: center; gap: 7px;
    font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 600;
    padding: 9px 18px; border-radius: 10px; border: none; cursor: pointer;
    background: #2563eb; color: #fff; text-decoration: none; transition: background .15s; white-space: nowrap;
}
.hh-btn-add:hover { background: #1d4ed8; color: #fff; }

.hh-stats {
    display: grid; grid-template-columns: repeat(4, minmax(0,1fr));
    gap: 14px; margin-bottom: 20px;
}
.hh-stat {
    background: #fff; border: 1px solid #e8edf4; border-radius: 14px;
    padding: 16px 18px; display: flex; align-items: center; gap: 12px;
    position: relative; overflow: hidden; transition: box-shadow .2s;
}
.hh-stat:hover { box-shadow: 0 6px 20px rgba(0,0,0,.08); }
.hh-stat::after {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; border-radius: 14px 14px 0 0;
}
.hh-stat.s-total::after   { background: #6366f1; }
.hh-stat.s-instock::after { background: #16a34a; }
.hh-stat.s-low::after     { background: #d97706; }
.hh-stat.s-out::after     { background: #dc2626; }
.hh-stat-ico {
    width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 17px;
}
.ico-purple { background: #ede9fe; color: #6366f1; }
.ico-green  { background: #dcfce7; color: #16a34a; }
.ico-amber  { background: #fef3c7; color: #d97706; }
.ico-red    { background: #fee2e2; color: #dc2626; }
.hh-stat-num { font-family: 'DM Mono', monospace; font-size: 22px; font-weight: 500; color: #0f172a; }
.hh-stat-lbl { font-size: 11px; color: #94a3b8; font-weight: 500; margin-top: 2px; }

.hh-toolbar { display: flex; gap: 10px; margin-bottom: 14px; flex-wrap: wrap; align-items: center; }
.hh-search-box {
    flex: 1; min-width: 200px;
    display: flex; align-items: center; gap: 8px;
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
    padding: 0 14px; transition: border-color .15s, box-shadow .15s;
}
.hh-search-box:focus-within { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
.hh-search-box i { color: #94a3b8; font-size: 13px; flex-shrink: 0; }
.hh-search-box input {
    border: none; outline: none; padding: 10px 0; width: 100%;
    font-family: 'Inter', sans-serif; font-size: 13px; color: #0f172a; background: transparent;
}
.hh-select {
    font-family: 'Inter', sans-serif; font-size: 13px; color: #0f172a;
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
    padding: 9px 12px; outline: none; cursor: pointer;
}
.hh-select:focus { border-color: #2563eb; }
.hh-btn-search {
    display: inline-flex; align-items: center; gap: 6px;
    font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 600;
    padding: 9px 16px; border-radius: 10px; border: none; cursor: pointer;
    background: #2563eb; color: #fff; transition: background .15s;
}
.hh-btn-search:hover { background: #1d4ed8; }
.hh-btn-reset {
    display: inline-flex; align-items: center; gap: 6px;
    font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 500;
    padding: 9px 14px; border-radius: 10px; cursor: pointer;
    border: 1px solid #e2e8f0; background: #fff; color: #64748b; text-decoration: none; transition: background .15s;
}
.hh-btn-reset:hover { background: #f8fafc; color: #0f172a; }

.hh-fpills { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px; }
.hh-fpill {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 500; padding: 3px 10px; border-radius: 9999px;
    background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;
}
.hh-fpill a { color: #93c5fd; text-decoration: none; margin-left: 2px; font-weight: 700; }
.hh-fpill a:hover { color: #1d4ed8; }

.hh-card { background: #fff; border: 1px solid #e8edf4; border-radius: 16px; overflow: hidden; }
.hh-card-top {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; gap: 8px;
}
.hh-result-info { font-size: 13px; color: #64748b; }
.hh-result-info strong { color: #0f172a; }

.hh-table { width: 100%; border-collapse: collapse; min-width: 820px; }
.hh-table thead th {
    padding: 11px 14px; text-align: left;
    font-size: 11px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .06em; color: #94a3b8;
    background: #f8fafc; border-bottom: 1px solid #f1f5f9; white-space: nowrap;
}
.hh-table tbody td {
    padding: 12px 14px; font-size: 13px; color: #0f172a;
    border-bottom: 1px solid #f8fafc; vertical-align: middle;
}
.hh-table tbody tr:last-child td { border-bottom: none; }
.hh-table tbody tr:hover td { background: #fafbff; }

.hh-code-badge {
    font-family: 'DM Mono', monospace; font-size: 11px; font-weight: 500;
    padding: 3px 8px; border-radius: 6px; background: #f0f0ff; color: #4338ca; display: inline-block;
}
.hh-prod-name { font-weight: 600; color: #0f172a; line-height: 1.3; }
.hh-prod-sub  { font-size: 11px; color: #94a3b8; margin-top: 2px; font-family: 'DM Mono', monospace; }
.hh-price     { font-family: 'DM Mono', monospace; font-weight: 500; color: #059669; }
.cat-pill { font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 9999px; background: #ede9fe; color: #5b21b6; }

.stock-chip {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'DM Mono', monospace; font-size: 12px; font-weight: 500;
    padding: 3px 9px; border-radius: 9999px;
}
.stock-ok  { background: #dcfce7; color: #166534; }
.stock-low { background: #fef3c7; color: #92400e; }
.stock-out { background: #fee2e2; color: #991b1b; }
.stock-na  { background: #f1f5f9; color: #94a3b8; }

.st-on  { display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;padding:4px 10px;border-radius:9999px;background:#dcfce7;color:#166534; }
.st-off { display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;padding:4px 10px;border-radius:9999px;background:#f1f5f9;color:#64748b; }
.st-dot { width:6px;height:6px;border-radius:50%;flex-shrink:0; }

.hh-acts { display: flex; align-items: center; gap: 5px; }
.hh-act {
    width: 30px; height: 30px; border-radius: 8px; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; font-size: 12px;
    text-decoration: none; transition: all .15s;
}
.hh-act-view { background: #eff6ff; color: #2563eb; }
.hh-act-view:hover { background: #2563eb; color: #fff; }
.hh-act-edit { background: #fef3c7; color: #d97706; }
.hh-act-edit:hover { background: #d97706; color: #fff; }
.hh-act-del  { background: #fee2e2; color: #dc2626; }
.hh-act-del:hover  { background: #dc2626; color: #fff; }

.hh-empty { text-align: center; padding: 56px 24px; }
.hh-empty-icon { font-size: 40px; color: #cbd5e1; margin-bottom: 12px; }
.hh-empty h4 { font-size: 15px; font-weight: 600; color: #0f172a; margin: 0 0 6px; }
.hh-empty p  { font-size: 13px; color: #94a3b8; margin: 0; }

.hh-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 20px; border-top: 1px solid #f1f5f9; flex-wrap: wrap; gap: 8px;
}
.hh-pages { display: flex; gap: 5px; }
.hh-pg {
    min-width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 600; font-family: 'Inter', sans-serif;
    border: 1px solid #e2e8f0; background: #fff; color: #64748b;
    cursor: pointer; text-decoration: none; transition: all .15s;
}
.hh-pg:hover { border-color: #2563eb; color: #2563eb; }
.hh-pg.active { background: #2563eb; color: #fff; border-color: #2563eb; }
.hh-pg.disabled { opacity: .35; pointer-events: none; }

.hh-modal .modal-content { border: none; border-radius: 16px; overflow: hidden; }
.hh-modal .modal-header  { background: #2563eb; color: #fff; border: none; padding: 16px 22px; }
.hh-modal .modal-title   { font-size: 15px; font-weight: 700; margin: 0; }
.hh-modal .modal-sub     { font-family: 'DM Mono', monospace; font-size: 11px; opacity: .75; margin-top: 2px; }
.hh-modal .modal-body    { padding: 18px 22px; }
.hh-modal .drow  { display: flex; padding: 9px 0; border-bottom: 1px solid #f1f5f9; gap: 10px; }
.hh-modal .drow:last-child { border-bottom: none; }
.hh-modal .dlabel { font-size: 12px; font-weight: 600; color: #94a3b8; width: 130px; flex-shrink: 0; }
.hh-modal .dval   { font-size: 13px; color: #0f172a; }
.hh-modal .modal-footer { border-top: 1px solid #f1f5f9; padding: 12px 22px; gap: 8px; }

@media (max-width: 1024px) { .hh-stats { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 600px)  { .hh-stats { grid-template-columns: 1fr 1fr; } }
</style>

<?php
$keyword   = htmlspecialchars($_GET['keyword']    ?? '');
$filterDM  = $_GET['ma_danh_muc'] ?? '';
$filterTT  = $_GET['trang_thai']  ?? '';
$hasFilter = $keyword !== '' || $filterDM !== '' || $filterTT !== '';
$showFrom  = min(($page-1)*15+1, $total ?? 0);
$showTo    = min($page*15, $total ?? 0);
?>

<div class="hh-root">

<!-- PAGE HEADER -->
<div class="hh-page-head">
    <div>
        <h2><i class="fas fa-box" style="color:#2563eb;font-size:18px;vertical-align:-2px;margin-right:8px;"></i>Quản lý hàng hóa</h2>
        <p>Danh sách sản phẩm, giá bán và tình trạng tồn kho</p>
    </div>
    <?php if (in_array($_SESSION['user']['ma_chuc_vu'], ['ADMIN','QUAN_LY'])): ?>
    <a href="index.php?controller=hang_hoa&action=create" class="hh-btn-add">
        <i class="fas fa-plus" style="font-size:11px;"></i> Thêm hàng hóa
    </a>
    <?php endif; ?>
</div>

<!-- STATS -->
<div class="hh-stats">
    <div class="hh-stat s-total">
        <div class="hh-stat-ico ico-purple"><i class="fas fa-layer-group"></i></div>
        <div><div class="hh-stat-num"><?= number_format($stats['total'] ?? 0) ?></div><div class="hh-stat-lbl">Tổng SKU</div></div>
    </div>
    <div class="hh-stat s-instock">
        <div class="hh-stat-ico ico-green"><i class="fas fa-check-circle"></i></div>
        <div><div class="hh-stat-num"><?= number_format($stats['inStock'] ?? 0) ?></div><div class="hh-stat-lbl">Còn hàng</div></div>
    </div>
    <div class="hh-stat s-low">
        <div class="hh-stat-ico ico-amber"><i class="fas fa-exclamation-triangle"></i></div>
        <div><div class="hh-stat-num"><?= number_format($stats['lowStock'] ?? 0) ?></div><div class="hh-stat-lbl">Sắp hết (&lt;10)</div></div>
    </div>
    <div class="hh-stat s-out">
        <div class="hh-stat-ico ico-red"><i class="fas fa-times-circle"></i></div>
        <div><div class="hh-stat-num"><?= number_format($stats['outStock'] ?? 0) ?></div><div class="hh-stat-lbl">Hết hàng</div></div>
    </div>
</div>

<!-- TOOLBAR -->
<form method="GET" id="filterForm">
<input type="hidden" name="controller" value="hang_hoa">
<input type="hidden" name="action"     value="index">
<input type="hidden" name="page"       value="1">
<div class="hh-toolbar">
    <div class="hh-search-box">
        <i class="fas fa-search"></i>
        <input type="text" name="keyword"
               placeholder="Tìm theo mã, tên sản phẩm, mã vạch…"
               value="<?= $keyword ?>">
    </div>
    <select name="ma_danh_muc" class="hh-select" onchange="document.getElementById('filterForm').submit()">
        <option value="">Tất cả danh mục</option>
        <?php foreach ($danhMucs as $dm): ?>
        <option value="<?= $dm['ma_danh_muc'] ?>" <?= $filterDM == $dm['ma_danh_muc'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($dm['ten_danh_muc']) ?>
        </option>
        <?php endforeach; ?>
    </select>
    <select name="trang_thai" class="hh-select" onchange="document.getElementById('filterForm').submit()">
        <option value="">Tất cả trạng thái</option>
        <option value="DANG_KINH_DOANH"  <?= $filterTT=='DANG_KINH_DOANH'  ? 'selected':'' ?>>Đang kinh doanh</option>
        <option value="NGUNG_KINH_DOANH" <?= $filterTT=='NGUNG_KINH_DOANH' ? 'selected':'' ?>>Ngừng kinh doanh</option>
    </select>
    <button type="submit" class="hh-btn-search"><i class="fas fa-search" style="font-size:11px;"></i> Tìm</button>
    <?php if ($hasFilter): ?>
    <a href="index.php?controller=hang_hoa&action=index" class="hh-btn-reset">
        <i class="fas fa-times" style="font-size:11px;"></i> Xóa lọc
    </a>
    <?php endif; ?>
</div>
</form>

<!-- ACTIVE FILTER PILLS -->
<?php if ($hasFilter): ?>
<div class="hh-fpills">
    <?php if ($keyword !== ''): ?>
    <span class="hh-fpill">
        <i class="fas fa-search" style="font-size:10px;"></i> "<?= $keyword ?>"
        <a href="?controller=hang_hoa&action=index&ma_danh_muc=<?= urlencode($filterDM) ?>&trang_thai=<?= urlencode($filterTT) ?>">×</a>
    </span>
    <?php endif; ?>
    <?php if ($filterDM !== ''): ?>
    <?php $dmName=''; foreach($danhMucs as $d){ if($d['ma_danh_muc']==$filterDM){ $dmName=$d['ten_danh_muc']; break; } } ?>
    <span class="hh-fpill">
        <i class="fas fa-folder" style="font-size:10px;"></i> <?= htmlspecialchars($dmName) ?>
        <a href="?controller=hang_hoa&action=index&keyword=<?= urlencode($keyword) ?>&trang_thai=<?= urlencode($filterTT) ?>">×</a>
    </span>
    <?php endif; ?>
    <?php if ($filterTT !== ''): ?>
    <span class="hh-fpill">
        <i class="fas fa-circle" style="font-size:10px;"></i>
        <?= $filterTT=='DANG_KINH_DOANH' ? 'Đang KD' : 'Ngừng KD' ?>
        <a href="?controller=hang_hoa&action=index&keyword=<?= urlencode($keyword) ?>&ma_danh_muc=<?= urlencode($filterDM) ?>">×</a>
    </span>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- TABLE CARD -->
<div class="hh-card">
    <div class="hh-card-top">
        <span class="hh-result-info">
            <?php if ($total ?? 0): ?>
                Hiển thị <strong><?= $showFrom ?>–<?= $showTo ?></strong> / <strong><?= number_format($total) ?></strong> sản phẩm
            <?php else: ?>
                Không có kết quả
            <?php endif; ?>
        </span>
        <span style="font-size:11px; color:#94a3b8;">
            <i class="fas fa-database" style="margin-right:4px;"></i>Tồn kho tính từ tất cả lô hàng hiện tại
        </span>
    </div>

    <div style="overflow-x:auto;">
    <table class="hh-table">
        <thead>
            <tr>
                <th style="width:36px;">#</th>
                <th>Mã hàng</th>
                <th>Sản phẩm</th>
                <th>ĐVT</th>
                <th>Giá bán</th>
                <th>Danh mục</th>
                <th>Nhà cung cấp</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
                <th style="text-align:center; width:110px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $rowNum = ($page - 1) * 15 + 1;
        if (!empty($hangHoas)):
            foreach ($hangHoas as $hh):
                $isOn   = $hh['trang_thai'] === 'DANG_KINH_DOANH';
                $tonKho = isset($hh['ton_kho']) ? (int)$hh['ton_kho'] : null;
                if ($tonKho === null)    { $sc='stock-na';  $sl='—'; }
                elseif ($tonKho === 0)  { $sc='stock-out'; $sl='Hết hàng'; }
                elseif ($tonKho < 10)   { $sc='stock-low'; $sl=number_format($tonKho); }
                else                    { $sc='stock-ok';  $sl=number_format($tonKho); }
                $mid = 'hm'.preg_replace('/[^a-z0-9]/i','_',$hh['ma_hang_hoa']);
        ?>
        <tr>
            <td style="color:#cbd5e1;font-size:11px;font-family:'DM Mono',monospace;"><?= $rowNum++ ?></td>
            <td><span class="hh-code-badge"><?= htmlspecialchars($hh['ma_hang_hoa']) ?></span></td>
            <td>
                <div class="hh-prod-name"><?= htmlspecialchars($hh['ten_hang_hoa']) ?></div>
                <?php if (!empty($hh['ma_vach'])): ?>
                <div class="hh-prod-sub"><i class="fas fa-barcode" style="margin-right:3px;"></i><?= htmlspecialchars($hh['ma_vach']) ?></div>
                <?php endif; ?>
            </td>
            <td style="color:#64748b;font-size:12px;"><?= htmlspecialchars($hh['don_vi_tinh']) ?></td>
            <td><span class="hh-price"><?= number_format($hh['gia_ban']) ?>đ</span></td>
            <td>
                <?php if (!empty($hh['ten_danh_muc'])): ?>
                    <span class="cat-pill"><?= htmlspecialchars($hh['ten_danh_muc']) ?></span>
                <?php else: ?><span style="color:#cbd5e1;">—</span><?php endif; ?>
            </td>
            <td style="font-size:12px;color:#64748b;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                <?= htmlspecialchars($hh['ten_nha_cung_cap'] ?? '—') ?>
            </td>
            <td>
                <span class="stock-chip <?= $sc ?>">
                    <?php if ($tonKho !== null && $tonKho > 0): ?><i class="fas fa-circle" style="font-size:6px;"></i><?php endif; ?>
                    <?= $sl ?>
                    <?php if ($tonKho !== null && $tonKho > 0): ?><?= htmlspecialchars($hh['don_vi_tinh']) ?><?php endif; ?>
                </span>
            </td>
            <td>
                <?php if ($isOn): ?>
                    <span class="st-on"><span class="st-dot" style="background:#16a34a;"></span>Đang KD</span>
                <?php else: ?>
                    <span class="st-off"><span class="st-dot" style="background:#94a3b8;"></span>Ngừng KD</span>
                <?php endif; ?>
            </td>
            <td>
                <div class="hh-acts" style="justify-content:center;">
                    <button class="hh-act hh-act-view" title="Xem chi tiết"
                            data-bs-toggle="modal" data-bs-target="#<?= $mid ?>">
                        <i class="fas fa-eye"></i>
                    </button>
                    <?php if (in_array($_SESSION['user']['ma_chuc_vu'],['ADMIN','QUAN_LY'])): ?>
                    <a href="index.php?controller=hang_hoa&action=edit&id=<?= urlencode($hh['ma_hang_hoa']) ?>"
                       class="hh-act hh-act-edit" title="Chỉnh sửa">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <?php endif; ?>
                    <?php if ($_SESSION['user']['ma_chuc_vu']==='ADMIN'): ?>
                    <button class="hh-act hh-act-del" title="Xóa"
                            onclick="hhDel('<?= addslashes($hh['ma_hang_hoa']) ?>','<?= addslashes($hh['ten_hang_hoa']) ?>')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>

        <!-- Detail modal -->
        <div class="modal fade hh-modal" id="<?= $mid ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <div class="modal-title"><?= htmlspecialchars($hh['ten_hang_hoa']) ?></div>
                            <div class="modal-sub"><?= htmlspecialchars($hh['ma_hang_hoa']) ?></div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <?php
                        $mrows = [
                            ['Đơn vị tính',  htmlspecialchars($hh['don_vi_tinh'])],
                            ['Giá bán',       '<strong style="color:#059669">'.number_format($hh['gia_ban']).'đ</strong>'],
                            ['Mã vạch',       htmlspecialchars(!empty($hh['ma_vach']) ? $hh['ma_vach'] : '—')],
                            ['Danh mục',      htmlspecialchars(!empty($hh['ten_danh_muc']) ? $hh['ten_danh_muc'] : '—')],
                            ['Nhà cung cấp',  htmlspecialchars(!empty($hh['ten_nha_cung_cap']) ? $hh['ten_nha_cung_cap'] : '—')],
                            ['Tồn kho',       ($tonKho!==null ? number_format($tonKho).' '.htmlspecialchars($hh['don_vi_tinh']) : '—')],
                            ['Trạng thái',    $isOn
                                ? '<span class="st-on"><span class="st-dot" style="background:#16a34a"></span>Đang kinh doanh</span>'
                                : '<span class="st-off"><span class="st-dot" style="background:#94a3b8"></span>Ngừng kinh doanh</span>'],
                        ];
                        foreach ($mrows as [$lbl,$val]): ?>
                        <div class="drow">
                            <span class="dlabel"><?= $lbl ?></span>
                            <span class="dval"><?= $val ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="modal-footer">
                        <?php if (in_array($_SESSION['user']['ma_chuc_vu'],['ADMIN','QUAN_LY'])): ?>
                        <a href="index.php?controller=hang_hoa&action=edit&id=<?= urlencode($hh['ma_hang_hoa']) ?>"
                           class="hh-btn-search" style="font-size:12px;padding:7px 14px;border-radius:8px;text-decoration:none;">
                            <i class="fas fa-pencil-alt" style="font-size:11px;"></i> Chỉnh sửa
                        </a>
                        <?php endif; ?>
                        <button class="hh-btn-reset" data-bs-dismiss="modal" style="font-size:12px;padding:7px 14px;">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <?php endforeach; else: ?>
        <tr><td colspan="10">
            <div class="hh-empty">
                <div class="hh-empty-icon"><i class="fas fa-box-open"></i></div>
                <h4><?= $hasFilter ? 'Không tìm thấy kết quả' : 'Chưa có hàng hóa' ?></h4>
                <p><?= $hasFilter ? 'Thử thay đổi bộ lọc hoặc từ khóa.' : 'Bắt đầu bằng cách thêm sản phẩm đầu tiên.' ?></p>
                <?php if ($hasFilter): ?>
                <a href="index.php?controller=hang_hoa&action=index" class="hh-btn-reset" style="display:inline-flex;margin-top:12px;">
                    <i class="fas fa-times" style="font-size:11px;"></i> Xóa bộ lọc
                </a>
                <?php elseif (in_array($_SESSION['user']['ma_chuc_vu'],['ADMIN','QUAN_LY'])): ?>
                <a href="index.php?controller=hang_hoa&action=create" class="hh-btn-add" style="display:inline-flex;margin-top:12px;">
                    <i class="fas fa-plus" style="font-size:11px;"></i> Thêm hàng hóa đầu tiên
                </a>
                <?php endif; ?>
            </div>
        </td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>

    <!-- PAGINATION -->
    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="hh-footer">
        <span style="font-size:12px;color:#94a3b8;">Trang <?= $page ?> / <?= $totalPages ?></span>
        <div class="hh-pages">
            <a class="hh-pg <?= $page<=1?'disabled':'' ?>"
               href="?controller=hang_hoa&action=index&page=<?= $page-1 ?>&keyword=<?= urlencode($keyword) ?>&ma_danh_muc=<?= urlencode($filterDM) ?>&trang_thai=<?= urlencode($filterTT) ?>">
                <i class="fas fa-chevron-left" style="font-size:10px;"></i>
            </a>
            <?php
            $ps = max(1, $page-2); $pe = min($totalPages, $page+2);
            if ($ps > 1) echo '<span class="hh-pg disabled" style="pointer-events:none;">…</span>';
            for ($i=$ps; $i<=$pe; $i++):
            ?>
            <a class="hh-pg <?= $i===$page?'active':'' ?>"
               href="?controller=hang_hoa&action=index&page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>&ma_danh_muc=<?= urlencode($filterDM) ?>&trang_thai=<?= urlencode($filterTT) ?>">
                <?= $i ?>
            </a>
            <?php endfor;
            if ($pe < $totalPages) echo '<span class="hh-pg disabled" style="pointer-events:none;">…</span>'; ?>
            <a class="hh-pg <?= $page>=$totalPages?'disabled':'' ?>"
               href="?controller=hang_hoa&action=index&page=<?= $page+1 ?>&keyword=<?= urlencode($keyword) ?>&ma_danh_muc=<?= urlencode($filterDM) ?>&trang_thai=<?= urlencode($filterTT) ?>">
                <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

</div>
<script>
function hhDel(id, name) {
    Swal.fire({
        title: 'Xóa hàng hóa?',
        html: 'Bạn sắp xóa <strong>' + name + '</strong>.<br><small style="color:#94a3b8">Hành động này không thể hoàn tác.</small>',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#64748b',
        confirmButtonText: 'Xóa', cancelButtonText: 'Hủy'
    }).then(function(r){ if(r.isConfirmed) location.href='index.php?controller=hang_hoa&action=delete&id='+encodeURIComponent(id); });
}
</script>