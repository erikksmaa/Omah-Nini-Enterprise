<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Stok Opname</h5>
                </div>
                <div class="card-body p-3">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <p class="mb-0"><?= $error ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info">
                        <strong>Informasi Produk:</strong><br>
                        SKU: <?= $produk['sku'] ?><br>
                        Nama: <?= $produk['nama_barang'] ?><br>
                        Stok Sistem: <strong class="text-primary"><?= number_format($produk['stok']) ?></strong><br>
                        Minimal Stok: <?= number_format($produk['min_stok']) ?>
                    </div>

                    <form action="<?= base_url('gudang/stok/update-opname/' . $produk['id']) ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label>Stok Fisik <span class="text-danger">*</span></label>
                            <input type="number" 
                                   name="stok_fisik" 
                                   class="form-control form-control-lg"
                                   value="<?= old('stok_fisik', $produk['stok']) ?>"
                                   min="0"
                                   required>
                            <small class="text-muted">Masukkan jumlah stok hasil pengecekan fisik</small>
                        </div>

                        <div class="mb-3">
                            <label>Keterangan</label>
                            <textarea name="keterangan" 
                                      class="form-control" 
                                      rows="3"
                                      placeholder="Contoh: Hasil stock opname tanggal <?= date('d-m-Y') ?>">Stok opname <?= date('d-m-Y') ?></textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> <strong>Perhatian:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Stok opname akan mengubah stok sistem sesuai stok fisik</li>
                                <li>Selisih stok akan tercatat di log histori</li>
                                <li>Pastikan data yang dimasukkan sudah benar</li>
                            </ul>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Simpan Opname
                            </button>
                            <a href="<?= base_url('gudang/stok/detail/' . $produk['id']) ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>