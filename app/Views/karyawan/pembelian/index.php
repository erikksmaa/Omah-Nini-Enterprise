<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4><?= $title ?></h4>
        <a href="<?= base_url('karyawan/pembelian/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Barang Masuk
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="get" class="row mb-4">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="<?= $tanggalMulai ?? '' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" class="form-control" value="<?= $tanggalAkhir ?? '' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Supplier</label>
                <select name="supplier" class="form-select">
                    <option value="">-- Semua --</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['id'] ?>" <?= ($selectedSupplier ?? '') == $sup['id'] ? 'selected' : '' ?>>
                            <?= esc($sup['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-funnel"></i> Filter</button>
                <a href="<?= base_url('karyawan/pembelian') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <!-- Tabel -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Supplier</th>
                        <th>Tanggal</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($pembelian)): ?>
                    <?php $no = 1 + (($pager->getCurrentPage() - 1) * $pager->getPerPage()); ?>
                    <?php foreach ($pembelian as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($row['no_invoice']) ?></td>
                            <td><?= esc($row['supplier_nama']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_pembelian'])) ?></td>
                            <td><?= esc($row['catatan'] ?? '-') ?></td>
                            <td>
                                <a href="<?= base_url('karyawan/pembelian/detail/' . $row['id']) ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">Belum ada data.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= $pager ? $pager->links('default', 'bootstrap_pagination') : '' ?>
    </div>
</div>
<?= $this->endSection() ?>