<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- FORM TAMBAH KATEGORI -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Kategori</h5>
        </div>
        <div class="card-bod p-3">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <p class="mb-0"><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/kategori/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="<?= old('nama') ?>" placeholder="Contoh: Elektronik, Makanan, Minuman">
                            <small class="text-muted">Nama kategori harus unik dan minimal 3 karakter</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi kategori (opsional)"><?= old('deskripsi') ?></textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Kategori
                </button>
            </form>
        </div>
    </div>

    <!-- TABEL DAFTAR KATEGORI -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Kategori</h5>
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
                            <th width="30%">Nama Kategori</th>
                            <th width="45%">Deskripsi</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = ($pager->getCurrentPage() - 1) * 10 + 1; ?>
                        <?php foreach ($kategori as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= esc($item['nama']) ?></strong></td>
                                <td><?= esc($item['deskripsi']) ?: '<span class="text-muted">-</span>' ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editModal<?= $item['id'] ?>">
                                        <i class="bi bi-pencil"></i> 
                                    </button>
                                    <a href="<?= base_url('admin/kategori/delete/' . $item['id']) ?>"
                                        class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Yakin hapus kategori <?= esc($item['nama']) ?>?')">
                                        <i class="bi bi-trash"></i> 
                                    </a>
                                </td>
                            </tr>

                            <!-- MODAL EDIT KATEGORI -->
                            <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit Kategori: <?= esc($item['nama']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= base_url('admin/kategori/update/' . $item['id']) ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label>Nama Kategori</label>
                                                    <input type="text" name="nama" class="form-control" value="<?= $item['nama'] ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label>Deskripsi</label>
                                                    <textarea name="deskripsi" class="form-control" rows="3"><?= $item['deskripsi'] ?></textarea>
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