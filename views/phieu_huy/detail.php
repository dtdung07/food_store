<?php
declare(strict_types=1);
$slip = $slip ?? [];
$canApprove = $canApprove ?? false;
?>
<section class="page-hero">
    <div>
        <h1>Chi tiết phiếu hủy hàng</h1>
        <p>Mã phiếu: <code><?= e($slip['ma_phieu_huy']) ?></code></p>
    </div>
    <div class="page-actions">
        <a class="button button--ghost" href="<?= e(url_for('phieu-huy', 'index')) ?>">← Quay lại</a>
    </div>
</section>

<!-- Thông tin chung phiếu hủy -->
<section class="panel" style="margin-bottom: 24px;">
    <div class="toolbar" style="margin-bottom: 24px; border-bottom: 1px solid var(--line); padding-bottom: 16px;">
        <div>
            <h3 class="section-title">Thông tin phiếu hủy</h3>
            <p class="section-subtitle">Thông tin chi tiết về người đề xuất, ngày tạo, lý do và trạng thái duyệt.</p>
        </div>
    </div>
    
    <div class="form-grid">
        <div class="field">
            <label>Mã phiếu hủy</label>
            <div style="font-weight: 700; font-size: 16px;"><code><?= e($slip['ma_phieu_huy']) ?></code></div>
        </div>
        <div class="field">
            <label>Ngày tạo</label>
            <div style="font-size: 16px;"><?= e(date('d/m/Y', strtotime($slip['ngay_tao']))) ?></div>
        </div>
        <div class="field">
            <label>Người đề xuất</label>
            <div style="font-size: 16px;"><?= e($slip['ten_nhan_vien'] ?? '—') ?></div>
        </div>
        <div class="field">
            <label>Trạng thái</label>
            <div>
                <span class="badge badge--<?= badge_tone($slip['trang_thai']) ?>"><?= e(status_label($slip['trang_thai'])) ?></span>
            </div>
        </div>
        <div class="field">
            <label>Tổng số lượng hủy</label>
            <div style="font-size: 16px;"><span class="badge badge--neutral"><?= e(number_format((int) $slip['tong_so_luong'])) ?> sản phẩm</span></div>
        </div>
        <div class="field">
            <label>Người duyệt phiếu</label>
            <div style="font-size: 16px;"><?= e($slip['ten_nguoi_duyet'] ?? '—') ?></div>
        </div>
        <?php if (!empty($slip['ngay_duyet'])): ?>
            <div class="field">
                <label>Ngày duyệt</label>
                <div style="font-size: 16px;"><?= e(date('d/m/Y', strtotime($slip['ngay_duyet']))) ?></div>
            </div>
        <?php endif; ?>
        <div class="field field--full">
            <label>Lý do hủy chung / Mô tả</label>
            <div style="font-size: 16px; padding: 12px 16px; background: var(--bg); border-radius: 12px; border: 1px solid var(--line);"><?= e($slip['ly_do_huy_chung'] ?: '(Không có lý do chung)') ?></div>
        </div>
        <?php if ($slip['trang_thai'] === 'TU_CHOI' && !empty($slip['ly_do_tu_choi'])): ?>
            <div class="field field--full">
                <label style="color: var(--red);">Lý do từ chối duyệt</label>
                <div style="font-size: 16px; color: var(--red); padding: 12px 16px; background: var(--red-soft); border-radius: 12px; border: 1px solid var(--red); font-weight: 600;"><?= e($slip['ly_do_tu_choi']) ?></div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Danh sách hàng hóa hủy -->
