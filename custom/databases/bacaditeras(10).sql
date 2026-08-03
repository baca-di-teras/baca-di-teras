-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2026 at 03:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bacaditeras`
--

-- --------------------------------------------------------

--
-- Table structure for table `pathfinder_categories`
--

DROP TABLE IF EXISTS `pathfinder_categories`;
CREATE TABLE `pathfinder_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL COMMENT 'Nama kategori',
  `slug` varchar(160) NOT NULL COMMENT 'URL-friendly',
  `description` text DEFAULT NULL,
  `icon` varchar(80) DEFAULT NULL COMMENT 'Nama ikon Phosphor Icons',
  `sort_order` tinyint(3) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kategori utama modul Pathfinder';

--
-- Dumping data for table `pathfinder_categories`
--

INSERT INTO `pathfinder_categories` (`id`, `name`, `slug`, `description`, `icon`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Literasi Bahasa & Sastra', 'literasi-bahasa-sastra', 'Panduan membaca, menulis, dan mengapresiasi karya sastra dari berbagai sumber terpercaya.', 'book-open-text', 1, 'active', '2026-07-27 22:20:49', '2026-07-27 22:20:49'),
(2, 'Literasi Lingkungan', 'literasi-lingkungan', 'Kumpulan referensi tentang kelestarian alam, ekologi, dan gaya hidup ramah lingkungan.', 'leaf', 2, 'active', '2026-07-27 22:20:49', '2026-07-27 22:20:49'),
(3, 'Literasi Pertanian & Peternakan', 'literasi-pertanian-peternakan', 'Panduan praktis dan ilmiah di bidang pertanian modern, budidaya, dan peternakan.', 'plant', 3, 'active', '2026-07-27 22:20:49', '2026-07-27 22:20:49'),
(4, 'Literasi Kesehatan & Keluarga', 'literasi-kesehatan-keluarga', 'Referensi kesehatan, gizi, pola hidup sehat, dan panduan parenting bagi keluarga.', 'heartbeat', 4, 'active', '2026-07-27 22:20:49', '2026-07-27 22:20:49'),
(5, 'Literasi Pendidikan & Sains', 'literasi-pendidikan-sains', 'Sumber belajar kurikuler dan ilmiah untuk mendukung proses belajar-mengajar.', 'graduation-cap', 5, 'active', '2026-07-27 22:20:49', '2026-07-27 22:20:49'),
(6, 'Literasi Ekonomi & Kewirausahaan', 'literasi-ekonomi-kewirausahaan', 'Panduan dasar ekonomi, manajemen usaha, dan kewirausahaan berbasis masyarakat desa.', 'currency-circle-dollar', 6, 'active', '2026-07-27 22:20:49', '2026-07-27 22:20:49'),
(7, 'Literasi Pengembangan Diri', 'literasi-pengembangan-diri', 'Referensi untuk meningkatkan keterampilan, motivasi, kepemimpinan, dan produktivitas.', 'lightning', 7, 'active', '2026-07-27 22:20:49', '2026-07-27 22:20:49'),
(8, 'Literasi Sejarah, Budaya & Keagamaan', 'literasi-sejarah-budaya-keagamaan', 'Panduan mengenal warisan sejarah lokal, budaya Nusantara, dan khazanah keagamaan.', 'mosque', 8, 'active', '2026-07-27 22:20:49', '2026-07-27 22:20:49');

-- --------------------------------------------------------

--
-- Table structure for table `pathfinder_downloads`
--

DROP TABLE IF EXISTS `pathfinder_downloads`;
CREATE TABLE `pathfinder_downloads` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL COMMENT 'FK -> pathfinder_topics.id',
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(500) NOT NULL COMMENT 'Path file relatif dari ROOT_PATH',
  `file_type` enum('pdf','doc','docx','xls','xlsx','ppt','pptx','jpg','jpeg','png','zip','other') NOT NULL DEFAULT 'pdf',
  `file_size` int(11) DEFAULT 0 COMMENT 'Ukuran file dalam bytes',
  `download_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='File download terkurasi per topik Pathfinder';

--
-- Dumping data for table `pathfinder_downloads`
--

INSERT INTO `pathfinder_downloads` (`id`, `topic_id`, `title`, `description`, `file_path`, `file_type`, `file_size`, `download_count`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 'Infografis Sistem Hidroponik NFT', 'Penjelasan visual cara kerja sistem NFT untuk hidroponik.', '/custom/uploads/pathfinder/downloads/infografis-nft-hidroponik.pdf', 'pdf', 512000, 0, 'active', '2026-07-27 22:20:50', '2026-07-27 22:20:50'),
(2, 1, 'Brosur 3R: Reduce, Reuse, Recycle', 'Brosur cetak untuk sosialisasi pengelolaan sampah di lingkungan RT/RW.', '/custom/uploads/pathfinder/downloads/brosur-3r-pengelolaan-sampah.pdf', 'pdf', 256000, 0, 'active', '2026-07-27 22:20:50', '2026-07-27 22:20:50'),
(3, 8, 'Poster Isi Piringku', 'Poster panduan gizi seimbang dari Kementerian Kesehatan RI.', '/custom/uploads/pathfinder/downloads/poster-isi-piringku.jpg', 'jpg', 1024000, 0, 'active', '2026-07-27 22:20:50', '2026-07-27 22:20:50');

-- --------------------------------------------------------

--
-- Table structure for table `pathfinder_external_resources`
--

DROP TABLE IF EXISTS `pathfinder_external_resources`;
CREATE TABLE `pathfinder_external_resources` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL COMMENT 'FK -> pathfinder_topics.id',
  `title` varchar(255) NOT NULL,
  `resource_type` enum('website','video','pdf','article') NOT NULL DEFAULT 'website',
  `url` text NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` tinyint(3) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Referensi eksternal terkurasi per topik Pathfinder';

--
-- Dumping data for table `pathfinder_external_resources`
--

INSERT INTO `pathfinder_external_resources` (`id`, `topic_id`, `title`, `resource_type`, `url`, `description`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(7, 1, 'https://archive.org/details/booksbylanguage', 'website', 'https://archive.org/details/booksbylanguage', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(8, 1, 'SIBI - Sistem Informasi Perbukuan Indonesia', 'website', 'https://buku.kemendikdasmen.go.id/katalog/buku-non-teks#', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(9, 1, 'Baca - Buku Digital', 'website', 'https://budi.kemendikdasmen.go.id/buku?level=&theme=3600a374-b5f1-4e74-b6ea-16f7ba8b27c0&language=&tipe=&katakunci=', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(10, 1, 'Let\'s Read | Children\'s Books | Free to Read Download Translate', 'website', 'https://www.letsreadasia.org/collection/englishdecodables', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(11, 13, 'https://www.ojk.go.id/id/Default.aspx', 'website', 'https://www.ojk.go.id/id/Default.aspx', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(12, 13, 'https://ipusnas.perpusnas.go.id/book/506ef511-91dc-4b55-8141-f2650dff07da', 'website', 'https://ipusnas.perpusnas.go.id/book/506ef511-91dc-4b55-8141-f2650dff07da', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(13, 13, 'https://archive.org/details/rentjana-ekonomi-tan-malaka', 'website', 'https://archive.org/details/rentjana-ekonomi-tan-malaka', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(14, 13, 'https://openlibrary.org/works/OL5866985W/Ekonomi_pembangunan?edition=key%3A/books/OL6812966M', 'website', 'https://openlibrary.org/works/OL5866985W/Ekonomi_pembangunan?edition=key%3A/books/OL6812966M', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(15, 13, 'https://youtu.be/_44pe9_iu14?si=TNBfthI4YXrJxgjF', 'video', 'https://youtu.be/_44pe9_iu14?si=TNBfthI4YXrJxgjF', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(16, 13, 'https://ipusnas.perpusnas.go.id/book/04d47f00-362c-428b-ac49-b921b2d4aaee', 'website', 'https://ipusnas.perpusnas.go.id/book/04d47f00-362c-428b-ac49-b921b2d4aaee', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(17, 4, 'https://ipusnas.perpusnas.go.id/book/30b7a94a-3539-4ecd-bcae-cbbce6451081', 'website', 'https://ipusnas.perpusnas.go.id/book/30b7a94a-3539-4ecd-bcae-cbbce6451081', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(18, 4, 'https://openlibrary.org/works/OL40570457W/Belajar_Bahasa_Inggris_Nama_Hewan', 'website', 'https://openlibrary.org/works/OL40570457W/Belajar_Bahasa_Inggris_Nama_Hewan_Buku_Mewarnai_Dan_Temukan_Milikmu_Hewan_Totem_Semangat_Pelindung?edition=key%3A/books/OL55161017M', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(19, 4, 'https://online.fliphtml5.com/kynrn/favp/', 'website', 'https://online.fliphtml5.com/kynrn/favp/', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(20, 4, 'https://archive.org/details/rubuhnya-surau-kami-kumpulan-cerpen', 'website', 'https://archive.org/details/rubuhnya-surau-kami-kumpulan-cerpen', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(21, 2, 'https://ipusnas.perpusnas.go.id/book/28ff5465-8087-40fe-867a-070cc17f1ba1', 'website', 'https://ipusnas.perpusnas.go.id/book/28ff5465-8087-40fe-867a-070cc17f1ba1', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(22, 2, 'https://www.gutenberg.org/ebooks/14838', 'website', 'https://www.gutenberg.org/ebooks/14838', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(23, 2, 'https://www.letsreadasia.org/book/malam-yang-terang?bookLang=626007401614540', 'website', 'https://www.letsreadasia.org/book/malam-yang-terang?bookLang=6260074016145408', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(24, 2, 'https://openlibrary.org/works/OL23997339W/Sastra_anak?edition=key%3A/books/OL31696511M', 'website', 'https://openlibrary.org/works/OL23997339W/Sastra_anak?edition=key%3A/books/OL31696511M', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(25, 2, 'https://ipusnas.perpusnas.go.id/book/17036a28-fba9-447d-ad45-18337e164b58', 'website', 'https://ipusnas.perpusnas.go.id/book/17036a28-fba9-447d-ad45-18337e164b58', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(26, 5, 'https://www.worldfloraonline.org/', 'website', 'https://www.worldfloraonline.org/', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(27, 5, 'https://online.fliphtml5.com/kynrn/favp/', 'website', 'https://online.fliphtml5.com/kynrn/favp/', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(28, 5, 'https://ipusnas.perpusnas.go.id/book/a1c2f58b-329e-45d5-8b1a-1e96e1db4dfb', 'website', 'https://ipusnas.perpusnas.go.id/book/a1c2f58b-329e-45d5-8b1a-1e96e1db4dfb', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(29, 5, 'https://buku.kemendikdasmen.go.id/katalog/pendidikan-agama-islam-dan-budi-pekert', 'website', 'https://buku.kemendikdasmen.go.id/katalog/pendidikan-agama-islam-dan-budi-pekerti-untuk-sd-kelas-ii', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(30, 15, 'https://ipusnas.perpusnas.go.id/book/782d60b4-8e6b-47c5-9eb4-1103d5a2775a', 'website', 'https://ipusnas.perpusnas.go.id/book/782d60b4-8e6b-47c5-9eb4-1103d5a2775a', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(31, 15, 'https://openlibrary.org/works/OL16625095W/Pendidikan_Islam?edition=key%3A/bo', 'website', 'https://openlibrary.org/works/OL16625095W/Pendidikan_Islam?edition=key%3A/books/OL25305515M', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(32, 15, 'https://www.letsreadasia.org/book/f2b5039f-f632-4292-91c6-2146bcdb4ebb?bookLan', 'website', 'https://www.letsreadasia.org/book/f2b5039f-f632-4292-91c6-2146bcdb4ebb?bookLang=6260074016145408', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(33, 9, 'https://ipusnas.perpusnas.go.id/book/dd965de8-d2d0-40d2-9774-77b0816b84fa', 'website', 'https://ipusnas.perpusnas.go.id/book/dd965de8-d2d0-40d2-9774-77b0816b84fa', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(34, 9, 'https://budi.kemendikdasmen.go.id/baca/video/aku-tak-mau-minum-obat', 'website', 'https://budi.kemendikdasmen.go.id/baca/video/aku-tak-mau-minum-obat', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(35, 9, 'https://ipusnas.perpusnas.go.id/book/e0ac9dc7-8ee4-4dbd-9cac-7a15523b0a3a', 'website', 'https://ipusnas.perpusnas.go.id/book/e0ac9dc7-8ee4-4dbd-9cac-7a15523b0a3a', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(36, 9, 'https://openlibrary.org/works/OL19235979W/Kesehatan_reproduksi_remaja?edition=', 'website', 'https://openlibrary.org/works/OL19235979W/Kesehatan_reproduksi_remaja?edition=key%3A/books/OL521060M', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(37, 9, 'Let\'s Read | Children\'s Books | Free to Read Download Translate', 'website', 'https://www.letsreadasia.org/collection/Tasting%20Home', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(38, 3, 'Baca - Buku Digital', 'website', 'https://budi.kemendikdasmen.go.id/buku?level=&theme=0f6205de-aa89-4d14-b572-ac81951a80c1&language=&tipe=&katakunci=', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(39, 3, 'Baca - Buku Digital', 'website', 'https://budi.kemendikdasmen.go.id/buku?level=&theme=9bc1806f-8e83-4cc3-b212-c30f9f3284d1&language=&tipe=&katakunci=', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(40, 3, 'Jangan Salah Buang, Dong! - Buku Digital', 'website', 'https://budi.kemendikdasmen.go.id/baca/digital/jangan-salah-buang-dong', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(41, 3, 'Let\'s Read | Children\'s Books | Free to Read Download Translate', 'website', 'https://www.letsreadasia.org/search/s?searchText=&lId=4846240843956224&tid=5728283396145152&countryOfOrigin=&audio=false&limit=20&cursor=', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(42, 10, 'Baca - Buku Digital', 'website', 'https://budi.kemendikdasmen.go.id/buku?level=&theme=a3a97977-5dfd-4265-b8b9-b33d6c9d6fea&language=&tipe=&katakunci=', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(43, 10, 'Baca - Buku Digital', 'website', 'https://budi.kemendikdasmen.go.id/buku?level=&theme=9bc1806f-8e83-4cc3-b212-c30f9f3284d1&language=&tipe=&katakunci=', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(44, 10, 'Seni mengasuh anak dengan cinta dan disiplin - iPusnasWebsite', 'website', 'https://ipusnas.perpusnas.go.id/book/8bf2a8d1-f913-47e5-8c06-7de9aa984494', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(45, 10, 'Baca - Buku Digital', 'website', 'https://budi.kemendikdasmen.go.id/buku?level=&theme=e7df84ce-c2ed-4bf9-8649-3be2e7ce0565&language=&tipe=&katakunci=', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(46, 11, 'SIBI - Sistem Informasi Perbukuan Indonesia', 'website', 'https://buku.kemendikdasmen.go.id/buku-stem', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(47, 11, 'SIBI - Sistem Informasi Perbukuan Indonesia', 'website', 'https://buku.kemendikdasmen.go.id/katalog/buku-kurikulum-merdeka', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(48, 11, 'SIBI - Sistem Informasi Perbukuan Indonesia', 'website', 'https://buku.kemendikdasmen.go.id/katalog/buku-teks-k13', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(49, 11, 'SIBI - Sistem Informasi Perbukuan Indonesia', 'website', 'https://buku.kemendikdasmen.go.id/katalog/buku-non-teks#', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(50, 14, 'Let\'s Read | Children\'s Books | Free to Read Download Translate', 'website', 'https://www.letsreadasia.org/collection/a7a73141-dc3e-4cc8-8dbe-99df2e37b56a', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(51, 14, 'Download books Self-Help, Relationships & Lifestyle - Psychological Self-Help', 'website', 'https://z-library.im/category/601/Psychological-Self-Help', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(52, 14, 'Download books Psychology - Social Psychology Ebook library z-library.im', 'website', 'https://z-library.im/category/816/Social-Psychology', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(53, 6, 'Pertanian Press', 'website', 'https://repository.pertanian.go.id/collections/c54e89b7-73da-42f9-b4ba-c8b819321fb4', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(54, 6, 'Majalah Intan', 'website', 'https://repository.pertanian.go.id/collections/71127d2d-e21c-4621-9763-91d37f7e3506', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(55, 6, 'Perlindungan Perkebunan', 'website', 'https://repository.pertanian.go.id/collections/a71ab363-1fbf-4912-8dae-b155fd944b66', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(56, 6, 'Komik Pertanian', 'website', 'https://repository.pertanian.go.id/collections/4828e057-d9c7-47d1-9dea-b31ade168510', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(57, 7, 'Baca - Buku Digital', 'website', 'https://budi.kemendikdasmen.go.id/buku?level=&theme=9bc1806f-8e83-4cc3-b212-c30f9f3284d1&language=&tipe=&katakunci=', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(58, 7, 'Budi Daya Ayam Petelur Bebas Sangkar Skala Komersial di Indonesia', 'website', 'https://repository.pertanian.go.id/items/05b65d25-5ae2-4cad-ad6a-6e7dc93b61b4', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(59, 7, 'Pengolahan dan Pemasaran Hasil Peternakan', 'website', 'https://repository.pertanian.go.id/collections/14c56af8-f2fa-4304-ada3-e19325b4ad2f', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(60, 12, 'Baca - Buku Digital', 'website', 'https://budi.kemendikdasmen.go.id/buku?level=&theme=36de7f48-72df-4f54-82a3-0e8f8c138ad4&language=&tipe=&katakunci=', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(61, 12, 'Let\'s Read | Children\'s Books | Free to Read Download Translate', 'website', 'https://www.letsreadasia.org/collection/EverydaySTEM', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41'),
(62, 12, 'Let\'s Read | Children\'s Books | Free to Read Download Translate', 'website', 'https://www.letsreadasia.org/category/5643342180253696', NULL, 0, 'active', '2026-07-29 02:50:41', '2026-07-29 02:50:41');

-- --------------------------------------------------------

--
-- Table structure for table `pathfinder_guides`
--

DROP TABLE IF EXISTS `pathfinder_guides`;
CREATE TABLE `pathfinder_guides` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL COMMENT 'FK -> pathfinder_topics.id',
  `title` varchar(255) NOT NULL,
  `slug` varchar(270) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `content` longtext DEFAULT NULL COMMENT 'Isi panduan HTML',
  `sort_order` tinyint(3) NOT NULL DEFAULT 0,
  `status` enum('published','draft','archived') NOT NULL DEFAULT 'draft',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tips & Panduan terkurasi per topik Pathfinder';

--
-- Dumping data for table `pathfinder_guides`
--

INSERT INTO `pathfinder_guides` (`id`, `topic_id`, `title`, `slug`, `thumbnail`, `content`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 'Cara Memulai Hidroponik di Rumah dengan Modal Minim', 'cara-memulai-hidroponik-di-rumah', '/custom/assets/images/guides/hidroponik-dasar.jpg', '<h2>Persiapan Alat dan Bahan</h2><p>Untuk memulai hidroponik skala rumah tangga, Anda hanya membutuhkan:</p><ul><li>Ember atau wadah plastik bekas</li><li>Pipa paralon atau talang air</li><li>Netpot (dapat diganti gelas plastik bekas)</li><li>Nutrisi AB Mix</li><li>Media tanam: rockwool atau cocopeat</li><li>Benih sayuran: selada, kangkung, atau bayam</li></ul><h2>Langkah-Langkah</h2><ol><li>Siapkan wadah dan lubangi tutupnya sesuai ukuran netpot</li><li>Isi wadah dengan air bersih, tambahkan nutrisi AB Mix sesuai dosis</li><li>Semai benih di rockwool selama 3-5 hari</li><li>Pindahkan bibit ke netpot setelah muncul 2 daun sejati</li><li>Pantau kepekatan nutrisi (EC) dan pH air secara rutin</li></ol>', 1, 'published', '2026-07-27 22:20:49', '2026-07-27 22:20:49'),
(2, 1, 'Cara Membuat Kompos dari Sampah Dapur dalam 30 Hari', 'cara-membuat-kompos-sampah-dapur', '/custom/assets/images/guides/kompos-dapur.jpg', '<h2>Apa itu Kompos?</h2><p>Kompos adalah hasil penguraian bahan organik oleh mikroorganisme. Kompos buatan sendiri dari sampah dapur merupakan pupuk alami terbaik untuk tanaman.</p><h2>Bahan yang Dibutuhkan</h2><ul><li>Sampah dapur organik: sisa sayur, kulit buah, ampas teh/kopi</li><li>Tanah biasa (sebagai sumber mikroba)</li><li>Ember atau tong dengan lubang aerasi</li></ul><h2>Proses Pembuatan</h2><ol><li>Pisahkan sampah organik dari plastik dan bahan non-organik</li><li>Masukkan lapisan sampah organik (10 cm), tutup dengan lapisan tanah tipis</li><li>Aduk tumpukan setiap 3-4 hari untuk menjaga aerasi</li><li>Kompos matang dalam 3-4 minggu, ditandai warna coklat gelap dan bau tanah</li></ol>', 1, 'published', '2026-07-27 22:20:49', '2026-07-27 22:20:49');

-- --------------------------------------------------------

--
-- Table structure for table `pathfinder_related_books`
--

DROP TABLE IF EXISTS `pathfinder_related_books`;
CREATE TABLE `pathfinder_related_books` (
  `id` int(11) NOT NULL,
  `biblio_id` int(11) NOT NULL COMMENT 'Ref -> biblio.biblio_id (buku utama)',
  `related_biblio_id` int(11) NOT NULL COMMENT 'Ref -> biblio.biblio_id (buku terkait)',
  `relation_type` enum('same_topic','recommendation','new_collection') NOT NULL DEFAULT 'recommendation',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relasi antar buku SLiMS untuk fitur Related Collection';

--
-- Dumping data for table `pathfinder_related_books`
--

INSERT INTO `pathfinder_related_books` (`id`, `biblio_id`, `related_biblio_id`, `relation_type`, `created_at`) VALUES
(4, 5160, 5173, 'recommendation', '2026-07-29 02:38:24'),
(5, 5160, 5219, 'recommendation', '2026-07-29 02:38:24'),
(6, 5160, 5150, 'recommendation', '2026-07-29 02:38:24'),
(7, 5173, 5160, 'recommendation', '2026-07-29 02:38:24'),
(8, 5173, 5219, 'recommendation', '2026-07-29 02:38:24'),
(9, 5173, 5150, 'recommendation', '2026-07-29 02:38:24'),
(10, 5219, 5160, 'recommendation', '2026-07-29 02:38:24'),
(11, 5219, 5173, 'recommendation', '2026-07-29 02:38:24'),
(12, 5219, 5150, 'recommendation', '2026-07-29 02:38:24'),
(13, 5175, 5176, 'recommendation', '2026-07-29 02:38:24'),
(14, 5176, 5175, 'recommendation', '2026-07-29 02:38:24'),
(15, 5150, 5211, 'recommendation', '2026-07-29 02:38:24'),
(16, 5273, 5163, 'recommendation', '2026-07-29 02:38:24'),
(17, 5163, 5273, 'recommendation', '2026-07-29 02:38:24'),
(18, 5211, 5150, 'recommendation', '2026-07-29 02:38:24'),
(19, 5404, 5295, 'recommendation', '2026-07-29 02:38:24'),
(20, 5404, 4919, 'recommendation', '2026-07-29 02:38:24'),
(21, 5246, 5145, 'recommendation', '2026-07-29 02:38:24'),
(22, 5246, 5148, 'recommendation', '2026-07-29 02:38:24'),
(23, 5295, 5404, 'recommendation', '2026-07-29 02:38:24'),
(24, 5295, 4919, 'recommendation', '2026-07-29 02:38:24'),
(25, 5287, 5288, 'recommendation', '2026-07-29 02:38:24'),
(26, 5287, 5294, 'recommendation', '2026-07-29 02:38:24'),
(27, 5217, 5146, 'recommendation', '2026-07-29 02:38:24'),
(28, 5217, 5286, 'recommendation', '2026-07-29 02:38:24'),
(29, 5217, 5253, 'recommendation', '2026-07-29 02:38:24'),
(30, 5217, 5284, 'recommendation', '2026-07-29 02:38:24'),
(31, 5145, 5246, 'recommendation', '2026-07-29 02:38:24'),
(32, 5145, 5148, 'recommendation', '2026-07-29 02:38:24'),
(33, 5145, 5144, 'recommendation', '2026-07-29 02:38:24'),
(34, 5145, 5248, 'recommendation', '2026-07-29 02:38:24'),
(35, 5145, 5251, 'recommendation', '2026-07-29 02:38:24'),
(36, 5146, 5217, 'recommendation', '2026-07-29 02:38:24'),
(37, 5146, 5286, 'recommendation', '2026-07-29 02:38:24'),
(38, 5146, 5253, 'recommendation', '2026-07-29 02:38:24'),
(39, 5146, 5284, 'recommendation', '2026-07-29 02:38:24'),
(40, 5288, 5287, 'recommendation', '2026-07-29 02:38:24'),
(41, 5288, 5294, 'recommendation', '2026-07-29 02:38:24'),
(42, 5196, 5294, 'recommendation', '2026-07-29 02:38:24'),
(43, 5204, 5216, 'recommendation', '2026-07-29 02:38:24'),
(44, 5204, 5340, 'recommendation', '2026-07-29 02:38:24'),
(45, 5216, 5204, 'recommendation', '2026-07-29 02:38:24'),
(46, 5216, 5340, 'recommendation', '2026-07-29 02:38:24'),
(47, 5148, 5246, 'recommendation', '2026-07-29 02:38:24'),
(48, 5148, 5145, 'recommendation', '2026-07-29 02:38:24'),
(49, 5148, 5144, 'recommendation', '2026-07-29 02:38:24'),
(50, 5148, 5248, 'recommendation', '2026-07-29 02:38:24'),
(51, 5148, 5251, 'recommendation', '2026-07-29 02:38:24'),
(52, 5340, 5204, 'recommendation', '2026-07-29 02:38:24'),
(53, 5340, 5216, 'recommendation', '2026-07-29 02:38:24'),
(54, 4919, 5404, 'recommendation', '2026-07-29 02:38:24'),
(55, 4919, 5295, 'recommendation', '2026-07-29 02:38:24'),
(56, 5294, 5287, 'recommendation', '2026-07-29 02:38:24'),
(57, 5294, 5288, 'recommendation', '2026-07-29 02:38:24'),
(58, 5286, 5217, 'recommendation', '2026-07-29 02:38:24'),
(59, 5286, 5146, 'recommendation', '2026-07-29 02:38:24'),
(60, 5286, 5253, 'recommendation', '2026-07-29 02:38:24'),
(61, 5286, 5284, 'recommendation', '2026-07-29 02:38:24'),
(62, 5253, 5217, 'recommendation', '2026-07-29 02:38:24'),
(63, 5253, 5146, 'recommendation', '2026-07-29 02:38:24'),
(64, 5253, 5286, 'recommendation', '2026-07-29 02:38:24'),
(65, 5253, 5284, 'recommendation', '2026-07-29 02:38:24'),
(66, 5294, 5196, 'recommendation', '2026-07-29 02:38:24'),
(67, 5144, 5246, 'recommendation', '2026-07-29 02:38:24'),
(68, 5144, 5145, 'recommendation', '2026-07-29 02:38:24'),
(69, 5144, 5148, 'recommendation', '2026-07-29 02:38:24'),
(70, 5144, 5248, 'recommendation', '2026-07-29 02:38:24'),
(71, 5144, 5251, 'recommendation', '2026-07-29 02:38:24'),
(72, 5248, 5246, 'recommendation', '2026-07-29 02:38:24'),
(73, 5248, 5145, 'recommendation', '2026-07-29 02:38:24'),
(74, 5248, 5148, 'recommendation', '2026-07-29 02:38:24'),
(75, 5248, 5144, 'recommendation', '2026-07-29 02:38:24'),
(76, 5248, 5251, 'recommendation', '2026-07-29 02:38:24'),
(77, 5251, 5246, 'recommendation', '2026-07-29 02:38:24'),
(78, 5251, 5145, 'recommendation', '2026-07-29 02:38:24'),
(79, 5251, 5148, 'recommendation', '2026-07-29 02:38:24'),
(80, 5251, 5144, 'recommendation', '2026-07-29 02:38:24'),
(81, 5251, 5248, 'recommendation', '2026-07-29 02:38:24'),
(82, 5188, 5274, 'recommendation', '2026-07-29 02:38:24'),
(83, 5188, 5153, 'recommendation', '2026-07-29 02:38:24'),
(84, 5188, 5261, 'recommendation', '2026-07-29 02:38:24'),
(85, 5188, 5265, 'recommendation', '2026-07-29 02:38:24'),
(86, 5274, 5188, 'recommendation', '2026-07-29 02:38:24'),
(87, 5274, 5153, 'recommendation', '2026-07-29 02:38:24'),
(88, 5274, 5261, 'recommendation', '2026-07-29 02:38:24'),
(89, 5274, 5265, 'recommendation', '2026-07-29 02:38:24'),
(90, 5153, 5188, 'recommendation', '2026-07-29 02:38:24'),
(91, 5153, 5274, 'recommendation', '2026-07-29 02:38:24'),
(92, 5153, 5261, 'recommendation', '2026-07-29 02:38:24'),
(93, 5153, 5265, 'recommendation', '2026-07-29 02:38:24'),
(94, 5261, 5188, 'recommendation', '2026-07-29 02:38:24'),
(95, 5261, 5274, 'recommendation', '2026-07-29 02:38:24'),
(96, 5261, 5153, 'recommendation', '2026-07-29 02:38:24'),
(97, 5261, 5265, 'recommendation', '2026-07-29 02:38:24'),
(98, 5265, 5188, 'recommendation', '2026-07-29 02:38:24'),
(99, 5265, 5274, 'recommendation', '2026-07-29 02:38:24'),
(100, 5265, 5153, 'recommendation', '2026-07-29 02:38:24'),
(101, 5265, 5261, 'recommendation', '2026-07-29 02:38:24'),
(102, 5242, 5205, 'recommendation', '2026-07-29 02:38:24'),
(103, 5178, 5179, 'recommendation', '2026-07-29 02:38:24'),
(104, 5178, 5180, 'recommendation', '2026-07-29 02:38:24'),
(105, 5178, 5181, 'recommendation', '2026-07-29 02:38:24'),
(106, 5179, 5178, 'recommendation', '2026-07-29 02:38:24'),
(107, 5179, 5180, 'recommendation', '2026-07-29 02:38:24'),
(108, 5179, 5181, 'recommendation', '2026-07-29 02:38:24'),
(109, 5180, 5178, 'recommendation', '2026-07-29 02:38:24'),
(110, 5180, 5179, 'recommendation', '2026-07-29 02:38:24'),
(111, 5180, 5181, 'recommendation', '2026-07-29 02:38:24'),
(112, 5181, 5178, 'recommendation', '2026-07-29 02:38:24'),
(113, 5181, 5179, 'recommendation', '2026-07-29 02:38:24'),
(114, 5181, 5180, 'recommendation', '2026-07-29 02:38:24'),
(115, 5202, 5186, 'recommendation', '2026-07-29 02:38:24'),
(116, 5202, 5189, 'recommendation', '2026-07-29 02:38:24'),
(117, 5186, 5202, 'recommendation', '2026-07-29 02:38:24'),
(118, 5186, 5189, 'recommendation', '2026-07-29 02:38:24'),
(119, 5189, 5202, 'recommendation', '2026-07-29 02:38:24'),
(120, 5189, 5186, 'recommendation', '2026-07-29 02:38:24'),
(121, 5238, 5240, 'recommendation', '2026-07-29 02:38:24'),
(122, 5238, 5247, 'recommendation', '2026-07-29 02:38:24'),
(123, 5238, 5250, 'recommendation', '2026-07-29 02:38:24'),
(124, 5240, 5238, 'recommendation', '2026-07-29 02:38:24'),
(125, 5240, 5247, 'recommendation', '2026-07-29 02:38:24'),
(126, 5240, 5250, 'recommendation', '2026-07-29 02:38:24'),
(127, 5247, 5238, 'recommendation', '2026-07-29 02:38:24'),
(128, 5247, 5240, 'recommendation', '2026-07-29 02:38:24'),
(129, 5247, 5250, 'recommendation', '2026-07-29 02:38:24'),
(130, 5250, 5238, 'recommendation', '2026-07-29 02:38:24'),
(131, 5250, 5240, 'recommendation', '2026-07-29 02:38:24'),
(132, 5250, 5247, 'recommendation', '2026-07-29 02:38:24'),
(133, 5252, 5254, 'recommendation', '2026-07-29 02:38:24'),
(134, 5252, 5192, 'recommendation', '2026-07-29 02:38:24'),
(135, 5252, 5232, 'recommendation', '2026-07-29 02:38:24'),
(136, 5254, 5252, 'recommendation', '2026-07-29 02:38:24'),
(137, 5254, 5192, 'recommendation', '2026-07-29 02:38:24'),
(138, 5254, 5232, 'recommendation', '2026-07-29 02:38:24'),
(139, 5192, 5252, 'recommendation', '2026-07-29 02:38:24'),
(140, 5192, 5254, 'recommendation', '2026-07-29 02:38:24'),
(141, 5192, 5232, 'recommendation', '2026-07-29 02:38:24'),
(142, 5232, 5252, 'recommendation', '2026-07-29 02:38:24'),
(143, 5232, 5254, 'recommendation', '2026-07-29 02:38:24'),
(144, 5232, 5192, 'recommendation', '2026-07-29 02:38:24'),
(145, 5245, 5165, 'recommendation', '2026-07-29 02:38:24'),
(146, 5245, 5201, 'recommendation', '2026-07-29 02:38:24'),
(147, 5245, 5170, 'recommendation', '2026-07-29 02:38:24'),
(148, 5165, 5245, 'recommendation', '2026-07-29 02:38:24'),
(149, 5165, 5201, 'recommendation', '2026-07-29 02:38:24'),
(150, 5165, 5170, 'recommendation', '2026-07-29 02:38:24'),
(151, 5369, 5215, 'recommendation', '2026-07-29 02:38:24'),
(152, 5215, 5369, 'recommendation', '2026-07-29 02:38:24'),
(153, 5201, 5245, 'recommendation', '2026-07-29 02:38:24'),
(154, 5201, 5165, 'recommendation', '2026-07-29 02:38:24'),
(155, 5201, 5170, 'recommendation', '2026-07-29 02:38:24'),
(156, 5170, 5245, 'recommendation', '2026-07-29 02:38:24'),
(157, 5170, 5165, 'recommendation', '2026-07-29 02:38:24'),
(158, 5170, 5201, 'recommendation', '2026-07-29 02:38:24'),
(159, 5205, 5242, 'recommendation', '2026-07-29 02:38:24'),
(160, 5152, 5272, 'recommendation', '2026-07-29 02:38:24'),
(161, 5190, 5157, 'recommendation', '2026-07-29 02:38:24'),
(162, 5190, 5151, 'recommendation', '2026-07-29 02:38:24'),
(163, 5272, 5152, 'recommendation', '2026-07-29 02:38:24'),
(164, 5158, 5263, 'recommendation', '2026-07-29 02:38:24'),
(165, 5158, 5266, 'recommendation', '2026-07-29 02:38:24'),
(166, 5263, 5158, 'recommendation', '2026-07-29 02:38:24'),
(167, 5263, 5266, 'recommendation', '2026-07-29 02:38:24'),
(168, 5266, 5158, 'recommendation', '2026-07-29 02:38:24'),
(169, 5266, 5263, 'recommendation', '2026-07-29 02:38:24'),
(170, 5157, 5190, 'recommendation', '2026-07-29 02:38:24'),
(171, 5157, 5151, 'recommendation', '2026-07-29 02:38:24'),
(172, 5151, 5190, 'recommendation', '2026-07-29 02:38:24'),
(173, 5151, 5157, 'recommendation', '2026-07-29 02:38:24'),
(174, 5374, 5230, 'recommendation', '2026-07-29 02:38:24'),
(175, 5374, 5377, 'recommendation', '2026-07-29 02:38:24'),
(176, 5374, 5378, 'recommendation', '2026-07-29 02:38:24'),
(177, 5374, 5384, 'recommendation', '2026-07-29 02:38:24'),
(178, 5374, 5234, 'recommendation', '2026-07-29 02:38:24'),
(179, 5230, 5374, 'recommendation', '2026-07-29 02:38:24'),
(180, 5230, 5377, 'recommendation', '2026-07-29 02:38:24'),
(181, 5230, 5378, 'recommendation', '2026-07-29 02:38:24'),
(182, 5230, 5384, 'recommendation', '2026-07-29 02:38:24'),
(183, 5230, 5234, 'recommendation', '2026-07-29 02:38:24'),
(184, 5377, 5374, 'recommendation', '2026-07-29 02:38:24'),
(185, 5377, 5230, 'recommendation', '2026-07-29 02:38:24'),
(186, 5377, 5378, 'recommendation', '2026-07-29 02:38:24'),
(187, 5377, 5384, 'recommendation', '2026-07-29 02:38:24'),
(188, 5377, 5234, 'recommendation', '2026-07-29 02:38:24'),
(189, 5378, 5374, 'recommendation', '2026-07-29 02:38:24'),
(190, 5378, 5230, 'recommendation', '2026-07-29 02:38:24'),
(191, 5378, 5377, 'recommendation', '2026-07-29 02:38:24'),
(192, 5378, 5384, 'recommendation', '2026-07-29 02:38:24'),
(193, 5378, 5234, 'recommendation', '2026-07-29 02:38:24'),
(194, 5384, 5374, 'recommendation', '2026-07-29 02:38:24'),
(195, 5384, 5230, 'recommendation', '2026-07-29 02:38:24'),
(196, 5384, 5377, 'recommendation', '2026-07-29 02:38:24'),
(197, 5384, 5378, 'recommendation', '2026-07-29 02:38:24'),
(198, 5384, 5234, 'recommendation', '2026-07-29 02:38:24'),
(199, 5234, 5374, 'recommendation', '2026-07-29 02:38:24'),
(200, 5234, 5230, 'recommendation', '2026-07-29 02:38:24'),
(201, 5234, 5377, 'recommendation', '2026-07-29 02:38:24'),
(202, 5234, 5378, 'recommendation', '2026-07-29 02:38:24'),
(203, 5234, 5384, 'recommendation', '2026-07-29 02:38:24'),
(204, 5381, 5234, 'recommendation', '2026-07-29 02:38:24'),
(205, 5381, 5383, 'recommendation', '2026-07-29 02:38:24'),
(206, 5381, 5336, 'recommendation', '2026-07-29 02:38:24'),
(207, 5383, 5234, 'recommendation', '2026-07-29 02:38:24'),
(208, 5383, 5381, 'recommendation', '2026-07-29 02:38:24'),
(209, 5383, 5336, 'recommendation', '2026-07-29 02:38:24'),
(210, 5336, 5234, 'recommendation', '2026-07-29 02:38:24'),
(211, 5336, 5381, 'recommendation', '2026-07-29 02:38:24'),
(212, 5336, 5383, 'recommendation', '2026-07-29 02:38:24'),
(213, 5373, 5350, 'recommendation', '2026-07-29 02:38:24'),
(214, 5373, 5351, 'recommendation', '2026-07-29 02:38:24'),
(215, 5373, 5379, 'recommendation', '2026-07-29 02:38:24'),
(216, 5350, 5373, 'recommendation', '2026-07-29 02:38:24'),
(217, 5350, 5351, 'recommendation', '2026-07-29 02:38:24'),
(218, 5350, 5379, 'recommendation', '2026-07-29 02:38:24'),
(219, 5284, 5217, 'recommendation', '2026-07-29 02:38:24'),
(220, 5284, 5146, 'recommendation', '2026-07-29 02:38:24'),
(221, 5284, 5286, 'recommendation', '2026-07-29 02:38:24'),
(222, 5284, 5253, 'recommendation', '2026-07-29 02:38:24'),
(223, 5351, 5373, 'recommendation', '2026-07-29 02:38:24'),
(224, 5351, 5350, 'recommendation', '2026-07-29 02:38:24'),
(225, 5351, 5379, 'recommendation', '2026-07-29 02:38:24'),
(226, 5379, 5373, 'recommendation', '2026-07-29 02:38:24'),
(227, 5379, 5350, 'recommendation', '2026-07-29 02:38:24'),
(228, 5379, 5351, 'recommendation', '2026-07-29 02:38:24'),
(229, 5346, 5368, 'recommendation', '2026-07-29 02:38:24'),
(230, 5346, 5382, 'recommendation', '2026-07-29 02:38:24'),
(231, 5346, 5367, 'recommendation', '2026-07-29 02:38:24'),
(232, 5368, 5346, 'recommendation', '2026-07-29 02:38:24'),
(233, 5368, 5382, 'recommendation', '2026-07-29 02:38:24'),
(234, 5368, 5367, 'recommendation', '2026-07-29 02:38:24'),
(235, 5382, 5346, 'recommendation', '2026-07-29 02:38:24'),
(236, 5382, 5368, 'recommendation', '2026-07-29 02:38:24'),
(237, 5367, 5346, 'recommendation', '2026-07-29 02:38:24'),
(238, 5367, 5368, 'recommendation', '2026-07-29 02:38:24'),
(239, 5367, 5382, 'recommendation', '2026-07-29 02:38:24'),
(240, 5338, 5339, 'recommendation', '2026-07-29 02:38:24'),
(241, 5338, 5336, 'recommendation', '2026-07-29 02:38:24'),
(242, 5339, 5338, 'recommendation', '2026-07-29 02:38:24'),
(243, 5339, 5336, 'recommendation', '2026-07-29 02:38:24'),
(244, 5255, 5364, 'recommendation', '2026-07-29 02:38:24'),
(245, 5255, 5365, 'recommendation', '2026-07-29 02:38:24'),
(246, 5255, 5222, 'recommendation', '2026-07-29 02:38:24'),
(247, 5255, 5372, 'recommendation', '2026-07-29 02:38:24'),
(248, 5255, 5227, 'recommendation', '2026-07-29 02:38:24'),
(249, 5364, 5255, 'recommendation', '2026-07-29 02:38:24'),
(250, 5364, 5365, 'recommendation', '2026-07-29 02:38:24'),
(251, 5364, 5222, 'recommendation', '2026-07-29 02:38:24'),
(252, 5364, 5372, 'recommendation', '2026-07-29 02:38:24'),
(253, 5364, 5227, 'recommendation', '2026-07-29 02:38:24'),
(254, 5365, 5255, 'recommendation', '2026-07-29 02:38:24'),
(255, 5365, 5364, 'recommendation', '2026-07-29 02:38:24'),
(256, 5365, 5222, 'recommendation', '2026-07-29 02:38:24'),
(257, 5365, 5372, 'recommendation', '2026-07-29 02:38:24'),
(258, 5365, 5227, 'recommendation', '2026-07-29 02:38:24'),
(259, 5222, 5255, 'recommendation', '2026-07-29 02:38:24'),
(260, 5222, 5364, 'recommendation', '2026-07-29 02:38:24'),
(261, 5222, 5365, 'recommendation', '2026-07-29 02:38:24'),
(262, 5222, 5372, 'recommendation', '2026-07-29 02:38:24'),
(263, 5222, 5227, 'recommendation', '2026-07-29 02:38:24'),
(264, 5372, 5255, 'recommendation', '2026-07-29 02:38:24'),
(265, 5372, 5364, 'recommendation', '2026-07-29 02:38:24'),
(266, 5372, 5365, 'recommendation', '2026-07-29 02:38:24'),
(267, 5372, 5222, 'recommendation', '2026-07-29 02:38:24'),
(268, 5372, 5227, 'recommendation', '2026-07-29 02:38:24'),
(269, 5227, 5255, 'recommendation', '2026-07-29 02:38:24'),
(270, 5227, 5364, 'recommendation', '2026-07-29 02:38:24'),
(271, 5227, 5365, 'recommendation', '2026-07-29 02:38:24'),
(272, 5227, 5222, 'recommendation', '2026-07-29 02:38:24'),
(273, 5227, 5372, 'recommendation', '2026-07-29 02:38:24'),
(274, 5386, 5220, 'recommendation', '2026-07-29 02:38:24'),
(275, 5386, 5371, 'recommendation', '2026-07-29 02:38:24'),
(276, 5386, 5224, 'recommendation', '2026-07-29 02:38:24'),
(277, 5386, 5366, 'recommendation', '2026-07-29 02:38:24'),
(278, 5220, 5386, 'recommendation', '2026-07-29 02:38:24'),
(279, 5220, 5371, 'recommendation', '2026-07-29 02:38:25'),
(280, 5220, 5224, 'recommendation', '2026-07-29 02:38:25'),
(281, 5220, 5366, 'recommendation', '2026-07-29 02:38:25'),
(282, 5371, 5386, 'recommendation', '2026-07-29 02:38:25'),
(283, 5371, 5220, 'recommendation', '2026-07-29 02:38:25'),
(284, 5371, 5224, 'recommendation', '2026-07-29 02:38:25'),
(285, 5371, 5366, 'recommendation', '2026-07-29 02:38:25'),
(286, 5224, 5386, 'recommendation', '2026-07-29 02:38:25'),
(287, 5224, 5220, 'recommendation', '2026-07-29 02:38:25'),
(288, 5224, 5371, 'recommendation', '2026-07-29 02:38:25'),
(289, 5224, 5366, 'recommendation', '2026-07-29 02:38:25'),
(290, 5366, 5386, 'recommendation', '2026-07-29 02:38:25'),
(291, 5366, 5220, 'recommendation', '2026-07-29 02:38:25'),
(292, 5366, 5371, 'recommendation', '2026-07-29 02:38:25'),
(293, 5366, 5224, 'recommendation', '2026-07-29 02:38:25'),
(294, 5333, 5334, 'recommendation', '2026-07-29 02:38:25'),
(295, 5333, 5335, 'recommendation', '2026-07-29 02:38:25'),
(296, 5334, 5333, 'recommendation', '2026-07-29 02:38:25'),
(297, 5334, 5335, 'recommendation', '2026-07-29 02:38:25'),
(298, 5335, 5333, 'recommendation', '2026-07-29 02:38:25'),
(299, 5335, 5334, 'recommendation', '2026-07-29 02:38:25'),
(300, 5280, 5237, 'recommendation', '2026-07-29 02:38:25'),
(301, 5280, 5241, 'recommendation', '2026-07-29 02:38:25'),
(302, 5280, 5239, 'recommendation', '2026-07-29 02:38:25'),
(303, 5237, 5280, 'recommendation', '2026-07-29 02:38:25'),
(304, 5237, 5241, 'recommendation', '2026-07-29 02:38:25'),
(305, 5237, 5239, 'recommendation', '2026-07-29 02:38:25'),
(306, 5241, 5280, 'recommendation', '2026-07-29 02:38:25'),
(307, 5241, 5237, 'recommendation', '2026-07-29 02:38:25'),
(308, 5241, 5239, 'recommendation', '2026-07-29 02:38:25'),
(309, 5239, 5280, 'recommendation', '2026-07-29 02:38:25'),
(310, 5239, 5237, 'recommendation', '2026-07-29 02:38:25'),
(311, 5239, 5241, 'recommendation', '2026-07-29 02:38:25'),
(312, 5388, 5348, 'recommendation', '2026-07-29 02:38:25'),
(313, 5388, 5387, 'recommendation', '2026-07-29 02:38:25'),
(314, 5388, 5343, 'recommendation', '2026-07-29 02:38:25'),
(315, 5348, 5388, 'recommendation', '2026-07-29 02:38:25'),
(316, 5348, 5387, 'recommendation', '2026-07-29 02:38:25'),
(317, 5348, 5343, 'recommendation', '2026-07-29 02:38:25'),
(318, 5387, 5388, 'recommendation', '2026-07-29 02:38:25'),
(319, 5387, 5348, 'recommendation', '2026-07-29 02:38:25'),
(320, 5387, 5343, 'recommendation', '2026-07-29 02:38:25'),
(321, 5343, 5388, 'recommendation', '2026-07-29 02:38:25'),
(322, 5343, 5348, 'recommendation', '2026-07-29 02:38:25'),
(323, 5343, 5387, 'recommendation', '2026-07-29 02:38:25'),
(324, 5090, 5207, 'recommendation', '2026-07-29 02:38:25'),
(325, 5090, 5270, 'recommendation', '2026-07-29 02:38:25'),
(326, 5090, 5199, 'recommendation', '2026-07-29 02:38:25'),
(327, 5207, 5090, 'recommendation', '2026-07-29 02:38:25'),
(328, 5207, 5270, 'recommendation', '2026-07-29 02:38:25'),
(329, 5207, 5199, 'recommendation', '2026-07-29 02:38:25'),
(330, 5270, 5090, 'recommendation', '2026-07-29 02:38:25'),
(331, 5270, 5207, 'recommendation', '2026-07-29 02:38:25'),
(332, 5270, 5199, 'recommendation', '2026-07-29 02:38:25'),
(333, 5199, 5090, 'recommendation', '2026-07-29 02:38:25'),
(334, 5199, 5207, 'recommendation', '2026-07-29 02:38:25'),
(335, 5199, 5270, 'recommendation', '2026-07-29 02:38:25'),
(336, 5231, 5194, 'recommendation', '2026-07-29 02:38:25'),
(337, 5231, 5149, 'recommendation', '2026-07-29 02:38:25'),
(338, 5231, 5172, 'recommendation', '2026-07-29 02:38:25'),
(339, 5231, 5162, 'recommendation', '2026-07-29 02:38:25'),
(340, 5194, 5231, 'recommendation', '2026-07-29 02:38:25'),
(341, 5194, 5149, 'recommendation', '2026-07-29 02:38:25'),
(342, 5194, 5172, 'recommendation', '2026-07-29 02:38:25'),
(343, 5194, 5162, 'recommendation', '2026-07-29 02:38:25'),
(344, 5149, 5231, 'recommendation', '2026-07-29 02:38:25'),
(345, 5149, 5194, 'recommendation', '2026-07-29 02:38:25'),
(346, 5149, 5172, 'recommendation', '2026-07-29 02:38:25'),
(347, 5149, 5162, 'recommendation', '2026-07-29 02:38:25'),
(348, 5172, 5231, 'recommendation', '2026-07-29 02:38:25'),
(349, 5172, 5194, 'recommendation', '2026-07-29 02:38:25'),
(350, 5172, 5149, 'recommendation', '2026-07-29 02:38:25'),
(351, 5172, 5162, 'recommendation', '2026-07-29 02:38:25'),
(352, 5162, 5231, 'recommendation', '2026-07-29 02:38:25'),
(353, 5162, 5194, 'recommendation', '2026-07-29 02:38:25'),
(354, 5162, 5149, 'recommendation', '2026-07-29 02:38:25'),
(355, 5162, 5172, 'recommendation', '2026-07-29 02:38:25'),
(356, 5243, 5291, 'recommendation', '2026-07-29 02:38:25'),
(357, 5243, 5299, 'recommendation', '2026-07-29 02:38:25'),
(358, 5243, 5332, 'recommendation', '2026-07-29 02:38:25'),
(359, 5243, 5300, 'recommendation', '2026-07-29 02:38:25'),
(360, 5291, 5243, 'recommendation', '2026-07-29 02:38:25'),
(361, 5291, 5299, 'recommendation', '2026-07-29 02:38:25'),
(362, 5291, 5332, 'recommendation', '2026-07-29 02:38:25'),
(363, 5291, 5300, 'recommendation', '2026-07-29 02:38:25'),
(364, 5299, 5243, 'recommendation', '2026-07-29 02:38:25'),
(365, 5299, 5291, 'recommendation', '2026-07-29 02:38:25'),
(366, 5299, 5332, 'recommendation', '2026-07-29 02:38:25'),
(367, 5299, 5300, 'recommendation', '2026-07-29 02:38:25'),
(368, 5332, 5243, 'recommendation', '2026-07-29 02:38:25'),
(369, 5332, 5291, 'recommendation', '2026-07-29 02:38:25'),
(370, 5332, 5299, 'recommendation', '2026-07-29 02:38:25'),
(371, 5332, 5300, 'recommendation', '2026-07-29 02:38:25'),
(372, 5300, 5243, 'recommendation', '2026-07-29 02:38:25'),
(373, 5300, 5291, 'recommendation', '2026-07-29 02:38:25'),
(374, 5300, 5299, 'recommendation', '2026-07-29 02:38:25'),
(375, 5300, 5332, 'recommendation', '2026-07-29 02:38:25'),
(376, 5154, 5267, 'recommendation', '2026-07-29 02:38:25'),
(377, 5154, 5169, 'recommendation', '2026-07-29 02:38:25'),
(378, 5154, 5203, 'recommendation', '2026-07-29 02:38:25'),
(379, 5154, 5271, 'recommendation', '2026-07-29 02:38:25'),
(380, 5267, 5154, 'recommendation', '2026-07-29 02:38:25'),
(381, 5267, 5169, 'recommendation', '2026-07-29 02:38:25'),
(382, 5267, 5203, 'recommendation', '2026-07-29 02:38:25'),
(383, 5267, 5271, 'recommendation', '2026-07-29 02:38:25'),
(384, 5169, 5154, 'recommendation', '2026-07-29 02:38:25'),
(385, 5169, 5267, 'recommendation', '2026-07-29 02:38:25'),
(386, 5169, 5203, 'recommendation', '2026-07-29 02:38:25'),
(387, 5169, 5271, 'recommendation', '2026-07-29 02:38:25'),
(388, 5203, 5154, 'recommendation', '2026-07-29 02:38:25'),
(389, 5203, 5267, 'recommendation', '2026-07-29 02:38:25'),
(390, 5203, 5169, 'recommendation', '2026-07-29 02:38:25'),
(391, 5203, 5271, 'recommendation', '2026-07-29 02:38:25'),
(392, 5271, 5154, 'recommendation', '2026-07-29 02:38:25'),
(393, 5271, 5267, 'recommendation', '2026-07-29 02:38:25'),
(394, 5271, 5169, 'recommendation', '2026-07-29 02:38:25'),
(395, 5271, 5203, 'recommendation', '2026-07-29 02:38:25'),
(396, 5380, 5289, 'recommendation', '2026-07-29 02:38:25'),
(397, 5380, 5337, 'recommendation', '2026-07-29 02:38:25'),
(398, 5380, 5376, 'recommendation', '2026-07-29 02:38:25'),
(399, 5380, 5285, 'recommendation', '2026-07-29 02:38:25'),
(400, 5289, 5380, 'recommendation', '2026-07-29 02:38:25'),
(401, 5289, 5337, 'recommendation', '2026-07-29 02:38:25'),
(402, 5289, 5376, 'recommendation', '2026-07-29 02:38:25'),
(403, 5289, 5285, 'recommendation', '2026-07-29 02:38:25'),
(404, 5337, 5380, 'recommendation', '2026-07-29 02:38:25'),
(405, 5337, 5289, 'recommendation', '2026-07-29 02:38:25'),
(406, 5337, 5376, 'recommendation', '2026-07-29 02:38:25'),
(407, 5337, 5285, 'recommendation', '2026-07-29 02:38:25'),
(408, 5376, 5380, 'recommendation', '2026-07-29 02:38:25'),
(409, 5376, 5289, 'recommendation', '2026-07-29 02:38:25'),
(410, 5376, 5337, 'recommendation', '2026-07-29 02:38:25'),
(411, 5376, 5285, 'recommendation', '2026-07-29 02:38:25'),
(412, 5285, 5380, 'recommendation', '2026-07-29 02:38:25'),
(413, 5285, 5289, 'recommendation', '2026-07-29 02:38:25'),
(414, 5285, 5337, 'recommendation', '2026-07-29 02:38:25'),
(415, 5285, 5376, 'recommendation', '2026-07-29 02:38:25'),
(416, 5209, 5198, 'recommendation', '2026-07-29 02:38:25'),
(417, 5209, 5247, 'recommendation', '2026-07-29 02:38:25'),
(418, 5209, 5268, 'recommendation', '2026-07-29 02:38:25'),
(419, 5209, 5277, 'recommendation', '2026-07-29 02:38:25'),
(420, 5198, 5209, 'recommendation', '2026-07-29 02:38:25'),
(421, 5198, 5247, 'recommendation', '2026-07-29 02:38:25'),
(422, 5198, 5268, 'recommendation', '2026-07-29 02:38:25'),
(423, 5198, 5277, 'recommendation', '2026-07-29 02:38:25'),
(424, 5247, 5209, 'recommendation', '2026-07-29 02:38:25'),
(425, 5247, 5198, 'recommendation', '2026-07-29 02:38:25'),
(426, 5247, 5268, 'recommendation', '2026-07-29 02:38:25'),
(427, 5247, 5277, 'recommendation', '2026-07-29 02:38:25'),
(428, 5268, 5209, 'recommendation', '2026-07-29 02:38:25'),
(429, 5268, 5198, 'recommendation', '2026-07-29 02:38:25'),
(430, 5268, 5247, 'recommendation', '2026-07-29 02:38:25'),
(431, 5268, 5277, 'recommendation', '2026-07-29 02:38:25'),
(432, 5277, 5209, 'recommendation', '2026-07-29 02:38:25'),
(433, 5277, 5198, 'recommendation', '2026-07-29 02:38:25'),
(434, 5277, 5247, 'recommendation', '2026-07-29 02:38:25'),
(435, 5277, 5268, 'recommendation', '2026-07-29 02:38:25'),
(436, 5191, 5342, 'recommendation', '2026-07-29 02:38:25'),
(437, 5191, 5228, 'recommendation', '2026-07-29 02:38:25'),
(438, 5191, 5257, 'recommendation', '2026-07-29 02:38:25'),
(439, 5191, 5244, 'recommendation', '2026-07-29 02:38:25'),
(440, 5342, 5191, 'recommendation', '2026-07-29 02:38:25'),
(441, 5342, 5228, 'recommendation', '2026-07-29 02:38:25'),
(442, 5342, 5257, 'recommendation', '2026-07-29 02:38:25'),
(443, 5342, 5244, 'recommendation', '2026-07-29 02:38:25'),
(444, 5228, 5191, 'recommendation', '2026-07-29 02:38:25'),
(445, 5228, 5342, 'recommendation', '2026-07-29 02:38:25'),
(446, 5228, 5257, 'recommendation', '2026-07-29 02:38:25'),
(447, 5228, 5244, 'recommendation', '2026-07-29 02:38:25'),
(448, 5257, 5191, 'recommendation', '2026-07-29 02:38:25'),
(449, 5257, 5342, 'recommendation', '2026-07-29 02:38:25'),
(450, 5257, 5228, 'recommendation', '2026-07-29 02:38:25'),
(451, 5257, 5244, 'recommendation', '2026-07-29 02:38:25'),
(452, 5244, 5191, 'recommendation', '2026-07-29 02:38:25'),
(453, 5244, 5342, 'recommendation', '2026-07-29 02:38:25'),
(454, 5244, 5228, 'recommendation', '2026-07-29 02:38:25'),
(455, 5244, 5257, 'recommendation', '2026-07-29 02:38:25'),
(456, 5363, 5249, 'recommendation', '2026-07-29 02:38:25'),
(457, 5363, 5375, 'recommendation', '2026-07-29 02:38:25'),
(458, 5363, 5167, 'recommendation', '2026-07-29 02:38:25'),
(459, 5249, 5363, 'recommendation', '2026-07-29 02:38:25'),
(460, 5249, 5375, 'recommendation', '2026-07-29 02:38:25'),
(461, 5249, 5167, 'recommendation', '2026-07-29 02:38:25'),
(462, 5375, 5363, 'recommendation', '2026-07-29 02:38:25'),
(463, 5375, 5249, 'recommendation', '2026-07-29 02:38:25'),
(464, 5375, 5167, 'recommendation', '2026-07-29 02:38:25'),
(465, 5167, 5363, 'recommendation', '2026-07-29 02:38:25'),
(466, 5167, 5249, 'recommendation', '2026-07-29 02:38:25'),
(467, 5167, 5375, 'recommendation', '2026-07-29 02:38:25');

-- --------------------------------------------------------

--
-- Table structure for table `pathfinder_topics`
--

DROP TABLE IF EXISTS `pathfinder_topics`;
CREATE TABLE `pathfinder_topics` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL COMMENT 'FK -> pathfinder_categories.id',
  `name` varchar(150) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(80) DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `sort_order` tinyint(3) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','draft') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Topik dalam setiap kategori Pathfinder';

--
-- Dumping data for table `pathfinder_topics`
--

INSERT INTO `pathfinder_topics` (`id`, `category_id`, `name`, `slug`, `description`, `icon`, `banner_image`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bahasa', 'bahasa', 'Koleksi buku bahasa Indonesia, Arab, Inggris, dan tata bahasa untuk semua usia.', 'book-open-text', NULL, 1, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(2, 1, 'Fiksi & Sastra', 'fiksi-sastra', 'Novel, cerita pendek, antologi, komik, dan buku bergambar dari berbagai usia.', 'books', NULL, 2, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(3, 2, 'Lingkungan Hidup', 'lingkungan-hidup', 'Buku tentang pengelolaan sampah, perubahan iklim, pelestarian alam, dan gaya hidup ramah lingkungan.', 'leaf', NULL, 1, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(4, 2, 'Fauna (Ilmu Hewan)', 'fauna-ilmu-hewan', 'Cara merawat hewan peliharaan, satwa liar, dan peran hewan dalam ekosistem.', 'paw-print', NULL, 2, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(5, 2, 'Flora (Ilmu Tumbuhan)', 'flora-ilmu-tumbuhan', 'Menanam dan merawat tanaman hias, tanaman obat, dan peran tumbuhan dalam ekosistem.', 'potted-plant', NULL, 3, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(6, 3, 'Pertanian & Perkebunan', 'pertanian-perkebunan', 'Teknik budidaya tanaman pangan, sayuran, buah, pupuk organik, hidroponik, dan aquaponik.', 'plant', NULL, 1, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(7, 3, 'Peternakan & Budidaya Hewan', 'peternakan-budidaya-hewan', 'Cara beternak ayam, sapi, kambing, ikan, pemberian pakan, dan pencegahan penyakit ternak.', 'fish', NULL, 2, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(8, 4, 'Keluarga & Rumah Tangga', 'keluarga-rumah-tangga', 'Tips mengelola rumah, memasak, merawat lingkungan keluarga, dan hidup hemat harmonis.', 'house', NULL, 1, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(9, 4, 'Kesehatan & Pengobatan', 'kesehatan-pengobatan', 'Pola hidup sehat, penyakit umum, tanaman herbal, kesehatan anak, dan kesehatan mental.', 'heartbeat', NULL, 2, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(10, 4, 'Mengasuh Anak & Tumbuh Kembang', 'mengasuh-anak-tumbuh-kembang', 'Parenting, stimulasi tumbuh kembang, psikologi anak, dan mendidik anak dengan kasih sayang.', 'baby', NULL, 3, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(11, 5, 'Pendidikan', 'pendidikan', 'Buku pelajaran SD-SMA, metode belajar efektif, panduan mengajar, dan filosofi pendidikan.', 'graduation-cap', NULL, 1, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(12, 5, 'Sains & Pengetahuan Umum', 'sains-pengetahuan-umum', 'Ilmu pengetahuan alam, ensiklopedia, buku fakta menarik, teknologi, dan alam semesta.', 'flask', NULL, 2, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(13, 6, 'Ekonomi & Bisnis', 'ekonomi-bisnis', 'Mengelola keuangan, membangun usaha, akuntansi sederhana, perbankan syariah, dan manajemen.', 'currency-circle-dollar', NULL, 1, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(14, 7, 'Pengembangan Diri & Psikologi', 'pengembangan-diri-psikologi', 'Motivasi, manajemen diri, psikologi positif, komunikasi interpersonal, dan mengelola emosi.', 'lightning', NULL, 1, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(15, 8, 'Islam', 'islam', 'Fikih, akidah, akhlak, kisah nabi, doa sehari-hari, dan panduan ibadah untuk semua usia.', 'mosque', NULL, 1, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(16, 8, 'Sejarah & Budaya', 'sejarah-budaya', 'Sejarah Indonesia, dunia, kearifan lokal, adat istiadat, seni budaya, dan kisah peradaban.', 'map-trifold', NULL, 2, 'active', '2026-07-29 01:36:59', '2026-07-29 01:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `pathfinder_topic_introductions`
--

DROP TABLE IF EXISTS `pathfinder_topic_introductions`;
CREATE TABLE `pathfinder_topic_introductions` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL COMMENT 'FK -> pathfinder_topics.id (1:1)',
  `definition` longtext DEFAULT NULL COMMENT 'Definisi / pengertian topik (HTML)',
  `learning_objectives` longtext DEFAULT NULL COMMENT 'Tujuan pembelajaran (HTML)',
  `importance` longtext DEFAULT NULL COMMENT 'Mengapa topik ini penting (HTML)',
  `topics_to_learn` longtext DEFAULT NULL COMMENT 'Sub-topik yang dipelajari (HTML)',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Konten pendahuluan per topik Pathfinder (1:1)';

--
-- Dumping data for table `pathfinder_topic_introductions`
--

INSERT INTO `pathfinder_topic_introductions` (`id`, `topic_id`, `definition`, `learning_objectives`, `importance`, `topics_to_learn`, `created_at`, `updated_at`) VALUES
(1, 1, '<blockquote class=\"ptc-quote\">\"Kata-kata adalah jembatan. Semakin banyak bahasa yang kita kenal, semakin luas dunia yang bisa kita jangkau.\"</blockquote><p><strong>Bahasa</strong> adalah alat utama manusia untuk berkomunikasi, menyampaikan pikiran, dan memahami orang lain. Koleksi buku Bahasa di Perpustakaan Desa Teras mencakup buku pelajaran bahasa Indonesia, bahasa Arab, bahasa Inggris, dan buku tata bahasa yang bisa digunakan oleh anak-anak maupun orang dewasa.</p>', '<ul><li>Meningkatkan kemampuan membaca, menulis, dan berkomunikasi</li><li>Mengenal kosakata baru dalam bahasa Indonesia maupun bahasa asing</li><li>Memahami tata bahasa yang benar untuk keperluan sehari-hari dan pendidikan</li><li>Menumbuhkan rasa cinta terhadap bahasa sebagai warisan budaya</li></ul>', '<p>Kemampuan berbahasa yang baik membuka banyak pintu, baik dalam pendidikan, pekerjaan, maupun kehidupan sosial. Membaca buku bahasa membantu kita menulis lebih rapi, berbicara lebih jelas, dan memahami orang lain dengan lebih baik.</p>', '<ul><li>Bahasa Indonesia</li><li>Bahasa Inggris</li><li>Bahasa Jawa</li><li>Kamus & Referensi Bahasa</li><li>Belajar Bahasa Asing</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(2, 2, '<blockquote class=\"ptc-quote\">\"Buku fiksi adalah pintu masuk ke dunia lain ├óÔé¼ÔÇØ tempat imajinasi bebas terbang tanpa batas.\"</blockquote><p><strong>Fiksi & Sastra</strong> mencakup novel, cerita pendek, antologi, komik, dan buku bergambar yang bisa dinikmati oleh semua usia. Membaca fiksi bukan hanya hiburan melainkan juga dapat melatih empati, memperluas imajinasi, dan memperkaya cara pandang kita terhadap kehidupan.</p>', '<ul><li>Menumbuhkan kesenangan membaca dan cinta terhadap sastra</li><li>Melatih daya imajinasi dan kreativitas</li><li>Memahami nilai-nilai kehidupan melalui cerita</li><li>Meningkatkan empati dengan melihat dunia dari sudut pandang tokoh yang berbeda</li></ul>', '<p>Cerita fiksi membawa kita masuk ke dalam pikiran dan perasaan tokoh yang berbeda-beda. Lewat cerita, kita bisa belajar memahami orang lain, menghadapi konflik dengan lebih bijak, dan menemukan nilai-nilai kehidupan yang tidak selalu tersurat dalam buku pelajaran.</p>', '<ul><li>Novel Fiksi & Romansa</li><li>Cerita Pendek & Antologi</li><li>Komik & Novel Grafis</li><li>Puisi & Drama</li><li>Sastra Anak & Remaja</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(3, 3, '<blockquote class=\"ptc-quote\">\"Bumi bukan warisan dari nenek moyang kita ├óÔé¼ÔÇØ ia adalah titipan dari anak cucu kita. Sudah saatnya kita menjaganya.\"</blockquote><p><strong>Lingkungan Hidup</strong> berisi buku-buku tentang isu-isu lingkungan yang paling relevan dengan kehidupan kita hari ini: pengelolaan sampah, perubahan iklim, pelestarian alam, air bersih, dan gaya hidup ramah lingkungan. Koleksi ini cocok untuk semua usia yang ingin memahami kondisi lingkungan dan berkontribusi nyata dalam menjaganya.</p>', '<ul><li>Memahami kondisi lingkungan hidup dan tantangan yang dihadapi saat ini</li><li>Mengenal cara-cara praktis untuk hidup lebih ramah lingkungan</li><li>Menumbuhkan kepedulian terhadap pelestarian alam dan ekosistem</li><li>Memahami dampak perubahan iklim dan cara beradaptasi menghadapinya</li></ul>', '<p>Permasalahan lingkungan bukan isu yang hanya dibahas di seminar atau berita. Ia terasa nyata di kehidupan sehari-hari kita seperti banjir yang makin sering, sampah yang menumpuk, udara yang makin kotor. Memahami isu lingkungan adalah langkah pertama untuk menjadi bagian dari solusi, bukan bagian dari masalah.</p>', '<ul><li>Pengelolaan Sampah & 3R</li><li>Perubahan Iklim</li><li>Energi Terbarukan</li><li>Pelestarian Ekosistem</li><li>Gaya Hidup Ramah Lingkungan</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(4, 4, '<blockquote class=\"ptc-quote\">\"Setiap hewan punya peran penting dalam rantai kehidupan. Mengenal mereka berarti menjaga keseimbangan alam.\"</blockquote><p><strong>Fauna (Ilmu Hewan)</strong> berisi buku-buku tentang hewan mulai dari cara merawat hewan peliharaan, mengenal berbagai jenis satwa liar, hingga memahami peran hewan dalam ekosistem. Koleksi ini cocok untuk anak-anak yang penasaran tentang hewan, maupun orang dewasa yang ingin belajar lebih jauh tentang dunia satwa.</p>', '<ul><li>Mengenal berbagai jenis hewan dan ciri-ciri khasnya</li><li>Memahami peran hewan dalam keseimbangan ekosistem</li><li>Menumbuhkan sikap peduli dan bertanggung jawab terhadap satwa</li><li>Menambah wawasan tentang dunia hewan yang beragam</li></ul>', '<p>Hewan bukan sekadar makhluk yang hidup di sekitar kita. Mereka adalah bagian dari ekosistem yang saling bergantung, termasuk dengan manusia. Memahami dunia fauna membantu kita lebih menghargai alam dan ikut menjaga kelestariannya.</p>', '<ul><li>Hewan Peliharaan</li><li>Satwa Liar & Konservasi</li><li>Peran Hewan dalam Ekosistem</li><li>Hewan Air & Kehidupan Laut</li><li>Ilmu Hewan Terapan</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(5, 5, '<blockquote class=\"ptc-quote\">\"Membangun kesadaran kolektif untuk masa depan bumi yang lebih hijau. Mari belajar menjaga ekosistem kita mulai dari langkah kecil di teras rumah.\"</blockquote><p><strong>Flora (Ilmu Tumbuhan)</strong> berisi buku-buku tentang dunia tumbuhan, dari cara menanam dan merawat tanaman hias, mengenal tanaman obat tradisional, hingga memahami peran tumbuhan dalam menjaga keseimbangan alam. Koleksi ini cocok untuk siapa saja yang ingin lebih dekat dengan alam hijau di sekitarnya.</p>', '<ul><li>Mengenal berbagai jenis tumbuhan dan manfaatnya bagi kehidupan</li><li>Memahami cara merawat tanaman dengan baik dan benar</li><li>Menumbuhkan kesadaran akan pentingnya menjaga keanekaragaman hayati</li><li>Mengenal tanaman obat tradisional sebagai bagian dari kearifan lokal</li></ul>', '<p>Tumbuhan bukan sekadar dekorasi rumah atau sumber makanan. Mereka adalah paru-paru bumi yang menyerap karbon dioksida dan menghasilkan oksigen yang kita hirup setiap saat. Memahami dunia flora adalah langkah pertama untuk menjadi penjaga lingkungan yang baik.</p>', '<ul><li>Tanaman Hias & Taman Rumah</li><li>Tanaman Obat Tradisional</li><li>Botani & Ekologi Tumbuhan</li><li>Berkebun & Urban Farming</li><li>Keanekaragaman Hayati Flora</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(6, 6, '<blockquote class=\"ptc-quote\">\"Di setiap butir beras yang kita makan, ada kerja keras petani yang menyentuh tanah setiap harinya. Kenali dan hargai mereka.\"</blockquote><p><strong>Pertanian & Perkebunan</strong> adalah koleksi terbesar di Perpustakaan Desa Teras. Berisi buku-buku tentang teknik budidaya tanaman pangan, sayuran, buah-buahan, tanaman perkebunan, pupuk dan pestisida organik, hidroponik, aquaponik, hingga pengolahan hasil pertanian. Koleksi ini sangat relevan dengan kehidupan masyarakat Desa Teras yang mayoritas berprofesi atau berkaitan erat dengan pertanian.</p>', '<ul><li>Mempelajari teknik budidaya tanaman yang efektif dan ramah lingkungan</li><li>Mengenal berbagai metode pertanian modern seperti hidroponik dan aquaponik</li><li>Memahami cara membuat dan menggunakan pupuk organik secara mandiri</li><li>Mendukung produktivitas petani lokal dengan pengetahuan yang terbarukan</li></ul>', '<p>Pertanian adalah pondasi kehidupan. Pengetahuan tentang teknik bertani yang baik dan berkelanjutan membantu petani meningkatkan hasil panen, menjaga kesuburan tanah, dan menciptakan pertanian yang tidak merusak lingkungan untuk generasi berikutnya.</p>', '<ul><li>Teknik Budidaya Tanaman Pangan</li><li>Sayuran & Buah-buahan</li><li>Hidroponik & Aquaponik</li><li>Pupuk Organik & Pestisida Alami</li><li>Pengolahan Hasil Pertanian</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(7, 7, '<blockquote class=\"ptc-quote\">\"Ternak yang sehat bermula dari peternak yang berpengetahuan. Rawat dengan baik, hasilnya akan berbicara sendiri.\"</blockquote><p><strong>Peternakan & Budidaya Hewan</strong> berisi buku-buku tentang cara beternak ayam, sapi, kambing, ikan, dan berbagai hewan budidaya lainnya. Koleksi ini mencakup panduan pemberian pakan, penanganan penyakit ternak, manajemen kandang, hingga cara memulai usaha peternakan dari nol.</p>', '<ul><li>Memahami teknik beternak yang baik dan benar untuk berbagai jenis hewan</li><li>Mengenal cara mencegah dan menangani penyakit pada hewan ternak</li><li>Mendapatkan panduan memulai usaha peternakan skala kecil hingga menengah</li><li>Mengenal cara budidaya ikan dan hewan air lainnya sebagai alternatif usaha</li></ul>', '<p>Peternakan adalah salah satu sumber penghasilan penting bagi banyak keluarga di desa. Dengan pengetahuan yang tepat, peternak bisa meningkatkan produktivitas, menekan kerugian akibat penyakit, dan mengembangkan usaha ternaknya secara berkelanjutan.</p>', '<ul><li>Budidaya Ayam & Unggas</li><li>Peternakan Sapi & Kambing</li><li>Budidaya Ikan & Kolam</li><li>Manajemen Kandang & Pakan</li><li>Pencegahan Penyakit Ternak</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(8, 8, '<blockquote class=\"ptc-quote\">\"Keluarga yang bahagia dibangun satu langkah kecil setiap hari. Mulai dari dapur, taman, hingga cara kita berbicara satu sama lain.\"</blockquote><p><strong>Keluarga & Rumah Tangga</strong> berisi buku-buku tentang tips mengelola rumah, memasak, merawat lingkungan keluarga, hingga tips hidup hemat dan harmonis. Koleksi ini cocok untuk ibu rumah tangga, pasangan muda, maupun siapa saja yang ingin menciptakan rumah yang nyaman dan bahagia.</p>', '<ul><li>Mendapatkan inspirasi dan tips praktis dalam mengelola rumah tangga</li><li>Menemukan ide-ide baru untuk menciptakan suasana rumah yang nyaman</li><li>Memahami cara mengelola keuangan rumah tangga dengan bijak</li><li>Mengenal cara merawat dan mempercantik lingkungan rumah secara sederhana</li></ul>', '<p>Rumah yang nyaman bukan soal besar atau mewahnya bangunan, melainkan tentang bagaimana kita mengelolanya dengan baik. Buku-buku dalam koleksi ini memberikan inspirasi dan panduan praktis untuk membuat kehidupan rumah tangga lebih teratur, efisien, dan menyenangkan.</p>', '<ul><li>Memasak & Kuliner Rumahan</li><li>Manajemen Keuangan Keluarga</li><li>Dekorasi & Perawatan Rumah</li><li>Hubungan Keluarga & Komunikasi</li><li>Hidup Hemat & Minimalis</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(9, 9, '<blockquote class=\"ptc-quote\">\"Kesehatan adalah investasi terbaik yang bisa kita berikan untuk diri sendiri dan keluarga.\"</blockquote><p><strong>Kesehatan & Pengobatan</strong> mencakup buku-buku tentang pola hidup sehat, penanganan penyakit umum, tanaman herbal dan obat tradisional, kesehatan anak, hingga tips menjaga kesehatan mental. Koleksi ini bisa menjadi panduan pertama sebelum berkonsultasi ke dokter, atau sumber pengetahuan untuk menjaga kesehatan keluarga sehari-hari.</p>', '<ul><li>Memahami cara menjaga kesehatan tubuh melalui pola hidup sehat</li><li>Mengenal gejala penyakit umum dan cara penanganan awalnya</li><li>Mengenal tanaman herbal dan pengobatan tradisional yang aman</li><li>Meningkatkan kesadaran tentang pentingnya kesehatan mental</li></ul>', '<p>Pengetahuan kesehatan yang baik membantu kita mencegah penyakit sebelum terlanjur parah. Memahami tubuh sendiri, mengenali gejala awal penyakit, dan tahu pertolongan pertama yang tepat adalah bekal penting yang seharusnya dimiliki setiap anggota keluarga.</p>', '<ul><li>Pola Hidup Sehat</li><li>Penyakit Umum & Pertolongan Pertama</li><li>Tanaman Herbal & Obat Tradisional</li><li>Kesehatan Mental</li><li>Gizi & Nutrisi</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(10, 10, '<blockquote class=\"ptc-quote\">\"Setiap anak adalah keajaiban. Mendampingi tumbuh kembangnya dengan penuh kesadaran adalah hadiah terbesar yang bisa diberikan orang tua.\"</blockquote><p><strong>Mengasuh Anak & Tumbuh Kembang</strong> berisi buku-buku tentang parenting, stimulasi tumbuh kembang anak, psikologi anak, pola makan sehat untuk anak, hingga cara mendidik anak dengan penuh kasih sayang. Koleksi ini hadir untuk mendampingi orang tua dan pengasuh dalam menjalani perjalanan luar biasa membesarkan anak.</p>', '<ul><li>Memahami tahapan tumbuh kembang anak sesuai usianya</li><li>Mendapatkan panduan praktis dalam mengasuh anak dengan penuh kasih</li><li>Mengenal cara memberikan stimulasi yang tepat untuk perkembangan anak</li><li>Memahami cara menghadapi tantangan pengasuhan dengan lebih tenang</li></ul>', '<p>Setiap tahap tumbuh kembang anak menyimpan keajaiban sekaligus tantangan tersendiri. Dengan pemahaman yang baik tentang kebutuhan dan perkembangan anak, orang tua bisa lebih percaya diri, lebih sabar, dan lebih hadir secara emosional untuk buah hati mereka.</p>', '<ul><li>Parenting & Pengasuhan</li><li>Stimulasi Tumbuh Kembang</li><li>Psikologi Anak</li><li>Nutrisi & Gizi Anak</li><li>Pendidikan Karakter Anak</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(11, 11, '<blockquote class=\"ptc-quote\">\"Belajar tidak mengenal usia. Selama ada niat, selalu ada jalan untuk tumbuh menjadi lebih baik.\"</blockquote><p><strong>Pendidikan</strong> berisi buku-buku pelajaran untuk berbagai jenjang sekolah, buku tentang metode belajar efektif, panduan mengajar untuk guru, hingga buku tentang filosofi dan sistem pendidikan. Koleksi ini cocok untuk siswa, guru, orang tua, maupun siapa saja yang percaya bahwa pendidikan adalah kunci perubahan.</p>', '<ul><li>Mendukung proses belajar siswa dari berbagai jenjang pendidikan</li><li>Memberikan inspirasi dan metode mengajar bagi para pendidik</li><li>Memahami pentingnya pendidikan karakter di samping akademis</li><li>Menumbuhkan motivasi dan semangat belajar sepanjang hayat</li></ul>', '<p>Pendidikan yang baik bukan hanya soal nilai di rapor. Ia tentang rasa ingin tahu yang terjaga, kemampuan berpikir kritis, dan semangat untuk terus belajar sepanjang hayat. Buku-buku dalam koleksi ini hadir untuk mendukung perjalanan belajar dari semua sisi, bagi yang belajar, maupun yang mengajar.</p>', '<ul><li>Buku Pelajaran SD-SMA</li><li>Metode Belajar Efektif</li><li>Panduan Mengajar & Kurikulum</li><li>Pendidikan Karakter</li><li>Pendidikan Berbasis Nilai Lokal</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(12, 12, '<blockquote class=\"ptc-quote\">\"Rasa ingin tahu adalah bahan bakar sains. Dan setiap pertanyaan yang kamu ajukan adalah awal dari penemuan baru.\"</blockquote><p><strong>Sains & Pengetahuan Umum</strong> berisi buku-buku tentang ilmu pengetahuan alam, ensiklopedia, buku fakta menarik, pengetahuan tentang alam semesta, teknologi, dan berbagai topik sains yang disajikan dengan cara yang mudah dipahami. Koleksi ini cocok untuk anak-anak yang suka bertanya, remaja yang penasaran, maupun orang dewasa yang ingin menyegarkan pengetahuannya.</p>', '<ul><li>Memahami konsep-konsep sains dasar dengan cara yang menyenangkan</li><li>Mengenal berbagai fakta menarik tentang alam, manusia, dan alam semesta</li><li>Menumbuhkan rasa ingin tahu dan kemampuan berpikir ilmiah</li><li>Mendukung proses belajar sains di sekolah dengan referensi tambahan</li></ul>', '<p>Pengetahuan sains membantu kita memahami dunia dengan cara yang lebih rasional dan berbasis fakta. Di era informasi seperti sekarang, kemampuan berpikir ilmiah dan kritis menjadi bekal penting agar kita tidak mudah tertipu oleh informasi yang salah.</p>', '<ul><li>Ilmu Pengetahuan Alam</li><li>Ensiklopedia & Buku Fakta</li><li>Teknologi & Inovasi</li><li>Alam Semesta & Astronomi</li><li>Matematika & Logika</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(13, 13, '<blockquote class=\"ptc-quote\">\"Memahami ekonomi bukan hanya urusan pengusaha besar. Setiap rumah tangga pun perlu melek keuangan.\"</blockquote><p><strong>Ekonomi & Bisnis</strong> berisi buku-buku tentang cara mengelola keuangan, membangun usaha, memahami akuntansi sederhana, perpajakan, perbankan syariah, hingga manajemen dan kepemimpinan. Koleksi ini cocok untuk siapa saja yang ingin lebih mandiri secara finansial atau punya mimpi membangun usaha sendiri.</p>', '<ul><li>Memahami dasar-dasar pengelolaan keuangan rumah tangga</li><li>Mengenal peluang usaha dan cara memulai bisnis dari nol</li><li>Memahami konsep akuntansi dan pembukuan sederhana</li><li>Mengenal sistem perbankan dan keuangan syariah</li></ul>', '<p>Pengetahuan ekonomi dan bisnis membantu kita membuat keputusan keuangan yang lebih bijak. Dari mengelola tabungan keluarga, memulai usaha kecil di rumah, hingga memahami hak dan kewajiban sebagai wajib pajak.</p>', '<ul><li>Manajemen Keuangan Keluarga</li><li>Memulai Usaha & UMKM</li><li>Akuntansi & Pembukuan Sederhana</li><li>Pemasaran & Branding</li><li>Keuangan Syariah</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(14, 14, '<blockquote class=\"ptc-quote\">\"Versi terbaik dirimu sudah ada di dalam sana. Buku hanya membantumu menemukannya.\"</blockquote><p><strong>Pengembangan Diri & Psikologi</strong> berisi buku-buku tentang motivasi, manajemen diri, psikologi positif, komunikasi interpersonal, hingga cara memahami emosi dan pikiran kita sendiri. Koleksi ini cocok untuk siapa saja yang ingin tumbuh menjadi versi yang lebih baik dari dirinya. Baik dalam kehidupan pribadi, keluarga, maupun sosial.</p>', '<ul><li>Mengenal diri sendiri lebih dalam melalui pemahaman psikologi</li><li>Mendapatkan panduan praktis untuk pengembangan diri dan karier</li><li>Memahami cara mengelola emosi dan menghadapi tekanan hidup</li><li>Membangun kebiasaan positif yang mendukung pertumbuhan pribadi</li></ul>', '<p>Mengenal diri sendiri adalah perjalanan paling penting yang bisa dilakukan manusia. Dengan memahami cara kerja pikiran dan emosi, kita bisa membuat keputusan yang lebih bijak, membangun hubungan yang lebih sehat, dan menjalani hidup dengan lebih bermakna.</p>', '<ul><li>Manajemen Diri & Produktivitas</li><li>Psikologi Positif</li><li>Komunikasi & Hubungan Sosial</li><li>Motivasi & Mindset</li><li>Kesehatan Mental & Emosi</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(15, 15, '<blockquote class=\"ptc-quote\">\"Ilmu adalah cahaya. Membaca buku Islam adalah salah satu cara kita mendekat pada cahaya itu.\"</blockquote><p><strong>Islam</strong> di Perpustakaan Desa Teras mencakup buku-buku tentang fikih, akidah, akhlak, kisah para nabi, doa sehari-hari, hingga panduan ibadah yang bisa dipahami oleh anak-anak maupun orang dewasa. Koleksi ini hadir untuk mendampingi perjalanan belajar agama seluruh anggota keluarga.</p>', '<ul><li>Memperdalam pemahaman tentang ajaran Islam sesuai usia dan kemampuan</li><li>Mengenal kisah-kisah teladan dari para nabi dan sahabat</li><li>Memahami panduan ibadah sehari-hari secara praktis</li><li>Menumbuhkan akhlak yang baik dalam kehidupan bermasyarakat</li></ul>', '<p>Memahami agama secara mendalam membantu kita menjalani kehidupan dengan lebih terarah, penuh makna, dan berakhlak mulia. Buku-buku Islam dalam koleksi ini disajikan dengan bahasa yang mudah dipahami karena ilmu agama seharusnya bisa diakses oleh semua orang, bukan hanya yang sudah fasih.</p>', '<ul><li>Fikih Ibadah Sehari-hari</li><li>Akidah & Tauhid</li><li>Kisah Nabi & Sahabat</li><li>Akhlak & Adab</li><li>Pendidikan Islam Anak</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59'),
(16, 16, '<blockquote class=\"ptc-quote\">\"Bangsa yang besar adalah bangsa yang menghargai sejarahnya. Kenali akar kita agar kita tahu ke mana harus melangkah.\"</blockquote><p><strong>Sejarah & Budaya</strong> berisi buku-buku tentang sejarah Indonesia, sejarah dunia, kearifan lokal, adat istiadat, seni budaya, dan kisah-kisah peradaban yang membentuk dunia seperti yang kita kenal sekarang. Koleksi ini cocok untuk siapa saja yang ingin memahami dari mana kita berasal dan apa yang membentuk identitas kita sebagai bangsa.</p>', '<ul><li>Memahami perjalanan sejarah Indonesia dan dunia secara menarik</li><li>Mengenal kekayaan budaya lokal dan kearifan tradisional yang perlu dijaga</li><li>Menumbuhkan rasa bangga dan cinta terhadap identitas bangsa</li></ul>', '<p>Sejarah bukan sekadar deretan tanggal dan nama. Ia adalah cermin yang menunjukkan perjuangan, kesalahan, dan keberhasilan generasi sebelum kita. Dengan memahami sejarah, kita belajar lebih bijak dan tidak mengulang kesalahan yang sama.</p>', '<ul><li>Sejarah Indonesia</li><li>Sejarah Dunia & Peradaban</li><li>Kebudayaan & Kearifan Lokal</li><li>Seni & Tradisi Nusantara</li><li>Tokoh & Pahlawan Bangsa</li></ul>', '2026-07-29 01:36:59', '2026-07-29 01:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `pathfinder_topic_mapping`
--

DROP TABLE IF EXISTS `pathfinder_topic_mapping`;
CREATE TABLE `pathfinder_topic_mapping` (
  `id` int(11) NOT NULL,
  `pathfinder_topic_id` int(11) NOT NULL COMMENT 'FK -> pathfinder_topics.id',
  `slims_topic_id` int(11) NOT NULL COMMENT 'Ref -> mst_topic.topic_id',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pemetaan topik Pathfinder ke subyek SLiMS (mst_topic)';

--
-- Dumping data for table `pathfinder_topic_mapping`
--

INSERT INTO `pathfinder_topic_mapping` (`id`, `pathfinder_topic_id`, `slims_topic_id`, `created_at`) VALUES
(1, 1, 1285, '2026-07-29 01:36:59'),
(2, 1, 1281, '2026-07-29 01:36:59'),
(3, 1, 1276, '2026-07-29 01:36:59'),
(4, 1, 1197, '2026-07-29 01:36:59'),
(5, 1, 1196, '2026-07-29 01:36:59'),
(6, 1, 1109, '2026-07-29 01:36:59'),
(7, 2, 1260, '2026-07-29 01:36:59'),
(8, 3, 1272, '2026-07-29 01:36:59'),
(9, 4, 1222, '2026-07-29 01:36:59'),
(10, 5, 1265, '2026-07-29 01:36:59'),
(11, 6, 1270, '2026-07-29 01:36:59'),
(12, 6, 1279, '2026-07-29 01:36:59'),
(13, 8, 1289, '2026-07-29 01:36:59'),
(14, 9, 965, '2026-07-29 01:36:59'),
(15, 10, 1278, '2026-07-29 01:36:59'),
(16, 11, 1303, '2026-07-29 01:36:59'),
(17, 11, 1299, '2026-07-29 01:36:59'),
(18, 11, 1293, '2026-07-29 01:36:59'),
(19, 11, 1108, '2026-07-29 01:36:59'),
(20, 12, 1309, '2026-07-29 01:36:59'),
(21, 13, 1275, '2026-07-29 01:36:59'),
(22, 14, 1277, '2026-07-29 01:36:59'),
(23, 15, 1280, '2026-07-29 01:36:59'),
(24, 16, 1188, '2026-07-29 01:36:59'),
(25, 16, 1187, '2026-07-29 01:36:59'),
(26, 16, 1186, '2026-07-29 01:36:59'),
(27, 16, 1185, '2026-07-29 01:36:59'),
(28, 16, 1184, '2026-07-29 01:36:59'),
(29, 16, 1183, '2026-07-29 01:36:59'),
(30, 16, 1182, '2026-07-29 01:36:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pathfinder_categories`
--
ALTER TABLE `pathfinder_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categories_slug` (`slug`),
  ADD KEY `idx_categories_status_sort` (`status`,`sort_order`);

--
-- Indexes for table `pathfinder_downloads`
--
ALTER TABLE `pathfinder_downloads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_downloads_topic_status` (`topic_id`,`status`);

--
-- Indexes for table `pathfinder_external_resources`
--
ALTER TABLE `pathfinder_external_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ext_resources_topic_type` (`topic_id`,`resource_type`),
  ADD KEY `idx_ext_resources_topic_status` (`topic_id`,`status`,`sort_order`);

--
-- Indexes for table `pathfinder_guides`
--
ALTER TABLE `pathfinder_guides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_guides_slug` (`slug`),
  ADD KEY `idx_guides_topic_status` (`topic_id`,`status`),
  ADD KEY `idx_guides_sort` (`topic_id`,`sort_order`);

--
-- Indexes for table `pathfinder_related_books`
--
ALTER TABLE `pathfinder_related_books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_related_books` (`biblio_id`,`related_biblio_id`),
  ADD KEY `idx_related_biblio_id` (`related_biblio_id`),
  ADD KEY `idx_related_type` (`relation_type`);

--
-- Indexes for table `pathfinder_topics`
--
ALTER TABLE `pathfinder_topics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_topics_slug` (`slug`),
  ADD KEY `idx_topics_category_id` (`category_id`),
  ADD KEY `idx_topics_status_sort` (`status`,`sort_order`);

--
-- Indexes for table `pathfinder_topic_introductions`
--
ALTER TABLE `pathfinder_topic_introductions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `topic_id` (`topic_id`);

--
-- Indexes for table `pathfinder_topic_mapping`
--
ALTER TABLE `pathfinder_topic_mapping`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_mapping_topic_slims` (`pathfinder_topic_id`,`slims_topic_id`),
  ADD KEY `idx_mapping_slims_topic_id` (`slims_topic_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pathfinder_categories`
--
ALTER TABLE `pathfinder_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pathfinder_downloads`
--
ALTER TABLE `pathfinder_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pathfinder_external_resources`
--
ALTER TABLE `pathfinder_external_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `pathfinder_guides`
--
ALTER TABLE `pathfinder_guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pathfinder_related_books`
--
ALTER TABLE `pathfinder_related_books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=468;

--
-- AUTO_INCREMENT for table `pathfinder_topics`
--
ALTER TABLE `pathfinder_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `pathfinder_topic_introductions`
--
ALTER TABLE `pathfinder_topic_introductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `pathfinder_topic_mapping`
--
ALTER TABLE `pathfinder_topic_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pathfinder_downloads`
--
ALTER TABLE `pathfinder_downloads`
  ADD CONSTRAINT `fk_downloads_topic` FOREIGN KEY (`topic_id`) REFERENCES `pathfinder_topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pathfinder_external_resources`
--
ALTER TABLE `pathfinder_external_resources`
  ADD CONSTRAINT `fk_ext_resources_topic` FOREIGN KEY (`topic_id`) REFERENCES `pathfinder_topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pathfinder_guides`
--
ALTER TABLE `pathfinder_guides`
  ADD CONSTRAINT `fk_guides_topic` FOREIGN KEY (`topic_id`) REFERENCES `pathfinder_topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pathfinder_topics`
--
ALTER TABLE `pathfinder_topics`
  ADD CONSTRAINT `fk_topics_category` FOREIGN KEY (`category_id`) REFERENCES `pathfinder_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pathfinder_topic_introductions`
--
ALTER TABLE `pathfinder_topic_introductions`
  ADD CONSTRAINT `fk_intro_topic` FOREIGN KEY (`topic_id`) REFERENCES `pathfinder_topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pathfinder_topic_mapping`
--
ALTER TABLE `pathfinder_topic_mapping`
  ADD CONSTRAINT `fk_mapping_pathfinder_topic` FOREIGN KEY (`pathfinder_topic_id`) REFERENCES `pathfinder_topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
