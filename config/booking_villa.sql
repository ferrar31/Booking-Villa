-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Nov 2025 pada 09.00
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booking_villa`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `level` enum('superadmin','staff') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `email`, `password`, `no_telp`, `level`, `created_at`) VALUES
(1, 'Deigla Arjuna Ferrar', 'admin@gmail.com', '$2y$10$FN4EclrkqRwvPQCqmeBHD.ypVxrIB57xUxzgOVTUpw.jdYXakVfMS', '082125638205', 'superadmin', '2025-10-29 08:20:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_villa`
--

CREATE TABLE `detail_villa` (
  `id_villa` int(11) NOT NULL,
  `nama_villa` varchar(100) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `fasilitas` text DEFAULT NULL,
  `harga_permalam` decimal(10,2) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `status` enum('tersedia','dibooking') DEFAULT 'tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_villa`
--

INSERT INTO `detail_villa` (`id_villa`, `nama_villa`, `lokasi`, `deskripsi`, `fasilitas`, `harga_permalam`, `gambar`, `status`) VALUES
(1, 'Villa Arjuna', 'Batu, Malang', 'Villa mewah dengan pemandangan gunung.', '3 kamar, kolam renang, dapur', 750000.00, 'villa1.jpg', 'dibooking'),
(2, 'Villa Afdal', 'Lembang, Bandung', 'Villa sejuk di area perbukitan.', '2 kamar, balkon, BBQ', 650000.00, 'villa2.jpg', 'dibooking'),
(3, 'villa ferrar', 'gunhal', 'wenak poll', 'ac,tv,kasur', 3000000.00, NULL, 'dibooking'),
(4, 'villa in', 'halu', 'bismillah', 'tv,kulkas', 500000.00, 'logo villa.png', 'tersedia');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama_pengguna` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama_pengguna`, `email`, `password`, `no_telp`, `alamat`, `created_at`) VALUES
(1, 'Juna', 'juna@villa.com', '6ad14ba9986e3615423dfca256d04e3f', '082345678901', 'Malang', '2025-10-29 08:20:18'),
(2, 'ferrar', 'ferrar@gmail.com', '$2y$10$OkpRJAWrHBKhJnKUI7ERwuqKIKUf3/gRSfyEVn9.2Hs404EQFyIri', '084326236236', 'gunhal', '2025-10-31 11:14:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_villa` int(11) NOT NULL,
  `tanggal_checkin` date NOT NULL,
  `tanggal_checkout` date NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `status` enum('pending','dibayar','selesai','batal') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bukti_pembayaran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pengguna`, `id_villa`, `tanggal_checkin`, `tanggal_checkout`, `total_harga`, `status`, `created_at`, `bukti_pembayaran`) VALUES
(1, 1, 1, '2025-10-29', '2025-10-31', 1500000.00, '', '2025-10-29 08:25:49', NULL),
(2, 2, 2, '2025-10-31', '2025-11-02', 1300000.00, '', '2025-10-31 11:15:25', NULL),
(3, 2, 3, '2025-11-04', '2025-11-05', 3000000.00, 'batal', '2025-11-03 09:17:45', NULL),
(4, 2, 3, '2025-11-03', '2025-11-08', 15000000.00, '', '2025-11-03 12:17:49', '1762172280_activity.png');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_transaksi_detail`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_transaksi_detail` (
`id_transaksi` int(11)
,`nama_pengguna` varchar(100)
,`nama_villa` varchar(100)
,`lokasi` varchar(255)
,`tanggal_checkin` date
,`tanggal_checkout` date
,`total_harga` decimal(12,2)
,`status` enum('pending','dibayar','selesai','batal')
);

-- --------------------------------------------------------

--
-- Struktur untuk view `v_transaksi_detail`
--
DROP TABLE IF EXISTS `v_transaksi_detail`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_transaksi_detail`  AS SELECT `t`.`id_transaksi` AS `id_transaksi`, `p`.`nama_pengguna` AS `nama_pengguna`, `v`.`nama_villa` AS `nama_villa`, `v`.`lokasi` AS `lokasi`, `t`.`tanggal_checkin` AS `tanggal_checkin`, `t`.`tanggal_checkout` AS `tanggal_checkout`, `t`.`total_harga` AS `total_harga`, `t`.`status` AS `status` FROM ((`transaksi` `t` join `pengguna` `p` on(`t`.`id_pengguna` = `p`.`id_pengguna`)) join `detail_villa` `v` on(`t`.`id_villa` = `v`.`id_villa`)) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `detail_villa`
--
ALTER TABLE `detail_villa`
  ADD PRIMARY KEY (`id_villa`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `id_villa` (`id_villa`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `detail_villa`
--
ALTER TABLE `detail_villa`
  MODIFY `id_villa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`),
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_villa`) REFERENCES `detail_villa` (`id_villa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
