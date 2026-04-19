<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Laporan Retur Penjualan</h5>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-success w-100" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="<?= base_url('admin/retur/export-excel?start_date=' . $start_date . '&end_date=' . $end_date) ?>" class="btn btn-info w-100">
                        <i class="bi bi-file-excel"></i> Export Excel
                    </a>
                </div>
            </form>

            <!-- Statistik Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h6 class="card-title">Total Retur</h6>
                            <h2 class="mb-0"><?= number_format($total_retur) ?></h2>
                            <small>Transaksi retur</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <h6 class="card-title">Total Nominal Retur</h6>
                            <h3 class="mb-0">Rp <?= number_format($total_nominal_retur, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h6 class="card-title">Rata-rata Retur</h6>
                            <h3 class="mb-0">Rp <?= number_format($rata_retur, 0, ',', '.') ?></h3>
                            <small>Per transaksi retur</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Retur per Bulan</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="returPerBulanChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="bi bi-pie-chart"></i> Top 5 Alasan Retur</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="alasanReturChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Produk Diretur -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="bi bi-trophy"></i> Top 10 Produk Paling Sering Diretur</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th class="text-end">Jumlah Diretur</th>
                                    <th class="text-end">Total Nominal</th>
                                    <th>Kontribusi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalAll = array_sum(array_column($top_produk_diretur, 'total_nominal'));
                                $no = 1;
                                foreach ($top_produk_diretur as $item): 
                                    $persen = $totalAll > 0 ? ($item['total_nominal'] / $totalAll) * 100 : 0;
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><strong><?= $item['nama_produk'] ?></strong></td>
                                        <td class="text-end"><?= number_format($item['total_jumlah']) ?> pcs</td>
                                        <td class="text-end">Rp <?= number_format($item['total_nominal'], 0, ',', '.') ?></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-danger" style="width: <?= $persen ?>%">
                                                    <?= number_format($persen, 1) ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($top_produk_diretur)): ?>
                                    <tr><td colspan="5" class="text-center">Belum ada data retur</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Detail Retur -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-table"></i> Detail Retur Penjualan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tableRetur">
                            <thead class="table-dark">
                                <tr>
                                    <th>No Retur</th>
                                    <th>No Invoice</th>
                                    <th>Tanggal Retur</th>
                                    <th class="text-end">Total Retur</th>
                                    <th>Alasan</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($retur as $item): ?>
                                    <tr>
                                        <td><strong><?= $item['no_retur'] ?></strong></td>
                                        <td><?= $item['no_invoice'] ?></td>
                                        <td><?= date('d-m-Y', strtotime($item['tanggal_retur'])) ?></td>
                                        <td class="text-end text-danger">Rp <?= number_format($item['total_retur'], 0, ',', '.') ?></td>
                                        <td><?= $item['alasan'] ?></td>
                                        <td><?= $item['username'] ?? '-' ?></td>
                                        <td>
                                            <a href="<?= base_url('admin/retur/detail/' . $item['id']) ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                         </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($retur)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada data retur</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">GRAND TOTAL</td>
                                    <td class="text-end fw-bold">Rp <?= number_format($total_nominal_retur, 0, ',', '.') ?></td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Chart Retur per Bulan
    const returPerBulanData = <?= json_encode($retur_per_bulan) ?>;
    if (returPerBulanData.length > 0) {
        const labels = returPerBulanData.map(item => {
            const [tahun, bulan] = item.bulan.split('-');
            const bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${bulanNames[parseInt(bulan)-1]} ${tahun}`;
        }).reverse();
        const values = returPerBulanData.map(item => parseInt(item.total)).reverse();
        
        new Chart(document.getElementById('returPerBulanChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nominal Retur (Rp)',
                    data: values,
                    backgroundColor: '#ef4444',
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
    } else {
        document.getElementById('returPerBulanChart').parentElement.innerHTML = '<div class="alert alert-info">Belum ada data retur</div>';
    }
    
    // Chart Alasan Retur
    const alasanData = <?= json_encode($alasan_terbanyak) ?>;
    if (alasanData.length > 0) {
        const labels = alasanData.map(item => item.alasan.length > 20 ? item.alasan.substring(0, 20) + '...' : item.alasan);
        const values = alasanData.map(item => item.jumlah);
        
        new Chart(document.getElementById('alasanReturChart'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#ef4444', '#f59e0b', #10b981', '#3b82f6', '#8b5cf6']
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
                                return `${label}: ${value} retur (${percent}%)`;
                            }
                        }
                    }
                }
            }
        });
    } else {
        document.getElementById('alasanReturChart').parentElement.innerHTML = '<div class="alert alert-info">Belum ada data retur</div>';
    }
    
    $(document).ready(function() {
        $('#tableRetur').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[2, 'desc']],
            pageLength: 25
        });
    });
</script>

<style media="print">
    .btn, .sidebar, nav, footer {
        display: none !important;
    }
    .card {
        border: none !important;
    }
    .progress {
        display: none !important;
    }
</style>
<?= $this->endSection() ?>