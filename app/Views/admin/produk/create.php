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
                    <form action="<?= base_url('admin/produk/store') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Supplier / Brand <span class="text-danger">*</span></label>
                                    <select name="id_supplier" id="id_supplier" class="form-control" required>
                                        <option value="">-- Pilih Supplier --</option>
                                        <?php foreach ($suppliers as $sup): ?>
                                            <option value="<?= $sup['id'] ?>" <?= old('id_supplier') == $sup['id'] ? 'selected' : '' ?>>
                                                <?= esc($sup['nama']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">SKU</label>
                                    <div class="input-group">
                                        <input type="text" name="sku" id="sku" class="form-control" value="<?= old('sku') ?>">
                                        <button type="button" id="btnGenerateSku" class="btn btn-secondary">Generate</button>
                                    </div>
                                    <small class="text-muted">Kosongkan untuk generate otomatis</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Motif <span class="text-danger">*</span></label>
                                    <select name="id_motif" id="id_motif" class="form-control" required>
                                        <option value="">-- Pilih Supplier Terlebih Dahulu --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Warna <span class="text-danger">*</span></label>
                                    <select name="id_warna" id="id_warna" class="form-control" required>
                                        <option value="">-- Pilih Warna --</option>
                                        <?php foreach ($warnas as $wrn): ?>
                                            <option value="<?= $wrn['id'] ?>" <?= old('id_warna') == $wrn['id'] ? 'selected' : '' ?>>
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
                                    <label class="form-label">Stok Awal</label>
                                    <input type="number" name="stok" class="form-control" value="<?= old('stok') ?? 0 ?>" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Stok Minimal (Peringatan)</label>
                                    <input type="number" name="min_stok" class="form-control" value="<?= old('min_stok') ?? 0 ?>" min="0">
                                    <small class="text-muted">Jika stok <= nilai ini, akan muncul peringatan</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?= old('keterangan') ?></textarea>
                        </div>

                        <div class="mb-3 text-center">
                            <a href="<?= base_url('admin/produk') ?>" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Load motif based on supplier
    document.getElementById('id_supplier').addEventListener('change', function() {
        const supplierId = this.value;
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
                            motifSelect.appendChild(option);
                        });
                    } else {
                        motifSelect.innerHTML = '<option value="">-- Tidak ada motif --</option>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    motifSelect.innerHTML = '<option value="">-- Error loading data --</option>';
                });
        } else {
            motifSelect.innerHTML = '<option value="">-- Pilih Supplier Terlebih Dahulu --</option>';
        }
    });

    // Generate SKU
    document.getElementById('btnGenerateSku').addEventListener('click', function() {
        const supplierId = document.getElementById('id_supplier').value;
        const motifId = document.getElementById('id_motif').value;
        const warnaId = document.getElementById('id_warna').value;
        
        if (!supplierId || !motifId || !warnaId) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih supplier, motif, dan warna terlebih dahulu!'
            });
            return;
        }
        
        fetch('<?= base_url("admin/produk/generateSku") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id_supplier: supplierId,
                id_motif: motifId,
                id_warna: warnaId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('sku').value = data.sku;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Gagal generate SKU'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat generate SKU'
            });
        });
    });
</script>

<?= $this->endSection() ?>