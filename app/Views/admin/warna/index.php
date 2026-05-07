<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addModal">
                        <i class="bi bi-plus-circle"></i> Tambah Warna
                    </button>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="keyword" class="form-control" placeholder="Cari warna..."
                                value="<?= $keyword ?? '' ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Cari
                            </button>
                            <?php if (!empty($keyword)): ?>
                                <a href="<?= base_url('admin/warna') ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Reset
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Warna</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($warna)): ?>
                                    <?php $no = 1 + (($pager->getCurrentPage() - 1) * 10); ?>
                                    <?php foreach ($warna as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= esc($row['nama_warna']) ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-warning btn-sm btn-edit" data-id="<?= $row['id'] ?>"
                                                    data-nama_warna="<?= esc($row['nama_warna']) ?>"
                                                    data-kode_hex="<?= esc($row['kode_hex'] ?? '') ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $row['id'] ?>"
                                                    data-nama_warna="<?= esc($row['nama_warna']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data warna</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Warna -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Warna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/warna/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Warna <span class="text-danger">*</span></label>
                        <input type="text" name="nama_warna" class="form-control" required>
                        <small class="text-muted">Contoh: Putih, Biru, Merah, Sogan, Toska, dll</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Warna -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Warna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Warna <span class="text-danger">*</span></label>
                        <input type="text" name="nama_warna" id="edit_nama_warna" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    // Edit button handler
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const nama_warna = this.dataset.nama_warna;
            const kode_hex = this.dataset.kode_hex;

            document.getElementById('edit_nama_warna').value = nama_warna;

            const form = document.getElementById('editForm');
            form.action = `<?= base_url('admin/warna/update') ?>/${id}`;

            new bootstrap.Modal(document.getElementById('editModal')).show();
        });
    });

    // Delete button handler
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const nama_warna = this.dataset.nama_warna;

            Swal.fire({
                title: 'Yakin hapus?',
                text: `Warna "${nama_warna}" akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `<?= base_url('admin/warna/delete') ?>/${id}`;
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>