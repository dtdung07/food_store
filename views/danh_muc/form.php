<?php
declare(strict_types=1);
$category = $category ?? [];
$errors = $errors ?? [];
$isEdit = $isEdit ?? false;
?>
<section class="panel">
    <div class="toolbar">
        <div>
            <h3 class="section-title"><?= $isEdit ? 'Cập nhật danh mục' : 'Thêm danh mục' ?></h3>
            <p class="section-subtitle">Thông tin danh mục dùng để phân loại hàng hóa trong hệ thống.</p>
        </div>
        <a class="button button--ghost" href="<?= e(url_for('danh-muc', 'index')) ?>">Quay lại</a>
    </div>

    <?php if ($errors !== []): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= e(url_for('danh-muc', 'save')) ?>" class="form-grid">
        <input type="hidden" name="is_edit" value="<?= $isEdit ? '1' : '0' ?>">
        
        <div class="field">
            <label for="ma_danh_muc">Mã danh mục <span style="color: var(--red);">*</span></label>
            <input id="ma_danh_muc" name="ma_danh_muc" value="<?= $isEdit ? e($category['ma_danh_muc'] ?? '') : e($nextId) ?>" readonly style="background: var(--gray-soft); cursor: not-allowed; font-weight: bold;">
        </div>

        <div class="field">
            <label for="ten_danh_muc">Tên danh mục <span style="color: var(--red);">*</span></label>
            <input id="ten_danh_muc" name="ten_danh_muc" value="<?= e($category['ten_danh_muc'] ?? '') ?>" required placeholder="Ví dụ: Đồ uống, Sữa trứng">
        </div>

        <div class="field field--full">
            <label for="mo_ta">Mô tả</label>
            <textarea id="mo_ta" name="mo_ta" placeholder="Mô tả ngắn gọn về danh mục..."><?= e($category['mo_ta'] ?? '') ?></textarea>
        </div>

        <div class="field field--full">
            <button type="submit"><?= $isEdit ? 'Lưu cập nhật' : 'Thêm danh mục' ?></button>
        </div>
    </form>
</section>
