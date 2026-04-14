<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- FORM TAMBAH PRODUK -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Produk</h5>
            </div>
            <div class="card-body p-3" style="max-height: 600px; overflow-y: auto;">
                <form action="<?= base_url('admin/produk/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>SKU <small class="text-muted">(opsional)</small></label>
                                <input type="text" 
                                       name="sku" 
                                       class="form-control <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['sku'])) ? 'is-invalid' : '' ?>"
                                       value="<?= old('sku') ?>"
                                       placeholder="Auto jika kosong">
                                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['sku'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['sku'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="nama_barang" 
                                       class="form-control <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nama_barang'])) ? 'is-invalid' : '' ?>"
                                       value="<?= old('nama_barang') ?>"
                                       >
                                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nama_barang'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['nama_barang'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Kategori <span class="text-danger">*</span></label>
                                <select name="id_kategori" 
                                        class="form-control <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['id_kategori'])) ? 'is-invalid' : '' ?>"
                                        >
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($kategori as $item): ?>
                                        <option value="<?= $item['id'] ?>" <?= old('id_kategori') == $item['id'] ? 'selected' : '' ?>>
                                            <?= esc($item['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['id_kategori'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['id_kategori'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Supplier <span class="text-danger">*</span></label>
                                <select name="id_supplier" 
                                        class="form-control <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['id_supplier'])) ? 'is-invalid' : '' ?>"
                                        >
                                    <option value="">Pilih Supplier</option>
                                    <?php foreach ($supplier as $item): ?>
                                        <option value="<?= $item['id'] ?>" <?= old('id_supplier') == $item['id'] ? 'selected' : '' ?>>
                                            <?= esc($item['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['id_supplier'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['id_supplier'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Harga Beli <span class="text-danger">*</span></label>
                                <input type="number" 
                                    name="harga_beli" 
                                    class="form-control <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['harga_beli'])) ? 'is-invalid' : '' ?>"
                                    value="<?= old('harga_beli') ?>"
                                    >
                                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['harga_beli'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['harga_beli'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Harga Jual <span class="text-danger">*</span></label>
                                <input type="number" 
                                       name="harga_jual" 
                                       class="form-control <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['harga_jual'])) ? 'is-invalid' : '' ?>"
                                       value="<?= old('harga_jual') ?>"
                                       >
                                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['harga_jual'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['harga_jual'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Stok Minimal</label>
                                <input type="number" 
                                       name="min_stok" 
                                       class="form-control <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['min_stok'])) ? 'is-invalid' : '' ?>"
                                       value="<?= old('min_stok', 0) ?>">
                                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['min_stok'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['min_stok'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Stok</label>
                                <input type="number" 
                                       name="stok" 
                                       class="form-control <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['stok'])) ? 'is-invalid' : '' ?>"
                                       value="<?= old('stok', 0) ?>">
                                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['stok'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['stok'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"><?= old('keterangan') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save"></i> Simpan Produk
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- TABEL DAFTAR PRODUK -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Produk</h5>
            </div>
            <div class="card-body p-3" style="max-height: 600px; overflow-y: auto;">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm" id="tableProduk">
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

                                <!-- MODAL EDIT PRODUK dengan Validasi -->
                                <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">Edit Produk: <?= esc($item['nama_barang']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('admin/produk/update/' . $item['id']) ?>"
                                                method="POST">
                                                <?= csrf_field() ?>
                                                <div class="modal-body">
                                                    <!-- Error khusus untuk edit modal -->
                                                    <?php if (session()->getFlashdata('errors') && old('_ci_validation') == $item['id']): ?>
                                                        <div class="alert alert-danger">
                                                            <strong>Validasi Gagal!</strong>
                                                            <ul class="mb-0 mt-2">
                                                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                                                    <li><?= esc($error) ?></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label>SKU <span class="text-danger">*</span></label>
                                                                <input type="text" 
                                                                       name="sku" 
                                                                       class="form-control"
                                                                       value="<?= old('sku', esc($item['sku'])) ?>" 
                                                                       >
                                                                <small class="text-muted">Kode unik untuk produk</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label>Nama Barang <span class="text-danger">*</span></label>
                                                                <input type="text" 
                                                                       name="nama_barang" 
                                                                       class="form-control"
                                                                       value="<?= old('nama_barang', esc($item['nama_barang'])) ?>" 
                                                                       >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label>Kategori <span class="text-danger">*</span></label>
                                                                <select name="id_kategori" class="form-control" >
                                                                    <?php foreach ($kategori as $kat): ?>
                                                                        <option value="<?= $kat['id'] ?>"
                                                                            <?= (old('id_kategori', $item['id_kategori']) == $kat['id']) ? 'selected' : '' ?>>
                                                                            <?= esc($kat['nama']) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label>Supplier <span class="text-danger">*</span></label>
                                                                <select name="id_supplier" class="form-control" >
                                                                    <?php foreach ($supplier as $sup): ?>
                                                                        <option value="<?= $sup['id'] ?>"
                                                                            <?= (old('id_supplier', $item['id_supplier']) == $sup['id']) ? 'selected' : '' ?>>
                                                                            <?= esc($sup['nama']) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label>Harga Beli <span class="text-danger">*</span></label>
                                                                <input type="number" 
                                                                       name="harga_beli" 
                                                                       class="form-control"
                                                                       value="<?= old('harga_beli', $item['harga_beli']) ?>" 
                                                                       >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label>Harga Jual <span class="text-danger">*</span></label>
                                                                <input type="number" 
                                                                       name="harga_jual" 
                                                                       class="form-control"
                                                                       value="<?= old('harga_jual', $item['harga_jual']) ?>" 
                                                                       >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label>Stok Minimal</label>
                                                                <input type="number" 
                                                                       name="min_stok" 
                                                                       class="form-control"
                                                                       value="<?= old('min_stok', $item['min_stok']) ?>">
                                                                <small class="text-muted">Peringatan jika stok di bawah ini</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Stok Saat Ini</label>
                                                        <input type="text" class="form-control" value="<?= $item['stok'] ?>"
                                                            disabled>
                                                        <small class="text-muted">Stok tidak bisa diubah dari sini, gunakan fitur transaksi</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Keterangan</label>
                                                        <textarea name="keterangan" class="form-control"
                                                            rows="2"><?= old('keterangan', esc($item['keterangan'])) ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
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
        $('#tableProduk').DataTable({
            language: { 
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' 
            },
            pageLength: 10,
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: 5 } // Kolom aksi tidak bisa diurutkan
            ]
        });
    });
</script>

<style>
    /* Style untuk stok menipis */
    .table-warning {
        background-color: #fff3cd !important;
    }
    
    /* Animasi fade untuk alert */
    .alert {
        animation: fadeIn 0.5s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<?= $this->endSection() ?>