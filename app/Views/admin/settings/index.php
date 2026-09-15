<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Pengaturan Sistem</h4>
        <p class="text-muted small mb-0">Kelola identitas cafe, persentase pajak, format antrian, dan tampilan struk.</p>
    </div>
</div>

<form action="<?= base_url('/admin/settings/update') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="list-group border-0 shadow-sm rounded-3">
                <a class="list-group-item list-group-item-action active py-3 fw-semibold" id="tab-general-btn" data-bs-toggle="list" href="#tab-general">
                    <i class="bi bi-shop me-2"></i>Identitas Cafe
                </a>
                <a class="list-group-item list-group-item-action py-3 fw-semibold" id="tab-tax-btn" data-bs-toggle="list" href="#tab-tax">
                    <i class="bi bi-percent me-2"></i>Pajak & Layanan
                </a>
                <a class="list-group-item list-group-item-action py-3 fw-semibold" id="tab-queue-btn" data-bs-toggle="list" href="#tab-queue">
                    <i class="bi bi-card-heading me-2"></i>Nomor Antrian
                </a>
                <a class="list-group-item list-group-item-action py-3 fw-semibold" id="tab-receipt-btn" data-bs-toggle="list" href="#tab-receipt">
                    <i class="bi bi-printer me-2"></i>Struk Thermal
                </a>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-3 fw-bold py-2 shadow-sm">
                <i class="bi bi-save me-1"></i> SIMPAN PENGATURAN
            </button>
        </div>

        <div class="col-lg-9">
            <div class="tab-content">
                
                <!-- TAB 1: IDENTITAS CAFE -->
                <div class="tab-pane fade show active" id="tab-general">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="fw-bold mb-0">Informasi & Identitas Cafe</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Cafe</label>
                                <input type="text" name="cafe_name" class="form-control" value="<?= esc($settings['cafe_name'] ?? 'Kopi Senja Utama') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Deskripsi / Slogan</label>
                                <input type="text" name="cafe_description" class="form-control" value="<?= esc($settings['cafe_description'] ?? 'Tempat Nongkrong Kopi Paling Nyaman') ?>">
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nomor Telepon</label>
                                    <input type="text" name="cafe_phone" class="form-control" value="<?= esc($settings['cafe_phone'] ?? '0812-3456-7890') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Contact</label>
                                    <input type="email" name="cafe_email" class="form-control" value="<?= esc($settings['cafe_email'] ?? 'info@kopisenja.com') ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Alamat Lengkap</label>
                                <textarea name="cafe_address" class="form-control" rows="2"><?= esc($settings['cafe_address'] ?? 'Jl. Kopi Senja No. 88, Jakarta') ?></textarea>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Logo Cafe</label>
                                    <input type="file" name="cafe_logo" class="form-control">
                                    <?php if (!empty($settings['cafe_logo'])): ?>
                                        <div class="mt-2"><img src="<?= base_url($settings['cafe_logo']) ?>" height="40" alt="Logo"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Favicon</label>
                                    <input type="file" name="cafe_favicon" class="form-control">
                                    <?php if (!empty($settings['cafe_favicon'])): ?>
                                        <div class="mt-2"><img src="<?= base_url($settings['cafe_favicon']) ?>" height="30" alt="Favicon"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: PAJAK & LAYANAN -->
                <div class="tab-pane fade" id="tab-tax">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="fw-bold mb-0">Persentase Pajak & Service Charge</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Pajak Restoran / PB1 (%)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.1" name="tax_percentage" class="form-control" value="<?= esc($settings['tax_percentage'] ?? '10') ?>" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="form-text">Persentase pajak yang dikenakan pada total belanja.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Service Charge (%)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.1" name="service_charge_percentage" class="form-control" value="<?= esc($settings['service_charge_percentage'] ?? '0') ?>" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="form-text">Biaya layanan operasional cafe (Opsional, isi 0 jika gratis).</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: NOMOR ANTRIAN -->
                <div class="tab-pane fade" id="tab-queue">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="fw-bold mb-0">Format & Penomoran Antrian</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Prefix Antrian</label>
                                    <input type="text" name="queue_prefix" class="form-control" value="<?= esc($settings['queue_prefix'] ?? 'A') ?>" required>
                                    <div class="form-text">Awalan huruf antrian (Contoh: A, B, Q).</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jumlah Digit Nomor</label>
                                    <select name="queue_digit_length" class="form-select">
                                        <option value="3" <?= ($settings['queue_digit_length'] ?? '3') == '3' ? 'selected' : '' ?>>3 Digit (Contoh: A001)</option>
                                        <option value="4" <?= ($settings['queue_digit_length'] ?? '3') == '4' ? 'selected' : '' ?>>4 Digit (Contoh: A0001)</option>
                                    </select>
                                    <div class="form-text">Format panjang digit nomor antrian harian.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: STRUK THERMAL -->
                <div class="tab-pane fade" id="tab-receipt">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="fw-bold mb-0">Header & Footer Struk Thermal 58mm</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Pada Header Struk</label>
                                <input type="text" name="receipt_header_name" class="form-control" value="<?= esc($settings['receipt_header_name'] ?? 'KOPI SENJA UTAMA') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Pesan Footer Struk</label>
                                <textarea name="receipt_footer" class="form-control" rows="3"><?= esc($settings['receipt_footer'] ?? "TERIMA KASIH\nSelamat menikmati pesanan Anda!") ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>