<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap');

.dm-form-wrap { font-family: 'Plus Jakarta Sans', sans-serif; max-width: 680px; }

.dm-form-hero {
    display: flex; align-items: center; gap: 16px;
    background: linear-gradient(135deg, #6c63ff 0%, #a78bfa 100%);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 24px;
    color: white;
    position: relative; overflow: hidden;
}
.dm-form-hero::after {
    content: '';
    position: absolute; right: -30px; top: -30px;
    width: 160px; height: 160px;
    background: rgba(255,255,255,.08);
    border-radius: 50%;
}
.dm-form-hero-icon {
    width: 56px; height: 56px; border-radius: 16px;
    background: rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; flex-shrink: 0; position: relative; z-index: 1;
}
.dm-form-hero-text h2 { margin: 0; font-size: 20px; font-weight: 800; }
.dm-form-hero-text p  { margin: 4px 0 0; font-size: 13px; opacity: .8; }

.dm-form-card {
    background: white;
    border: 1px solid #eef0f6;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,.06);
}
.dm-form-section {
    padding: 28px 32px;
    border-bottom: 1px solid #f3f4f8;
}
.dm-form-section:last-child { border-bottom: none; }
.dm-section-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .1em; color: #8990aa; margin-bottom: 20px;
    display: flex; align-items: center; gap: 8px;
}
.dm-section-title::after {
    content: ''; flex: 1; height: 1px; background: #eef0f6;
}

