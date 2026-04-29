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
                <?php if (session()->getFlashdata('validation_errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Validasi Gagal!</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach (session()->getFlashdata('validation_errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <form action="<?= base_url('admin/user/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label>Username <span class="text-danger">*</span></label>
                        <input type="text" name="username"
                            class="form-control <?= (isset(session()->getFlashdata('validation_errors')['username'])) ? 'is-invalid' : '' ?>"
                            value="<?= old('username') ?>" placeholder="Contoh: admin, gudang01, kasir01">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" name="password"
                                    class="form-control <?= (isset(session()->getFlashdata('validation_errors')['password'])) ? 'is-invalid' : '' ?>"
                                >
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" name="confirm_password"
                                    class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['confirm_password'])) ? 'is-invalid' : '' ?>"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Role <span class="text-danger">*</span></label>
                        <select name="role"
                            class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['role'])) ? 'is-invalid' : '' ?>"
                        >
                            <option value="">Pilih Role</option>
                            <option value="admin" <?= old('role') == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="gudang" <?= old('role') == 'gudang' ? 'selected' : '' ?>>Gudang</option>
                            <option value="kasir" <?= old('role') == 'kasir' ? 'selected' : '' ?>>Kasir</option>
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
                    <table class="table table-striped table-hover" id="tableUser">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="35%">Username</th>
                                <th width="25%">Role</th>
                                <th width="35%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($users as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= esc($item['username']) ?></strong></td>
                                    <td>
                                        <?php
                                        $roleClass = [
                                            'admin' => 'primary',
                                            'gudang' => 'secondary',
                                            'kasir' => 'success'
                                        ];
                                        $class = $roleClass[$item['role']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $class ?>"><?= strtoupper($item['role']) ?></span>
                                    </td>
                                    <td>
                                        <!-- PERBAIKAN: user_id BUKAN id -->
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editModal<?= $item['user_id'] ?>">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>

                                        <?php if (session()->get('user_id') != $item['user_id']): ?>
                                            <a href="#" class="btn btn-sm btn-danger"
                                                onclick="return confirmDelete('<?= base_url('admin/user/delete/' . $item['user_id']) ?>', '<?= esc($item['username']) ?>', 'User')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- MODAL EDIT USER - PERBAIKAN: user_id -->
                                <div class="modal fade" id="editModal<?= $item['user_id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">Edit User: <?= esc($item['username']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('admin/user/update/' . $item['user_id']) ?>"
                                                method="POST">
                                                <?= csrf_field() ?>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label>Username <span class="text-danger">*</span></label>
                                                        <input type="text" name="username" class="form-control"
                                                            value="<?= old('username', esc($item['username'])) ?>">
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label>Password <small class="text-muted">(Kosongkan jika
                                                                        tidak diubah)</small></label>
                                                                <input type="password" name="password" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label>Konfirmasi Password</label>
                                                                <input type="password" name="confirm_password"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label>Role <span class="text-danger">*</span></label>
                                                        <select name="role" class="form-control">
                                                            <option value="admin" <?= $item['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                                            <option value="gudang" <?= $item['role'] == 'gudang' ? 'selected' : '' ?>>Gudang</option>
                                                            <option value="kasir" <?= $item['role'] == 'kasir' ? 'selected' : '' ?>>Kasir</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#tableUser').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            pageLength: 10,
            order: [[1, 'asc']]
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