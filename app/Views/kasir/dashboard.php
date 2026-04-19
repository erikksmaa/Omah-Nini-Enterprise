<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Card -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white mb-4">
                <div class="card-body">
                    <h4 class="mb-2">Selamat datang, <strong><?= esc(session()->get('username')) ?></strong>!</h4>
                    <p class="mb-0">Dashboard kasir Omah Nini Enterprise. Pantau penjualan dan transaksi di sini.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Hari Ini -->
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0"><i class="bi bi-receipt"></i> Transaksi Hari Ini</h6>
                            <h2 class="mb-0"><?= number_format($jumlah_transaksi_hari_ini) ?></h2>
                            <small>Transaksi</small>
                        </div>
                        <i class="bi bi-cart-check fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0"><i class="bi bi-currency-dollar"></i> Omset Hari Ini</h6>
                            <h3 class="mb-0">Rp <?= number_format($omset_hari_ini, 0, ',', '.') ?></h3>
                        </div>
                        <i class="bi bi-graph-up fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0"><i class="bi bi-clock"></i> Jam Sibuk</h6>
                            <h2 class="mb-0"><?= $jam_sibuk ? date('H:i', strtotime($jam_sibuk->jam . ':00')) : 'Belum ada' ?></h2>
                            <small>Paling banyak transaksi</small>
                        </div>
                        <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Bulan Ini -->
    <div class="row">
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-month"></i> Statistik Bulan Ini</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4 border-end">
                            <h6 class="text-muted">Total Transaksi</h6>
                            <h3><?= number_format($jumlah_transaksi_bulan_ini) ?>x</h3>
                        </div>
                        <div class="col-4 border-end">
                            <h6 class="text-muted">Total Omset</h6>
                            <h3>Rp <?= number_format($omset_bulan_ini, 0, ',', '.') ?></h3>
                        </div>
                        <div class="col-4">
                            <h6 class="text-muted">Rata-rata / Hari</h6>
                            <h3><?= number_format($rata_transaksi_harian) ?>x</h3>
                            <small>Rp <?= number_format($rata_omset_harian, 0, ',', '.') ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card"></i> Metode Pembayaran Hari Ini</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($metode_pembayaran as $item): ?>
                            <div class="col-4 text-center">
                                <div class="p-3 border rounded">
                                    <i class="bi bi-<?= $item['tipe_pembayaran'] == 'tunai' ? 'cash-stack' : ($item['tipe_pembayaran'] == 'transfer' ? 'bank2' : 'qr-code') ?> fs-2"></i>
                                    <h5 class="mt-2 mb-0"><?= strtoupper($item['tipe_pembayaran']) ?></h5>
                                    <h4><?= number_format($item['jumlah']) ?>x</h4>
                                    <small>Rp <?= number_format($item['total'], 0, ',', '.') ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($metode_pembayaran)): ?>
                            <div class="col-12 text-center">Belum ada transaksi hari ini</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-8 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Penjualan 7 Hari Terakhir</h5>
                </div>
                <div class="card-body">
                    <canvas id="weeklySalesChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Metode Pembayaran</h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Produk Terlaris -->
    <div class="row">
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-trophy"></i> Top 5 Produk Terlaris Hari Ini</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Terjual</th>
                                    <th class="text-end">Omset</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produk_terlaris_hari_ini as $item): ?>
                                    <tr>
                                        <td><?= $item['nama_produk'] ?></td>
                                        <td class="text-end"><?= number_format($item['total_terjual']) ?> pcs</td>
                                        <td class="text-end">Rp <?= number_format($item['total_omset'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($produk_terlaris_hari_ini)): ?>
                                    <tr><td colspan="3" class="text-center">Belum ada data penjualan</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Top 5 Produk Terlaris Bulan Ini</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Terjual</th>
                                    <th class="text-end">Omset</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produk_terlaris_bulan_ini as $item): ?>
                                    <tr>
                                        <td><?= $item['nama_produk'] ?></td>
                                        <td class="text-end"><?= number_format($item['total_terjual']) ?> pcs</td>
                                        <td class="text-end">Rp <?= number_format($item['total_omset'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($produk_terlaris_bulan_ini)): ?>
                                    <tr><td colspan="3" class="text-center">Belum ada data penjualan</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stok Menipis - Card Terpisah -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Stok Menipis</h5>
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
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stok_menipis as $item): ?>
                                    <tr>
                                        <td><?= $item['sku'] ?></td>
                                        <td><?= $item['nama_barang'] ?></td>
                                        <td class="text-danger fw-bold text-end"><?= number_format($item['stok']) ?></td>
                                        <td class="text-end"><?= number_format($item['min_stok']) ?></td>
                                        <td>
                                            <?php if ($item['stok'] <= 0): ?>
                                                <span class="badge bg-danger">HABIS</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">MENIPIS</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($stok_menipis)): ?>
                                    <td><td colspan="5" class="text-center">Semua stok aman</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Terbaru - Card Terpisah Full Width -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Transaksi Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tableTransaksiTerbaru">
                            <thead class="table-dark">
                                <tr>
                                    <th>No Invoice</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Total</th>
                                    <th>Tipe Bayar</th>
                                    <th>Kasir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transaksi_terbaru as $item): ?>
                                    <tr>
                                        <td><strong><?= $item['no_invoice'] ?></strong></td>
                                        <td><?= date('d-m-Y H:i', strtotime($item['created_at'])) ?></td>
                                        <td class="text-end">Rp <?= number_format($item['total_bayar'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $item['tipe_pembayaran'] == 'tunai' ? 'success' : ($item['tipe_pembayaran'] == 'transfer' ? 'info' : 'primary') ?>">
                                                <?= strtoupper($item['tipe_pembayaran']) ?>
                                            </span>
                                        </td>
                                        <td><?= $item['username'] ?? '-' ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info" onclick="showStruk(<?= $item['id'] ?>)">
                                                <i class="bi bi-eye"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($transaksi_terbaru)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada transaksi</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    function showStruk(id) {
        window.open(`<?= base_url('kasir/penjualan/struk/') ?>${id}`, '_blank');
    }
    
    // Weekly Sales Chart
    fetch('<?= base_url("kasir/dashboard/getWeeklySalesChart") ?>')
        .then(response => response.json())
        .then(data => {
            if (data && data.labels && data.values) {
                new Chart(document.getElementById('weeklySalesChart'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Penjualan (Rp)',
                            data: data.values,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + context.raw.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }
        })
        .catch(error => console.error('Weekly Sales Chart Error:', error));
    
    // Payment Method Chart
    fetch('<?= base_url("kasir/dashboard/getPaymentMethodChart") ?>')
        .then(response => response.json())
        .then(data => {
            if (data && data.labels && data.values && data.values.length > 0) {
                new Chart(document.getElementById('paymentChart'), {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${label}: ${value} transaksi (${percent}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                document.getElementById('paymentChart').parentElement.innerHTML = '<div class="alert alert-info">Belum ada data pembayaran hari ini</div>';
            }
        })
        .catch(error => console.error('Payment Chart Error:', error));
</script>

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
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.02);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.04);
    }
</style>
<?= $this->endSection() ?>