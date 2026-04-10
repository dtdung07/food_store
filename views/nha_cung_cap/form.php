<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap');

.ncc-form-wrap { font-family: 'Plus Jakarta Sans', sans-serif; max-width: 760px; }

.ncc-form-hero {
    display: flex; align-items: center; gap: 16px;
    background: linear-gradient(135deg, #10d9a0 0%, #06c97b 100%);
    border-radius: 20px; padding: 28px 32px; margin-bottom: 24px;
    color: white; position: relative; overflow: hidden;
}
.ncc-form-hero::after {
    content: ''; position: absolute; right: -40px; top: -40px;
    width: 180px; height: 180px; background: rgba(255,255,255,.08); border-radius: 50%;
}
.ncc-form-hero-icon {
    width: 56px; height: 56px; border-radius: 16px;
    background: rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; flex-shrink: 0; position: relative; z-index: 1;
}
.ncc-form-hero-text h2 { margin: 0; font-size: 20px; font-weight: 800; }
.ncc-form-hero-text p  { margin: 4px 0 0; font-size: 13px; opacity: .8; }

.ncc-form-card {
    background: white; border: 1px solid #eef0f6; border-radius: 20px;
    overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.06);
}
.ncc-form-section { padding: 26px 30px; border-bottom: 1px solid #f3f4f8; }
.ncc-form-section:last-child { border-bottom: none; }
.ncc-section-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .1em; color: #8990aa; margin-bottom: 20px;
    display: flex; align-items: center; gap: 8px;
}
.ncc-section-title::after { content: ''; flex: 1; height: 1px; background: #eef0f6; }

.ncc-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.ncc-form-grid.full { grid-template-columns: 1fr; }
.ncc-field { display: flex; flex-direction: column; gap: 6px; }
.ncc-label { font-size: 13px; font-weight: 600; color: #1a1d2e; }
.ncc-label .req { color: #ff4d6d; margin-left: 2px; }
.ncc-label .opt { font-weight: 400; color: #8990aa; font-size: 12px; }
.ncc-input, .ncc-select, .ncc-textarea {
    width: 100%; padding: 11px 16px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; color: #1a1d2e;
    background: #f9fafb; border: 1.5px solid #eef0f6;
    border-radius: 12px; outline: none; transition: all .2s; box-sizing: border-box;
}
.ncc-input:focus, .ncc-select:focus, .ncc-textarea:focus {
    background: white; border-color: #10d9a0; box-shadow: 0 0 0 3px rgba(16,217,160,.12);
}
.ncc-input[readonly] { background: #f3f4f8; color: #8990aa; cursor: not-allowed; }
.ncc-textarea { resize: vertical; min-height: 90px; }
.ncc-hint { font-size: 11px; color: #8990aa; }

/* Phone prefix group */
.ncc-phone-group { display: flex; gap: 0; }
.ncc-phone-prefix {
    padding: 11px 14px; background: #f3f4f8; border: 1.5px solid #eef0f6; border-right: none;
    border-radius: 12px 0 0 12px; font-size: 13px; color: #8990aa; font-weight: 600; white-space: nowrap;
    display: flex; align-items: center;
}
.ncc-phone-group .ncc-input { border-radius: 0 12px 12px 0; flex: 1; }
.ncc-phone-group .ncc-input:focus { border-color: #10d9a0; }

/* Status toggle */
.ncc-status-toggle { display: flex; gap: 10px; }
.ncc-status-opt {
    flex: 1; padding: 10px; border-radius: 12px;
    border: 1.5px solid #eef0f6; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    transition: all .2s; font-size: 13px; font-weight: 600; color: #8990aa;
    background: #f9fafb;
}
.ncc-status-opt input[type=radio] { display: none; }
.ncc-status-opt.active-green { border-color: #10d9a0; background: rgba(16,217,160,.06); color: #10d9a0; }
.ncc-status-opt.active-red   { border-color: #ff4d6d; background: rgba(255,77,109,.06); color: #ff4d6d; }
.ncc-status-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

/* Quick stat cards (edit mode) */
.ncc-quick-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.ncc-qs {
    background: #f9fafb; border: 1px solid #eef0f6; border-radius: 12px;
    padding: 14px 16px; display: flex; align-items: center; gap: 10px;
}
.ncc-qs-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
.ncc-qs-val  { font-family: 'Space Mono', monospace; font-size: 18px; font-weight: 700; color: #1a1d2e; }
.ncc-qs-lbl  { font-size: 11px; color: #8990aa; }

/* Actions */
.ncc-form-actions {
    display: flex; align-items: center; justify-content: flex-end; gap: 10px;
    padding: 20px 30px; background: #f9fafb; border-top: 1px solid #eef0f6;
}
.ncc-btn-save {
    display: inline-flex; align-items: center; gap: 8px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 700;
    padding: 12px 28px; border-radius: 12px;
    background: linear-gradient(135deg, #10d9a0, #06c97b);
    color: white; border: none; cursor: pointer;
    box-shadow: 0 4px 14px rgba(16,217,160,.35); transition: all .2s;
}
.ncc-btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16,217,160,.45); }
.ncc-btn-cancel {
    display: inline-flex; align-items: center; gap: 8px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 600;
    padding: 11px 22px; border-radius: 12px;
    background: white; color: #8990aa; border: 1.5px solid #eef0f6;
    cursor: pointer; text-decoration: none; transition: all .2s;
}
.ncc-btn-cancel:hover { background: #f3f4f8; color: #1a1d2e; }

@media (max-width: 640px) {
    .ncc-form-grid { grid-template-columns: 1fr; }
    .ncc-quick-stats { grid-template-columns: 1fr; }
}
</style>

<div class="ncc-form-wrap fade-in">

<!-- Hero -->
<div class="ncc-form-hero">
    <div class="ncc-form-hero-icon">
        <i class="fas <?= isset($nhaCungCap) ? 'fa-edit' : 'fa-building-circle-check' ?>"></i>
    </div>
    <div class="ncc-form-hero-text">
        <h2><?= isset($nhaCungCap) ? 'Cập nhật nhà cung cấp' : 'Thêm nhà cung cấp mới' ?></h2>
        <p><?= isset($nhaCungCap) ? 'Chỉnh sửa thông tin đối tác cung ứng' : 'Đăng ký đối tác cung ứng mới vào hệ thống' ?></p>
    </div>
</div>

<?php if (isset($error)): ?>
<div style="background:#fff0f3; border:1.5px solid #ffd0d8; border-radius:12px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; gap:10px; font-size:13px; color:#c0384d;">
    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
</div>
<?php endif; ?>

<div class="ncc-form-card">
    <form method="POST">

        <!-- Thông tin định danh -->
        <div class="ncc-form-section">
            <div class="ncc-section-title"><i class="fas fa-id-card" style="color:#10d9a0;"></i> Thông tin định danh</div>
            <div class="ncc-form-grid">
                <div class="ncc-field">
                    <label class="ncc-label">Mã nhà cung cấp <span class="req">*</span></label>
                    <input type="text" name="ma_nha_cung_cap" class="ncc-input" required
                           value="<?= isset($nhaCungCap) ? htmlspecialchars($nhaCungCap['ma_nha_cung_cap']) : '' ?>"
                           <?= isset($nhaCungCap) ? 'readonly' : '' ?>
                           placeholder="Ví dụ: NCC001, NCC-VINAMILK...">
                    <?php if (!isset($nhaCungCap)): ?>
                        <span class="ncc-hint">Không thể thay đổi sau khi tạo</span>
                    <?php endif; ?>
                </div>
                <div class="ncc-field">
                    <label class="ncc-label">Tên nhà cung cấp <span class="req">*</span></label>
                    <input type="text" name="ten_nha_cung_cap" class="ncc-input" required
                           value="<?= isset($nhaCungCap) ? htmlspecialchars($nhaCungCap['ten_nha_cung_cap']) : '' ?>"
                           placeholder="Tên công ty / đơn vị cung cấp">
                </div>
            </div>
        </div>

        <!-- Thông tin liên hệ -->
        <div class="ncc-form-section">
            <div class="ncc-section-title"><i class="fas fa-address-book" style="color:#4facfe;"></i> Thông tin liên hệ</div>
            <div class="ncc-form-grid">
                <div class="ncc-field">
                    <label class="ncc-label">Số điện thoại <span class="opt">· Không bắt buộc</span></label>
                    <div class="ncc-phone-group">
                        <div class="ncc-phone-prefix"><i class="fas fa-flag" style="color:#10d9a0; margin-right:6px;"></i>+84</div>
                        <input type="text" name="so_dien_thoai" class="ncc-input"
                               value="<?= isset($nhaCungCap) ? htmlspecialchars($nhaCungCap['so_dien_thoai'] ?? '') : '' ?>"
                               placeholder="901 234 567">
                    </div>
                </div>
                <div class="ncc-field">
                    <label class="ncc-label">Email <span class="opt">· Không bắt buộc</span></label>
                    <input type="email" name="email" class="ncc-input"
                           value="<?= isset($nhaCungCap) ? htmlspecialchars($nhaCungCap['email'] ?? '') : '' ?>"
                           placeholder="company@example.com">
                </div>
                <div class="ncc-field">
                    <label class="ncc-label">Người liên hệ <span class="opt">· Không bắt buộc</span></label>
                    <input type="text" name="ten_nguoi_lien_he" class="ncc-input"
                           value="<?= isset($nhaCungCap) ? htmlspecialchars($nhaCungCap['ten_nguoi_lien_he'] ?? '') : '' ?>"
                           placeholder="Họ tên người đại diện">
                </div>
                <div class="ncc-field">
                    <label class="ncc-label">Trạng thái</label>
                    <div class="ncc-status-toggle" id="statusToggle">
                        <label class="ncc-status-opt <?= (!isset($nhaCungCap) || $nhaCungCap['trang_thai'] == 'HOAT_DONG') ? 'active-green' : '' ?>"
                               id="opt-active" onclick="setStatus('HOAT_DONG')">
                            <input type="radio" name="trang_thai" value="HOAT_DONG"
                                   <?= (!isset($nhaCungCap) || $nhaCungCap['trang_thai'] == 'HOAT_DONG') ? 'checked' : '' ?>>
                            <div class="ncc-status-dot" style="background:#10d9a0;"></div>
                            Hoạt động
                        </label>
                        <label class="ncc-status-opt <?= (isset($nhaCungCap) && $nhaCungCap['trang_thai'] == 'VO_HIEU_HOA') ? 'active-red' : '' ?>"
                               id="opt-inactive" onclick="setStatus('VO_HIEU_HOA')">
                            <input type="radio" name="trang_thai" value="VO_HIEU_HOA"
                                   <?= (isset($nhaCungCap) && $nhaCungCap['trang_thai'] == 'VO_HIEU_HOA') ? 'checked' : '' ?>>
                            <div class="ncc-status-dot" style="background:#ff4d6d;"></div>
                            Vô hiệu hóa
                        </label>
                    </div>
                </div>
            </div>
            <div class="ncc-form-grid full" style="margin-top:16px;">
                <div class="ncc-field">
                    <label class="ncc-label">Địa chỉ <span class="opt">· Không bắt buộc</span></label>
                    <textarea name="dia_chi" class="ncc-textarea"
                              placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố..."><?= isset($nhaCungCap) ? htmlspecialchars($nhaCungCap['dia_chi'] ?? '') : '' ?></textarea>
                </div>
            </div>
        </div>

        <?php if (isset($nhaCungCap)): ?>
        <!-- Thống kê nhanh (chế độ sửa) -->
        <div class="ncc-form-section">
            <div class="ncc-section-title"><i class="fas fa-chart-bar" style="color:#ff9500;"></i> Thống kê nhanh</div>
            <div class="ncc-quick-stats">
                <div class="ncc-qs">
                    <div class="ncc-qs-icon" style="background:rgba(108,99,255,.1); color:#6c63ff;"><i class="fas fa-box"></i></div>
                    <div>
                        <div class="ncc-qs-val"><?= $productCount ?? 0 ?></div>
                        <div class="ncc-qs-lbl">Sản phẩm cung cấp</div>
                    </div>
                </div>
                <div class="ncc-qs">
                    <div class="ncc-qs-icon" style="background:rgba(16,217,160,.1); color:#10d9a0;"><i class="fas fa-calendar-alt"></i></div>
                    <div>
                        <div class="ncc-qs-val" style="font-size:14px;"><?= date('d/m/Y') ?></div>
                        <div class="ncc-qs-lbl">Ngày cập nhật</div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="ncc-form-actions">
            <a href="index.php?controller=nha_cung_cap&action=index" class="ncc-btn-cancel">
                <i class="fas fa-times"></i> Hủy bỏ
            </a>
            <button type="submit" class="ncc-btn-save">
                <i class="fas fa-save"></i> <?= isset($nhaCungCap) ? 'Lưu thay đổi' : 'Thêm nhà cung cấp' ?>
            </button>
        </div>
    </form>
</div>

</div>

<script>
function setStatus(val) {
    var optA = document.getElementById('opt-active');
    var optI = document.getElementById('opt-inactive');
    optA.classList.remove('active-green');
    optI.classList.remove('active-red');
    if (val === 'HOAT_DONG') {
        optA.classList.add('active-green');
        optA.querySelector('input').checked = true;
    } else {
        optI.classList.add('active-red');
        optI.querySelector('input').checked = true;
    }
}
</script>