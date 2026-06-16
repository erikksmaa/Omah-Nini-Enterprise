<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">

    <!-- ── Summary Bar (tidak diubah) ──────────────────────────────── -->
    <div class="summary-bar mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex flex-wrap gap-2">
            <div class="summary-chip">
                <strong><?= $total_merek ?></strong> Merek
            </div>
            <div class="summary-chip">
                <strong><?= $total_motif ?></strong> Motif
            </div>
            <div class="summary-chip">
                <strong><?= $total_sku ?></strong> Produk
            </div>
            <div class="summary-chip summary-chip--stok">
                <i class="bi bi-box-seam"></i>
                <strong><?= number_format($total_stok) ?></strong> Total Stok
            </div>
        </div>
        <a href="<?= base_url('admin/produk/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Produk Baru
        </a>
    </div>

    <!-- ── Filter Bar ──────────────────────────────────────────────── -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label small fw-semibold mb-2">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="keyword" class="form-control"
                            placeholder="Cari SKU, motif, warna, merek..." value="<?= esc($keyword ?? '') ?>">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold mb-2">Merek</label>
                    <select name="supplier" class="form-select">
                        <option value="">— Semua Merek —</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= $sup['id'] ?>" <?= (($selectedSupplier ?? '') == $sup['id']) ? 'selected' : '' ?>>
                                <?= esc($sup['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-2">Tampilkan</label>
                    <select name="per_page" class="form-select">
                        <?php foreach ([10, 25, 50, 100] as $n): ?>
                            <option value="<?= $n ?>" <?= (($per_page ?? 10) == $n) ? 'selected' : '' ?>>
                                <?= $n ?> data
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="<?= base_url('admin/produk') ?>" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Tabel Desktop ───────────────────────────────────────────── -->
    <div class="d-none d-md-block">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center ps-3" style="width:64px;">Foto</th>
                                <th class="sort-header" data-col="1" style="cursor:pointer;user-select:none">
                                    Merek <i class="bi bi-arrow-up-down ms-1 text-muted small sort-icon"></i>
                                </th>
                                <th class="sort-header" data-col="2" style="cursor:pointer;user-select:none">
                                    Motif <i class="bi bi-arrow-up-down ms-1 text-muted small sort-icon"></i>
                                </th>
                                <th class="sort-header" data-col="3" style="cursor:pointer;user-select:none">
                                    Warna <i class="bi bi-arrow-up-down ms-1 text-muted small sort-icon"></i>
                                </th>
                                <th class="sort-header" data-col="4" style="cursor:pointer;user-select:none">
                                    SKU <i class="bi bi-arrow-up-down ms-1 text-muted small sort-icon"></i>
                                </th>
                                <th class="text-center sort-header" data-col="5"
                                    style="cursor:pointer;user-select:none">
                                    Stok <i class="bi bi-arrow-up-down ms-1 text-muted small sort-icon"></i>
                                </th>
                                <th class="text-center">Min. Stok</th>
                                <th class="text-center">Status</th>
                                <th class="text-center pe-3" style="width:90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="mainTableBody">
                            <?php if (!empty($produk)): ?>
                                <?php foreach ($produk as $row): ?>
                                    <?php
                                    $stok = (int) $row['stok'];
                                    $min = (int) $row['min_stok'];
                                    if ($stok == 0) {
                                        $badgeClass = 'bg-danger';
                                        $statusText = 'Habis';
                                        $stokClass = 'text-danger';
                                    } elseif ($stok <= $min) {
                                        $badgeClass = 'bg-warning text-dark';
                                        $statusText = 'Menipis';
                                        $stokClass = 'text-warning';
                                    } else {
                                        $badgeClass = 'bg-success';
                                        $statusText = 'Aman';
                                        $stokClass = 'text-success';
                                    }
                                    $fotoUrl = !empty($row['foto'])
                                        ? base_url('uploads/produk/' . $row['foto'])
                                        : '';
                                    ?>
                                    <tr>
                                        <td class="text-center ps-3">
                                            <?php if ($fotoUrl): ?>
                                                <img src="<?= $fotoUrl ?>" class="img-thumbnail img-lightbox-trigger"
                                                    style="width:50px; height:50px; object-fit:cover; cursor:pointer;"
                                                    data-large-src="<?= $fotoUrl ?>" alt="<?= esc($row['sku']) ?>">
                                            <?php else: ?>
                                                <div class="bg-light border d-inline-flex justify-content-center
                                                            align-items-center rounded text-muted"
                                                    style="width:50px; height:50px; font-size:10px;">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-semibold"><?= esc($row['nama_supplier']) ?></td>
                                        <td><?= esc($row['nama_motif']) ?></td>
                                        <td><?= esc($row['nama_warna']) ?></td>
                                        <td><code class="text-primary"><?= esc($row['sku']) ?></code></td>
                                        <td class="text-center fw-bold">
                                            <span class="<?= $stokClass ?>"><?= number_format($stok) ?></span>
                                        </td>
                                        <td class="text-center text-muted"><?= number_format($min) ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $badgeClass ?> px-2 py-1"><?= $statusText ?></span>
                                        </td>
                                        <td class="text-center pe-3">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="<?= base_url('admin/produk/edit/' . $row['id']) ?>"
                                                    class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="confirmHapus(<?= $row['id'] ?>, '<?= esc($row['sku'], 'js') ?>')"
                                                    title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                                        Tidak ada data produk yang sesuai filter
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <?php if ($pager && $pager->getTotal() > 0): ?>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 px-3 py-3 border-top">
                        <small class="text-muted">
                            Menampilkan <strong><?= count($produk) ?></strong>
                            dari <strong><?= $pager->getTotal() ?></strong> produk
                        </small>
                        <div><?= $pager->links('default', 'bootstrap_pagination') ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── Card View Mobile ────────────────────────────────────────── -->
    <div class="d-md-none">
        <?php if (!empty($produk)): ?>
            <?php foreach ($produk as $row): ?>
                <?php
                $stok = (int) $row['stok'];
                $min = (int) $row['min_stok'];
                if ($stok == 0) {
                    $badgeClass = 'bg-danger';
                    $statusText = 'Habis';
                    $stokClass = 'text-danger';
                } elseif ($stok <= $min) {
                    $badgeClass = 'bg-warning text-dark';
                    $statusText = 'Menipis';
                    $stokClass = 'text-warning';
                } else {
                    $badgeClass = 'bg-success';
                    $statusText = 'Aman';
                    $stokClass = 'text-success';
                }
                $fotoUrl = !empty($row['foto'])
                    ? base_url('uploads/produk/' . $row['foto'])
                    : '';
                ?>
                <div class="card border mb-2">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <?php if ($fotoUrl): ?>
                                    <img src="<?= $fotoUrl ?>" class="img-thumbnail img-lightbox-trigger"
                                        style="width:45px; height:45px; object-fit:cover; cursor:pointer;"
                                        data-large-src="<?= $fotoUrl ?>" alt="">
                                <?php else: ?>
                                    <div class="bg-light border d-inline-flex justify-content-center
                                                align-items-center rounded text-muted"
                                        style="width:45px; height:45px; font-size:16px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="badge <?= $badgeClass ?>"><?= $statusText ?></span>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="<?= base_url('admin/produk/edit/' . $row['id']) ?>" class="btn btn-sm btn-warning"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-danger"
                                    onclick="confirmHapus(<?= $row['id'] ?>, '<?= esc($row['sku'], 'js') ?>')" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row g-1 small">
                            <div class="col-4 text-muted">Merek:</div>
                            <div class="col-8 fw-semibold"><?= esc($row['nama_supplier']) ?></div>

                            <div class="col-4 text-muted">Motif:</div>
                            <div class="col-8"><?= esc($row['nama_motif']) ?></div>

                            <div class="col-4 text-muted">Warna:</div>
                            <div class="col-8"><?= esc($row['nama_warna']) ?></div>

                            <div class="col-4 text-muted">SKU:</div>
                            <div class="col-8"><code class="text-primary"><?= esc($row['sku']) ?></code></div>

                            <div class="col-4 text-muted">Stok:</div>
                            <div class="col-8">
                                <span class="<?= $stokClass ?> fw-bold"><?= number_format($stok) ?></span>
                                <span class="text-muted"> / Min: <?= number_format($min) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                Tidak ada data produk yang sesuai filter
            </div>
        <?php endif; ?>

        <?php if ($pager && $pager->getTotal() > 0): ?>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 pt-2 border-top">
                <small class="text-muted">
                    Menampilkan <strong><?= count($produk) ?></strong>
                    dari <strong><?= $pager->getTotal() ?></strong> produk
                </small>
                <div><?= $pager->links('default', 'bootstrap_pagination') ?></div>
            </div>
        <?php endif; ?>
    </div>

</div><!-- /container-fluid -->


<!-- ═══ MODAL HAPUS (tidak diubah) ═══ -->
<div class="modal-overlay" id="modalHapus">
    <div class="modal-box">
        <i class="bi bi-exclamation-triangle"></i>
        <p>Yakin hapus produk<br><strong id="hapusSku">—</strong>?</p>
        <div class="modal-actions">
            <form id="formHapus" method="post" action="">
                <?= csrf_field() ?>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-modal-hapus">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══ LIGHTBOX (tidak diubah) ═══ -->
<div id="image-lightbox" class="lightbox-overlay">
    <span class="lightbox-close">&times;</span>
    <img class="lightbox-content" id="lightbox-img" src="" alt="Foto Produk">
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    // ── MODAL HAPUS ──
    function confirmHapus(id, sku) {
        document.getElementById('hapusSku').textContent = sku;
        document.getElementById('formHapus').action = '<?= base_url('admin/produk/delete/') ?>' + id;
        document.getElementById('modalHapus').classList.add('show');
    }
    function closeModal() {
        document.getElementById('modalHapus').classList.remove('show');
    }
    document.getElementById('modalHapus').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });

    // ── LIGHTBOX ──
    document.querySelectorAll('.img-lightbox-trigger').forEach(img => {
        img.addEventListener('click', function (e) {
            e.stopPropagation();
            document.getElementById('lightbox-img').src = this.dataset.largeSrc;
            document.getElementById('image-lightbox').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    });
    document.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
    document.getElementById('image-lightbox').addEventListener('click', function (e) {
        if (e.target === this) closeLightbox();
    });
    function closeLightbox() {
        document.getElementById('image-lightbox').style.display = 'none';
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeLightbox(); closeModal(); }
    });

    // ── CLIENT-SIDE TABLE SORT ──
    document.querySelectorAll('.sort-header').forEach(th => {
        th.addEventListener('click', function () {
            const col = parseInt(this.dataset.col);
            const tbody = document.getElementById('mainTableBody');
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const asc = this.dataset.order !== 'asc';
            this.dataset.order = asc ? 'asc' : 'desc';

            document.querySelectorAll('.sort-header').forEach(t => {
                t.querySelector('.sort-icon').className =
                    'bi bi-arrow-up-down ms-1 text-muted small sort-icon';
            });
            this.querySelector('.sort-icon').className =
                `bi bi-arrow-${asc ? 'up' : 'down'} ms-1 text-primary small sort-icon`;

            rows.sort((a, b) => {
                const av = (a.cells[col]?.textContent ?? '').trim();
                const bv = (b.cells[col]?.textContent ?? '').trim();
                // Numeric sort for stok column (col 5)
                if (col === 5) {
                    return asc
                        ? (parseFloat(av.replace(/\./g, '')) || 0) - (parseFloat(bv.replace(/\./g, '')) || 0)
                        : (parseFloat(bv.replace(/\./g, '')) || 0) - (parseFloat(av.replace(/\./g, '')) || 0);
                }
                return asc
                    ? av.localeCompare(bv, 'id', { sensitivity: 'base' })
                    : bv.localeCompare(av, 'id', { sensitivity: 'base' });
            });
            rows.forEach(r => tbody.appendChild(r));
        });
    });
