<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <a href="<?= base_url('karyawan/pembelian/create') ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Tambah Barang Masuk
                    </a>
                </div>
                <div class="card-body p-2 p-md-3">
                    <!-- Filter Form -->
                    <div class="card mb-3">
                        <div class="card-body p-2 p-md-3">
                            <form method="get" class="row g-2">
                                <div class="col-md-2 col-6">
                                    <label class="form-label small mb-0">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" class="form-control form-control-sm" value="<?= $tanggalMulai ?? '' ?>">
                                </div>
                                <div class="col-md-2 col-6">
                                    <label class="form-label small mb-0">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" class="form-control form-control-sm" value="<?= $tanggalAkhir ?? '' ?>">
                                </div>
                                <div class="col-md-2 col-6">
                                    <label class="form-label small mb-0">Merek</label>
                                    <select name="supplier" onchange="this.form.submit()" class="form-select form-select-sm">
                                        <option value="">-- Semua --</option>
                                        <?php foreach ($suppliers as $sup): ?>
                                            <option value="<?= $sup['id'] ?>" <?= ($selectedSupplier ?? '') == $sup['id'] ? 'selected' : '' ?>>
                                                <?= esc($sup['nama']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6">
                                    <label class="form-label small mb-0">User</label>
                                    <select name="user" onchange="this.form.submit()" class="form-select form-select-sm">
                                        <option value="">-- Semua --</option>
                                        <?php foreach ($users as $u): ?>
                                            <option value="<?= $u['user_id'] ?>" <?= ($selectedUser ?? '') == $u['user_id'] ? 'selected' : '' ?>>
                                                <?= esc($u['username']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill"><i class="bi bi-funnel"></i> Filter</button>
                                    <a href="<?= base_url('karyawan/pembelian') ?>" class="btn btn-outline-secondary btn-sm flex-fill"><i class="bi bi-arrow-repeat"></i> Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Info periode -->
                    <?php if (!empty($tanggalMulai) && !empty($tanggalAkhir)): ?>
                        <div class="alert alert-info py-2">
                            <i class="bi bi-calendar"></i> Menampilkan data dari 
                            <strong><?= date('d/m/Y', strtotime($tanggalMulai)) ?></strong> s.d 
                            <strong><?= date('d/m/Y', strtotime($tanggalAkhir)) ?></strong>
                        </div>
                    <?php endif; ?>

                    <!-- Tabel Desktop -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>No. Invoice</th>
                                        <th>Merek</th>
                                        <th>Tanggal</th>
                                        <th>User</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($pembelian)): ?>
                                    <?php $no = 1 + (($pager->getCurrentPage()-1) * ($pager->getPerPage() ?? 10)); ?>
                                    <?php foreach ($pembelian as $row): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><code><?= esc($row['no_invoice']) ?></code></td>
                                            <td><?= esc($row['supplier_nama']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['tanggal_pembelian'])) ?></td>
                                            <td><?= esc($row['user_username']) ?></td>
                                            <td class="text-center">
                                                <a href="<?= base_url('karyawan/pembelian/detail/' . $row['id']) ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center py-4">Belum ada data pembelian</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?= $pager ? $pager->links('default', 'bootstrap_pagination') : '' ?>
                    </div>

                    <!-- Card Mobile -->
                    <div class="d-md-none">
                        <?php if (!empty($pembelian)): ?>
                            <?php foreach ($pembelian as $row): ?>
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="badge bg-primary"><?= esc($row['no_invoice']) ?></span>
                                                <small class="text-muted ms-2"><?= date('d/m/Y', strtotime($row['tanggal_pembelian'])) ?></small>
                                            </div>
                                            <a href="<?= base_url('karyawan/pembelian/detail/' . $row['id']) ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                        </div>
                                        <div class="row g-1 small">
                                            <div class="col-4 text-muted">Merek:</div>
                                            <div class="col-8 fw-semibold"><?= esc($row['supplier_nama']) ?></div>
                                            <div class="col-4 text-muted">User:</div>
                                            <div class="col-8"><?= esc($row['user_username']) ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">Belum ada data pembelian</div>
                        <?php endif; ?>
                        <div class="mt-3"><?= $pager ? $pager->links('default', 'bootstrap_pagination') : '' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>