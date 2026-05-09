<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Struk Penjualan</h4>
        <div>
            <a href="<?= base_url('karyawan/penjualan') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Cetak</button>
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
                        <div class="col-7 col-md-8">: <?= date('d/m/Y H:i', strtotime($header['tanggal_transaksi'])) ?></div>

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
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($items)): ?>
                    <?php $no = 1; foreach ($items as $item): ?>
                        <?php $subtotal = $item['harga_satuan'] * $item['jumlah']; ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($item['nama_produk']) ?></td>
                            <td><?= $item['jumlah'] ?></td>
                            <td class="text-end"><?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                            <td class="text-end"><?= number_format($subtotal, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-active fw-bold">
                        <td colspan="4" class="text-end">TOTAL</td>
                        <td class="text-end"><?= number_format($total, 0, ',', '.') ?></td>
                    </tr>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">Tidak ada item</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <p class="text-muted mt-3 small">** Stok akan berkurang sesuai transaksi ini dan tercatat di log stok.</p>
    </div>
</div>

<style>
    .info-grid .row {
        margin-bottom: 8px;
    }
    @media print {
        body * { visibility: hidden; }
        #struk-print, #struk-print * { visibility: visible; }
        #struk-print { position: absolute; left: 0; top: 0; width: 100%; }
        .btn, .card-header { display: none; }
        table { width: 100%; }
        td, th { padding: 5px; }
    }
    @media (max-width: 768px) {
        .info-grid .col-5, .info-grid .col-7 {
            font-size: 13px;
        }
    }
</style>
<?= $this->endSection() ?>