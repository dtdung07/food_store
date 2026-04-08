<?php
$employee = $employee ?? [];
$errors = $errors ?? [];
$roles = $roles ?? [];
$isEdit = $isEdit ?? false;
?>
<section class="panel">
    <div class="toolbar">
        <div>
            <h3 class="section-title"><?= $isEdit ? 'Cập nhật nhân viên' : 'Thêm nhân viên' ?></h3>
            <p class="section-subtitle">Thông tin cơ bản được lưu trực tiếp vào bảng nhân_vien.</p>
        </div>
        <a class="button button--ghost" href="<?= e(url_for('nhan-vien', 'index')) ?>">Quay lại</a>
    </div>

    <?php if ($errors !== []): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= e(url_for('nhan-vien', 'save')) ?>" class="form-grid">
        <input type="hidden" name="is_edit" value="<?= $isEdit ? '1' : '0' ?>">

        <div class="field">
            <label for="ma_nhan_vien">Mã nhân viên</label>
            <input id="ma_nhan_vien" name="ma_nhan_vien" value="<?= e($employee['ma_nhan_vien'] ?? '') ?>" <?= $isEdit ? 'readonly' : '' ?> required>
        </div>

        <div class="field">
            <label for="ten_nhan_vien">Tên nhân viên</label>
            <input id="ten_nhan_vien" name="ten_nhan_vien" value="<?= e($employee['ten_nhan_vien'] ?? '') ?>" required>
        </div>

        <div class="field">
            <label for="gioi_tinh">Giới tính</label>
            <select id="gioi_tinh" name="gioi_tinh">
                <?php $gender = $employee['gioi_tinh'] ?? ''; ?>
                <option value="">Chọn giới tính</option>
                <option value="Nam" <?= $gender === 'Nam' ? 'selected' : '' ?>>Nam</option>
                <option value="Nu" <?= $gender === 'Nu' ? 'selected' : '' ?>>Nữ</option>
                <option value="Khac" <?= $gender === 'Khac' ? 'selected' : '' ?>>Khác</option>
            </select>
        </div>

        <div class="field">
            <label for="ngay_sinh">Ngày sinh</label>
            <input id="ngay_sinh" name="ngay_sinh" type="date" value="<?= e($employee['ngay_sinh'] ?? '') ?>">
        </div>

        <div class="field">
            <label for="so_dien_thoai">Số điện thoại</label>
            <input id="so_dien_thoai" name="so_dien_thoai" value="<?= e($employee['so_dien_thoai'] ?? '') ?>">
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="<?= e($employee['email'] ?? '') ?>">
        </div>

        <div class="field">
            <label for="ma_chuc_vu">Chức vụ</label>
            <select id="ma_chuc_vu" name="ma_chuc_vu" required>
                <option value="">Chọn chức vụ</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= e($role['ma_chuc_vu']) ?>" <?= ($employee['ma_chuc_vu'] ?? '') === $role['ma_chuc_vu'] ? 'selected' : '' ?>>
                        <?= e($role['ten_chuc_vu']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field field--full">
            <label for="dia_chi">Địa chỉ</label>
            <textarea id="dia_chi" name="dia_chi"><?= e($employee['dia_chi'] ?? '') ?></textarea>
        </div>

        <div class="field field--full">
            <button type="submit"><?= $isEdit ? 'Lưu cập nhật' : 'Thêm nhân viên' ?></button>
        </div>
    </form>
</section>
