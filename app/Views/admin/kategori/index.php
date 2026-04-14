<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- FORM TAMBAH KATEGORI -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Kategori</h5>
            </div>
            <div class="card-body p-3">
                <form action="<?= base_url('admin/kategori/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label>Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nama" 
                               class="form-control <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nama'])) ? 'is-invalid' : '' ?>"
                               value="<?= old('nama') ?>"
                               placeholder="Contoh: Elektronik, Makanan, Minuman"
                               >
                        <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nama'])): ?>
                            <div class="invalid-feedback">
                                <?= session()->getFlashdata('errors')['nama'] ?>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Nama kategori harus unik dan minimal 3 karakter</small>
                    </div>
                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" 
                                  class="form-control <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['deskripsi'])) ? 'is-invalid' : '' ?>"
                                  rows="3"
                                  placeholder="Deskripsi kategori (opsional)"><?= old('deskripsi') ?></textarea>
                        <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['deskripsi'])): ?>
                            <div class="invalid-feedback">
                                <?= session()->getFlashdata('errors')['deskripsi'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save"></i> Simpan Kategori
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- TABEL DAFTAR KATEGORI -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Kategori</h5>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tableKategori">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">Nama Kategori</th>
                                <th width="45%">Deskripsi</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($kategori as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= esc($item['nama']) ?></strong></td>
                                    <td><?= esc($item['deskripsi']) ?: '<span class="text-muted">-</span>' ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editModal<?= $item['id'] ?>">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <a href="<?= base_url('admin/kategori/delete/' . $item['id']) ?>"
                                            class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Yakin hapus kategori <?= esc($item['nama']) ?>?\n\nKategori yang memiliki produk TIDAK BISA dihapus!')">
                                            <i class="bi bi-trash"></i> Hapus
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
                                            <form action="<?= base_url('admin/kategori/update/' . $item['id']) ?>"
                                                method="POST">
                                                <?= csrf_field() ?>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label>Nama Kategori <span class="text-danger">*</span></label>
                                                        <input type="text" 
                                                               name="nama" 
                                                               class="form-control"
                                                               value="<?= old('nama', esc($item['nama'])) ?>" 
                                                               >
                                                        <small class="text-muted">Nama kategori harus unik</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Deskripsi</label>
                                                        <textarea name="deskripsi" 
                                                                  class="form-control" 
                                                                  rows="3"><?= old('deskripsi', esc($item['deskripsi'])) ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning">
                                                        <i class="bi bi-save"></i> Update
                                                    </button>
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
        $('#tableKategori').DataTable({
            language: { 
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' 
            },
            pageLength: 10,
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: 3 } // Kolom aksi tidak bisa diurutkan
            ]
        });
    });
</script>
<?= $this->endSection() ?>