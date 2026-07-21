-- ============================================================
-- Baca Di Teras — Information & Village Extension Tables
-- ============================================================
--
-- File    : bdt_info_village_extension.sql
-- Project : Baca Di Teras
-- Version : 1.0.0
-- Database: bacaditeras
--
-- Tabel tambahan:
--   1. bdt_village_profile  → Profil dan kontak utama desa
--   2. bdt_feature          → Fitur/layanan di landing page
--   3. bdt_information      → FAQ, jadwal, panduan, unduhan
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;

-- ============================================================
-- 1. bdt_village_profile
-- ============================================================

CREATE TABLE IF NOT EXISTS `bdt_village_profile` (
    `profile_id`      INT(11)       NOT NULL AUTO_INCREMENT,
    `village_name`    VARCHAR(150)  NOT NULL DEFAULT 'Desa Teras',
    `tagline`         VARCHAR(255)  DEFAULT NULL,
    `description`     TEXT          DEFAULT NULL,
    `sejarah`         TEXT          DEFAULT NULL,
    `address`         VARCHAR(255)  DEFAULT NULL,
    `district`        VARCHAR(100)  DEFAULT NULL  COMMENT 'Kecamatan',
    `regency`         VARCHAR(100)  DEFAULT NULL  COMMENT 'Kabupaten',
    `province`        VARCHAR(100)  DEFAULT NULL  COMMENT 'Provinsi',
    `phone`           VARCHAR(20)   DEFAULT NULL,
    `email`           VARCHAR(100)  DEFAULT NULL,
    `whatsapp`        VARCHAR(20)   DEFAULT NULL,
    `website`         VARCHAR(100)  DEFAULT NULL,
    `google_maps_url` TEXT          DEFAULT NULL  COMMENT 'URL embed Google Maps',
    `latitude`        DECIMAL(10,8) DEFAULT NULL,
    `longitude`       DECIMAL(11,8) DEFAULT NULL,
    `logo_path`       VARCHAR(255)  DEFAULT NULL,
    `hero_image_path` VARCHAR(255)  DEFAULT NULL,
    `updated_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Profil utama Desa Teras (satu baris)';


-- ============================================================
-- 2. bdt_feature
-- ============================================================

CREATE TABLE IF NOT EXISTS `bdt_feature` (
    `feature_id`  INT(11)       NOT NULL AUTO_INCREMENT,
    `icon`        TEXT          DEFAULT NULL  COMMENT 'SVG path string (isi <svg>)',
    `title`       VARCHAR(150)  NOT NULL,
    `description` TEXT          DEFAULT NULL,
    `href`        VARCHAR(255)  DEFAULT '#',
    `status`      ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    `sort_order`  INT(3)        NOT NULL DEFAULT 0,
    PRIMARY KEY (`feature_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Daftar fitur/layanan yang ditampilkan di landing page';


-- ============================================================
-- 3. bdt_information
-- ============================================================

CREATE TABLE IF NOT EXISTS `bdt_information` (
    `info_id`    INT(11)      NOT NULL AUTO_INCREMENT,
    `id_name`    VARCHAR(100) DEFAULT NULL  COMMENT 'Identifier unik, misal: peminjaman, keanggotaan',
    `type`       ENUM('faq','service','rule','schedule','download','lainnya') NOT NULL DEFAULT 'lainnya',
    `title`      VARCHAR(255) NOT NULL,
    `content`    TEXT         DEFAULT NULL,
    `extra_data` JSON         DEFAULT NULL  COMMENT 'Data tambahan: poin, action label, dsb.',
    `status`     ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    `sort_order` INT(3)       NOT NULL DEFAULT 0,
    PRIMARY KEY (`info_id`),
    INDEX `idx_type`   (`type`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Informasi umum: FAQ, layanan, jadwal, unduhan';


-- ============================================================
-- SEED DATA — bdt_village_profile
-- ============================================================

INSERT INTO `bdt_village_profile`
    (`village_name`, `tagline`, `description`, `sejarah`,
     `address`, `district`, `regency`, `province`,
     `phone`, `email`, `whatsapp`, `website`,
     `google_maps_url`, `latitude`, `longitude`)
VALUES (
    'Desa Teras',
    'Portal Literasi Digital Desa Teras',
    'Baca Di Teras merupakan portal literasi digital yang lahir dari semangat komunitas Desa Teras, Boyolali. Kami percaya bahwa membaca adalah jendela dunia yang harus bisa diakses oleh semua kalangan, tanpa terkecuali — dari anak-anak hingga orang tua.',
    'Baca Di Teras berdiri sejak lebih dari 10 tahun lalu sebagai gerakan literasi grassroots yang digagas oleh para tokoh masyarakat Desa Teras. Berawal dari satu rak buku sederhana di teras rumah, kini telah berkembang menjadi jaringan 6 perpustakaan dengan ribuan koleksi.',
    'Jl. Raya Teras, Desa Teras',
    'Teras',
    'Boyolali',
    'Jawa Tengah',
    '+62 271-781000',
    'contact@desateras.id',
    '+62 812-3456-7890',
    'https://desateras.go.id',
    'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.034!2d110.648!3d-7.515!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sTeras%2C+Boyolali!5e0!3m2!1sid!2sid!4v0000000000000',
    -7.51500000,
    110.64800000
);


-- ============================================================
-- SEED DATA — bdt_feature
-- ============================================================

INSERT INTO `bdt_feature` (`icon`, `title`, `description`, `href`, `status`, `sort_order`) VALUES
(
    '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
    'Layar Peminjaman',
    'Pinjam dan kembalikan buku secara digital tanpa perlu antri. Semua proses cukup lewat smartphone Anda.',
    '/layanan/peminjaman',
    'aktif',
    1
),
(
    '<rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>',
    'Perpustakaan Digital',
    'Akses ribuan koleksi buku, jurnal, dan artikel kapan saja dan di mana saja secara gratis.',
    '/layanan/digital',
    'aktif',
    2
),
(
    '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
    'Acara & Workshop',
    'Kegiatan literasi rutin setiap bulan untuk semua kalangan: anak-anak, remaja, hingga orang tua.',
    '/layanan/acara',
    'aktif',
    3
),
(
    '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
    'Keanggotaan Gratis',
    'Daftar menjadi anggota secara gratis dan nikmati seluruh fasilitas perpustakaan tanpa biaya apapun.',
    '/daftar',
    'aktif',
    4
);


-- ============================================================
-- SEED DATA — bdt_information (FAQ)
-- ============================================================

INSERT INTO `bdt_information` (`type`, `title`, `content`, `status`, `sort_order`) VALUES
('faq', 'Bagaimana jika saya terlambat mengembalikan buku?',
 'Anda akan dikenakan sanksi berupa denda administratif ringan atau pembatasan peminjaman sementara waktu sesuai tata tertib perpustakaan untuk menjaga sirkulasi buku tetap lancar.',
 'aktif', 1),

('faq', 'Apakah warga luar Desa Teras boleh meminjam buku?',
 'Tentu saja! Siapa pun boleh membaca di tempat. Untuk peminjaman ke rumah, Anda perlu mendaftar menjadi anggota dengan menyertakan kartu identitas resmi.',
 'aktif', 2),

('faq', 'Bisakah saya menyumbangkan buku ke perpustakaan?',
 'Sangat bisa! Kami menerima donasi buku layak baca untuk disalurkan ke 6 perpustakaan desa. Silakan hubungi pustakawan kami untuk informasi alur donasi lebih lanjut.',
 'aktif', 3),

('faq', 'Apakah ada akses internet/WiFi di area baca?',
 'Ya, seluruh perpustakaan desa kami difasilitasi dengan koneksi internet nirkabel (WiFi) gratis untuk menunjang aktivitas belajar dan literasi digital warga.',
 'aktif', 4);


-- ============================================================
-- SEED DATA — bdt_information (Service)
-- ============================================================

INSERT INTO `bdt_information` (`id_name`, `type`, `title`, `content`, `extra_data`, `status`, `sort_order`) VALUES
('peminjaman', 'service', 'Panduan Peminjaman',
 'Pelajari langkah mudah meminjam buku favorit Anda. Kami mendukung kemudahan akses literasi untuk seluruh warga.',
 '{"points":["Maksimal 3 buku per anggota","Durasi pinjam selama 14 hari","Perpanjangan dapat dilakukan 1x"],"action_label":"Lihat Detail Alur","action_href":"#"}',
 'aktif', 1),

('keanggotaan', 'service', 'Keanggotaan',
 'Menjadi bagian dari komunitas pembaca Desa Teras sangatlah mudah dan gratis.',
 '{"action_label":"Daftar Sekarang","action_href":"#"}',
 'aktif', 2);


-- ============================================================
-- SEED DATA — bdt_information (Download)
-- ============================================================

INSERT INTO `bdt_information` (`type`, `title`, `content`, `status`, `sort_order`) VALUES
('download', 'Formulir Pendaftaran', '#', 'aktif', 1),
('download', 'Katalog Buku 2024', '#', 'aktif', 2);
