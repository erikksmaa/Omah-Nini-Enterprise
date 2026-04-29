<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- FORM TAMBAH SUPPLIER -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-truck"></i> Tambah Supplier</h5>
        </div>
        <div class="card-body p-3">
            <?php if (session()->getFlashdata('validation_errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach (session()->getFlashdata('validation_errors') as $error): ?>
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
                            <input type="text" name="nama" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['nama'])) ? 'is-invalid' : '' ?>" value="<?= old('nama') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Kontak <span class="text-danger">*</span></label>
                            <input type="text" name="kontak" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['kontak'])) ? 'is-invalid' : '' ?>" value="<?= old('kontak') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['email'])) ? 'is-invalid' : '' ?>" value="<?= old('email') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Alamat</label>
                            <input type="text" name="alamat" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['alamat'])) ? 'is-invalid' : '' ?>" value="<?= old('alamat') ?>">
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
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Kategori</h5>
            <!-- Form Search -->
            <form method="GET" class="d-flex">
                <input type="text" name="keyword" class="form-control form-control-sm me-2" style="width: 250px;"
                    placeholder="Cari nama/kontak/email..." value="<?= esc($keyword ?? '') ?>">
                <button type="submit" class="btn btn-light btn-sm">
                    <i class="bi bi-search"></i>
                </button>
                <?php if (!empty($keyword)): ?>
                    <a href="<?= base_url('admin/kategori') ?>" class="btn btn-outline-light btn-sm ms-2">
                        <i class="bi bi-x-circle"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Supplier</th>
                            <th width="15%">Kontak</th>
                            <th width="20%">Email</th>
                            <th width="20%">Alamat</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($supplier) && is_array($supplier)): ?>
                            <?php
                            $currentPage = $pager->getCurrentPage();
                            $perPage = 10;
                            $no = ($currentPage - 1) * $perPage + 1;
                            ?>
                            <?php foreach ($supplier as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= esc($item['nama']) ?></strong></td>
                                    <td><?= esc($item['kontak']) ?></td>
                                    <td><?= esc($item['email']) ?: '-' ?></td>
                                    <td><?= esc($item['alamat']) ?: '-' ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editModal<?= $item['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="#" class="btn btn-sm btn-danger"
                                            onclick="return confirmDelete('<?= base_url('admin/supplier/delete/' . $item['id']) ?>', '<?= esc($item['nama']) ?>', 'Supplier')">
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
                                            <form action="<?= base_url('admin/supplier/update/' . $item['id']) ?>"
                                                method="POST">
                                                <?= csrf_field() ?>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label>Nama Supplier <span class="text-danger">*</span></label>
                                                        <input type="text" name="nama" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['nama'])) ? 'is-invalid' : '' ?>"
                                                            value="<?= old('nama', esc($item['nama'])) ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Kontak <span class="text-danger">*</span></label>
                                                        <input type="text" name="kontak" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['kontak'])) ? 'is-invalid' : '' ?>"
                                                            value="<?= old('kontak', esc($item['kontak'])) ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Email</label>
                                                        <input type="email" name="email" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['email'])) ? 'is-invalid' : '' ?>"
                                                            value="<?= old('email', esc($item['email'])) ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Alamat</label>
                                                        <textarea name="alamat" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['alamat'])) ? 'is-invalid' : '' ?>"
                                                            rows="2"><?= old('alamat', esc($item['alamat'])) ?></textarea>
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
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <?= !empty($keyword) ? 'Supplier "' . esc($keyword) . '" tidak ditemukan' : 'Belum ada data supplier' ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($pager) && $pager && $pager->getTotal() > 0): ?>
                <div class="mt-4 d-flex justify-content-center">
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
                <div class="text-center text-muted small mt-2">
                    Menampilkan <?= count($supplier) ?> dari <?= $pager->getTotal() ?> data
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>