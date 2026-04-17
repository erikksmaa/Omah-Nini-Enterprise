<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- FORM TAMBAH SUPPLIER -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-truck"></i> Tambah Supplier</h5>
        </div>
        <div class="card-body p-3">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <p class="mb-0"><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/supplier/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="<?= old('nama') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Kontak <span class="text-danger">*</span></label>
                            <input type="text" name="kontak" class="form-control" value="<?= old('kontak') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?= old('email') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Alamat</label>
                            <input type="text" name="alamat" class="form-control" value="<?= old('alamat') ?>">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Supplier
                </button>
            </form>
        </div>
    </div>

    <!-- TABEL DAFTAR SUPPLIER -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Supplier</h5>
        </div>
        <div class="card-body p-3">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="30%">Nama Supplier</th>
                            <th width="20%">Kontak</th>
                            <th width="25%">Email</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = ($pager->getCurrentPage() - 1) * 10 + 1; ?>
                        <?php foreach ($supplier as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= esc($item['nama']) ?></strong></td>
                                <td><?= esc($item['kontak']) ?></td>
                                <td><?= esc($item['email']) ?? '-' ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editModal<?= $item['id'] ?>">
                                        <i class="bi bi-pencil"></i> 
                                    </button>
                                    <a href="<?= base_url('admin/supplier/delete/' . $item['id']) ?>"
                                        class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Yakin hapus supplier <?= esc($item['nama']) ?>?')">
                                        <i class="bi bi-trash"></i> 
                                    </a>
                                </td>
                            </tr>

                            <!-- MODAL EDIT SUPPLIER -->
                            <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit Supplier: <?= esc($item['nama']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= base_url('admin/supplier/update/' . $item['id']) ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label>Nama Supplier</label>
                                                    <input type="text" name="nama" class="form-control" value="<?= $item['nama'] ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label>Kontak</label>
                                                    <input type="text" name="kontak" class="form-control" value="<?= $item['kontak'] ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label>Email</label>
                                                    <input type="email" name="email" class="form-control" value="<?= $item['email'] ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label>Alamat</label>
                                                    <textarea name="alamat" class="form-control" rows="2"><?= $item['alamat'] ?></textarea>
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
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- PAGINATION -->
            <div class="mt-4">
                <?= $pager->links('default', 'bootstrap_pagination') ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>