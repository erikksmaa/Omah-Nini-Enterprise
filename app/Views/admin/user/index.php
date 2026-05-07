<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- FORM TAMBAH USER -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-plus"></i> Tambah User</h5>
            </div>
            <div class="card-body p-3">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Validasi Gagal!</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('admin/user/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label>Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" value="<?= old('username') ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required>
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-control" required>
                            <option value="">Pilih Role</option>
                            <option value="admin" <?= old('role') == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="pemilik" <?= old('role') == 'pemilik' ? 'selected' : '' ?>>Pemilik</option>
                            <option value="karyawan" <?= old('role') == 'karyawan' ? 'selected' : '' ?>>Karyawan</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save"></i> Simpan User
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- TABEL DAFTAR USER -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-people"></i> Daftar User</h5>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tableUser">
                        <thead >
                            <tr>
                                <th width="5%">No</th>
                                <th width="35%">Username</th>
                                <th width="25%">Role</th>
                                <th width="35%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($users as $item): ?>
                                <tr id="row-<?= $item['user_id'] ?>">
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= esc($item['username']) ?></strong></td>
                                    <td>
                                        <?php
                                        $roleClass = $item['role'] == 'admin' ? 'primary' : 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $roleClass ?>"><?= strtoupper($item['role']) ?></span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning btn-edit" 
                                                data-id="<?= $item['user_id'] ?>"
                                                data-username="<?= esc($item['username']) ?>"
                                                data-role="<?= $item['role'] ?>">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>

                                        <?php if (session()->get('user_id') != $item['user_id']): ?>
                                            <a href="#" class="btn btn-sm btn-danger btn-delete"
                                                data-id="<?= $item['user_id'] ?>"
                                                data-username="<?= esc($item['username']) ?>">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT USER (Single modal, reuse untuk semua user) -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Password <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
                                <input type="password" name="password" id="edit_password" class="form-control">
                                <small class="text-muted">Minimal 6 karakter jika diisi</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Konfirmasi Password</label>
                                <input type="password" name="confirm_password" id="edit_confirm_password" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Role <span class="text-danger">*</span></label>
                        <select name="role" id="edit_role" class="form-control" required>
                            <option value="admin">Admin</option>
                            <option value="karyawan">Karyawan</option>
                            <option value="pemilik">Pemilik</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
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
        const username = this.dataset.username;
        const role = this.dataset.role;
        
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_confirm_password').value = '';
        
        const form = document.getElementById('editForm');
        form.action = `<?= base_url('admin/user/update') ?>/${id}`;
        
        new bootstrap.Modal(document.getElementById('editModal')).show();
    });
});

// Delete button handler
document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const id = this.dataset.id;
        const username = this.dataset.username;
        
        Swal.fire({
            title: 'Yakin hapus?',
            text: `User "${username}" akan dihapus permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?= base_url('admin/user/delete') ?>/${id}`;
            }
        });
    });
});
</script>

<style>
    .badge {
        font-size: 0.8em;
        padding: 5px 10px;
    }
</style>
<?= $this->endSection() ?>