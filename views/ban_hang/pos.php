<div
    id="pos-root"
    class="pos-layout"
    hx-boost="false"
    data-scan-url="<?= e(url_for('ban-hang', 'scan_barcode')) ?>"
    data-checkout-url="<?= e(url_for('ban-hang', 'checkout')) ?>"
    data-beep-url="<?= e(asset_url('audio/freesound_community-store-scanner-beep-90395.mp3')) ?>"
    data-wrong-beep-url="<?= e(asset_url('audio/freesound_community-wronganswer-37702.mp3')) ?>"
>
    <section class="pos-main">
        <!-- <div class="page-header">
            <div class="page-header__left">
                <h2>POS thanh toán</h2>
                <p>Quét mã vạch cố định hoặc tem cân, cập nhật giỏ hàng theo thời gian thực.</p>
            </div>
            <button type="button" class="button button--ghost" id="clear-cart">
                <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                Hủy hóa đơn
            </button>
        </div> -->

        <!-- Scanner card: camera full-width, controls below -->
        <div class="pos-scanner card">
            <!-- Camera area -->
            <div id="barcode-reader" class="pos-camera"></div>

            <!-- Controls bar below camera -->
            <div class="pos-scanner__controls">
                <button type="button" class="button button--secondary" id="toggle-camera" style="flex-shrink: 0; white-space: nowrap;">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" style="width:18px;height:18px;"><path d="M4 7h4l2-3h4l2 3h4v13H4V7zm8 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" fill="currentColor"/></svg>
                    Bật camera
                </button>

                <form id="manual-scan-form" class="pos-manual-scan">
                    <input type="text" id="manual-barcode" class="input" placeholder="Nhập hoặc quét mã vạch vào đây..." autocomplete="off">
                    <button type="submit" class="button button--primary">
                        <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" style="width:16px;height:16px;"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                        Thêm
                    </button>
                </form>

                <div id="pos-status" class="pos-status" role="status" aria-live="polite"></div>

                <!-- <p class="form-note" style="margin: 0;">
                    <svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" style="display:inline;vertical-align:middle;margin-right:4px;"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M12 8v4m0 4h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    Camera cần HTTPS hoặc localhost. Dùng ô nhập thủ công khi trình duyệt không cấp quyền camera.
                </p> -->
            </div>
        </div>

        <!-- Cart table -->
        <div class="card pos-cart-card">
            <div class="form-section__header">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <h3 class="form-section__title">Chi tiết hóa đơn</h3>
                    <span class="badge badge--neutral" id="cart-count">0 dòng</span>
                </div>
                <button type="button" class="button button--ghost button--sm" id="clear-cart" style="color: var(--red); border-color: var(--line-strong);">
                    <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" style="width: 16px; height: 16px; fill: currentColor; stroke: none;"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                    Hủy hóa đơn
                </button>
            </div>

            <div class="table-wrap">
                <table class="data-table pos-cart-table">
                    <thead>
                        <tr>
                            <th style="width: 48px">STT</th>
                            <th>Sản phẩm</th>
                            <th style="width: 140px">Loại</th>
                            <th style="width: 150px">Số lượng</th>
                            <th style="width: 150px">Đơn giá</th>
                            <th style="width: 160px">Thành tiền</th>
                            <th style="width: 56px"></th>
                        </tr>
                    </thead>
                    <tbody id="cart-body">
                        <tr>
                            <td colspan="7" class="empty-state">Chưa có sản phẩm trong hóa đơn.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <aside class="pos-payment card">
        <h3 class="form-section__title" style="margin-bottom: 20px;">Thanh toán</h3>

        <div class="pos-total-line">
            <span>Tạm tính</span>
            <strong id="subtotal">0 VND</strong>
        </div>
        <div class="pos-total-line pos-total-line--large" style="margin-bottom: 24px;">
            <span>Tổng tiền</span>
            <strong id="grand-total">0 VND</strong>
        </div>

        <div class="form-group">
            <label class="form-label" for="payment-method">Phương thức thanh toán</label>
            <select id="payment-method" class="input">
                <option value="Tiền mặt">Tiền mặt</option>
                <option value="Chuyển khoản">Chuyển khoản</option>
                <option value="Thẻ">Thẻ ngân hàng</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="customer-paid">Tiền khách đưa (VND)</label>
            <input type="number" id="customer-paid" class="input" min="0" step="1000" value="0" placeholder="Nhập số tiền khách đưa...">
        </div>

        <div class="pos-total-line" style="margin-top: 8px;">
            <span>Tiền thừa</span>
            <strong id="change-due" style="color: var(--green); font-size: 22px;">0 VND</strong>
        </div>

        <button type="button" class="button button--primary button--full button--lg" id="checkout-button" style="margin-top: 20px;">
            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Hoàn tất thanh toán
        </button>
    </aside>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script src="<?= e(asset_url('js/pos.js')) ?>?v=<?= e(APP_VERSION) ?>"></script>
