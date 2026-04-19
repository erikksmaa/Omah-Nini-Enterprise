<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Card -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white mb-4">
                <div class="card-body">
                    <h4 class="mb-2">Selamat datang, <strong><?= esc(session()->get('username')) ?></strong>!</h4>
                    <p class="mb-0">Dashboard gudang Omah Nini Enterprise. Pantau stok dan pembelian barang di sini.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Produk</h6>
                            <h2 class="mb-0"><?= number_format($total_produk) ?></h2>
                        </div>
                        <i class="bi bi-box fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Nilai Stok</h6>
                            <h4 class="mb-0">Rp <?= number_format($total_nilai_stok, 0, ',', '.') ?></h4>
                        </div>
                        <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Stok Menipis</h6>
                            <h2 class="mb-0"><?= number_format($stok_menipis) ?></h2>
                            <small>Perlu restock</small>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Stok Habis</h6>
                            <h2 class="mb-0"><?= number_format($stok_habis) ?></h2>
                            <small>Segera restock!</small>
                        </div>
                        <i class="bi bi-x-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pembelian & Statistik -->
    <div class="row">
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-cart-plus"></i> Pembelian Bulan Ini</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <h6 class="text-muted">Jumlah Pembelian</h6>
                            <h3><?= number_format($jumlah_pembelian_bulan_ini) ?>x</h3>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted">Total Pengeluaran</h6>
                            <h3>Rp <?= number_format($total_pembelian_bulan_ini, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-truck"></i> Top 5 Supplier</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th class="text-end">Pembelian</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($statistik_supplier as $item): ?>
                                    <tr>
                                        <td><?= $item['nama'] ?? '-' ?></td>
                                        <td class="text-end"><?= number_format($item['jumlah_pembelian']) ?>x</td>
                                        <td class="text-end">Rp <?= number_format($item['total_pembelian'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($statistik_supplier)): ?>
                                    <tr><td colspan="3" class="text-center">Belum ada data pembelian</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Produk Stok Menipis & Pembelian Terbaru -->
    <div class="row">
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Produk Stok Menipis</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>SKU</th>
                                    <th>Nama Produk</th>
                                    <th class="text-end">Stok</th>
                                    <th class="text-end">Min Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produk_menipis as $item): ?>
                                    <tr>
                                        <td><?= $item['sku'] ?></td>
                                        <td><?= $item['nama_barang'] ?></td>
                                        <td class="text-danger fw-bold text-end"><?= number_format($item['stok']) ?></td>
                                        <td class="text-end"><?= number_format($item['min_stok']) ?></td>
                                        <td>
                                            <a href="<?= base_url('gudang/stok/opname/' . $item['id']) ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($produk_menipis)): ?>
                                    <td><td colspan="5" class="text-center">Semua stok aman</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Pembelian Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>No Invoice</th>
                                    <th>Supplier</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pembelian_terbaru as $item): ?>
                                    <tr>
                                        <td><?= $item['no_invoice'] ?></td>
                                        <td><?= $item['supplier_nama'] ?? '-' ?></td>
                                        <td><?= date('d-m-Y', strtotime($item['tanggal_pembelian'])) ?></td>
                                        <td class="text-end">Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($pembelian_terbaru)): ?>
                                    <td><td colspan="4" class="text-center">Belum ada data pembelian</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Produk Stok Terbanyak & Aktivitas Terbaru -->
    <div class="row">
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-arrow-up"></i> Produk Stok Terbanyak</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>SKU</th>
                                    <th>Nama Produk</th>
                                    <th class="text-end">Stok</th>
                                    <th class="text-end">Nilai Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produk_terbanyak as $item): ?>
                                    <tr>
                                        <td><?= $item['sku'] ?></td>
                                        <td><?= $item['nama_barang'] ?></td>
                                        <td class="text-end"><?= number_format($item['stok']) ?></td>
                                        <td class="text-end">Rp <?= number_format($item['stok'] * $item['harga_beli'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($produk_terbanyak)): ?>
                                    <td><td colspan="4" class="text-center">Belum ada data produk</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-activity"></i> Aktivitas Stok Terbaru</h5>
                </div>
                <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                    <?php foreach ($log_terbaru as $log): ?>
                        <div class="mb-3 pb-2 border-bottom">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted"><?= date('d-m-Y H:i', strtotime($log['created_at'])) ?></small>
                                <small><?= $log['username'] ?? 'System' ?></small>
                            </div>
                            <div>
                                <strong><?= $log['nama_barang'] ?></strong>
                                <br>
                                <small>
                                    <?php 
                                    $badge = ($log['jumlah_perubahan'] ?? 0) > 0 ? 'success' : 'danger';
                                    $sign = ($log['jumlah_perubahan'] ?? 0) > 0 ? '+' : '';
                                    ?>
                                    <span class="badge bg-<?= $badge ?>">
                                        <?= $sign . number_format($log['jumlah_perubahan'] ?? 0) ?>
                                    </span>
                                    <?= $log['aktivitas'] ?? '-' ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($log_terbaru)): ?>
                        <p class="text-center text-muted">Belum ada aktivitas</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .fs-1 {
        font-size: 2.5rem;
    }
    .opacity-50 {
        opacity: 0.5;
    }
</style>
<?= $this->endSection() ?>