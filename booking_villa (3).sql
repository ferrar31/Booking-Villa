-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Nov 2025 pada 04.12
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
(1, 'villa ferrar', 'gununghalu', 'enken', 'tv,ps5', 500000.00, 'logo villa.png', 'dibooking'),
(2, 'villa ajun', 'gununghalu', 'well', 'balong', 800000.00, 'villabg.jpg', 'dibooking'),
(3, 'villa deigla', 'gununghalu', 'amin', 'balong,kolam,pemancingan', 1000000.00, 'Gambar villa.jpg', 'dibooking'),
(4, 'ipan imut', 'kirintil', 'teuing', 'seblak', 300000.00, 'activity.png', 'dibooking');

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
(3, 'moch ojo', 'ojo@gmail.com', '$2y$10$gY06y7cmga63UrGQS6NQTuxmtaD9L2VgWqgtY/PKvmiBSZnn2hT82', '084326236236', 'rongga', '2025-11-05 04:22:56'),
(4, 'afdal', 'dal@gmail.com', '$2y$10$LZb8MbI.3s2kz1OLUGM85O2t5wuHd1ZPBiA/JZ/lIvdvi6XLXTWLC', '084326236236', 'rongga', '2025-11-05 04:39:18');

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
  `status` enum('pending','lunas','batal') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `notif_user` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pengguna`, `id_villa`, `tanggal_checkin`, `tanggal_checkout`, `total_harga`, `status`, `created_at`, `bukti_pembayaran`, `notif_user`) VALUES
(1, 4, 1, '2025-11-10', '2025-11-20', 5000000.00, 'lunas', '2025-11-10 01:40:22', NULL, NULL),
(2, 3, 4, '2025-11-10', '2025-11-15', 1500000.00, 'batal', '2025-11-10 01:44:52', '1762739100_sequence.png', NULL),
(3, 3, 4, '2025-11-10', '2025-11-30', 6000000.00, 'lunas', '2025-11-10 02:06:24', '1762740768_activity.png', NULL);

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
,`status` enum('pending','lunas','batal')
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
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
