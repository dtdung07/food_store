<?php
$errors = $errors ?? [];
$input = $input ?? [];
?>
<section class="login-screen">
    <div class="login-card">
        <h1 class="page-title">Đăng nhập</h1>

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
            Tài khoản admin mẫu: <strong>admin</strong> / <strong>password</strong>.
        </p>
    </div>
</section>
