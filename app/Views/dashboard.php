<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Card -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h4 class="mb-2">Selamat datang, <strong><?= esc($username) ?></strong>!</h4>
                    <p class="mb-0">Dashboard administrator Omah Nini Enterprise. Berikut ringkasan data sistem Anda.</p>
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
                            <h6 class="mb-0">Total Kategori</h6>
                            <h2 class="mb-0"><?= number_format($total_kategori) ?></h2>
                        </div>
                        <i class="bi bi-tags fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Supplier</h6>
                            <h2 class="mb-0"><?= number_format($total_supplier) ?></h2>
                        </div>
                        <i class="bi bi-truck fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total User</h6>
                            <h2 class="mb-0"><?= number_format($total_user) ?></h2>
                        </div>
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stok Alert Cards -->
    <div class="row">
        <div class="col-xl-6 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Stok Habis</h6>
                            <h2 class="mb-0"><?= number_format($stok_habis) ?></h2>
                            <small>Produk dengan stok 0</small>
                        </div>
                        <i class="bi bi-x-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Stok Menipis</h6>
                            <h2 class="mb-0"><?= number_format($stok_menipis) ?></h2>
                            <small>Produk dengan stok ≤ min stok</small>
                        </div>
                        <i class="bi bi-arrow-down fs-1 opacity-50"></i>
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

    <div class="row">
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Penjualan 12 Bulan Terakhir</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlySalesChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-trophy"></i> Top 5 Produk Terlaris Bulan Ini</h5>
                </div>
                <div class="card-body">
                    <canvas id="topProductsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-currency-dollar"></i> Laba/Rugi Bulan Ini</h5>
                </div>
                <div class="card-body">
                    <canvas id="profitLossChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Daftar Stok Menipis</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="lowStockTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>SKU</th>
                                    <th>Nama Produk</th>
                                    <th>Stok</th>
                                    <th>Min Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Helper function to format Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    // 1. Weekly Sales Chart (Line)
    fetch('<?= base_url("admin/dashboard/getWeeklySalesChart") ?>')
        .then(response => response.json())
        .then(data => {
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
        });

    // 2. Monthly Sales Chart (Bar)
    fetch('<?= base_url("admin/dashboard/getMonthlySalesChart") ?>')
        .then(response => response.json())
        .then(data => {
            new Chart(document.getElementById('monthlySalesChart'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: data.values,
                        backgroundColor: '#007bff',
                        borderRadius: 5
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
        });

    // 3. Payment Method Chart (Pie)
    fetch('<?= base_url("admin/dashboard/getPaymentMethodChart") ?>')
        .then(response => response.json())
        .then(data => {
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
                                    const percent = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} transaksi (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });

    // 4. Top Products Chart (Bar)
    fetch('<?= base_url("admin/dashboard/getTopProductsChart") ?>')
        .then(response => response.json())
        .then(data => {
            new Chart(document.getElementById('topProductsChart'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Jumlah Terjual (pcs)',
                        data: data.values,
                        backgroundColor: '#ffc107',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    indexAxis: 'y'
                }
            });
        });

    // 5. Profit Loss Chart (Doughnut)
    fetch('<?= base_url("admin/dashboard/getProfitLossChart") ?>')
        .then(response => response.json())
        .then(data => {
            new Chart(document.getElementById('profitLossChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Pemasukan', 'Pengeluaran', 'Laba'],
                    datasets: [{
                        data: [data.pemasukan, data.pengeluaran, data.laba],
                        backgroundColor: ['#28a745', '#dc3545', '#17a2b8']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return formatRupiah(context.raw);
                                }
                            }
                        }
                    }
                }
            });
        });

    // 6. Low Stock Table
    fetch('<?= base_url("admin/dashboard/getLowStockData") ?>')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#lowStockTable tbody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada produk dengan stok menipis</td></tr>';
            } else {
                tbody.innerHTML = data.map(item => `
                    <tr>
                        <td>${item.sku}</td>
                        <td><strong>${item.nama_barang}</strong><br><small class="text-muted">${item.kategori || '-'}</small></td>
                        <td class="text-danger fw-bold">${item.stok.toLocaleString()}</td>
                        <td>${item.min_stok.toLocaleString()}</td>
                        <td>
                            <a href="<?= base_url('gudang/stok/opname/') ?>${item.id}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Opname
                            </a>
                        </td>
                    </tr>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.querySelector('#lowStockTable tbody').innerHTML = '<tr><td colspan="5" class="text-center">Gagal memuat data</td></tr>';
        });
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
</style>
<?= $this->endSection() ?>