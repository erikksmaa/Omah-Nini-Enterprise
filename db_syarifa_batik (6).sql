-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 09, 2026 at 02:41 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_syarifa_batik`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pembelian`
--

CREATE TABLE `detail_pembelian` (
  `id` int UNSIGNED NOT NULL,
  `id_pembelian` int UNSIGNED NOT NULL,
  `id_produk` int UNSIGNED NOT NULL,
  `nama_produk` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jumlah` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pembelian`
--

INSERT INTO `detail_pembelian` (`id`, `id_pembelian`, `id_produk`, `nama_produk`, `jumlah`) VALUES
(1, 1, 10, 'AFT-LIT-TOS-010 - Litis 09 Toska', 8),
(2, 2, 10, 'AFT-LIT-TOS-010 - Litis 09 Toska', 9),
(3, 3, 6, 'MDX-MAI-PUT-006 - Maida Exclusive 01 Putih', 8),
(4, 3, 10, 'AFT-LIT-TOS-010 - Litis 09 Toska', 8),
(5, 4, 8, 'MDK-MAI-PUT-008 - Maida Katun Super 01 Putih', 8),
(6, 4, 7, 'MDX-MAI-PUT-007 - Maida Exclusive 02 Putih', 9),
(7, 5, 2, 'BJK-SEK-BIR-002 - Sekar Jagat Laseman Series Biru', 5),
(8, 5, 5, 'BJK-JLA-HIJ-005 - Jlamprang Tantum Hijau', 1),
(9, 6, 6, 'MDX-MAI-PUT-006 - Maida Exclusive 01 Putih', 12),
(10, 7, 8, 'Maida Katun Super - Maida Katun Super 01 Putih', 25),
(11, 8, 6, 'Maida Exclusive - Maida Exclusive 01 Putih', 8),
(12, 9, 8, 'Maida Katun Super - Maida Katun Super 01 Putih', 8),
(13, 10, 6, 'Maida Exclusive - Maida Exclusive 01 Putih', 5),
(14, 11, 8, 'Maida Katun Super - Maida Katun Super 01 Putih', 5),
(15, 12, 8, 'Maida Katun Super - Maida Katun Super 01 Putih', 2);

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id` int UNSIGNED NOT NULL,
  `id_transaksi` int UNSIGNED NOT NULL,
  `id_produk` int UNSIGNED NOT NULL,
  `nama_produk` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jumlah` int NOT NULL,
  `harga_satuan` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id`, `id_transaksi`, `id_produk`, `nama_produk`, `jumlah`, `harga_satuan`) VALUES
(1, 1, 3, 'BJK-CEN-BIR-003 - Cendrawasih Biru', 7, 0.00),
(2, 2, 6, 'MDX-MAI-PUT-006 - Maida Exclusive 01 Putih', 5, 0.00),
(3, 2, 10, 'AFT-LIT-TOS-010 - Litis 09 Toska', 6, 0.00),
(4, 3, 10, 'AFT-LIT-TOS-010 - Litis 09 Toska', 2, 0.00),
(5, 4, 6, 'MDX-MAI-PUT-006 - Maida Exclusive 01 Putih', 5, 0.00),
(6, 4, 5, 'BJK-JLA-HIJ-005 - Jlamprang Tantum Hijau', 5, 0.00),
(7, 5, 1, 'BJK-SEK-ABU-001 - Sekar Jagat Laseman Series Abu', 2, 0.00),
(8, 5, 4, 'BJK-CEN-MUS-004 - Cendrawasih Mustard', 5, 0.00),
(9, 8, 10, 'AL FATI - Litis 09 Toska', 10, 8000.00);

-- --------------------------------------------------------

--
-- Table structure for table `laporan_barang_masuk`
--

CREATE TABLE `laporan_barang_masuk` (
  `id` int UNSIGNED NOT NULL,
  `no_laporan` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_user` int UNSIGNED NOT NULL,
  `tanggal_awal` date DEFAULT NULL,
  `tanggal_akhir` date DEFAULT NULL,
  `id_supplier` int UNSIGNED DEFAULT NULL,
  `nama_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_stok`
--

CREATE TABLE `log_stok` (
  `id` int UNSIGNED NOT NULL,
  `id_produk` int UNSIGNED NOT NULL,
  `id_user` int UNSIGNED NOT NULL,
  `tipe_ref` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_ref` int DEFAULT NULL,
  `jumlah_sebelum` int NOT NULL,
  `jumlah_perubahan` int NOT NULL,
  `jumlah_sesudah` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_stok`
--

INSERT INTO `log_stok` (`id`, `id_produk`, `id_user`, `tipe_ref`, `id_ref`, `jumlah_sebelum`, `jumlah_perubahan`, `jumlah_sesudah`, `created_at`) VALUES
(1, 10, 2, 'pembelian', 1, 63, 8, 71, '2026-05-01 15:49:17'),
(2, 10, 2, 'pembelian', 2, 71, 9, 80, '2026-05-01 16:02:59'),
(3, 6, 2, 'pembelian', 3, 12, 8, 20, '2026-05-01 16:08:33'),
(4, 10, 2, 'pembelian', 3, 80, 8, 88, '2026-05-01 16:08:33'),
(5, 8, 2, 'pembelian', 4, 12, 8, 20, '2026-05-01 16:09:01'),
(6, 7, 2, 'pembelian', 4, 11, 9, 20, '2026-05-01 16:09:01'),
(7, 3, 2, 'penjualan', 1, 77, 7, 70, '2026-05-01 16:18:13'),
(8, 6, 2, 'penjualan', 2, 20, 5, 15, '2026-05-01 16:33:51'),
(9, 10, 2, 'penjualan', 2, 88, 6, 82, '2026-05-01 16:33:51'),
(10, 10, 2, 'penjualan', 3, 82, 2, 80, '2026-05-01 16:34:45'),
(11, 6, 2, 'penjualan', 4, 15, 5, 10, '2026-05-01 16:40:07'),
(12, 5, 2, 'penjualan', 4, 5, 5, 0, '2026-05-01 16:40:07'),
(13, 2, 2, 'pembelian', 5, 45, 5, 50, '2026-05-01 17:17:37'),
(14, 5, 2, 'pembelian', 5, 0, 1, 1, '2026-05-01 17:17:37'),
(15, 1, 2, 'penjualan', 5, 72, 2, 70, '2026-05-01 17:19:16'),
(16, 4, 2, 'penjualan', 5, 85, 5, 80, '2026-05-01 17:19:16'),
(17, 1, 2, 'penyesuaian', NULL, 70, 10, 80, '2026-05-01 17:20:11'),
(18, 6, 2, 'pembelian', 6, 10, 12, 22, '2026-05-02 03:45:32'),
(21, 10, 1, 'penjualan', 8, 80, 10, 70, '2026-05-09 08:16:15'),
(22, 8, 1, 'pembelian', 7, 20, 25, 45, '2026-05-09 08:39:40'),
(23, 6, 1, 'pembelian', 8, 22, 8, 30, '2026-05-09 08:40:42'),
(24, 8, 1, 'pembelian', 9, 45, 8, 53, '2026-05-09 08:41:36'),
(25, 6, 1, 'pembelian', 10, 30, 5, 35, '2026-05-09 08:41:52'),
(26, 8, 1, 'pembelian', 11, 53, 5, 58, '2026-05-09 08:42:13'),
(27, 8, 1, 'pembelian', 12, 58, 2, 60, '2026-05-09 08:51:17');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-05-01-082148', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1777623910, 1),
(2, '2026-05-01-082149', 'App\\Database\\Migrations\\CreateSupplierTable', 'default', 'App', 1777623910, 1),
(3, '2026-05-01-082150', 'App\\Database\\Migrations\\CreateMotifTable', 'default', 'App', 1777623910, 1),
(4, '2026-05-01-082150', 'App\\Database\\Migrations\\CreateWarnaTable', 'default', 'App', 1777624001, 2),
(5, '2026-05-01-082151', 'App\\Database\\Migrations\\CreatePelangganTable', 'default', 'App', 1777624001, 2),
(6, '2026-05-01-082701', 'App\\Database\\Migrations\\CreateProdukTable', 'default', 'App', 1777624044, 3),
(7, '2026-05-01-082743', 'App\\Database\\Migrations\\CreatePembelianTable', 'default', 'App', 1777624079, 4),
(8, '2026-05-01-082810', 'App\\Database\\Migrations\\CreateDetailPembelianTable', 'default', 'App', 1777624102, 5),
(9, '2026-05-01-082831', 'App\\Database\\Migrations\\CreateTransaksiTable', 'default', 'App', 1777624118, 6),
(10, '2026-05-01-082849', 'App\\Database\\Migrations\\CreateDetailTransaksiTable', 'default', 'App', 1777624161, 7),
(11, '2026-05-01-082907', 'App\\Database\\Migrations\\CreateLogStokTable', 'default', 'App', 1777624161, 7),
(12, '2026-05-02-044617', 'App\\Database\\Migrations\\CreateLaporanBarangMasuk', 'default', 'App', 1777697841, 8),
(13, '2026-05-07-144327', 'App\\Database\\Migrations\\AddHargaToDetailTransaksi', 'default', 'App', 1778166765, 9),
(14, '2026-05-09-143504', 'App\\Database\\Migrations\\AddFotoToProduk', 'default', 'App', 1778337342, 10);

-- --------------------------------------------------------

--
-- Table structure for table `motif`
--

CREATE TABLE `motif` (
  `id` int UNSIGNED NOT NULL,
  `id_supplier` int UNSIGNED NOT NULL,
  `nama_motif` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `motif`
--

INSERT INTO `motif` (`id`, `id_supplier`, `nama_motif`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, 'Sekar Jagat Laseman Series', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(2, 1, 'Bakaran Putih', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(3, 1, 'Cendrawasih', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(4, 1, 'Jlamprang Tantum', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(5, 1, 'Blarak Lantang', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(6, 1, 'Bakaran Pagi Sore', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(7, 2, 'Maida Exclusive 01', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(8, 2, 'Maida Exclusive 02', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(9, 2, 'Maida Exclusive 03', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(10, 2, 'Maida Exclusive 04', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(11, 2, 'Maida Exclusive 05', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(12, 2, 'Maida Exclusive 06', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(13, 2, 'Maida Exclusive 07', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(14, 2, 'Maida Exclusive 08', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(15, 3, 'Maida Katun Super 01', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(16, 3, 'Maida Katun Super 02', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(17, 3, 'Maida Katun Super 03', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(18, 3, 'Maida Katun Super 04', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(19, 3, 'Maida Katun Super 05', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(20, 3, 'Maida Katun Super 06', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(21, 3, 'Maida Katun Super 07', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(22, 3, 'Maida Katun Super 08', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(23, 4, 'Wayans', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(24, 4, 'Siyomakti', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(25, 4, 'Litis 09', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(26, 4, 'Seno Repekhan', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(27, 4, 'Benowd', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(28, 4, 'Lagonian Nadhiza', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(29, 4, 'Kalmaghan', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(30, 4, 'Macon', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(31, 1, 'Laragon123', '', '2026-05-01 09:36:15', '2026-05-01 09:36:15');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int UNSIGNED NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `no_telp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama`, `alamat`, `no_telp`, `created_at`, `updated_at`) VALUES
(1, 'Husna', 'Jl. Merdeka No. 10, Pekalongan', '081234567001', '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(2, 'Wahyu', 'Jl. Diponegoro No. 25, Pekalongan', '081234567002', '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(3, 'Siti', 'Jl. Batik No. 7, Solo', '081234567003', '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(4, 'Budi', 'Jl. Raya No. 45, Yogyakarta', '081234567004', '2026-05-01 08:36:23', '2026-05-01 08:36:23');

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `id` int UNSIGNED NOT NULL,
  `no_invoice` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_supplier` int UNSIGNED NOT NULL,
  `id_user` int UNSIGNED NOT NULL,
  `tanggal_pembelian` date NOT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`id`, `no_invoice`, `id_supplier`, `id_user`, `tanggal_pembelian`, `catatan`, `created_at`) VALUES
(1, 'PO-260501-0001', 1, 2, '2026-05-01', '', NULL),
(2, 'PO-260501-0002', 4, 2, '2026-05-01', '', NULL),
(3, 'PO-260501-0003', 2, 2, '2026-05-01', '', NULL),
(4, 'PO-260501-0004', 2, 2, '2026-05-01', '', NULL),
(5, 'PO-260502-0005', 2, 2, '2026-05-02', '', NULL),
(6, 'PO-260502-0006', 3, 2, '2026-05-02', '', NULL),
(7, 'PO-260509-0001', 3, 1, '2026-05-09', 'bla bla bla', NULL),
(8, 'PO-260509-0002', 2, 1, '2026-05-09', 'bla bla bla', NULL),
(9, 'PO-260509-0003', 3, 1, '2026-05-09', 'bla bla bla', NULL),
(10, 'PO-260509-0004', 2, 1, '2026-05-09', 'bla bla bla', NULL),
(11, 'PO-260509-0005', 3, 1, '2026-05-09', 'bla bla bla', NULL),
(12, 'PO-260509-0006', 3, 1, '2026-05-09', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int UNSIGNED NOT NULL,
  `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_supplier` int UNSIGNED NOT NULL,
  `id_motif` int UNSIGNED NOT NULL,
  `id_warna` int UNSIGNED NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `min_stok` int NOT NULL DEFAULT '0',
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `foto` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `sku`, `id_supplier`, `id_motif`, `id_warna`, `stok`, `min_stok`, `keterangan`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'BJK-SEK-ABU-001', 1, 1, 3, 80, 10, 'Sekar Jagat Laseman Series - Abu', NULL, '2026-05-01 08:36:23', '2026-05-01 17:20:11'),
(2, 'BJK-SEK-BIR-002', 1, 1, 4, 50, 10, 'Sekar Jagat Laseman Series - Biru', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(3, 'BJK-CEN-BIR-003', 1, 3, 4, 70, 10, 'Cendrawasih - Biru', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(4, 'BJK-CEN-MUS-004', 1, 3, 5, 80, 10, 'Cendrawasih - Mustard', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(5, 'BJK-JLA-HIJ-005', 1, 4, 6, 1, 5, 'Jlamprang Tantum - Hijau', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(6, 'MDX-MAI-PUT-006', 2, 7, 1, 35, 5, 'Maida Exclusive 01 - Putih', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(7, 'MDX-MAI-PUT-007', 2, 8, 1, 20, 5, 'Maida Exclusive 02 - Putih', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(8, 'MDK-MAI-PUT-008', 3, 15, 1, 60, 5, 'Maida Katun Super 01 - Putih', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(9, 'AFT-WAY-SOG-009', 4, 23, 2, 50, 10, 'Wayans - Sogan', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(10, 'AFT-LIT-TOS-010', 4, 25, 7, 70, 10, 'Litis 09 - Toska', NULL, '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(11, 'AFT-LAG-NAV-001', 4, 28, 11, 0, 0, 'lalala', NULL, '2026-05-02 03:48:47', '2026-05-02 03:48:59'),
(12, 'MDX-MAI-PIN-001', 2, 9, 16, 0, 0, '', '1778337537_67da87d8838ad8b7.jpg', '2026-05-09 14:38:27', '2026-05-09 14:38:57');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id` int UNSIGNED NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kontak` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(90) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `nama`, `kontak`, `email`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'Bang Jack\'s / Aulia', '081234567890', 'bangjacks@example.com', 'Jl. Batik No. 45, Pekalongan', '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(2, 'Maida Exclusive', '081234567891', 'maida@example.com', 'Jl. Raya Batik No. 12, Solo', '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(3, 'Maida Katun Super', '081234567892', 'maida.katun@example.com', 'Jl. Raya Batik No. 12, Solo', '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(4, 'AL FATI', '081234567893', 'alfati@example.com', 'Jl. Batik KM 5, Yogyakarta', '2026-05-01 08:36:23', '2026-05-01 08:36:23');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int UNSIGNED NOT NULL,
  `no_invoice` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_user` int UNSIGNED NOT NULL,
  `id_pelanggan` int UNSIGNED DEFAULT NULL,
  `nama_pembeli` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_transaksi` timestamp NULL DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `no_invoice`, `id_user`, `id_pelanggan`, `nama_pembeli`, `tanggal_transaksi`, `catatan`, `created_at`) VALUES
(1, 'INV-260501-0001', 2, 2, 'wahyu', '2026-05-01 16:17:00', '', NULL),
(2, 'INV-260501-0002', 2, NULL, 'wahyu', '2026-05-01 16:32:00', '', NULL),
(3, 'INV-260501-0003', 2, NULL, 'wahyu', '2026-05-01 16:33:00', '', NULL),
(4, 'INV-260501-0004', 2, 4, 'Budi', '2026-05-01 16:39:00', '', NULL),
(5, 'INV-260502-0005', 2, 4, 'Budi', '2026-05-01 17:18:00', '', NULL),
(8, 'INV-260509-0001', 1, NULL, 'Albar', '2026-05-09 07:38:00', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int UNSIGNED NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','karyawan','pemilik') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'karyawan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$12$h6peZJELYbZkFzRKBrUGeecKyMsBit8poEpDomd50SkeMfEFhYzq.', 'admin', '2026-05-01 08:36:22', '2026-05-01 08:36:22'),
(2, 'karyawan1', '$2y$12$mqZoUYakturWUH9PTUDxpekgsYUt5rJreIpcI1tS/PDRr.g7GV7GO', 'karyawan', '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(3, 'karyawan2', '$2y$12$eFPOP1Th4GDeMfVHdwBnm.1jU85fhDZlfsCWMrnhZbifxXOTPxiDG', 'karyawan', '2026-05-01 08:36:23', '2026-05-01 08:36:23'),
(4, 'pemilik', '$2y$12$M7FzDJv.Z1Ssk9lzIsyaSuOT5c7xxHcoevFE1lT.pabBIzMHyQX9O', 'pemilik', '2026-05-02 05:48:48', '2026-05-02 05:48:48');

-- --------------------------------------------------------

--
-- Table structure for table `warna`
--

CREATE TABLE `warna` (
  `id` int UNSIGNED NOT NULL,
  `nama_warna` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warna`
--

INSERT INTO `warna` (`id`, `nama_warna`, `created_at`) VALUES
(1, 'Putih', '2026-05-01 08:36:23'),
(2, 'Sogan', '2026-05-01 08:36:23'),
(3, 'Abu', '2026-05-01 08:36:23'),
(4, 'Biru', '2026-05-01 08:36:23'),
(5, 'Mustard', '2026-05-01 08:36:23'),
(6, 'Hijau', '2026-05-01 08:36:23'),
(7, 'Toska', '2026-05-01 08:36:23'),
(8, 'Merah', '2026-05-01 08:36:23'),
(9, 'Hitam', '2026-05-01 08:36:23'),
(10, 'Maroon', '2026-05-01 08:36:23'),
(11, 'Navy', '2026-05-01 08:36:23'),
(12, 'Kuning', '2026-05-01 08:36:23'),
(13, 'Ungu', '2026-05-01 08:36:23'),
(14, 'Orange', '2026-05-01 08:36:23'),
(15, 'Coklat', '2026-05-01 08:36:23'),
(16, 'Pink', '2026-05-01 08:36:23'),
(17, 'Cream', '2026-05-01 08:36:23'),
(18, 'Monocrom', '2026-05-01 08:36:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_pembelian_id_pembelian_foreign` (`id_pembelian`),
  ADD KEY `detail_pembelian_id_produk_foreign` (`id_produk`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_transaksi_id_transaksi_foreign` (`id_transaksi`),
  ADD KEY `detail_transaksi_id_produk_foreign` (`id_produk`);

--
-- Indexes for table `laporan_barang_masuk`
--
ALTER TABLE `laporan_barang_masuk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_laporan` (`no_laporan`),
  ADD KEY `laporan_barang_masuk_id_user_foreign` (`id_user`),
  ADD KEY `laporan_barang_masuk_id_supplier_foreign` (`id_supplier`);

--
-- Indexes for table `log_stok`
--
ALTER TABLE `log_stok`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_stok_id_produk_foreign` (`id_produk`),
  ADD KEY `log_stok_id_user_foreign` (`id_user`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `motif`
--
ALTER TABLE `motif`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_motif_per_supplier` (`id_supplier`,`nama_motif`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_invoice` (`no_invoice`),
  ADD KEY `pembelian_id_supplier_foreign` (`id_supplier`),
  ADD KEY `pembelian_id_user_foreign` (`id_user`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `produk_id_supplier_foreign` (`id_supplier`),
  ADD KEY `produk_id_motif_foreign` (`id_motif`),
  ADD KEY `produk_id_warna_foreign` (`id_warna`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_invoice` (`no_invoice`),
  ADD KEY `transaksi_id_user_foreign` (`id_user`),
  ADD KEY `transaksi_id_pelanggan_foreign` (`id_pelanggan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `warna`
--
ALTER TABLE `warna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_warna` (`nama_warna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `laporan_barang_masuk`
--
ALTER TABLE `laporan_barang_masuk`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_stok`
--
ALTER TABLE `log_stok`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `motif`
--
ALTER TABLE `motif`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `warna`
--
ALTER TABLE `warna`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD CONSTRAINT `detail_pembelian_id_pembelian_foreign` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pembelian_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `detail_transaksi_id_transaksi_foreign` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `laporan_barang_masuk`
--
ALTER TABLE `laporan_barang_masuk`
  ADD CONSTRAINT `laporan_barang_masuk_id_supplier_foreign` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `laporan_barang_masuk_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `log_stok`
--
ALTER TABLE `log_stok`
  ADD CONSTRAINT `log_stok_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `log_stok_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `motif`
--
ALTER TABLE `motif`
  ADD CONSTRAINT `motif_id_supplier_foreign` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `pembelian_id_supplier_foreign` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `pembelian_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_id_motif_foreign` FOREIGN KEY (`id_motif`) REFERENCES `motif` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `produk_id_supplier_foreign` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `produk_id_warna_foreign` FOREIGN KEY (`id_warna`) REFERENCES `warna` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `transaksi_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
