<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Manajemen Kategori</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
        <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Nama Kategori</th>
                        <th>Slug</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada kategori.</td></tr>
                    <?php else: foreach ($categories as $i => $cat): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= esc($cat['name']) ?></td>
                            <td><code><?= esc($cat['slug']) ?></code></td>
                            <td><?= esc($cat['sort_order']) ?></td>
                            <td>
                                <?php if ($cat['is_active']): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $cat['id'] ?>"><i class="bi bi-pencil"></i></button>
                                <a href="<?= base_url('/admin/categories/delete/' . $cat['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kategori ini?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit<?= $cat['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="<?= base_url('/admin/categories/update/' . $cat['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Kategori</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Kategori</label>
                                                <input type="text" name="name" class="form-control" value="<?= esc($cat['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Urutan</label>
                                                <input type="number" name="sort_order" class="form-control" value="<?= esc($cat['sort_order']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="is_active" class="form-select">
                                                    <option value="1" <?= $cat['is_active'] == 1 ? 'selected' : '' ?>>Aktif</option>
                                                    <option value="0" <?= $cat['is_active'] == 0 ? 'selected' : '' ?>>Nonaktif</option>
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
        <form action="<?= base_url('/admin/categories/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" placeholder="misal: Coffee" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>