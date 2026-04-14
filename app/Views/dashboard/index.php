<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row">
    <div class="col-12">
        <div id="standar-harga-jual-status" class="alert" style="display:none;"></div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Ringkasan Keuangan</h4>
                <div class="d-flex gap-2">
                    <select id="jenisBarangFilter" class="form-select form-select-sm">
                        <option value="">Semua Jenis</option>
                        <?php if (!empty($jenisBarangOptions)): ?>
                            <?php foreach($jenisBarangOptions as $jenis): ?>
                                <option value="<?= $jenis ?>"><?= $jenis ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <select id="chartTypeFilter" class="form-select form-select-sm">
                        <option value="margine_bersih">Margin Bersih</option>
                        <option value="margine">Margin Kootor</option>
                        <option value="biaya">Biaya</option>
                    </select>
                    <select id="summaryPeriodFilter" class="form-select form-select-sm">
                        <option value="monthly">Bulanan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <canvas id="financialLineChart" width="100%" height="40"></canvas>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Penjualan Harian</h4>
                <div class="d-flex gap-2">
                    <select id="tipeBarangFilter" class="form-select form-select-sm">
                        <option value="">Semua Tipe</option>
                    </select>
                    <input type="date" id="startDateFilter" class="form-control form-control-sm">
                    <input type="date" id="endDateFilter" class="form-control form-control-sm">
                </div>
            </div>
            <div class="card-body">
                <canvas id="dailyBarChart" width="100%" height="40"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Ringkasan Hari Ini</h4>
            </div>
            <div class="card-body">
                <canvas id="dailyPieChart" width="100%" height="100"></canvas>
                <div id="todaySummaryDetails" class="mt-4 p-3 bg-light rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Tunai:</span>
                        <span id="cashAmount" class="text-success">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Transfer:</span>
                        <span id="transferAmount" class="text-primary">0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Piutang:</span>
                        <span id="piutangAmount" class="text-danger">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Fungsi Financial Chart
        function fetchFinancialChartData() {
            const jenisBarang = $('#jenisBarangFilter').val();
            const chartType = $('#chartTypeFilter').val();
            const summaryPeriod = $('#summaryPeriodFilter').val();

            $.post('<?= base_url('dashboard/getChartData') ?>', {
                jenis_barang: jenisBarang,
                chart_type: chartType,
                summary_period: summaryPeriod 
            }, function(data) {
                const ctx = document.getElementById('financialLineChart').getContext('2d');
                
                if (window.financialLineChart instanceof Chart) {
                    window.financialLineChart.destroy();
                }

                window.financialLineChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Grafik Keuangan',
                            data: data.values,
                            borderColor: '#435ebe',
                            backgroundColor: 'rgba(67, 94, 190, 0.2)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: { responsive: true }
                });
            }, 'json');
        }

        // Fungsi Pie Chart
        function fetchDailyPieChartData() {
            $.get('<?= base_url('dashboard/getDailyPieChartData') ?>', function(data) {
                const ctx = document.getElementById('dailyPieChart').getContext('2d');
                
                if (window.dailyPieChart instanceof Chart) {
                    window.dailyPieChart.destroy();
                }

                window.dailyPieChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: ['#5ddab4', '#435ebe', '#ff7976'],
                        }]
                    },
                    options: { responsive: true, cutout: '70%' }
                });

                // Format Rupiah sederhana
                const formatRp = (angka) => 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
                
                $('#cashAmount').text(formatRp(data.values[0]));
                $('#transferAmount').text(formatRp(data.values[1]));
                $('#piutangAmount').text(formatRp(data.values[2]));
            }, 'json');
        }

        // Fungsi Bar Chart
        function loadTipeBarangOptions() {
            $.get('<?= base_url('dashboard/getTipeBarangOptions') ?>', function(data) {
                data.forEach(function(item) {
                    $('#tipeBarangFilter').append(`<option value="${item.id_tipe}">${item.id_tipe}</option>`);
                });
            }, 'json');
        }

        function fetchDailyBarChartData() {
            $.get('<?= base_url('dashboard/getDailyBarChartData') ?>', {
                id_tipe: $('#tipeBarangFilter').val(),
                start_date: $('#startDateFilter').val(),
                end_date: $('#endDateFilter').val()
            }, function(data) {
                const ctx = document.getElementById('dailyBarChart').getContext('2d');
                
                if (window.dailyBarChart instanceof Chart) {
                    window.dailyBarChart.destroy();
                }

                window.dailyBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Kuantitas Terjual',
                                data: data.quantities,
                                backgroundColor: '#56b6f7',
                                yAxisID: 'y'
                            },
                            {
                                label: 'Total Pendapatan',
                                data: data.revenues,
                                backgroundColor: '#435ebe',
                                type: 'line',
                                tension: 0.3,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: { 
                        responsive: true,
                        scales: {
                            y: { type: 'linear', display: true, position: 'left' },
                            y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false } }
                        }
                    }
                });
            }, 'json');
        }

        function checkStandarHargaJualStatus() {
            $.get('<?= base_url('dashboard/checkStandarHargaJualStatus') ?>', function(data) {
                const statusElement = $('#standar-harga-jual-status');
                if (data.is_updated) {
                    statusElement.removeClass('alert-warning alert-danger').addClass('alert-success');
                    statusElement.html('<i class="bi bi-check-circle"></i> Harga Jual sudah diperbarui hari ini (' + data.last_update_date + ')');
                } else {
                    statusElement.removeClass('alert-success alert-danger').addClass('alert-warning');
                    statusElement.html('<i class="bi bi-exclamation-triangle"></i> Harga Jual terakhir diperbarui pada ' + data.last_update_date + ' (' + data.days_since_update + ' hari lalu)');
                }
                statusElement.show();
            });
        }

        // Trigger Event
        $('#jenisBarangFilter, #chartTypeFilter, #summaryPeriodFilter').change(fetchFinancialChartData);
        $('#tipeBarangFilter, #startDateFilter, #endDateFilter').change(fetchDailyBarChartData);

        // Inisialisasi awal
        const today = new Date();
        const sevenDaysAgo = new Date(today);
        sevenDaysAgo.setDate(today.getDate() - 7);
        $('#startDateFilter').val(sevenDaysAgo.toISOString().split('T')[0]);
        $('#endDateFilter').val(today.toISOString().split('T')[0]);

        loadTipeBarangOptions();
        fetchFinancialChartData();
        fetchDailyPieChartData();
        fetchDailyBarChartData();
        checkStandarHargaJualStatus();
    });
</script>

<?= $this->endSection() ?>