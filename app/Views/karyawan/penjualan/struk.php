<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
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
        <!-- Header Info -->
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
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Foto</th>
                        <th>SKU</th>
                        <th>Produk</th>
                        <th>Motif</th>
                        <th>Warna</th>
                        <th class="text-center">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($items)): ?>
                    <?php $no = 1; foreach ($items as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <?php if (!empty($item['foto'])): ?>
                                    <img src="<?= base_url('uploads/produk/' . $item['foto']) ?>" 
                                         alt="Foto Produk" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; cursor: pointer;"
                                         class="img-thumbnail"
                                         onclick="showZoom('<?= base_url('uploads/produk/' . $item['foto']) ?>', '<?= esc($item['nama_produk']) ?>')">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-image text-muted fs-4"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><code><?= esc($item['sku']) ?></code></td>
                            <td><?= esc($item['nama_produk']) ?></td>
                            <td><?= esc($item['nama_motif']) ?></td>
                            <td><?= esc($item['nama_warna']) ?></td>
                            <td class="text-center fw-bold"><?= number_format($item['jumlah']) ?> pcs</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada item</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="text-muted mt-3 small">** Stok akan berkurang sesuai transaksi ini dan tercatat di log stok.</p>
    </div>
</div>

<!-- Modal Zoom Foto -->
<div class="modal fade" id="zoomModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">Foto Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="zoomImage" src="" style="max-width: 100%; max-height: 70vh;">
                <p id="zoomCaption" class="mt-2 text-muted"></p>
            </div>
        </div>
    </div>
</div>

<script>
function showZoom(imgSrc, caption) {
    document.getElementById('zoomImage').src = imgSrc;
    document.getElementById('zoomCaption').innerText = caption || 'Foto Produk';
    new bootstrap.Modal(document.getElementById('zoomModal')).show();
}
</script>

<style>
@media print {
    body * { visibility: hidden; }
    #struk-print, #struk-print * { visibility: visible; }
    #struk-print { position: absolute; left: 0; top: 0; width: 100%; }
    .btn, .card-header, .modal, .img-thumbnail, [onclick] { display: none; }
    table { width: 100%; }
    td, th { padding: 5px; }
}
</style>

<?= $this->endSection() ?>