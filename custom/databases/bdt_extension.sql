-- ============================================================
-- Baca Di Teras — Custom Extension Tables
-- ============================================================
--
-- File    : bdt_extension.sql
-- Project : Baca Di Teras
-- Version : 1.0.0
-- Database: bacaditeras  (database yang sama dengan SLiMS)
--
-- Tabel-tabel ini merupakan EKSTENSI dari database SLiMS.
-- Mereka menyimpan data tambahan yang tidak disediakan SLiMS
-- namun dibutuhkan oleh portal Baca Di Teras:
--
--   1. bdt_library    → Profil lengkap tiap perpustakaan desa
--   2. bdt_library_gallery → Foto-foto per perpustakaan
--   3. bdt_library_facility → Fasilitas yang tersedia
--   4. bdt_library_hour → Jam operasional per hari
--   5. bdt_article    → Artikel / berita yang diterbitkan
--   6. bdt_article_tag → Tag per artikel
--   7. bdt_article_view → Statistik pembacaan artikel
--
-- Relasi ke SLiMS:
--   bdt_library.slims_location_id → mst_location.location_id
--   bdt_article.created_by        → user.user_id
--
-- Dibuat: Juli 2026
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;

-- ============================================================
-- 1. bdt_library
--    Profil perpustakaan desa.
--    slims_location_id menghubungkan ke tabel mst_location
--    milik SLiMS sehingga item/biblio yang berada di lokasi
--    tersebut bisa diquery tanpa duplikasi data.
-- ============================================================

