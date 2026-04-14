# create_views.ps1
Write-Host "--- Memulai Pembuatan Struktur View Omah Nini Enterprise ---" -ForegroundColor Cyan

$basePath = "app\Views"

# Daftar folder yang perlu dibuat berdasarkan Use Case & Desain A3
$folders = @(
    "auth",
    "dashboard",
    "admin\users",
    "admin\kategori",
    "admin\supplier",
    "admin\produk",
    "admin\laporan",
    "admin\retur",
    "gudang\stok",
    "gudang\pembelian",
    "gudang\barcode",
    "kasir\transaksi",
    "kasir\riwayat"
)

# Pembuatan Folder
foreach ($folder in $folders) {
    $targetPath = Join-Path $basePath $folder
    if (!(Test-Path $targetPath)) {
        New-Item -Path $targetPath -ItemType Directory -Force | Out-Null
        Write-Host "[Folder] Berhasil dibuat: $folder" -ForegroundColor Gray
    }
}

# Daftar File View spesifik sesuai kebutuhan Aktor
$viewFiles = @(
    # Auth & Dashboard
    "auth\login.php",
    "dashboard.php",

    # Admin: Kelola Master & User [cite: 14, 15, 16, 17]
    "admin\users\index.php", "admin\users\create.php", "admin\users\edit.php",
    "admin\kategori\index.php", "admin\kategori\create.php", "admin\kategori\edit.php",
    "admin\supplier\index.php", "admin\supplier\create.php", "admin\supplier\edit.php",
    "admin\produk\index.php", "admin\produk\create.php", "admin\produk\edit.php",
    
    # Admin: Laporan & Otorisasi [cite: 23, 24]
    "admin\laporan\keuangan.php", "admin\laporan\laba_rugi.php",
    "admin\retur\index.php",

    # Gudang: Logistik & Barcode [cite: 17, 18, 22]
    "gudang\stok\index.php", "gudang\stok\opname.php", "gudang\stok\history.php",
    "gudang\pembelian\index.php", "gudang\pembelian\create.php", "gudang\pembelian\detail.php",
    "gudang\barcode\index.php", "gudang\barcode\cetak.php", "gudang\barcode\single.php",

    # Kasir: POS & Riwayat [cite: 20, 21]
    "kasir\transaksi\index.php", "kasir\transaksi\nota.php",
    "kasir\riwayat\index.php", "kasir\riwayat\detail.php"
)

# Pembuatan File Kosong
foreach ($file in $viewFiles) {
    $filePath = Join-Path $basePath $file
    if (!(Test-Path $filePath)) {
        New-Item -Path $filePath -ItemType File -Force | Out-Null
        Write-Host "[File]   Berhasil dibuat: $file" -ForegroundColor White
    }
}

Write-Host "`n--- Semua View Berhasil Disiapkan (Total: $($viewFiles.Count)) ---" -ForegroundColor Green
