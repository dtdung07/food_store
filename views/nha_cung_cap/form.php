<?php
declare(strict_types=1);
$supplier = $supplier ?? [];
$errors = $errors ?? [];
$isEdit = $isEdit ?? false;
?>
<section class="panel">
    <div class="toolbar">
        <div>
            <h3 class="section-title"><?= $isEdit ? 'Cập nhật nhà cung cấp' : 'Thêm nhà cung cấp' ?></h3>
            <p class="section-subtitle">Thông tin nhà cung cấp dùng để lưu kho và nhập hàng hóa.</p>
        </div>
        <a class="button button--ghost" href="<?= e(url_for('nha-cung-cap', 'index')) ?>">Quay lại</a>
    </div>

    <?php if ($errors !== []): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= e(url_for('nha-cung-cap', 'save')) ?>" class="form-grid">
        <input type="hidden" name="is_edit" value="<?= $isEdit ? '1' : '0' ?>">
        <input type="hidden" name="original_id" value="<?= e($supplier['ma_nha_cung_cap'] ?? '') ?>">

        <div class="field">
            <label for="ma_nha_cung_cap">Mã nhà cung cấp <span style="color: var(--red);">*</span></label>
            <input id="ma_nha_cung_cap" name="ma_nha_cung_cap" value="<?= e($supplier['ma_nha_cung_cap'] ?? '') ?>" <?= $isEdit ? 'readonly' : '' ?> required placeholder="Ví dụ: NCC_VINAMILK, NCC01">
        </div>

        <div class="field">
            <label for="ten_nha_cung_cap">Tên nhà cung cấp <span style="color: var(--red);">*</span></label>
            <input id="ten_nha_cung_cap" name="ten_nha_cung_cap" value="<?= e($supplier['ten_nha_cung_cap'] ?? '') ?>" required placeholder="Ví dụ: Công ty Cổ phần Sữa Việt Nam">
        </div>

        <div class="field">
            <label for="ten_nguoi_lien_he">Người liên hệ</label>
            <input id="ten_nguoi_lien_he" name="ten_nguoi_lien_he" value="<?= e($supplier['ten_nguoi_lien_he'] ?? '') ?>" placeholder="Ví dụ: Nguyễn Văn A">
        </div>

        <div class="field">
            <label for="so_dien_thoai">Số điện thoại</label>
            <input id="so_dien_thoai" name="so_dien_thoai" value="<?= e($supplier['so_dien_thoai'] ?? '') ?>" placeholder="Ví dụ: 0987654321">
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="<?= e($supplier['email'] ?? '') ?>" placeholder="Ví dụ: ncc@example.com">
        </div>

        <div class="field">
            <label for="trang_thai">Trạng thái hợp tác</label>
            <select id="trang_thai" name="trang_thai">
                <option value="HOAT_DONG" <?= ($supplier['trang_thai'] ?? '') === 'HOAT_DONG' ? 'selected' : '' ?>>Hoạt động</option>
                <option value="VO_HIEU_HOA" <?= ($supplier['trang_thai'] ?? '') === 'VO_HIEU_HOA' ? 'selected' : '' ?>>Vô hiệu hóa</option>
            </select>
        </div>

        <div class="field field--full">
            <label for="dia_chi">Địa chỉ</label>
            <textarea id="dia_chi" name="dia_chi" placeholder="Nhập địa chỉ của nhà cung cấp..."><?= e($supplier['dia_chi'] ?? '') ?></textarea>
        </div>

        <div class="field field--full">
            <button type="submit"><?= $isEdit ? 'Lưu cập nhật' : 'Thêm nhà cung cấp' ?></button>
        </div>
    </form>
</section>
