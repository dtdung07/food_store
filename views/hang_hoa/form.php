<?php
declare(strict_types=1);
$hangHoa = $hangHoa ?? [];
$errors = $errors ?? [];
$isEdit = $isEdit ?? false;
$danhMucs = $danhMucs ?? [];
$nhaCungCaps = $nhaCungCaps ?? [];
$loHangs = $loHangs ?? [];
?>
<section class="panel">
    <div class="toolbar">
        <div>
            <h3 class="section-title"><?= $isEdit ? 'Cập nhật hàng hóa' : 'Thêm hàng hóa' ?></h3>
            <p class="section-subtitle">Thông tin sản phẩm, đơn giá bán và quản lý số lượng theo các lô hàng.</p>
        </div>
        <a class="button button--ghost" href="<?= e(url_for('hang-hoa', 'index')) ?>">Quay lại</a>
    </div>

    <?php if ($errors !== []): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= e(url_for('hang-hoa', 'save')) ?>" class="form-grid">
        <input type="hidden" name="is_edit" value="<?= $isEdit ? '1' : '0' ?>">
        <input type="hidden" name="original_id" value="<?= e($hangHoa['ma_hang_hoa'] ?? '') ?>">

        <div class="field">
            <label for="ma_hang_hoa">Mã hàng hóa <span style="color: var(--red);">*</span></label>
            <input id="ma_hang_hoa" name="ma_hang_hoa" value="<?= e($hangHoa['ma_hang_hoa'] ?? '') ?>" <?= $isEdit ? 'readonly' : '' ?> required placeholder="Ví dụ: HH001, COCA, ...">
        </div>

        <div class="field">
            <label for="ten_hang_hoa">Tên hàng hóa <span style="color: var(--red);">*</span></label>
            <input id="ten_hang_hoa" name="ten_hang_hoa" value="<?= e($hangHoa['ten_hang_hoa'] ?? '') ?>" required placeholder="Ví dụ: Nước ngọt Coca-Cola 320ml">
        </div>

        <div class="field">
            <label for="don_vi_tinh">Đơn vị tính <span style="color: var(--red);">*</span></label>
            <select id="don_vi_tinh" name="don_vi_tinh" required>
                <option value="">Chọn đơn vị tính</option>
                <?php
                $dvts = ['Cái', 'Hộp', 'Kg', 'Chai', 'Lon', 'Gói', 'Khay', 'Túi'];
                $currentDvt = $hangHoa['don_vi_tinh'] ?? '';
                foreach ($dvts as $dvt):
                ?>
                    <option value="<?= e($dvt) ?>" <?= $currentDvt === $dvt ? 'selected' : '' ?>><?= e($dvt) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label for="gia_ban">Giá bán <span style="color: var(--red);">*</span></label>
            <input id="gia_ban" name="gia_ban" type="number" step="any" min="0" value="<?= e((string)($hangHoa['gia_ban'] ?? '')) ?>" required placeholder="Ví dụ: 10000">
        </div>

        <div class="field">
            <label for="ma_vach">Mã vạch</label>
            <input id="ma_vach" name="ma_vach" value="<?= e($hangHoa['ma_vach'] ?? '') ?>" placeholder="Nhập mã vạch sản phẩm...">
        </div>

        <div class="field">
            <label for="ma_danh_muc">Danh mục</label>
            <select id="ma_danh_muc" name="ma_danh_muc">
                <option value="">Chọn danh mục</option>
                <?php foreach ($danhMucs as $dm): ?>
                    <option value="<?= e($dm['ma_danh_muc']) ?>" <?= ($hangHoa['ma_danh_muc'] ?? '') === $dm['ma_danh_muc'] ? 'selected' : '' ?>>
                        <?= e($dm['ten_danh_muc']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label for="ma_nha_cung_cap">Nhà cung cấp</label>
            <select id="ma_nha_cung_cap" name="ma_nha_cung_cap">
                <option value="">Chọn nhà cung cấp</option>
                <?php foreach ($nhaCungCaps as $ncc): ?>
                    <option value="<?= e($ncc['ma_nha_cung_cap']) ?>" <?= ($hangHoa['ma_nha_cung_cap'] ?? '') === $ncc['ma_nha_cung_cap'] ? 'selected' : '' ?>>
                        <?= e($ncc['ten_nha_cung_cap']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label for="trang_thai">Trạng thái kinh doanh</label>
            <select id="trang_thai" name="trang_thai">
                <option value="DANG_KINH_DOANH" <?= ($hangHoa['trang_thai'] ?? '') === 'DANG_KINH_DOANH' ? 'selected' : '' ?>>Đang kinh doanh</option>
                <option value="NGUNG_KINH_DOANH" <?= ($hangHoa['trang_thai'] ?? '') === 'NGUNG_KINH_DOANH' ? 'selected' : '' ?>>Ngừng kinh doanh</option>
            </select>
        </div>

        <?php if ($isEdit && $loHangs !== []): ?>
            <!-- Quản lý số lượng lô hàng -->
            <div class="field field--full" style="margin-top: 24px;">
                <h3 class="section-title">Quản lý số lượng tồn kho theo lô hàng</h3>
                <p class="section-subtitle">Chỉnh sửa số lượng trong kho và trên kệ trực tiếp cho từng lô hàng của sản phẩm này.</p>
                <div class="table-wrap" style="margin-top: 14px; border: 1px solid var(--line); border-radius: 18px; overflow: hidden;">
                    <table>
                        <thead>
                            <tr>
                                <th>Mã lô</th>
                                <th>Hạn sử dụng</th>
                                <th>Số lượng trong kho</th>
                                <th>Số lượng trên kệ</th>
                                <th>Tổng tồn kho</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loHangs as $lot): ?>
                                <tr>
                                    <td><code><?= e($lot['ma_lo_hang']) ?></code></td>
                                    <td>
                                        <?php 
                                        $exp = date('d/m/Y', strtotime((string)$lot['han_su_dung']));
                                        $days = (strtotime((string)$lot['han_su_dung']) - time()) / (60 * 60 * 24);
                                        $style = $days < 7 ? 'color: var(--red); font-weight: 700;' : '';
                                        ?>
                                        <span style="<?= $style ?>"><?= e($exp) ?></span>
                                    </td>
                                    <td>
                                        <input type="number" min="0" name="lo[<?= e($lot['ma_lo_hang']) ?>][so_luong_trong_kho]" value="<?= e((string)$lot['so_luong_trong_kho']) ?>" style="padding: 8px 12px; width: 120px; border-radius: 12px;">
                                    </td>
                                    <td>
                                        <input type="number" min="0" name="lo[<?= e($lot['ma_lo_hang']) ?>][so_luong_tren_ke]" value="<?= e((string)$lot['so_luong_tren_ke']) ?>" style="padding: 8px 12px; width: 120px; border-radius: 12px;">
                                    </td>
                                    <td>
                                        <strong id="total-<?= e($lot['ma_lo_hang']) ?>"><?= e((string)($lot['so_luong_trong_kho'] + $lot['so_luong_tren_ke'])) ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <div class="field field--full" style="margin-top: 18px;">
            <button type="submit"><?= $isEdit ? 'Lưu cập nhật' : 'Thêm hàng hóa' ?></button>
        </div>
    </form>
</section>
