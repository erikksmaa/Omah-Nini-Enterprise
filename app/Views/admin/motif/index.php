<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-4">

    <div class="mb-3 d-flex gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Motif
        </button>
    </div>

    <!-- ── Main Card ───────────────────────────────────────────────── -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3 p-md-4">

            <!-- Filter Bar -->
            <form method="GET" id="filterForm" class="row g-3 align-items-end mb-4">
                <div class="col-12 col-lg-4">
                    <label class="form-label small fw-semibold text-muted mb-2">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="keyword" class="form-control border-start-0 ps-0"
                            placeholder="Cari motif atau merk..."
                            value="<?= esc($keyword ?? '') ?>">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label small fw-semibold text-muted mb-2">Merk / Brand</label>
                    <select name="filter_supplier" class="form-select">
                        <option value="">— Semua Merk —</option>
                        <?php if (!empty($suppliers) && is_array($suppliers)): ?>
                            <?php foreach ($suppliers as $sup): ?>
                                <option value="<?= isset($sup['id']) ? esc($sup['id']) : '' ?>"
                                    <?= (($filter_supplier ?? '') == (isset($sup['id']) ? $sup['id'] : '')) ? 'selected' : '' ?>>
                                    <?= isset($sup['nama']) ? esc($sup['nama']) : 'N/A' ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-2">Tampilkan</label>
                    <select name="per_page" class="form-select">
                        <?php foreach ([10, 25, 50, 100] as $n): ?>
                            <option value="<?= $n ?>" <?= (($per_page ?? 10) == $n) ? 'selected' : '' ?>>
                                <?= $n ?> data
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="<?= base_url('admin/motif') ?>"
                        class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                    </a>
                </div>
            </form>

            <!-- ── Desktop Table ──────────────────────────────────── -->
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center text-muted small" style="width:5%">#</th>
                                <th class="sort-header" data-col="1" style="cursor:pointer;user-select:none">
                                    Merk / Brand
                                    <i class="bi bi-arrow-up-down ms-1 text-muted small sort-icon"></i>
                                </th>
                                <th class="sort-header" data-col="2" style="cursor:pointer;user-select:none">
                                    Nama Motif
                                    <i class="bi bi-arrow-up-down ms-1 text-muted small sort-icon"></i>
                                </th>
                                <th class="sort-header" data-col="3" style="cursor:pointer;user-select:none">
                                    Keterangan
                                    <i class="bi bi-arrow-up-down ms-1 text-muted small sort-icon"></i>
                                </th>
                                <th class="text-center" style="width:12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="mainTableBody">
                            <?php if (!empty($motif)): ?>
                                <?php $no = 1 + (($pager->getCurrentPage() - 1) * ($per_page ?? 10)); ?>
                                <?php foreach ($motif as $row): ?>
                                    <tr>
                                        <td class="text-center text-muted small"><?= $no++ ?></td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                                                <?= esc($row['nama_supplier']) ?>
                                            </span>
                                        </td>
                                        <td class="fw-semibold"><?= esc($row['nama_motif']) ?></td>
                                        <td class="small text-muted"><?= esc($row['keterangan'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button class="btn btn-sm btn-warning btn-edit"
                                                    data-id="<?= $row['id'] ?>"
                                                    data-id_supplier="<?= $row['id_supplier'] ?>"
                                                    data-nama_motif="<?= esc($row['nama_motif']) ?>"
                                                    data-keterangan="<?= esc($row['keterangan'] ?? '') ?>"
                                                    title="Edit Motif">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger btn-delete"
                                                    data-id="<?= $row['id'] ?>"
                                                    data-nama_motif="<?= esc($row['nama_motif']) ?>"
                                                    title="Hapus Motif">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                                        <?= !empty($keyword) || !empty($filter_supplier)
                                            ? 'Tidak ada motif yang sesuai filter'
                                            : 'Belum ada data motif' ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <?php if (isset($pager) && $pager->getTotal() > 0): ?>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 pt-3 border-top">
                        <small class="text-muted">
                            Menampilkan <strong><?= count($motif) ?></strong>
                            dari <strong><?= $pager->getTotal() ?></strong> data
                        </small>
                        <div><?= $pager->links('default', 'bootstrap_pagination') ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ── Mobile Cards ────────────────────────────────────── -->
            <div class="d-md-none">
                <?php if (!empty($motif)): ?>
                    <?php foreach ($motif as $row): ?>
                        <div class="card border mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="flex-grow-1 min-w-0">
                                        <p class="fw-bold mb-1 text-truncate"><?= esc($row['nama_motif']) ?></p>
                                        <p class="mb-1">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">
                                                <?= esc($row['nama_supplier']) ?>
                                            </span>
                                        </p>
                                        <?php if (!empty($row['keterangan'])): ?>
                                            <p class="text-muted small mb-0"><?= esc($row['keterangan']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mobile-action-btns">
                                        <button class="btn btn-sm btn-warning btn-edit"
                                            data-id="<?= $row['id'] ?>"
                                            data-id_supplier="<?= $row['id_supplier'] ?>"
                                            data-nama_motif="<?= esc($row['nama_motif']) ?>"
                                            data-keterangan="<?= esc($row['keterangan'] ?? '') ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete"
                                            data-id="<?= $row['id'] ?>"
                                            data-nama_motif="<?= esc($row['nama_motif']) ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                        <?= !empty($keyword) || !empty($filter_supplier)
                            ? 'Tidak ada motif yang sesuai filter'
                            : 'Belum ada data motif' ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($pager) && $pager->getTotal() > 0): ?>
                    <div class="d-flex justify-content-center mt-3 pt-2 border-top">
                        <?= $pager->links('default', 'bootstrap_pagination') ?>
                    </div>
                <?php endif; ?>
            </div>

        </div><!-- /card-body -->
    </div><!-- /card -->
</div><!-- /container -->


<!-- ══════════════════════════════════════════════════════════════════
     MODAL: Tambah Motif
═══════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-primary text-white rounded-top">
                <h5 class="modal-title fw-semibold" id="addModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Motif Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="<?= base_url('admin/motif/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Merk / Brand <span class="text-danger">*</span>
                        </label>
                        <select name="id_supplier" class="form-select" required>
                            <option value="">— Pilih Merk —</option>
                            <?php foreach ($suppliers as $sup): ?>
                                <option value="<?= $sup['id'] ?>"><?= esc($sup['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Nama Motif <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_motif" class="form-control"
                            placeholder="Contoh: Kawung, Parang, Mega Mendung..." required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"
                            placeholder="Deskripsi singkat motif..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan Motif
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════════════════
     MODAL: Edit Motif
═══════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-warning rounded-top">
                <h5 class="modal-title fw-semibold" id="editModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Motif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Merk / Brand <span class="text-danger">*</span>
                        </label>
                        <select name="id_supplier" id="edit_id_supplier" class="form-select" required>
                            <option value="">— Pilih Merk —</option>
                            <?php foreach ($suppliers as $sup): ?>
                                <option value="<?= $sup['id'] ?>"><?= esc($sup['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Nama Motif <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_motif" id="edit_nama_motif"
                            class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan"
                            class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save me-1"></i>Update Motif
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


<script>
    // ── Edit Modal Handler ──────────────────────────────────────────
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id_supplier').value = this.dataset.id_supplier ?? '';
            document.getElementById('edit_nama_motif').value = this.dataset.nama_motif ?? '';
            document.getElementById('edit_keterangan').value = this.dataset.keterangan ?? '';
            document.getElementById('editForm').action =
                `<?= base_url('admin/motif/update') ?>/${this.dataset.id}`;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        });
    });

    // ── Delete Handler ──────────────────────────────────────────────
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nama_motif = this.dataset.nama_motif;
            Swal.fire({
                title: 'Hapus Motif?',
                html: `Motif <strong>${nama_motif}</strong> akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash me-1"></i>Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed)
                    window.location.href = `<?= base_url('admin/motif/delete') ?>/${id}`;
            });
        });
    });

    // ── Client-side Table Sort ──────────────────────────────────────
    document.querySelectorAll('.sort-header').forEach(th => {
        th.addEventListener('click', function() {
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
                return asc ?
                    av.localeCompare(bv, 'id', {
                        sensitivity: 'base'
                    }) :
                    bv.localeCompare(av, 'id', {
                        sensitivity: 'base'
                    });
            });
            rows.forEach(r => tbody.appendChild(r));
        });
    });
</script>

<?= $this->endSection() ?>