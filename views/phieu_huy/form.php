<?php
declare(strict_types=1);
$old = $old ?? [];
$errors = $errors ?? [];
?>
<section class="panel">
    <div class="toolbar">
        <div>
            <h3 class="section-title">Lập phiếu hủy hàng</h3>
            <p class="section-subtitle">Tạo phiếu đề xuất hủy các mặt hàng hư hỏng, dập nát hoặc hết hạn sử dụng.</p>
        </div>
        <a class="button button--ghost" href="<?= e(url_for('phieu-huy', 'index')) ?>">Quay lại</a>
    </div>

    <?php if ($errors !== []): ?>
        <div class="errors" style="margin-top: 20px;">
            <strong>Vui lòng kiểm tra lại thông tin lập phiếu:</strong>
            <ul style="margin: 8px 0 0; padding-left: 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= e(url_for('phieu-huy', 'save')) ?>" id="form-phieu-huy" class="form-grid" style="margin-top: 24px;">
        <div class="field">
            <label for="ma_phieu_huy">Mã phiếu hủy</label>
            <input type="text" id="ma_phieu_huy" value="<?= e($nextId) ?>" readonly style="background: var(--gray-soft); cursor: not-allowed; font-weight: 700;">
        </div>

        <div class="field">
            <label for="nguoi_lap">Người đề xuất</label>
            <input type="text" id="nguoi_lap" value="<?= e($currentUser['ten_nhan_vien'] ?? $currentUser['ten_dang_nhap'] ?? 'Người dùng') ?>" readonly style="background: var(--gray-soft); cursor: not-allowed;">
        </div>

        <div class="field field--full">
            <label for="ly_do_huy_chung">Lý do hủy chung / Mô tả</label>
            <textarea name="ly_do_huy_chung" id="ly_do_huy_chung" rows="2" placeholder="Mô tả chung lý do lập phiếu hủy..."><?= e($old['ly_do_huy_chung'] ?? '') ?></textarea>
        </div>

        <div class="field field--full" style="margin-top: 16px; border-top: 1px solid var(--line); padding-top: 24px;">
            <div class="toolbar" style="margin-bottom: 16px; padding: 0;">
                <div>
                    <h3 class="section-title" style="font-size: 18px;">Chi tiết hàng hóa đề xuất hủy</h3>
                    <p class="section-subtitle">Chọn sản phẩm, nhập số lượng hủy, chọn lý do cụ thể và theo dõi tồn kho hiện tại.</p>
                </div>
                <button type="button" class="button button--secondary" id="btn-add-row" style="padding: 10px 18px;">
                    <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" style="width:16px; height:16px;"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Thêm dòng mới
                </button>
            </div>

            <div class="table-card table-card--flush">
                <div class="table-wrap">
                    <table id="table-details">
                        <thead>
                            <tr>
                                <th>Hàng hóa <span style="color: var(--red);">*</span></th>
                                <th style="width:110px; text-align: right;">SL hủy <span style="color: var(--red);">*</span></th>
                                <th style="width:145px; text-align: right;">Giá bán <span style="color: var(--red);">*</span></th>
                                <th style="width:140px; text-align: right;">Thành tiền (Thất thoát)</th>
                                <th style="width:230px;">Lý do cụ thể <span style="color: var(--red);">*</span></th>
                                <th style="width:200px;">Tồn kho hiện có (Kho | Kệ)</th>
                                <th style="width:50px"></th>
                            </tr>
                        </thead>
                        <tbody id="detail-body">
                            <!-- Rows added via JS -->
                        </tbody>
                        <tfoot>
                            <tr style="background: var(--surface-soft); font-weight: 700;">
                                <td style="text-align: right; padding: 22px 18px;">Tổng cộng:</td>
                                <td style="text-align: right; padding: 22px 18px;"><strong id="total-qty">0</strong></td>
                                <td></td>
                                <td style="text-align: right; padding: 22px 18px; color: var(--red); font-size: 18px;"><strong id="total-amount">0 VND</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="field field--full" style="margin-top: 24px; border-top: 1px solid var(--line); padding-top: 24px; text-align: right;">
            <button type="submit" class="button" id="btn-submit" style="padding: 15px 32px;">
                <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" style="width:18px; height:18px;"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Tạo phiếu hủy (Chờ duyệt)
            </button>
        </div>
    </form>
</section>

<script src="<?= e(asset_url('js/phieu_huy.js')) ?>?v=<?= e(APP_VERSION) ?>"></script>
