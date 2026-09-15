<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Manajemen Meja Cafe</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
        <i class="bi bi-plus-circle me-1"></i> Tambah Meja
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Nomor Meja</th>
                        <th>Nama Meja</th>
                        <th>Kapasitas</th>
                        <th>Status Meja</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tables)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada meja.</td></tr>
                    <?php else: foreach ($tables as $i => $tbl): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><span class="badge bg-dark"><?= esc($tbl['table_number']) ?></span></td>
                            <td class="fw-semibold"><?= esc($tbl['name']) ?></td>
                            <td><?= esc($tbl['capacity']) ?> Orang</td>
                            <td>
                                <?php
                                $badge = match ($tbl['status']) {
                                    'available' => 'bg-success',
                                    'occupied'  => 'bg-danger',
                                    'reserved'  => 'bg-warning text-dark',
                                    default     => 'bg-secondary',
                                };
                                ?>
                                <span class="badge <?= $badge ?>"><?= strtoupper(esc($tbl['status'])) ?></span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $tbl['id'] ?>"><i class="bi bi-pencil"></i></button>
                                <a href="<?= base_url('/admin/tables/delete/' . $tbl['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus meja ini?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>

                        <!-- Modal Edit Meja -->
                        <div class="modal fade" id="modalEdit<?= $tbl['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="<?= base_url('/admin/tables/update/' . $tbl['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Meja</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nomor/Kode Meja</label>
                                                <input type="text" name="table_number" class="form-control" value="<?= esc($tbl['table_number']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Meja</label>
                                                <input type="text" name="name" class="form-control" value="<?= esc($tbl['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Kapasitas (Orang)</label>
                                                <input type="number" name="capacity" class="form-control" value="<?= esc($tbl['capacity']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status Meja</label>
                                                <select name="status" class="form-select">
                                                    <option value="available" <?= $tbl['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                                                    <option value="occupied" <?= $tbl['status'] == 'occupied' ? 'selected' : '' ?>>Occupied</option>
                                                    <option value="reserved" <?= $tbl['status'] == 'reserved' ? 'selected' : '' ?>>Reserved</option>
                                                    <option value="inactive" <?= $tbl['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
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
        <form action="<?= base_url('/admin/tables/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Meja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nomor/Kode Meja</label>
                        <input type="text" name="table_number" class="form-control" placeholder="misal: M01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Meja</label>
                        <input type="text" name="name" class="form-control" placeholder="misal: Meja Utama 01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas (Orang)</label>
                        <input type="number" name="capacity" class="form-control" value="2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Meja</label>
                        <select name="status" class="form-select">
                            <option value="available">Available</option>
                            <option value="occupied">Occupied</option>
                            <option value="reserved">Reserved</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Meja</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>