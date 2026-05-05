<?php
declare(strict_types=1);
$receipt = $receipt ?? [];

$formatQty = static function (mixed $value): string {
    $number = (float) $value;
    $formatted = number_format($number, 3, ',', '.');
    return rtrim(rtrim($formatted, '0'), ',');
};
?>
<section class="page-hero">
    <div>
        <h1>Chi tiết phiếu nhập</h1>
        <p>Mã phiếu: <code><?= e($receipt['ma_phieu_nhap']) ?></code></p>
    </div>
    <div class="page-actions">
        <a class="button button--ghost" href="<?= e(url_for('kho', 'nhap_index')) ?>">← Quay lại</a>
    </div>
</section>

<!-- Thông tin chung phiếu nhập -->
<section class="panel" style="margin-bottom: 24px;">
    <div class="toolbar" style="margin-bottom: 24px; border-bottom: 1px solid var(--line); padding-bottom: 16px;">
        <div>
            <h3 class="section-title">Thông tin phiếu nhập</h3>
            <p class="section-subtitle">Thông tin chi tiết về nhà cung cấp, ngày lập và người thực hiện.</p>
        </div>
    </div>
    
    <div class="form-grid">
        <div class="field">
            <label>Mã phiếu nhập</label>
            <div style="font-weight: 700; font-size: 16px;"><code><?= e($receipt['ma_phieu_nhap']) ?></code></div>
        </div>
        <div class="field">
            <label>Ngày tạo</label>
            <div style="font-size: 16px;"><?= e(date('d/m/Y H:i', strtotime($receipt['ngay_tao']))) ?></div>
        </div>
        <div class="field">
            <label>Nhà cung cấp</label>
            <div style="font-weight: 600; font-size: 16px; color: var(--blue);"><?= e($receipt['ten_nha_cung_cap'] ?? '—') ?></div>
        </div>
        <div class="field">
            <label>Người lập phiếu</label>
            <div style="font-size: 16px;"><?= e($receipt['ten_nhan_vien'] ?? '—') ?></div>
        </div>
        <div class="field">
            <label>Tổng số lượng nhập</label>
            <div style="font-size: 16px;"><span class="badge badge--neutral"><?= e($formatQty($receipt['tong_so_luong'])) ?> sản phẩm</span></div>
        </div>
        <div class="field">
            <label>Tổng giá trị nhập</label>
            <div style="font-weight: 800; font-size: 18px; color: var(--red);"><?= currency($receipt['tong_tien']) ?></div>
        </div>
        <?php if (!empty($receipt['ghi_chu'])): ?>
            <div class="field field--full">
                <label>Ghi chú</label>
                <div style="font-size: 16px; color: var(--muted-strong); padding: 12px 16px; background: var(--bg); border-radius: 12px;"><?= e($receipt['ghi_chu']) ?></div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Danh sách hàng hóa nhập -->
<section class="table-card table-card--flush">
    <div class="table-card__header">
        <div>
            <h3>Chi tiết sản phẩm & Lô hàng</h3>
            <p class="section-subtitle">Danh sách các mặt hàng, thông tin hạn sử dụng và thành tiền chi tiết.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 65px;">STT</th>
                    <th>Mã hàng</th>
                    <th>Tên hàng hóa</th>
                    <th>ĐVT</th>
                    <th>Mã Lô</th>
                    <th>Ngày SX</th>
                    <th>HSD</th>
                    <th style="text-align: right; width: 110px;">Số lượng</th>
                    <th style="text-align: right; width: 145px;">Đơn giá nhập</th>
                    <th style="text-align: right; width: 140px;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($receipt['chi_tiet'] as $i => $ct): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><code><?= e($ct['ma_hang_hoa']) ?></code></td>
                        <td><strong><?= e($ct['ten_hang_hoa']) ?></strong></td>
                        <td><?= e($ct['don_vi_tinh']) ?></td>
                        <td><span class="badge badge--neutral"><?= e($ct['ma_lo_hang'] ?? '—') ?></span></td>
                        <td><?= !empty($ct['ngay_san_xuat']) ? e(date('d/m/Y', strtotime($ct['ngay_san_xuat']))) : '—' ?></td>
                        <td><?= !empty($ct['han_su_dung']) ? e(date('d/m/Y', strtotime($ct['han_su_dung']))) : '—' ?></td>
                        <td style="text-align: right;"><strong><?= e($formatQty($ct['so_luong'])) ?></strong></td>
                        <td style="text-align: right;"><?= currency($ct['don_gia_nhap']) ?></td>
                        <td style="text-align: right; color: var(--blue); font-weight: 700;"><?= currency($ct['so_luong'] * $ct['don_gia_nhap']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: var(--surface-soft); font-weight: 700;">
                    <td colspan="7" style="text-align: right; padding: 22px 18px;">Tổng cộng:</td>
                    <td style="text-align: right; padding: 22px 18px; color: var(--text);"><strong><?= e($formatQty($receipt['tong_so_luong'])) ?></strong></td>
                    <td></td>
                    <td style="text-align: right; padding: 22px 18px; color: var(--red); font-size: 18px;"><strong><?= currency($receipt['tong_tien']) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</section>
