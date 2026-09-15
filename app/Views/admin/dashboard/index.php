<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-3 mb-4">
    <!-- STAT 1: PENDAPATAN HARI INI -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="badge bg-primary bg-opacity-10 text-primary p-3 me-3 rounded-3">
                        <i class="bi bi-currency-dollar fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Pendapatan Hari Ini</div>
                        <h4 class="fw-bold text-dark mb-0">Rp <?= number_format($todayRevenue, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STAT 2: PENDAPATAN BULAN INI -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="badge bg-success bg-opacity-10 text-success p-3 me-3 rounded-3">
                        <i class="bi bi-wallet2 fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Pendapatan Bulan Ini</div>
                        <h4 class="fw-bold text-dark mb-0">Rp <?= number_format($monthRevenue, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STAT 3: TRANSAKSI HARIAN -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="badge bg-warning bg-opacity-10 text-warning p-3 me-3 rounded-3">
                        <i class="bi bi-bag-check fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Pesanan Selesai (Hari Ini)</div>
                        <h4 class="fw-bold text-dark mb-0"><?= number_format($todayOrders) ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STAT 4: OKUPANSI MEJA -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="badge bg-info bg-opacity-10 text-info p-3 me-3 rounded-3">
                        <i class="bi bi-grid-3x3-gap fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Status Meja Terisi</div>
                        <h4 class="fw-bold text-dark mb-0"><?= $occupiedTables ?> / <?= $totalTables ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- GRAFIK PENJUALAN 7 HARI TERAKHIR -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Grafik Omset 7 Hari Terakhir</h6>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- WIDGET MENU TERLARIS -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">5 Menu Terlaris (Best Seller)</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($bestSellers)): ?>
                        <li class="list-group-item text-muted text-center py-4">Belum ada data penjualan.</li>
                    <?php else: foreach ($bestSellers as $bs): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="fw-semibold text-dark"><?= esc($bs['product_name']) ?></span>
                            <span class="badge bg-primary rounded-pill"><?= number_format($bs['total_qty']) ?> Terjual</span>
                        </li>
                    <?php endforeach; endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartDates) ?>,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: <?= json_encode($chartData) ?>,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
<?= $this->endSection() ?>