<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4>Struk Penjualan</h4>
        <div>
            <a href="<?= base_url('karyawan/penjualan') ?>" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>
    <div class="card-body p-2 p-md-3" id="struk-print">
        <!-- Header Info -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="info-grid">
                    <div class="row g-2">
                        <div class="col-5 col-md-4 fw-bold text-muted">No. Invoice</div>
                        <div class="col-7 col-md-8">: <?= esc($header['no_invoice']) ?></div>

                        <div class="col-5 col-md-4 fw-bold text-muted">Tanggal</div>
                        <div class="col-7 col-md-8">: <?= date('d/m/Y H:i', strtotime($header['tanggal_transaksi'])) ?>
                        </div>

                        <div class="col-5 col-md-4 fw-bold text-muted">Pembeli</div>
                        <div class="col-7 col-md-8">: <?= esc($header['nama_pembeli']) ?></div>

                        <?php if ($header['nama_pelanggan']): ?>
                            <div class="col-5 col-md-4 fw-bold text-muted">Pelanggan</div>
                            <div class="col-7 col-md-8">: <?= esc($header['nama_pelanggan']) ?></div>
                        <?php endif; ?>

                        <div class="col-5 col-md-4 fw-bold text-muted">Catatan</div>
                        <div class="col-7 col-md-8">: <?= esc($header['catatan'] ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <hr>
        <h5>Item Produk</h5>

        <!-- ========== TABEL (Desktop) ========== -->
        <div class="d-none d-md-block">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Foto</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php $no = 1;
                            foreach ($items as $item): ?>
                                <?php $subtotal = $item['harga_satuan'] * $item['jumlah']; ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($item['foto'])): ?>
                                            <img src="<?= base_url('uploads/produk/' . $item['foto']) ?>" alt="Foto Produk"
                                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; cursor: pointer;"
                                                class="img-thumbnail"
                                                onclick="showZoom('<?= base_url('uploads/produk/' . $item['foto']) ?>', '<?= addslashes($item['nama_produk']) ?>')">

                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px; margin: 0 auto;">
                                                <i class="bi bi-image text-muted fs-4"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($item['nama_produk']) ?></td>
                                    <td class="text-center"><?= $item['jumlah'] ?></td>
                                    <td class="text-end">Rp.<?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                                    <td class="text-end">Rp.<?= number_format($subtotal, 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-active fw-bold">
                                <td colspan="5" class="text-end">TOTAL</td>
                                <td class="text-end">Rp.<?= number_format($total, 0, ',', '.') ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada item</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========== CARD VIEW (Mobile) ========== -->
        <div class="d-md-none">
            <?php if (!empty($items)): ?>
                <?php $no = 1;
                foreach ($items as $item): ?>
                    <?php $subtotal = $item['harga_satuan'] * $item['jumlah']; ?>
                    <div class="card mb-2 shadow-sm">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary">#<?= $no++ ?></span>
                                    <strong><?= esc($item['nama_produk']) ?></strong>
                                </div>
                                <span class="fw-bold text-primary"><?= number_format($subtotal, 0, ',', '.') ?></span>
                            </div>

                            <?php if (!empty($item['foto'])): ?>
                                <div class="text-center mb-2">
                                    <img src="<?= base_url('uploads/produk/' . $item['foto']) ?>" alt="Foto Produk"
                                        style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; cursor: pointer;"
                                        class="img-thumbnail"
                                        onclick="showZoom('<?= base_url('uploads/produk/' . $item['foto']) ?>', '<?= addslashes($item['nama_produk']) ?>')">
                                </div>
                            <?php endif; ?>

                            <div class="row g-1 small">
                                <div class="col-6 text-muted">Jumlah:</div>
                                <div class="col-6"><?= $item['jumlah'] ?> pcs</div>

                                <div class="col-6 text-muted">Harga Satuan:</div>
                                <div class="col-6">Rp.<?= number_format($item['harga_satuan'], 0, ',', '.') ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="alert alert-primary text-center fw-bold">
                    TOTAL: Rp.<?= number_format($total, 0, ',', '.') ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-inbox fs-2 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Tidak ada item</p>
                </div>
            <?php endif; ?>
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
        // Pastikan document.getElementById mengembalikan element yang valid
        var zoomImg = document.getElementById('zoomImage');
        var zoomCaption = document.getElementById('zoomCaption');
        var zoomModal = document.getElementById('zoomModal');

        if (zoomImg) zoomImg.src = imgSrc;
        if (zoomCaption) zoomCaption.innerText = caption || 'Foto Produk';
        if (zoomModal) {
            var modal = new bootstrap.Modal(zoomModal);
            modal.show();
        }
    }
</script>

<style>
    .info-grid .row {
        margin-bottom: 8px;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        #struk-print,
        #struk-print * {
            visibility: visible;
        }

        #struk-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .btn,
        .card-header,
        .modal {
            display: none;
        }

        table {
            width: 100%;
        }

        td,
        th {
            padding: 5px;
        }

        img {
            max-width: 30px !important;
            max-height: 30px !important;
        }
    }

    @media (max-width: 768px) {

        .info-grid .col-5,
        .info-grid .col-7 {
            font-size: 13px;
        }
    }
</style>
<?= $this->endSection() ?>