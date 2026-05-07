<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-3">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                    <a href="<?= base_url('admin/produk/create') ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Tambah Produk
                    </a>
                </div>
                <div class="card-body">
                    <!-- ========== FILTER CARD MODERN ========== -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-funnel fs-5 me-2 text-primary"></i>
                                <h6 class="font-weight-bold mb-0 text-primary">Filter Data Produk</h6>
                                <span class="ms-2 text-muted small">| Temukan produk dengan cepat</span>
                            </div>

                            <form method="GET" id="filterForm">
                                <div class="row g-3">
                                    <!-- Baris 1: Cari -->
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="bi bi-search text-muted"></i>
                                            </span>
                                            <input type="text" name="keyword" class="form-control border-start-0"
                                                placeholder="Cari produk berdasarkan SKU, motif, warna, atau merk..."
                                                value="<?= $keyword ?? '' ?>" style="border-left: none;">
                                            <button class="btn btn-primary px-4" type="submit">
                                                <i class="bi bi-search me-1"></i> Cari
                                            </button>
                                            <a href="<?= base_url('admin/produk') ?>"
                                                class="btn btn-outline-secondary px-4">
                                                <i class="bi bi-arrow-repeat me-1"></i> Reset
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Baris 2: Dropdown Filter -->
                                    <div class="col-md-12">
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <select name="filter_supplier" id="filter_supplier"
                                                        class="form-select">
                                                        <option value="">-- Semua Merk --</option>
                                                        <?php foreach ($suppliers as $sup): ?>
                                                            <option value="<?= $sup['id'] ?>" <?= ($filter_supplier ?? '') == $sup['id'] ? 'selected' : '' ?>>
                                                                <?= esc($sup['nama']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label><i class="bi bi-building me-1"></i> Merk / Brand</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <select name="filter_motif" id="filter_motif" class="form-select">
                                                        <option value="">-- Semua Motif --</option>
                                                        <?php if (!empty($filter_supplier) && !empty($motif_by_supplier)): ?>
                                                            <?php foreach ($motif_by_supplier as $mot): ?>
                                                                <option value="<?= $mot['id'] ?>" <?= ($filter_motif ?? '') == $mot['id'] ? 'selected' : '' ?>>
                                                                    <?= esc($mot['nama_motif']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <?php foreach ($motifs as $mot): ?>
                                                                <option value="<?= $mot['id'] ?>" <?= ($filter_motif ?? '') == $mot['id'] ? 'selected' : '' ?>>
                                                                    <?= esc($mot['nama_motif']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                    <label><i class="bi bi-brush me-1"></i> Motif</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <select name="filter_warna" class="form-select">
                                                        <option value="">-- Semua Warna --</option>
                                                        <?php foreach ($warnas as $wrn): ?>
                                                            <option value="<?= $wrn['id'] ?>" <?= ($filter_warna ?? '') == $wrn['id'] ? 'selected' : '' ?>>
                                                                <?= esc($wrn['nama_warna']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label><i class="bi bi-palette me-1"></i> Warna</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <select name="filter_stok" class="form-select">
                                                        <option value="">-- Semua Stok --</option>
                                                        <option value="aman" <?= ($filter_stok ?? '') == 'aman' ? 'selected' : '' ?>>✅ Aman (Stok > Min)</option>
                                                        <option value="menipis" <?= ($filter_stok ?? '') == 'menipis' ? 'selected' : '' ?>>⚠️ Menipis (Stok ≤ Min, > 0)</option>
                                                        <option value="habis" <?= ($filter_stok ?? '') == 'habis' ? 'selected' : '' ?>>❌ Habis (Stok = 0)</option>
                                                    </select>
                                                    <label><i class="bi bi-box-seam me-1"></i> Status Stok</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ========== TABEL (Desktop) ========== -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">SKU</th>
                                        <th width="15%">Foto</th>
                                        <th>Merk / Brand</th>
                                        <th>Motif</th>
                                        <th>Warna</th>
                                        <th>Stok</th>
                                        <th>Min Stok</th>
                                        <th>Status</th>
                                        <th width="12%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($produk)): ?>
                                        <?php $no = 1 + (($pager->getCurrentPage() - 1) * 10); ?>
                                        <?php foreach ($produk as $row): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td class="text-center">
                                                    <?php if (!empty($row['foto'])): ?>
                                                        <img src="<?= base_url('uploads/produk/' . $row['foto']) ?>"
                                                            alt="Foto Produk"
                                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px; cursor: pointer;"
                                                            class="img-thumbnail"
                                                            onclick="showZoom('<?= base_url('uploads/produk/' . $row['foto']) ?>', '<?= esc($row['nama_motif']) ?> - <?= esc($row['nama_warna']) ?>')">
                                                    <?php else: ?>
                                                        <i class="bi bi-image text-muted fs-4"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td><code><?= esc($row['sku']) ?></code></td>
                                                <td><?= esc($row['nama_supplier']) ?></td>
                                                <td><?= esc($row['nama_motif']) ?></td>
                                                <td><?= esc($row['nama_warna']) ?></td>
                                                <td>
                                                    <?php if ($row['stok'] <= $row['min_stok'] && $row['stok'] > 0): ?>
                                                        <span
                                                            class="badge bg-warning text-dark"><?= number_format($row['stok']) ?></span>
                                                    <?php elseif ($row['stok'] == 0): ?>
                                                        <span class="badge bg-danger"><?= number_format($row['stok']) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success"><?= number_format($row['stok']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= number_format($row['min_stok']) ?></td>
                                                <td>
                                                    <?php if ($row['stok'] == 0): ?>
                                                        <span class="badge bg-danger">Habis</span>
                                                    <?php elseif ($row['stok'] <= $row['min_stok']): ?>
                                                        <span class="badge bg-warning text-dark">Menipis</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Aman</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= base_url('admin/produk/edit/' . $row['id']) ?>"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $row['id'] ?>"
                                                        data-sku="<?= esc($row['sku']) ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Tidak ada data produk</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========== CARD VIEW (Mobile) ========== -->
                    <div class="d-md-none">
                        <?php if (!empty($produk)): ?>
                            <?php $no = 1 + (($pager->getCurrentPage() - 1) * 10); ?>
                            <?php foreach ($produk as $row): ?>
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-body p-2">
                                        <!-- Header Card -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-secondary">#<?= $no++ ?></span>
                                                <code class="small"><?= esc($row['sku']) ?></code>
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('admin/produk/edit/' . $row['id']) ?>"
                                                    class="btn btn-outline-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="btn btn-outline-danger btn-delete" data-id="<?= $row['id'] ?>"
                                                    data-sku="<?= esc($row['sku']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Foto Produk (Tampil di Mobile) -->
                                        <div class="text-center mb-2">
                                            <?php if (!empty($row['foto'])): ?>
                                                <img src="<?= base_url('uploads/produk/' . $row['foto']) ?>" alt="Foto Produk"
                                                    style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; cursor: pointer;"
                                                    class="img-thumbnail"
                                                    onclick="showZoom('<?= base_url('uploads/produk/' . $row['foto']) ?>', '<?= esc($row['nama_motif']) ?> - <?= esc($row['nama_warna']) ?>')">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto"
                                                    style="width: 100px; height: 100px;">
                                                    <i class="bi bi-image text-muted fs-2"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Info Produk -->
                                        <div class="row g-1 small">
                                            <div class="col-4 text-muted">Merk:</div>
                                            <div class="col-8 fw-semibold"><?= esc($row['nama_supplier']) ?></div>

                                            <div class="col-4 text-muted">Motif:</div>
                                            <div class="col-8"><?= esc($row['nama_motif']) ?></div>

                                            <div class="col-4 text-muted">Warna:</div>
                                            <div class="col-8"><?= esc($row['nama_warna']) ?></div>

                                            <div class="col-4 text-muted">Stok:</div>
                                            <div class="col-8">
                                                <?php if ($row['stok'] <= $row['min_stok'] && $row['stok'] > 0): ?>
                                                    <span
                                                        class="badge bg-warning text-dark"><?= number_format($row['stok']) ?></span>
                                                <?php elseif ($row['stok'] == 0): ?>
                                                    <span class="badge bg-danger"><?= number_format($row['stok']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-success"><?= number_format($row['stok']) ?></span>
                                                <?php endif; ?>
                                                <span class="text-muted"> / Min: <?= number_format($row['min_stok']) ?></span>
                                            </div>

                                            <div class="col-4 text-muted">Status:</div>
                                            <div class="col-8">
                                                <?php if ($row['stok'] == 0): ?>
                                                    <span class="badge bg-danger">Habis</span>
                                                <?php elseif ($row['stok'] <= $row['min_stok']): ?>
                                                    <span class="badge bg-warning text-dark">Menipis</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Aman</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-2 text-muted"></i>
                                <p class="text-muted mt-2">Tidak ada data produk</p>
                            </div>
                        <?php endif; ?>

                        <!-- Pagination -->
                        <div class="mt-3">
                            <?= $pager->links('default', 'bootstrap_pagination') ?>
                        </div>
                    </div>
                </div>
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

<script>
    // Dynamic motif loading based on supplier filter
    const filterSupplier = document.getElementById('filter_supplier');
    const filterMotif = document.getElementById('filter_motif');

    if (filterSupplier) {
        filterSupplier.addEventListener('change', function () {
            const supplierId = this.value;

            if (supplierId) {
                fetch(`<?= base_url('admin/produk/getMotifBySupplierAjax') ?>?id_supplier=${supplierId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            let options = '<option value="">-- Semua Motif --</option>';
                            data.data.forEach(motif => {
                                options += `<option value="${motif.id}">${escapeHtml(motif.nama_motif)}</option>`;
                            });
                            filterMotif.innerHTML = options;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                // Load semua motif
                fetch(`<?= base_url('admin/motif/getOptions') ?>`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            let options = '<option value="">-- Semua Motif --</option>';
                            data.data.forEach(motif => {
                                options += `<option value="${motif.id}">${escapeHtml(motif.nama_motif)}</option>`;
                            });
                            filterMotif.innerHTML = options;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text.replace(/[&<>]/g, function (m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;'; if (m === '>') return '&gt;'; return m;
        });
    } // Delete button handler
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const sku = this.dataset.sku;

            Swal.fire({
                title: 'Yakin hapus?',
                text: `Produk dengan SKU "${sku}" akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `<?= base_url('admin/produk/delete') ?>/${id}`;
                }
            });
        });
    });
    </>

        <style>
        /* Card view styling */
            .card {
                border - radius: 10px;
        }

            .card .badge {
                font - size: 11px;
        }
        </style>

    <?= $this->endSection() ?>