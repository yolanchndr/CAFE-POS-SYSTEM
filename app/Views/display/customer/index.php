<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="shortcut icon" href="<?= base_url(setting('cafe_favicon', 'assets/images/favicon.ico')) ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-base: #090d16;
            --surface: #111827;
            --surface-glass: rgba(17, 24, 39, 0.75);
            --border-color: rgba(255, 255, 255, 0.08);
            --accent-gold: #f59e0b;
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--bg-base);
            color: #f3f4f6;
            height: 100vh;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .header-display {
            background: var(--surface-glass);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            height: 85px;
        }

        .menu-section {
            height: calc(100vh - 85px);
            overflow-y: auto;
            padding: 2rem;
        }

        .order-section {
            background: rgba(13, 17, 23, 0.95);
            border-left: 1px solid var(--border-color);
            height: calc(100vh - 85px);
            display: flex;
            flex-direction: column;
            padding: 2rem;
        }

        /* Scrollbars */
        .menu-section::-webkit-scrollbar,
        #active-items-list::-webkit-scrollbar {
            width: 5px;
        }
        .menu-section::-webkit-scrollbar-thumb,
        #active-items-list::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .product-card {
            background: var(--surface);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .product-card:hover {
            transform: translateY(-3px);
            border-color: rgba(245, 158, 11, 0.3);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.08);
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .sold-out-overlay {
            position: absolute;
            inset: 0;
            background: rgba(9, 13, 22, 0.85);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        #active-items-list {
            overflow-y: auto;
            min-height: 0;
            padding-right: 4px;
        }

        .order-item {
            transition: background-color 0.2s ease;
            border-bottom: 1px solid var(--border-color) !important;
        }

        .connection-indicator {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #34d399;
            margin-right: 6px;
            box-shadow: 0 0 8px rgba(52, 211, 153, 0.5);
        }
        .connection-indicator.offline {
            background: #f87171;
            box-shadow: 0 0 8px rgba(248, 113, 113, 0.5);
        }

        .display-status {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 3px;
        }

        @media (max-width: 992px) {
            .header-display, .menu-section, .order-section { padding: 1rem; }
        }
    </style>
