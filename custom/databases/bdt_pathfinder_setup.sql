-- --------------------------------------------------------
-- Table structure for table `bdt_pathfinder_category`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `bdt_pathfinder_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `bdt_pathfinder`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `bdt_pathfinder` (
  `pathfinder_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `badge` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recommendations` int(11) DEFAULT 0,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `broader_terms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`broader_terms`)),
  `narrower_terms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`narrower_terms`)),
  `related_terms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`related_terms`)),
  `catalog_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `books` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`books`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`pathfinder_id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_pathfinder_category` (`category_id`),
  CONSTRAINT `fk_pathfinder_category` FOREIGN KEY (`category_id`) REFERENCES `bdt_pathfinder_category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Dummy Data Insertion
-- --------------------------------------------------------

INSERT INTO `bdt_pathfinder_category` (`category_id`, `slug`, `label`, `icon`, `description`, `tags`) VALUES
(1, 'teknologi', 'Teknologi', 'globe', 'Jelajahi kumpulan panduan literasi dan referensi pilihan di bidang teknologi informasi.', '["AI & Robotics", "Software Engineering", "Data Science", "Cybersecurity", "Cloud Computing"]'),
(2, 'sains', 'Sains', 'flask', 'Temukan referensi ilmiah terkurasi di bidang sains murni dan terapan.', '["Fisika", "Biologi", "Kimia", "Matematika", "Lingkungan"]'),
(3, 'seni-humaniora', 'Seni & Humaniora', 'palette', 'Eksplorasi dunia seni, sastra, sejarah, dan filsafat.', '["Sastra", "Sejarah", "Filsafat", "Seni Rupa", "Musik"]'),
(4, 'ekonomi', 'Ekonomi', 'chart', 'Akses panduan komprehensif tentang ekonomi, bisnis, dan keuangan.', '["Manajemen", "Akuntansi", "Pemasaran", "Keuangan", "Kewirausahaan"]'),
(5, 'hukum', 'Hukum', 'scale', 'Panduan literasi hukum yang mengkurasi referensi penting.', '["Hukum Perdata", "Hukum Pidana", "Hukum Tata Negara", "HAM", "Hukum Bisnis"]');

INSERT INTO `bdt_pathfinder` (`pathfinder_id`, `category_id`, `slug`, `title`, `badge`, `recommendations`, `description`, `broader_terms`, `narrower_terms`, `related_terms`, `catalog_url`, `author`, `books`) VALUES
(1, 1, 'pemrograman-web', 'Pemrograman Web: Fundamental ke Modern', 'TERPOPULER', 24, 'Pemrograman Web mencakup pemahaman mendalam tentang arsitektur client-server, manipulasi DOM, dan manajemen state untuk membangun aplikasi interaktif.', '["Ilmu Komputer", "Rekayasa Perangkat Lunak"]', '["Pengembangan Frontend", "Sistem Backend"]', '["Desain UX/UI", "Aksesibilitas Web"]', 'bacaditeras.my.id/katalog', 'Nicholas Sio Pradiva', '[{"title": "Learning Web Design", "author": "Jennifer Robbins", "year": 2018, "publisher": "O''Reilly Media", "call_number": "006.7 ROB l", "isbn": "978-1-491-96020-2", "location": "Perpustakaan Utama - Bagian Teknologi"}, {"title": "JavaScript: The Definitive Guide", "author": "David Flanagan", "year": 2020, "publisher": "O''Reilly Media", "call_number": "005.133 FLA j", "isbn": "978-1-491-95202-3", "location": "Perpustakaan Digital Teras"}]'),
(2, 4, 'pengantar-akuntansi', 'Pengantar Akuntansi untuk Pemula', 'BARU', 12, 'Panduan dasar akuntansi keuangan dan manajerial.', '["Ilmu Ekonomi", "Keuangan"]', '["Akuntansi Biaya", "Audit"]', '["Pajak", "Manajemen Keuangan"]', 'bacaditeras.my.id/katalog', 'Admin', '[]');