</script>

<style>
    :root {
        --primary: #4f46e5;
        --primary-light: #6366f1;
        --primary-dark: #4338ca;
        --success: #10b981;
        --success-light: #34d399;
        --success-dark: #059669;
        --danger: #ef4444;
        --danger-light: #f87171;
        --danger-dark: #dc2626;
        --warning: #f59e0b;
        --warning-light: #fbbf24;
        --warning-dark: #d97706;
        --info: #06b6d4;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --card-bg: #ffffff;
        --card-border: #e2e8f0;
    }

    /* ── BUTTON TAMBAH ── */
    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--primary);
        color: #fff !important;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-add:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    * ── SUMMARY BAR ── */ .summary-bar {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .summary-chip {
        background: var(--card-bg);
        border: 1px solid var(--gray-200);
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--gray-700);
    }

    .summary-chip strong {
        font-size: 16px;
        font-weight: 600;
        color: var(--primary);
    }

    .summary-chip--stok {
        border-color: #6ee7b7;
        background: #ecfdf5;
    }

    .summary-chip--stok strong {
        color: var(--success-dark);
    }

    .summary-chip--stok i {
        color: var(--success-dark);
    }

    /* ── FILTER CARD ── */
    .card {
        background: var(--card-bg);
        border: 1px solid var(--gray-200) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        border-bottom: 1px solid var(--gray-200) !important;
        background: var(--gray-50);
    }

    .card-body {
        border: 0;
    }

    .form-label {
        color: var(--gray-700);
        font-weight: 600;
        font-size: 12px;
    }

    .form-label.small {
        font-weight: 500;
    }

    .form-control,
    .form-select {
        border: 1px solid var(--gray-200) !important;
        border-radius: 8px;
        font-size: 13px;
        transition: border-color 0.15s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-light) !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
    }

    .form-control::placeholder {
        color: var(--gray-400);
    }

    .input-group-text {
        border: 1px solid var(--gray-200) !important;
        background: var(--gray-50) !important;
        color: var(--gray-500) !important;
    }

    /* ── TABLE ── */
    .table {
        font-size: 13px;
        border-collapse: collapse;
    }

    .table thead {
        background: var(--gray-50);
        border-bottom: 2px solid var(--gray-200);
    }

    .table th {
        color: var(--gray-700);
        font-weight: 600;
        padding: 12px;
        vertical-align: middle;
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .sort-header {
        cursor: pointer;
        user-select: none;
        transition: color 0.15s;
    }

    .sort-header:hover {
        color: var(--primary-dark);
    }

    code {
        background: var(--gray-100);
        color: var(--primary);
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
        font-family: 'Courier New', monospace;
    }

    /* ── BADGES ── */
    .badge {
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 600;
    }

    .bg-success {
        background-color: #ecfdf5 !important;
        color: var(--success-dark) !important;
        border: 1px solid #6ee7b7;
    }

    .bg-warning {
        background-color: #fffbeb !important;
        color: var(--warning-dark) !important;
        border: 1px solid var(--warning-light);
    }

    .bg-danger {
        background-color: #fef2f2 !important;
        color: var(--danger-dark) !important;
        border: 1px solid var(--danger-light);
    }

    /* ── BUTTONS ── */
    .btn-primary {
        background: var(--primary) !important;
        border: none !important;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        background: var(--primary-dark) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .btn-outline-secondary {
        border: 1px solid var(--gray-300) !important;
        color: var(--gray-700) !important;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .btn-outline-secondary:hover {
        background: var(--gray-100);
        border-color: var(--gray-400) !important;
    }

    .btn-warning {
        background: var(--warning) !important;
        border: none !important;
        color: #fff !important;
        border-radius: 6px;
        transition: all 0.2s;
    }

    .btn-warning:hover {
        background: var(--warning-dark) !important;
    }

    .btn-danger {
        background: var(--danger) !important;
        border: none !important;
        color: #fff !important;
        border-radius: 6px;
        transition: all 0.2s;
    }

    .btn-danger:hover {
        background: var(--danger-dark) !important;
    }

    /* ── IMAGE ── */
    .img-thumbnail {
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        padding: 2px;
        transition: all 0.2s;
    }

    .img-thumbnail:hover {
        border-color: var(--primary-light);
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.15);
    }

    /* ── MODAL HAPUS ── */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 8888;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-box {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 32px 28px 24px;
        width: 320px;
        text-align: center;
        box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
    }

    .modal-box>i {
        font-size: 40px;
        color: var(--danger);
        display: block;
        margin-bottom: 16px;
    }

    .modal-box>p {
        font-size: 14px;
        color: var(--gray-700);
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .modal-box strong {
        color: var(--gray-800);
        font-weight: 700;
    }

    .modal-actions {
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .btn-modal-cancel {
        padding: 8px 20px;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        background: var(--card-bg);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        color: var(--gray-700);
        transition: all 0.2s;
    }

    .btn-modal-cancel:hover {
        background: var(--gray-100);
        border-color: var(--gray-400);
    }

    .btn-modal-hapus {
        padding: 8px 20px;
        border: none;
        border-radius: 8px;
        background: var(--danger);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-modal-hapus:hover {
        background: var(--danger-dark);
    }

    /* ── LIGHTBOX ── */
    .lightbox-overlay {
        display: none;
        position: fixed;
        z-index: 9999;
        inset: 0;
        background: rgba(0, 0, 0, 0.9);
        justify-content: center;
        align-items: center;
    }

    .lightbox-overlay.show {
        display: flex;
    }

    .lightbox-content {
        max-width: 90%;
        max-height: 80vh;
        object-fit: contain;
        border: 3px solid #fff;
        border-radius: 8px;
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 40px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10000;
        transition: color 0.2s;
    }

    .lightbox-close:hover {
        color: #ccc;
    }

    /* ── PAGINATION ── */
    .pagination {
        gap: 4px;
    }

    .pagination .page-link {
        border: 1px solid var(--gray-200);
        color: var(--primary);
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 12px;
        transition: all 0.2s;
    }

    .pagination .page-link:hover {
        background: var(--gray-100);
        border-color: var(--primary-light);
    }

    .pagination .page-item.active .page-link {
        background: var(--primary);
        border-color: var(--primary);
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
        .summary-chip {
            padding: 8px 12px;
            font-size: 12px;
        }

        .summary-chip strong {
            font-size: 16px;
        }
    }
</style>
<?= $this->endSection() ?>