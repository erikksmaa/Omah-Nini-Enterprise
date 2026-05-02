<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Riwayat Barang Masuk</h4>
        <a href="<?= base_url('karyawan/pembelian/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Barang Masuk
        </a>
    </div>
    <div class="card-body">
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
                    <?php $no = 1; foreach ($pembelian as $row): ?>
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
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data pembelian.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?= $pager ? $pager->links('default', 'bootstrap_pagination') : '' ?>
    </div>
</div>
<?= $this->endSection() ?>