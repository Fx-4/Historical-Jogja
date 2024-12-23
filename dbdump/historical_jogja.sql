-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2024 at 03:15 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `historical_jogja`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `PASSWORD`, `email`, `created_at`, `updated_at`) VALUES
(1, 'haikal', '$2y$10$Nx0KySqgmuF9ronyWt0nw.94NY40NnkZzsNSxmAp3GyovI4kR5fFW', 'haikalhelmy14@Gmail.com', '2024-12-13 07:35:52', '2024-12-13 07:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`message_id`, `name`, `email`, `message`, `status`, `created_at`) VALUES
(1, 'Test User', 'test@email.com', 'Test Message', '', '2024-12-23 00:58:13'),
(2, 'HAIKAL HIFZHI HELMY', 'haikalhelmy13@Gmail.com', 'anjay mabar\\r\\n', '', '2024-12-23 01:04:55');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `gallery_id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`gallery_id`, `building_id`, `image_path`, `caption`, `created_at`) VALUES
(66, 12, 'candi/12/676384e6b6e92_20241219.jpg', '', '2024-12-19 02:28:54');

-- --------------------------------------------------------

--
-- Table structure for table `historical_buildings`
--

CREATE TABLE `historical_buildings` (
  `building_id` int(11) NOT NULL,
  `building_name` varchar(100) NOT NULL,
  `category` enum('candi','istana','benteng','monumen','masjid','museum','makam') NOT NULL,
  `construction_year` int(11) DEFAULT NULL,
  `STATUS` enum('aktif','tidak_aktif') DEFAULT 'aktif',
  `complete_history` text NOT NULL,
  `main_image` varchar(255) NOT NULL,
  `important_figures` text DEFAULT NULL,
  `cultural_value` text DEFAULT NULL,
  `address` text NOT NULL,
  `open_hours` text DEFAULT NULL,
  `ticket_price` varchar(100) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `map_embed` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `historical_buildings`
--

INSERT INTO `historical_buildings` (`building_id`, `building_name`, `category`, `construction_year`, `STATUS`, `complete_history`, `main_image`, `important_figures`, `cultural_value`, `address`, `open_hours`, `ticket_price`, `contact`, `map_embed`, `created_at`, `updated_at`) VALUES
(12, 'Candi Prambanan', 'candi', 850, 'aktif', 'Candi Prambanan, yang dibangun pada tahun 850 Masehi, adalah hasil karya Rakai Pikatan dari Wangsa Sanjaya sebagai penghormatan kepada agama Hindu. Kompleks ini mencakup sekitar 240 candi, dengan Candi Siwa sebagai candi utama yang memiliki ketinggian 47 meter. Pembangunan candi ini bertujuan untuk bersaing dengan candi Buddha seperti Borobudur dan Sewu yang berdekatan. Setelah mengalami kerusakan dan terbengkalai karena perpindahan pusat kekuasaan ke Jawa Timur, candi ini ditemukan kembali pada abad ke-18 oleh arkeolog Belanda, yang kemudian memulai upaya restorasi untuk mengembalikan kemegahannya.', '6766c17589df2_UTAMA.png', '1. Roro Jonggrang\r\nPutri dalam legenda lokal yang dikutuk menjadi arca oleh Bandung Bondowoso karena menipunya saat pembangunan 1.000 candi.\r\n2. Bandung Bondowoso \r\nPangeran sakti yang berusaha membangun 1.000 candi dalam semalam untuk memenuhi syarat pernikahan Roro Jonggrang.\r\n3. Trimurti\r\nTiga dewa utama Hindu:\r\nBrahma (Dewa Pencipta), dihormati di Candi Brahma\r\nVishnu (Dewa Pemelihara), dihormati di Candi Vishnu\r\nShiva (Dewa Penghancur), dihormati di Candi Shiva, yang menjadi candi utama di kompleks Prambanan\r\n4. Rakai Pikatan\r\nSecara historis, Candi Prambanan dibangun oleh Rakai Pikatan, raja dari Kerajaan Medang (Mataram Kuno), pada abad ke-9 Masehi. Kompleks ini kemungkinan diperluas oleh penerusnya, seperti Rakai Kayuwangi.\r\n5. Raja Balitung Maha Sambu\r\nMelanjutkan dan memperbaiki kompleks candi setelah Rakai Pikatan, berkontribusi besar dalam pengembangan situs tersebut.\r\n6. Raja Daksha\r\nDi dalam relief Candi Prambanan, terdapat kisah Parwati, istri Dewa Shiva, yang berkaitan dengan Raja Daksha. Kisah ini merupakan bagian dari Mahabharata dan Ramayana, yang juga diabadikan di relief candi.\r\n', 'Menggambarkan keagungan arsitektur Hindu di Jawa,\r\nBukti kemajuan peradaban dan ketrampilan arsitektur pada masa itu,\r\nRepresentasi kepercayaan dan praktik keagamaan Hindu.', 'Jl. Raya Solo - Yogyakarta No.16, Kranggan, Bokoharjo, Kec. Prambanan, Kabupaten Sleman, Daerah Istimewa Yogyakarta \r\n', '06.30 - 17.00 WIB\r\n', 'Domestik Dewasa Rp 50.000,00Domestik Anak-Anak Rp 25.000,00Mancanegara Dewasa 25 USDMancanegar', '1. Kantor PusatJl. Raya Yogya-Solo KM. 16, Prambanan, Sleman, D.I Yogyakarta 55561Telp: (0274) 4', NULL, '2024-12-19 02:28:54', '2024-12-21 13:24:05'),
(15, 'Keraton Ngayogyakarta Hadiningrat', 'istana', 1755, 'aktif', 'Keraton Ngayogyakarta Hadiningrat didirikan pada tahun 1755 sebagai pusat pemerintahan dan budaya Jawa yang aktif hingga kini. Pembangunan keraton ini dilakukan oleh Sultan Hamengku Buwono I setelah Perjanjian Giyanti pada 13 Februari 1755, yang memisahkan Kerajaan Mataram menjadi dua bagian: Kasunanan Surakarta dan Kesultanan Yogyakarta. Pada tanggal 15 Maret 1755, Sultan mengumumkan berdirinya Nagari Ngayogyakarta Hadiningrat, dan pembangunan keraton dimulai pada 9 Oktober 1755. Keraton ini menjadi simbol kekuasaan dan budaya Yogyakarta serta berfungsi sebagai kediaman keluarga kesultanan. Proses pembangunan selesai pada tahun 1756, menjadikannya pusat kegiatan budaya dan pemerintahan di Yogyakarta.', 'istana/6766c37edf0b8_UTAMA.jpeg', 'Sri Sultan Hamengkubuwono I\r\nPendiri Keraton Yogyakarta pada 1755, setelah Perjanjian Giyanti yang membagi Kerajaan Mataram menjadi Kasunanan Surakarta dan Kesultanan Yogyakarta.\r\nPangeran Mangkubumi\r\nNama gelar Sultan Hamengku Buwono I sebelum menjadi sultan, merupakan tokoh utama dalam perlawanan terhadap penjajahan Belanda.\r\nTumenggung Joyowinoto\r\nBertugas menyiapkan lokasi untuk tempat tinggal Sultan di Pesanggrahan Ambarketawang sebelum pembangunan keraton dimulai.\r\n', 'Pusat pelestarian budaya Jawa\r\nSimbol kontinuitas sejarah dan tradisi\r\nWahana pendidikan tentang sistem pemerintahan tradisional\r\n', 'Jl. Rotowijayan Blok No. 1, Panembahan, Kecamatan Kraton, Kota Yogyakarta, Daerah Istimewa Yogyakarta', 'Senin (Tutup)\r\nSelasa - Minggu (8.30 - 14.30 WIB)\r\n', 'Dewasa Domestik: Rp 15.000\r\nAnak-Anak Domestik: Rp 10.000\r\nDewasa Mancanegara: Rp 25.000\r\nAnak-Anak ', '(0274) 373721 ', NULL, '2024-12-21 13:32:46', '2024-12-21 13:32:46'),
(16, 'Taman Sari', 'istana', 1758, 'aktif', 'Taman Sari adalah kompleks taman air yang dibangun antara tahun 1758 hingga 1765 pada masa Sultan Hamengku Buwono I. Kompleks ini awalnya merupakan pemandian kerajaan yang megah dengan sistem pengairan canggih, termasuk kolam renang dan ruang-ruang rahasia. Pembangunan Taman Sari dilakukan sebagai penghargaan Sultan kepada permaisurinya yang banyak berkorban selama perang melawan VOC. Selain berfungsi sebagai tempat rekreasi bagi Sultan dan keluarganya, Taman Sari juga berfungsi sebagai tempat perlindungan saat terjadi serangan musuh. Dengan luas lebih dari 10 hektare, kompleks ini terdiri dari sekitar 57 bangunan yang mencerminkan kombinasi arsitektur Jawa, Eropa, Hindu, dan China.', 'istana/6766c554a4c86_UTAMA.jpg', 'Sri Sultan Hamengkubuwono I\r\nPendiri Taman Sari yang memerintahkan pembangunan kompleks ini sebagai tanda penghormatan kepada permaisurinya dan untuk memberikan tempat istirahat bagi keluarga kerajaan.\r\nTumenggung Mangundipura\r\narsitek dan bangsawan yang dipercaya Sultan Hamengkubuwono I untuk merancang Taman Sari. Ia bekerja sama dengan tenaga ahli dari Portugis, yang memberi sentuhan arsitektur Eropa pada desain kompleks ini.\r\nDemang Tegis\r\nArsitek yang terlibat dalam pembangunan Taman Sari; ia dikenal sebagai seorang Portugis yang membawa pengaruh arsitektur Eropa ke dalam desain taman.\r\nRatu Keraton\r\nDalam fungsi utamanya, Taman Sari sering digunakan oleh para permaisuri dan selir Sultan sebagai tempat bersantai dan berendam. Sosok para perempuan keraton memiliki keterkaitan erat dengan area pemandian.\r\nPara Abdi Dalem Keraton\r\nAbdi dalem bertugas menjaga dan mengelola Taman Sari. Mereka memastikan tempat ini terpelihara dan siap digunakan oleh keluarga kerajaan.\r\nKolonial Belanda\r\nPada era kolonial, sebagian kompleks Taman Sari mengalami kerusakan akibat konflik, termasuk Perang Jawa yang dipimpin Pangeran Diponegoro (1825-1830). Peran Belanda dalam sejarah Taman Sari adalah sebagai faktor eksternal yang memengaruhi keutuhan kompleks.\r\n', 'Menunjukkan kecanggihan arsitektur dan teknik bangunan masa lalu\r\nBukti akulturasi budaya Jawa dan pengaruh asing\r\nRepresentasi kehidupan kerajaan pada masa lampau\r\n', 'Patehan, Kraton, Yogyakarta City, Special Region of Yogyakarta ', '09.00 - 15.00 WIB', 'Anak 2-12 tahun :\r\n-Domestik Rp 10.000 \r\n-Internasional Rp20.000\r\nDewasa :\r\n-Domestik Rp 20.000 \r\n-I', '0812 1625 3307', NULL, '2024-12-21 13:40:36', '2024-12-21 13:40:36');

-- --------------------------------------------------------

--
-- Table structure for table `tourist_map`
--

CREATE TABLE `tourist_map` (
  `location_id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `description` text DEFAULT NULL,
  `marker_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tourist_map`
--

INSERT INTO `tourist_map` (`location_id`, `building_id`, `location_name`, `latitude`, `longitude`, `description`, `marker_type`, `created_at`) VALUES
(12, 12, 'Candi Prambanan', -7.75238861, 110.49152970, '', NULL, '2024-12-19 02:28:54'),
(15, 15, 'Keraton Ngayogyakarta Hadiningrat', -7.80533200, 110.36422100, '', NULL, '2024-12-21 13:32:46'),
(16, 16, 'Taman Sari', -7.81012700, 110.35921100, '', NULL, '2024-12-21 13:40:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`gallery_id`),
  ADD KEY `building_id` (`building_id`);

--
-- Indexes for table `historical_buildings`
--
ALTER TABLE `historical_buildings`
  ADD PRIMARY KEY (`building_id`);

--
-- Indexes for table `tourist_map`
--
ALTER TABLE `tourist_map`
  ADD PRIMARY KEY (`location_id`),
  ADD UNIQUE KEY `unique_building` (`building_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `gallery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `historical_buildings`
--
ALTER TABLE `historical_buildings`
  MODIFY `building_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tourist_map`
--
ALTER TABLE `tourist_map`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gallery`
--
ALTER TABLE `gallery`
  ADD CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`building_id`) REFERENCES `historical_buildings` (`building_id`) ON DELETE CASCADE;

--
-- Constraints for table `tourist_map`
--
ALTER TABLE `tourist_map`
  ADD CONSTRAINT `tourist_map_ibfk_1` FOREIGN KEY (`building_id`) REFERENCES `historical_buildings` (`building_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
