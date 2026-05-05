(function () {
    const root = document.getElementById('pos-root');
    if (!root) return;

    const scanUrl = root.dataset.scanUrl;
    const checkoutUrl = root.dataset.checkoutUrl;
    const beepUrl = root.dataset.beepUrl;
    const scannerBeep = beepUrl ? new Audio(beepUrl) : null;
    const wrongBeepUrl = root.dataset.wrongBeepUrl;
    const scannerWrongBeep = wrongBeepUrl ? new Audio(wrongBeepUrl) : null;
    const cartBody = document.getElementById('cart-body');
    const cartCount = document.getElementById('cart-count');
    const subtotalEl = document.getElementById('subtotal');
    const grandTotalEl = document.getElementById('grand-total');
    const changeDueEl = document.getElementById('change-due');
    const customerPaidEl = document.getElementById('customer-paid');
    const paymentMethodEl = document.getElementById('payment-method');
    const statusEl = document.getElementById('pos-status');
    const toggleCameraBtn = document.getElementById('toggle-camera');
    const checkoutButton = document.getElementById('checkout-button');
    const clearCartBtn = document.getElementById('clear-cart');
    const manualForm = document.getElementById('manual-scan-form');
    const manualBarcodeEl = document.getElementById('manual-barcode');

    let cart = [];
    let scanner = null;
    let scannerRunning = false;
    let lastCode = '';
    let lastAt = 0;

    function money(value) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(Number(value) || 0)) + ' VND';
    }

    function qty(value) {
        const formatted = new Intl.NumberFormat('vi-VN', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 3
        }).format(Number(value) || 0);

        return formatted;
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function notify(message, type) {
        statusEl.textContent = message || '';
        statusEl.className = 'pos-status' + (type ? ' pos-status--' + type : '');
    }

    function isValidEan13(barcode) {
        if (!/^\d{13}$/.test(barcode)) return false;

        let sum = 0;
        for (let i = 0; i < 12; i++) {
            const digit = Number(barcode[i]);
            sum += i % 2 === 0 ? digit : digit * 3;
        }

        const checkDigit = (10 - (sum % 10)) % 10;
        return checkDigit === Number(barcode[12]);
    }

    function classifyBarcode(barcode) {
        const numeric = String(barcode || '').replace(/\D+/g, '');

        if (/^20\d{11}$/.test(numeric) && isValidEan13(numeric)) {
            return {
                type: 'TEM_CAN',
                scaleCode: numeric.slice(2, 7),
                amount: Number(numeric.slice(7, 12))
            };
        }

        return { type: 'CO_DINH' };
    }

    function getTotal() {
        return cart.reduce((total, item) => total + Number(item.tong_tien || 0), 0);
    }

    function recalcLine(item) {
        if (item.loai_ma_vach === 'CO_DINH') {
            item.tong_tien = Number(item.so_luong || 0) * Number(item.gia_ban || 0);
        }
    }

    function normalizeScannedItem(item) {
        const type = item.loai_ma_vach === 'TEM_CAN' ? 'TEM_CAN' : 'CO_DINH';

        return {
            id: type + '-' + Date.now() + '-' + Math.random().toString(16).slice(2),
            loai_ma_vach: type,
            ma_hang_hoa: item.ma_hang_hoa,
            ten_hang_hoa: item.ten_hang_hoa,
            don_vi_tinh: item.don_vi_tinh || '',
            so_luong: Number(item.so_luong || 0),
            trong_luong: item.trong_luong === null || item.trong_luong === undefined ? null : Number(item.trong_luong),
            gia_ban: Number(item.gia_ban || 0),
            tong_tien: Number(item.tong_tien || 0),
            ma_vach_quet: item.ma_vach_quet || ''
        };
    }

    function addLineToCart(item) {
        const line = normalizeScannedItem(item);

        if (line.loai_ma_vach === 'CO_DINH') {
            const existing = cart.find((cartLine) =>
                cartLine.loai_ma_vach === 'CO_DINH' && cartLine.ma_hang_hoa === line.ma_hang_hoa
            );

            if (existing) {
                existing.so_luong = Number(existing.so_luong || 0) + Number(line.so_luong || 1);
                existing.ma_vach_quet = line.ma_vach_quet || existing.ma_vach_quet;
                recalcLine(existing);
                renderCart();
                notify('Đã cộng thêm ' + existing.ten_hang_hoa + ' vào hóa đơn.', 'success');
                return;
            }
        }

        cart.push(line);
        renderCart();
        notify('Đã thêm ' + line.ten_hang_hoa + ' vào hóa đơn.', 'success');
    }

    function renderCart() {
        cartBody.innerHTML = '';

        if (cart.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="7" class="empty-state">Chưa có sản phẩm trong hóa đơn.</td>';
            cartBody.appendChild(row);
        } else {
            cart.forEach((item, index) => {
                const row = document.createElement('tr');
                row.dataset.id = item.id;

                const quantityControl = item.loai_ma_vach === 'CO_DINH'
                    ? `<input type="number" class="input cart-qty" min="0.001" step="any" value="${escapeHtml(item.so_luong)}">`
                    : `<strong>${escapeHtml(qty(item.so_luong))}</strong> ${escapeHtml(item.don_vi_tinh)}<br><span class="meta-text">Tem cân</span>`;

                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>
                        <strong>${escapeHtml(item.ten_hang_hoa)}</strong><br>
                        <span class="meta-text">${escapeHtml(item.ma_hang_hoa)}${item.ma_vach_quet ? ' / ' + escapeHtml(item.ma_vach_quet) : ''}</span>
                    </td>
                    <td><span class="badge badge--${item.loai_ma_vach === 'TEM_CAN' ? 'warning' : 'neutral'}">${item.loai_ma_vach === 'TEM_CAN' ? 'Tem cân' : 'Cố định'}</span></td>
                    <td>${quantityControl}</td>
                    <td>${escapeHtml(money(item.gia_ban))}</td>
                    <td><strong>${escapeHtml(money(item.tong_tien))}</strong></td>
                    <td>
                        <button type="button" class="button button--ghost button--sm cart-remove" aria-label="Xóa dòng">&times;</button>
                    </td>
                `;

                cartBody.appendChild(row);
            });
        }

        cartCount.textContent = cart.length + ' dòng';
        updateTotals();
    }

    function updateTotals() {
        const total = getTotal();
        const paid = Number(customerPaidEl.value || 0);
        const change = Math.max(0, paid - total);

        subtotalEl.textContent = money(total);
        grandTotalEl.textContent = money(total);
        changeDueEl.textContent = money(change);
    }

    async function postJson(url, payload) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        });

        const json = await response.json();
        if (!json.success) {
            throw new Error(json.message || 'Thao tác không thành công.');
        }

        return json;
    }

    function triggerSuccessFeedback() {
        if (scannerBeep) {
            scannerBeep.currentTime = 0;
            scannerBeep.play().catch(function (e) {
                console.warn('MP3 playback failed/blocked:', e);
            });
        }

        if (typeof navigator !== 'undefined' && navigator.vibrate) {
            navigator.vibrate(50);
        }

        const reader = document.getElementById('barcode-reader');
        if (reader) {
            const originalBorder = reader.style.borderColor;
            const originalShadow = reader.style.boxShadow;
            reader.style.transition = 'border-color 0.15s ease, box-shadow 0.15s ease';
            reader.style.borderColor = '#10b981';
            reader.style.boxShadow = '0 0 16px rgba(16, 185, 129, 0.7)';
            setTimeout(() => {
                reader.style.borderColor = originalBorder;
                reader.style.boxShadow = originalShadow;
            }, 250);
        }
    }

    function triggerErrorFeedback() {
        if (scannerWrongBeep) {
            scannerWrongBeep.currentTime = 0;
            scannerWrongBeep.play().catch(function (e) {
                console.warn('Wrong MP3 playback failed/blocked:', e);
            });
        }
    }

    async function processBarcode(barcode, isManual) {
        const normalized = String(barcode || '').trim();
        if (normalized === '') return;

        const manual = isManual === true;

        if (!manual && normalized === lastCode && Date.now() - lastAt < 2500) {
            return;
        }

        const classification = classifyBarcode(normalized);

        if (!manual) {
            lastCode = normalized;
            lastAt = Date.now();
        }

        notify(
            classification.type === 'TEM_CAN'
                ? 'Đang xử lý tem cân ' + classification.scaleCode + ' / ' + money(classification.amount) + '...'
                : 'Đang xử lý mã ' + normalized + '...',
            'info'
        );

        try {
            const json = await postJson(scanUrl, { barcode: normalized });
            addLineToCart(json.data);
            triggerSuccessFeedback();
        } catch (error) {
            notify(error.message, 'error');
            triggerErrorFeedback();
        }
    }

    function createScanner() {
        if (!window.Html5Qrcode) {
            throw new Error('Không tải được thư viện quét mã vạch.');
        }

        const config = {};
        if (window.Html5QrcodeSupportedFormats) {
            config.formatsToSupport = [
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.CODE_128
            ];
        }

        return new Html5Qrcode('barcode-reader', config);
    }

    async function toggleCamera() {
        try {
            if (!scannerRunning) {
                scanner = scanner || createScanner();
                await scanner.start(
                    { facingMode: 'environment' },
                    {
                        fps: 15,
                        qrbox: function (viewfinderWidth, viewfinderHeight) {
                            const w = Math.floor(viewfinderWidth * 0.85);
                            const h = Math.floor(viewfinderHeight * 0.55);
                            return {
                                width: Math.max(w, 280),
                                height: Math.max(h, 130)
                            };
                        }
                    },
                    processBarcode,
                    function () { }
                );
                scannerRunning = true;
                toggleCameraBtn.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Tắt camera';
                notify('Camera đang bật.', 'success');
            } else {
                await scanner.stop();
                scanner.clear();
                scannerRunning = false;
                toggleCameraBtn.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M4 7h4l2-3h4l2 3h4v13H4V7zm8 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>Bật camera';
                notify('Camera đã tắt.', 'info');
            }
        } catch (error) {
            notify(error.message || 'Không thể bật camera.', 'error');
        }
    }

    cartBody.addEventListener('change', function (event) {
        const input = event.target.closest('.cart-qty');
        if (!input) return;

        const row = input.closest('tr');
        const item = cart.find((cartLine) => cartLine.id === row.dataset.id);
        if (!item) return;

        item.so_luong = Math.max(0.001, Number(input.value || 0));
        recalcLine(item);
        renderCart();
    });

    cartBody.addEventListener('click', function (event) {
        const button = event.target.closest('.cart-remove');
        if (!button) return;

        const row = button.closest('tr');
        cart = cart.filter((item) => item.id !== row.dataset.id);
        renderCart();
    });

    manualForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const barcode = manualBarcodeEl.value.trim();
        manualBarcodeEl.value = '';
        processBarcode(barcode, true);
    });

    customerPaidEl.addEventListener('input', updateTotals);
    toggleCameraBtn.addEventListener('click', toggleCamera);

    clearCartBtn.addEventListener('click', function () {
        if (cart.length === 0 || window.confirm('Hủy toàn bộ hóa đơn hiện tại?')) {
            cart = [];
            renderCart();
            notify('Đã làm trống hóa đơn.', 'info');
        }
    });

    checkoutButton.addEventListener('click', async function () {
        const total = getTotal();
        const paid = Number(customerPaidEl.value || 0);

        if (cart.length === 0) {
            notify('Giỏ hàng trống.', 'error');
            return;
        }

        if (paid < total) {
            notify('Tiền khách đưa chưa đủ.', 'error');
            return;
        }

        checkoutButton.disabled = true;
        notify('Đang hoàn tất thanh toán...', 'info');

        try {
            const payload = {
                phuong_thuc_thanh_toan: paymentMethodEl.value,
                tien_khach_dua: paid,
                items: cart.map((item) => ({
                    loai_ma_vach: item.loai_ma_vach,
                    ma_hang_hoa: item.ma_hang_hoa,
                    so_luong: item.so_luong,
                    ma_vach_quet: item.ma_vach_quet
                }))
            };

            const json = await postJson(checkoutUrl, payload);
            window.location.href = json.detail_url;
        } catch (error) {
            notify(error.message, 'error');
            checkoutButton.disabled = false;
        }
    });

    window.addEventListener('beforeunload', function () {
        if (scannerRunning && scanner) {
            scanner.stop().catch(function () { });
        }
    });

    //Tự động bôi đen toàn bộ khi click/focus vào ô số lượng/tiền khách đưa để nhập liệu nhanh
    document.addEventListener('focus', function (e) {
        if (e.target && (e.target.classList.contains('cart-qty') || e.target.id === 'customer-paid')) {
            setTimeout(() => {
                if (typeof e.target.select === 'function') {
                    e.target.select();
                }
            }, 50);
        }
    }, true);

    renderCart();
    toggleCamera();
})();
