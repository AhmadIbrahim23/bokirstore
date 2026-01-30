-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 26, 2025 at 06:27 PM
-- Server version: 8.0.42-cll-lve
-- PHP Version: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sakurupi_demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `id` int NOT NULL,
  `slider` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`id`, `slider`) VALUES
(1, 'https://sakurupiah.my.id/assets/img/banner/banner5.png'),
(2, 'https://sakurupiah.my.id/assets/img/banner/banner4.png'),
(4, 'https://sakurupiah.my.id/assets/img/banner/banner3.png');

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` int NOT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `konten` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `waktu_dibuat` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`id`, `judul`, `konten`, `logo`, `waktu_dibuat`) VALUES
(1, 'Bayar Tagihan Lebih Mudah Lewat Layanan PPOB', 'Kini bayar tagihan bulanan tidak perlu repot! Dengan layanan PPOB (Payment Point Online Bank), kamu bisa membayar listrik, air PDAM, BPJS Kesehatan, hingga tagihan internet hanya dalam satu platform. Cukup masukkan nomor pelanggan dan pilih metode pembayaran yang kamu suka. Proses cepat, aman, dan langsung terverifikasi. Cocok untuk kamu yang sibuk atau ingin buka usaha loket pembayaran.', 'tentang-kami.png', '2025-06-22 11:02:16'),
(2, 'Top Up Game Lebih Murah, Banyak Bonus!', 'Para gamers merapat! Kami hadirkan promo top up game terbaik untuk Mobile Legends, Free Fire, PUBG, dan game populer lainnya. Tak hanya murah, setiap pembelian top up juga bisa dapatkan cashback atau bonus item in-game. Proses cepat dan legal langsung dari distributor resmi. Jangan sampai ketinggalan event spesial ini, karena stok promo terbatas dan hanya berlaku bulan ini!', 'games-online.png', '2025-06-22 11:02:16');

-- --------------------------------------------------------

--
-- Table structure for table `deposit`
--

CREATE TABLE `deposit` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `kode_transaksi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `metode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `payment_gateway` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT 'sakurupiah',
  `amount` decimal(12,2) NOT NULL,
  `biaya` decimal(12,2) DEFAULT '0.00',
  `total_transfer` decimal(12,2) NOT NULL,
  `payment_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `status` enum('pending','paid','expired','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT 'pending',
  `reference_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `trx_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `json_invoice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `callback_response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `expired_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `layanan`
--

CREATE TABLE `layanan` (
  `id` int NOT NULL,
  `kode_produk` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `nama_produk` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `kategori` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `brand` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `tipe` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `deskripsi` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `logo` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `harga_asli` double NOT NULL,
  `harga_jual` double NOT NULL,
  `harga_reseller` double NOT NULL,
  `harga_diskon` double NOT NULL,
  `tipe_diskon` enum('On','Off') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `status` enum('normal','gangguan') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `expired_diskon` datetime NOT NULL,
  `tipe_produk` enum('prabayar','pascabayar') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumping data for table `layanan`
--

INSERT INTO `layanan` (`id`, `kode_produk`, `nama_produk`, `kategori`, `brand`, `tipe`, `deskripsi`, `logo`, `harga_asli`, `harga_jual`, `harga_reseller`, `harga_diskon`, `tipe_diskon`, `status`, `expired_diskon`, `tipe_produk`) VALUES
(1, '1', 'Indosat 100 MB 30 Hari', 'paket_data', 'indosat', 'umum', 'Indosat 100MB 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 880, 1880, 1380, 1680, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(2, '10', 'Indosat 4 GB 30 Hari', 'paket_data', 'indosat', 'umum', 'kuota 4GB 30 hari', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 21585, 22585, 22085, 22385, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(3, '11', 'Indosat 5 GB 30 Hari', 'paket_data', 'indosat', 'umum', 'KUOTA 5GB 30 hari', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 30175, 31175, 30675, 30975, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(4, '13', 'Indosat 8 GB 30 Hari', 'paket_data', 'indosat', 'umum', 'Kuota 8GB 30 hari', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 41725, 42725, 42225, 42525, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(5, '14', 'Indosat 10 GB 30 Hari', 'paket_data', 'indosat', 'umum', 'Kuota 10GB 30 hari', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 42000, 43000, 42500, 42800, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(6, '2', 'Indosat 200 MB 30 Hari', 'paket_data', 'indosat', 'umum', 'Indosat 200MB 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 1640, 2640, 2140, 2440, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(7, '3', 'Indosat 300 MB 30 Hari', 'paket_data', 'indosat', 'umum', 'Indosat 300MB 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 3405, 4405, 3905, 4205, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(8, '4', 'Indosat 400 MB 30 Hari', 'paket_data', 'indosat', 'umum', 'Indosat 400 MB 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 2716, 3716, 3216, 3516, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(9, '5', 'Indosat 500 MB 30 Hari', 'paket_data', 'indosat', 'umum', 'Indosat 500MB 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 4605, 5605, 5105, 5405, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(10, '6', 'Indosat 750 MB 30 Hari', 'paket_data', 'indosat', 'umum', 'Indosat 750MB 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 6005, 7005, 6505, 6805, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(11, '7', 'Indosat 1 GB 30 Hari', 'paket_data', 'indosat', 'umum', 'KUOTA 1GB 30 hari', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 8950, 10350, 9450, 9750, 'On', 'normal', '2025-06-30 15:57:16', 'prabayar'),
(12, '8', 'Indosat 2 GB 30 Hari', 'paket_data', 'indosat', 'umum', 'KUOTA 2GB 30 hari', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 17875, 18875, 18375, 18675, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(13, '9', 'Indosat 3 GB 30 Hari', 'paket_data', 'indosat', 'umum', 'KUOTA 3GB 30 hari', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 16965, 17965, 17465, 17200, 'On', 'normal', '2025-06-30 16:01:19', 'prabayar'),
(14, 'ACD', 'Call of Duty Mobile 26 CP', 'game', 'call of duty mobile', 'umum', 'Call of Duty Mobile 26CP', 'https://sakurupiah.my.id/assets/img/produk/callofdutymobile.jpg', 4850, 5850, 5350, 5650, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(15, 'Aktivasi-axis3gb', 'Aktivasi Perdana Axis Bronet 3 GB 60 Hari', 'aktivasi', 'axis', 'bronet', 'Perdana Bronet 3GB + Kuota di Kota-mu 60hr', 'https://sakurupiah.my.id/assets/img/produk/axis-perdana.png', 22410, 23410, 22910, 23210, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(16, 'Aktivasi-perdanaaxis60hr', 'Aktivasi Perdana Axis Bronet 1.5 GB / 60 Hari', 'aktivasi', 'axis', 'bronet', 'SP Bronet 1.5GB National/ 2GB BIGBRO/ 3GB BIGBRO 2.5/ 3GB BOY/ 4GB BIGB OY 60hr', 'https://sakurupiah.my.id/assets/img/produk/axis-perdana.png', 15205, 16205, 15705, 16005, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(17, 'Aktivasi-perdanatlkm30hre', 'Aktivasi Perdana Telkomsel Jawa Tengah - DIY InternetMAX Lite 3 GB 30 Hari', 'aktivasi', 'telkomsel', 'jawa tengah - diy', 'Aktivasi Perdana Telkomsel Jawa Tengah - DIY InternetMAX Lite 3 GB 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 20010, 21010, 20510, 20810, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(18, 'Aktivasi-Smartfren2gb', 'Aktivasi Perdana Smartfren Unlimited Nonstop 2 GB 10 Hari', 'aktivasi', 'smartfren', 'unlimited nonstop', '2 GB Utama 24 jam, akses nonstop, gratis nelpon semua smartfren.', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 12843, 13843, 13343, 13643, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(19, 'aov', 'AOV 7 Vouchers', 'game', 'arena of valor', 'umum', 'AOV 7 Vouchers', 'https://sakurupiah.my.id/assets/img/produk/arenaOfValor.jpg', 1900, 2900, 2400, 2700, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(20, 'AU', 'AU2 72 Diamonds', 'game', 'au2 mobile', 'umum', 'AU2 Mobile 72 Diamonds', 'https://sakurupiah.my.id/assets/img/produk/au2Dance.jpg', 14350, 15350, 14850, 15150, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(21, 'Axis-10', 'Axis 10.000', 'pulsa', 'axis', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/axis-perdana.png', 10820, 11820, 11320, 11000, 'On', 'normal', '2025-06-30 16:47:03', 'prabayar'),
(22, 'Axis-15', 'Axis 15.000', 'pulsa', 'axis', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/axis-perdana.png', 14980, 15980, 15480, 15780, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(23, 'Axis-5', 'Axis 5.000', 'pulsa', 'axis', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/axis-perdana.png', 5825, 6825, 6325, 6625, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(24, 'Axis100', 'Axis 100.000', 'pulsa', 'axis', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/axis-perdana.png', 99195, 100195, 99695, 99995, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(25, 'BAKULAN-ELEKTRIK', 'Honor of Kings 8 Tokens', 'game', 'honor of kings', 'umum', 'Token Utama, bonus sesuai akun pengguna', 'https://sakurupiah.my.id/assets/img/produk/honor-of-kings.jpg', 1375, 2375, 1875, 2175, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(26, 'BANGJEFF', 'Astral Guardians 90 Diamonds', 'game', 'astral guardians', 'umum', 'Masukkan User ID dan Nama Server', 'https://sakurupiah.my.id/assets/img/produk/astral-guardians.jpg', 15754, 16754, 16254, 16554, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(27, 'BRIZZ1', 'BRIZZI 20.000', 'emoney', 'bri brizzi', 'umum', 'Saldo BRIZZI 20.000. Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/brizii.png', 20125, 21125, 20625, 20925, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(28, 'BRIZZ2', 'BRIZZI 50.000', 'emoney', 'bri brizzi', 'umum', 'Saldo BRIZZI 50.000. Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/brizii.png', 50150, 51150, 50650, 50950, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(29, 'BRIZZ3', 'BRIZZI 100.000', 'emoney', 'bri brizzi', 'umum', 'Saldo BRIZZI 100.000. Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/brizii.png', 100150, 101150, 100650, 100950, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(30, 'BRIZZ4', 'BRIZZI 150.000', 'emoney', 'bri brizzi', 'umum', 'Saldo BRIZZI 150.000. Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/brizii.png', 150300, 151300, 150800, 151100, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(31, 'BRONET3gb', 'Axis Data BRONET 3 GB 30 Hari', 'paket_data', 'axis', 'bronet', 'AIGO Bronet 24Jam 3GB + Kuota di Kota-mu 30hr', 'https://sakurupiah.my.id/assets/img/produk/axis-perdana.png', 16060, 17060, 16560, 16860, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(32, 'BRONET8gb', 'Axis Data BRONET 8 GB 30 Hari', 'paket_data', 'axis', 'bronet', 'AIGO Bronet 24Jam 8GB + Kuota di Kota-mu 30hr', 'https://sakurupiah.my.id/assets/img/produk/axis-perdana.png', 35075, 37075, 35575, 36100, 'On', 'normal', '2025-06-30 15:58:12', 'prabayar'),
(33, 'BTKAS60', 'Be The King AS 60 Gold', 'game', 'be the king', 'region as', 'Masukkan gabungan User Id dan Server. Contoh jika User Id adalah 9999999999 dan Server S1, maka tujuan diisi 9999999999S1', 'https://sakurupiah.my.id/assets/img/produk/be-the-king.jpg', 13660, 14660, 14160, 14460, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(34, 'Bukalapak20', 'Bukalapak 20.000', 'emoney', 'bukalapak', 'umum', 'Saldo Bukalapak 20.000, input nomor hp terdaftar di Bukalapak.', 'https://sakurupiah.my.id/assets/img/produk/bukalapak.png', 21250, 22250, 21750, 22050, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(35, 'byu15', 'by.U 15.000', 'pulsa', 'by.u', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/byu-perdana.png', 15035, 16035, 15535, 15835, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(36, 'byu5', 'by.U 5.000', 'pulsa', 'by.u', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/byu-perdana.png', 5090, 6090, 5590, 5890, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(37, 'DANA1', 'DANA 10.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 10105, 11105, 10605, 10905, 'On', 'normal', '2025-06-30 15:47:58', 'prabayar'),
(38, 'DANA10', 'DANA 40.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 40175, 41175, 40675, 40975, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(39, 'DANA11', 'DANA 50.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 50250, 51250, 50750, 51050, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(40, 'DANA12', 'DANA 70.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 70175, 71175, 70675, 70975, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(41, 'DANA13', 'DANA 75.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 75175, 76175, 75675, 75975, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(42, 'DANA14', 'DANA 100.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 100100, 101100, 100600, 100900, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(43, 'DANA2', 'DANA 11.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 11225, 12225, 11725, 12025, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(44, 'DANA3', 'DANA 12.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 12225, 13225, 12725, 13025, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(45, 'DANA5', 'DANA 15.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 15260, 16260, 15760, 16060, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(46, 'DANA6', 'DANA 20.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 20110, 21110, 20610, 20910, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(47, 'DANA7', 'DANA 25.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 25110, 26110, 25610, 25910, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(48, 'DANA8', 'DANA 30.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 30125, 31125, 30625, 30925, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(49, 'DANA9', 'DANA 35.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 35235, 36235, 35735, 36035, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(50, 'DANAv4', 'DANA 13.000', 'emoney', 'dana', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/DANA.png', 13225, 14225, 13725, 14025, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(51, 'E-Toll1', 'Mandiri E-Toll 20.000', 'emoney', 'mandiri e-toll', 'umum', 'Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/etool.jpg', 20360, 21360, 20860, 21160, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(52, 'E-Toll10', 'Mandiri E-Toll 400.000', 'emoney', 'mandiri e-toll', 'umum', 'Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/etool.jpg', 400990, 401990, 401490, 401790, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(53, 'E-Toll2', 'Mandiri E-Toll 25.000', 'emoney', 'mandiri e-toll', 'umum', 'Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/etool.jpg', 26260, 27260, 26760, 27060, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(54, 'E-Toll3', 'Mandiri E-Toll 30.000', 'emoney', 'mandiri e-toll', 'umum', 'Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/etool.jpg', 31275, 32275, 31775, 32075, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(55, 'E-Toll4', 'Mandiri E-Toll 40.000', 'emoney', 'mandiri e-toll', 'umum', 'Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/etool.jpg', 41275, 42275, 41775, 42075, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(56, 'E-Toll5', 'Mandiri E-Toll 50.000', 'emoney', 'mandiri e-toll', 'umum', 'Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/etool.jpg', 50375, 51375, 50875, 51175, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(57, 'E-Toll6', 'Mandiri E-Toll 75.000', 'emoney', 'mandiri e-toll', 'umum', 'Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/etool.jpg', 75525, 76525, 76025, 76325, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(58, 'E-Toll7', 'Mandiri E-Toll 100.000', 'emoney', 'mandiri e-toll', 'umum', 'Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/etool.jpg', 100375, 101375, 100875, 101175, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(59, 'E-Toll8', 'Mandiri E-Toll 200.000', 'emoney', 'mandiri e-toll', 'umum', 'Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/etool.jpg', 202025, 203025, 202525, 202825, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(60, 'E-Toll9', 'Mandiri E-Toll 300.000', 'emoney', 'mandiri e-toll', 'umum', 'Wajib update saldo setelah pengisian sukses.', 'https://sakurupiah.my.id/assets/img/produk/etool.jpg', 302025, 303025, 302525, 302825, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(61, 'Fire1', 'Free Fire 5 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 826, 1826, 1326, 1626, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(62, 'Fire10', 'Free Fire 55 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 7584, 8584, 8084, 8384, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(63, 'Fire12', 'Free Fire 70 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 8495, 9495, 8995, 9295, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(64, 'Fire13', 'Free Fire 75 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 9332, 10332, 9532, 10132, 'On', 'normal', '2025-06-30 15:48:28', 'prabayar'),
(65, 'Fire15', 'Free Fire 90 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 11669, 12669, 12169, 12469, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(66, 'Fire16', 'Free Fire 95 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 12606, 13606, 13106, 13406, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(67, 'Fire17', 'Free Fire 100 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 13346, 14346, 13846, 14146, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(68, 'Fire18', 'Free Fire 120 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 14778, 15778, 15278, 15578, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(69, 'Fire19', 'Free Fire 130 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 17108, 18108, 17608, 17508, 'On', 'gangguan', '2025-06-30 15:48:56', 'prabayar'),
(70, 'Fire20', 'Free Fire 140 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 16984, 17984, 17484, 17784, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(71, 'Fire33', 'Free Fire 180 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 22829, 23829, 23329, 23629, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(72, 'Fire34', 'Free Fire 190 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 24105, 25105, 24605, 24905, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(73, 'Fire36', 'Free Fire 405 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 49518, 50518, 50018, 50318, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(74, 'Fire37', 'Free Fire 500 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 64752, 65752, 65252, 65552, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(75, 'Fire38', 'Free Fire 545 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 75600, 76600, 76100, 76400, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(76, 'Fire39', 'Free Fire 565 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 69218, 70218, 69718, 70018, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(77, 'Fire41', 'Free Fire 80 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 10110, 12110, 10610, 10910, 'On', 'normal', '2025-06-30 15:55:45', 'prabayar'),
(78, 'Fire6', 'Free Fire 25 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 4505, 5505, 5005, 5305, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(79, 'Fire7', 'Free Fire 30 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 4691, 5691, 5191, 5491, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(80, 'Fire8', 'Free Fire 40 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 6805, 7805, 7305, 7605, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(81, 'Fire9', 'Free Fire 50 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 6254, 7254, 6754, 7054, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(82, 'FOOTB18FMP', 'Football Master 2 18 FMP', 'game', 'football master 2', 'umum', 'Masukkan User ID.', 'https://sakurupiah.my.id/assets/img/produk/football-master.png', 3064, 4064, 3564, 3864, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(83, 'Free-Fire12', 'Free Fire 12 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 1880, 2880, 2380, 2680, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(84, 'Free-Fire140', 'Free Fire 140 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 18081, 19081, 18581, 18881, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(85, 'Free-Fire20', 'Free Fire 20 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 3129, 4129, 3629, 3929, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(86, 'Free-Fire5', 'Free Fire 5 Diamond', 'game', 'free fire', 'umum', 'Jumlah diamond sesuai diamond normal, bonus tidak dihitung', 'https://sakurupiah.my.id/assets/img/produk/free-fire.jpg', 842, 1842, 1342, 1642, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(87, 'Garena1', 'Garena 33 Shell', 'game', 'garena', 'umum', 'Tujuan = ID garena', 'https://sakurupiah.my.id/assets/img/produk/garena.png', 9155, 10155, 9655, 9955, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(88, 'Garena2', 'Garena 66 Shell', 'game', 'garena', 'umum', 'Tujuan = ID garena', 'https://sakurupiah.my.id/assets/img/produk/garena.png', 18210, 19210, 18710, 19010, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(89, 'Garena3', 'Garena 165 Shell', 'game', 'garena', 'umum', 'Tujuan = ID garena', 'https://sakurupiah.my.id/assets/img/produk/garena.png', 45325, 46325, 45825, 46125, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(90, 'Garena4', 'Garena 330 Shell', 'game', 'garena', 'umum', 'Tujuan = ID garena', 'https://sakurupiah.my.id/assets/img/produk/garena.png', 92025, 93025, 92525, 92825, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(91, 'Grab1', 'Grab penumpang 20.000', 'emoney', 'grab', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/grab.png', 20985, 21985, 21485, 21785, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(92, 'Grab2', 'Grab penumpang 25.000', 'emoney', 'grab', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/grab.png', 25985, 26985, 26485, 26785, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(93, 'Grab3', 'Grab penumpang 40.000', 'emoney', 'grab', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/grab.png', 41050, 42050, 41550, 41850, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(94, 'Grab4', 'Grab penumpang 50.000', 'emoney', 'grab', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/grab.png', 51050, 52050, 51550, 51850, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(95, 'Grab5', 'Grab penumpang 75.000', 'emoney', 'grab', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/grab.png', 76050, 77050, 76550, 76850, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(96, 'Grab6', 'Grab penumpang 100.000', 'emoney', 'grab', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/grab.png', 101050, 102050, 101550, 101850, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(97, 'Grab7', 'Grab penumpang 150.000', 'emoney', 'grab', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/grab.png', 151050, 152050, 151550, 151850, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(98, 'Grab8', 'Grab penumpang 200.000', 'emoney', 'grab', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/grab.png', 201125, 202125, 201625, 201925, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(99, 'Grab9', 'Grab penumpang 500.000', 'emoney', 'grab', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/grab.png', 500825, 501825, 501325, 501625, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(100, 'Hago1', 'Hago 5 Diamonds', 'game', 'hago', 'umum', 'Masukkan Player ID Hago Anda', 'https://sakurupiah.my.id/assets/img/produk/hago.png', 1726, 2726, 2226, 2526, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(101, 'Indosa-100', 'Indosat 100.000', 'pulsa', 'indosat', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 98098, 99098, 98598, 98898, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(102, 'Indosa-20', 'Indosat 20.000', 'pulsa', 'indosat', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 20500, 21500, 21000, 21300, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(103, 'Indosa-25', 'Indosat 25.000', 'pulsa', 'indosat', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 25350, 26350, 25850, 26150, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(104, 'Indosa-5', 'Indosat 5.000', 'pulsa', 'indosat', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 5835, 6835, 6335, 6635, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(105, 'Indosa-50', 'Indosat 50.000', 'pulsa', 'indosat', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 49119, 50119, 49619, 49919, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(106, 'K-Vision', 'K-Vision & GOL Paket CLING (CL01)  30 Hari', 'tv', 'k-vision dan gol', 'cling', 'Siaran National Geographic, Nat Geo Wild, My Family, My Cinema, MTV, Rock , Kids TV, dll', 'https://sakurupiah.my.id/assets/img/produk/tv.png', 19794, 20794, 20294, 20594, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(107, 'Laplace', 'Laplace M 60 Spirals', 'game', 'laplace m', 'umum', 'Laplace M 60 Spirals', 'https://sakurupiah.my.id/assets/img/produk/laplaceMm.png', 14105, 15105, 14605, 14905, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(108, 'LinkAja4', 'LinkAja Rp 40.000', 'emoney', 'linkaja', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/LINKAJA.png', 40210, 41210, 40710, 41010, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(109, 'LinkAja5', 'LinkAja Rp 50.000', 'emoney', 'linkaja', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/LINKAJA.png', 50210, 51210, 50710, 51010, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(110, 'LinkAja6', 'LinkAja Rp 60.000', 'emoney', 'linkaja', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/LINKAJA.png', 60210, 61210, 60710, 61010, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(111, 'LinkAja7', 'LinkAja Rp 65.000', 'emoney', 'linkaja', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/LINKAJA.png', 65350, 66350, 65850, 66150, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(112, 'LinkAja8', 'LinkAja Rp 80.000', 'emoney', 'linkaja', 'umum', '-', 'https://sakurupiah.my.id/assets/img/produk/LINKAJA.png', 80210, 81210, 80710, 81010, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(113, 'Lord', 'Lords Mobile 67 Diamonds', 'game', 'lords mobile', 'umum', 'Lords Mobile 67 Diamonds', 'https://sakurupiah.my.id/assets/img/produk/lords-mobile.jpg', 9905, 10905, 10405, 10705, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(114, 'MangaToon', 'MangaToon 100 Coins', 'game', 'mangatoon', 'umum', 'MangaToon 100 Coins', 'https://sakurupiah.my.id/assets/img/produk/mangatoon.jpg', 16500, 17500, 17000, 17300, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(115, 'masaktif-Telkomsel5hri', 'Telkomsel Tambah Masa Aktif Kartu 5 Hari', 'masa_aktif', 'telkomsel', 'umum', 'Telkomsel Tambah Masa Aktif Kartu 5 Hari', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 2363, 3363, 2863, 3163, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(116, 'MOBILELEGEND1', 'MOBILELEGEND - 3 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 1200, 2200, 1700, 2000, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(117, 'MOBILELEGEND10', 'MOBILELEGEND - 36 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 9621, 10621, 10121, 10421, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(118, 'MOBILELEGEND100', 'MOBILELEGEND - 6050 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 1484400, 1485400, 1484900, 1485200, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(119, 'MOBILELEGEND11', 'MOBILELEGEND - 42 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 9629, 10629, 10129, 10429, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(120, 'MOBILELEGEND111', 'MOBILELEGEND - 5532 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 1351000, 1352000, 1351500, 1351800, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(121, 'MOBILELEGEND12', 'MOBILELEGEND - 44 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 11005, 12005, 11505, 11805, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(122, 'MOBILELEGEND13', 'MOBILELEGEND - 45 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 12565, 13565, 13065, 13365, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(123, 'MOBILELEGEND14', 'MOBILELEGEND - 56 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 13000, 14000, 13500, 13800, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(124, 'MOBILELEGEND15', 'MOBILELEGEND - 59 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 15370, 16370, 15870, 16170, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(125, 'MOBILELEGEND16', 'MOBILELEGEND - 60 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 16291, 17291, 16791, 17091, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(126, 'MOBILELEGEND17', 'MOBILELEGEND - 70 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 16090, 17090, 16590, 16890, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(127, 'MOBILELEGEND18', 'MOBILELEGEND - 74 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 20500, 21500, 21000, 21300, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(128, 'MOBILELEGEND19', 'MOBILELEGEND - 85 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 22327, 23327, 22827, 23127, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(129, 'MOBILELEGEND20', 'MOBILELEGEND - 86 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 18915, 19915, 19415, 19715, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(130, 'MOBILELEGEND21', 'MOBILELEGEND - 100 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 26212, 27212, 26712, 27012, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(131, 'MOBILELEGEND22', 'MOBILELEGEND - 112 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 25149, 26149, 25649, 25949, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(132, 'MOBILELEGEND25', 'MOBILELEGEND - 170 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 44159, 45159, 44659, 44300, 'On', 'normal', '2025-06-30 15:49:45', 'prabayar'),
(133, 'MOBILELEGEND26', 'MOBILELEGEND - 172 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 37515, 38515, 38015, 38315, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(134, 'MOBILELEGEND27', 'MOBILELEGEND - 185 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 40952, 41952, 41452, 41752, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(135, 'MOBILELEGEND28', 'MOBILELEGEND - 222 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 50692, 51692, 51192, 51492, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(136, 'MOBILELEGEND29', 'MOBILELEGEND - 240 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 53700, 54700, 54200, 54500, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(137, 'MOBILELEGEND3', 'MOBILELEGEND - 5 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 1785, 2785, 2285, 2585, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(138, 'MOBILELEGEND30', 'MOBILELEGEND - 257 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 56395, 57395, 56895, 57195, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(139, 'MOBILELEGEND31', 'MOBILELEGEND - 284 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 62400, 63400, 62900, 63200, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(140, 'MOBILELEGEND32', 'MOBILELEGEND - 296 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 66198, 67198, 66698, 66998, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(141, 'MOBILELEGEND33', 'MOBILELEGEND - 344 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 74980, 75980, 75480, 75780, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(142, 'MOBILELEGEND333', 'MOBILELEGEND - 4830 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 1136539, 1137539, 1137039, 1137339, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(143, 'MOBILELEGEND34', 'MOBILELEGEND - 355 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 91708, 92708, 92208, 92508, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(144, 'MOBILELEGEND35', 'MOBILELEGEND - 370 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 94852, 95852, 95352, 95652, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(145, 'MOBILELEGEND36', 'MOBILELEGEND - 408 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 99795, 100795, 100295, 100595, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(146, 'MOBILELEGEND37', 'MOBILELEGEND - 429 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 93860, 94860, 94360, 94660, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(147, 'MOBILELEGEND38', 'MOBILELEGEND - 514 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 134360, 135360, 134860, 135160, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(148, 'MOBILELEGEND39', 'MOBILELEGEND - 568 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 120625, 121625, 121125, 121425, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(149, 'MOBILELEGEND4', 'MOBILELEGEND - 12 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 3775, 4775, 4275, 4575, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(150, 'MOBILELEGEND40', 'MOBILELEGEND - 600 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 152300, 153300, 152800, 153100, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(151, 'MOBILELEGEND5', 'MOBILELEGEND - 14 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 3220, 4220, 3720, 4020, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(152, 'MOBILELEGEND55', 'MOBILELEGEND - 1412 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 300950, 301950, 301450, 301750, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(153, 'MOBILELEGEND6', 'MOBILELEGEND - 19 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 5276, 6276, 5776, 6076, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(154, 'MOBILELEGEND66', 'MOBILELEGEND - 1220 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 264070, 265070, 264570, 264870, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(155, 'MOBILELEGEND7', 'MOBILELEGEND - 28 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 6410, 7410, 6910, 7210, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(156, 'MOBILELEGEND706', 'MOBILELEGEND - 706 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 179290, 180290, 179790, 180090, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(157, 'MOBILELEGEND777', 'MOBILELEGEND - 1000 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 246721, 247721, 247221, 247521, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(158, 'MOBILELEGEND8', 'MOBILELEGEND - 30 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 8189, 9189, 8689, 8989, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(159, 'MOBILELEGEND9', 'MOBILELEGEND - 33 Diamond', 'game', 'mobile legends', 'umum', 'no pelanggan = gabungan antara user_id dan zone_id', 'https://sakurupiah.my.id/assets/img/produk/mobile-legends.png', 9066, 10066, 9566, 9866, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(160, 'mytyy', 'Maxis 5', 'luar_negeri', 'maxis', 'umum', 'Maxis 5', 'https://sakurupiah.my.id/assets/img/produk/new-product.png', 21435, 22435, 21935, 22235, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(161, 'Nelponxl50Menit', 'XL Nelpon Sesama 350 Menit + Operator Lain 50 Menit - 7 Hari', 'telepon_sms', 'xl', 'umum', 'AnyNet Nelp 350Mnt+50Mnt(Offnet), 7hr', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 11250, 12250, 11750, 12050, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(162, 'OVO1', 'OVO 10.000', 'emoney', 'ovo', 'umum', 'OVO 10.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 9224, 10224, 9724, 10024, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(163, 'OVO10', 'OVO 60.000', 'emoney', 'ovo', 'umum', 'OVO 60.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 60750, 61750, 61250, 61550, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(164, 'OVO11', 'OVO 70.000', 'emoney', 'ovo', 'umum', 'OVO 70.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 70750, 71750, 71250, 71550, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(165, 'OVO12', 'OVO 80.000', 'emoney', 'ovo', 'umum', 'OVO 80.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 80750, 81750, 81250, 81550, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(166, 'OVO13', 'OVO 90.000', 'emoney', 'ovo', 'umum', 'OVO 90.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 90750, 91750, 91250, 91550, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(167, 'OVO14', 'OVO 100.000', 'emoney', 'ovo', 'umum', 'OVO 100.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 100750, 101750, 101250, 101550, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(168, 'OVO15', 'OVO 150.000', 'emoney', 'ovo', 'umum', 'OVO 150.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 157500, 158500, 158000, 158300, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(169, 'OVO2', 'OVO 15.000', 'emoney', 'ovo', 'umum', 'OVO 15.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 15750, 16750, 16250, 16550, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(170, 'OVO3', 'OVO 20.000', 'emoney', 'ovo', 'umum', 'OVO 20.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 20750, 21750, 21250, 21550, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(171, 'OVO4', 'OVO 25.000', 'emoney', 'ovo', 'umum', 'OVO 25.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 25750, 26750, 26250, 26550, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(172, 'OVO5', 'OVO 30.000', 'emoney', 'ovo', 'umum', 'OVO 30.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 30750, 31750, 31250, 31550, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(173, 'OVO6', 'OVO 35.000', 'emoney', 'ovo', 'umum', 'OVO 35.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 35750, 36750, 36250, 36550, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(174, 'OVO7', 'OVO 40.000', 'emoney', 'ovo', 'umum', 'OVO 40.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 40750, 41750, 41250, 41550, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(175, 'OVO8', 'OVO 45.000', 'emoney', 'ovo', 'umum', 'OVO 45.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 45750, 46750, 46250, 46550, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(176, 'OVO9', 'OVO 50.000', 'emoney', 'ovo', 'umum', 'OVO 50.000', 'https://sakurupiah.my.id/assets/img/produk/OVO.png', 50750, 51750, 51250, 51550, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(177, 'Parabola3501', 'Nex Parabola Paket Premium Sports 30 Hari (3501)', 'tv', 'nex parabola', 'umum', 'Hiburan Keluarga & Olahraga + 4 Channel TV National Berbayar', 'https://sakurupiah.my.id/assets/img/produk/tv.png', 203810, 204810, 204310, 204610, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(178, 'Pay1', 'Go Pay 10.000', 'emoney', 'go pay', 'customer', 'Masukan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 10655, 11655, 11155, 11455, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(179, 'Pay10', 'Go Pay 55.000', 'emoney', 'go pay', 'customer', 'Masukan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 55695, 56695, 56195, 56495, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(180, 'Pay11', 'Go Pay 60.000', 'emoney', 'go pay', 'customer', 'Masukkan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 60695, 61695, 61195, 61495, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(181, 'Pay12', 'Go Pay 65.000', 'emoney', 'go pay', 'customer', 'Masukkan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 65695, 66695, 66195, 66495, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(182, 'Pay13', 'Go Pay 70.000', 'emoney', 'go pay', 'customer', 'Masukan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 70775, 71775, 71275, 71575, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(183, 'Pay14', 'Go Pay 75.000', 'emoney', 'go pay', 'customer', 'Masukan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 75825, 76825, 76325, 76625, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(184, 'Pay15', 'Go Pay 80.000', 'emoney', 'go pay', 'customer', 'Masukkan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 80825, 81825, 81325, 81625, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(185, 'Pay16', 'Go Pay 85.000', 'emoney', 'go pay', 'customer', 'Masukkan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 85825, 86825, 86325, 86625, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(186, 'Pay17', 'Go Pay 90.000', 'emoney', 'go pay', 'customer', 'Masukkan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 90825, 91825, 91325, 91625, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(187, 'Pay18', 'Go Pay 95.000', 'emoney', 'go pay', 'customer', 'Masukkan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 95825, 96825, 96325, 96625, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(188, 'Pay19', 'Go Pay 100.000', 'emoney', 'go pay', 'customer', 'Masukan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 100825, 101825, 101325, 101625, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(189, 'Pay2', 'Go Pay 15.000', 'emoney', 'go pay', 'customer', 'Masukan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 15775, 16775, 16275, 16575, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(190, 'Pay3', 'Go Pay 20.000', 'emoney', 'go pay', 'customer', 'Masukan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 20660, 21660, 21160, 21460, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(191, 'Pay4', 'Go Pay 25.000', 'emoney', 'go pay', 'customer', 'Masukan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 25825, 26825, 26325, 26625, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(192, 'Pay5', 'Go Pay 30.000', 'emoney', 'go pay', 'customer', 'Masukan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 30825, 31825, 31325, 31625, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(193, 'Pay6', 'Go Pay 35.000', 'emoney', 'go pay', 'customer', 'Masukan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 35825, 36825, 36325, 36625, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(194, 'Pay7', 'Go Pay 40.000', 'emoney', 'go pay', 'customer', 'Masukan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 40675, 41675, 41175, 41475, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(195, 'Pay9', 'Go Pay 50.000', 'emoney', 'go pay', 'customer', 'Masukan no HP', 'https://sakurupiah.my.id/assets/img/produk/GOPAY.png', 50825, 51825, 51325, 51625, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(196, 'pb', '1.200 PB Cash', 'game', 'point blank', 'umum', '1200 Point Blank Cash', 'https://sakurupiah.my.id/assets/img/produk/point-blank.jpg', 9700, 10700, 10200, 10500, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(197, 'Pertagas20K', 'Pertagas 20.000', 'gas', 'pertamina gas', 'umum', 'Pertagas 20.000', 'https://sakurupiah.my.id/assets/img/produk/pertamina.png', 21710, 22710, 22210, 22510, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(198, 'Pertagas50K', 'Pertagas 50.000', 'gas', 'pertamina gas', 'umum', 'Pertagas 50.000', 'https://sakurupiah.my.id/assets/img/produk/pertamina.png', 51725, 52725, 52225, 52525, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(199, 'PLN', 'PLN 5.000', 'pln', 'pln', 'umum', 'masukkan nomor meter/id pelanggan', 'https://sakurupiah.my.id/assets/img/produk/pln.png', 6705, 7705, 7205, 6999, 'On', 'gangguan', '2025-06-30 15:54:34', 'prabayar'),
(200, 'PLN10', 'PLN 10.000', 'pln', 'pln', 'umum', 'masukkan nomor meter/id pelanggan', 'https://sakurupiah.my.id/assets/img/produk/pln.png', 11655, 12655, 12155, 12455, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(201, 'PLN1015', 'PLN 15.000', 'pln', 'pln', 'umum', 'masukkan nomor meter/id pelanggan', 'https://sakurupiah.my.id/assets/img/produk/pln.png', 16570, 17570, 17070, 17370, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar');
INSERT INTO `layanan` (`id`, `kode_produk`, `nama_produk`, `kategori`, `brand`, `tipe`, `deskripsi`, `logo`, `harga_asli`, `harga_jual`, `harga_reseller`, `harga_diskon`, `tipe_diskon`, `status`, `expired_diskon`, `tipe_produk`) VALUES
(202, 'PLN1020', 'PLN 20.000', 'pln', 'pln', 'umum', 'masukkan nomor meter/id pelanggan', 'https://sakurupiah.my.id/assets/img/produk/pln.png', 20565, 21565, 21065, 21000, 'On', 'normal', '2025-06-30 15:52:26', 'prabayar'),
(203, 'PUBG1', 'PUBG MOBILE 16 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 16 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 7995, 8995, 8495, 8795, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(204, 'PUBG10', 'PUBG MOBILE 131 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 131 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 40529, 41529, 41029, 41329, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(205, 'PUBG11', 'PUBG MOBILE 150 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 150 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 36043, 37043, 36543, 36843, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(206, 'PUBG12', 'PUBG MOBILE 156 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 156 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 40544, 41544, 41044, 41344, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(207, 'PUBG13', 'PUBG MOBILE 210 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 210 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 47470, 48470, 47970, 48270, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(208, 'PUBG15', 'PUBG MOBILE 263 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 263 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 67556, 68556, 68056, 68356, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(209, 'PUBG16', 'PUBG MOBILE 500 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 500 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 113425, 114425, 113925, 114225, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(210, 'PUBG17', 'PUBG MOBILE 700 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 700 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 153625, 154625, 154125, 154425, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(211, 'PUBG18', 'PUBG MOBILE 750 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 750 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 163174, 164174, 163674, 163974, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(212, 'PUBG19', 'PUBG MOBILE 825 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 825 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 176680, 177680, 177180, 177480, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(213, 'PUBG2', 'PUBG MOBILE 26 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 26 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 11995, 12995, 12495, 12795, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(214, 'PUBG20', 'PUBG MOBILE 1100 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 1100 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 231173, 232173, 231673, 231973, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(215, 'PUBG21', 'PUBG MOBILE 1250 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 1250 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 247025, 248025, 247525, 247825, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(216, 'PUBG22', 'PUBG MOBILE 1750 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 1750 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 341398, 342398, 341898, 342198, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(217, 'PUBG23', 'PUBG MOBILE 2500 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 2500 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 452500, 453500, 453000, 453300, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(218, 'PUBG24', 'PUBG MOBILE 3500 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 3500 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 682909, 683909, 683409, 683709, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(219, 'PUBG25', 'PUBG MOBILE 5000 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 5000 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 927564, 928564, 928064, 928364, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(220, 'PUBG3', 'PUBG MOBILE 35 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 35 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 14010, 15010, 14510, 14810, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(221, 'PUBG4', 'PUBG MOBILE 50 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 50 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 13760, 14760, 14260, 14560, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(222, 'PUBG5', 'PUBG MOBILE 70 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 70 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 12670, 13670, 13170, 13470, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(223, 'PUBG6', 'PUBG MOBILE 100 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 100 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 24670, 25670, 25170, 25470, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(224, 'PUBG7', 'PUBG MOBILE 105 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 105 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 27022, 28022, 27522, 27822, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(225, 'PUBG9', 'PUBG MOBILE 125 UC', 'game', 'pubg mobile', 'umum', 'PUBG MOBILE 125 UC', 'https://sakurupiah.my.id/assets/img/produk/pubG.png', 31626, 32626, 32126, 32426, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(226, 'SHOPEE1', 'SHOPEE PAY 10.000', 'emoney', 'shopee pay', 'umum', 'SHOPEE PAY 10.000', 'https://sakurupiah.my.id/assets/img/produk/SHOPPEPAY.png', 10165, 11165, 10665, 10965, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(227, 'SHOPEE2', 'SHOPEE PAY 15.000', 'emoney', 'shopee pay', 'umum', 'SHOPEE PAY 15.000', 'https://sakurupiah.my.id/assets/img/produk/SHOPPEPAY.png', 15175, 16175, 15675, 15975, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(228, 'SHOPEE3', 'SHOPEE PAY 20.000', 'emoney', 'shopee pay', 'umum', 'SHOPEE PAY 20.000', 'https://sakurupiah.my.id/assets/img/produk/SHOPPEPAY.png', 20185, 21185, 20685, 20985, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(229, 'SHOPEE4', 'SHOPEE PAY 25.000', 'emoney', 'shopee pay', 'umum', 'SHOPEE PAY 25.000', 'https://sakurupiah.my.id/assets/img/produk/SHOPPEPAY.png', 25185, 26185, 25685, 25985, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(230, 'SHOPEE5', 'SHOPEE PAY 50.000', 'emoney', 'shopee pay', 'umum', 'SHOPEE PAY 50.000', 'https://sakurupiah.my.id/assets/img/produk/SHOPPEPAY.png', 50245, 51245, 50745, 51045, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(231, 'SHOPEE6', 'SHOPEE PAY 75.000', 'emoney', 'shopee pay', 'umum', 'SHOPEE PAY 75.000', 'https://sakurupiah.my.id/assets/img/produk/SHOPPEPAY.png', 75275, 76275, 75775, 76075, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(232, 'SHOPEE7', 'SHOPEE PAY 100.000', 'emoney', 'shopee pay', 'umum', 'SHOPEE PAY 100.000', 'https://sakurupiah.my.id/assets/img/produk/SHOPPEPAY.png', 100295, 101295, 100795, 101095, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(233, 'Smartfren-10', 'Smartfren 10.000', 'pulsa', 'smartfren', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 9705, 10705, 10205, 10505, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(234, 'Smartfren-100', 'Smartfren 100.000', 'pulsa', 'smartfren', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 95770, 96770, 96270, 96570, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(235, 'Smartfren-15', 'Smartfren 15.000', 'pulsa', 'smartfren', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 14588, 15588, 15088, 15388, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(236, 'Smartfren-20', 'Smartfren 20.000', 'pulsa', 'smartfren', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 19860, 20860, 20360, 20660, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(237, 'Smartfren-25', 'Smartfren 25.000', 'pulsa', 'smartfren', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 24485, 25485, 24985, 25285, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(238, 'Smartfren-5', 'Smartfren 5.000', 'pulsa', 'smartfren', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 4860, 5860, 5360, 5660, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(239, 'Smartfren-50', 'Smartfren 50.000', 'pulsa', 'smartfren', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 48060, 49060, 48560, 48860, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(240, 'Smartfren-500', 'Smartfren 500.000', 'pulsa', 'smartfren', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 488775, 489775, 489275, 489575, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(241, 'Smartfren-60', 'Smartfren 60.000', 'pulsa', 'smartfren', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 58610, 59610, 59110, 59410, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(242, 'Smartfren1', 'Smartfren Internet 10.000', 'paket_data', 'smartfren', 'umum', 'Kuota Internet sebesar 10rb selama 7 hari', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 10993, 11993, 11493, 11793, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(243, 'Smartfren10', 'Smartfren Data 3 GB 5 Hari', 'paket_data', 'smartfren', 'umum', 'Smartfren Data 3 GB / 5 Hari', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 11710, 12710, 12210, 12510, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(244, 'Smartfren11', 'Smartfren Data 2 GB 7 Hari', 'paket_data', 'smartfren', 'umum', '1GB Kuota Utama + 1GB Kuota Chat', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 9870, 10870, 10370, 10670, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(245, 'Smartfren13', 'Smartfren Data 4 GB 14 Hari', 'paket_data', 'smartfren', 'umum', '2GB Regular + 2GB Malam', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 16655, 17655, 17155, 17455, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(246, 'Smartfren14', 'Smartfren Data 8 GB 14 Hari', 'paket_data', 'smartfren', 'umum', '4GB Regular + 4GB Malam', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 38000, 39000, 38500, 38800, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(247, 'Smartfren16', 'Smartfren Data 16 GB 30 Hari', 'paket_data', 'smartfren', 'umum', '8GB Regular + 8GB Malam', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 51225, 52225, 51725, 52025, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(248, 'Smartfren2', 'Smartfren Internet 20.000', 'paket_data', 'smartfren', 'umum', 'Kuota Internet sebesar 20rb selama 30 hari', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 18610, 19610, 19110, 19410, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(249, 'Smartfren3', 'Smartfren Internet 30.000', 'paket_data', 'smartfren', 'umum', 'Kuota Internet sebesar 30rb selama 30 hari', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 26625, 27625, 27125, 27425, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(250, 'Smartfren4', 'Smartfren Internet 100.000', 'paket_data', 'smartfren', 'umum', 'Kuota Internet sebesar 100rb selama 30 hari', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 100025, 101025, 100525, 100825, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(251, 'Smartfren5', 'Smartfren Internet 150.000', 'paket_data', 'smartfren', 'umum', 'Kuota Internet sebesar 150rb selama 30 hari', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 149025, 150025, 149525, 149825, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(252, 'Smartfren6', 'Smartfren Internet 200.000', 'paket_data', 'smartfren', 'umum', 'Kuota Internet sebesar 200.000 selama 30 hari', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 199025, 200025, 199525, 199825, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(253, 'Smartfren7', 'Smartfren Data 1 GB 3 Hari', 'paket_data', 'smartfren', 'umum', 'Smartfren Data 1 GB / 3 Hari', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 5055, 6055, 5555, 5855, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(254, 'Smartfren9', 'Smartfren Data 2 GB 3 Hari', 'paket_data', 'smartfren', 'umum', 'Smartfren Data 2 GB / 3 Hari', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 7805, 8805, 8305, 8605, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(255, 'streaming-vidio1', 'Vidio Platinum 30 Hari', 'lainnya', 'vidio', 'umum', 'Masukkan no hp yang terdaftar di Vidio', 'https://sakurupiah.my.id/assets/img/produk/new-product.png', 42400, 43400, 42900, 43200, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(256, 'streaming-WeTV25Coins', 'WeTV 25 Coins', 'lainnya', 'wetv', 'umum', 'Masukkan ID', 'https://sakurupiah.my.id/assets/img/produk/new-product.png', 3602, 4602, 4102, 4402, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(257, 'Telkomsel-10', 'Telkomsel 10.000', 'pulsa', 'telkomsel', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 10180, 11180, 10680, 10980, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(258, 'Telkomsel-100', 'Telkomsel 100.000', 'pulsa', 'telkomsel', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 99094, 100094, 99594, 99894, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(259, 'Telkomsel-15', 'Telkomsel 15.000', 'pulsa', 'telkomsel', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 14960, 15960, 15460, 15760, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(260, 'Telkomsel-150', 'Telkomsel 150.000', 'pulsa', 'telkomsel', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 145470, 146470, 145970, 146270, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(261, 'Telkomsel-20', 'Telkomsel 20.000', 'pulsa', 'telkomsel', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 19765, 20765, 20265, 20565, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(262, 'Telkomsel-200', 'Telkomsel 200.000', 'pulsa', 'telkomsel', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 193593, 194593, 194093, 194393, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(263, 'Telkomsel-25', 'Telkomsel 25.000', 'pulsa', 'telkomsel', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 24665, 25665, 25165, 25465, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(264, 'Telkomsel-30', 'Telkomsel 30.000', 'pulsa', 'telkomsel', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 29435, 30435, 29935, 30235, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(265, 'Telkomsel-res-5', 'Telkomsel 5.000', 'pulsa', 'telkomsel', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 5195, 6195, 5695, 5995, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(266, 'telkomsel-ress-5', 'Telkomsel 5.000', 'pulsa', 'telkomsel', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 5235, 6235, 5735, 6035, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(267, 'Telkomsel15', 'Telkomsel Data 3 GB + 12 GB Videomax / 30 Hari', 'paket_data', 'telkomsel', 'umum', 'Berlaku nasional, masa aktif 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 56020, 57020, 56520, 56820, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(268, 'Telkomsel3', 'Telkomsel Data 1 GB + 2 GB Game / 30 Hari', 'paket_data', 'telkomsel', 'umum', 'Flash 1GB + 2GB Game (30 hari)', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 23760, 24760, 24260, 24560, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(269, 'Telkomsel35', 'Telkomsel Data 7 GB + 28 GB Videomax / 30 Hari', 'paket_data', 'telkomsel', 'umum', 'Berlaku nasional, masa aktif 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 103225, 104225, 103725, 104025, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(270, 'Telkomsel5', 'Telkomsel Data 1 GB + 5 GB Ruang Guru / 30 Hari', 'paket_data', 'telkomsel', 'umum', 'masa aktif 30 hr', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 25525, 26525, 26025, 26325, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(271, 'Telkomsel6gb', 'Telkomsel Data 1 GB + 5 GB Videomax / 30 Hari', 'paket_data', 'telkomsel', 'umum', 'Berlaku nasional, masa aktif 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 26200, 27200, 26700, 27000, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(272, 'Three15', 'Three 15.000', 'pulsa', 'tri', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 15951, 16951, 16451, 16751, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(273, 'Three40', 'Three 40.000', 'pulsa', 'tri', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 39436, 40436, 39936, 40236, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(274, 'Three75', 'Three 75.000', 'pulsa', 'tri', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 73825, 74825, 74325, 74625, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(275, 'tlp-telkomsel50', 'Telkomsel Telepon 50.000', 'telepon_sms', 'telkomsel', 'umum', 'Telepon 1200 menit sesama, 100 menit semua op 15 Hari (sesuai zona)', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 45025, 46025, 45525, 45825, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(276, 'tlp-telkomsel80', 'Telkomsel Telepon 80.000', 'telepon_sms', 'telkomsel', 'umum', 'Telepon 2000 menit sesama, 100 menit semua op 30 Hari (sesuai zona)', 'https://sakurupiah.my.id/assets/img/produk/telkomsel-perdana.png', 60025, 61025, 60525, 60825, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(277, 'TOF60', 'Tower of Fantasy 60 Tanium', 'game', 'tower of fantasy', 'umum', 'Masukkan userid dan server', 'https://sakurupiah.my.id/assets/img/produk/toweroffantasy.png', 11873, 12873, 12373, 12673, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(278, 'Tri1', 'Tri Data 750 MB 14 Hari', 'paket_data', 'tri', 'pure 14 hari', '24 jam nasional', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 5155, 6155, 5655, 5955, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(279, 'Tri12', 'Tri Data 10 GB 30 Hari', 'paket_data', 'tri', 'pure 30 hari', 'Kuota 10GB ( 2G/3G/4G ) 24 JAM masa aktif 30 hari', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 39525, 40525, 40025, 40325, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(280, 'Tri13', 'Tri Data 15 GB / 30 Hari', 'paket_data', 'tri', 'umum', 'Kuota 15GB ( 2G/3G/4G ) 24 JAM masa aktif 30 hari', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 61626, 62626, 62126, 62426, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(281, 'Tri14', 'Tri Data 20 GB / 30 Hari', 'paket_data', 'tri', 'umum', 'Kuota 20GB ( 2G/3G/4G ) 24 JAM masa aktif 30 hari', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 68425, 69425, 68925, 69225, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(282, 'Tri15', 'Tri Data 33 GB 30 Hari', 'paket_data', 'tri', 'umum', '33 GB nasional 24 jam, 30 hari', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 97325, 98325, 97825, 98125, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(283, 'Tri16', 'Tri Data 22 GB + Unlimited YouTube dan VIU / 30 Hari', 'paket_data', 'tri', 'umum', 'cek di web https://tri.co.id/AOn-Unlimited-22GB', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 76175, 77175, 76675, 76975, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(284, 'Tri2', 'Tri Data 1 GB 14 Hari', 'paket_data', 'tri', 'pure 14 hari', '24 jam nasional', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 10705, 11705, 11205, 11505, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(285, 'Tri3', 'Tri Data 1.5 GB 14 Hari', 'paket_data', 'tri', 'pure 14 hari', '24 jam nasional', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 10255, 11255, 10755, 11055, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(286, 'Tri4', 'Tri Data 2.5 GB 14 Hari', 'paket_data', 'tri', 'pure 14 hari', '24 jam nasional', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 13950, 14950, 14450, 14750, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(287, 'Tri6', 'Tri Data 100 MB 30 Hari', 'paket_data', 'tri', 'pure 30 hari', 'Tri Data 100 MB / 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 660, 1660, 1160, 1460, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(288, 'Tri7', 'Tri Data 200 MB 30 Hari', 'paket_data', 'tri', 'pure 30 hari', 'Tri Data 200 MB / 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 1455, 2455, 1955, 2255, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(289, 'Tri8', 'Tri Data 500 MB 30 Hari', 'paket_data', 'tri', 'pure 30 hari', 'Tri Data 500 MB / 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 3705, 4705, 4205, 4505, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(290, 'Tri9', 'Tri Data 1 GB 30 Hari', 'paket_data', 'tri', 'pure 30 hari', 'Tri Data 1 GB / 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/3-perdana.png', 6540, 7540, 7040, 7340, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(291, 'Voc1', 'Voucher AIGO 1 GB / 30 Hari', 'voucher', 'axis', 'umum', '1 GB All Jaringan Berlaku 24 Jam Masa Aktif 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/axis-perdana.png', 13360, 14360, 13860, 14160, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(292, 'Voc2', 'Voucher AIGO 1 GB / 30 Hari', 'voucher', 'axis', 'umum', '1 GB All Jaringan Berlaku 24 Jam Masa Aktif 30 Hari', 'https://sakurupiah.my.id/assets/img/produk/axis-perdana.png', 15999, 16999, 16499, 16799, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(293, 'Voc3', 'Voucher AIGO 2 GB 30 Hari', 'voucher', 'axis', 'umum', 'AIGO Bronet 2GB + Kuota di Kota-mu 30hr', 'https://sakurupiah.my.id/assets/img/produk/axis-perdana.png', 23360, 24360, 23860, 24160, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(294, 'VocSmart', 'Voucher Smartfren Data 2.5 GB 7 Hari', 'voucher', 'smartfren', 'umum', '1.5GB Kuota Utama + 1GB Kuota Chat', 'https://sakurupiah.my.id/assets/img/produk/smartfren-perdana.png', 10870, 11870, 11370, 11670, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(295, 'vouchwer', 'Voucher Indosat 5.000', 'voucher', 'indosat', 'umum', 'Voucher pulsa indosat 5.000', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 5405, 6405, 5905, 6205, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(296, 'wweew', 'eSIM Indosat Freedom Internet 3 GB 28 Hari', 'esim', 'indosat', 'freedom internet', 'https://id.digiflazz.com/blog/post/penjelasan-esim-dan-cara-mengaktifkannya=all', 'https://sakurupiah.my.id/assets/img/produk/indosat-perdana.png', 39010, 40010, 39510, 39810, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(297, 'XL1', 'XL Data 100 MB 30 Hari', 'paket_data', 'xl', 'umum', 'DRP DATA 100 MB, 2G3G4G, 30D', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 2865, 3865, 3365, 3665, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(298, 'Xl10', 'Xl 10.000', 'pulsa', 'xl', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 10855, 11855, 11355, 11655, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(299, 'XL101', 'XL Data 6 GB 30 Hari', 'paket_data', 'xl', 'umum', 'DRP DATA 6 GB, 2G3G4G, 30D', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 53500, 54500, 54000, 54300, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(300, 'XL11', 'XL Data 8 GB 30 Hari', 'paket_data', 'xl', 'umum', 'DRP DATA 8 GB, 2G3G4G, 30D', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 55000, 56000, 55500, 55800, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(301, 'Xl150', 'Xl 150.000', 'pulsa', 'xl', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 149525, 150525, 150025, 150325, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(302, 'XL2', 'XL Data 500 MB 30 Hari', 'paket_data', 'xl', 'umum', 'DRP DATA 500 MB, 2G3G4G, 30D', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 3700, 4700, 4200, 4500, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(303, 'Xl25', 'Xl 25.000', 'pulsa', 'xl', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 25100, 26100, 25600, 25900, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(304, 'XL3', 'XL Data 800 MB 30 Hari', 'paket_data', 'xl', 'umum', 'DRP DATA 800 MB, 2G3G4G, 30D', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 5890, 6890, 6390, 6690, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(305, 'XL4', 'XL Data 1 GB 30 Hari', 'paket_data', 'xl', 'umum', 'DRP DATA 1 GB, 2G3G4G, 30D', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 7510, 8510, 8010, 8310, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(306, 'Xl5', 'Xl 5.000', 'pulsa', 'xl', 'umum', 'Reguler', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 5825, 6825, 6325, 6625, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(307, 'XL51', 'XL Data 2 GB 30 Hari', 'paket_data', 'xl', 'umum', 'DRP DATA 2 GB, 2G3G4G, 30D', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 12520, 13520, 13020, 13320, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar'),
(308, 'XL6', 'XL Data 3 GB 30 Hari', 'paket_data', 'xl', 'umum', 'DRP DATA 3 GB, 2G3G4G, 30D', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 22530, 23530, 23030, 23330, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(309, 'XL7', 'XL Data 4 GB 30 Hari', 'paket_data', 'xl', 'umum', 'DRP DATA 4 GB, 2G3G4G, 30D', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 25725, 26725, 26225, 26525, 'Off', 'normal', '0000-00-00 00:00:00', 'prabayar'),
(310, 'XL9', 'XL Data 5 GB 30 Hari', 'paket_data', 'xl', 'umum', 'DRP DATA 5 GB, 2G3G4G, 30D', 'https://sakurupiah.my.id/assets/img/produk/xl-perdana.png', 31200, 32200, 31700, 32000, 'Off', 'gangguan', '0000-00-00 00:00:00', 'prabayar');

-- --------------------------------------------------------

--
-- Table structure for table `meta_data`
--

CREATE TABLE `meta_data` (
  `id` int NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `keyword` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `whatsapp` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `facebook` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `instagram` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `youtube` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `twitter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `waktu_dibuat` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumping data for table `meta_data`
--

INSERT INTO `meta_data` (`id`, `nama`, `title`, `deskripsi`, `keyword`, `whatsapp`, `email`, `facebook`, `instagram`, `youtube`, `twitter`, `waktu_dibuat`) VALUES
(1, 'SAKUPIAH', 'Sakurupiah - PPOB & Topup Game Terpercaya', 'Sakurupiah adalah platform layanan PPOB dan top up game terpercaya di Indonesia. Kami menyediakan pulsa, paket data, token listrik, serta berbagai voucher game dengan harga bersaing dan proses cepat.', 'ppob, topup game, pulsa murah, token listrik, voucher game, sakurupiah, bayar tagihan online, isi saldo, top up', '6281234567890', 'cs@sakurupiah.id', 'https://facebook.com/sakurupiah', 'https://instagram.com/sakurupiah', 'https://youtube.com/@sakurupiah', 'https://twitter.com/sakurupiah', '2025-06-22 11:22:26');

-- --------------------------------------------------------

--
-- Table structure for table `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id` int NOT NULL,
  `kode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `minimal` double NOT NULL,
  `maksimal` double NOT NULL,
  `biaya` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `percent_biaya` enum('Percent','Nominal') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `tambahan_biaya` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `tipe_tambahan` enum('Percent','Nominal') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `status` enum('Aktif','Offline') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `metode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `logo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `guide` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumping data for table `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`id`, `kode`, `nama`, `minimal`, `maksimal`, `biaya`, `percent_biaya`, `tambahan_biaya`, `tipe_tambahan`, `status`, `metode`, `logo`, `guide`) VALUES
