<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-3">
    <!-- SEKSI KIRI: KATALOG MENU CAFE -->
    <div class="col-lg-7 col-xl-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="search-product" class="form-control bg-light border-start-0" placeholder="Cari menu...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-1 overflow-auto pb-1" id="category-filters">
                            <button class="btn btn-sm btn-primary active filter-cat" data-cat="all">Semua</button>
                            <?php foreach ($categories as $cat): ?>
                                <button class="btn btn-sm btn-outline-secondary filter-cat text-nowrap" data-cat="<?= $cat['id'] ?>">
                                    <?= esc($cat['name']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-3 overflow-auto" style="max-height: calc(100vh - 180px);">
                <div class="row g-3" id="product-list">
                    <?php foreach ($products as $p): ?>
                        <div class="col-6 col-sm-4 col-md-3 product-item" data-cat="<?= $p['category_id'] ?>" data-name="<?= strtolower(esc($p['name'])) ?>">
                            <div class="card h-100 border-0 shadow-sm product-card position-relative <?= $p['is_available'] == 0 ? 'disabled-card' : '' ?>" 
                                 onclick="selectProduct(<?= $p['id'] ?>, <?= $p['is_available'] ?>)">
                                
                                <div class="ratio ratio-4x3 bg-light rounded-top overflow-hidden">
                                    <?php if ($p['image']): ?>
                                        <img src="<?= base_url($p['image']) ?>" class="card-img-top object-fit-cover" alt="<?= esc($p['name']) ?>">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-secondary">
                                            <i class="bi bi-cup-hot fs-1"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($p['is_available'] == 0): ?>
                                    <div class="sold-out-overlay">
                                        <span class="badge bg-danger px-3 py-2 fw-bold shadow">HABIS</span>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body p-2 d-flex flex-column justify-content-between">
                                    <div>
                                        <h6 class="card-title fw-bold text-dark mb-1 small line-clamp-2"><?= esc($p['name']) ?></h6>
                                    </div>
                                    <div class="mt-2">
                                        <span class="fw-bold text-primary small">Rp <?= number_format($p['base_price'], 0, ',', '.') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- SEKSI KANAN: KERANJANG & RINCIAN PESANAN -->
    <div class="col-lg-5 col-xl-4">
        <div class="card border-0 shadow-sm h-100 d-flex flex-column">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold mb-0"><i class="bi bi-cart3 me-2"></i>Pesanan Aktif</h6>
                <button class="btn btn-sm btn-outline-danger" onclick="clearCart()"><i class="bi bi-trash me-1"></i>Reset</button>
            </div>

            <!-- CART ITEM LIST -->
            <div class="card-body p-3 flex-grow-1 overflow-auto" style="max-height: 320px;" id="cart-items-container">
                <div class="text-center text-muted py-5" id="cart-empty-state">
                    <i class="bi bi-cart-x fs-1 opacity-50"></i>
                    <p class="small mt-2">Keranjang masih kosong.<br>Klik menu di sebelah kiri untuk menambahkan.</p>
                </div>
                <div class="list-group list-group-flush" id="cart-items-list"></div>
            </div>

            <!-- PERHITUNGAN & TRANSAKSI FORM -->
            <div class="card-footer bg-light p-3 border-top">
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold mb-1">Tipe Pesanan</label>
                        <select id="order-type" class="form-select form-select-sm" onchange="toggleTableSelect()">
                            <option value="dine_in">DINE-IN</option>
                            <option value="takeaway">TAKEAWAY</option>
                        </select>
                    </div>
                    <div class="col-6" id="table-select-wrapper">
                        <label class="form-label small fw-semibold mb-1">Pilih Meja</label>
                        <select id="table-id" class="form-select form-select-sm">
                            <option value="">-- Pilih Meja --</option>
                            <?php foreach ($tables as $tbl): ?>
                                <option value="<?= $tbl['id'] ?>" <?= $tbl['status'] == 'occupied' ? 'disabled' : '' ?>>
                                    <?= esc($tbl['table_number']) ?> (<?= esc($tbl['capacity']) ?> org) <?= $tbl['status'] == 'occupied' ? '- Terisi' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="bg-white p-2 rounded border mb-2 small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-semibold" id="summary-subtotal">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Pajak (<?= $tax_rate ?>%)</span>
                        <span class="fw-semibold" id="summary-tax">Rp 0</span>
                    </div>
                    <?php if ($service_rate > 0): ?>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Service Charge (<?= $service_rate ?>%)</span>
                        <span class="fw-semibold" id="summary-service">Rp 0</span>
                    </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Diskon (Rp)</span>
                        <input type="number" id="input-discount" class="form-control form-control-sm text-end w-50 py-0" value="0" onchange="calculateTotals()">
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between fw-bold text-primary fs-6">
                        <span>GRAND TOTAL</span>
                        <span id="summary-grand-total">Rp 0</span>
                    </div>
                </div>

                <button type="button" class="btn btn-success w-100 fw-bold py-2 shadow-sm" onclick="processCheckout()" id="btn-checkout" disabled>
                    <i class="bi bi-wallet2 me-1"></i> PROSES CHECKOUT & BAYAR
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL VARIAN & TOPPING -->
<div class="modal fade" id="modalProductOptions" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="modal-product-name">Pilih Varian & Topping</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal-product-id">
                <input type="hidden" id="modal-base-price">

                <div class="mb-3" id="section-variants">
                    <label class="form-label small fw-bold text-uppercase text-secondary">Varian</label>
                    <div class="d-flex flex-column gap-2" id="variants-container"></div>
                </div>

                <div class="mb-3" id="section-toppings">
                    <label class="form-label small fw-bold text-uppercase text-secondary">Topping Tambahan</label>
                    <div class="d-flex flex-wrap gap-2" id="toppings-container"></div>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-semibold">Catatan Pesanan</label>
                    <input type="text" id="modal-notes" class="form-control form-control-sm" placeholder="misal: Less ice, extra hot...">
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-primary btn-sm w-100 fw-bold" onclick="addSelectedToCart()">
                    Tambahkan Ke Keranjang - <span id="modal-total-price">Rp 0</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PEMBAYARAN KASIR -->
<div class="modal fade" id="modalPayment" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title fw-bold"><i class="bi bi-cash-stack me-2"></i>Pembayaran Kasir</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pay-order-id">
                
                <div class="text-center bg-light p-3 rounded mb-3 border">
                    <div class="text-muted small fw-semibold text-uppercase">Total Tagihan</div>
                    <div class="fs-3 fw-bold text-primary" id="pay-grand-total">Rp 0</div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Metode Pembayaran</label>
                    <select id="pay-method" class="form-select" onchange="togglePaymentMethod()">
                        <option value="cash">CASH (Tunai)</option>
                        <option value="non_cash">NON-CASH (QRIS / Debit / Transfer)</option>
                    </select>
                </div>

                <div id="wrapper-non-cash" style="display: none;" class="mb-3">
                    <label class="form-label small fw-semibold">Pilih Provider Non-Cash</label>
                    <select id="pay-provider" class="form-select form-select-sm mb-2">
                        <option value="QRIS">QRIS Statis/Dinamis</option>
                        <option value="GoPay">GoPay</option>
                        <option value="OVO">OVO</option>
                        <option value="DANA">DANA</option>
                        <option value="ShopeePay">ShopeePay</option>
                        <option value="EDC BCA">Debit / Kredit BCA</option>
                        <option value="EDC Mandiri">Debit / Kredit Mandiri</option>
                        <option value="Transfer">Transfer Bank Direct</option>
                    </select>
                    <input type="text" id="pay-ref" class="form-control form-control-sm" placeholder="Nomor Referensi Transaksi (Opsional)">
                </div>

                <div id="wrapper-cash" class="mb-3">
                    <label class="form-label small fw-bold">Jumlah Uang Diterima (Rp)</label>
                    <input type="number" id="pay-amount" class="form-control form-control-lg fw-bold text-end text-success mb-2" onkeyup="calculateChange()" placeholder="0">
                    
                    <div class="d-flex gap-1 flex-wrap mb-2" id="quick-money-buttons">
                        <button class="btn btn-sm btn-outline-secondary" onclick="setQuickMoney('exact')">Uang Pas</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="setQuickMoney(10000)">10rb</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="setQuickMoney(20000)">20rb</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="setQuickMoney(50000)">50rb</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="setQuickMoney(100000)">100rb</button>
                    </div>

                    <div class="p-2 bg-light rounded border d-flex justify-content-between align-items-center">
                        <span class="fw-bold small">Kembalian:</span>
                        <span class="fs-5 fw-bold text-danger" id="pay-change">Rp 0</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-success w-100 fw-bold py-2" onclick="submitPayment()">
                    <i class="bi bi-check-circle me-1"></i> SELESAIKAN PEMBAYARAN
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL SUKSES TRANSAKSI & NOMOR ANTRIAN -->
<div class="modal fade" id="modalSuccess" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered text-center">
        <div class="modal-content border-0 shadow">
            <div class="modal-body p-4">
                <div class="text-success mb-2">
                    <i class="bi bi-check-circle-fill display-3"></i>
                </div>
                <h5 class="fw-bold">Pembayaran Berhasil!</h5>
                <p class="text-muted small mb-3">Pesanan telah dikirim ke dapur/bar.</p>

                <div class="bg-light p-3 rounded border mb-3">
                    <div class="text-muted small fw-semibold text-uppercase">Nomor Antrian Anda</div>
                    <div class="display-4 fw-bold text-primary my-1" id="success-queue-number">A000</div>
                    <div class="text-secondary small" id="success-invoice-number">INV-00000000-0000</div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary w-50" onclick="window.location.reload()">Selesai / Transaksi Baru</button>
                    <button class="btn btn-primary w-50 fw-bold" onclick="printReceipt()"><i class="bi bi-printer me-1"></i> Cetak Struk</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .product-card { cursor: pointer; transition: transform 0.15s ease-in-out; }
    .product-card:hover { transform: translateY(-2px); }
    .disabled-card { opacity: 0.6; cursor: not-allowed; }
    .sold-out-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.7); display: flex; align-items: center; justify-content: center; z-index: 5;
    }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const TAX_RATE = <?= $tax_rate ?>;
    const SERVICE_RATE = <?= $service_rate ?>;
    let cart = [];
    let currentGrandTotal = 0;
    let lastPaidOrderId = null;

    // Filter Kategori & Search
    document.querySelectorAll('.filter-cat').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-cat').forEach(b => b.classList.remove('btn-primary', 'active'));
            document.querySelectorAll('.filter-cat').forEach(b => b.classList.add('btn-outline-secondary'));
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-primary', 'active');
            
            const cat = this.getAttribute('data-cat');
            filterProducts(cat, document.getElementById('search-product').value.toLowerCase());
        });
    });

    document.getElementById('search-product').addEventListener('input', function() {
        const cat = document.querySelector('.filter-cat.active').getAttribute('data-cat');
        filterProducts(cat, this.value.toLowerCase());
    });

    function filterProducts(cat, search) {
        document.querySelectorAll('.product-item').forEach(item => {
            const itemCat = item.getAttribute('data-cat');
            const itemName = item.getAttribute('data-name');
            const matchCat = (cat === 'all' || itemCat === cat);
            const matchSearch = itemName.includes(search);
            item.style.display = (matchCat && matchSearch) ? 'block' : 'none';
        });
    }

    function toggleTableSelect() {
        const type = document.getElementById('order-type').value;
        const wrapper = document.getElementById('table-select-wrapper');
        wrapper.style.display = (type === 'dine_in') ? 'block' : 'none';
    }

    function selectProduct(id, isAvailable) {
        if (!isAvailable) return;
        
        fetch(`<?= base_url('/admin/pos/product-details/') ?>${id}`)
            .then(res => res.json())
            .then(data => {
                if (!data.status) return;
                
                const p = data.product;
                document.getElementById('modal-product-id').value = p.id;
                document.getElementById('modal-product-name').innerText = p.name;
                document.getElementById('modal-base-price').value = p.base_price;
                document.getElementById('modal-notes').value = '';

                const vContainer = document.getElementById('variants-container');
                vContainer.innerHTML = '';
                if (data.variants.length > 0) {
                    document.getElementById('section-variants').style.display = 'block';
                    data.variants.forEach((v, idx) => {
                        vContainer.innerHTML += `
                            <div class="form-check">
                                <input class="form-check-input variant-radio" type="radio" name="variant" id="v-${v.id}" value="${v.id}" data-name="${v.name}" data-price="${v.price_adjustment}" ${idx === 0 ? 'checked' : ''} onchange="updateModalPrice()">
                                <label class="form-check-label d-flex justify-content-between" for="v-${v.id}">
                                    <span>${v.name}</span>
                                    <span class="text-muted">+Rp ${parseInt(v.price_adjustment).toLocaleString('id-ID')}</span>
                                </label>
                            </div>`;
                    });
                } else {
                    document.getElementById('section-variants').style.display = 'none';
                }

                const tContainer = document.getElementById('toppings-container');
                tContainer.innerHTML = '';
                if (data.toppings.length > 0) {
                    document.getElementById('section-toppings').style.display = 'block';
                    data.toppings.forEach(t => {
                        tContainer.innerHTML += `
                            <div class="form-check me-3 mb-1">
                                <input class="form-check-input topping-checkbox" type="checkbox" id="t-${t.id}" value="${t.id}" data-name="${t.name}" data-price="${t.price}" onchange="updateModalPrice()">
                                <label class="form-check-label" for="t-${t.id}">${t.name} (+Rp ${parseInt(t.price).toLocaleString('id-ID')})</label>
                            </div>`;
                    });
                } else {
                    document.getElementById('section-toppings').style.display = 'none';
                }

                updateModalPrice();
                const modal = new bootstrap.Modal(document.getElementById('modalProductOptions'));
                modal.show();
            });
    }

    function updateModalPrice() {
        const base = parseFloat(document.getElementById('modal-base-price').value) || 0;
        let variantPrice = 0;
        const selectedVariant = document.querySelector('.variant-radio:checked');
        if (selectedVariant) {
            variantPrice = parseFloat(selectedVariant.getAttribute('data-price')) || 0;
        }

        let toppingPrice = 0;
        document.querySelectorAll('.topping-checkbox:checked').forEach(cb => {
            toppingPrice += parseFloat(cb.getAttribute('data-price')) || 0;
        });

        const total = base + variantPrice + toppingPrice;
        document.getElementById('modal-total-price').innerText = `Rp ${total.toLocaleString('id-ID')}`;
    }

    function addSelectedToCart() {
        const productId = document.getElementById('modal-product-id').value;
        const productName = document.getElementById('modal-product-name').innerText;
        const basePrice = parseFloat(document.getElementById('modal-base-price').value);
        const notes = document.getElementById('modal-notes').value;

        let variantId = null;
        let variantName = '';
        let variantPrice = 0;
        const selectedVariant = document.querySelector('.variant-radio:checked');
        if (selectedVariant) {
            variantId = selectedVariant.value;
            variantName = selectedVariant.getAttribute('data-name');
            variantPrice = parseFloat(selectedVariant.getAttribute('data-price'));
        }

        let toppings = [];
        let totalToppingPrice = 0;
        document.querySelectorAll('.topping-checkbox:checked').forEach(cb => {
            const tPrice = parseFloat(cb.getAttribute('data-price'));
            toppings.push({ id: cb.value, name: cb.getAttribute('data-name'), price: tPrice });
            totalToppingPrice += tPrice;
        });

        const unitPrice = basePrice + variantPrice + totalToppingPrice;
        const cartKey = `${productId}-${variantId}-${toppings.map(t=>t.id).join(',')}-${notes}`;

        const existingIdx = cart.findIndex(item => item.key === cartKey);
        if (existingIdx > -1) {
            cart[existingIdx].qty += 1;
        } else {
            cart.push({
                key: cartKey,
                productId, productName, variantId, variantName, toppings, unitPrice, qty: 1, notes
            });
        }

        const modalEl = document.getElementById('modalProductOptions');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();

        renderCart();
    }

    function renderCart() {
        const list = document.getElementById('cart-items-list');
        const emptyState = document.getElementById('cart-empty-state');
        list.innerHTML = '';

        if (cart.length === 0) {
            emptyState.style.display = 'block';
            document.getElementById('btn-checkout').disabled = true;
            calculateTotals();
            return;
        }

        emptyState.style.display = 'none';
        document.getElementById('btn-checkout').disabled = false;

        cart.forEach((item, index) => {
            const subtotal = item.unitPrice * item.qty;
            let toppingsText = item.toppings.map(t => t.name).join(', ');
            
            list.innerHTML += `
                <div class="list-group-item px-0 py-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="me-2">
                            <div class="fw-bold small">${item.productName} ${item.variantName ? '(' + item.variantName + ')' : ''}</div>
                            ${toppingsText ? `<div class="text-muted text-xs">+ ${toppingsText}</div>` : ''}
                            ${item.notes ? `<div class="fst-italic text-secondary text-xs"><i class="bi bi-pencil-square"></i> ${item.notes}</div>` : ''}
                            <div class="text-primary fw-semibold small mt-1">Rp ${item.unitPrice.toLocaleString('id-ID')}</div>
                        </div>
                        <div class="text-end">
                            <div class="btn-group btn-group-sm mb-1">
                                <button class="btn btn-outline-secondary py-0 px-2" onclick="updateQty(${index}, -1)">-</button>
                                <span class="btn btn-light disabled text-dark py-0 px-2">${item.qty}</span>
                                <button class="btn btn-outline-secondary py-0 px-2" onclick="updateQty(${index}, 1)">+</button>
                            </div>
                            <div class="fw-bold small text-dark">Rp ${subtotal.toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                </div>`;
        });

        calculateTotals();
    }

    function updateQty(index, delta) {
        cart[index].qty += delta;
        if (cart[index].qty <= 0) {
            cart.splice(index, 1);
        }
        renderCart();
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function calculateTotals() {
        const subtotal = cart.reduce((sum, item) => sum + (item.unitPrice * item.qty), 0);
        const tax = (subtotal * TAX_RATE) / 100;
        const service = (subtotal * SERVICE_RATE) / 100;
        const discount = parseFloat(document.getElementById('input-discount').value) || 0;
        currentGrandTotal = Math.max(0, subtotal + tax + service - discount);

        document.getElementById('summary-subtotal').innerText = `Rp ${subtotal.toLocaleString('id-ID')}`;
        document.getElementById('summary-tax').innerText = `Rp ${tax.toLocaleString('id-ID')}`;
        if (document.getElementById('summary-service')) {
            document.getElementById('summary-service').innerText = `Rp ${service.toLocaleString('id-ID')}`;
        }
        document.getElementById('summary-grand-total').innerText = `Rp ${currentGrandTotal.toLocaleString('id-ID')}`;
    }

    function processCheckout() {
        const orderType = document.getElementById('order-type').value;
        const tableId = document.getElementById('table-id').value;

        if (orderType === 'dine_in' && !tableId) {
            alert('Wajib memilih nomor meja untuk tipe pesanan DINE-IN.');
            return;
        }

        const payload = {
            order_type: orderType,
            table_id: tableId,
            discount: parseFloat(document.getElementById('input-discount').value) || 0,
            cart: cart
        };

        fetch('<?= base_url('/admin/pos/checkout') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                lastPaidOrderId = data.data.order_id;
                document.getElementById('pay-order-id').value = data.data.order_id;
                document.getElementById('pay-grand-total').innerText = `Rp ${parseInt(data.data.grand_total).toLocaleString('id-ID')}`;
                document.getElementById('pay-amount').value = '';
                document.getElementById('pay-change').innerText = 'Rp 0';
                
                // Reset form pembayaran ke default Cash
                document.getElementById('pay-method').value = 'cash';
                togglePaymentMethod();

                const payModal = new bootstrap.Modal(document.getElementById('modalPayment'));
                payModal.show();
            } else {
                alert('Gagal membuat pesanan: ' + data.message);
            }
        });
    }

    function togglePaymentMethod() {
        const method = document.getElementById('pay-method').value;
        const wrapperNonCash = document.getElementById('wrapper-non-cash');
        const wrapperCash = document.getElementById('wrapper-cash');
        const payProvider = document.getElementById('pay-provider');
        const payRef = document.getElementById('pay-ref');

        if (method === 'cash') {
            wrapperCash.style.display = 'block';
            wrapperNonCash.style.display = 'none';
            payProvider.value = ''; // Kosongkan provider agar tidak terkirim saat cash
            payRef.value = '';     // Kosongkan nomor referensi
        } else {
            wrapperCash.style.display = 'none';
            wrapperNonCash.style.display = 'block';
            if (!payProvider.value) {
                payProvider.value = 'QRIS'; // Set default kembali ke QRIS jika kosong
            }
        }
    }

    function setQuickMoney(val) {
        if (val === 'exact') {
            document.getElementById('pay-amount').value = currentGrandTotal;
        } else {
            document.getElementById('pay-amount').value = val;
        }
        calculateChange();
    }

    function calculateChange() {
        const paid = parseFloat(document.getElementById('pay-amount').value) || 0;
        const change = paid - currentGrandTotal;
        document.getElementById('pay-change').innerText = `Rp ${Math.max(0, change).toLocaleString('id-ID')}`;
    }

    function submitPayment() {
        const orderId = document.getElementById('pay-order-id').value;
        const method = document.getElementById('pay-method').value;
        const provider = (method === 'cash') ? null : document.getElementById('pay-provider').value;
        const amountPaid = parseFloat(document.getElementById('pay-amount').value) || 0;
        const refNumber = (method === 'cash') ? null : document.getElementById('pay-ref').value;

        if (method === 'cash' && amountPaid < currentGrandTotal) {
            alert('Jumlah uang pembayaran tunai kurang.');
            return;
        }

        const payload = {
            order_id: orderId,
            payment_method: method,
            payment_provider: provider,
            amount_paid: amountPaid,
            reference_number: refNumber
        };

        fetch('<?= base_url('/admin/pos/process-payment') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                const payModalEl = document.getElementById('modalPayment');
                const payModal = bootstrap.Modal.getInstance(payModalEl);
                payModal.hide();

                document.getElementById('success-queue-number').innerText = data.data.queue_number;
                document.getElementById('success-invoice-number').innerText = data.data.order_number;

                const successModal = new bootstrap.Modal(document.getElementById('modalSuccess'));
                successModal.show();
            } else {
                alert('Gagal memproses pembayaran: ' + data.message);
            }
        });
    }

    function printReceipt() {
        if (lastPaidOrderId) {
            window.open(`<?= base_url('/admin/orders/print/') ?>${lastPaidOrderId}`, '_blank', 'width=400,height=600');
        }
    }
</script>
<?= $this->endSection() ?>