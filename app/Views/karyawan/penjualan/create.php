<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4>Barang Keluar (POS)</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('karyawan/penjualan/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">No. Invoice</label>
                    <input type="text" name="no_invoice" class="form-control" value="<?= old('no_invoice', $no_invoice) ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="datetime-local" name="tanggal_transaksi" class="form-control" value="<?= old('tanggal_transaksi', date('Y-m-d\TH:i')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pelanggan (opsional)</label>
                    <select name="id_pelanggan" class="form-select" id="pelanggan">
                        <option value="">-- Umum / Tidak Ada --</option>
                        <?php foreach ($pelanggan_list as $pel): ?>
                            <option value="<?= $pel['id'] ?>" data-nama="<?= esc($pel['nama']) ?>"><?= esc($pel['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Pembeli</label>
                    <input type="text" name="nama_pembeli" id="nama_pembeli" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <hr>
            <h5>Item Produk</h5>
            <div id="items-container"></div>

            <button type="button" id="btnAddItem" class="btn btn-outline-primary mb-3">
                <i class="bi bi-plus"></i> Tambah Produk
            </button>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">Proses Penjualan</button>
                <a href="<?= base_url('karyawan/penjualan') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<template id="itemTemplate">
    <div class="row item-row mb-2 align-items-end border-bottom pb-2">
        <div class="col-md-7">
            <label class="form-label">Produk</label>
            <select name="items[INDEX][id_produk]" class="form-select produk-select" style="width: 100%;" required>
                <option value="">-- Cari Produk --</option>
            </select>
            <div class="mt-2 foto-preview" style="display: none;">
                <img class="foto-img" src="" alt="Foto Produk">
                <span class="small text-muted ms-2">Klik gambar untuk memperbesar</span>
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Jumlah</label>
            <input type="number" name="items[INDEX][jumlah]" class="form-control jumlah" min="1" value="1" required>
        </div>
        <div class="col-md-2 text-end">
            <button type="button" class="btn btn-danger btn-remove-item"><i class="bi bi-trash"></i> Hapus</button>
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
    let productFotoMap = new Map();

    function formatOption(option) {
        if (!option.id) return option.text;
        return option.text;
    }

    function initSelect2(element, previewContainer, fotoImg) {
        $(element).select2({
            placeholder: "-- Cari Produk --",
            allowClear: true,
            width: '100%',
            templateResult: formatOption,
            templateSelection: formatOption,
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

    function addItem() {
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.item-row');
        row.innerHTML = row.innerHTML.replace(/INDEX/g, itemIndex);

        const select = row.querySelector('.produk-select');
        const preview = row.querySelector('.foto-preview');
        const fotoImg = row.querySelector('.foto-img');

        container.appendChild(row);
        initSelect2(select, preview, fotoImg);

        $(fotoImg).on('click', function() {
            const src = $(this).attr('src');
            if (src && !src.includes('no-image')) {
                $('#zoomImage').attr('src', src);
                $('#zoomModal').modal('show');
            }
        });

        row.querySelector('.btn-remove-item').addEventListener('click', function() {
            $(select).select2('destroy');
            row.remove();
        });

        itemIndex++;
    }

    // Pelanggan ke Nama Pembeli
    $('#pelanggan').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const nama = selectedOption.data('nama');
        if (nama) {
            $('#nama_pembeli').val(nama).prop('readonly', true);
        } else {
            $('#nama_pembeli').val('').prop('readonly', false);
        }
    });

    $('#btnAddItem').on('click', addItem);

    if (container.children.length === 0) {
        addItem();
    }
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