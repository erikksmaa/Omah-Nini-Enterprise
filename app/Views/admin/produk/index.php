<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- FORM TAMBAH PRODUK -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Produk</h5>
        </div>
        <div class="card-body p-3">
              <?php if (session()->getFlashdata('validation_errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach (session()->getFlashdata('validation_errors') as $error): ?>
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
                            <input type="text" name="sku" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['sku'])) ? 'is-invalid' : '' ?>" value="<?= old('sku') ?>"
                                placeholder="Auto jika kosong">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['nama_barang'])) ? 'is-invalid' : '' ?>" value="<?= old('nama_barang') ?>"
                                >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Kategori <span class="text-danger">*</span></label>
                            <select name="id_kategori" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['id_kategori'])) ? 'is-invalid' : '' ?>" >
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
                            <select name="id_supplier" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['id_supplier'])) ? 'is-invalid' : '' ?>" >
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
                            <input type="text" id="harga_beli" name="harga_beli" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['harga_beli'])) ? 'is-invalid' : '' ?>" value="<?=old('harga_beli')?>"
                                >
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label>Harga Jual <span class="text-danger">*</span></label>
                            <input type="text"  id="harga_jual" name="harga_jual" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['harga_jual'])) ? 'is-invalid' : '' ?>" value="<?=old('harga_jual')?>"
                                >
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label>Stok</label>
                            <input type="number" name="stok" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['stok'])) ? 'is-invalid' : '' ?>" value="<?= old('stok', 0) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label>Stok Minimal</label>
                            <input type="number" name="min_stok" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['min_stok'])) ? 'is-invalid' : '' ?>" value="<?= old('min_stok', 0) ?>">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control <?= (session()->getFlashdata('validation_errors') && isset(session()->getFlashdata('validation_errors')['keterangan'])) ? 'is-invalid' : '' ?>" rows="2"><?= old('keterangan') ?></textarea>
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
            <!-- Filter Form -->
            <form method="GET" class="row mb-3">
                <div class="col-md-3">
                    <input type="text" name="keyword" class="form-control" placeholder="Cari nama/SKU..."
                        value="<?= esc($keyword ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="kategori_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategori as $kat): ?>
                            <option value="<?= $kat['id'] ?>" <?= ($kategori_id ?? '') == $kat['id'] ? 'selected' : '' ?>>
                                <?= esc($kat['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="supplier_id" class="form-select">
                        <option value="">Semua Supplier</option>
                        <?php foreach ($supplier as $sup): ?>
                            <option value="<?= $sup['id'] ?>" <?= ($supplier_id ?? '') == $sup['id'] ? 'selected' : '' ?>>
                                <?= esc($sup['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status_stok" class="form-select">
                        <option value="">Semua Stok</option>
                        <option value="aman" <?= ($status_stok ?? '') == 'aman' ? 'selected' : '' ?>>Stok Aman</option>
                        <option value="menipis" <?= ($status_stok ?? '') == 'menipis' ? 'selected' : '' ?>>Stok Menipis
                        </option>
                        <option value="habis" <?= ($status_stok ?? '') == 'habis' ? 'selected' : '' ?>>Stok Habis</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
                <div class="col-md-1">
                    <?php if (!empty($keyword) || !empty($kategori_id) || !empty($supplier_id) || !empty($status_stok)): ?>
                        <a href="<?= base_url('admin/produk') ?>" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-repeat"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>SKU</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Supplier</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($produk) && is_array($produk)): ?>
                            <?php foreach ($produk as $item): ?>
                                <tr class="<?= $item['stok'] <= $item['min_stok'] ? 'table-warning' : '' ?>">
                                    <td><small><?= esc($item['sku']) ?></small></td>
                                    <td><?= esc($item['nama_barang']) ?></td>
                                    <td><?= esc($item['nama_kategori'] ?? '-') ?></td>
                                    <td><?= esc($item['nama_supplier'] ?? '-') ?></td>
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
                                        <a href="#" class="btn btn-sm btn-danger"
                                            onclick="return confirmDelete('<?= base_url('admin/produk/delete/' . $item['id']) ?>', '<?= esc($item['nama_barang']) ?>', 'Produk')">
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
                                                                <input type="text" name="sku" class="form-control"
                                                                    value="<?= old('sku', esc($item['sku'])) ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label>Nama Barang</label>
                                                                <input type="text" name="nama_barang" class="form-control"
                                                                    value="<?= old('nama_barang', esc($item['nama_barang'])) ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label>Kategori</label>
                                                                <select name="id_kategori" class="form-control">
                                                                    <?php foreach ($kategori as $kat): ?>
                                                                        <option value="<?= $kat['id'] ?>"
                                                                            <?= ($kat['id'] == $item['id_kategori']) ? 'selected' : '' ?>><?= esc($kat['nama']) ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label>Supplier</label>
                                                                <select name="id_supplier" class="form-control">
                                                                    <?php foreach ($supplier as $sup): ?>
                                                                        <option value="<?= $sup['id'] ?>"
                                                                            <?= ($sup['id'] == $item['id_supplier']) ? 'selected' : '' ?>><?= esc($sup['nama']) ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label>Harga Beli</label>
                                                                <input type="text" id="harga_beli" name="harga_beli" class="form-control"
                                                                    value="<?= old('harga_beli', $item['harga_beli']) ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label>Harga Jual</label>
                                                                <input type="text" id="harga_jual" name="harga_jual" class="form-control "
                                                                    value="<?= old('harga_jual', $item['harga_jual']) ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label>Stok Minimal</label>
                                                                <input type="number" name="min_stok" class="form-control"
                                                                    value="<?= old('min_stok', $item['min_stok']) ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Stok Saat Ini</label>
                                                        <input type="text" class="form-control" value="<?= $item['stok'] ?>"
                                                            disabled>
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
                                                    <button type="submit" class="btn btn-warning">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    <?php
                                    if (!empty($keyword) || !empty($kategori_id) || !empty($supplier_id) || !empty($status_stok)) {
                                        echo 'Tidak ada produk yang sesuai dengan filter';
                                    } else {
                                        echo 'Belum ada data produk';
                                    }
                                    ?>
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
                    Menampilkan <?= count($produk) ?> dari <?= $pager->getTotal() ?> data
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
    // Fungsi untuk apply mask
    function applyMask() {
        $('#harga_beli, #harga_jual').mask("#.##0", {reverse: true});
    }
    
    $(document).ready(function(){
        // Apply pertama kali
        applyMask();
    });
    
    // Setiap modal ditampilkan, apply ulang
    $(document).on('shown.bs.modal', function() {
        applyMask();
    });
    
    // Khusus untuk modal edit (karena ID-nya dinamis)
    $(document).on('shown.bs.modal', '[id^="editModal"]', function() {
        applyMask();
    });
</script>

<?= $this->endSection() ?>