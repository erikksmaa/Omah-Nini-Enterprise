<?php

if (!function_exists('format_rupiah')) {
    /**
     * Format angka ke format rupiah (1.500.000)
     */
    function format_rupiah($angka, $withPrefix = false)
    {
        if ($angka === null || $angka === '') return '';
        $formatted = number_format((float) $angka, 0, ',', '.');
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('unformat_rupiah')) {
    /**
     * Hapus format rupiah menjadi angka biasa (1.500.000 → 1500000)
     */
    function unformat_rupiah($angka)
    {
        if ($angka === null || $angka === '') return 0;
        return (int) str_replace('.', '', $angka);
    }
}

if (!function_exists('rupiah_to_int')) {
    /**
     * Konversi string rupiah ke integer
     */
    function rupiah_to_int($rupiah)
    {
        return (int) preg_replace('/[^0-9]/', '', $rupiah);
    }
}

if (!function_exists('int_to_rupiah')) {
    /**
     * Konversi integer ke string rupiah
     */
    function int_to_rupiah($angka)
    {
        return number_format($angka, 0, ',', '.');
    }
}