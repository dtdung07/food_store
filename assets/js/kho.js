//JavaScript cho nghiệp vụ Nhập kho & Xuất kho

let rowIdx = 0;

// Khi click vào ô nhập số, bôi đen toàn bộ nội dung để nhập nhanh
document.addEventListener('click', (e) => {
    if (e.target.type === 'number' || e.target.type === 'text') {
        e.target.select();
    }
});

//-------------COMMON AUTOCOMPLETE----
function setupAutocomplete(inputEl, onSelect) {
    const wrapper = document.createElement('div');
    wrapper.style.position = 'relative';
    inputEl.parentNode.insertBefore(wrapper, inputEl);
    wrapper.appendChild(inputEl);

    const dropdown = document.createElement('div');
    dropdown.className = 'autocomplete-dropdown';
    dropdown.style.cssText = 'position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid var(--line-strong); max-height:200px; overflow-y:auto; z-index:9999; display:none; border-radius:12px; box-shadow:var(--shadow-soft);';
    wrapper.appendChild(dropdown);

    let debounceTimer;
    inputEl.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const query = this.value.trim();
        if (query.length < 2) {
            dropdown.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`index.php?c=kho&a=search_hang_hoa&q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(res => {
                    if (res.success && res.data.length > 0) {
                        dropdown.innerHTML = '';
                        res.data.forEach(item => {
                            const option = document.createElement('div');
                            option.style.cssText = 'padding:10px 16px; cursor:pointer; border-bottom:1px solid var(--line); font-size:15px; transition: background-color 0.2s;';
                            option.innerHTML = `<strong>${item.ma_hang_hoa}</strong> - ${item.ten_hang_hoa} <span style="font-size:0.8rem; color:var(--muted);">(Tồn: ${item.ton_trong_kho} | Kệ: ${item.ton_ke})</span>`;
                            option.addEventListener('click', () => {
                                inputEl.value = `${item.ma_hang_hoa} - ${item.ten_hang_hoa}`;
                                dropdown.style.display = 'none';
                                onSelect(item);
                            });
                            //Hover effect
                            option.addEventListener('mouseover', () => option.style.background = 'var(--surface-muted)');
                            option.addEventListener('mouseout', () => option.style.background = '#fff');
                            dropdown.appendChild(option);
                        });
                        dropdown.style.display = 'block';
                    } else {
                        dropdown.style.display = 'none';
                    }
                })
                .catch(err => console.error(err));
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!wrapper.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}

//---------------FORM NHẬP KHO---------
function initNhapKhoForm() {
    const detailBody = document.getElementById('detail-body');
    const btnAddRow = document.getElementById('btn-add-row');

    if (!detailBody || !btnAddRow) return;

    //Reset body trước khi gán để tránh nhân đôi dòng khi htmx load lại
    detailBody.innerHTML = '';

    btnAddRow.onclick = () => addNhapRow();

    //Thêm dòng đầu tiên mặc định
    addNhapRow();

    function addNhapRow() {
        const idx = rowIdx++;
        const card = document.createElement('div');
        card.id = `row-${idx}`;
        card.style.cssText = 'background: var(--surface); border: 1px solid var(--line); border-radius: 16px; padding: 16px 20px; position: relative; transition: box-shadow 0.2s;';
        card.innerHTML = `
            <div style="display: flex; gap: 12px; align-items: flex-start; margin-bottom: 14px;">
                <div style="flex: 1; min-width: 0;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">Hàng hóa <span style="color:var(--red)">*</span></label>
                    <input type="text" class="input-search-product" placeholder="Nhập tên hoặc mã hàng hóa để tìm kiếm..." required autocomplete="off" style="width: 100%; padding: 10px 14px; border-radius: 12px; font-size: 15px; box-sizing: border-box;">
                    <input type="hidden" name="ma_hang_hoa[]" class="row-ma-hang-hoa">
                </div>
                <button type="button" class="button button--danger btn-remove-row" style="padding: 0; width: 34px; height: 34px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 26px;" title="Xóa dòng">
                    <svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" style="stroke: currentColor; stroke-width: 3; fill: none; stroke-linecap: round; stroke-linejoin: round; width: 14px; height: 14px; display: block;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 130px;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">Mã Lô <span style="color:var(--red)">*</span></label>
                    <input type="text" name="ma_lo_hang[]" class="row-ma-lo" placeholder="VD: LH-260612-..." required style="width: 100%; padding: 10px 14px; border-radius: 12px; font-size: 14px; box-sizing: border-box;">
                </div>
                <div style="flex: 1; min-width: 140px;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">Ngày SX <span style="color:var(--red)">*</span></label>
                    <input type="date" name="ngay_san_xuat[]" class="row-ngay-sx" required style="width: 100%; padding: 10px 14px; border-radius: 12px; font-size: 14px; box-sizing: border-box;">
                </div>
                <div style="flex: 1; min-width: 140px;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">HSD <span style="color:var(--red)">*</span></label>
                    <input type="date" name="han_su_dung[]" class="row-hsd" required style="width: 100%; padding: 10px 14px; border-radius: 12px; font-size: 14px; box-sizing: border-box;">
                </div>
                <div style="min-width: 100px;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">Số lượng <span style="color:var(--red)">*</span></label>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <input type="number" name="so_luong[]" class="row-so-luong" min="1" value="1" required style="width: 100px; padding: 10px 12px; border-radius: 12px; font-size: 15px; font-weight: 600; text-align: right; box-sizing: border-box;">
                        <span class="row-dvt" style="font-size: 13px; color: var(--muted); font-weight: 500; white-space: nowrap; min-width: 24px;"></span>
                    </div>
                </div>
                <div style="min-width: 140px; flex: 1;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">Đơn giá nhập <span style="color:var(--red)">*</span></label>
                    <input type="number" name="don_gia_nhap[]" class="row-don-gia" min="0" step="100" value="0" required style="width: 100%; padding: 10px 14px; border-radius: 12px; font-size: 14px; text-align: right; box-sizing: border-box;">
                </div>
                <div style="min-width: 120px; text-align: right;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">Thành tiền</label>
                    <span class="row-thanh-tien" style="font-weight: 700; color: var(--blue); font-size: 15px; line-height: 42px; display: block;">0 VND</span>
                </div>
            </div>
        `;

        detailBody.appendChild(card);

        const searchInput = card.querySelector('.input-search-product');
        const hiddenInput = card.querySelector('.row-ma-hang-hoa');
        const priceInput = card.querySelector('.row-don-gia');
        const qtyInput = card.querySelector('.row-so-luong');
        const dvtSpan = card.querySelector('.row-dvt');
        const totalSpan = card.querySelector('.row-thanh-tien');

        setupAutocomplete(searchInput, (item) => {
            hiddenInput.value = item.ma_hang_hoa;

            // Hiển thị ĐVT
            dvtSpan.textContent = item.don_vi_tinh || '';

            //Đặt min và step động dựa trên ma_tem_can
            if (item.ma_tem_can) {
                qtyInput.step = "0.001";
                qtyInput.min = "0.001";
            } else {
                qtyInput.step = "1";
                qtyInput.min = "1";
                qtyInput.value = Math.round(parseFloat(qtyInput.value) || 1);
            }

            //Tự động gợi ý Mã Lô (Người dùng có thể sửa thủ công mã lô theo NSX)
            const lotInput = card.querySelector('.row-ma-lo');
            if (lotInput) {
                fetch(`index.php?c=kho&a=generate_lo_code&ma_hang_hoa=${encodeURIComponent(item.ma_hang_hoa)}`)
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            let code = res.code;

                            //Kiểm tra trùng lặp trên giao diện hiện tại
                            let isDuplicate = true;
                            let suffixNum = parseInt(code.split('-').pop()) || 1;
                            const prefix = code.substring(0, code.lastIndexOf('-') + 1);

                            while (isDuplicate) {
                                isDuplicate = false;
                                document.querySelectorAll('#detail-body > div').forEach(row => {
                                    if (row !== card) {
                                        const otherLotVal = row.querySelector('.row-ma-lo')?.value;
                                        if (otherLotVal === code) {
                                            isDuplicate = true;
                                        }
                                    }
                                });
                                if (isDuplicate) {
                                    suffixNum++;
                                    code = prefix + String(suffixNum).padStart(2, '0');
                                }
                            }

                            lotInput.value = code;
                        }
                    })
                    .catch(err => console.error(err));
            }
        });

        //Event tính thành tiền
        const updateRowTotal = () => {
            const qty = parseFloat(qtyInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = qty * price;
            totalSpan.textContent = total.toLocaleString('vi-VN') + ' VND';
            updateAllTotals();
        };

        qtyInput.addEventListener('input', updateRowTotal);
        priceInput.addEventListener('input', updateRowTotal);

        card.querySelector('.btn-remove-row').addEventListener('click', () => {
            card.remove();
            updateAllTotals();
        });

        // Hiệu ứng hover card
        card.addEventListener('mouseenter', () => card.style.boxShadow = 'var(--shadow-soft)');
        card.addEventListener('mouseleave', () => card.style.boxShadow = 'none');
    }

    function updateAllTotals() {
        let totalQty = 0;
        let totalAmount = 0;

        document.querySelectorAll('#detail-body > div').forEach(card => {
            const qty = parseFloat(card.querySelector('.row-so-luong').value) || 0;
            const price = parseFloat(card.querySelector('.row-don-gia').value) || 0;
            totalQty += qty;
            totalAmount += qty * price;
        });

        const totalQtyEl = document.getElementById('total-qty');
        const totalAmountEl = document.getElementById('total-amount');
        if (totalQtyEl) {
            totalQtyEl.textContent = new Intl.NumberFormat('vi-VN', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 3
            }).format(totalQty);
        }
        if (totalAmountEl) totalAmountEl.textContent = totalAmount.toLocaleString('vi-VN') + ' VND';
    }
}

//------------FORM XUẤT KHO------------
function initXuatKhoForm() {
    const detailBody = document.getElementById('detail-body');
    const btnAddRow = document.getElementById('btn-add-row');

    if (!detailBody || !btnAddRow) return;

    detailBody.innerHTML = '';

    btnAddRow.onclick = () => addXuatRow();

    // Thêm dòng đầu tiên mặc định
    addXuatRow();

    function addXuatRow() {
        const idx = rowIdx++;
        const card = document.createElement('div');
        card.id = `row-${idx}`;
        card.style.cssText = 'background: var(--surface); border: 1px solid var(--line); border-radius: 16px; padding: 16px 20px; position: relative; transition: box-shadow 0.2s;';
        card.innerHTML = `
            <div style="display: flex; gap: 12px; align-items: flex-start; margin-bottom: 14px;">
                <div style="flex: 1; min-width: 0;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">Hàng hóa <span style="color:var(--red)">*</span></label>
                    <input type="text" class="input-search-product" placeholder="Nhập tên hoặc mã hàng hóa để tìm kiếm..." required autocomplete="off" style="width: 100%; padding: 10px 14px; border-radius: 12px; font-size: 15px; box-sizing: border-box;">
                    <input type="hidden" name="ma_hang_hoa[]" class="row-ma-hang-hoa">
                </div>
                <button type="button" class="button button--danger btn-remove-row" style="padding: 0; width: 34px; height: 34px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 26px;" title="Xóa dòng">
                    <svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" style="stroke: currentColor; stroke-width: 3; fill: none; stroke-linecap: round; stroke-linejoin: round; width: 14px; height: 14px; display: block;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div style="display: flex; gap: 16px; align-items: flex-start; flex-wrap: wrap;">
                <div style="min-width: 120px;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">Số lượng xuất <span style="color:var(--red)">*</span></label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <input type="number" name="so_luong[]" class="row-so-luong" min="1" value="1" required style="width: 110px; padding: 10px 12px; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: right; box-sizing: border-box;">
                        <span class="row-dvt" style="font-size: 13px; color: var(--muted); font-weight: 500; white-space: nowrap;"></span>
                    </div>
                </div>
                <div style="flex: 1; min-width: 240px;">
                    <label style="font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">Gợi ý phân bổ lô (FIFO)</label>
                    <div class="fifo-suggest-container text-muted" style="font-size: 0.9rem; padding: 10px 14px; background: var(--surface-soft); border-radius: 12px; min-height: 42px; display: flex; align-items: center;">
                        Nhập sản phẩm & số lượng để xem gợi ý phân bổ lô.
                    </div>
                </div>
            </div>
        `;

        detailBody.appendChild(card);

        const searchInput = card.querySelector('.input-search-product');
        const hiddenInput = card.querySelector('.row-ma-hang-hoa');
        const qtyInput = card.querySelector('.row-so-luong');
        const dvtSpan = card.querySelector('.row-dvt');
        const suggestContainer = card.querySelector('.fifo-suggest-container');

        const triggerSuggest = () => {
            const mhh = hiddenInput.value;
            const qty = parseFloat(qtyInput.value) || 0;

            if (mhh === '' || qty <= 0) {
                suggestContainer.innerHTML = 'Nhập sản phẩm & số lượng để xem gợi ý phân bổ lô.';
                return;
            }

            suggestContainer.innerHTML = '<span class="text-info">Đang tính toán gợi ý...</span>';

            fetch(`index.php?c=kho&a=suggest_fifo&ma_hang_hoa=${mhh}&qty=${qty}`)
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        const info = res.data;
                        if (!info.enough) {
                            const totalAvailFormatted = new Intl.NumberFormat('vi-VN', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 3
                            }).format(info.total_available);
                            suggestContainer.innerHTML = `<span class="text-danger" style="font-weight:bold;">Tồn kho không đủ! Chỉ còn tổng tồn: ${totalAvailFormatted}</span>`;
                            return;
                        }

                        let html = '<ul style="margin:0; padding-left:1.2rem; text-align:left;">';
                        info.suggestions.forEach(s => {
                            const qtyXuatFormatted = new Intl.NumberFormat('vi-VN', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 3
                            }).format(s.so_luong_xuat);
                            html += `<li>Lô <strong>${s.ma_lo_hang}</strong> (HSD: ${formatDate(s.han_su_dung)}): Lấy <strong>${qtyXuatFormatted}</strong></li>`;
                        });
                        html += '</ul>';
                        suggestContainer.innerHTML = html;
                    } else {
                        suggestContainer.innerHTML = `<span class="text-danger">${res.message}</span>`;
                    }
                })
                .catch(err => {
                    console.error(err);
                    suggestContainer.innerHTML = '<span class="text-danger">Lỗi tải dữ liệu gợi ý.</span>';
                });
        };

        setupAutocomplete(searchInput, (item) => {
            hiddenInput.value = item.ma_hang_hoa;
            dvtSpan.textContent = item.don_vi_tinh || '';

            //Đặt min và step động dựa trên ma_tem_can
            if (item.ma_tem_can) {
                qtyInput.step = "0.001";
                qtyInput.min = "0.001";
            } else {
                qtyInput.step = "1";
                qtyInput.min = "1";
                qtyInput.value = Math.round(parseFloat(qtyInput.value) || 1);
            }

            triggerSuggest();
        });

        qtyInput.addEventListener('input', () => {
            triggerSuggest();
            updateAllTotals();
        });

        card.querySelector('.btn-remove-row').addEventListener('click', () => {
            card.remove();
            updateAllTotals();
        });

        card.addEventListener('mouseenter', () => card.style.boxShadow = 'var(--shadow-soft)');
        card.addEventListener('mouseleave', () => card.style.boxShadow = 'none');
    }

    function updateAllTotals() {
        let totalQty = 0;
        document.querySelectorAll('#detail-body > div').forEach(card => {
            const qty = parseFloat(card.querySelector('.row-so-luong').value) || 0;
            totalQty += qty;
        });
        const totalQtyEl = document.getElementById('total-qty');
        if (totalQtyEl) {
            totalQtyEl.textContent = new Intl.NumberFormat('vi-VN', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 3
            }).format(totalQty);
        }
    }
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const parts = dateStr.split('-');
    if (parts.length === 3) {
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    return dateStr;
}

//----------TỰ ĐỘNG KHỞI TẠO------------
function initKhoFormsGlobal() {
    const nhapForm = document.getElementById('form-nhap-kho');
    if (nhapForm && !nhapForm.dataset.initialized) {
        initNhapKhoForm();
        nhapForm.dataset.initialized = 'true';
    }

    const xuatForm = document.getElementById('form-xuat-kho');
    if (xuatForm && !xuatForm.dataset.initialized) {
        initXuatKhoForm();
        xuatForm.dataset.initialized = 'true';
    }
}

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    initKhoFormsGlobal();
} else {
    document.addEventListener('DOMContentLoaded', initKhoFormsGlobal);
}

document.addEventListener('htmx:load', initKhoFormsGlobal);

//Tự động bôi đen toàn bộ khi click/focus vào ô số lượng/đơn giá để nhập liệu nhanh
document.addEventListener('focus', function (e) {
    if (e.target && (e.target.classList.contains('row-so-luong') || e.target.classList.contains('row-don-gia'))) {
        setTimeout(() => {
            if (typeof e.target.select === 'function') {
                e.target.select();
            }
        }, 50);
    }
}, true);

