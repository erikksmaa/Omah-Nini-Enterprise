<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
// Reopen add modal automatically if there are validation errors
if (session()->getFlashdata('validation_errors')): ?>
<script>
    document.addEventListener('DOMContentLoaded', () =>
        new bootstrap.Modal(document.getElementById('addModal')).show()
    );
</script>
<?php endif; ?>

<div class="container-fluid px-2 px-md-4">

    <!-- ── Page Header ─────────────────────────────────────────────── -->
    <div class="mb-3 d-flex gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Merk
        </button>
    </div>

    <!-- ── Main Card ───────────────────────────────────────────────── -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3 p-md-4">

            <!-- Filter Bar -->
            <form method="GET" id="filterForm" class="row g-2 align-items-end mb-4">
                <div class="col-12 col-md-5">
                    <label class="form-label small fw-semibold text-muted mb-1">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="keyword" class="form-control border-start-0 ps-0"
                            placeholder="Cari nama, kontak, email..."
                            value="<?= esc($keyword ?? '') ?>">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Tampilkan</label>
                    <select name="per_page" class="form-select">
                        <?php foreach ([10, 25, 50, 100] as $n): ?>
                            <option value="<?= $n ?>" <?= (($per_page ?? 10) == $n) ? 'selected' : '' ?>>
                                <?= $n ?> data
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="<?= base_url('admin/supplier') ?>"
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
                                    Nama Merk
                                    <i class="bi bi-arrow-up-down ms-1 text-muted small sort-icon"></i>
                                </th>
                                <th class="sort-header" data-col="2" style="cursor:pointer;user-select:none">
                                    Kontak
                                    <i class="bi bi-arrow-up-down ms-1 text-muted small sort-icon"></i>
                                </th>
                                <th class="sort-header" data-col="3" style="cursor:pointer;user-select:none">
                                    Email
                                    <i class="bi bi-arrow-up-down ms-1 text-muted small sort-icon"></i>
                                </th>
                                <th class="sort-header" data-col="4" style="cursor:pointer;user-select:none">
                                    Alamat
                                    <i class="bi bi-arrow-up-down ms-1 text-muted small sort-icon"></i>
                                </th>
                                <th class="text-center" style="width:12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="mainTableBody">
                            <?php if (!empty($supplier) && is_array($supplier)): ?>
                                <?php
                                $currentPage = $pager->getCurrentPage();
                                $perPage     = $per_page ?? 10;
                                $no          = ($currentPage - 1) * $perPage + 1;
                                ?>
                                <?php foreach ($supplier as $item): ?>
                                    <tr>
                                        <td class="text-center text-muted small"><?= $no++ ?></td>
                                        <td class="fw-semibold"><?= esc($item['nama']) ?></td>
                                        <td>
                                            <?php if (!empty($item['kontak'])): ?>
                                                <i class="bi bi-telephone text-muted me-1 small"></i><?= esc($item['kontak']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small">
                                            <?php if (!empty($item['email'])): ?>
                                                <i class="bi bi-envelope text-muted me-1 small"></i><?= esc($item['email']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-muted"><?= esc($item['alamat']) ?: '-' ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button class="btn btn-sm btn-warning btn-edit"
                                                    data-id="<?= $item['id'] ?>"
                                                    data-nama="<?= esc($item['nama']) ?>"
                                                    data-kontak="<?= esc($item['kontak']) ?>"
                                                    data-email="<?= esc($item['email']) ?>"
                                                    data-alamat="<?= esc($item['alamat']) ?>"
                                                    title="Edit Merk">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="#" class="btn btn-sm btn-danger"
                                                    onclick="return confirmDelete('<?= base_url('admin/supplier/delete/' . $item['id']) ?>', '<?= esc($item['nama']) ?>', 'Merk')"
                                                    title="Hapus Merk">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                                        <?= !empty($keyword)
                                            ? 'Merk "<strong>' . esc($keyword) . '</strong>" tidak ditemukan'
                                            : 'Belum ada data merk' ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer: count + pagination -->
                <?php if (isset($pager) && $pager && $pager->getTotal() > 0): ?>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 pt-3 border-top">
                        <small class="text-muted">
                            Menampilkan <strong><?= count($supplier) ?></strong>
                            dari <strong><?= $pager->getTotal() ?></strong> data
                        </small>
                        <div><?= $pager->links('default', 'bootstrap_pagination') ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ── Mobile Cards ────────────────────────────────────── -->
            <div class="d-md-none">
                <?php if (!empty($supplier) && is_array($supplier)): ?>
                    <?php foreach ($supplier as $item): ?>
                        <div class="card border mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="flex-grow-1 min-w-0">
                                        <p class="fw-bold mb-1 text-truncate"><?= esc($item['nama']) ?></p>
                                        <?php if (!empty($item['kontak'])): ?>
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-telephone me-1"></i><?= esc($item['kontak']) ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if (!empty($item['email'])): ?>
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-envelope me-1"></i><?= esc($item['email']) ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if (!empty($item['alamat'])): ?>
                                            <p class="text-muted small mb-0">
                                                <i class="bi bi-geo-alt me-1"></i><?= esc($item['alamat']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mobile-action-btns">
                                        <button class="btn btn-sm btn-warning btn-edit"
                                            data-id="<?= $item['id'] ?>"
                                            data-nama="<?= esc($item['nama']) ?>"
                                            data-kontak="<?= esc($item['kontak']) ?>"
                                            data-email="<?= esc($item['email']) ?>"
                                            data-alamat="<?= esc($item['alamat']) ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="#" class="btn btn-sm btn-danger"
                                            onclick="return confirmDelete('<?= base_url('admin/supplier/delete/' . $item['id']) ?>', '<?= esc($item['nama']) ?>', 'Merk')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                        <?= !empty($keyword) ? 'Tidak ada hasil untuk "' . esc($keyword) . '"' : 'Belum ada data merk' ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($pager) && $pager && $pager->getTotal() > 0): ?>
                    <div class="d-flex justify-content-center mt-3 pt-2 border-top">
                        <?= $pager->links('default', 'bootstrap_pagination') ?>
                    </div>
                <?php endif; ?>
            </div>

        </div><!-- /card-body -->
    </div><!-- /card -->
</div><!-- /container -->


<!-- ══════════════════════════════════════════════════════════════════
     MODAL: Tambah Merk
═══════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-primary text-white rounded-top">
                <h5 class="modal-title fw-semibold" id="addModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Merk Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="<?= base_url('admin/supplier/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">

                    <?php if (session()->getFlashdata('validation_errors')): ?>
                        <div class="alert alert-danger py-2 small">
                            <?php foreach (session()->getFlashdata('validation_errors') as $err): ?>
                                <div><i class="bi bi-exclamation-circle me-1"></i><?= $err ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Nama Merk <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama" required class="form-control"
                            placeholder="Contoh: Batik Keris, Danar Hadi..."
                            value="<?= old('nama') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Kontak <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="kontak" required class="form-control"
                            placeholder="08xxxxxxxxxx"
                            value="<?= old('kontak') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email</label>
                        <input type="email" name="email" class="form-control"
                            placeholder="email@domain.com"
                            value="<?= old('email') ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"
                            placeholder="Alamat lengkap..."><?= old('alamat') ?></textarea>
                    </div>

                </div>
                <div class="modal-footer bg-light rounded-bottom">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan Merk
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════════════════
     MODAL: Edit Merk (single shared modal, populated via JS)
═══════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-warning rounded-top">
                <h5 class="modal-title fw-semibold" id="editModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Merk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Nama Merk <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Kontak <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="kontak" required id="edit_kontak" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Alamat</label>
                        <textarea name="alamat" id="edit_alamat" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save me-1"></i>Update Merk
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


<script>
    // ── Edit Modal Handler ──────────────────────────────────────────
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit_nama').value    = this.dataset.nama    ?? '';
            document.getElementById('edit_kontak').value  = this.dataset.kontak  ?? '';
            document.getElementById('edit_email').value   = this.dataset.email   ?? '';
            document.getElementById('edit_alamat').value  = this.dataset.alamat  ?? '';
            document.getElementById('editForm').action    =
                `<?= base_url('admin/supplier/update') ?>/${this.dataset.id}`;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        });
    });

    // ── Client-side Table Sort ──────────────────────────────────────
    document.querySelectorAll('.sort-header').forEach(th => {
        th.addEventListener('click', function () {
            const col   = parseInt(this.dataset.col);
            const tbody = document.getElementById('mainTableBody');
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const asc  = this.dataset.order !== 'asc';
            this.dataset.order = asc ? 'asc' : 'desc';

            // Reset all sort icons
            document.querySelectorAll('.sort-header').forEach(t => {
                t.querySelector('.sort-icon').className =
                    'bi bi-arrow-up-down ms-1 text-muted small sort-icon';
            });
            this.querySelector('.sort-icon').className =
                `bi bi-arrow-${asc ? 'up' : 'down'} ms-1 text-primary small sort-icon`;

            rows.sort((a, b) => {
                const av = (a.cells[col]?.textContent ?? '').trim();
                const bv = (b.cells[col]?.textContent ?? '').trim();
                return asc
                    ? av.localeCompare(bv, 'id', { sensitivity: 'base' })
                    : bv.localeCompare(av, 'id', { sensitivity: 'base' });
            });
            rows.forEach(r => tbody.appendChild(r));
        });
    });
</script>

<?= $this->endSection() ?>