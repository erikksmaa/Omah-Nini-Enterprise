<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-circle"></i> Tambah Motif
                    </button>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="keyword" class="form-control" 
                                   placeholder="Cari motif atau supplier..." value="<?= $keyword ?? '' ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Cari
                            </button>
                            <?php if (!empty($keyword)): ?>
                                <a href="<?= base_url('admin/motif') ?>" class="btn btn-secondary">
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
                                    <th>Merk / Brand</th>
                                    <th>Nama Motif</th>
                                    <th>Keterangan</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($motif)): ?>
                                    <?php $no = 1 + (($pager->getCurrentPage() - 1) * 10); ?>
                                    <?php foreach ($motif as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><strong><?= esc($row['nama_supplier']) ?></strong></td>
                                            <td><?= esc($row['nama_motif']) ?></td>
                                            <td><?= esc($row['keterangan'] ?? '-') ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-warning btn-sm btn-edit" 
                                                        data-id="<?= $row['id'] ?>"
                                                        data-id_supplier="<?= $row['id_supplier'] ?>"
                                                        data-nama_motif="<?= esc($row['nama_motif']) ?>"
                                                        data-keterangan="<?= esc($row['keterangan'] ?? '') ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm btn-delete" 
                                                        data-id="<?= $row['id'] ?>"
                                                        data-nama_motif="<?= esc($row['nama_motif']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data motif</td>
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

<!-- Modal Add Motif -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Motif Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/motif/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Merk / Brand <span class="text-danger">*</span></label>
                        <select name="id_supplier" class="form-control">
                            <option value="">-- Pilih Merk --</option>
                            <?php foreach ($suppliers as $sup): ?>
                                <option value="<?= $sup['id'] ?>"><?= esc($sup['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Motif <span class="text-danger">*</span></label>
                        <input type="text" name="nama_motif" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
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

<!-- Modal Edit Motif -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Motif</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Merk / Brand <span class="text-danger">*</span></label>
                        <select name="id_supplier" id="edit_id_supplier" class="form-control">
                            <option value="">-- Pilih Merk --</option>
                            <?php foreach ($suppliers as $sup): ?>
                                <option value="<?= $sup['id'] ?>"><?= esc($sup['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Motif <span class="text-danger">*</span></label>
                        <input type="text" name="nama_motif" id="edit_nama_motif" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="2"></textarea>
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
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const id_supplier = this.dataset.id_supplier;
            const nama_motif = this.dataset.nama_motif;
            const keterangan = this.dataset.keterangan;
            
            document.getElementById('edit_id_supplier').value = id_supplier;
            document.getElementById('edit_nama_motif').value = nama_motif;
            document.getElementById('edit_keterangan').value = keterangan;
            
            const form = document.getElementById('editForm');
            form.action = `<?= base_url('admin/motif/update') ?>/${id}`;
            
            new bootstrap.Modal(document.getElementById('editModal')).show();
        });
    });

    // Delete button handler
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const nama_motif = this.dataset.nama_motif;
            
            Swal.fire({
                title: 'Yakin hapus?',
                text: `Motif "${nama_motif}" akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `<?= base_url('admin/motif/delete') ?>/${id}`;
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>