.dm-field { margin-bottom: 20px; }
.dm-label {
    display: block;
    font-size: 13px; font-weight: 600; color: #1a1d2e;
    margin-bottom: 8px;
}
.dm-label .req { color: #ff4d6d; margin-left: 2px; }
.dm-label .hint { font-weight: 400; color: #8990aa; font-size: 12px; margin-left: 6px; }
.dm-input {
    width: 100%; padding: 11px 16px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px; color: #1a1d2e;
    background: #f9fafb;
    border: 1.5px solid #eef0f6;
    border-radius: 12px;
    outline: none;
    transition: all .2s;
    box-sizing: border-box;
}
.dm-input:focus {
    background: white;
    border-color: #6c63ff;
    box-shadow: 0 0 0 3px rgba(108,99,255,.12);
}
.dm-input[readonly] {
    background: #f3f4f8; color: #8990aa; cursor: not-allowed;
}
.dm-textarea {
    resize: vertical; min-height: 100px;
}
.dm-hint { font-size: 11px; color: #8990aa; margin-top: 5px; }

/* Color picker for category */
.dm-color-row {
    display: flex; gap: 10px; flex-wrap: wrap; margin-top: 4px;
}
.dm-color-opt {
    width: 32px; height: 32px; border-radius: 8px;
    border: 2.5px solid transparent; cursor: pointer;
    transition: transform .15s, border-color .15s;
}
.dm-color-opt:hover { transform: scale(1.15); }
.dm-color-opt.selected { border-color: #1a1d2e; transform: scale(1.1); }

/* Preview card */
.dm-preview {
    background: #f9fafb;
    border: 1px solid #eef0f6;
    border-radius: 14px;
    padding: 20px;
    display: flex; align-items: center; gap: 14px;
}
.dm-preview-icon {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; flex-shrink: 0;
    transition: background .3s, color .3s;
}
.dm-preview-info { flex: 1; }
.dm-preview-code { font-family: 'Space Mono', monospace; font-size: 10px; color: #8990aa; }
.dm-preview-name { font-size: 16px; font-weight: 700; color: #1a1d2e; }
.dm-preview-desc { font-size: 12px; color: #8990aa; margin-top: 2px; }

/* Action bar */
.dm-form-actions {
    display: flex; align-items: center; justify-content: flex-end; gap: 10px;
    padding: 20px 32px;
    background: #f9fafb;
    border-top: 1px solid #eef0f6;
}
.dm-btn-save {
    display: inline-flex; align-items: center; gap: 8px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 14px; font-weight: 700;
    padding: 12px 28px; border-radius: 12px;
    background: linear-gradient(135deg, #6c63ff, #a78bfa);
    color: white; border: none; cursor: pointer;
    box-shadow: 0 4px 14px rgba(108,99,255,.35);
    transition: all .2s;
}
.dm-btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(108,99,255,.45); }
.dm-btn-cancel {
    display: inline-flex; align-items: center; gap: 8px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px; font-weight: 600;
    padding: 11px 22px; border-radius: 12px;
    background: white; color: #8990aa;
    border: 1.5px solid #eef0f6;
    cursor: pointer; text-decoration: none; transition: all .2s;
}
.dm-btn-cancel:hover { background: #f3f4f8; color: #1a1d2e; }
</style>

<div class="dm-form-wrap fade-in">

<!-- Hero -->
<div class="dm-form-hero">
    <div class="dm-form-hero-icon">
        <i class="fas <?= isset($danhMuc) ? 'fa-edit' : 'fa-folder-plus' ?>"></i>
    </div>
    <div class="dm-form-hero-text">
        <h2><?= isset($danhMuc) ? 'Cập nhật danh mục' : 'Thêm danh mục mới' ?></h2>
        <p><?= isset($danhMuc) ? 'Chỉnh sửa thông tin phân loại sản phẩm' : 'Tạo phân loại sản phẩm mới trong hệ thống' ?></p>
    </div>
</div>

<?php if (isset($error)): ?>
<div style="background:#fff0f3; border:1.5px solid #ffd0d8; border-radius:12px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; gap:10px; font-size:13px; color:#c0384d;">
    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
</div>
<?php endif; ?>

<div class="dm-form-card">
    <form method="POST">
        <!-- Section: Thông tin cơ bản -->
        <div class="dm-form-section">
            <div class="dm-section-title"><i class="fas fa-info-circle" style="color:#6c63ff;"></i> Thông tin cơ bản</div>

            <div class="dm-field">
                <label class="dm-label">Mã danh mục <span class="req">*</span>
                    <?php if (!isset($danhMuc)): ?><span class="hint">· Không thể thay đổi sau khi tạo</span><?php endif; ?>
                </label>
                <input type="text" name="ma_danh_muc" class="dm-input" required
                       id="inputCode"
                       value="<?= isset($danhMuc) ? htmlspecialchars($danhMuc['ma_danh_muc']) : '' ?>"
                       <?= isset($danhMuc) ? 'readonly' : '' ?>
                       placeholder="Ví dụ: DM001, THUCPHAM, ...">
                <div class="dm-hint">Dùng chữ in hoa, không dấu, không khoảng trắng</div>
            </div>

            <div class="dm-field">
                <label class="dm-label">Tên danh mục <span class="req">*</span></label>
                <input type="text" name="ten_danh_muc" class="dm-input" required
                       id="inputName"
                       value="<?= isset($danhMuc) ? htmlspecialchars($danhMuc['ten_danh_muc']) : '' ?>"
                       placeholder="Ví dụ: Đồ uống, Thực phẩm khô, Sữa & Trứng...">
            </div>

            <div class="dm-field">
                <label class="dm-label">Mô tả <span class="hint">· Không bắt buộc</span></label>
                <textarea name="mo_ta" class="dm-input dm-textarea" id="inputDesc"
                          placeholder="Mô tả ngắn về danh mục này..."><?= isset($danhMuc) ? htmlspecialchars($danhMuc['mo_ta'] ?? '') : '' ?></textarea>
            </div>
        </div>

        <!-- Section: Biểu tượng -->
        <div class="dm-form-section">
            <div class="dm-section-title"><i class="fas fa-palette" style="color:#a78bfa;"></i> Màu sắc đại diện</div>
            <div class="dm-color-row" id="colorRow">
                <?php
                $colorOpts = ['#6c63ff','#10d9a0','#4facfe','#ff9500','#ff4d6d','#a78bfa','#ffd60a','#06d6a0','#f72585','#118ab2'];
                foreach ($colorOpts as $col):
                ?>
                <div class="dm-color-opt" style="background:<?= $col ?>;"
                     data-color="<?= $col ?>"
                     onclick="selectColor(this, '<?= $col ?>')"></div>
                <?php endforeach; ?>
            </div>
            <input type="hidden" name="color" id="selectedColor" value="<?= $colorOpts[0] ?>">
        </div>

        <!-- Section: Preview -->
        <div class="dm-form-section">
            <div class="dm-section-title"><i class="fas fa-eye" style="color:#10d9a0;"></i> Xem trước</div>
            <div class="dm-preview">
                <div class="dm-preview-icon" id="previewIcon" style="background:rgba(108,99,255,.12); color:#6c63ff;">
                    <i class="fas fa-folder"></i>
                </div>
                <div class="dm-preview-info">
                    <div class="dm-preview-code" id="previewCode"><?= isset($danhMuc) ? htmlspecialchars($danhMuc['ma_danh_muc']) : 'DM---' ?></div>
                    <div class="dm-preview-name" id="previewName"><?= isset($danhMuc) ? htmlspecialchars($danhMuc['ten_danh_muc']) : 'Tên danh mục' ?></div>
                    <div class="dm-preview-desc" id="previewDesc"><?= isset($danhMuc) ? htmlspecialchars($danhMuc['mo_ta'] ?? 'Chưa có mô tả') : 'Chưa có mô tả' ?></div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="dm-form-actions">
            <a href="index.php?controller=danh_muc&action=index" class="dm-btn-cancel">
                <i class="fas fa-times"></i> Hủy bỏ
            </a>
            <button type="submit" class="dm-btn-save">
                <i class="fas fa-save"></i> <?= isset($danhMuc) ? 'Lưu thay đổi' : 'Tạo danh mục' ?>
            </button>
        </div>
    </form>
</div>

</div>

<script>
var currentColor = '#6c63ff';

function selectColor(el, color) {
    document.querySelectorAll('.dm-color-opt').forEach(function(e){ e.classList.remove('selected'); });
    el.classList.add('selected');
    currentColor = color;
    document.getElementById('selectedColor').value = color;
    updatePreview();
}

function hexToRgba(hex, a) {
    var r = parseInt(hex.slice(1,3),16), g = parseInt(hex.slice(3,5),16), b = parseInt(hex.slice(5,7),16);
    return 'rgba('+r+','+g+','+b+','+a+')';
}

function updatePreview() {
    var icon = document.getElementById('previewIcon');
    icon.style.background = hexToRgba(currentColor, .12);
    icon.style.color = currentColor;
    document.getElementById('previewCode').textContent = document.getElementById('inputCode').value || 'DM---';
    document.getElementById('previewName').textContent = document.getElementById('inputName').value || 'Tên danh mục';
    document.getElementById('previewDesc').textContent = document.getElementById('inputDesc').value || 'Chưa có mô tả';
}

// Init
document.getElementById('colorRow').children[0].classList.add('selected');
['inputCode','inputName','inputDesc'].forEach(function(id){
    var el = document.getElementById(id);
    if(el) el.addEventListener('input', updatePreview);
});
</script>