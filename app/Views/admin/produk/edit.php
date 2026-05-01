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
                                    <div class="input-group">
                                        <input type="text" name="sku" id="sku" class="form-control" value="<?= old('sku', $produk['sku']) ?>" required>
                                        <button type="button" id="btnGenerateSku" class="btn btn-secondary">Generate</button>
                                    </div>
                                    <small class="text-muted">Klik Generate untuk membuat SKU otomatis berdasarkan supplier, motif, dan warna</small>
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
    // DOM Elements
    const supplierSelect = document.getElementById('id_supplier');
    const motifSelect = document.getElementById('id_motif');
    const warnaSelect = document.getElementById('id_warna');
    const btnGenerateSku = document.getElementById('btnGenerateSku');
    const skuInput = document.getElementById('sku');
    
    // Current motif ID for preserving selection
    const currentMotifId = '<?= $produk['id_motif'] ?>';
    const currentWarnaId = '<?= $produk['id_warna'] ?>';

    // Load motif based on supplier
    async function loadMotif(supplierId) {
        if (!supplierId) {
            motifSelect.innerHTML = '<option value="">-- Pilih Supplier Terlebih Dahulu --</option>';
            return;
        }
        
        motifSelect.innerHTML = '<option value="">-- Memuat data motif... --</option>';
        
        try {
            const response = await fetch(`<?= base_url('api/motif/by-supplier') ?>/${supplierId}`);
            const result = await response.json();
            
            if (result.status === 'success' && result.data.length > 0) {
                let options = '<option value="">-- Pilih Motif --</option>';
                result.data.forEach(motif => {
                    const selected = (motif.id == currentMotifId) ? 'selected' : '';
                    options += `<option value="${motif.id}" ${selected}>${escapeHtml(motif.nama_motif)}</option>`;
                });
                motifSelect.innerHTML = options;
            } else {
                motifSelect.innerHTML = '<option value="">-- Tidak ada motif untuk supplier ini --</option>';
            }
        } catch (error) {
            console.error('Error loading motif:', error);
            motifSelect.innerHTML = '<option value="">-- Error loading data --</option>';
        }
    }

    // Helper function escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        return text.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // Event listener untuk supplier change
    if (supplierSelect) {
        supplierSelect.addEventListener('change', function() {
            loadMotif(this.value);
        });
        
        // Load motif if supplier already selected
        if (supplierSelect.value) {
            loadMotif(supplierSelect.value);
        }
    }

    // Generate SKU
    if (btnGenerateSku) {
        btnGenerateSku.addEventListener('click', async function() {
            const supplierId = supplierSelect?.value;
            const motifId = motifSelect?.value;
            const warnaId = warnaSelect?.value;
            
            console.log('Generate SKU - Values:', {supplierId, motifId, warnaId});
            
            if (!supplierId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih supplier terlebih dahulu!'
                });
                return;
            }
            
            if (!motifId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih motif terlebih dahulu!'
                });
                return;
            }
            
            if (!warnaId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih warna terlebih dahulu!'
                });
                return;
            }
            
            // Tampilkan loading
            btnGenerateSku.disabled = true;
            btnGenerateSku.innerHTML = 'Generating...';
            
            try {
                // Kirim sebagai Form URL Encoded (lebih reliable)
                const formData = new URLSearchParams();
                formData.append('id_supplier', supplierId);
                formData.append('id_motif', motifId);
                formData.append('id_warna', warnaId);
                
                const response = await fetch('<?= base_url("admin/produk/generateSku") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    skuInput.value = data.sku;
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'SKU berhasil digenerate',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal generate SKU'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat generate SKU'
                });
            } finally {
                btnGenerateSku.disabled = false;
                btnGenerateSku.innerHTML = 'Generate';
            }
        });
    }
</script>

<?= $this->endSection() ?>