<section class="table-card table-card--flush" style="margin-bottom: 24px;">
    <div class="table-card__header">
        <div>
            <h3>Danh sách sản phẩm đề xuất hủy</h3>
            <p class="section-subtitle">Danh sách các mặt hàng, lý do chi tiết và chi tiết phân bổ lô hàng hủy.</p>
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
                    <th style="text-align: right; width: 110px;">Số lượng</th>
                    <th style="text-align: right; width: 145px;">Đơn giá</th>
                    <th style="text-align: right; width: 140px;">Thành tiền (Thất thoát)</th>
                    <th>Lý do cụ thể</th>
                    <th>Chi tiết phân bổ Lô (FIFO)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalAmount = 0;
                foreach ($slip['chi_tiet'] as $i => $ct): 
                    $thanhTien = $ct['so_luong'] * $ct['gia_ban'];
                    $totalAmount += $thanhTien;
                ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><code><?= e($ct['ma_hang_hoa']) ?></code></td>
                        <td><strong><?= e($ct['ten_hang_hoa'] ?? $ct['ma_hang_hoa']) ?></strong></td>
                        <td><?= e($ct['don_vi_tinh'] ?? '—') ?></td>
                        <td style="text-align: right;"><strong><?= e(number_format((int) $ct['so_luong'])) ?></strong></td>
                        <td style="text-align: right;"><?= currency($ct['gia_ban']) ?></td>
                        <td style="text-align: right; color: var(--red); font-weight: 700;"><?= currency($thanhTien) ?></td>
                        <td>
                            <?php
                            $reasonLabels = [
                                'HET_HAN' => 'Hết hạn sử dụng',
                                'HONG_THOI' => 'Hư hỏng, thối',
                                'NAT_VO' => 'Dập nát, vỡ',
                                'LOI_QC' => 'Không đạt chất lượng',
                                'KHAC' => 'Khác',
                              ];
                            $detailReason = $ct['ly_do_huy'] ?? 'KHAC';
                            $reasonText = $reasonLabels[$detailReason] ?? $detailReason;
                            ?>
                            <span class="badge badge--neutral"><?= e($reasonText) ?></span>
                        </td>
                        <td>
                            <ul class="lot-distribution-list" style="margin: 0; padding-left: 1.2rem; font-size: 0.9rem; line-height: 1.6;">
                                <?php if (!empty($ct['huy_lo'])): ?>
                                    <?php foreach ($ct['huy_lo'] as $hl): ?>
                                        <li>
                                            Lô <span class="badge badge--neutral"><?= e($hl['ma_lo_hang']) ?></span> 
                                            (HSD: <?= !empty($hl['han_su_dung']) ? e(date('d/m/Y', strtotime($hl['han_su_dung']))) : '—' ?>)
                                            — Hủy: <strong><?= e(number_format((int) $hl['so_luong'])) ?></strong>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size: 0.9rem;">(Chưa phân bổ / Phiếu chưa duyệt)</span>
                                <?php endif; ?>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: var(--surface-soft); font-weight: 700;">
                    <td colspan="4" style="text-align: right; padding: 22px 18px;">Tổng cộng:</td>
                    <td style="text-align: right; padding: 22px 18px; color: var(--text);"><strong><?= e(number_format((int) $slip['tong_so_luong'])) ?></strong></td>
                    <td></td>
                    <td style="text-align: right; padding: 22px 18px; color: var(--red); font-size: 18px;"><strong><?= currency($totalAmount) ?></strong></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</section>

<!-- Các nút duyệt và từ chối nếu có quyền -->
<?php if ($canApprove): ?>
    <section class="panel" style="text-align: right; display: flex; justify-content: flex-end; gap: 16px; padding: 20px 28px;">
        <!-- Form Từ chối -->
        <form id="form-reject-phieu" method="post" action="<?= e(url_for('phieu-huy', 'reject')) ?>" style="display:inline-block; margin: 0;">
            <input type="hidden" name="ma_phieu_huy" value="<?= e($slip['ma_phieu_huy']) ?>">
            <input type="hidden" name="ly_do_tu_choi" id="input-ly-do-tu-choi" value="">
            <button type="button" class="button button--danger" id="btn-reject" style="padding: 15px 32px;">
                <svg viewBox="0 0 24 24" width="18" height="18" style="stroke: currentColor; stroke-width: 2; fill: none; display: inline; vertical-align: middle; margin-right: 8px;"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round"/></svg>
                Từ chối duyệt
            </button>
        </form>

        <!-- Form Đồng ý duyệt -->
        <form id="form-approve-phieu" method="post" action="<?= e(url_for('phieu-huy', 'approve')) ?>" style="display:inline-block; margin: 0;">
            <input type="hidden" name="ma_phieu_huy" value="<?= e($slip['ma_phieu_huy']) ?>">
            <button type="submit" class="button button--primary" id="btn-approve" style="padding: 15px 32px;">
                <svg viewBox="0 0 24 24" width="18" height="18" style="stroke: currentColor; stroke-width: 2; fill: none; display: inline; vertical-align: middle; margin-right: 8px;"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Duyệt phiếu hủy
            </button>
        </form>
    </section>
<?php endif; ?>

<script src="<?= e(asset_url('js/phieu_huy.js')) ?>?v=<?= e(APP_VERSION) ?>"></script>
