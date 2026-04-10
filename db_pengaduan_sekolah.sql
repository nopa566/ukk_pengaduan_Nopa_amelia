-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 31, 2026 at 08:02 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pengaduan_sekolah`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_petugas` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_petugas`) VALUES
(1, 'admin_roger', 'roger', 'Mas Roger'),
(2, 'admin_sofia', 'sofia', 'Mba Sofia');

-- --------------------------------------------------------

--
-- Table structure for table `aspirasi`
--

CREATE TABLE `aspirasi` (
  `id_aspirasi` int NOT NULL,
  `nis` char(10) NOT NULL,
  `id_kategori` int NOT NULL,
  `lokasi` varchar(50) NOT NULL,
  `keterangan` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Menunggu','Proses','Selesai') NOT NULL DEFAULT 'Menunggu',
  `feedback` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `aspirasi`
--

INSERT INTO `aspirasi` (`id_aspirasi`, `nis`, `id_kategori`, `lokasi`, `keterangan`, `foto`, `tanggal`, `status`, `feedback`) VALUES
(2, '1', 3, 'lab PPLG', 'kehilangan pulpen', 'pal.jpeg', '2026-03-31', 'Proses', 'okkkk'),
(3, '2', 1, '10 pplg', 'kelas bau', 'book.jpeg', '2026-03-31', 'Selesai', 'okkk'),
(4, '3', 5, 'perpustakaan', 'buku berantakan', 'book.jpeg', '2026-03-31', 'Menunggu', ''),
(5, '4', 1, 'X DKV', 'kelasnya berisik', 'unduhan.webp', '2026-03-31', 'Proses', 'okkkk'),
(6, '5', 2, 'kelas X PM', 'sampah berserakan', 'unduhan (1).webp', '2026-03-31', 'Menunggu', ''),
(7, '6', 4, 'Lab PM', 'komputer rusak', 'unduhan (2).webp', '2026-03-31', 'Menunggu', ''),
(8, '7', 3, 'kelas X pm', 'hp hilang', 'th.webp', '2026-03-31', 'Menunggu', ''),
(9, '8', 3, 'musholla', 'mukena hilang', 'unduhan (3).webp', '2026-03-31', 'Menunggu', ''),
(10, '9', 1, 'X pplg', 'kelas bau sapi', '', '2026-03-31', 'Menunggu', ''),
(11, '10', 5, 'perpustakaan', 'buku di perpus berantakannnn', '', '2026-03-31', 'Menunggu', ''),
(12, '22', 1, 'XII PPLG', 'kelas berisik', 'unduhan (4).webp', '2026-03-31', 'Menunggu', '');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'kelas'),
(2, 'Kebersihan'),
(3, 'Keamanan'),
(4, 'Laboratorium'),
(5, 'Perpustakaan');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `nis` char(10) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`nis`, `nama`, `kelas`, `password`) VALUES
('1', 'Riaydi jelek ', 'X PPLG', 'jelek1'),
('10', 'Rifai', 'XI DKV', 'rifai1'),
('11', 'salman', 'XII PPLG', 'salman1'),
('2', 'Nopa', 'XI PPLG', 'nopa1'),
('22', 'aceng', 'XII PPLG', 'aceng1'),
('23', 'nenes', 'X TKJ', 'nenes1'),
('3', 'Neiza', 'X PPLG', 'neiza1'),
('4', 'salsa', 'XI DKV', 'salsa1'),
('5', 'Angelia', 'XI PPLG', 'angel1'),
('6', 'Royan', 'X PM', 'royan1'),
('7', 'clarisa', 'X PM', 'clarisa1'),
('8', 'afrida', 'XII TKJ', 'afrida1'),
('9', 'dira', 'X PM', 'dira1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `aspirasi`
--
ALTER TABLE `aspirasi`
  ADD PRIMARY KEY (`id_aspirasi`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`nis`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `aspirasi`
--
ALTER TABLE `aspirasi`
  MODIFY `id_aspirasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
