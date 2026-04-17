<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- FORM TAMBAH PRODUK -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Produk</h5>
        </div>
        <div class="card-body p-3">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <p class="mb-0"><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/produk/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>SKU <small class="text-muted">(opsional)</small></label>
                            <input type="text" name="sku" class="form-control" value="<?= old('sku') ?>" placeholder="Auto jika kosong">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" class="form-control" value="<?= old('nama_barang') ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Kategori <span class="text-danger">*</span></label>
                            <select name="id_kategori" class="form-control">
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($kategori as $item): ?>
                                    <option value="<?= $item['id'] ?>"><?= esc($item['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Supplier <span class="text-danger">*</span></label>
                            <select name="id_supplier" class="form-control">
                                <option value="">Pilih Supplier</option>
                                <?php foreach ($supplier as $item): ?>
                                    <option value="<?= $item['id'] ?>"><?= esc($item['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label>Harga Beli <span class="text-danger">*</span></label>
                            <input type="number" name="harga_beli" class="form-control" value="<?= old('harga_beli') ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label>Harga Jual <span class="text-danger">*</span></label>
                            <input type="number" name="harga_jual" class="form-control" value="<?= old('harga_jual') ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label>Stok</label>
                            <input type="number" name="stok" class="form-control" value="<?= old('stok', 0) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label>Stok Minimal</label>
                            <input type="number" name="min_stok" class="form-control" value="<?= old('min_stok', 0) ?>">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2"><?= old('keterangan') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Produk
                </button>
            </form>
        </div>
    </div>

    <!-- TABEL DAFTAR PRODUK -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Produk</h5>
        </div>
        <div class="card-body p-3">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tableProduk">
                    <thead class="table-dark">
                        <tr>
                            <th>SKU</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produk as $item): ?>
                            <tr class="<?= $item['stok'] <= $item['min_stok'] ? 'table-warning' : '' ?>">
                                <td><small><?= esc($item['sku']) ?></small></td>
                                <td><?= esc($item['nama_barang']) ?></td>
                                <td><?= esc($item['nama_kategori'] ?? '-') ?></td>
                                <td>Rp <?= number_format($item['harga_jual'], 0, ',', '.') ?></td>
                                <td class="<?= $item['stok'] <= $item['min_stok'] ? 'text-danger fw-bold' : '' ?>">
                                    <?= $item['stok'] ?>
                                    <?php if ($item['stok'] <= $item['min_stok']): ?>
                                        <i class="bi bi-exclamation-triangle" title="Stok menipis!"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editModal<?= $item['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="<?= base_url('admin/produk/delete/' . $item['id']) ?>"
                                        class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Yakin hapus produk <?= esc($item['nama_barang']) ?>?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- MODAL EDIT PRODUK -->
                            <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit Produk: <?= esc($item['nama_barang']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= base_url('admin/produk/update/' . $item['id']) ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label>SKU</label>
                                                            <input type="text" name="sku" class="form-control" value="<?= $item['sku'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label>Nama Barang</label>
                                                            <input type="text" name="nama_barang" class="form-control" value="<?= $item['nama_barang'] ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label>Kategori</label>
                                                            <select name="id_kategori" class="form-control">
                                                                <?php foreach ($kategori as $kat): ?>
                                                                    <option value="<?= $kat['id'] ?>" <?= ($kat['id'] == $item['id_kategori']) ? 'selected' : '' ?>><?= esc($kat['nama']) ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label>Supplier</label>
                                                            <select name="id_supplier" class="form-control">
                                                                <?php foreach ($supplier as $sup): ?>
                                                                    <option value="<?= $sup['id'] ?>" <?= ($sup['id'] == $item['id_supplier']) ? 'selected' : '' ?>><?= esc($sup['nama']) ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label>Harga Beli</label>
                                                            <input type="number" name="harga_beli" class="form-control" value="<?= $item['harga_beli'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label>Harga Jual</label>
                                                            <input type="number" name="harga_jual" class="form-control" value="<?= $item['harga_jual'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label>Stok Minimal</label>
                                                            <input type="number" name="min_stok" class="form-control" value="<?= $item['min_stok'] ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Stok Saat Ini</label>
                                                    <input type="text" class="form-control" value="<?= $item['stok'] ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Keterangan</label>
                                                    <textarea name="keterangan" class="form-control" rows="2"><?= $item['keterangan'] ?></textarea>
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

<script>
    $(document).ready(function() {
        $('#tableProduk').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            pageLength: 10,
            paging: false, // Matikan DataTable pagination karena pakai CI4 pagination
            searching: false,
            ordering: false,
            info: false
        });
    });
</script>
<?= $this->endSection() ?>