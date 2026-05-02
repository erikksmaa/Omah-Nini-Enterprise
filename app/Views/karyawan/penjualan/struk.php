<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Struk Penjualan</h4>
        <div>
            <a href="<?= base_url('karyawan/penjualan') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>
    <div class="card-body" id="struk-print">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>No. Invoice</th><td>: <?= esc($header['no_invoice']) ?></td></tr>
                    <tr><th>Tanggal</th><td>: <?= date('d/m/Y H:i', strtotime($header['tanggal_transaksi'])) ?></td></tr>
                    <tr><th>Pembeli</th><td>: <?= esc($header['nama_pembeli']) ?></td></tr>
                    <?php if ($header['nama_pelanggan']): ?>
                        <tr><th>Pelanggan</th><td>: <?= esc($header['nama_pelanggan']) ?></td></tr>
                    <?php endif; ?>
                    <tr><th>Catatan</th><td>: <?= esc($header['catatan'] ?? '-') ?></td></tr>
                </table>
            </div>
        </div>

        <hr>
        <h5>Item Produk</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>SKU</th>
                        <th>Produk</th>
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
                    <tr><td colspan="6" class="text-center">Tidak ada item</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="text-muted">** Stok akan berkurang sesuai transaksi ini dan tercatat di log stok.</p>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #struk-print, #struk-print * { visibility: visible; }
    #struk-print { position: absolute; left: 0; top: 0; }
    .btn, .card-header { display: none; }
}
</style>
<?= $this->endSection() ?>