</head>
<body>

    <!-- HEADER CUSTOMER DISPLAY -->
    <header class="header-display d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning border border-warning border-opacity-10">
                <i class="bi bi-cup-hot-fill fs-5"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-white tracking-tight"><?= esc(setting('cafe_name', 'Kopi Senja Utama')) ?></h4>
                <span class="text-secondary small"><?= esc(setting('cafe_description', 'Tempat Nongkrong Kopi Paling Nyaman')) ?></span>
            </div>
        </div>
        <div class="text-end">
            <div class="fs-5 fw-bold font-monospace text-warning lh-1 mb-1" id="live-clock">00:00:00</div>
            <div class="text-secondary small"><?= date('l, d F Y') ?></div>
            <div class="display-status">
                <span id="connection-indicator" class="connection-indicator"></span>
                <span id="connection-status">Menghubungkan...</span>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="container-fluid p-0">
        <div class="row g-0">
            
            <!-- SEKSI KIRI: KATALOG MENU -->
            <div class="col-7 col-xl-8 menu-section">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold mb-0 text-white d-flex align-items-center gap-2">
                        <i class="bi bi-grid-fill text-warning"></i> Daftar Menu Cafe
                    </h5>
                </div>
                <div class="row g-3">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $p): ?>
                            <div class="col-6 col-md-4 col-xl-3">
                                <div class="product-card h-100 p-2">
                                    <div class="ratio ratio-4x3 bg-dark rounded-3 overflow-hidden mb-2">
                                        <?php if (!empty($p['image'])): ?>
                                            <img src="<?= base_url($p['image']) ?>" class="product-image" alt="<?= esc($p['name']) ?>" loading="lazy">
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center text-secondary">
                                                <i class="bi bi-cup-hot fs-2"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ((int) $p['is_available'] === 0): ?>
                                        <div class="sold-out-overlay rounded-3">
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 fw-bold tracking-wider">
                                                HABIS
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <h6 class="fw-bold text-white mb-1 small text-truncate"><?= esc($p['name']) ?></h6>
                                    <div class="fw-extrabold text-warning small font-monospace">
                                        Rp <?= number_format($p['base_price'], 0, ',', '.') ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="text-center text-secondary py-5">
                                <i class="bi bi-inbox display-4 d-block mb-3 opacity-50"></i>
                                <p class="mb-0">Belum ada menu tersedia.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SEKSI KANAN: PESANAN AKTIF -->
            <div class="col-5 col-xl-4 order-section">
                <h5 class="fw-bold mb-3 text-white border-bottom border-dark pb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-receipt text-warning"></i> Pesanan Anda
                </h5>
                
                <!-- IDLE STATE -->
                <div id="display-idle" class="text-center my-auto py-5 text-secondary">
                    <div class="bg-warning bg-opacity-05 border border-warning border-opacity-10 p-4 rounded-circle d-inline-block mb-3">
                        <i class="bi bi-shop display-4 text-warning"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-1">Selamat Datang!</h5>
                    <p class="small px-4 text-secondary">Pesanan Anda akan muncul secara otomatis di layar ini saat kasir menginput transaksi.</p>
                </div>

                <!-- ACTIVE ORDER -->
                <div id="display-active" style="display: none;" class="flex-grow-1 d-flex flex-column">
                    <div class="flex-grow-1 overflow-auto mb-3" id="active-items-list"></div>
                    
                    <div class="bg-dark bg-opacity-50 p-3 rounded-4 border border-dark mt-auto shadow-sm">
                        <div class="d-flex justify-content-between mb-1 small text-secondary">
                            <span>Subtotal</span>
                            <span id="display-subtotal" class="font-monospace text-white">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small text-secondary">
                            <span>Pajak & Layanan</span>
                            <span id="display-tax" class="font-monospace text-white">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small text-secondary">
                            <span>Diskon</span>
                            <span id="display-discount" class="font-monospace text-danger">- Rp 0</span>
                        </div>
                        <hr class="border-dark my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small fw-bold text-uppercase tracking-wider text-secondary">Total Tagihan</span>
                            <span id="display-grand-total" class="fs-4 fw-extrabold font-monospace text-warning">Rp 0</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        function updateClock() {
            const now = new Date();
            const clock = document.getElementById('live-clock');
            if (clock) {
                clock.innerText = now.toLocaleTimeString('id-ID');
            }
        }
        updateClock();
        setInterval(updateClock, 1000);

        const idleView = document.getElementById('display-idle');
        const activeView = document.getElementById('display-active');
        const itemsList = document.getElementById('active-items-list');
        const connectionIndicator = document.getElementById('connection-indicator');
        const connectionStatus = document.getElementById('connection-status');

        function formatRupiah(value) {
            const number = parseInt(value) || 0;
            return 'Rp ' + number.toLocaleString('id-ID');
        }

        function setConnectionStatus(connected) {
            if (connected) {
                connectionIndicator.classList.remove('offline');
                connectionStatus.innerText = 'Terhubung';
            } else {
                connectionIndicator.classList.add('offline');
                connectionStatus.innerText = 'Koneksi terputus';
            }
        }

        function escapeHtml(value) {
            if (value === null || value === undefined) return '';
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function showIdle() {
            idleView.style.display = 'block';
            activeView.style.display = 'none';
            itemsList.innerHTML = '';
            document.getElementById('display-subtotal').innerText = 'Rp 0';
            document.getElementById('display-tax').innerText = 'Rp 0';
            document.getElementById('display-discount').innerText = 'Rp 0';
            document.getElementById('display-grand-total').innerText = 'Rp 0';
        }

        function showActiveOrder(data) {
            idleView.style.display = 'none';
            activeView.style.display = 'flex';
            activeView.style.flexDirection = 'column';

            let html = '';
            data.items.forEach(item => {
                const productName = escapeHtml(item.product_name);
                const variantName = escapeHtml(item.variant_name);
                const notes = escapeHtml(item.notes);
                
                html += `
                    <div class="order-item d-flex justify-content-between align-items-center py-2 mb-1">
                        <div class="pe-2">
                            <div class="fw-bold text-white small">
                                <span class="text-warning me-1">${escapeHtml(item.quantity)}x</span> ${productName}
                            </div>
                            ${variantName ? `<div class="text-secondary font-monospace" style="font-size: 11px;">Varian: ${variantName}</div>` : ''}
                            ${notes ? `<div class="fst-italic text-warning" style="font-size: 11px;">Note: ${notes}</div>` : ''}
                        </div>
                        <div class="fw-bold font-monospace text-white small text-nowrap">
                            ${formatRupiah(item.subtotal)}
                        </div>
                    </div>
                `;
            });
            itemsList.innerHTML = html;

            document.getElementById('display-subtotal').innerText = formatRupiah(data.subtotal);
            document.getElementById('display-tax').innerText = formatRupiah(data.tax);
            document.getElementById('display-discount').innerText = formatRupiah(data.discount);
            document.getElementById('display-grand-total').innerText = formatRupiah(data.grand_total);
        }

        let requestInProgress = false;
        async function fetchActiveOrder() {
            if (requestInProgress) return;
            requestInProgress = true;
            try {
                const response = await fetch('<?= base_url('/api/customer-display/active-order') ?>', {
                    method: 'GET',
                    cache: 'no-store',
                    headers: { 'Accept': 'application/json', 'Cache-Control': 'no-cache' }
                });
                if (!response.ok) throw new Error('HTTP Error: ' + response.status);
                const result = await response.json();
                
                setConnectionStatus(true);

                if (result.status && result.data && Array.isArray(result.data.items) && result.data.items.length > 0) {
                    showActiveOrder(result.data);
                } else {
                    showIdle();
                }
            } catch (error) {
                console.error('Customer Display:', error);
                setConnectionStatus(false);
            } finally {
                requestInProgress = false;
            }
        }

        setInterval(fetchActiveOrder, 1000);
        fetchActiveOrder();
    </script>
</body>
</html>