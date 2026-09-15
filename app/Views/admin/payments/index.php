<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Riwayat Pembayaran</h4>
        <p class="text-muted small mb-0">Daftar seluruh transaksi pembayaran tunai dan non-tunai yang telah diproses.</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Waktu Bayar</th>
                        <th>Invoice / Antrian</th>
                        <th>Metode</th>
                        <th>Provider / Ref</th>
                        <th>Tagihan</th>
                        <th>Uang Diterima</th>
                        <th>Kembalian</th>
                        <th width="100" class="text-center">Struk</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada riwayat pembayaran.</td></tr>
                    <?php else: foreach ($payments as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <div class="fw-semibold text-dark"><?= date('d/m/Y H:i', strtotime($p['paid_at'] ?? $p['created_at'])) ?></div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary"><?= esc($p['order_number'] ?? '-') ?></div>
                                <?php if ($p['queue_number']): ?>
                                    <span class="badge bg-light text-dark border">Antrian: <?= esc($p['queue_number']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['payment_method'] === 'cash'): ?>
                                    <span class="badge bg-success"><i class="bi bi-cash me-1"></i>CASH</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark"><i class="bi bi-credit-card me-1"></i>NON-CASH</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= esc($p['payment_provider'] ?? '-') ?></div>
                                <?php if ($p['reference_number']): ?>
                                    <div class="text-xs text-muted">Ref: <?= esc($p['reference_number']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>Rp <?= number_format($p['amount_due'], 0, ',', '.') ?></td>
                            <td class="fw-semibold text-dark">Rp <?= number_format($p['amount_paid'], 0, ',', '.') ?></td>
                            <td class="text-danger">Rp <?= number_format($p['change_amount'], 0, ',', '.') ?></td>
                            <td class="text-center">
                                <a href="<?= base_url('/admin/orders/print/' . $p['order_id']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak Struk">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>