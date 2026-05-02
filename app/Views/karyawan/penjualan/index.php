<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Riwayat Penjualan</h4>
        <a href="<?= base_url('karyawan/penjualan/create') ?>" class="btn btn-primary">
            <i class="bi bi-cart-plus"></i> Barang Keluar (POS)
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Pembeli</th>
                        <th>Tanggal</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($transaksi)): ?>
                    <?php $no = 1; foreach ($transaksi as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($row['no_invoice']) ?></td>
                            <td><?= esc($row['nama_pembeli']) ?> <?= $row['nama_pelanggan'] ? '(' . esc($row['nama_pelanggan']) . ')' : '' ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_transaksi'])) ?></td>
                            <td><?= esc($row['catatan'] ?? '-') ?></td>
                            <td>
                                <a href="<?= base_url('karyawan/penjualan/struk/' . $row['id']) ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-receipt"></i> Struk
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data penjualan.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= $pager ? $pager->links('default', 'bootstrap_pagination') : '' ?>
    </div>
</div>
<?= $this->endSection() ?>