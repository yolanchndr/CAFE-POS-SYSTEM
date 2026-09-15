<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Laporan Penjualan</h4>
        <p class="text-muted small mb-0">Rekapitulasi keuangan dan transaksi cafe berdasarkan rentang tanggal.</p>
    </div>
</div>

<!-- FILTER TANGGAL -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="<?= base_url('/admin/reports') ?>" method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="<?= esc($startDate) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control" value="<?= esc($endDate) ?>" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary fw-bold w-100">
                    <i class="bi bi-filter me-1"></i> TAMPILKAN LAPORAN
                </button>
            </div>
        </form>
    </div>
</div>

<!-- RINGKASAN REKAPITULASI -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-primary text-white p-3">
            <div class="text-white-50 small fw-semibold">TOTAL PENDAPATAN</div>
            <h3 class="fw-bold mb-0">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></h3>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-success text-white p-3">
            <div class="text-white-50 small fw-semibold">TOTAL TRANSAKSI SELESAI</div>
            <h3 class="fw-bold mb-0"><?= number_format($totalOrders) ?> Transaksi</h3>
        </div>
    </div>
</div>

<!-- TABEL TRANSAKSI -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Nomor Invoice</th>
                        <th>Waktu Transaksi</th>
                        <th>Tipe</th>
                        <th>Metode Bayar</th>
                        <th>Total Subtotal</th>
                        <th>Pajak</th>
                        <th>Diskon</th>
                        <th>Grand Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada transaksi selesai pada periode ini.</td></tr>
                    <?php else: foreach ($orders as $i => $ord): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-bold text-primary"><?= esc($ord['order_number']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= strtoupper(esc($ord['order_type'])) ?></span></td>
                            <td><span class="badge bg-secondary"><?= strtoupper(esc($ord['payment_method'] ?? 'CASH')) ?></span></td>
                            <td>Rp <?= number_format($ord['subtotal'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($ord['tax'], 0, ',', '.') ?></td>
                            <td>-Rp <?= number_format($ord['discount'], 0, ',', '.') ?></td>
                            <td class="fw-bold text-success">Rp <?= number_format($ord['grand_total'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>