<?php
declare(strict_types=1);
$export = $export ?? [];
?>
<section class="page-hero">
    <div>
        <h1>Chi tiết phiếu xuất</h1>
        <p>Mã phiếu: <code><?= e($export['ma_phieu_xuat']) ?></code></p>
    </div>
    <div class="page-actions">
        <a class="button button--ghost" href="<?= e(url_for('kho', 'xuat_index')) ?>">← Quay lại</a>
    </div>
</section>

<!-- Thông tin chung phiếu xuất -->
<section class="panel" style="margin-bottom: 24px;">
    <div class="toolbar" style="margin-bottom: 24px; border-bottom: 1px solid var(--line); padding-bottom: 16px;">
        <div>
            <h3 class="section-title">Thông tin phiếu xuất</h3>
            <p class="section-subtitle">Thông tin chi tiết về ngày lập, người thực hiện và tổng số lượng.</p>
        </div>
    </div>
    
    <div class="form-grid">
        <div class="field">
            <label>Mã phiếu xuất</label>
            <div style="font-weight: 700; font-size: 16px;"><code><?= e($export['ma_phieu_xuat']) ?></code></div>
        </div>
        <div class="field">
            <label>Ngày tạo</label>
            <div style="font-size: 16px;"><?= e(date('d/m/Y', strtotime($export['ngay_tao']))) ?></div>
        </div>
        <div class="field">
            <label>Người lập phiếu</label>
            <div style="font-size: 16px;"><?= e($export['ten_nhan_vien'] ?? '—') ?></div>
        </div>
        <div class="field">
            <label>Tổng số lượng xuất</label>
            <div style="font-size: 16px;"><span class="badge badge--neutral"><?= e(number_format((int) $export['tong_so_luong'])) ?> sản phẩm</span></div>
        </div>
        <?php if (!empty($export['ghi_chu'])): ?>
            <div class="field field--full">
                <label>Ghi chú</label>
                <div style="font-size: 16px; color: var(--muted-strong); padding: 12px 16px; background: var(--bg); border-radius: 12px;"><?= e($export['ghi_chu']) ?></div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Danh sách hàng hóa đã xuất -->
<section class="table-card table-card--flush">
    <div class="table-card__header">
        <div>
            <h3>Chi tiết sản phẩm & Lô xuất</h3>
            <p class="section-subtitle">Danh sách các mặt hàng, đơn vị tính và chi tiết phân bổ lô hàng theo FIFO.</p>
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
                    <th style="text-align: right; width: 150px;">Tổng SL xuất</th>
                    <th>Chi tiết phân bổ Lô (FIFO)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($export['chi_tiet'] as $i => $ct): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><code><?= e($ct['ma_hang_hoa']) ?></code></td>
                        <td><strong><?= e($ct['ten_hang_hoa'] ?? $ct['ma_hang_hoa']) ?></strong></td>
                        <td><?= e($ct['don_vi_tinh'] ?? '—') ?></td>
                        <td style="text-align: right;"><strong><?= e(number_format((int) $ct['so_luong'])) ?></strong></td>
                        <td>
                            <ul class="lot-distribution-list" style="margin: 0; padding-left: 1.2rem; font-size: 0.9rem; line-height: 1.6;">
                                <?php if (!empty($ct['xuat_lo'])): ?>
                                    <?php foreach ($ct['xuat_lo'] as $xl): ?>
                                        <li>
                                            Lô <span class="badge badge--neutral"><?= e($xl['ma_lo_hang']) ?></span> 
                                            (HSD: <?= !empty($xl['han_su_dung']) ? e(date('d/m/Y', strtotime($xl['han_su_dung']))) : '—' ?>)
                                            — Xuất: <strong><?= e(number_format((int) $xl['so_luong'])) ?></strong>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="text-muted">Không có chi tiết lô.</li>
                                <?php endif; ?>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: var(--surface-soft); font-weight: 700;">
                    <td colspan="4" style="text-align: right; padding: 22px 18px;">Tổng cộng:</td>
                    <td style="text-align: right; padding: 22px 18px; color: var(--text);"><strong><?= e(number_format((int) $export['tong_so_luong'])) ?></strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</section>
