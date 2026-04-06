<?php
/** Form thêm/sửa danh mục */
$dm     = $data['danhMuc'] ?? null;
$isEdit = !empty($dm);
?>
<h2 class="mb-3"><i class="bi bi-tag"></i> <?= $isEdit ? 'Sửa' : 'Thêm' ?> Danh mục</h2>
<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= BASE_URL ?>?c=danh-muc&a=save" class="row g-3">
            <?php if ($isEdit): ?>
                <input type="hidden" name="is_edit" value="1">
            <?php endif; ?>
            <div class="col-md-4">
                <label class="form-label">Mã danh mục</label>
                <input type="text" name="ma_danh_muc" class="form-control" required
                       value="<?= htmlspecialchars($dm['ma_danh_muc'] ?? '') ?>" <?= $isEdit ? 'readonly' : '' ?>>
            </div>
            <div class="col-md-8">
                <label class="form-label">Tên danh mục</label>
                <input type="text" name="ten_danh_muc" class="form-control" required
                       value="<?= htmlspecialchars($dm['ten_danh_muc'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Mô tả</label>
                <textarea name="mo_ta" class="form-control" rows="3"><?= htmlspecialchars($dm['mo_ta'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="<?= BASE_URL ?>?c=danh-muc" class="btn btn-outline-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>
