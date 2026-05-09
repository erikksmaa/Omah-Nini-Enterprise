<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Detail Barang Masuk</h4>
        <a href="<?= base_url('karyawan/pembelian') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
    <div class="card-body p-2 p-md-3">
        <!-- Header Info -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="info-grid">
                    <div class="row g-2">
                        <div class="col-5 col-md-4 fw-bold text-muted">No. Invoice</div>
                        <div class="col-7 col-md-8">: <?= esc($header['no_invoice']) ?></div>
                        
                        <div class="col-5 col-md-4 fw-bold text-muted">Merek</div>
                        <div class="col-7 col-md-8">: <?= esc($header['supplier_nama']) ?></div>
                        
                        <div class="col-5 col-md-4 fw-bold text-muted">Tanggal</div>
                        <div class="col-7 col-md-8">: <?= date('d/m/Y', strtotime($header['tanggal_pembelian'])) ?></div>
                        
                        <div class="col-5 col-md-4 fw-bold text-muted">Catatan</div>
                        <div class="col-7 col-md-8">: <?= esc($header['catatan'] ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <h5>Item Produk</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk (Merek - Motif - Warna)</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php $no = 1;
                        foreach ($items as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($item['nama_produk']) ?></td>
                                <td><?= $item['jumlah'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada item.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .info-grid .row {
        margin-bottom: 8px;
    }
    @media (max-width: 768px) {
        .info-grid .col-5, .info-grid .col-7 {
            font-size: 13px;
        }
    }
</style>
<?= $this->endSection() ?>