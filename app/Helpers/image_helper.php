<?php

use CodeIgniter\Images\ImageHandlerInterface;

if (!function_exists('uploadAndResizeImage')) {
    function uploadAndResizeImage($file, $existingFile = null)
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        // Konfigurasi
        $thumbnailWidth = 150;    // Ukuran thumbnail (untuk tampilan kecil)
        $thumbnailHeight = 150;
        $displayWidth = 600;       // Ukuran untuk tampilan normal (zoom awal)
        $displayHeight = 600;
        $quality = 85;             // Kualitas gambar (1-100)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

        // Validasi tipe file
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return null;
        }

        // Validasi ukuran (maks 10MB untuk kualitas zoom bagus)
        if ($file->getSize() > 10 * 1024 * 1024) {
            return null;
        }

        // Hapus file lama jika ada
        if ($existingFile) {
            deleteImage($existingFile);
        }

        // Buat folder jika belum ada
        if (!is_dir(FCPATH . 'uploads/produk')) {
            mkdir(FCPATH . 'uploads/produk', 0777, true);
        }
        if (!is_dir(FCPATH . 'uploads/produk/thumb')) {
            mkdir(FCPATH . 'uploads/produk/thumb', 0777, true);
        }

        // Generate nama file unik (gunakan timestamp + random)
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        $newFilename = $timestamp . '_' . $random . '.jpg';

        // === 1. Simpan gambar asli (ORIGINAL) untuk zoom detail ===
        $originalPath = FCPATH . 'uploads/produk/original/' . $newFilename;
        if (!is_dir(FCPATH . 'uploads/produk/original')) {
            mkdir(FCPATH . 'uploads/produk/original', 0777, true);
        }
        
        // Copy file asli tanpa resize
        copy($file->getTempName(), $originalPath);

        // === 2. Resize untuk tampilan display (600x600) ===
        $displayPath = FCPATH . 'uploads/produk/' . $newFilename;
        $image = \Config\Services::image('gd')
            ->withFile($file->getTempName())
            ->fit($displayWidth, $displayHeight, 'center')
            ->save($displayPath, $quality);

        // === 3. Buat thumbnail (150x150) ===
        $thumbPath = FCPATH . 'uploads/produk/thumb/' . $newFilename;
        $image = \Config\Services::image('gd')
            ->withFile($file->getTempName())
            ->fit($thumbnailWidth, $thumbnailHeight, 'center')
            ->save($thumbPath, 75); // Kualitas lebih rendah untuk thumbnail

        return $newFilename;
    }
}

if (!function_exists('deleteImage')) {
    function deleteImage($filename)
    {
        if (empty($filename)) {
            return true;
        }

        // Hapus file display
        $displayPath = FCPATH . 'uploads/produk/' . $filename;
        if (file_exists($displayPath)) {
            @unlink($displayPath);
        }

        // Hapus thumbnail
        $thumbPath = FCPATH . 'uploads/produk/thumb/' . $filename;
        if (file_exists($thumbPath)) {
            @unlink($thumbPath);
        }

        // Hapus original
        $originalPath = FCPATH . 'uploads/produk/original/' . $filename;
        if (file_exists($originalPath)) {
            @unlink($originalPath);
        }

        return true;
    }
}

if (!function_exists('getImageUrl')) {
    function getImageUrl($filename, $size = 'display')
    {
        if (empty($filename)) {
            return base_url('assets/img/no-image.png');
        }

        switch ($size) {
            case 'thumb':
                return base_url('uploads/produk/thumb/' . $filename);
            case 'original':
                return base_url('uploads/produk/original/' . $filename);
            case 'display':
            default:
                return base_url('uploads/produk/' . $filename);
        }
    }
}