<?php
declare(strict_types=1);
$old = $old ?? [];
$errors = $errors ?? [];
?>
<section class="panel">
    <div class="toolbar">
        <div>
            <h3 class="section-title">Lập phiếu xuất kho ra quầy</h3>
            <p class="section-subtitle">Xuất hàng từ kho chính ra kệ trưng bày bán hàng.</p>
        </div>
        <a class="button button--ghost" href="<?= e(url_for('kho', 'xuat_index')) ?>">Quay lại</a>
    </div>

    <?php if ($errors !== []): ?>
        <div class="errors" style="margin-top: 20px;">
            <strong>Vui lòng kiểm tra lại thông tin xuất kho:</strong>
            <ul style="margin: 8px 0 0; padding-left: 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= e(url_for('kho', 'xuat_save')) ?>" id="form-xuat-kho" class="form-grid" style="margin-top: 24px;">
        <div class="field">
            <label for="ma_phieu_xuat">Mã phiếu xuất</label>
            <input type="text" id="ma_phieu_xuat" value="<?= e($nextId ?? '') ?>" readonly style="background: var(--gray-soft); cursor: not-allowed; font-weight: bold;">
        </div>
        
        <div class="field">
            <label for="nguoi_lap">Người lập phiếu</label>
            <input type="text" id="nguoi_lap" value="<?= e($currentUser['ten_nhan_vien'] ?? $currentUser['ten_dang_nhap'] ?? 'Người dùng') ?>" readonly style="background: var(--gray-soft); cursor: not-allowed;">
        </div>

        <div class="field field--full">
            <label for="ghi_chu">Ghi chú</label>
            <textarea name="ghi_chu" id="ghi_chu" rows="2" placeholder="Ghi chú thêm về phiếu xuất..."><?= e($old['ghi_chu'] ?? '') ?></textarea>
        </div>

        <div class="field field--full" style="margin-top: 16px; border-top: 1px solid var(--line); padding-top: 24px;">
            <div class="toolbar" style="margin-bottom: 16px; padding: 0;">
                <div>
                    <h3 class="section-title" style="font-size: 18px;">Chi tiết hàng hóa xuất</h3>
                    <p class="section-subtitle">Chọn sản phẩm, số lượng xuất và xem gợi ý phân bổ lô hàng theo FIFO.</p>
                </div>
                <button type="button" class="button button--secondary" id="btn-add-row" style="padding: 10px 18px;">
                    <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" style="width:16px; height:16px;"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Thêm dòng mới
                </button>
            </div>

            <div id="detail-body" style="display: flex; flex-direction: column; gap: 12px;">
                <!-- Các dòng sẽ được thêm bằng JS -->
            </div>

            <div style="margin-top: 16px; padding: 18px 24px; background: var(--surface-soft); border-radius: 16px; display: flex; justify-content: flex-end; align-items: center; gap: 40px; font-weight: 700; font-size: 15px;">
                <span>Tổng số lượng xuất: <strong id="total-qty" style="color: var(--text); font-size: 16px; margin-left: 8px;">0</strong></span>
            </div>
        </div>

        <div class="field field--full" style="margin-top: 24px; border-top: 1px solid var(--line); padding-top: 24px; text-align: right;">
            <button type="submit" class="button" id="btn-submit" style="padding: 15px 32px;">
                <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" style="width:18px; height:18px;"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Hoàn tất Phiếu Xuất
            </button>
        </div>
    </form>
</section>

<script src="<?= e(asset_url('js/kho.js')) ?>?v=<?= e(APP_VERSION) ?>"></script>