(1, 'QRIS', 'QRIS', 500, 2000000, '0.7', 'Percent', '350', 'Nominal', 'Aktif', 'QRIS', 'https://sakurupiah.id/assets/img/payment/QRIS.png', '1.Buka aplikasi pembayaran yang mendukung pembayaran menggunakan QRIS.\n         2.Pilih fitur QRIS Bayar.\n         3.Pindai kode QR yang diberikan oleh Merchant.\n         4.Pastikan tagihan yang ditagihkan sesuai dengan invoice.\n         5.Klik tombol konfirmasi pembayaran.\n         6.Masukkan PIN untuk menyelesaikan pembayaran.\n         7.Setelah pembayaran berhasil, kamu akan dialihkan ke Halaman Hasil Pembayaran.\n         8.Pembayaran selesai.'),
(2, 'QRIS2', 'QRIS2', 100, 10000000, '1.9', 'Percent', '0', 'Nominal', 'Aktif', 'QRIS', 'https://sakurupiah.id/assets/img/payment/QRIS.png', '1.Buka aplikasi pembayaran yang mendukung pembayaran menggunakan QRIS.\r\n         2.Pilih fitur QRIS Bayar.\r\n         3.Pindai kode QR yang diberikan oleh Merchant.\r\n         4.Pastikan tagihan yang ditagihkan sesuai dengan invoice.\r\n         5.Klik tombol konfirmasi pembayaran.\r\n         6.Masukkan PIN untuk menyelesaikan pembayaran.\r\n         7.Setelah pembayaran berhasil, kamu akan dialihkan ke Halaman Hasil Pembayaran.\r\n         8.Pembayaran selesai.'),
(3, 'ShopeePay', 'ShopeePay', 1000, 2000000, '3', 'Percent', '0', 'Nominal', 'Aktif', 'E-Wallet', 'https://sakurupiah.id/assets/img/payment/SHOPPEPAY.png', 'Berikut langkah-langkah untuk melakukan pembayaran melalui ShopeePay:\r\n\r\n1. Setelah memilih produk dan metode pembayaran ShopeePay, klik tombol \"Bayar Sekarang\" atau \"Menuju Pembayaran\".\r\n2. Anda akan diarahkan ke halaman checkout resmi dari payment gateway.\r\n3. Pada halaman tersebut, pilih metode pembayaran ShopeePay (jika belum otomatis).\r\n4. Akan muncul data invoice kode pembayaran khusus untuk ShopeePay atau halaman pembayaran.\r\n5. Periksa detail transaksi yang muncul di aplikasi Shopee.\r\n6. Jika sudah sesuai, klik \"Bayar Sekarang\" di aplikasi.\r\n7. Setelah pembayaran berhasil, Anda akan menerima notifikasi bahwa transaksi telah selesai.\r\n8. Sistem kami akan memproses pesanan Anda secara otomatis.\r\n\r\nCatatan:\r\n- Pastikan saldo ShopeePay Anda mencukupi.\r\n- Jika pembayaran gagal, ulangi dari langkah 1 atau hubungi layanan pelanggan.\r\n- Simpan bukti pembayaran jika diperlukan untuk konfirmasi lebih lanjut.'),
(4, 'DANA', 'DANA E-Wallet', 1000, 2000000, '3', 'Percent', '0', 'Nominal', 'Offline', 'E-Wallet', 'https://sakurupiah.id/assets/img/payment/DANA.png', '1. Buka aplikasi DANA di ponsel Anda.\r\n2. Masukan nomor Hanphone DANA yang terdaftar lalu buka notifikasi pembayaran yang masuk.\r\n3. Pastikan Nominal Pembayaran Sesuai Dengan Jumlah Pembayaran anda.\r\n4. Klik Lanjutkan.\r\n5. Masukan PIN DANA Anda.\r\n6. Konfirmasi pembayaran dan tunggu prosesnya selesai.'),
(5, 'OVO', 'OVO E-Wallet', 1000, 2000000, '3.5', 'Percent', '0', 'Nominal', 'Offline', 'E-Wallet', 'https://sakurupiah.id/assets/img/payment/OVO.png', '1. Buka aplikasi OVO di ponsel Anda.\n2. Masukan nomor Hanphone OVO yang terdaftar lalu buka notifikasi pembayaran yang masuk pada aplikasi OVO anda.\n3. Pastikan Nominal Pembayaran Sesuai Dengan Jumlah Pembayaran anda.\n4. Klik Lanjutkan.\n5. Masukan PIN OVO Anda.\n6. Konfirmasi pembayaran dan tunggu prosesnya selesai.'),
(6, 'GOPAY', 'GOPAY E-Wallet', 1000, 2000000, '3', 'Percent', '0', 'Nominal', 'Offline', 'E-Wallet', 'https://sakurupiah.id/assets/img/payment/GOPAY.png', '1. Buka aplikasi GOPAY di ponsel Anda.\n2. MAsuk ke akun GOPAY anda yang sudah terdaftar lalu buka notifikasi pembayaran yang masuk pada aplikasi GOPAY anda.\n3. Cek Kembali Invoice Pembayaran Yang Masuk, Pastikan Nominal Pembayaran Sesuai Dengan Jumlah Pembayaran anda.\n4. Klik Lanjutkan.\n5. Masukan PIN GOPAY Anda.\n6. Konfirmasi pembayaran dan tunggu prosesnya selesai.'),
(7, 'LinkAja', 'Link Aja', 1000, 2000000, '3', 'Percent', '0', 'Nominal', 'Offline', 'E-Wallet', 'https://sakurupiah.id/assets/img/payment/app-LINKAJA.png', '1. Buka aplikasi Link Aja di ponsel Anda.\r\n2. MAsuk ke akun Link Aja anda yang sudah terdaftar lalu buka notifikasi pembayaran yang masuk pada aplikasi Link Aja anda.\r\n3. Cek Kembali Invoice Pembayaran Yang Masuk, Pastikan Nominal Pembayaran Sesuai Dengan Jumlah Pembayaran anda.\r\n4. Klik Lanjutkan.\r\n5. Masukan PIN.\r\n6. Konfirmasi pembayaran dan tunggu prosesnya selesai.'),
(8, 'BCAVA', 'BCA Virtual-Account', 10000, 25000000, '5500', 'Nominal', '0', 'Nominal', 'Offline', 'Virtual-Account', 'https://sakurupiah.id/assets/img/payment/BCA.png', '1. Melalui ATM BCA:\r\n- Masukkan kartu ATM dan PIN.\r\n- Pilih menu \"Transaksi Lainnya\".\r\n- Pilih \"Transfer\".\r\n- Pilih \"Ke Rek BCA Virtual Account\".\r\n- Masukkan nomor Virtual Account.\r\n- Cek detail pembayaran dan pilih \"Ya\" jika sesuai.\r\n- Simpan struk transaksi sebagai bukti. \r\n2. Melalui myBCA (Mobile Banking):\r\n- Login ke aplikasi myBCA.\r\n- Pilih menu \"Transfer\".\r\n- Pilih \"BCA Virtual Account\".\r\n- Masukkan nomor Virtual Account.\r\nCek detail pembayaran dan pilih \"Kirim\".\r\n- Input PIN BCA dan transaksi selesai. '),
(9, 'BNIVA', 'BNI Virtual-Account', 10000, 20000000, '4200', 'Nominal', '0', 'Nominal', 'Aktif', 'Virtual-Account', 'https://sakurupiah.id/assets/img/payment/BNI.png', '1. Melalui ATM BNI\r\n- Masukkan kartu ATM dan PIN\r\n- Pilih Menu Lain\r\n- Pilih Transfer\r\n- Pilih Virtual Account Billing\r\n- Masukkan Nomor VA (misal: 8808123456789012)\r\n- Tekan Benar\r\n- Muncul detail tagihan → pastikan nama & nominal benar\r\n- Pilih Ya untuk konfirmasi\r\n- Selesai, simpan bukti transaksi\r\n\r\n2. Melalui BNI Mobile Banking\r\nLogin ke aplikasi BNI Mobile Banking\r\n- Pilih menu Transfer\r\n- Pilih Virtual Account Billing\r\n- Masukkan Nomor VA (misal: 8808123456789012)\r\n- Tekan Lanjut\r\n- Muncul detail tagihan → pastikan sesuai\r\n- Masukkan MPIN\r\n- Selesai, akan muncul notifikasi pembayaran berhasil'),
(10, 'PERMATAVA', 'PERMATA Virtual-Account', 10000, 20000000, '4200', 'Nominal', '0', 'Nominal', 'Aktif', 'Virtual-Account', 'https://sakurupiah.id/assets/img/payment/PERMATABANK.png', '1. Melalui ATM Permata\r\n- Masukkan kartu ATM Permata dan PIN\r\n- Pilih Transaksi Lainnya\r\n- Pilih Pembayaran\r\n- Pilih Virtual Account\r\n- Masukkan Nomor VA (misal: 8960123456789012)\r\n- Periksa detail tagihan (nama dan jumlah)\r\n- Tekan Ya untuk konfirmasi\r\n- Transaksi selesai → simpan bukti pembayaran\r\n\r\n2. Melalui PermataMobile X\r\n- Login ke aplikasi PermataMobile X\r\n- Pilih menu Bayar\r\n- Pilih Virtual Account\r\n- Masukkan Nomor VA\r\n- Lihat detail pembayaran → pastikan sesuai\r\n- Masukkan PIN untuk konfirmasi\r\n- Selesai, akan muncul notifikasi berhasil\r\n\r\n3. Melalui PermataNet (Internet Banking)\r\n- Login ke https://new.permatanet.com/\r\n- Pilih Pembayaran > Virtual Account\r\n- Masukkan Nomor VA\r\n- Pilih rekening sumber dana\r\n- Konfirmasi detail → lanjutkan\r\n- Selesai\r\n\r\n4. Dari Bank Lain (ATM Bersama / Prima)\r\n- Gunakan menu Transfer Antar Bank\r\n- Masukkan Kode Bank Permata: 013\r\n- Masukkan Nomor Virtual Account lengkap (biasanya: 8960 + USER_ID)\r\n- Contoh: 8960123456789012\r\n- Masukkan nominal sesuai tagihan\r\n- Konfirmasi detail → lanjutkan transfer\r\n'),
(11, 'BRIVA', 'BRI Virtual-Account', 10000, 25000000, '4200', 'Nominal', '0', 'Nominal', 'Aktif', 'Virtual-Account', 'https://sakurupiah.id/assets/img/payment/BRI.png', '1. Melalui ATM BRI\r\n- Masukkan kartu ATM dan PIN\r\n- Pilih Menu Transaksi Lain\r\n- Pilih Pembayaran\r\n- Pilih Lainnya → BRIVA (BRI Virtual Account)\r\n- Masukkan Nomor Virtual Account BRI (contoh: 123456789012345)\r\n- Cek nama dan nominal pembayaran\r\n- Pilih Ya untuk konfirmasi\r\n- Selesai → Simpan bukti transaksi\r\n\r\n2. Melalui BRImo (BRI Mobile Banking)\r\n- Buka dan login ke aplikasi BRImo\r\n- Pilih menu BRIVA\r\n- Masukkan Nomor Virtual Account\r\n- Tekan Lanjutkan\r\n- Muncul detail pembayaran → pastikan sesuai\r\n- Tekan Bayar dan masukkan PIN\r\n- Transaksi selesai\r\n\r\n3. Melalui BRI Internet Banking\r\n- Login ke https://ib.bri.co.id/\r\n- Pilih menu Pembayaran → BRIVA\r\n- Masukkan Nomor VA\r\n- Pilih sumber dana\r\n- Cek detail → konfirmasi pembayaran\r\n- Masukkan mToken → Selesai\r\n\r\n4. Dari Bank Lain (Via ATM Bersama / Prima)\r\n-Pilih menu Transfer ke Bank Lain\r\n- Masukkan Kode Bank BRI: 002\r\n- Masukkan Nomor VA sebagai rekening tujuan\r\n- Masukkan nominal sesuai tagihan\r\n- Cek detail → konfirmasi\r\n- Selesai'),
(12, 'CIMBVA', 'CIMB NIAGA Virtual-Account', 10000, 10000000, '4200', 'Nominal', '0', 'Nominal', 'Aktif', 'Virtual-Account', 'https://sakurupiah.id/assets/img/payment/CIMBNIAGA.png', '1. Melalui ATM CIMB Niaga\r\n- Masukkan kartu ATM dan PIN\r\n- Pilih menu Transaksi Lainnya\r\n- Pilih Pembayaran\r\n- Pilih Virtual Account\r\n- Masukkan Nomor VA CIMB Niaga (misal: 1239123456789012)\r\n- Periksa nama penerima dan nominal\r\n- Konfirmasi pembayaran\r\n- Selesai, simpan struk bukti transaksi\r\n\r\n2. Melalui OCTO Mobile (Aplikasi CIMB Niaga)\r\n- Buka dan login ke aplikasi OCTO Mobile\r\n- Pilih menu Bayar & Beli\r\n- Pilih Virtual Account\r\n- Masukkan Nomor Virtual Account\r\n- Cek detail pembayaran\r\n- Klik Bayar Sekarang\r\n- Masukkan PIN → selesai\r\n\r\n3. Melalui OCTO Clicks (Internet Banking CIMB)\r\n- Login ke https://www.octoclicks.co.id/\r\n- Pilih menu Transfer Dana → CIMB Niaga Virtual Account\r\n- Masukkan Nomor VA\r\n- Pilih sumber dana\r\n- Cek rincian → lanjutkan pembayaran\r\n- Masukkan token PIN / mPIN\r\n- Selesai\r\n\r\n4. Dari Bank Lain (ATM Bersama / Prima / Alto)\r\n- Gunakan menu Transfer ke Bank Lain\r\n- Masukkan Kode Bank CIMB Niaga: 022\r\n- Masukkan Nomor VA CIMB Niaga sebagai rekening tujuan\r\n- Masukkan nominal sesuai tagihan\r\n- Konfirmasi data dan selesaikan transaksi'),
(13, 'MANDIRIVA', 'MANDIRI Virtual-Account', 10000, 15000000, '4200', 'Nominal', '0', 'Nominal', 'Aktif', 'Virtual-Account', 'https://sakurupiah.id/assets/img/payment/MANDIRI.png', '1.Melalui ATM Mandiri\r\n- Masukkan kartu ATM dan PIN\r\n- Pilih Bayar/Beli\r\n- Pilih Multi Payment\r\n- Masukkan Kode Perusahaan: 70012\r\n- Masukkan Kode Pembayaran (Bill Key Anda)\r\n- Periksa nama dan jumlah\r\n- Konfirmasi dan selesaikan transaksi\r\n\r\n2.Melalui Livin\' by Mandiri (Mobile Banking)\r\n- Login ke aplikasi\r\n- Pilih Bayar > Multi Payment\r\n- Masukkan Kode Perusahaan: 70012\r\n- Masukkan Kode Pembayaran Anda\r\n- Periksa detail pembayaran\r\n- Lanjutkan dan konfirmasi dengan PIN'),
(14, 'ALFAMART', 'ALFAMART', 5000, 5000000, '4000', 'Nominal', '0', 'Nominal', 'Offline', 'Convenience Store', 'https://sakurupiah.id/assets/img/payment/ALFAMART.png', '1. Catat atau screenshot Kode Pembayaran\r\nKode pembayaran biasanya berupa angka 12–16 digit.\r\nContoh: 1234 5678 9101 1121\r\n\r\n2. Kunjungi gerai Alfamart terdekat\r\n3. Sampaikan kepada kasir:\r\n“Saya ingin melakukan pembayaran online atau e-commerce.”\r\n\r\n4. Berikan Kode Pembayaran ke kasir\r\n5. Bayar sesuai total tagihan\r\nPastikan jumlah pembayaran sesuai yang tertera di halaman.\r\n\r\n6. Simpan struk sebagai bukti pembayaran'),
(15, 'INDOMARET', 'INDOMARET', 5000, 5000000, '4000', 'Nominal', '0', 'Nominal', 'Offline', 'Convenience Store', 'https://sakurupiah.id/assets/img/payment/INDOMARET.png', '1. Catat atau screenshot Kode Pembayaran\r\nKode pembayaran biasanya berupa angka 12–16 digit.\r\nContoh: 1234 5678 9101 1121\r\n\r\n2. Kunjungi gerai Indomaret terdekat\r\n3. Sampaikan kepada kasir:\r\n“Saya ingin melakukan pembayaran online atau e-commerce.”\r\n\r\n4. Berikan Kode Pembayaran ke kasir\r\n5. Bayar sesuai total tagihan\r\nPastikan jumlah pembayaran sesuai yang tertera di halaman.\r\n\r\n6. Simpan struk sebagai bukti pembayaran');

