<?php
$invoice = $invoice ?? [];
$details = $invoice['chi_tiet'] ?? [];
$formatQty = static function (mixed $value): string {
    $number = (float) $value;
    $formatted = number_format($number, 3, ',', '.');

    return rtrim(rtrim($formatted, '0'), ',');
};
$total = (float) ($invoice['tong_tien'] ?? 0);
$paid = (float) ($invoice['tien_khach_dua'] ?? 0);
$change = max(0, $paid - $total);
?>

<div class="page-header no-print">
    <div class="page-header__left">
        <h2>Hóa đơn thanh toán</h2>
        <p>Mã hóa đơn: <strong><?= e($invoice['ma_hoa_don'] ?? '') ?></strong></p>
    </div>
    <div class="inline-actions">
        <a class="button button--ghost" href="<?= e(url_for('ban-hang', 'pos')) ?>" hx-boost="false">Tạo hóa đơn mới</a>
        <button type="button" class="button button--primary" onclick="window.print()">
            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z"/></svg>
            In hóa đơn
        </button>
    </div>
</div>

<section class="invoice-print card">
    <div class="invoice-print__header">
        <h2>FreshMart Pro</h2>
        <p>Hóa đơn bán hàng</p>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-item__label">Mã hóa đơn</span>
            <span class="detail-item__value"><?= e($invoice['ma_hoa_don'] ?? '') ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-item__label">Ngày tạo</span>
            <span class="detail-item__value">
                <?= !empty($invoice['ngay_tao']) ? e(date('d/m/Y H:i', strtotime((string) $invoice['ngay_tao']))) : '—' ?>
            </span>
        </div>
        <div class="detail-item">
            <span class="detail-item__label">Thu ngân</span>
            <span class="detail-item__value"><?= e($invoice['ten_nhan_vien'] ?? $invoice['ma_nhan_vien'] ?? '—') ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-item__label">Trạng thái</span>
            <span class="detail-item__value">
                <span class="badge badge--<?= e(badge_tone((string) ($invoice['trang_thai'] ?? ''))) ?>">
                    <?= e(status_label((string) ($invoice['trang_thai'] ?? ''))) ?>
                </span>
            </span>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 48px">STT</th>
                    <th>Sản phẩm</th>
                    <th style="width: 120px">Loại</th>
                    <th style="width: 120px">Số lượng</th>
                    <th style="width: 140px">Đơn giá</th>
                    <th style="width: 150px">Thành tiền</th>
                    <th>Phân bổ lô FIFO</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $index => $detail): ?>
                    <tr>
                        <td><?= e((string) ($index + 1)) ?></td>
                        <td>
                            <strong><?= e($detail['ten_hang_hoa'] ?? $detail['ma_hang_hoa'] ?? '') ?></strong><br>
                            <span class="meta-text">
                                <?= e($detail['ma_hang_hoa'] ?? '') ?>
                                <?php if (!empty($detail['ma_vach_quet'])): ?>
                                    / <?= e($detail['ma_vach_quet']) ?>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge--<?= ($detail['loai_ma_vach'] ?? '') === 'TEM_CAN' ? 'warning' : 'neutral' ?>">
                                <?= ($detail['loai_ma_vach'] ?? '') === 'TEM_CAN' ? 'Tem cân' : 'Cố định' ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= e($formatQty($detail['so_luong'] ?? 0)) ?></strong>
                            <?= e($detail['don_vi_tinh'] ?? '') ?>
                            <?php if (($detail['loai_ma_vach'] ?? '') === 'TEM_CAN' && ($detail['trong_luong'] ?? null) !== null): ?>
                                <br><span class="meta-text">TL: <?= e($formatQty($detail['trong_luong'] ?? 0)) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= e(currency($detail['gia_ban'] ?? 0)) ?></td>
                        <td><strong><?= e(currency($detail['tong_tien'] ?? 0)) ?></strong></td>
                        <td>
                            <?php if (!empty($detail['ban_lo'])): ?>
                                <ul class="lot-distribution-list">
                                    <?php foreach ($detail['ban_lo'] as $lot): ?>
                                        <li>
                                            Lô <strong><?= e($lot['ma_lo_hang']) ?></strong>
                                            (HSD: <?= !empty($lot['han_su_dung']) ? e(date('d/m/Y', strtotime((string) $lot['han_su_dung']))) : '—' ?>)
                                            - Bán: <strong><?= e($formatQty($lot['so_luong'])) ?></strong>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <span class="meta-text">Không có chi tiết lô.</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="invoice-print__totals">
        <div><span>Tổng tiền</span><strong><?= e(currency($total)) ?></strong></div>
        <div><span>Phương thức</span><strong><?= e($invoice['phuong_thuc_thanh_toan'] ?? '') ?></strong></div>
        <div><span>Tiền khách đưa</span><strong><?= e(currency($paid)) ?></strong></div>
        <div><span>Tiền thừa</span><strong><?= e(currency($change)) ?></strong></div>
    </div>
</section>
