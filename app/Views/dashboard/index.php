<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-speedometer2"></i> Dashboard</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Selamat datang, <strong><?= esc($username) ?></strong>!</h5>
                        <p class="mb-0">Anda login sebagai: <span class="badge bg-primary"><?= strtoupper($role) ?></span></p>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-box"></i> Total Produk</h5>
                                    <h2 class="mb-0" id="totalProduk">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-tags"></i> Total Kategori</h5>
                                    <h2 class="mb-0" id="totalKategori">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-truck"></i> Total Supplier</h5>
                                    <h2 class="mb-0" id="totalSupplier">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-info mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-people"></i> Total User</h5>
                                    <h2 class="mb-0" id="totalUser">0</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Sistem</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td width="40%">Nama User</td>
                                            <td>: <strong><?= esc($username) ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Role Akses</td>
                                            <td>: <span class="badge bg-primary"><?= strtoupper($role) ?></span></td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal Login</td>
                                            <td>: <?= date('d-m-Y H:i:s') ?></td>
                                        </tr>
                                        <tr>
                                            <td>IP Address</td>
                                            <td>: <?= $_SERVER['REMOTE_ADDR'] ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="bi bi-question-circle"></i> Menu Cepat</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="<?= base_url('admin/produk') ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-box"></i> Kelola Produk
                                        </a>
                                        <a href="<?= base_url('admin/kategori') ?>" class="btn btn-outline-success">
                                            <i class="bi bi-tags"></i> Kelola Kategori
                                        </a>
                                        <a href="<?= base_url('admin/supplier') ?>" class="btn btn-outline-warning">
                                            <i class="bi bi-truck"></i> Kelola Supplier
                                        </a>
                                        <?php if (session()->get('role') == 'admin'): ?>
                                        <a href="<?= base_url('admin/user') ?>" class="btn btn-outline-info">
                                            <i class="bi bi-people"></i> Kelola User
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Ambil data total dari database (opsional)
    fetch('<?= base_url("api/totals") ?>')
        .then(response => response.json())
        .then(data => {
            if(data.produk) document.getElementById('totalProduk').innerText = data.produk;
            if(data.kategori) document.getElementById('totalKategori').innerText = data.kategori;
            if(data.supplier) document.getElementById('totalSupplier').innerText = data.supplier;
            if(data.user) document.getElementById('totalUser').innerText = data.user;
        })
        .catch(error => console.log('API tidak tersedia'));
</script>
<?= $this->endSection() ?>