-- --------------------------------------------------------

--
-- Table structure for table `payment_gateway`
--

CREATE TABLE `payment_gateway` (
  `id` int NOT NULL,
  `kode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `api_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `api_key` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumping data for table `payment_gateway`
--

INSERT INTO `payment_gateway` (`id`, `kode`, `api_id`, `api_key`) VALUES
(1, 'sakurupiah', 'ID-9XXXXXXXX', 'KEY-vXXXXXXXXXXXXX');

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan_ppob`
--

CREATE TABLE `pemesanan_ppob` (
  `id` int NOT NULL,
  `kode_transaksi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `no_hp` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `produk_id` int NOT NULL,
  `metode_pembayaran` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `payment_gateway` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT 'sakurupiah',
  `kode_pembayaran` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `harga_produk` decimal(12,2) NOT NULL,
  `biaya_admin` decimal(12,2) NOT NULL,
  `harga_total` decimal(12,2) NOT NULL,
  `kontak` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `tipe_produk` enum('prabayar','pascabayar') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `status_pembayaran` enum('pending','paid','expired','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT 'pending',
  `status_pengiriman` enum('pending','proses','berhasil','gagal') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT 'pending',
  `ref_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `status_digiflazz` enum('belum_dikirim','proses','sukses','gagal') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT 'belum_dikirim',
  `digiflazz_response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `callback_response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `json_invoice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `expired_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provider`
--

CREATE TABLE `provider` (
  `id` int NOT NULL,
  `nama` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `api_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumping data for table `provider`
--

INSERT INTO `provider` (`id`, `nama`, `username`, `api_key`, `status`) VALUES
(1, 'digiflazz', 'gozxxxxxxxx', 'bc605xxxxxxxxxxxxxxxxx', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_saldo`
--

CREATE TABLE `riwayat_saldo` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `tipe` enum('refund','topup','penarikan','pembelian','penyesuaian') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT 'pembelian',
  `jumlah` decimal(16,2) NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_profit`
--

CREATE TABLE `setting_profit` (
  `id` int NOT NULL,
  `profit_jual` double NOT NULL,
  `tipe_jual` enum('nominal','percent') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `profit_diskon` double NOT NULL,
  `tipe_diskon` enum('nominal','percent') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `profit_reseller` double NOT NULL,
  `tipe_reseller` enum('nominal','percent') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumping data for table `setting_profit`
--

INSERT INTO `setting_profit` (`id`, `profit_jual`, `tipe_jual`, `profit_diskon`, `tipe_diskon`, `profit_reseller`, `tipe_reseller`) VALUES
(1, 1000, 'nominal', 200, 'nominal', 500, 'nominal');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `nomor_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `saldo` decimal(12,2) DEFAULT '0.00',
  `status` enum('aktif','nonaktif','banned') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT 'aktif',
  `role` enum('user','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT 'user',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_logo`
--

CREATE TABLE `web_logo` (
  `id` int NOT NULL,
  `favicon` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `header` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `footer` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumping data for table `web_logo`
--

INSERT INTO `web_logo` (`id`, `favicon`, `header`, `footer`) VALUES
(1, 'https://sakurupiah.my.id/assets/img/web/logo-dompet-sakurupiah.png', 'https://sakurupiah.my.id/assets/img/web/text-sakurupiah.png', 'https://sakurupiah.my.id/assets/img/web/text-sakurupiah.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deposit`
--
ALTER TABLE `deposit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `kode_transaksi_2` (`kode_transaksi`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meta_data`
--
ALTER TABLE `meta_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_gateway`
--
ALTER TABLE `payment_gateway`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pemesanan_ppob`
--
ALTER TABLE `pemesanan_ppob`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kode_transaksi` (`kode_transaksi`),
  ADD KEY `produk_id` (`produk_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `provider`
--
ALTER TABLE `provider`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `riwayat_saldo`
--
ALTER TABLE `riwayat_saldo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `setting_profit`
--
ALTER TABLE `setting_profit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `web_logo`
--
ALTER TABLE `web_logo`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `deposit`
--
ALTER TABLE `deposit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=311;

--
-- AUTO_INCREMENT for table `meta_data`
--
ALTER TABLE `meta_data`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payment_gateway`
--
ALTER TABLE `payment_gateway`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pemesanan_ppob`
--
ALTER TABLE `pemesanan_ppob`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `provider`
--
ALTER TABLE `provider`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `riwayat_saldo`
--
ALTER TABLE `riwayat_saldo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_profit`
--
ALTER TABLE `setting_profit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_logo`
--
ALTER TABLE `web_logo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
