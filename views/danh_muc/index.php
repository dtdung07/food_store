<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap');

.dm-wrap { font-family: 'Plus Jakarta Sans', sans-serif; }

.dm-page-head {
    display: flex; align-items: flex-end; justify-content: space-between;
    margin-bottom: 28px;
}
.dm-page-head h2 {
    font-size: 22px; font-weight: 800; color: #1a1d2e; margin: 0 0 4px;
}
.dm-page-head p { font-size: 13px; color: #8990aa; margin: 0; }

/* Stats bar */
.dm-stats-bar {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
.dm-stat {
    background: white;
    border-radius: 14px;
    padding: 18px 20px;
    border: 1px solid #eef0f6;
    display: flex; align-items: center; gap: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    transition: transform .2s, box-shadow .2s;
}
.dm-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }
.dm-stat-icon {
    width: 46px; height: 46px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; font-size: 20px;
    flex-shrink: 0;
}
.dms-purple { background: rgba(108,99,255,.1); color: #6c63ff; }
.dms-green  { background: rgba(16,217,160,.1); color: #10d9a0; }
.dms-blue   { background: rgba(79,172,254,.1); color: #4facfe; }
.dm-stat-val { font-family: 'Space Mono', monospace; font-size: 24px; font-weight: 700; color: #1a1d2e; }
.dm-stat-lbl { font-size: 12px; color: #8990aa; font-weight: 500; }

/* Toolbar */
.dm-toolbar {
    display: flex; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;
}
.dm-search-wrap {
    flex: 1; min-width: 220px;
    display: flex; align-items: center;
    background: white;
    border: 1.5px solid #eef0f6;
    border-radius: 12px;
    padding: 0 16px;
    transition: border-color .2s, box-shadow .2s;
}
.dm-search-wrap:focus-within {
    border-color: #6c63ff;
    box-shadow: 0 0 0 3px rgba(108,99,255,.12);
}
.dm-search-wrap i { color: #8990aa; font-size: 14px; margin-right: 10px; }
.dm-search-wrap input {
    border: none; outline: none;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px; color: #1a1d2e; background: transparent;
    padding: 11px 0; width: 100%;
}
.dm-btn {
    display: inline-flex; align-items: center; gap: 8px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px; font-weight: 600;
    padding: 10px 18px; border-radius: 12px;
    border: none; cursor: pointer; transition: all .2s;
    text-decoration: none; white-space: nowrap;
}
.dm-btn-primary {
    background: linear-gradient(135deg, #6c63ff, #a78bfa);
    color: white; box-shadow: 0 4px 14px rgba(108,99,255,.3);
}
.dm-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(108,99,255,.4); color: white; }
.dm-btn-outline {
    background: white; color: #6c63ff;
    border: 1.5px solid rgba(108,99,255,.25);
}
.dm-btn-outline:hover { background: rgba(108,99,255,.05); }

/* Grid view */
.dm-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 16px;
}
.dm-category-card {
    background: white;
    border: 1px solid #eef0f6;
    border-radius: 16px;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    position: relative;
}
.dm-category-card:hover { transform: translateY(-4px); box-shadow: 0 12px 36px rgba(0,0,0,.1); }
.dm-card-stripe { height: 5px; }
.dm-card-body { padding: 18px 20px 14px; }
.dm-card-icon-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.dm-big-icon {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 22px;
}
.dm-card-code { font-family: 'Space Mono', monospace; font-size: 10px; color: #8990aa; font-weight: 700; }
.dm-card-name { font-size: 15px; font-weight: 700; color: #1a1d2e; margin-bottom: 4px; }
.dm-card-desc { font-size: 12px; color: #8990aa; line-height: 1.5; margin-bottom: 14px; min-height: 36px; }
.dm-card-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 20px;
    background: #f9fafb;
    border-top: 1px solid #eef0f6;
}
.dm-product-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 600;
    padding: 4px 10px; border-radius: 20px;
}
.dm-actions { display: flex; gap: 6px; }
.dm-action-btn {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; border: none; cursor: pointer; transition: all .2s;
    text-decoration: none;
}
.dm-action-edit   { background: rgba(255,149,0,.1);  color: #ff9500; }
.dm-action-edit:hover  { background: #ff9500; color: white; }
.dm-action-del    { background: rgba(255,77,109,.1); color: #ff4d6d; }
.dm-action-del:hover   { background: #ff4d6d; color: white; }

/* Table view (hidden by default) */
.dm-table-wrap { background: white; border-radius: 16px; border: 1px solid #eef0f6; overflow: hidden; }
.dm-table { width: 100%; border-collapse: collapse; }
.dm-table thead th {
    background: #f9fafb;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: #8990aa;
    padding: 14px 18px; border-bottom: 1px solid #eef0f6; text-align: left;
}
.dm-table tbody td { padding: 14px 18px; border-bottom: 1px solid #f3f4f8; font-size: 13px; color: #2d3047; vertical-align: middle; }
.dm-table tbody tr:last-child td { border-bottom: none; }
.dm-table tbody tr:hover td { background: #fafbff; }
.dm-code-badge { font-family: 'Space Mono', monospace; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 6px; }

/* Empty state */
.dm-empty {
    text-align: center; padding: 60px 20px;
    grid-column: 1 / -1;
}
.dm-empty-icon { font-size: 48px; color: #d0d4e8; margin-bottom: 14px; }
.dm-empty h4 { font-size: 16px; color: #1a1d2e; font-weight: 700; margin-bottom: 6px; }
.dm-empty p  { font-size: 13px; color: #8990aa; }

/* Pagination */
.dm-pagination { display: flex; justify-content: center; gap: 6px; margin-top: 24px; }
.dm-page-btn {
    min-width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif;
    border: 1.5px solid #eef0f6; background: white; color: #8990aa;
    cursor: pointer; text-decoration: none; transition: all .2s;
}
.dm-page-btn:hover { border-color: #6c63ff; color: #6c63ff; }
.dm-page-btn.active { background: linear-gradient(135deg, #6c63ff, #a78bfa); color: white; border-color: transparent; }

/* Color palettes for categories */
.pal-0 { --c: #6c63ff; }
.pal-1 { --c: #10d9a0; }
.pal-2 { --c: #4facfe; }
.pal-3 { --c: #ff9500; }
.pal-4 { --c: #ff4d6d; }
.pal-5 { --c: #a78bfa; }
.pal-6 { --c: #ffd60a; }
.pal-7 { --c: #06d6a0; }
</style>

<div class="dm-wrap fade-in">

<!-- Page header -->
<div class="dm-page-head">
    <div>
        <h2><i class="fas fa-folder-tree" style="color:#6c63ff; margin-right:8px;"></i> Quản lý danh mục</h2>
        <p>Quản lý phân loại sản phẩm trong hệ thống FoodStore</p>
    </div>
    <?php if (in_array($_SESSION['user']['ma_chuc_vu'], ['ADMIN', 'QUAN_LY'])): ?>
        <a href="index.php?controller=danh_muc&action=create" class="dm-btn dm-btn-primary">
            <i class="fas fa-plus"></i> Thêm danh mục
        </a>
    <?php endif; ?>
</div>

<!-- Stats bar -->
<div class="dm-stats-bar">
    <div class="dm-stat">
        <div class="dm-stat-icon dms-purple"><i class="fas fa-folder"></i></div>
        <div>
            <div class="dm-stat-val"><?= count($danhMucs ?? []) ?></div>
            <div class="dm-stat-lbl">Tổng danh mục</div>
        </div>
    </div>
    <div class="dm-stat">
        <div class="dm-stat-icon dms-green"><i class="fas fa-boxes"></i></div>
        <div>
            <div class="dm-stat-val"><?= array_sum(array_column($danhMucs ?? [], 'so_luong_sp')) ?></div>
            <div class="dm-stat-lbl">Tổng sản phẩm</div>
        </div>
    </div>
    <div class="dm-stat">
        <div class="dm-stat-icon dms-blue"><i class="fas fa-chart-bar"></i></div>
        <div>
            <div class="dm-stat-val"><?= $totalCount ?? count($danhMucs ?? []) ?></div>
            <div class="dm-stat-lbl">Kết quả tìm kiếm</div>
        </div>
    </div>
</div>

<!-- Toolbar -->
<div class="dm-toolbar">
    <form method="GET" style="display:contents;">
        <input type="hidden" name="controller" value="danh_muc">
        <input type="hidden" name="action" value="index">
        <div class="dm-search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" name="keyword" placeholder="Tìm theo mã hoặc tên danh mục..."
                   value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
        </div>
        <button type="submit" class="dm-btn dm-btn-primary"><i class="fas fa-search"></i> Tìm</button>
    </form>
    <button class="dm-btn dm-btn-outline" id="toggleView" onclick="toggleView(this)">
        <i class="fas fa-list"></i> Dạng bảng
    </button>
</div>

<!-- Grid view -->
<div class="dm-grid" id="gridView">
    <?php if (isset($danhMucs) && !empty($danhMucs)):
        $catIcons = ['fa-apple-alt','fa-bread-slice','fa-glass-cheers','fa-drumstick-bite','fa-seedling','fa-candy-cane','fa-coffee','fa-fish'];
        foreach ($danhMucs as $i => $dm):
            $palIdx = $i % 8;
            $colors = ['#6c63ff','#10d9a0','#4facfe','#ff9500','#ff4d6d','#a78bfa','#ffd60a','#06d6a0'];
            $c = $colors[$palIdx];
            $icon = $catIcons[$palIdx];
    ?>
    <div class="dm-category-card pal-<?= $palIdx ?>">
        <div class="dm-card-stripe" style="background:<?= $c ?>;"></div>
        <div class="dm-card-body">
            <div class="dm-card-icon-row">
                <div class="dm-big-icon" style="background:<?= $c ?>1a; color:<?= $c ?>;">
                    <i class="fas <?= $icon ?>"></i>
                </div>
                <span class="dm-card-code"><?= htmlspecialchars($dm['ma_danh_muc']) ?></span>
            </div>
            <div class="dm-card-name"><?= htmlspecialchars($dm['ten_danh_muc']) ?></div>
            <div class="dm-card-desc"><?= htmlspecialchars($dm['mo_ta'] ?? 'Chưa có mô tả') ?></div>
        </div>
        <div class="dm-card-footer">
            <span class="dm-product-badge" style="background:<?= $c ?>1a; color:<?= $c ?>;">
                <i class="fas fa-box"></i> <?= $dm['so_luong_sp'] ?? 0 ?> sản phẩm
            </span>
            <div class="dm-actions">
                <?php if (in_array($_SESSION['user']['ma_chuc_vu'], ['ADMIN', 'QUAN_LY'])): ?>
                    <a href="index.php?controller=danh_muc&action=edit&id=<?= $dm['ma_danh_muc'] ?>"
                       class="dm-action-btn dm-action-edit" title="Chỉnh sửa">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                <?php endif; ?>
                <?php if ($_SESSION['user']['ma_chuc_vu'] == 'ADMIN'): ?>
                    <button class="dm-action-btn dm-action-del" title="Xóa"
                            onclick="confirmDelete('<?= addslashes($dm['ma_danh_muc']) ?>', '<?= addslashes($dm['ten_danh_muc']) ?>')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; else: ?>
    <div class="dm-empty">
        <div class="dm-empty-icon"><i class="fas fa-folder-open"></i></div>
        <h4>Chưa có danh mục nào</h4>
        <p>Bắt đầu bằng cách thêm danh mục đầu tiên</p>
        <?php if (in_array($_SESSION['user']['ma_chuc_vu'], ['ADMIN', 'QUAN_LY'])): ?>
            <a href="index.php?controller=danh_muc&action=create" class="dm-btn dm-btn-primary" style="display:inline-flex; margin-top:16px;">
                <i class="fas fa-plus"></i> Thêm danh mục đầu tiên
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Table view (hidden) -->
<div id="tableView" style="display:none;">
    <div class="dm-table-wrap">
        <table class="dm-table">
            <thead>
                <tr>
                    <th>Mã danh mục</th>
                    <th>Tên danh mục</th>
                    <th>Mô tả</th>
                    <th>Số lượng SP</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($danhMucs) && !empty($danhMucs)):
                    foreach ($danhMucs as $i => $dm):
                        $colors = ['#6c63ff','#10d9a0','#4facfe','#ff9500','#ff4d6d','#a78bfa','#ffd60a','#06d6a0'];
                        $c = $colors[$i % 8];
                ?>
                <tr>
                    <td><span class="dm-code-badge" style="background:<?= $c ?>1a; color:<?= $c ?>;"><?= htmlspecialchars($dm['ma_danh_muc']) ?></span></td>
                    <td><strong><?= htmlspecialchars($dm['ten_danh_muc']) ?></strong></td>
                    <td style="color:#8990aa;"><?= htmlspecialchars($dm['mo_ta'] ?? '--') ?></td>
                    <td><span class="dm-product-badge" style="background:<?= $c ?>1a; color:<?= $c ?>; font-size:12px; padding:3px 10px; border-radius:20px;"><?= $dm['so_luong_sp'] ?? 0 ?></span></td>
                    <td>
                        <div class="dm-actions">
                            <?php if (in_array($_SESSION['user']['ma_chuc_vu'], ['ADMIN', 'QUAN_LY'])): ?>
                                <a href="index.php?controller=danh_muc&action=edit&id=<?= $dm['ma_danh_muc'] ?>" class="dm-action-btn dm-action-edit"><i class="fas fa-pencil-alt"></i></a>
                            <?php endif; ?>
                            <?php if ($_SESSION['user']['ma_chuc_vu'] == 'ADMIN'): ?>
                                <button class="dm-action-btn dm-action-del" onclick="confirmDelete('<?= addslashes($dm['ma_danh_muc']) ?>', '<?= addslashes($dm['ten_danh_muc']) ?>')"><i class="fas fa-trash-alt"></i></button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if (isset($totalPages) && $totalPages > 1): ?>
<div class="dm-pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a class="dm-page-btn <?= $i == ($page ?? 1) ? 'active' : '' ?>"
       href="?controller=danh_muc&action=index&page=<?= $i ?>&keyword=<?= urlencode($_GET['keyword'] ?? '') ?>">
        <?= $i ?>
    </a>
    <?php endfor; ?>
</div>
<?php endif; ?>

</div>

<script>
var isGrid = true;
function toggleView(btn) {
    isGrid = !isGrid;
    document.getElementById('gridView').style.display  = isGrid ? 'grid' : 'none';
    document.getElementById('tableView').style.display = isGrid ? 'none' : 'block';
    btn.innerHTML = isGrid
        ? '<i class="fas fa-list"></i> Dạng bảng'
        : '<i class="fas fa-th-large"></i> Dạng lưới';
}

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Xóa danh mục?',
        html: 'Bạn sắp xóa <strong>' + name + '</strong>.<br>Hành động này không thể hoàn tác!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff4d6d',
        cancelButtonColor: '#8990aa',
        confirmButtonText: '<i class="fas fa-trash"></i> Xóa',
        cancelButtonText: 'Hủy',
        borderRadius: '16px',
        background: '#fff',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'index.php?controller=danh_muc&action=delete&id=' + id;
        }
    });
}
</script>