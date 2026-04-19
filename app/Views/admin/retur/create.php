<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-arrow-return-left"></i> Form Retur Penjualan</h5>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <p class="mb-0"><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/retur/store') ?>" method="POST" id="formRetur">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>No Retur</label>
                            <input type="text" class="form-control" value="<?= $no_retur ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Tanggal Retur <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_retur" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Pilih Transaksi <span class="text-danger">*</span></label>
                            <select name="id_transaksi" id="id_transaksi" class="form-control" required>
                                <option value="">-- Pilih Transaksi --</option>
                                <?php foreach ($transaksi as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= $t['no_invoice'] ?> - <?= date('d-m-Y', strtotime($t['created_at'])) ?> (Rp <?= number_format($t['total_bayar'], 0, ',', '.') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Loading indicator -->
                <div id="loadingIndicator" style="display: none; text-align: center; padding: 20px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Memuat data transaksi...</p>
                </div>

                <!-- Detail Transaksi yang dipilih -->
                <div id="detailTransaksi" style="display: none;">
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">Detail Transaksi</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tabelDetailTransaksi">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">Pilih</th>
                                            <th>Produk</th>
                                            <th class="text-end">Harga Jual</th>
                                            <th class="text-center">Jumlah Beli</th>
                                            <th class="text-center">Jumlah Retur</th>
                                            <th class="text-end">Subtotal Retur</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailTransaksiBody">
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Pilih transaksi terlebih dahulu</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Alasan Retur <span class="text-danger">*</span></label>
                    <textarea name="alasan" class="form-control" rows="3" required placeholder="Alasan retur (rusak, salah kirim, dll)"></textarea>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Harga Jual</th>
                                <th class="text-center">Jumlah Retur</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada produk dipilih</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">TOTAL RETUR</td>
                                <td colspan="2" class="fw-bold" id="totalDisplay">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <input type="hidden" name="items" id="itemsData">
                <button type="submit" class="btn btn-primary" id="btnSubmit">
                    <i class="bi bi-save"></i> Simpan Retur
                </button>
                <a href="<?= base_url('admin/retur') ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let total = 0;

    // Event: Pilih transaksi
    document.getElementById('id_transaksi').addEventListener('change', function() {
        const id = this.value;
        const detailDiv = document.getElementById('detailTransaksi');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const detailBody = document.getElementById('detailTransaksiBody');
        
        if (!id) {
            detailDiv.style.display = 'none';
            return;
        }
        
        // Reset cart
        cart = [];
        updateCart();
        
        // Show loading
        loadingIndicator.style.display = 'block';
        detailDiv.style.display = 'none';
        detailBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Memuat data...</td></tr>';
        
        // Fetch detail transaksi
        fetch(`<?= base_url('admin/retur/getDetailTransaksi/') ?>${id}`)
            .then(response => response.json())
            .then(data => {
                loadingIndicator.style.display = 'none';
                
                if (data.success) {
                    renderDetailTransaksi(data.detail);
                    detailDiv.style.display = 'block';
                } else {
                    detailBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Gagal memuat data: ' + (data.message || 'Transaksi tidak ditemukan') + '</td></tr>';
                    detailDiv.style.display = 'block';
                }
            })
            .catch(error => {
                loadingIndicator.style.display = 'none';
                console.error('Error:', error);
                detailBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error: ' + error + '</td></tr>';
                detailDiv.style.display = 'block';
            });
    });

    // Render detail transaksi
    function renderDetailTransaksi(detail) {
        const tbody = document.getElementById('detailTransaksiBody');
        
        if (!detail || detail.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Tidak ada produk dalam transaksi ini</td></tr>';
            return;
        }
        
        tbody.innerHTML = '';
        
        detail.forEach(item => {
            const maxJumlah = item.jumlah;
            const sudahDiretur = cart.find(c => c.id_detail_transaksi == item.id);
            const sisa = maxJumlah - (sudahDiretur ? sudahDiretur.jumlah : 0);
            const isChecked = sudahDiretur ? true : false;
            const jumlahValue = sudahDiretur ? sudahDiretur.jumlah : 1;
            
            const row = tbody.insertRow();
            row.insertCell(0).innerHTML = `<input type="checkbox" class="pilih-produk" data-id="${item.id}" data-id-produk="${item.id_produk}" data-nama="${item.nama_produk}" data-harga="${item.harga_satuan}" data-max="${sisa}" ${isChecked ? 'checked' : ''}>`;
            row.insertCell(1).innerHTML = `<strong>${item.nama_produk}</strong>`;
            row.insertCell(2).innerHTML = '<div class="text-end">Rp ' + parseInt(item.harga_satuan).toLocaleString('id-ID') + '</div>';
            row.insertCell(3).innerHTML = '<div class="text-center">' + item.jumlah + '</div>';
            row.insertCell(4).innerHTML = `<input type="number" class="form-control form-control-sm jumlah-retur text-center" data-id="${item.id}" value="${jumlahValue}" min="1" max="${sisa}" style="width: 100px; margin: 0 auto;" ${!isChecked ? 'disabled' : ''}>`;
            row.insertCell(5).innerHTML = `<span id="subtotal-${item.id}" class="text-end">Rp ${(parseInt(item.harga_satuan) * jumlahValue).toLocaleString('id-ID')}</span>`;
        });
        
        // Event checkbox
        document.querySelectorAll('.pilih-produk').forEach(checkbox => {
            checkbox.removeEventListener('change', handleCheckboxChange);
            checkbox.addEventListener('change', handleCheckboxChange);
        });
        
        // Event jumlah retur
        document.querySelectorAll('.jumlah-retur').forEach(input => {
            input.removeEventListener('input', handleJumlahChange);
            input.addEventListener('input', handleJumlahChange);
        });
    }
    
    function handleCheckboxChange(e) {
        const id = this.getAttribute('data-id');
        const jumlahInput = document.querySelector(`.jumlah-retur[data-id="${id}"]`);
        const maxJumlah = parseInt(this.getAttribute('data-max'));
        const harga = parseInt(this.getAttribute('data-harga'));
        const nama = this.getAttribute('data-nama');
        const idProduk = this.getAttribute('data-id-produk');
        
        jumlahInput.disabled = !this.checked;
        
        if (this.checked) {
            let jumlah = parseInt(jumlahInput.value) || 1;
            if (jumlah > maxJumlah) {
                jumlah = maxJumlah;
                jumlahInput.value = maxJumlah;
            }
            const subtotal = harga * jumlah;
            document.getElementById(`subtotal-${id}`).innerHTML = 'Rp ' + subtotal.toLocaleString('id-ID');
            
            // Add to cart
            const existingIndex = cart.findIndex(c => c.id_detail_transaksi == id);
            const itemData = {
                id_detail_transaksi: id,
                id_produk: idProduk,
                nama_produk: nama,
                harga_jual: harga,
                jumlah: jumlah,
                subtotal: subtotal
            };
            
            if (existingIndex !== -1) {
                cart[existingIndex] = itemData;
            } else {
                cart.push(itemData);
            }
        } else {
            document.getElementById(`subtotal-${id}`).innerHTML = 'Rp 0';
            // Remove from cart
            const index = cart.findIndex(c => c.id_detail_transaksi == id);
            if (index !== -1) {
                cart.splice(index, 1);
            }
        }
        
        updateCart();
    }
    
    function handleJumlahChange(e) {
        const id = this.getAttribute('data-id');
        const checkbox = document.querySelector(`.pilih-produk[data-id="${id}"]`);
        if (!checkbox.checked) return;
        
        const harga = parseInt(checkbox.getAttribute('data-harga'));
        let jumlah = parseInt(this.value) || 0;
        const maxJumlah = parseInt(checkbox.getAttribute('data-max'));
        const nama = checkbox.getAttribute('data-nama');
        const idProduk = checkbox.getAttribute('data-id-produk');
        
        if (jumlah > maxJumlah) {
            jumlah = maxJumlah;
            this.value = maxJumlah;
            alert(`Jumlah retur tidak boleh melebihi ${maxJumlah}`);
        }
        
        if (jumlah < 1) {
            jumlah = 1;
            this.value = 1;
        }
        
        const subtotal = harga * jumlah;
        document.getElementById(`subtotal-${id}`).innerHTML = 'Rp ' + subtotal.toLocaleString('id-ID');
        
        // Update cart
        const existingIndex = cart.findIndex(c => c.id_detail_transaksi == id);
        const itemData = {
            id_detail_transaksi: id,
            id_produk: idProduk,
            nama_produk: nama,
            harga_jual: harga,
            jumlah: jumlah,
            subtotal: subtotal
        };
        
        if (existingIndex !== -1) {
            cart[existingIndex] = itemData;
        } else {
            cart.push(itemData);
        }
        
        updateCart();
    }
    
    // Update keranjang
    function updateCart() {
        const tbody = document.getElementById('cartBody');
        
        if (cart.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Belum ada produk dipilih</td></tr>';
            document.getElementById('totalDisplay').innerHTML = 'Rp 0';
            document.getElementById('itemsData').value = '';
            total = 0;
            return;
        }
        
        let html = '';
        total = 0;
        
        cart.forEach((item, index) => {
            total += item.subtotal;
            html += `
                <tr>
                    <td><strong>${item.nama_produk}</strong></td>
                    <td class="text-end">Rp ${item.harga_jual.toLocaleString('id-ID')}</td>
                    <td class="text-center">${item.jumlah}</td>
                    <td class="text-end">Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusItem(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
        document.getElementById('totalDisplay').innerHTML = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('itemsData').value = JSON.stringify(cart);
    }
    
    function hapusItem(index) {
        const item = cart[index];
        // Reset checkbox dan input
        const checkbox = document.querySelector(`.pilih-produk[data-id="${item.id_detail_transaksi}"]`);
        if (checkbox) {
            checkbox.checked = false;
            const jumlahInput = document.querySelector(`.jumlah-retur[data-id="${item.id_detail_transaksi}"]`);
            if (jumlahInput) {
                jumlahInput.disabled = true;
                jumlahInput.value = 1;
                document.getElementById(`subtotal-${item.id_detail_transaksi}`).innerHTML = 'Rp 0';
            }
        }
        cart.splice(index, 1);
        updateCart();
    }

    // Di bagian bawah view create, sebelum form submit
document.getElementById('formRetur').addEventListener('submit', function(e) {
    console.log('Cart items:', cart);
    console.log('Items data:', document.getElementById('itemsData').value);
    
    if (cart.length === 0) {
        e.preventDefault();
        alert('Minimal 1 produk harus diretur!');
        return false;
    }
    
    const alasan = document.querySelector('textarea[name="alasan"]').value;
    if (!alasan.trim()) {
        e.preventDefault();
        alert('Alasan retur harus diisi!');
        return false;
    }
    
    // Pastikan itemsData terisi
    if (!document.getElementById('itemsData').value) {
        console.error('itemsData kosong!');
        e.preventDefault();
        alert('Data retur kosong!');
        return false;
    }
    
    return true;
});
    
    // Validasi submit
    document.getElementById('formRetur').addEventListener('submit', function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Minimal 1 produk harus diretur!');
            return false;
        }
        
        const alasan = document.querySelector('textarea[name="alasan"]').value;
        if (!alasan.trim()) {
            e.preventDefault();
            alert('Alasan retur harus diisi!');
            return false;
        }
        
        return true;
    });
</script>

<style>
    .jumlah-retur {
        width: 100px;
        margin: 0 auto;
    }
    .table-bordered td, .table-bordered th {
        vertical-align: middle;
    }
</style>
<?= $this->endSection() ?>