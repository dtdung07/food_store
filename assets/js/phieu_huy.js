//JavaScript cho nghiệp vụ Phiếu hủy hàng
let phRowIdx = 0;

//Hàm autocomplete tìm kiếm hàng hóa
function setupPhAutocomplete(inputEl, onSelect) {
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

    //Xử lý click chuột bên ngoài dropdown
    document.addEventListener('click', function (e) {
        if (!wrapper.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}

//-----------FORM LẬP PHIẾU HỦY---------
function initPhieuHuyForm() {
    const detailBody = document.getElementById('detail-body');
    const btnAddRow = document.getElementById('btn-add-row');
    const phieuHuyForm = document.getElementById('form-phieu-huy');

    if (!detailBody || !btnAddRow) return;

    detailBody.innerHTML = '';

    btnAddRow.onclick = () => addPhieuHuyRow();

    //Thêm dòng đầu tiên mặc định
    addPhieuHuyRow();

    //Xử lý sự kiện submit của form để cảnh báo khi hủy vượt tồn kho
    if (phieuHuyForm) {
        let confirmSubmit = false;

        phieuHuyForm.addEventListener('submit', function (e) {
            if (confirmSubmit) {
                return;
            }

            let hasExceeded = false;
            let warningMessages = [];
            document.querySelectorAll('#detail-body tr').forEach((tr, i) => {
                const searchInput = tr.querySelector('.input-search-product');
                const searchInputVal = searchInput ? searchInput.value : '';
                const qtyInput = tr.querySelector('.row-so-luong');
                if (!qtyInput) return;
                const qty = parseInt(qtyInput.value) || 0;

                // Lấy tồn kho đã lưu ở dataset
                const tonTrongKho = parseInt(qtyInput.dataset.tonTrongKho) || 0;
                const tonKe = parseInt(qtyInput.dataset.tonKe) || 0;
                const totalStock = tonTrongKho + tonKe;

                if (qty > totalStock) {
                    hasExceeded = true;
                    // Lấy tên sản phẩm hiển thị ngắn gọn
                    const prodName = searchInputVal.split(' - ')[1] || searchInputVal || `Dòng ${i + 1}`;
                    warningMessages.push(`- <strong>${prodName}</strong>: Số lượng hủy (${qty}) vượt quá tồn kho hiện tại (${totalStock})`);
                }
            });

            if (hasExceeded) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                Swal.fire({
                    title: 'Cảnh báo tồn kho!',
                    html: `<div style="text-align: left; margin-bottom: 16px; font-size: 15px; line-height: 1.6;">Phát hiện sản phẩm có số lượng đề xuất hủy lớn hơn tồn kho hiện có:<br><br>${warningMessages.join('<br>')}</div><strong>Bạn có chắc chắn muốn tiếp tục lập phiếu hủy này không?</strong>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e73f73',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Có, tiếp tục tạo',
                    cancelButtonText: 'Quay lại sửa'
                }).then((result) => {
                    if (result.isConfirmed) {
                        confirmSubmit = true;
                        if (typeof phieuHuyForm.requestSubmit === 'function') {
                            phieuHuyForm.requestSubmit();
                        } else {
                            phieuHuyForm.submit();
                        }
                    }
                });
            }
        }, true);
    }

    function addPhieuHuyRow() {
        const idx = phRowIdx++;
        const tr = document.createElement('tr');
        tr.id = `row-${idx}`;
        tr.innerHTML = `
            <td>
                <input type="text" class="input-search-product" placeholder="Nhập tên/mã hàng..." required autocomplete="off" style="padding: 8px 12px; border-radius: 12px;">
                <input type="hidden" name="ma_hang_hoa[]" class="row-ma-hang-hoa">
            </td>
            <td>
                <input type="number" name="so_luong[]" class="row-so-luong" min="1" value="1" required style="padding: 8px 12px; border-radius: 12px; text-align: right; width: 90px;">
            </td>
            <td>
                <input type="text" class="row-don-gia-label" readonly style="padding: 8px 12px; border-radius: 12px; text-align: right; background: var(--gray-soft); cursor: not-allowed; width: 125px;" value="0 VND">
                <input type="hidden" class="row-don-gia" value="0">
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <span class="row-thanh-tien" style="font-weight: bold; color: var(--blue);">0 VND</span>
            </td>
            <td>
                <select name="ly_do_detail_select[]" class="row-ly-do" required style="padding: 8px 12px; border-radius: 12px; height: 38px; width: 100%;">
                    <option value="HET_HAN">Hết hạn sử dụng</option>
                    <option value="HONG_THOI">Hư hỏng, thối</option>
                    <option value="NAT_VO">Dập nát, vỡ</option>
                    <option value="LOI_QC">Không đạt chất lượng</option>
                    <option value="KHAC">Khác</option>
                </select>
                <input type="text" name="ly_do_detail_custom[]" class="row-ly-do-custom" placeholder="Nhập lý do chi tiết..." style="display:none; margin-top: 8px; padding: 8px 12px; border-radius: 12px; width: 100%; border: 1px solid var(--line);">
            </td>
            <td style="vertical-align: middle;">
                <span class="row-ton-kho text-muted" style="font-weight:500; font-size: 15px;">Kho: 0 | Kệ: 0</span>
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
        const qtyInput = tr.querySelector('.row-so-luong');
        const priceLabelInput = tr.querySelector('.row-don-gia-label');
        const priceInput = tr.querySelector('.row-don-gia');
        const tonSpan = tr.querySelector('.row-ton-kho');
        const totalSpan = tr.querySelector('.row-thanh-tien');
        const reasonSelect = tr.querySelector('.row-ly-do');
        const customReasonInput = tr.querySelector('.row-ly-do-custom');

        //Xử lý ẩn hiện ô nhập lý do tự do khi chọn 'Khác'
        reasonSelect.addEventListener('change', function () {
            if (this.value === 'KHAC') {
                customReasonInput.style.display = 'block';
                customReasonInput.required = true;
                customReasonInput.focus();
            } else {
                customReasonInput.style.display = 'none';
                customReasonInput.required = false;
                customReasonInput.value = '';
            }
        });

        setupPhAutocomplete(searchInput, (item) => {
            hiddenInput.value = item.ma_hang_hoa;
            tonSpan.textContent = `Kho: ${item.ton_trong_kho} | Kệ: ${item.ton_ke}`;
            tonSpan.className = 'row-ton-kho';

            const tonTrongKho = parseInt(item.ton_trong_kho) || 0;
            const tonKe = parseInt(item.ton_ke) || 0;
            if (tonTrongKho + tonKe === 0) {
                tonSpan.style.color = 'var(--red, #e73f73)';
            } else {
                tonSpan.style.color = 'var(--green, #54b87a)';
            }

            //Ghi nhận tồn kho vào dataset của qtyInput
            qtyInput.dataset.tonTrongKho = tonTrongKho;
            qtyInput.dataset.tonKe = tonKe;

            //Gán đơn giá
            const price = parseFloat(item.gia_ban) || 0;
            priceInput.value = price;
            priceLabelInput.value = price.toLocaleString('vi-VN') + ' VND';

            updateRowTotal();
        });

        const updateRowTotal = () => {
            const qty = parseInt(qtyInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = qty * price;
            totalSpan.textContent = total.toLocaleString('vi-VN') + ' VND';
            updateAllTotals();
        };

        qtyInput.addEventListener('input', updateRowTotal);

        tr.querySelector('.btn-remove-row').addEventListener('click', () => {
            tr.remove();
            updateAllTotals();
        });
    }

    //Hàm tính tổng số lượng và tổng tiền thất thoát
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

//-----------CHI TIẾT DUYỆT PHIẾU HỦY-----------
function initPhieuHuyDetailActions() {
    const btnApprove = document.getElementById('btn-approve');
    const formApprove = document.getElementById('form-approve-phieu');
    const btnReject = document.getElementById('btn-reject');
    const formReject = document.getElementById('form-reject-phieu');
    const inputLyDo = document.getElementById('input-ly-do-tu-choi');

    if (formApprove) {
        let confirmApprove = false;
        formApprove.addEventListener('submit', function (e) {
            if (confirmApprove) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            Swal.fire({
                title: 'Xác nhận duyệt?',
                text: 'Hệ thống sẽ thực hiện trừ tồn kho theo thứ tự FIFO của từng mặt hàng trong phiếu hủy!',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Đồng ý duyệt',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    confirmApprove = true;
                    if (typeof formApprove.requestSubmit === 'function') {
                        formApprove.requestSubmit();
                    } else {
                        formApprove.submit();
                    }
                }
            });
        }, true);
    }

    if (btnReject) {
        btnReject.onclick = function () {
            Swal.fire({
                title: 'Từ chối phiếu hủy',
                text: 'Vui lòng nhập lý do từ chối lập phiếu:',
                input: 'textarea',
                inputPlaceholder: 'Nhập lý do cụ thể tại đây...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Xác nhận từ chối',
                cancelButtonText: 'Quay lại',
                inputValidator: (value) => {
                    if (!value || value.trim() === '') {
                        return 'Lý do từ chối là bắt buộc!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    inputLyDo.value = result.value.trim();
                    if (typeof formReject.requestSubmit === 'function') {
                        formReject.requestSubmit();
                    } else {
                        formReject.submit();
                    }
                }
            });
        };
    }
}

//-----TỰ ĐỘNG KHỞI TẠO-----
function initPhieuHuyGlobal() {
    const phieuHuyForm = document.getElementById('form-phieu-huy');
    if (phieuHuyForm && !phieuHuyForm.dataset.initialized) {
        initPhieuHuyForm();
        phieuHuyForm.dataset.initialized = 'true';
    }

    const btnReject = document.getElementById('btn-reject');
    if (btnReject && !btnReject.dataset.initialized) {
        initPhieuHuyDetailActions();
        btnReject.dataset.initialized = 'true';
    }
}

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    initPhieuHuyGlobal();
} else {
    document.addEventListener('DOMContentLoaded', initPhieuHuyGlobal);
}

document.addEventListener('htmx:load', initPhieuHuyGlobal);

document.addEventListener('htmx:beforeSwap', function () {
    //Giải phóng class và style của SweetAlert2 để tránh lỗi khóa cuộn trang (scroll lock) khi HTMX swap trang
    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
    if (document.documentElement) {
        document.documentElement.classList.remove('swal2-shown', 'swal2-height-auto');
    }
    document.body.style.overflow = '';
});
