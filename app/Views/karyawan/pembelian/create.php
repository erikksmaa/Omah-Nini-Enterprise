<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4>Tambah Barang Masuk</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('karyawan/pembelian/store') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Header -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">No. Invoice</label>
                    <input type="text" name="no_invoice" class="form-control" value="<?= old('no_invoice', $no_invoice) ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <select name="id_supplier" class="form-select" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= $sup['id'] ?>" <?= old('id_supplier') == $sup['id'] ? 'selected' : '' ?>>
                                <?= esc($sup['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pembelian</label>
                    <input type="date" name="tanggal_pembelian" class="form-control" value="<?= old('tanggal_pembelian', date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2"><?= old('catatan') ?></textarea>
                </div>
            </div>

            <hr>
            <h5>Item Produk</h5>
            <div id="items-container">
                <!-- baris item ditambahkan oleh JS -->
            </div>

            <button type="button" id="btnAddItem" class="btn btn-outline-primary mb-3">
                <i class="bi bi-plus"></i> Tambah Produk
            </button>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i> Simpan Barang Masuk
                </button>
                <a href="<?= base_url('karyawan/pembelian') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<!-- Template Baris Item (dengan Select2) -->
<template id="itemTemplate">
    <div class="row item-row mb-2 align-items-end">
        <div class="col-md-8">
            <label class="form-label">Produk</label>
            <select name="items[INDEX][id_produk]" class="form-select produk-select" style="width: 100%;" required>
                <option value="">-- Cari Produk (SKU / Nama) --</option>
                <?php foreach ($produk_list as $prod): ?>
                    <option value="<?= $prod['id'] ?>">
                        <?= esc($prod['sku'] . ' - ' . $prod['nama_motif'] . ' ' . $prod['nama_warna']) ?> (Stok: <?= $prod['stok'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Jumlah</label>
            <input type="number" name="items[INDEX][jumlah]" class="form-control jumlah" placeholder="Jumlah" min="1" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-remove-item"><i class="bi bi-trash"></i></button>
        </div>
    </div>
</template>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Select2 CSS & JS (CDN) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('items-container');
        const template = document.getElementById('itemTemplate');
        let itemIndex = 0;

        // Fungsi inisialisasi Select2 pada elemen tertentu
        function initSelect2(element) {
            $(element).select2({
                placeholder: "-- Cari Produk (SKU / Nama) --",
                allowClear: true,
                width: 'resolve', // atau '100%'
            });
        }

        function addItem() {
            const clone = template.content.cloneNode(true);
            const row = clone.querySelector('.item-row');

            // Ganti placeholder INDEX
            row.innerHTML = row.innerHTML.replace(/INDEX/g, itemIndex);

            // Dapatkan select yang baru
            const select = row.querySelector('.produk-select');

            // Tambahkan ke container
            container.appendChild(row);

            // Inisialisasi Select2 pada select yang baru
            initSelect2(select);

            // Event hapus baris
            row.querySelector('.btn-remove-item').addEventListener('click', function() {
                // Hancurkan Select2 sebelum menghapus elemen
                $(select).select2('destroy');
                row.remove();
            });

            itemIndex++;
        }

        document.getElementById('btnAddItem').addEventListener('click', addItem);

        // Tambah satu baris pertama kali
        if (container.children.length === 0) {
            addItem();
        }
    });
</script>
<?= $this->endSection() ?>