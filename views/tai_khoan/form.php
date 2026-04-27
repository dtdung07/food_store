<?php
$account = $account ?? [];
$errors = $errors ?? [];
$roles = $roles ?? [];
$employees = $employees ?? [];
$isEdit = $isEdit ?? false;
?>
<section class="panel">
    <div class="toolbar">
        <div>
            <h3 class="section-title"><?= $isEdit ? 'Cập nhật tài khoản' : 'Thêm tài khoản' ?></h3>
            <p class="section-subtitle">Tạo người dùng mới và gắn vai trò truy cập theo chức vụ.</p>
        </div>
        <a class="button button--ghost" href="<?= e(url_for('tai-khoan', 'index')) ?>">Quay lại</a>
    </div>

    <?php if ($errors !== []): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!$isEdit && $employees === []): ?>
        <div class="empty-state">Không còn nhân viên nào trong danh sách có thể cấp tài khoản. Hãy tạo nhân viên mới hoặc sửa tài khoản hiện có.</div>
    <?php endif; ?>

    <form method="post" action="<?= e(url_for('tai-khoan', 'save')) ?>" class="form-grid">
        <input type="hidden" name="ma_tai_khoan" value="<?= e($account['ma_tai_khoan'] ?? 0) ?>">

        <div class="field">
            <label for="ten_dang_nhap">Tên đăng nhập</label>
            <input id="ten_dang_nhap" name="ten_dang_nhap" value="<?= e($account['ten_dang_nhap'] ?? '') ?>" required>
        </div>

        <div class="field">
            <label for="password">Mật khẩu <?= $isEdit ? '(để trống nếu không đổi)' : '' ?></label>
            <input id="password" name="password" type="password" <?= $isEdit ? '' : 'required' ?>>
        </div>

        <div class="field">
            <label for="ma_nhan_vien">Nhân viên</label>
            <select id="ma_nhan_vien" name="ma_nhan_vien" required>
                <option value="">Chọn nhân viên</option>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?= e($employee['ma_nhan_vien']) ?>" <?= ($account['ma_nhan_vien'] ?? '') === $employee['ma_nhan_vien'] ? 'selected' : '' ?>>
                        <?= e($employee['ten_nhan_vien']) ?> (<?= e($employee['ma_nhan_vien']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label for="vai_tro">Vai trò</label>
            <input id="vai_tro" type="text" value="<?= e($account['ten_chuc_vu'] ?? 'Sẽ tự động hiển thị theo nhân viên') ?>" readonly style="background-color: #f3f4f6; color: #6b7280; cursor: not-allowed;">
            <!-- <p style="font-size: 0.8rem; color: #6b7280; margin-top: 0.25rem;">Vai trò truy cập được tự động lấy từ chức vụ của nhân viên liên kết.</p> -->
        </div>

        <div class="field">
            <label for="trang_thai">Trạng thái</label>
            <select id="trang_thai" name="trang_thai">
                <?php $status = $account['trang_thai'] ?? 'HOAT_DONG'; ?>
                <option value="HOAT_DONG" <?= $status === 'HOAT_DONG' ? 'selected' : '' ?>>Hoạt động</option>
                <option value="VO_HIEU_HOA" <?= $status === 'VO_HIEU_HOA' ? 'selected' : '' ?>>Vô hiệu hóa</option>
            </select>
        </div>

        <div class="field field--full">
            <button type="submit"><?= $isEdit ? 'Lưu cập nhật' : 'Thêm tài khoản' ?></button>
        </div>
    </form>
</section>
