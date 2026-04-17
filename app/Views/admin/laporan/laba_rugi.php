<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Laporan Laba/Rugi</h5>
        </div>
        <div class="card-body p-3">
            <!-- Filter Form -->
            <form method="GET" class="row mb-4">
                <div class="col-md-3">
                    <label>Bulan</label>
                    <select name="bulan" class="form-control">
                        <?php foreach ($bulan_list as $key => $val): ?>
                            <option value="<?= $key ?>" <?= ($bulan ?? date('m')) == $key ? 'selected' : '' ?>><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Tahun</label>
                    <select name="tahun" class="form-control">
                        <?php foreach ($tahun_list as $key => $val): ?>
                            <option value="<?= $key ?>" <?= ($tahun ?? date('Y')) == $key ? 'selected' : '' ?>><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-success w-100" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                </div>
            </form>

            <!-- Ringkasan Laba/Rugi -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-success">
                        <div class="card-body p-3">
                            <h6>Total Penjualan</h6>
                            <h4>Rp <?= number_format($total_penjualan ?? 0, 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger">
                        <div class="card-body p-3">
                            <h6>HPP (Harga Pokok Penjualan)</h6>
                            <h4>Rp <?= number_format($hpp ?? 0, 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-warning">
                        <div class="card-body p-3">
                            <h6>Laba Kotor</h6>
                            <h4>Rp <?= number_format($laba_kotor ?? 0, 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-white bg-secondary">
                        <div class="card-body p-3">
                            <h6>Total Pembelian Barang</h6>
                            <h4>Rp <?= number_format($total_pembelian ?? 0, 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white bg-pirmary">
                        <div class="card-body p-3">
                            <h6>Biaya Operasional</h6>
                            <h4>Rp <?= number_format($biaya_operasional ?? 0, 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-white <?= ($laba_bersih ?? 0) >= 0 ? 'bg-primary' : 'bg-danger' ?>">
                        <div class="card-body p-3">
                            <h6>Laba Bersih</h6>
                            <h3>Rp <?= number_format(abs($laba_bersih ?? 0), 0, ',', '.') ?></h3>
                            <small><?= ($laba_bersih ?? 0) >= 0 ? 'UNTUNG' : 'RUGI' ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white bg-dark">
                        <div class="card-body p-3">
                            <h6>Margin Laba</h6>
                            <h3><?= number_format($margin_laba ?? 0, 2) ?>%</h3>
                            <small>Dari total penjualan</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Perhitungan -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Detail Perhitungan Laba/Rugi</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr class="table-success">
                                    <td width="40%"><strong>PENDAPATAN</strong></td>
                                    <td class="text-end"><strong>Rp <?= number_format($total_penjualan ?? 0, 0, ',', '.') ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Total Penjualan (Omset)</td>
                                    <td class="text-end">Rp <?= number_format($total_penjualan ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr class="table-danger">
                                    <td><strong>HARGA POKOK PENJUALAN (HPP)</strong></td>
                                    <td class="text-end"><strong>Rp <?= number_format($hpp ?? 0, 0, ',', '.') ?></strong></td>
                                </tr>
                                <tr>
                                    <td>HPP (Modal barang yang terjual)</td>
                                    <td class="text-end">Rp <?= number_format($hpp ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr class="table-warning">
                                    <td><strong>LABA KOTOR</strong></td>
                                    <td class="text-end"><strong>Rp <?= number_format($laba_kotor ?? 0, 0, ',', '.') ?></strong></td>
                                </tr>
                                <tr class="table-secondary">
                                    <td><strong>BIAYA OPERASIONAL</strong></td>
                                    <td class="text-end"><strong>Rp <?= number_format($biaya_operasional ?? 0, 0, ',', '.') ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Pembelian Barang</td>
                                    <td class="text-end">Rp <?= number_format($total_pembelian ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td>Biaya Operasional Lainnya</td>
                                    <td class="text-end">Rp <?= number_format($biaya_operasional_lain ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>LABA BERSIH</strong></td>
                                    <td class="text-end"><strong>Rp <?= number_format(abs($laba_bersih ?? 0), 0, ',', '.') ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Margin Laba</td>
                                    <td class="text-end"><?= number_format($margin_laba ?? 0, 2) ?>%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>