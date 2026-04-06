<?php
/** Danh sách danh mục — $data['danhMucs'] */
$danhMucs = $data['danhMucs'] ?? [];
?>
<h2 class="mb-3"><i class="bi bi-tags"></i> Quản lý Danh mục</h2>
<p>
    <a href="<?= BASE_URL ?>?c=danh-muc&a=form" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Thêm danh mục
    </a>
</p>
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle bg-white">
        <thead class="table-dark">
            <tr>
                <th>Mã</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th width="160">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($danhMucs)): ?>
                <tr><td colspan="4" class="text-center text-muted">Chưa có danh mục.</td></tr>
            <?php else: ?>
                <?php foreach ($danhMucs as $dm): ?>
                    <tr>
                        <td><?= htmlspecialchars($dm['ma_danh_muc'] ?? '') ?></td>
                        <td><?= htmlspecialchars($dm['ten_danh_muc'] ?? '') ?></td>
                        <td><?= htmlspecialchars($dm['mo_ta'] ?? '') ?></td>
                        <td>
                            <a class="btn btn-outline-primary btn-sm" href="<?= BASE_URL ?>?c=danh-muc&a=form&id=<?= urlencode($dm['ma_danh_muc']) ?>">Sửa</a>
                            <a class="btn btn-outline-danger btn-sm" href="<?= BASE_URL ?>?c=danh-muc&a=delete&id=<?= urlencode($dm['ma_danh_muc']) ?>"
                               onclick="return confirm('Xóa danh mục này?');">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
