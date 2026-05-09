<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4>Tambah Penjualan</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('karyawan/penjualan/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="datetime-local" name="tanggal_transaksi" class="form-control" value="<?= old('tanggal_transaksi', date('Y-m-d\TH:i')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pelanggan (opsional)</label>
                    <select name="id_pelanggan" id="pelanggan" class="form-select">
                        <option value="">-- Umum --</option>
                        <?php foreach ($pelanggan_list as $pel): ?>
                            <option value="<?= $pel['id'] ?>" data-nama="<?= esc($pel['nama']) ?>"><?= esc($pel['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nama Pembeli</label>
                    <input type="text" name="nama_pembeli" id="nama_pembeli" class="form-control" value="<?= old('nama_pembeli') ?>" required>
                    <small class="text-muted">Jika pelanggan dipilih, nama otomatis terisi.</small>
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
            <div id="items-container">
                <!-- JS akan mengisi baris item -->
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                <button type="button" id="btnAddItem" class="btn btn-outline-primary">
                    <i class="bi bi-plus"></i> Tambah Produk
                </button>
                <div class="text-end">
                    <small class="text-muted">Total Keseluruhan:</small>
                    <h4 class="mb-0 text-primary" id="totalKeseluruhan">Rp 0,00</h4>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success"><i class="bi bi-cart-check"></i> Proses Penjualan</button>
                <a href="<?= base_url('karyawan/penjualan') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<!-- Template Baris Item -->
<template id="itemTemplate">
    <div class="item-row border rounded p-3 mb-3 bg-light">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Produk</label>
                <select name="items[INDEX][id_produk]" class="form-select produk-select" required>
                    <option value="">-- Cari Produk (Merek - Motif - Warna) --</option>
                    <!-- Opsi default (jika ada) bisa diisi dari server, tapi Ajax akan menggantinya -->
                </select>
                <!-- Preview foto produk (jika ada) -->
                <div class="mt-2 foto-preview" style="display: none;">
                    <img class="foto-img" src="" alt="Foto Produk" style="max-height: 80px; cursor: pointer;">
                    <span class="small text-muted ms-2">Klik gambar untuk memperbesar</span>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Jumlah</label>
                <input type="number" name="items[INDEX][jumlah]" class="form-control jumlah" placeholder="Qty" min="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Harga Satuan</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" name="items[INDEX][harga_satuan]" class="form-control harga-satuan" placeholder="0" required>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Subtotal</label>
                <input type="text" class="form-control subtotal" readonly>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-remove-item w-100"><i class="bi bi-trash"></i></button>
            </div>
        </div>
    </div>
</template>

<!-- Modal Zoom -->
<div class="modal fade" id="zoomModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">Foto Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="zoomImage" src="" style="max-width: 100%; max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('items-container');
    const template = document.getElementById('itemTemplate');
    let itemIndex = 0;
    // Peta untuk menyimpan URL foto berdasarkan id produk
    const productFotoMap = new Map();

    // ---- Fungsi bantuan ----
    function formatRupiah(angka) {
        return 'Rp ' + Number(angka).toLocaleString('id-ID');
    }

    function hitungSubtotal(row) {
        const jumlah = row.querySelector('.jumlah')?.value || 0;
        const harga  = row.querySelector('.harga-satuan')?.value?.replace(/[^0-9]/g, '') || 0;
        const sub = jumlah * harga;
        const subtotalInput = row.querySelector('.subtotal');
        if (subtotalInput) subtotalInput.value = formatRupiah(sub);
        return sub;
    }

    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            total += hitungSubtotal(row);
        });
        document.getElementById('totalKeseluruhan').innerText = formatRupiah(total);
    }

    // ---- Inisialisasi Select2 dengan Ajax ----
    function initSelect2(element, previewContainer, fotoImg) {
        $(element).select2({
            placeholder: "-- Cari Produk (Merek - Motif - Warna) --",
            allowClear: true,
            width: '100%',
            templateResult: function(option) {
                if (!option.id) return option.text;
                return option.text;
            },
            templateSelection: function(option) {
                if (!option.id) return option.text;
                return option.text;
            },
            ajax: {
                url: '<?= base_url("api/search/produk") ?>',
                dataType: 'json',
                delay: 300,
                type: 'GET',
                data: function(params) {
                    return { keyword: params.term };
                },
                processResults: function(data) {
                    data.forEach(function(item) {
                        productFotoMap.set(parseInt(item.id), item.foto);
                    });
                    return {
                        results: data.map(function(item) {
                            return { id: parseInt(item.id), text: item.text };
                        })
                    };
                }
            },
            minimumInputLength: 2
        });

        // Tampilkan foto saat produk dipilih
        $(element).on('change', function() {
            const selectedId = parseInt($(this).val());
            const foto = productFotoMap.get(selectedId);
            if (foto && fotoImg && previewContainer) {
                $(fotoImg).attr('src', foto);
                $(previewContainer).css('display', 'flex');
            } else if (previewContainer) {
                $(previewContainer).hide();
                $(fotoImg).attr('src', '');
            }
        });
    }

    // ---- Tambah baris item ----
    function addItem() {
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.item-row');
        row.innerHTML = row.innerHTML.replace(/INDEX/g, itemIndex);

        const select = row.querySelector('.produk-select');
        const previewDiv = row.querySelector('.foto-preview');
        const fotoImg = row.querySelector('.foto-img');

        container.appendChild(row);

        // Inisialisasi Select2 + preview foto
        initSelect2(select, previewDiv, fotoImg);

        // Klik gambar untuk zoom
        $(fotoImg).on('click', function() {
            const src = $(this).attr('src');
            if (src && !src.includes('no-image')) {
                $('#zoomImage').attr('src', src);
                $('#zoomModal').modal('show');
            }
        });

        // Hitung ulang total jika jumlah atau harga berubah
        row.querySelector('.jumlah').addEventListener('input', hitungTotal);
        row.querySelector('.harga-satuan').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            hitungTotal();
        });

        // Tombol hapus baris
        row.querySelector('.btn-remove-item').addEventListener('click', function() {
            $(select).select2('destroy');
            row.remove();
            hitungTotal();
        });

        itemIndex++;
        hitungTotal();
    }

    // ---- Event tombol tambah ----
    document.getElementById('btnAddItem').addEventListener('click', addItem);

    // Satu baris awal
    if (container.children.length === 0) {
        addItem();
    }

    // ---- Sinkronisasi pelanggan -> nama pembeli ----
    const pelangganSelect = document.getElementById('pelanggan');
    const namaPembeliInput = document.getElementById('nama_pembeli');

    function syncNamaPembeli() {
        const selectedOption = pelangganSelect.options[pelangganSelect.selectedIndex];
        const namaPelanggan = selectedOption.getAttribute('data-nama');
        if (namaPelanggan) {
            namaPembeliInput.value = namaPelanggan;
            namaPembeliInput.setAttribute('readonly', true);
        } else {
            namaPembeliInput.value = '';
            namaPembeliInput.removeAttribute('readonly');
        }
    }

    pelangganSelect.addEventListener('change', syncNamaPembeli);
    syncNamaPembeli(); // inisialisasi
});
</script>

<style>
    .item-row {
        background-color: #f8f9fc;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    .foto-preview {
        margin-top: 8px;
        display: none;
        align-items: center;
        gap: 8px;
    }
    .foto-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
        cursor: pointer;
    }
</style>
<?= $this->endSection() ?>