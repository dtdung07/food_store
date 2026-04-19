//JavaScript cho nghiệp vụ Nhập kho & Xuất kho

let rowIdx = 0;

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
                            option.innerHTML = `<strong>${item.ma_hang_hoa}</strong> - ${item.ten_hang_hoa} <span style="font-size:0.8rem; color:var(--muted);">(Tồn: ${item.ton_kho} | Kệ: ${item.ton_ke})</span>`;
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
        const tr = document.createElement('tr');
        tr.id = `row-${idx}`;
        tr.innerHTML = `
            <td>
                <input type="text" class="input-search-product" placeholder="Nhập tên/mã hàng..." required autocomplete="off" style="padding: 8px 12px; border-radius: 12px;">
                <input type="hidden" name="ma_hang_hoa[]" class="row-ma-hang-hoa">
            </td>
            <td>
                <input type="text" name="ma_lo_hang[]" class="row-ma-lo" placeholder="Mã Lô" required style="padding: 8px 12px; border-radius: 12px;">
            </td>
            <td>
                <input type="date" name="ngay_san_xuat[]" class="row-ngay-sx" required style="padding: 8px 12px; border-radius: 12px;">
            </td>
            <td>
                <input type="date" name="han_su_dung[]" class="row-hsd" required style="padding: 8px 12px; border-radius: 12px;">
            </td>
            <td>
                <input type="number" name="so_luong[]" class="row-so-luong" min="1" value="1" required style="padding: 8px 12px; border-radius: 12px; text-align: right; width: 90px;">
            </td>
            <td>
                <input type="number" name="don_gia_nhap[]" class="row-don-gia" min="0" step="100" value="0" required style="padding: 8px 12px; border-radius: 12px; text-align: right; width: 125px;">
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <span class="row-thanh-tien" style="font-weight: bold; color: var(--blue);">0 VND</span>
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <button type="button" class="button button--danger btn-remove-row" style="padding: 0; width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;" title="Xóa dòng">
                    <svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" style="stroke: currentColor; stroke-width: 3; fill: none; stroke-linecap: round; stroke-linejoin: round; width: 14px; height: 14px; display: block;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </td>
        `;

        detailBody.appendChild(tr);

        const searchInput = tr.querySelector('.input-search-product');
        const hiddenInput = tr.querySelector('.row-ma-hang-hoa');
        const priceInput = tr.querySelector('.row-don-gia');
        const qtyInput = tr.querySelector('.row-so-luong');
        const totalSpan = tr.querySelector('.row-thanh-tien');

        setupAutocomplete(searchInput, (item) => {
            hiddenInput.value = item.ma_hang_hoa;

            // Tự động gợi ý Mã Lô (Cách 3: Auto-suggest + Manual Override)
            const lotInput = tr.querySelector('.row-ma-lo');
            if (lotInput) {
                fetch(`index.php?c=kho&a=generate_lo_code&ma_hang_hoa=${encodeURIComponent(item.ma_hang_hoa)}`)
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            let code = res.code;
                            
                            // Kiểm tra trùng lặp trên giao diện hiện tại
                            let isDuplicate = true;
                            let suffixNum = parseInt(code.split('-').pop()) || 1;
                            const prefix = code.substring(0, code.lastIndexOf('-') + 1);
                            
                            while (isDuplicate) {
                                isDuplicate = false;
                                document.querySelectorAll('#detail-body tr').forEach(row => {
                                    if (row !== tr) {
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
            const qty = parseInt(qtyInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = qty * price;
            totalSpan.textContent = total.toLocaleString('vi-VN') + ' VND';
            updateAllTotals();
        };

        qtyInput.addEventListener('input', updateRowTotal);
        priceInput.addEventListener('input', updateRowTotal);

        tr.querySelector('.btn-remove-row').addEventListener('click', () => {
            tr.remove();
            updateAllTotals();
        });
    }

    function updateAllTotals() {
        let totalQty = 0;
        let totalAmount = 0;

        document.querySelectorAll('#detail-body tr').forEach(tr => {
            const qty = parseInt(tr.querySelector('.row-so-luong').value) || 0;
            const price = parseFloat(tr.querySelector('.row-don-gia').value) || 0;
            totalQty += qty;
            totalAmount += qty * price;
        });

        const totalQtyEl = document.getElementById('total-qty');
        const totalAmountEl = document.getElementById('total-amount');
        if (totalQtyEl) totalQtyEl.textContent = totalQty.toLocaleString('vi-VN');
        if (totalAmountEl) totalAmountEl.textContent = totalAmount.toLocaleString('vi-VN') + ' VND';
    }
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
