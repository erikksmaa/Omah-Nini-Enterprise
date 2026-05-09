<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4>Tambah Barang Masuk</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('karyawan/pembelian/store') ?>" method="post" id="form-pembelian">
            <?= csrf_field() ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Merek</label>
                    <select name="id_supplier" id="id_supplier" class="form-select" required>
                        <option value="">-- Pilih Merek --</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= $sup['id'] ?>"><?= esc($sup['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pembelian</label>
                    <input type="date" name="tanggal_pembelian" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <hr>
            <h5>Item Produk</h5>
            <div class="alert alert-info small">
                <i class="bi bi-info-circle"></i> Pilih merek terlebih dahulu, lalu tambahkan produk. Produk yang tampil hanya dari merek tersebut.
            </div>
            <div id="items-container">
                <!-- baris item ditambahkan oleh JS -->
            </div>

            <button type="button" id="btnAddItem" class="btn btn-outline-primary mb-3">
                <i class="bi bi-plus"></i> Tambah Produk
            </button>

            <div class="mt-3">
                <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan Barang Masuk</button>
                <a href="<?= base_url('karyawan/pembelian') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<template id="itemTemplate">
    <div class="row item-row mb-2 align-items-end border-bottom pb-2">
        <div class="col-md-9">
            <label class="form-label">Produk</label>
            <select name="items[INDEX][id_produk]" class="form-select produk-select" style="width: 100%;" required>
                <option value="">-- Pilih Merek dulu --</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Jumlah</label>
            <input type="number" name="items[INDEX][jumlah]" class="form-control jumlah" min="1" value="1" required>
        </div>
        <div class="col-md-12 text-end mt-2">
            <button type="button" class="btn btn-danger btn-remove-item"><i class="bi bi-trash"></i> Hapus</button>
        </div>
    </div>
</template>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const supplierSelect = document.getElementById('id_supplier');
    const container = document.getElementById('items-container');
    const template = document.getElementById('itemTemplate');
    let itemIndex = 0;

    // Ambil produk berdasarkan supplier via API
    function loadProdukBySupplier(supplierId, callback) {
        if (!supplierId) {
            callback([]);
            return;
        }
        fetch(`<?= base_url('api/produk/by-supplier/') ?>${supplierId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    callback(data.data);
                } else {
                    callback([]);
                }
            })
            .catch(err => {
                console.error(err);
                callback([]);
            });
    }

    // Inisialisasi Select2 dengan data dari parameter
    function initSelect2(element, produkList) {
        // Hancurkan instance sebelumnya jika ada
        if ($(element).data('select2')) {
            $(element).select2('destroy');
        }
        $(element).empty().select2({
            placeholder: "-- Cari Produk (Motif - Warna) --",
            allowClear: true,
            width: 'resolve',
            data: produkList.map(p => ({
                id: p.id,
                text: `${p.nama_motif} - ${p.nama_warna} (Stok: ${p.stok})`
            }))
        });
    }

    // Perbarui semua select produk yang sudah ada
    function refreshAllSelects() {
        const supplierId = supplierSelect.value;
        loadProdukBySupplier(supplierId, function(produkList) {
            document.querySelectorAll('.produk-select').forEach(select => {
                initSelect2(select, produkList);
            });
        });
    }

    // Tambah baris item
    function addItem() {
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.item-row');
        row.innerHTML = row.innerHTML.replace(/INDEX/g, itemIndex);
        const select = row.querySelector('.produk-select');
        container.appendChild(row);

        // Inisialisasi dengan data saat ini
        const supplierId = supplierSelect.value;
        loadProdukBySupplier(supplierId, function(produkList) {
            initSelect2(select, produkList);
        });

        row.querySelector('.btn-remove-item').addEventListener('click', function() {
            if ($(select).data('select2')) {
                $(select).select2('destroy');
            }
            row.remove();
        });

        itemIndex++;
    }

    // Event ketika merek berubah
    supplierSelect.addEventListener('change', refreshAllSelects);

    document.getElementById('btnAddItem').addEventListener('click', addItem);

    // Tambah baris pertama kali
    if (container.children.length === 0) {
        addItem();
        // Jika ada old value (supplier terpilih), trigger refresh
        if (supplierSelect.value) {
            refreshAllSelects();
        }
    }
});
</script>
<?= $this->endSection() ?>