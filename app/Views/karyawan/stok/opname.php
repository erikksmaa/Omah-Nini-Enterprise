<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h4>Opname Stok</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('karyawan/stok/update-opname/' . $produk['id']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">SKU</th>
                            <td>: <?= esc($produk['sku']) ?></td>
                        </tr>
                        <tr>
                            <th>Produk</th>
                            <td>: <?= esc($produk['nama_motif'] . ' ' . $produk['nama_warna']) ?></td>
                        </tr>
                        <tr>
                            <th>Stok Saat Ini</th>
                            <td>: <strong><?= $produk['stok'] ?></strong> potong</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Stok Baru <span class="text-danger">*</span></label>
                    <input type="number" name="stok_baru" class="form-control" min="0" value="<?= old('stok_baru', $produk['stok']) ?>" required>
                    <small class="text-muted">Masukkan jumlah stok hasil opname terbaru.</small>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Penyesuaian
                </button>
                <a href="<?= base_url('karyawan/stok/detail/' . $produk['id']) ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>