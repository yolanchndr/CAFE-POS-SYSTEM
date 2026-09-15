<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0">Manajemen Pesanan</h4>
        <p class="text-muted small mb-0">Pantau dan kelola proses pesanan pelanggan di cafe.</p>
    </div>
    <div class="btn-group btn-group-sm">
        <a href="<?= base_url('/admin/orders') ?>" class="btn <?= $activeFilter == 'all' ? 'btn-primary' : 'btn-outline-secondary' ?>">Semua</a>
        <a href="<?= base_url('/admin/orders?status=pending') ?>" class="btn <?= $activeFilter == 'pending' ? 'btn-primary' : 'btn-outline-secondary' ?>">Pending</a>
        <a href="<?= base_url('/admin/orders?status=diproses') ?>" class="btn <?= $activeFilter == 'diproses' ? 'btn-primary' : 'btn-outline-secondary' ?>">Diproses</a>
        <a href="<?= base_url('/admin/orders?status=siap') ?>" class="btn <?= $activeFilter == 'siap' ? 'btn-primary' : 'btn-outline-secondary' ?>">Siap</a>
        <a href="<?= base_url('/admin/orders?status=selesai') ?>" class="btn <?= $activeFilter == 'selesai' ? 'btn-primary' : 'btn-outline-secondary' ?>">Selesai</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Antrian / Invoice</th>
                        <th>Tipe / Meja</th>
                        <th>Rincian Pesanan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th width="160" class="text-center">Aksi Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada pesanan ditemukan.</td></tr>
                    <?php else: foreach ($orders as $i => $ord): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <div class="fw-bold fs-6 text-primary"><?= esc($ord['queue_number'] ?? '-') ?></div>
                                <div class="text-muted text-xs"><?= esc($ord['order_number']) ?></div>
                                <div class="text-muted text-xs"><?= date('H:i, d M Y', strtotime($ord['created_at'])) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border mb-1"><?= strtoupper(esc($ord['order_type'])) ?></span>
                                <?php if ($ord['table_number']): ?>
                                    <div class="small fw-semibold text-secondary"><i class="bi bi-grid-3x3-gap me-1"></i><?= esc($ord['table_number']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <ul class="list-unstyled mb-0 small">
                                    <?php foreach ($ord['items'] as $item): ?>
                                        <li class="mb-1">
                                            <strong><?= $item['quantity'] ?>x</strong> <?= esc($item['product_name']) ?>
                                            <?= $item['variant_name'] ? '<span class="text-muted">('.esc($item['variant_name']).')</span>' : '' ?>
                                            <?php if (!empty($item['toppings'])): ?>
                                                <div class="text-muted text-xs ms-3">
                                                    + <?= implode(', ', array_column($item['toppings'], 'topping_name')) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($item['notes']): ?>
                                                <div class="fst-italic text-secondary text-xs ms-3"><i class="bi bi-pencil-square"></i> <?= esc($item['notes']) ?></div>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">Rp <?= number_format($ord['grand_total'], 0, ',', '.') ?></div>
                                <div class="text-xs text-muted"><?= strtoupper(esc($ord['payment_method'] ?? 'UNPAID')) ?> <?= $ord['payment_provider'] ? '('.esc($ord['payment_provider']).')' : '' ?></div>
                            </td>
                            <td>
                                <?php
                                $statusBadge = match ($ord['status']) {
                                    'pending'   => 'bg-secondary',
                                    'diproses'  => 'bg-warning text-dark',
                                    'siap'      => 'bg-info text-dark',
                                    'selesai'   => 'bg-success',
                                    'dibatalkan'=> 'bg-danger',
                                    default     => 'bg-light text-dark',
                                };
                                ?>
                                <span class="badge <?= $statusBadge ?> fs-7"><?= strtoupper(esc($ord['status'])) ?></span>
                            </td>
                            <td class="text-center">
                                <form action="<?= base_url('/admin/orders/update-status/' . $ord['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="input-group input-group-sm">
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="pending" <?= $ord['status'] == 'pending' ? 'selected' : '' ?>>PENDING</option>
                                            <option value="diproses" <?= $ord['status'] == 'diproses' ? 'selected' : '' ?>>DIPROSES</option>
                                            <option value="siap" <?= $ord['status'] == 'siap' ? 'selected' : '' ?>>SIAP</option>
                                            <option value="selesai" <?= $ord['status'] == 'selesai' ? 'selected' : '' ?>>SELESAI</option>
                                            <option value="dibatalkan" <?= $ord['status'] == 'dibatalkan' ? 'selected' : '' ?>>BATAL</option>
                                        </select>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>