CREATE TABLE IF NOT EXISTS `bdt_library` (
    `library_id`        INT(11)         NOT NULL AUTO_INCREMENT,

    -- ── Relasi ke SLiMS ──────────────────────────────────────
    -- Harus sesuai dengan mst_location.location_id (varchar 3)
    -- Contoh: 'SL', 'TB', 'PD'
    `slims_location_id` VARCHAR(3)      NOT NULL DEFAULT '' COMMENT 'FK → mst_location.location_id',

    -- ── Identitas ─────────────────────────────────────────────
    `slug`              VARCHAR(100)    NOT NULL UNIQUE   COMMENT 'URL-friendly identifier, contoh: perpustakaan-utama',
    `name`              VARCHAR(150)    NOT NULL          COMMENT 'Nama resmi perpustakaan',
    `tagline`           VARCHAR(255)    DEFAULT NULL      COMMENT 'Slogan atau sub-judul singkat',
    `badge`             VARCHAR(50)     DEFAULT NULL      COMMENT 'Label badge, contoh: Unggulan | Digital | Khusus Anak',
    `status`            ENUM('aktif','nonaktif','sementara-tutup') NOT NULL DEFAULT 'aktif',

    -- ── Kontak & Lokasi ───────────────────────────────────────
    `address`           VARCHAR(255)    DEFAULT NULL,
    `village`           VARCHAR(100)    DEFAULT NULL      COMMENT 'Nama dusun/RT',
    `phone`             VARCHAR(20)     DEFAULT NULL,
    `email`             VARCHAR(100)    DEFAULT NULL,
    `whatsapp`          VARCHAR(20)     DEFAULT NULL,
    `google_maps_url`   TEXT            DEFAULT NULL      COMMENT 'URL Google Maps embed atau link',
    `latitude`          DECIMAL(10,8)   DEFAULT NULL,
    `longitude`         DECIMAL(11,8)   DEFAULT NULL,

    -- ── Media ─────────────────────────────────────────────────
    `cover_image`       VARCHAR(255)    DEFAULT NULL      COMMENT 'Path gambar utama perpustakaan',
    `thumbnail_image`   VARCHAR(255)    DEFAULT NULL      COMMENT 'Path gambar thumbnail untuk card',

    -- ── Statistik Koleksi (denormalized, disync dari biblio) ──
    `total_koleksi`     INT(11)         NOT NULL DEFAULT 0 COMMENT 'Total koleksi; disinkronkan via cron/trigger',
    `total_anggota`     INT(11)         NOT NULL DEFAULT 0 COMMENT 'Total anggota aktif',

    -- ── Deskripsi ─────────────────────────────────────────────
    `description`       TEXT            DEFAULT NULL      COMMENT 'Deskripsi lengkap HTML diperbolehkan',
    `sejarah`           TEXT            DEFAULT NULL      COMMENT 'Sejarah singkat perpustakaan',
    `visi`              TEXT            DEFAULT NULL,
    `misi`              TEXT            DEFAULT NULL,

    -- ── Urutan tampil di portal ────────────────────────────────
    `sort_order`        INT(3)          NOT NULL DEFAULT 0 COMMENT 'Urutan tampil di halaman perpustakaan',

    -- ── Timestamp ─────────────────────────────────────────────
    `created_at`        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`library_id`),
    INDEX `idx_slug`              (`slug`),
    INDEX `idx_slims_location`    (`slims_location_id`),
    INDEX `idx_status`            (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Profil lengkap perpustakaan desa, terintegrasi dengan mst_location SLiMS';


-- ============================================================
-- 2. bdt_library_gallery
--    Foto-foto per perpustakaan (galeri).
-- ============================================================

CREATE TABLE IF NOT EXISTS `bdt_library_gallery` (
    `gallery_id`    INT(11)         NOT NULL AUTO_INCREMENT,
    `library_id`    INT(11)         NOT NULL COMMENT 'FK → bdt_library.library_id',
    `image_path`    VARCHAR(255)    NOT NULL COMMENT 'Path file gambar',
    `caption`       VARCHAR(255)    DEFAULT NULL,
    `alt_text`      VARCHAR(255)    DEFAULT NULL COMMENT 'Alt text untuk aksesibilitas',
    `sort_order`    INT(3)          NOT NULL DEFAULT 0,
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`gallery_id`),
    INDEX `idx_library_id` (`library_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Galeri foto per perpustakaan';


-- ============================================================
-- 3. bdt_library_facility
--    Fasilitas yang tersedia di perpustakaan.
-- ============================================================

CREATE TABLE IF NOT EXISTS `bdt_library_facility` (
    `facility_id`   INT(11)         NOT NULL AUTO_INCREMENT,
    `library_id`    INT(11)         NOT NULL COMMENT 'FK → bdt_library.library_id',
    `icon`          VARCHAR(50)     DEFAULT NULL COMMENT 'Nama icon SVG atau kelas ikon',
    `name`          VARCHAR(100)    NOT NULL    COMMENT 'Nama fasilitas, contoh: WiFi Gratis',
    `description`   VARCHAR(255)    DEFAULT NULL,
    `sort_order`    INT(3)          NOT NULL DEFAULT 0,

    PRIMARY KEY (`facility_id`),
    INDEX `idx_library_id` (`library_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Daftar fasilitas per perpustakaan';


-- ============================================================
-- 4. bdt_library_hour
--    Jam operasional per hari per perpustakaan.
-- ============================================================

CREATE TABLE IF NOT EXISTS `bdt_library_hour` (
    `hour_id`       INT(11)     NOT NULL AUTO_INCREMENT,
    `library_id`    INT(11)     NOT NULL COMMENT 'FK → bdt_library.library_id',
    -- 0=Minggu, 1=Senin, ..., 6=Sabtu
    `day_of_week`   TINYINT(1)  NOT NULL COMMENT '0=Minggu, 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat, 6=Sabtu',
    `is_open`       TINYINT(1)  NOT NULL DEFAULT 1 COMMENT '1=buka, 0=tutup',
    `open_time`     TIME        DEFAULT NULL COMMENT 'Jam buka, NULL jika tutup',
    `close_time`    TIME        DEFAULT NULL COMMENT 'Jam tutup, NULL jika tutup',
    `note`          VARCHAR(100) DEFAULT NULL COMMENT 'Catatan, contoh: Khusus anggota',

    PRIMARY KEY (`hour_id`),
    UNIQUE KEY `uk_library_day` (`library_id`, `day_of_week`),
    INDEX `idx_library_id` (`library_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Jam operasional per hari per perpustakaan';


-- ============================================================
-- 5. bdt_article
--    Artikel, berita, dan pengumuman yang dipublish di portal.
--
--    Terintegrasi dengan SLiMS:
--      - created_by → user.user_id (pustakawan SLiMS)
--      - biblio_id  → biblio.biblio_id (jika artikel terkait buku)
--      - library_id → bdt_library.library_id (jika artikel milik perpus tertentu)
-- ============================================================

CREATE TABLE IF NOT EXISTS `bdt_article` (
    `article_id`    INT(11)         NOT NULL AUTO_INCREMENT,

    -- ── Konten ────────────────────────────────────────────────
    `title`         VARCHAR(300)    NOT NULL,
    `slug`          VARCHAR(300)    NOT NULL UNIQUE      COMMENT 'URL-friendly, contoh: festival-baca-desa-teras-2026',
    `excerpt`       TEXT            DEFAULT NULL         COMMENT 'Ringkasan singkat (max 300 karakter)',
    `body`          LONGTEXT        NOT NULL             COMMENT 'Isi artikel penuh, mendukung HTML',
    `cover_image`   VARCHAR(255)    DEFAULT NULL         COMMENT 'Path gambar utama artikel',

    -- ── Kategori & Tipe ───────────────────────────────────────
    `category`      ENUM(
                        'berita',
                        'kegiatan',
                        'pengumuman',
                        'resensi',
                        'literasi',
                        'lainnya'
                    ) NOT NULL DEFAULT 'berita'         COMMENT 'Kategori artikel',

    -- ── Status publikasi ──────────────────────────────────────
    `status`        ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    `publish_date`  DATETIME        DEFAULT NULL        COMMENT 'Tanggal & jam dipublish (bisa dijadwalkan)',
    `is_featured`   TINYINT(1)      NOT NULL DEFAULT 0  COMMENT '1=tampil di homepage',
    `is_pinned`     TINYINT(1)      NOT NULL DEFAULT 0  COMMENT '1=ditempel di atas daftar',

    -- ── Relasi ke SLiMS user ──────────────────────────────────
    -- Mengacu ke tabel `user` SLiMS (user_id INT)
    `created_by`    INT(11)         DEFAULT NULL        COMMENT 'FK → user.user_id (SLiMS librarian)',
    `updated_by`    INT(11)         DEFAULT NULL        COMMENT 'FK → user.user_id (SLiMS librarian)',

    -- ── Relasi opsional ke buku SLiMS ─────────────────────────
    -- Diisi jika artikel adalah resensi atau terkait koleksi tertentu
    `biblio_id`     INT(11)         DEFAULT NULL        COMMENT 'FK opsional → biblio.biblio_id (SLiMS)',

    -- ── Relasi opsional ke perpustakaan ───────────────────────
    -- Diisi jika artikel adalah kegiatan spesifik perpustakaan
    `library_id`    INT(11)         DEFAULT NULL        COMMENT 'FK opsional → bdt_library.library_id',

    -- ── SEO ───────────────────────────────────────────────────
    `meta_description` VARCHAR(300) DEFAULT NULL,
    `meta_keywords`    VARCHAR(255) DEFAULT NULL,

    -- ── Statistik ─────────────────────────────────────────────
    `view_count`    INT(11)         NOT NULL DEFAULT 0,

    -- ── Timestamp ─────────────────────────────────────────────
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`article_id`),
    UNIQUE KEY `uk_slug`            (`slug`),
    INDEX `idx_status`              (`status`),
    INDEX `idx_category`            (`category`),
    INDEX `idx_publish_date`        (`publish_date`),
    INDEX `idx_is_featured`         (`is_featured`),
    INDEX `idx_library_id`          (`library_id`),
    INDEX `idx_biblio_id`           (`biblio_id`),
    INDEX `idx_created_by`          (`created_by`),
    FULLTEXT KEY `ft_title_body`    (`title`, `body`, `excerpt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Artikel, berita, dan kegiatan portal Baca Di Teras';


-- ============================================================
-- 6. bdt_article_tag
--    Tag yang melekat pada artikel. Tabel many-to-many
--    menggunakan approach tag name langsung (bukan normalized)
--    agar mudah digunakan tanpa JOIN tambahan.
-- ============================================================

CREATE TABLE IF NOT EXISTS `bdt_article_tag` (
    `tag_id`        INT(11)         NOT NULL AUTO_INCREMENT,
    `article_id`    INT(11)         NOT NULL COMMENT 'FK → bdt_article.article_id',
    `tag_name`      VARCHAR(50)     NOT NULL,

    PRIMARY KEY (`tag_id`),
    UNIQUE KEY `uk_article_tag`  (`article_id`, `tag_name`),
    INDEX `idx_tag_name`         (`tag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Tag per artikel';


-- ============================================================
-- 7. bdt_article_view
--    Statistik per-hari pembacaan artikel.
--    Digunakan untuk menghitung "artikel terpopuler".
-- ============================================================

CREATE TABLE IF NOT EXISTS `bdt_article_view` (
    `view_id`       INT(11)         NOT NULL AUTO_INCREMENT,
    `article_id`    INT(11)         NOT NULL COMMENT 'FK → bdt_article.article_id',
    `view_date`     DATE            NOT NULL DEFAULT (CURDATE()),
    `view_count`    INT(11)         NOT NULL DEFAULT 1,

    PRIMARY KEY (`view_id`),
    UNIQUE KEY `uk_article_date` (`article_id`, `view_date`),
    INDEX `idx_view_date`        (`view_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Statistik harian pembacaan artikel';


-- ============================================================
-- DATA AWAL (SEED)
-- ============================================================

-- Tambahkan lokasi perpustakaan desa ke mst_location SLiMS
-- (Jika location_id sudah ada, gunakan INSERT IGNORE)
INSERT IGNORE INTO `mst_location` (`location_id`, `location_name`, `input_date`, `last_update`) VALUES
('PU', 'Perpustakaan Utama Desa',    CURDATE(), CURDATE()),
('TB', 'Taman Baca Komunitas',        CURDATE(), CURDATE()),
('PD', 'Perpustakaan Digital Teras',  CURDATE(), CURDATE());

-- Data awal perpustakaan desa
INSERT INTO `bdt_library`
    (`slims_location_id`, `slug`, `name`, `tagline`, `badge`, `status`,
     `address`, `village`, `phone`, `email`, `whatsapp`, `google_maps_url`,
     `cover_image`, `thumbnail_image`,
     `total_koleksi`, `total_anggota`,
     `description`, `sejarah`, `visi`, `misi`, `sort_order`)
VALUES
(
    'PU',
    'perpustakaan-utama',
    'Perpustakaan Utama Desa',
    'Pusat Literasi & Pengetahuan Desa Teras',
    'Unggulan',
    'aktif',
    'Jl. Raya Teras No. 1, Desa Teras, Boyolali',
    'Pusat Desa',
    '(0276) 321000',
    'perpus.utama@teras.desa.id',
    '6281200001111',
    'https://maps.google.com/?q=Teras,Boyolali',
    '/custom/assets/images/library-1.png',
    '/custom/assets/images/library-1.png',
    3200, 580,
    'Perpustakaan Utama Desa Teras adalah pusat literasi terbesar di Desa Teras yang menyediakan koleksi buku, media digital, dan berbagai program kegiatan literasi untuk seluruh lapisan masyarakat.',
    'Perpustakaan ini didirikan pada tahun 2010 atas inisiatif pemerintah desa dan tokoh masyarakat yang peduli terhadap peningkatan literasi warga desa.',
    'Menjadi pusat literasi terdepan yang mendorong masyarakat cerdas, berbudaya, dan berdaya saing.',
    'Menyediakan layanan perpustakaan yang berkualitas, aksesibel, dan relevan bagi seluruh warga Desa Teras.',
    1
),
(
    'TB',
    'taman-baca-komunitas',
    'Taman Baca Komunitas',
    'Ruang Baca yang Nyaman di Tengah Komunitas',
    'Unggulan',
    'aktif',
    'Jl. Pendidikan No. 5, RT 02 RW 03, Teras',
    'RT 02 RW 03',
    '(0276) 321001',
    'taman.baca@teras.desa.id',
    '6281200002222',
    'https://maps.google.com/?q=Teras,Boyolali',
    '/custom/assets/images/library-2.png',
    '/custom/assets/images/library-2.png',
    1800, 420,
    'Taman Baca Komunitas hadir sebagai ruang literasi yang ramah dan terbuka untuk semua usia, dengan suasana yang nyaman dan koleksi yang beragam.',
    'Dibangun dari swadaya warga pada tahun 2015, taman baca ini menjadi bukti semangat gotong royong masyarakat Teras dalam memajukan pendidikan.',
    'Menjadi ruang komunitas literasi yang inklusif dan berkelanjutan.',
    'Menyediakan ruang baca yang nyaman, koleksi yang relevan, dan kegiatan literasi yang melibatkan seluruh komunitas.',
    2
),
(
    'PD',
    'perpustakaan-digital',
    'Perpustakaan Digital Teras',
    'Koleksi Digital untuk Era Modern',
    'Digital',
    'aktif',
    'Jl. Merdeka No. 12, Pusat Desa Teras',
    'Pusat Desa',
    '(0276) 321002',
    'perpus.digital@teras.desa.id',
    '6281200003333',
    'https://maps.google.com/?q=Teras,Boyolali',
    '/custom/assets/images/library-3.png',
    '/custom/assets/images/library-3.png',
    2500, 310,
    'Perpustakaan Digital Teras menghadirkan pengalaman membaca modern dengan koleksi e-book, audio book, dan akses internet gratis untuk mendukung literasi digital masyarakat desa.',
    'Diluncurkan pada tahun 2020 sebagai respons terhadap kebutuhan literasi digital di era pandemi, perpustakaan ini terus berkembang menjadi pusat teknologi literasi desa.',
    'Menjadi jembatan antara masyarakat desa dan dunia digital melalui literasi yang inklusif.',
    'Menyediakan akses layanan digital yang mudah, terjangkau, dan mendidik bagi seluruh warga.',
    3
);

-- Jam operasional Perpustakaan Utama (library_id=1)
-- Senin–Jumat: 08:00–16:00, Sabtu: 08:00–13:00, Minggu: tutup
INSERT INTO `bdt_library_hour` (`library_id`, `day_of_week`, `is_open`, `open_time`, `close_time`) VALUES
(1, 0, 0, NULL, NULL),           -- Minggu
(1, 1, 1, '08:00:00', '16:00:00'), -- Senin
(1, 2, 1, '08:00:00', '16:00:00'), -- Selasa
(1, 3, 1, '08:00:00', '16:00:00'), -- Rabu
(1, 4, 1, '08:00:00', '16:00:00'), -- Kamis
(1, 5, 1, '08:00:00', '16:00:00'), -- Jumat
(1, 6, 1, '08:00:00', '13:00:00'); -- Sabtu

-- Jam operasional Taman Baca Komunitas (library_id=2)
INSERT INTO `bdt_library_hour` (`library_id`, `day_of_week`, `is_open`, `open_time`, `close_time`) VALUES
(2, 0, 0, NULL, NULL),
(2, 1, 1, '09:00:00', '17:00:00'),
(2, 2, 1, '09:00:00', '17:00:00'),
(2, 3, 1, '09:00:00', '17:00:00'),
(2, 4, 1, '09:00:00', '17:00:00'),
(2, 5, 1, '09:00:00', '17:00:00'),
(2, 6, 1, '09:00:00', '14:00:00');

-- Jam operasional Perpustakaan Digital (library_id=3)
INSERT INTO `bdt_library_hour` (`library_id`, `day_of_week`, `is_open`, `open_time`, `close_time`) VALUES
(3, 0, 0, NULL, NULL),
(3, 1, 1, '08:00:00', '20:00:00'),
(3, 2, 1, '08:00:00', '20:00:00'),
(3, 3, 1, '08:00:00', '20:00:00'),
(3, 4, 1, '08:00:00', '20:00:00'),
(3, 5, 1, '08:00:00', '20:00:00'),
(3, 6, 1, '09:00:00', '16:00:00');

-- Fasilitas Perpustakaan Utama
INSERT INTO `bdt_library_facility` (`library_id`, `icon`, `name`, `sort_order`) VALUES
(1, 'wifi',      'WiFi Gratis',           1),
(1, 'ac',        'Ruangan Ber-AC',         2),
(1, 'computer',  'Komputer Umum',          3),
(1, 'printer',   'Layanan Cetak/Fotokopi', 4),
(1, 'child',     'Sudut Anak',             5),
(1, 'parking',   'Area Parkir',            6);

-- Fasilitas Taman Baca Komunitas
INSERT INTO `bdt_library_facility` (`library_id`, `icon`, `name`, `sort_order`) VALUES
(2, 'wifi',      'WiFi Gratis',            1),
(2, 'outdoor',   'Area Baca Outdoor',      2),
(2, 'child',     'Pojok Anak',             3),
(2, 'event',     'Ruang Kegiatan',         4);

-- Fasilitas Perpustakaan Digital
INSERT INTO `bdt_library_facility` (`library_id`, `icon`, `name`, `sort_order`) VALUES
(3, 'wifi',      'Internet Cepat',         1),
(3, 'computer',  'Komputer & Tablet',      2),
(3, 'ebook',     'Koleksi e-Book',         3),
(3, 'scan',      'Scanner Dokumen',        4),
(3, 'audio',     'Audio Book',             5);

-- Artikel seed awal
INSERT INTO `bdt_article`
    (`title`, `slug`, `excerpt`, `body`, `cover_image`, `category`, `status`, `publish_date`, `is_featured`, `created_by`, `library_id`)
VALUES
(
    'Festival Baca Desa Teras 2026: Merayakan Literasi Bersama',
    'festival-baca-desa-teras-2026',
    'Festival Baca Desa Teras kembali hadir tahun ini dengan berbagai kegiatan menarik yang melibatkan seluruh warga desa.',
    '<p>Festival Baca Desa Teras 2026 akan diselenggarakan pada tanggal 25–27 Juli 2026 di Balai Desa Teras. Festival ini menampilkan berbagai kegiatan seperti pameran buku, lomba membaca, diskusi literasi, dan pertunjukan seni berbasis sastra.</p><p>Acara ini terbuka untuk seluruh masyarakat dan tidak dipungut biaya masuk. Mari bergabung dan rayakan semangat literasi bersama!</p>',
    '/custom/assets/images/event-workshop.png',
    'kegiatan',
    'published',
    '2026-07-15 08:00:00',
    1,
    NULL,
    1
),
(
    'Koleksi Baru: 50 Judul Buku Pertanian Modern Hadir di Perpustakaan Utama',
    'koleksi-baru-buku-pertanian-modern',
    'Perpustakaan Utama Desa menerima donasi 50 judul buku pertanian modern dari Dinas Pertanian Boyolali.',
    '<p>Kabar baik bagi para petani dan pecinta pertanian di Desa Teras! Perpustakaan Utama Desa kini memiliki koleksi baru berupa 50 judul buku tentang pertanian modern, teknik bercocok tanam ramah lingkungan, dan agribisnis.</p><p>Buku-buku ini merupakan donasi dari Dinas Pertanian Kabupaten Boyolali dan sudah tersedia untuk dipinjam mulai hari ini.</p>',
    '/custom/assets/images/news-featured.png',
    'berita',
    'published',
    '2026-07-10 09:00:00',
    0,
    NULL,
    1
),
(
    'Workshop Literasi Digital untuk Ibu-Ibu PKK Desa Teras',
    'workshop-literasi-digital-pkk',
    'Taman Baca Komunitas mengadakan workshop literasi digital gratis untuk ibu-ibu anggota PKK di Desa Teras.',
    '<p>Taman Baca Komunitas bekerja sama dengan Pemerintah Desa Teras menyelenggarakan Workshop Literasi Digital yang ditujukan khusus untuk ibu-ibu anggota PKK.</p><p>Workshop ini membahas cara memanfaatkan smartphone untuk kegiatan produktif, menghindari hoaks, dan mengakses layanan perpustakaan digital secara gratis.</p><p>Pendaftaran dapat dilakukan langsung di Taman Baca Komunitas atau melalui WhatsApp di nomor yang tertera.</p>',
    '/custom/assets/images/event-book-club.png',
    'kegiatan',
    'published',
    '2026-07-12 10:00:00',
    1,
    NULL,
    2
),
(
    'Pengumuman: Perpustakaan Digital Teras Kini Buka hingga Pukul 20.00',
    'pengumuman-perpustakaan-digital-buka-malam',
    'Mulai 1 Agustus 2026, Perpustakaan Digital Teras memperpanjang jam layanan hingga pukul 20.00 WIB.',
    '<p>Demi memberikan layanan yang lebih optimal kepada masyarakat, Perpustakaan Digital Teras mengumumkan perpanjangan jam operasional mulai 1 Agustus 2026.</p><p>Perpustakaan kini melayani pengunjung dari pukul <strong>08.00 hingga 20.00 WIB</strong> pada hari Senin–Jumat, sehingga warga yang bekerja di siang hari tetap dapat mengakses layanan perpustakaan di malam hari.</p>',
    '/custom/assets/images/news-small.png',
    'pengumuman',
    'published',
    '2026-07-13 08:00:00',
    0,
    NULL,
    3
);

-- Tag untuk artikel
INSERT INTO `bdt_article_tag` (`article_id`, `tag_name`) VALUES
(1, 'festival'), (1, 'literasi'), (1, 'kegiatan-desa'),
(2, 'koleksi-baru'), (2, 'pertanian'), (2, 'donasi'),
(3, 'workshop'), (3, 'digital'), (3, 'PKK'), (3, 'literasi-digital'),
(4, 'pengumuman'), (4, 'jam-layanan'), (4, 'perpustakaan-digital');


-- ============================================================
-- VIEWS — Query helper untuk aplikasi
-- ============================================================

-- View: artikel published yang aktif (dipakai di landing page, dll.)
CREATE OR REPLACE VIEW `bdt_article_published` AS
SELECT
    a.*,
    l.name      AS library_name,
    l.slug      AS library_slug
FROM `bdt_article` a
LEFT JOIN `bdt_library` l ON a.library_id = l.library_id
WHERE a.status = 'published'
  AND (a.publish_date IS NULL OR a.publish_date <= NOW())
ORDER BY a.is_pinned DESC, a.publish_date DESC;

-- View: statistik koleksi per perpustakaan
-- Join ke tabel SLiMS: item (eksemplar) dan mst_location
CREATE OR REPLACE VIEW `bdt_library_stats` AS
SELECT
    lib.library_id,
    lib.slug,
    lib.name,
    lib.slims_location_id,
    lib.total_koleksi,
    lib.total_anggota,
    COUNT(DISTINCT i.item_id) AS total_eksemplar_aktif
FROM `bdt_library` lib
LEFT JOIN `item` i
    ON i.location_id = lib.slims_location_id
   AND i.item_status_id NOT IN ('WD', 'MIS')
GROUP BY lib.library_id;

COMMIT;
