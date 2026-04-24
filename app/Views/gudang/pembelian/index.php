<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-cart-plus"></i> Data Pembelian Barang</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="<?= base_url('gudang/pembelian/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Pembelian Baru
                </a>
            </div>

            <!-- Filter Form -->
            <form method="GET" class="row mb-3">
                <div class="col-md-3">
                    <input type="text" name="keyword" class="form-control" placeholder="Cari No Invoice..."
                        value="<?= esc($keyword ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="supplier_id" class="form-select">
                        <option value="">Semua Supplier</option>
                        <?php foreach ($supplier_list as $sup): ?>
                            <option value="<?= $sup['id'] ?>" <?= ($supplier_id ?? '') == $sup['id'] ? 'selected' : '' ?>>
                                <?= esc($sup['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="start_date" class="form-control" placeholder="Start Date"
                        value="<?= esc($start_date ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" name="end_date" class="form-control" placeholder="End Date"
                        value="<?= esc($end_date ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
                <div class="col-md-1">
                    <?php if (!empty($keyword) || !empty($supplier_id) || !empty($start_date) || !empty($end_date)): ?>
                        <a href="<?= base_url('gudang/pembelian') ?>" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-repeat"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped" id="tablePembelian">
                    <thead class="table-dark">
                        <tr>
                            <th>No Invoice</th>
                            <th>Supplier</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pembelian) && is_array($pembelian)): ?>
                            <?php foreach ($pembelian as $item): ?>
                                <tr>
                                    <td><strong><?= esc($item['no_invoice']) ?></strong></td>
                                    <td><?= esc($item['supplier_nama']) ?></td>
                                    <td><?= date('d-m-Y', strtotime($item['tanggal_pembelian'])) ?></td>
                                    <td>Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                                    <td><?= esc($item['catatan']) ?? '-' ?></td>
                                    <td>
                                        <a href="<?= base_url('gudang/pembelian/detail/' . $item['id']) ?>"
                                            class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <?php
                                    if (!empty($keyword) || !empty($supplier_id) || !empty($start_date) || !empty($end_date)) {
                                        echo 'Tidak ada data pembelian yang sesuai dengan filter';
                                    } else {
                                        echo 'Belum ada data pembelian';
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
                    Menampilkan <?= count($pembelian) ?> dari <?= $pager->getTotal() ?> data
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#tablePembelian').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            paging: false,
            searching: false,
            ordering: false,
            info: false
        });
    });
</script>
<?= $this->endSection() ?>