<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Manajemen Topping</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
        <i class="bi bi-plus-circle me-1"></i> Tambah Topping
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Nama Topping</th>
                        <th>Harga Topping</th>
                        <th>Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($toppings)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada topping.</td></tr>
                    <?php else: foreach ($toppings as $i => $t): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= esc($t['name']) ?></td>
                            <td class="text-success fw-bold">Rp <?= number_format($t['price'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($t['is_available']): ?>
                                    <span class="badge bg-success">TERSEDIA</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">HABIS</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $t['id'] ?>"><i class="bi bi-pencil"></i></button>
                                <a href="<?= base_url('/admin/toppings/delete/' . $t['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus topping ini?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>

                        <!-- Modal Edit Topping -->
                        <div class="modal fade" id="modalEdit<?= $t['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="<?= base_url('/admin/toppings/update/' . $t['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Topping</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Topping</label>
                                                <input type="text" name="name" class="form-control" value="<?= esc($t['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Harga (Rp)</label>
                                                <input type="number" name="price" class="form-control" value="<?= esc($t['price']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status Disponibilitas</label>
                                                <select name="is_available" class="form-select">
                                                    <option value="1" <?= $t['is_available'] == 1 ? 'selected' : '' ?>>TERSEDIA</option>
                                                    <option value="0" <?= $t['is_available'] == 0 ? 'selected' : '' ?>>HABIS</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('/admin/toppings/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Topping</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Topping</label>
                        <input type="text" name="name" class="form-control" placeholder="misal: Extra Shot" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="price" class="form-control" placeholder="4000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Disponibilitas</label>
                        <select name="is_available" class="form-select">
                            <option value="1">TERSEDIA</option>
                            <option value="0">HABIS</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Topping</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>