<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Detail Barang Masuk</h4>
        <a href="<?= base_url('karyawan/pembelian') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th>No. Invoice</th>
                        <td>: <?= esc($header['no_invoice']) ?></td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td>: <?= esc($header['supplier_nama']) ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>: <?= date('d/m/Y', strtotime($header['tanggal_pembelian'])) ?></td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>: <?= esc($header['catatan'] ?? '-') ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <h5>Item Produk</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Motif</th>
                        <th>Warna</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($items)): ?>
                    <?php $no = 1; foreach ($items as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($item['sku']) ?></td>
                            <td><?= esc($item['nama_produk']) ?></td>
                            <td><?= esc($item['nama_motif']) ?></td>
                            <td><?= esc($item['nama_warna']) ?></td>
                            <td><?= $item['jumlah'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada item.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>