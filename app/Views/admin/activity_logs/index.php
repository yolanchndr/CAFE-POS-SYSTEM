<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Log Aktivitas Sistem</h4>
        <p class="text-muted small mb-0">Catatan riwayat aksi pengguna dan perubahan data dalam sistem.</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th>Aksi / Event</th>
                        <th>Deskripsi</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada catatan aktivitas sistem.</td></tr>
                    <?php else: foreach ($logs as $i => $log): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><span class="text-muted small"><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></span></td>
                            <td class="fw-semibold text-dark"><?= esc($log['user_name'] ?? 'Sistem / Anonim') ?></td>
                            <td><span class="badge bg-primary"><?= esc($log['action'] ?? 'ACTIVITY') ?></span></td>
                            <td><?= esc($log['description'] ?? '-') ?></td>
                            <td><code class="text-xs"><?= esc($log['ip_address'] ?? '127.0.0.1') ?></code></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>