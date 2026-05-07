<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4>Detail Barang Masuk</h4>
            <a href="<?= base_url('karyawan/pembelian') ?>" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body p-2 p-md-3">
            <!-- Header Info -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="info-grid">
                        <div class="row g-2">
                            <div class="col-5 col-md-4 fw-bold text-muted">No. Invoice</div>
                            <div class="col-7 col-md-8">: <?= esc($header['no_invoice']) ?></div>
                            
                            <div class="col-5 col-md-4 fw-bold text-muted">Merk</div>
                            <div class="col-7 col-md-8">: <?= esc($header['supplier_nama']) ?></div>
                            
                            <div class="col-5 col-md-4 fw-bold text-muted">Tanggal</div>
                            <div class="col-7 col-md-8">: <?= date('d/m/Y', strtotime($header['tanggal_pembelian'])) ?></div>
                            
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
                                <th>Foto</th>
                                <th>SKU</th>
                                <th>Nama Produk</th>
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
                                                <img src="<?= base_url('uploads/produk/' . $item['foto']) ?>" alt="Foto Produk"
                                                    style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px; cursor: pointer;"
                                                    class="img-thumbnail"
                                                    onclick="showZoom('<?= base_url('uploads/produk/' . $item['foto']) ?>', '<?= esc($item['nama_produk']) ?>')">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                    style="width: 100px; height: 100px;">
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
                                <tr><td colspan="7" class="text-center">Tidak ada item.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========== CARD VIEW (Mobile) ========== -->
            <div class="d-md-none">
                <?php if (!empty($items)): ?>
                    <?php $no = 1; foreach ($items as $item): ?>
                        <div class="card mb-2 shadow-sm">
                            <div class="card-body p-2">
                                <!-- Header Card -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-secondary">#<?= $no++ ?></span>
                                        <code class="small"><?= esc($item['sku']) ?></code>
                                    </div>
                                    <span class="fw-bold text-primary"><?= number_format($item['jumlah']) ?> pcs</span>
                                </div>
                                
                                <!-- Foto Produk -->
                                <div class="text-center mb-2">
                                    <?php if (!empty($item['foto'])): ?>
                                        <img src="<?= base_url('uploads/produk/' . $item['foto']) ?>" alt="Foto Produk"
                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; cursor: pointer;"
                                            class="img-thumbnail"
                                            onclick="showZoom('<?= base_url('uploads/produk/' . $item['foto']) ?>', '<?= esc($item['nama_produk']) ?>')">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto"
                                            style="width: 100px; height: 100px;">
                                            <i class="bi bi-image text-muted fs-2"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Info Produk -->
                                <div class="row g-1 small">
                                    <div class="col-4 text-muted">Nama Produk:</div>
                                    <div class="col-8 fw-semibold"><?= esc($item['nama_produk']) ?></div>
                                    
                                    <div class="col-4 text-muted">Motif:</div>
                                    <div class="col-8"><?= esc($item['nama_motif']) ?></div>
                                    
                                    <div class="col-4 text-muted">Warna:</div>
                                    <div class="col-8"><?= esc($item['nama_warna']) ?></div>
                                    
                                    <div class="col-4 text-muted">Jumlah:</div>
                                    <div class="col-8"><span class="badge bg-primary"><?= number_format($item['jumlah']) ?> pcs</span></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-2 text-muted"></i>
                        <p class="text-muted mt-2 mb-0">Tidak ada item</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
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
    .info-grid .row {
        margin-bottom: 8px;
    }
    @media (max-width: 768px) {
        .info-grid .col-5, .info-grid .col-7 {
            font-size: 13px;
        }
        .card-body {
            padding: 12px;
        }
    }
</style>

<?= $this->endSection() ?>