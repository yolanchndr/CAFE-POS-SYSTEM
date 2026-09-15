<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Manajemen Produk / Menu</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
        <i class="bi bi-plus-circle me-1"></i> Tambah Produk
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Foto</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga Dasar</th>
                        <th>Status</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada produk.</td></tr>
                    <?php else: foreach ($products as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <?php if ($p['image']): ?>
                                    <img src="<?= base_url($p['image']) ?>" alt="Foto" width="50" height="50" class="rounded object-fit-cover">
                                <?php else: ?>
                                    <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                                        <i class="bi bi-cup-hot"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= esc($p['name']) ?></div>
                                <div class="text-muted small"><?= esc(character_limiter($p['description'] ?? '', 40)) ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= esc($p['category_name'] ?? '-') ?></span></td>
                            <td class="fw-bold text-success">Rp <?= number_format($p['base_price'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($p['is_available']): ?>
                                    <span class="badge bg-success">TERSEDIA</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">HABIS</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('/admin/products/toggle-availability/' . $p['id']) ?>" class="btn btn-sm btn-outline-warning me-1" title="Ubah Status Disponibilitas">
                                    <i class="bi bi-toggle-on"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $p['id'] ?>"><i class="bi bi-pencil"></i></button>
                                <a href="<?= base_url('/admin/products/delete/' . $p['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk ini?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit<?= $p['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <form action="<?= base_url('/admin/products/update/' . $p['id']) ?>" method="post" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Produk</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <label class="form-label">Nama Produk</label>
                                                    <input type="text" name="name" class="form-control" value="<?= esc($p['name']) ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Kategori</label>
                                                    <select name="category_id" class="form-select" required>
                                                        <?php foreach ($categories as $cat): ?>
                                                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $p['category_id'] ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Harga Dasar (Rp)</label>
                                                    <input type="number" name="base_price" class="form-control" value="<?= esc($p['base_price']) ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Status Disponibilitas</label>
                                                    <select name="is_available" class="form-select">
                                                        <option value="1" <?= $p['is_available'] == 1 ? 'selected' : '' ?>>TERSEDIA</option>
                                                        <option value="0" <?= $p['is_available'] == 0 ? 'selected' : '' ?>>HABIS</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Ganti Foto Produk (Opsional)</label>
                                                    <input type="file" name="image" class="form-control" accept="image/*">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Topping yang diizinkan untuk produk ini</label>
                                                    <div class="d-flex flex-wrap gap-3">
                                                        <?php foreach ($toppings as $top): ?>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="toppings[]" value="<?= $top['id'] ?>" id="topEdit<?= $p['id'] ?>_<?= $top['id'] ?>"
                                                                    <?= in_array($top['id'], $p['assigned_toppings'] ?? []) ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="topEdit<?= $p['id'] ?>_<?= $top['id'] ?>"><?= esc($top['name']) ?></label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Deskripsi Produk</label>
                                                    <textarea name="description" class="form-control" rows="2"><?= esc($p['description']) ?></textarea>
                                                </div>
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
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('/admin/products/store') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="name" class="form-control" placeholder="misal: Es Kopi Susu" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga Dasar (Rp)</label>
                            <input type="number" name="base_price" class="form-control" placeholder="18000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Disponibilitas</label>
                            <select name="is_available" class="form-select">
                                <option value="1">TERSEDIA</option>
                                <option value="0">HABIS</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Foto Produk</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Topping yang diizinkan untuk produk ini</label>
                            <div class="d-flex flex-wrap gap-3">
                                <?php foreach ($toppings as $top): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="toppings[]" value="<?= $top['id'] ?>" id="topAdd<?= $top['id'] ?>">
                                        <label class="form-check-label" for="topAdd<?= $top['id'] ?>"><?= esc($top['name']) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Penjelasan singkat produk..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Produk</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>