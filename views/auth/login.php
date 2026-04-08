<?php
$errors = $errors ?? [];
$input = $input ?? [];
?>
<section class="login-screen">
    <div class="login-card">
        <p class="eyebrow">Hệ thống nội bộ</p>
        <h1 class="page-title">Đăng nhập vào module được giao</h1>
        <p class="section-subtitle">
            Phạm vi đã được dựng sẵn cho báo cáo, nhân viên và người dùng.
        </p>

        <?php if ($errors !== []): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <div><?= e($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(url_for('auth', 'login')) ?>" class="form-grid">
            <div class="field field--full">
                <label for="ten_dang_nhap">Tên đăng nhập</label>
                <input id="ten_dang_nhap" name="ten_dang_nhap" value="<?= e($input['ten_dang_nhap'] ?? '') ?>" required>
            </div>

            <div class="field field--full">
                <label for="password">Mật khẩu</label>
                <input id="password" name="password" type="password" required>
            </div>

            <div class="field field--full">
                <button type="submit">Đăng nhập</button>
            </div>
        </form>

        <p class="login-card__note">
            Tài khoản mẫu theo dữ liệu seed: <strong>admin</strong> / <strong>password</strong>.
        </p>
    </div>
</section>
