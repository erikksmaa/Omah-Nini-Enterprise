<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $title ?></h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/produk/update/' . $produk['id']) ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Supplier / Brand <span class="text-danger">*</span></label>
                                    <select name="id_supplier" id="id_supplier" class="form-control" required>
                                        <option value="">-- Pilih Supplier --</option>
                                        <?php foreach ($suppliers as $sup): ?>
                                            <option value="<?= $sup['id'] ?>" <?= $produk['id_supplier'] == $sup['id'] ? 'selected' : '' ?>>
                                                <?= esc($sup['nama']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">SKU <span class="text-danger">*</span></label>
                                    <input type="text" name="sku" class="form-control" value="<?= old('sku', $produk['sku']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Motif <span class="text-danger">*</span></label>
                                    <select name="id_motif" id="id_motif" class="form-control" required>
                                        <option value="">-- Pilih Motif --</option>
                                        <?php foreach ($motifs as $mot): ?>
                                            <option value="<?= $mot['id'] ?>" <?= $produk['id_motif'] == $mot['id'] ? 'selected' : '' ?>>
                                                <?= esc($mot['nama_motif']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Warna <span class="text-danger">*</span></label>
                                    <select name="id_warna" id="id_warna" class="form-control" required>
                                        <option value="">-- Pilih Warna --</option>
                                        <?php foreach ($warnas as $wrn): ?>
                                            <option value="<?= $wrn['id'] ?>" <?= $produk['id_warna'] == $wrn['id'] ? 'selected' : '' ?>>
                                                <?= esc($wrn['nama_warna']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Stok</label>
                                    <input type="number" name="stok" class="form-control" value="<?= old('stok', $produk['stok']) ?>" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Stok Minimal (Peringatan)</label>
                                    <input type="number" name="min_stok" class="form-control" value="<?= old('min_stok', $produk['min_stok']) ?>" min="0">
                                    <small class="text-muted">Jika stok <= nilai ini, akan muncul peringatan</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?= old('keterangan', $produk['keterangan']) ?></textarea>
                        </div>

                        <div class="mb-3 text-center">
                            <a href="<?= base_url('admin/produk') ?>" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Load motif based on supplier (for edit form, filter motifs)
    document.getElementById('id_supplier').addEventListener('change', function() {
        const supplierId = this.value;
        const currentMotifId = '<?= $produk['id_motif'] ?>';
        const motifSelect = document.getElementById('id_motif');
        
        if (supplierId) {
            fetch('<?= base_url("api/motif/by-supplier") ?>/' + supplierId)
                .then(response => response.json())
                .then(data => {
                    motifSelect.innerHTML = '<option value="">-- Pilih Motif --</option>';
                    if (data.status === 'success' && data.data.length > 0) {
                        data.data.forEach(motif => {
                            const option = document.createElement('option');
                            option.value = motif.id;
                            option.textContent = motif.nama_motif;
                            if (motif.id == currentMotifId) {
                                option.selected = true;
                            }
                            motifSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>

<?= $this->endSection() ?>