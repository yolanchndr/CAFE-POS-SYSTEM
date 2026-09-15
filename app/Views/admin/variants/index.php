<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Manajemen Varian Produk</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
        <i class="bi bi-plus-circle me-1"></i> Tambah Varian
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Produk</th>
                        <th>Nama Varian</th>
                        <th>Penyesuaian Harga</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($variants)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada varian produk.</td></tr>
                    <?php else: foreach ($variants as $i => $v): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= esc($v['product_name']) ?></td>
                            <td><?= esc($v['name']) ?></td>
                            <td>
                                <?php if ($v['price_adjustment'] >= 0): ?>
                                    <span class="text-success">+Rp <?= number_format($v['price_adjustment'], 0, ',', '.') ?></span>
                                <?php else: ?>
                                    <span class="text-danger">-Rp <?= number_format(abs($v['price_adjustment']), 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($v['sort_order']) ?></td>
                            <td>
                                <?php if ($v['is_available']): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $v['id'] ?>"><i class="bi bi-pencil"></i></button>
                                <a href="<?= base_url('/admin/variants/delete/' . $v['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus varian ini?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>

                        <!-- Modal Edit Varian -->
                        <div class="modal fade" id="modalEdit<?= $v['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="<?= base_url('/admin/variants/update/' . $v['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Varian Produk</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Produk</label>
                                                <select name="product_id" class="form-select" required>
                                                    <?php foreach ($products as $p): ?>
                                                        <option value="<?= $p['id'] ?>" <?= $p['id'] == $v['product_id'] ? 'selected' : '' ?>><?= esc($p['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Varian</label>
                                                <input type="text" name="name" class="form-control" value="<?= esc($v['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tambahan/Penyesuaian Harga (Rp)</label>
                                                <input type="number" name="price_adjustment" class="form-control" value="<?= esc($v['price_adjustment']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Urutan</label>
                                                <input type="number" name="sort_order" class="form-control" value="<?= esc($v['sort_order']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status Disponibilitas</label>
                                                <select name="is_available" class="form-select">
                                                    <option value="1" <?= $v['is_available'] == 1 ? 'selected' : '' ?>>Aktif</option>
                                                    <option value="0" <?= $v['is_available'] == 0 ? 'selected' : '' ?>>Nonaktif</option>
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
        <form action="<?= base_url('/admin/variants/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Varian Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= esc($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Varian (misal: Large / Hot)</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tambahan/Penyesuaian Harga (Rp)</label>
                        <input type="number" name="price_adjustment" class="form-control" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Disponibilitas</label>
                        <select name="is_available" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Varian</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>