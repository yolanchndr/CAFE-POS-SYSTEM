<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?> - <?= esc(setting('app_name', 'Senja POS Cafe')) ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= base_url(setting('cafe_favicon', 'assets/images/favicon.ico')) ?>" type="image/x-icon">
    
    <!-- Bootstrap 5 CSS & FontAwesome Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 60px;
        }
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #2c3e50;
            color: #ecf0f1;
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
        }
        #sidebar .brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            background-color: #1a252f;
            font-weight: bold;
            font-size: 1.1rem;
        }
        #sidebar .nav-link {
            color: #bdc3c7;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
            border-left: 4px solid transparent;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            color: #ffffff;
            background-color: #34495e;
            border-left-color: #3498db;
        }
        #sidebar .nav-header {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #7f8c8d;
            padding: 1rem 1.5rem 0.4rem;
            font-weight: 700;
        }
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        #topbar {
            height: var(--topbar-height);
            background-color: #ffffff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
        }
        .content-body {
            padding: 1.5rem;
            flex: 1;
        }
        .footer {
            background-color: #ffffff;
            border-top: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            font-size: 0.875rem;
            color: #6c757d;
        }
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.active {
                margin-left: 0;
            }
            #main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<nav id="sidebar">
    <div class="brand text-truncate">
        <i class="bi bi-cup-hot me-2 text-warning"></i>
        <span><?= esc(setting('cafe_name', 'Kopi Senja Utama')) ?></span>
    </div>

    <div class="nav flex-column my-2">
        <a href="<?= base_url('/admin/dashboard') ?>" class="nav-link <?= uri_string() == 'admin/dashboard' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="<?= base_url('/admin/pos') ?>" class="nav-link <?= uri_string() == 'admin/pos' ? 'active' : '' ?>">
            <i class="bi bi-cart-check"></i> POS / Kasir
        </a>

        <div class="nav-header">Master Data</div>
        <a href="<?= base_url('/admin/products') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/products') ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i> Produk / Menu
        </a>
        <a href="<?= base_url('/admin/categories') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/categories') ? 'active' : '' ?>">
            <i class="bi bi-tags"></i> Kategori
        </a>
        <a href="<?= base_url('/admin/variants') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/variants') ? 'active' : '' ?>">
            <i class="bi bi-layers"></i> Varian
        </a>
        <a href="<?= base_url('/admin/toppings') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/toppings') ? 'active' : '' ?>">
            <i class="bi bi-plus-square"></i> Topping
        </a>
        <a href="<?= base_url('/admin/tables') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/tables') ? 'active' : '' ?>">
            <i class="bi bi-grid-3x3-gap"></i> Meja
        </a>

        <div class="nav-header">Transaksi</div>
        <a href="<?= base_url('/admin/orders') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/orders') ? 'active' : '' ?>">
            <i class="bi bi-receipt"></i> Pesanan
        </a>
        <a href="<?= base_url('/admin/payments') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/payments') ? 'active' : '' ?>">
            <i class="bi bi-credit-card"></i> Pembayaran
        </a>

        <div class="nav-header">Laporan & Sistem</div>
        <a href="<?= base_url('/admin/reports') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/reports') ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-bar-graph"></i> Laporan
        </a>
        <a href="<?= base_url('/admin/settings') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/settings') ? 'active' : '' ?>">
            <i class="bi bi-gear"></i> Pengaturan
        </a>
        <a href="<?= base_url('/admin/activity-logs') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/activity-logs') ? 'active' : '' ?>">
            <i class="bi bi-journal-text"></i> Activity Logs
        </a>

        <div class="nav-header">Display Monitor</div>
        <a href="<?= base_url('/customer-display') ?>" target="_blank" class="nav-link">
            <i class="bi bi-display"></i> Customer Display
        </a>
        <a href="<?= base_url('/queue-display') ?>" target="_blank" class="nav-link">
            <i class="bi bi-tv"></i> Queue Display
        </a>
    </div>
</nav>

<!-- MAIN CONTENT WRAPPER -->
<div id="main-content">
    
    <!-- TOPBAR -->
    <header id="topbar">
        <button class="btn btn-sm btn-light d-md-none" id="sidebar-toggle">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="fw-semibold text-secondary">
            <?= esc($title ?? 'Dashboard') ?>
        </div>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle fs-6"></i>
                <span><?= esc(session()->get('name') ?? 'Admin') ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text text-muted small">Role: <?= esc(session()->get('role')) ?></span></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= base_url('/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </div>
    </header>

    <!-- CONTENT BODY -->
    <main class="content-body">
        
        <!-- FLASH MESSAGES -->
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <!-- FOOTER -->
    <footer class="footer d-flex justify-content-between align-items-center">
        <div>
            &copy; <?= date('Y') ?> <strong><?= esc(setting('cafe_name', 'Kopi Senja Utama')) ?></strong>. All rights reserved.
        </div>
        <div class="small">
            <?= esc(setting('app_name', 'Senja POS Cafe')) ?> v1.0
        </div>
    </footer>
</div>

<!-- Bootstrap 5 JS Bundle (PASTIKAN MENGGUNAKAN src BUKAN href) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>