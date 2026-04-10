<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap');

.ncc-wrap { font-family: 'Plus Jakarta Sans', sans-serif; }

/* Page header */
.ncc-page-head { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 24px; }
.ncc-page-head h2 { font-size: 22px; font-weight: 800; color: #1a1d2e; margin: 0 0 4px; }
.ncc-page-head p  { font-size: 13px; color: #8990aa; margin: 0; }

/* Stats */
.ncc-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
.ncc-stat {
    background: white; border-radius: 14px;
    padding: 16px 18px;
    border: 1px solid #eef0f6;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    transition: transform .2s, box-shadow .2s;
    display: flex; align-items: center; gap: 12px;
}
.ncc-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.09); }
.ncc-stat-ico { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.ncc-sv { font-family: 'Space Mono', monospace; font-size: 22px; font-weight: 700; color: #1a1d2e; }
.ncc-sl { font-size: 11px; color: #8990aa; font-weight: 500; }

/* Toolbar */
.ncc-toolbar { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
.ncc-search {
    flex: 1; min-width: 220px;
    display: flex; align-items: center;
    background: white; border: 1.5px solid #eef0f6;
    border-radius: 12px; padding: 0 14px;
    transition: all .2s;
}
.ncc-search:focus-within { border-color: #10d9a0; box-shadow: 0 0 0 3px rgba(16,217,160,.12); }
.ncc-search i { color: #8990aa; font-size: 13px; margin-right: 8px; }
.ncc-search input {
    border: none; outline: none; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px; color: #1a1d2e; background: transparent; padding: 11px 0; width: 100%;
}
.ncc-filter-select {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px; color: #1a1d2e;
    background: white; border: 1.5px solid #eef0f6;
    border-radius: 12px; padding: 10px 14px; outline: none; cursor: pointer;
}
.ncc-btn {
    display: inline-flex; align-items: center; gap: 7px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px; font-weight: 600;
    padding: 10px 18px; border-radius: 12px;
    border: none; cursor: pointer; transition: all .2s; text-decoration: none;
}
.ncc-btn-primary { background: linear-gradient(135deg, #10d9a0, #06c97b); color: white; box-shadow: 0 4px 14px rgba(16,217,160,.3); }
.ncc-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16,217,160,.4); color: white; }
.ncc-btn-outline { background: white; color: #8990aa; border: 1.5px solid #eef0f6; }
.ncc-btn-outline:hover { background: #f9fafb; }

/* Cards grid */
.ncc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; }

.ncc-card {
    background: white; border: 1px solid #eef0f6; border-radius: 18px;
    overflow: hidden; transition: transform .2s, box-shadow .2s;
    position: relative;
}
.ncc-card:hover { transform: translateY(-4px); box-shadow: 0 14px 40px rgba(0,0,0,.1); }

.ncc-card-top {
    padding: 20px 22px 16px;
    display: flex; gap: 14px;
    border-bottom: 1px solid #f3f4f8;
}
.ncc-avatar {
    width: 50px; height: 50px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; font-weight: 800; color: white;
    flex-shrink: 0;
}
.ncc-card-info { flex: 1; min-width: 0; }
.ncc-card-name { font-size: 15px; font-weight: 700; color: #1a1d2e; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ncc-card-code { font-family: 'Space Mono', monospace; font-size: 10px; color: #8990aa; font-weight: 700; }
.ncc-status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; margin-top: 4px; }
.ncc-status-on  { background: rgba(16,217,160,.1); color: #10d9a0; }
.ncc-status-off { background: rgba(255,77,109,.1);  color: #ff4d6d; }

.ncc-card-meta {
    padding: 14px 22px;
    display: flex; flex-direction: column; gap: 7px;
}
.ncc-meta-row { display: flex; align-items: center; gap: 10px; font-size: 12px; color: #8990aa; }
.ncc-meta-row i { width: 16px; text-align: center; color: #c5c9dd; flex-shrink: 0; }
.ncc-meta-row span { color: #2d3047; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.ncc-card-foot {
    padding: 12px 22px;
    background: #f9fafb; border-top: 1px solid #f3f4f8;
    display: flex; align-items: center; justify-content: space-between;
}
.ncc-products-count { font-size: 12px; color: #8990aa; display: flex; align-items: center; gap: 5px; }
.ncc-products-count strong { color: #1a1d2e; }
.ncc-actions { display: flex; gap: 6px; }
.ncc-act-btn {
    width: 32px; height: 32px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; border: none; cursor: pointer; transition: all .2s;
    text-decoration: none;
}
.ncc-act-view { background: rgba(79,172,254,.1); color: #4facfe; }
.ncc-act-view:hover { background: #4facfe; color: white; }
.ncc-act-edit { background: rgba(255,149,0,.1); color: #ff9500; }
.ncc-act-edit:hover { background: #ff9500; color: white; }
.ncc-act-del  { background: rgba(255,77,109,.1); color: #ff4d6d; }
.ncc-act-del:hover  { background: #ff4d6d; color: white; }

/* Empty */
.ncc-empty { text-align: center; padding: 60px 20px; grid-column: 1/-1; }
.ncc-empty-icon { font-size: 48px; color: #d0d4e8; margin-bottom: 14px; }

/* Modal detail */
.ncc-modal-detail table { width: 100%; }
.ncc-modal-detail td { padding: 10px 0; border-bottom: 1px solid #f3f4f8; font-size: 13px; vertical-align: top; }
.ncc-modal-detail td:first-child { color: #8990aa; width: 38%; font-weight: 600; }
.ncc-modal-detail tr:last-child td { border: none; }

/* Pagination */
.ncc-pagi { display: flex; justify-content: center; gap: 6px; margin-top: 24px; }
.ncc-pg-btn {
    min-width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif;
    border: 1.5px solid #eef0f6; background: white; color: #8990aa;
    cursor: pointer; text-decoration: none; transition: all .2s;
}
.ncc-pg-btn:hover { border-color: #10d9a0; color: #10d9a0; }
.ncc-pg-btn.active { background: linear-gradient(135deg, #10d9a0, #06c97b); color: white; border-color: transparent; }

@media (max-width: 768px) {
    .ncc-stats { grid-template-columns: repeat(2, 1fr); }
    .ncc-grid  { grid-template-columns: 1fr; }
}
</style>

<div class="ncc-wrap fade-in">

<!-- Page header -->
<div class="ncc-page-head">
    <div>
        <h2><i class="fas fa-truck" style="color:#10d9a0; margin-right:8px;"></i> Quản lý nhà cung cấp</h2>
        <p>Quản lý đối tác cung ứng hàng hóa cho FoodStore</p>
    </div>
    <?php if (in_array($_SESSION['user']['ma_chuc_vu'], ['ADMIN', 'QUAN_LY'])): ?>
        <a href="index.php?controller=nha_cung_cap&action=create" class="ncc-btn ncc-btn-primary">
            <i class="fas fa-plus"></i> Thêm nhà cung cấp
        </a>
    <?php endif; ?>
</div>

<!-- Stats -->
<div class="ncc-stats">
    <div class="ncc-stat">
        <div class="ncc-stat-ico" style="background:rgba(16,217,160,.1); color:#10d9a0;"><i class="fas fa-building"></i></div>
        <div><div class="ncc-sv"><?= $totalCount ?? count($nhaCungCaps ?? []) ?></div><div class="ncc-sl">Tổng nhà cung cấp</div></div>
    </div>
    <div class="ncc-stat">
        <div class="ncc-stat-ico" style="background:rgba(79,172,254,.1); color:#4facfe;"><i class="fas fa-check-circle"></i></div>
        <div><div class="ncc-sv"><?= $activeCount ?? 0 ?></div><div class="ncc-sl">Đang hoạt động</div></div>
    </div>
    <div class="ncc-stat">
        <div class="ncc-stat-ico" style="background:rgba(255,77,109,.1); color:#ff4d6d;"><i class="fas fa-ban"></i></div>
        <div><div class="ncc-sv"><?= ($totalCount ?? 0) - ($activeCount ?? 0) ?></div><div class="ncc-sl">Vô hiệu hóa</div></div>
    </div>
    <div class="ncc-stat">
        <div class="ncc-stat-ico" style="background:rgba(255,149,0,.1); color:#ff9500;"><i class="fas fa-boxes"></i></div>
        <div><div class="ncc-sv"><?= $totalProducts ?? 0 ?></div><div class="ncc-sl">Tổng sản phẩm cung cấp</div></div>
    </div>
</div>

<!-- Toolbar -->
<div class="ncc-toolbar">
    <form method="GET" style="display:contents;">
        <input type="hidden" name="controller" value="nha_cung_cap">
        <input type="hidden" name="action" value="index">
        <div class="ncc-search">
            <i class="fas fa-search"></i>
            <input type="text" name="keyword" placeholder="Tìm theo mã, tên, điện thoại..."
                   value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
        </div>
        <select name="trang_thai" class="ncc-filter-select" onchange="this.form.submit()">
            <option value="">Tất cả trạng thái</option>
            <option value="HOAT_DONG"  <?= ($_GET['trang_thai'] ?? '') == 'HOAT_DONG'  ? 'selected' : '' ?>>Đang hoạt động</option>
            <option value="VO_HIEU_HOA" <?= ($_GET['trang_thai'] ?? '') == 'VO_HIEU_HOA' ? 'selected' : '' ?>>Vô hiệu hóa</option>
        </select>
        <button type="submit" class="ncc-btn ncc-btn-primary"><i class="fas fa-search"></i> Tìm</button>
    </form>
</div>

<!-- Cards -->
<div class="ncc-grid">
    <?php if (isset($nhaCungCaps) && !empty($nhaCungCaps)):
        $avatarColors = ['#6c63ff','#10d9a0','#4facfe','#ff9500','#ff4d6d','#a78bfa','#f72585','#118ab2'];
        foreach ($nhaCungCaps as $i => $ncc):
            $bg = $avatarColors[$i % count($avatarColors)];
            $initial = mb_strtoupper(mb_substr($ncc['ten_nha_cung_cap'], 0, 1));
            $isActive = $ncc['trang_thai'] == 'HOAT_DONG';
    ?>
    <div class="ncc-card">
        <div class="ncc-card-top">
            <div class="ncc-avatar" style="background:<?= $bg ?>;"><?= $initial ?></div>
            <div class="ncc-card-info">
                <div class="ncc-card-name" title="<?= htmlspecialchars($ncc['ten_nha_cung_cap']) ?>">
                    <?= htmlspecialchars($ncc['ten_nha_cung_cap']) ?>
                </div>
                <div class="ncc-card-code"><?= htmlspecialchars($ncc['ma_nha_cung_cap']) ?></div>
                <span class="ncc-status-badge <?= $isActive ? 'ncc-status-on' : 'ncc-status-off' ?>">
                    <i class="fas fa-circle" style="font-size:7px;"></i>
                    <?= $isActive ? 'Hoạt động' : 'Vô hiệu' ?>
                </span>
            </div>
        </div>

        <div class="ncc-card-meta">
            <?php if (!empty($ncc['ten_nguoi_lien_he'])): ?>
            <div class="ncc-meta-row">
                <i class="fas fa-user-tie"></i>
                <span><?= htmlspecialchars($ncc['ten_nguoi_lien_he']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($ncc['so_dien_thoai'])): ?>
            <div class="ncc-meta-row">
                <i class="fas fa-phone"></i>
                <span><?= htmlspecialchars($ncc['so_dien_thoai']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($ncc['email'])): ?>
            <div class="ncc-meta-row">
                <i class="fas fa-envelope"></i>
                <span><?= htmlspecialchars($ncc['email']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($ncc['dia_chi'])): ?>
            <div class="ncc-meta-row">
                <i class="fas fa-map-marker-alt"></i>
                <span><?= htmlspecialchars($ncc['dia_chi']) ?></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="ncc-card-foot">
            <div class="ncc-products-count">
                <i class="fas fa-box" style="color:<?= $bg ?>;"></i>
                <span><strong><?= $ncc['so_san_pham'] ?? 0 ?></strong> sản phẩm</span>
            </div>
            <div class="ncc-actions">
                <button class="ncc-act-btn ncc-act-view" title="Xem chi tiết"
                        data-bs-toggle="modal"
                        data-bs-target="#nccModal<?= str_replace([' ','-','/'], '_', $ncc['ma_nha_cung_cap']) ?>">
                    <i class="fas fa-eye"></i>
                </button>
                <?php if (in_array($_SESSION['user']['ma_chuc_vu'], ['ADMIN', 'QUAN_LY'])): ?>
                    <a href="index.php?controller=nha_cung_cap&action=edit&id=<?= $ncc['ma_nha_cung_cap'] ?>"
                       class="ncc-act-btn ncc-act-edit" title="Chỉnh sửa">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                <?php endif; ?>
                <?php if ($_SESSION['user']['ma_chuc_vu'] == 'ADMIN'): ?>
                    <button class="ncc-act-btn ncc-act-del" title="Xóa"
                            onclick="confirmDeleteNcc('<?= addslashes($ncc['ma_nha_cung_cap']) ?>', '<?= addslashes($ncc['ten_nha_cung_cap']) ?>')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal detail -->
    <div class="modal fade" id="nccModal<?= str_replace([' ','-','/'], '_', $ncc['ma_nha_cung_cap']) ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:18px; overflow:hidden; border:none;">
                <div class="modal-header" style="background:<?= $bg ?>; color:white; border:none; padding:18px 24px;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font-weight:800;">
                            <?= $initial ?>
                        </div>
                        <div>
                            <div style="font-size:15px; font-weight:700;"><?= htmlspecialchars($ncc['ten_nha_cung_cap']) ?></div>
                            <div style="font-size:11px; opacity:.8; font-family:'Space Mono', monospace;"><?= htmlspecialchars($ncc['ma_nha_cung_cap']) ?></div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body ncc-modal-detail" style="padding:20px 24px;">
                    <table>
                        <tr><td>Số điện thoại</td><td><?= htmlspecialchars($ncc['so_dien_thoai'] ?? '—') ?></td></tr>
                        <tr><td>Email</td><td><?= htmlspecialchars($ncc['email'] ?? '—') ?></td></tr>
                        <tr><td>Người liên hệ</td><td><?= htmlspecialchars($ncc['ten_nguoi_lien_he'] ?? '—') ?></td></tr>
                        <tr><td>Địa chỉ</td><td><?= htmlspecialchars($ncc['dia_chi'] ?? '—') ?></td></tr>
                        <tr><td>Trạng thái</td><td>
                            <span class="ncc-status-badge <?= $isActive ? 'ncc-status-on' : 'ncc-status-off' ?>">
                                <i class="fas fa-circle" style="font-size:7px;"></i>
                                <?= $isActive ? 'Đang hoạt động' : 'Vô hiệu hóa' ?>
                            </span>
                        </td></tr>
                        <tr><td>Số sản phẩm</td><td><strong><?= $ncc['so_san_pham'] ?? 0 ?></strong> sản phẩm</td></tr>
                    </table>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f8; padding:14px 24px;">
                    <?php if (in_array($_SESSION['user']['ma_chuc_vu'], ['ADMIN', 'QUAN_LY'])): ?>
                        <a href="index.php?controller=nha_cung_cap&action=edit&id=<?= $ncc['ma_nha_cung_cap'] ?>"
                           class="ncc-btn" style="background:<?= $bg ?>1a; color:<?= $bg ?>; border:none; text-decoration:none;">
                            <i class="fas fa-pencil-alt"></i> Chỉnh sửa
                        </a>
                    <?php endif; ?>
                    <button type="button" class="ncc-btn ncc-btn-outline" data-bs-dismiss="modal" style="border:1.5px solid #eef0f6; color:#8990aa; background:white;">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <?php endforeach; else: ?>
    <div class="ncc-empty">
        <div class="ncc-empty-icon"><i class="fas fa-truck"></i></div>
        <h4 style="font-size:16px; color:#1a1d2e; margin-bottom:6px;">Chưa có nhà cung cấp</h4>
        <p style="font-size:13px; color:#8990aa;">Thêm nhà cung cấp đầu tiên để bắt đầu</p>
        <?php if (in_array($_SESSION['user']['ma_chuc_vu'], ['ADMIN', 'QUAN_LY'])): ?>
            <a href="index.php?controller=nha_cung_cap&action=create" class="ncc-btn ncc-btn-primary" style="display:inline-flex; margin-top:16px;">
                <i class="fas fa-plus"></i> Thêm nhà cung cấp
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if (isset($totalPages) && $totalPages > 1): ?>
<div class="ncc-pagi">
    <a class="ncc-pg-btn <?= ($page <= 1) ? 'disabled' : '' ?>"
       href="?controller=nha_cung_cap&action=index&page=<?= $page-1 ?>&keyword=<?= urlencode($_GET['keyword'] ?? '') ?>">
        <i class="fas fa-chevron-left"></i>
    </a>
    <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?>
    <a class="ncc-pg-btn <?= $i == $page ? 'active' : '' ?>"
       href="?controller=nha_cung_cap&action=index&page=<?= $i ?>&keyword=<?= urlencode($_GET['keyword'] ?? '') ?>">
        <?= $i ?>
    </a>
    <?php endfor; ?>
    <a class="ncc-pg-btn <?= ($page >= $totalPages) ? 'disabled' : '' ?>"
       href="?controller=nha_cung_cap&action=index&page=<?= $page+1 ?>&keyword=<?= urlencode($_GET['keyword'] ?? '') ?>">
        <i class="fas fa-chevron-right"></i>
    </a>
</div>
<?php endif; ?>

</div>

<script>
function confirmDeleteNcc(id, name) {
    Swal.fire({
        title: 'Xóa nhà cung cấp?',
        html: 'Bạn sắp xóa <strong>' + name + '</strong>.<br>Điều này có thể ảnh hưởng đến sản phẩm liên quan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff4d6d',
        cancelButtonColor: '#8990aa',
        confirmButtonText: '<i class="fas fa-trash"></i> Xóa',
        cancelButtonText: 'Hủy'
    }).then(function(result) {
        if (result.isConfirmed) {
            window.location.href = 'index.php?controller=nha_cung_cap&action=delete&id=' + id;
        }
    });
}
</script>