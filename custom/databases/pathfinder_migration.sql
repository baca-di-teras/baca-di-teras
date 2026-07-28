-- ============================================================
-- Baca Di Teras -- Pathfinder Module Migration
-- ============================================================
-- File    : pathfinder_migration.sql
-- Project : Baca Di Teras
-- Version : 2.0.0
-- Database: bacaditeras (shared with SLiMS)
-- Urutan pembuatan tabel (dependency order):
--   1. pathfinder_categories
--   2. pathfinder_topics
--   3. pathfinder_topic_introductions
--   4. pathfinder_topic_mapping
--   5. pathfinder_related_books
--   6. pathfinder_guides
--   7. pathfinder_downloads
--   8. pathfinder_external_resources
-- Dibuat: Juli 2026
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- DROP (urut terbalik untuk menghindari FK error)
-- ============================================================
DROP TABLE IF EXISTS `pathfinder_external_resources`;
DROP TABLE IF EXISTS `pathfinder_downloads`;
DROP TABLE IF EXISTS `pathfinder_guides`;
DROP TABLE IF EXISTS `pathfinder_related_books`;
DROP TABLE IF EXISTS `pathfinder_topic_mapping`;
DROP TABLE IF EXISTS `pathfinder_topic_introductions`;
DROP TABLE IF EXISTS `pathfinder_topics`;
DROP TABLE IF EXISTS `pathfinder_categories`;

-- ============================================================
-- 1. pathfinder_categories
-- ============================================================
CREATE TABLE `pathfinder_categories` (
    `id`          INT(11)      NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(150) NOT NULL                 COMMENT 'Nama kategori',
    `slug`        VARCHAR(160) NOT NULL                 COMMENT 'URL-friendly',
    `description` TEXT         DEFAULT NULL,
    `icon`        VARCHAR(80)  DEFAULT NULL             COMMENT 'Nama ikon Phosphor Icons',
    `sort_order`  TINYINT(3)   NOT NULL DEFAULT 0,
    `status`      ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY  `uq_categories_slug` (`slug`),
    KEY         `idx_categories_status_sort` (`status`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Kategori utama modul Pathfinder';

-- ============================================================
-- 2. pathfinder_topics
-- ============================================================
CREATE TABLE `pathfinder_topics` (
    `id`           INT(11)      NOT NULL AUTO_INCREMENT,
    `category_id`  INT(11)      NOT NULL                 COMMENT 'FK -> pathfinder_categories.id',
    `name`         VARCHAR(150) NOT NULL,
    `slug`         VARCHAR(160) NOT NULL,
    `description`  TEXT         DEFAULT NULL,
    `icon`         VARCHAR(80)  DEFAULT NULL,
    `banner_image` VARCHAR(255) DEFAULT NULL,
    `sort_order`   TINYINT(3)   NOT NULL DEFAULT 0,
    `status`       ENUM('active','inactive','draft') NOT NULL DEFAULT 'active',
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY  `uq_topics_slug` (`slug`),
    KEY         `idx_topics_category_id` (`category_id`),
    KEY         `idx_topics_status_sort` (`status`, `sort_order`),
    CONSTRAINT `fk_topics_category`
        FOREIGN KEY (`category_id`) REFERENCES `pathfinder_categories` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Topik dalam setiap kategori Pathfinder';

-- ============================================================
-- 3. pathfinder_topic_introductions  (relasi 1:1 dengan topics)
-- ============================================================
CREATE TABLE `pathfinder_topic_introductions` (
    `id`                   INT(11)  NOT NULL AUTO_INCREMENT,
    `topic_id`             INT(11)  NOT NULL UNIQUE COMMENT 'FK -> pathfinder_topics.id (1:1)',
    `definition`           LONGTEXT DEFAULT NULL COMMENT 'Definisi / pengertian topik (HTML)',
    `learning_objectives`  LONGTEXT DEFAULT NULL COMMENT 'Tujuan pembelajaran (HTML)',
    `importance`           LONGTEXT DEFAULT NULL COMMENT 'Mengapa topik ini penting (HTML)',
    `topics_to_learn`      LONGTEXT DEFAULT NULL COMMENT 'Sub-topik yang dipelajari (HTML)',
    `created_at`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_intro_topic`
        FOREIGN KEY (`topic_id`) REFERENCES `pathfinder_topics` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Konten pendahuluan per topik Pathfinder (1:1)';

-- ============================================================
-- 4. pathfinder_topic_mapping  (bridge: topic <-> mst_topic SLiMS)
-- Tidak ada FK ke mst_topic karena mst_topic dikelola SLiMS
-- ============================================================
CREATE TABLE `pathfinder_topic_mapping` (
    `id`                   INT(11)  NOT NULL AUTO_INCREMENT,
    `pathfinder_topic_id`  INT(11)  NOT NULL COMMENT 'FK -> pathfinder_topics.id',
    `slims_topic_id`       INT(11)  NOT NULL COMMENT 'Ref -> mst_topic.topic_id',
    `created_at`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_mapping_topic_slims` (`pathfinder_topic_id`, `slims_topic_id`),
    KEY `idx_mapping_slims_topic_id` (`slims_topic_id`),
    CONSTRAINT `fk_mapping_pathfinder_topic`
        FOREIGN KEY (`pathfinder_topic_id`) REFERENCES `pathfinder_topics` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Pemetaan topik Pathfinder ke subyek SLiMS (mst_topic)';

-- ============================================================
-- 5. pathfinder_related_books  (kurasi relasi antar buku SLiMS)
-- Tidak ada FK ke biblio agar tidak terikat siklus SLiMS
-- ============================================================
CREATE TABLE `pathfinder_related_books` (
    `id`                INT(11)  NOT NULL AUTO_INCREMENT,
    `biblio_id`         INT(11)  NOT NULL COMMENT 'Ref -> biblio.biblio_id (buku utama)',
    `related_biblio_id` INT(11)  NOT NULL COMMENT 'Ref -> biblio.biblio_id (buku terkait)',
    `relation_type`     ENUM('same_topic','recommendation','new_collection')
                                 NOT NULL DEFAULT 'recommendation',
    `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_related_books` (`biblio_id`, `related_biblio_id`),
    KEY `idx_related_biblio_id` (`related_biblio_id`),
    KEY `idx_related_type`      (`relation_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Relasi antar buku SLiMS untuk fitur Related Collection';

-- ============================================================
-- 6. pathfinder_guides  (Tips & Panduan per topik, HTML)
-- ============================================================
CREATE TABLE `pathfinder_guides` (
    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
    `topic_id`   INT(11)      NOT NULL COMMENT 'FK -> pathfinder_topics.id',
    `title`      VARCHAR(255) NOT NULL,
    `slug`       VARCHAR(270) NOT NULL,
    `thumbnail`  VARCHAR(255) DEFAULT NULL,
    `content`    LONGTEXT     DEFAULT NULL COMMENT 'Isi panduan HTML',
    `sort_order` TINYINT(3)   NOT NULL DEFAULT 0,
    `status`     ENUM('published','draft','archived') NOT NULL DEFAULT 'draft',
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_guides_slug`          (`slug`),
    KEY        `idx_guides_topic_status` (`topic_id`, `status`),
    KEY        `idx_guides_sort`         (`topic_id`, `sort_order`),
    CONSTRAINT `fk_guides_topic`
        FOREIGN KEY (`topic_id`) REFERENCES `pathfinder_topics` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Tips & Panduan terkurasi per topik Pathfinder';

-- ============================================================
-- 7. pathfinder_downloads  (File download per topik)
-- ============================================================
CREATE TABLE `pathfinder_downloads` (
    `id`             INT(11)      NOT NULL AUTO_INCREMENT,
    `topic_id`       INT(11)      NOT NULL COMMENT 'FK -> pathfinder_topics.id',
    `title`          VARCHAR(255) NOT NULL,
    `description`    TEXT         DEFAULT NULL,
    `file_path`      VARCHAR(500) NOT NULL COMMENT 'Path file relatif dari ROOT_PATH',
    `file_type`      ENUM('pdf','doc','docx','xls','xlsx','ppt','pptx','jpg','jpeg','png','zip','other')
                                  NOT NULL DEFAULT 'pdf',
    `file_size`      INT(11)      DEFAULT 0 COMMENT 'Ukuran file dalam bytes',
    `download_count` INT(11)      NOT NULL DEFAULT 0,
    `status`         ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_downloads_topic_status` (`topic_id`, `status`),
    CONSTRAINT `fk_downloads_topic`
        FOREIGN KEY (`topic_id`) REFERENCES `pathfinder_topics` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='File download terkurasi per topik Pathfinder';

-- ============================================================
-- 8. pathfinder_external_resources  (Referensi eksternal)
-- ============================================================
CREATE TABLE `pathfinder_external_resources` (
    `id`            INT(11)      NOT NULL AUTO_INCREMENT,
    `topic_id`      INT(11)      NOT NULL COMMENT 'FK -> pathfinder_topics.id',
    `title`         VARCHAR(255) NOT NULL,
    `resource_type` ENUM('website','video','pdf','article') NOT NULL DEFAULT 'website',
    `url`           TEXT         NOT NULL,
    `description`   TEXT         DEFAULT NULL,
    `sort_order`    TINYINT(3)   NOT NULL DEFAULT 0,
    `status`        ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ext_resources_topic_type`   (`topic_id`, `resource_type`),
    KEY `idx_ext_resources_topic_status` (`topic_id`, `status`, `sort_order`),
    CONSTRAINT `fk_ext_resources_topic`
        FOREIGN KEY (`topic_id`) REFERENCES `pathfinder_topics` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Referensi eksternal terkurasi per topik Pathfinder';

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED DATA
-- ============================================================

-- 1. Kategori
INSERT INTO `pathfinder_categories` (`id`,`name`,`slug`,`description`,`icon`,`sort_order`,`status`) VALUES
(1,'Literasi Bahasa & Sastra','literasi-bahasa-sastra','Panduan membaca, menulis, dan mengapresiasi karya sastra dari berbagai sumber terpercaya.','book-open-text',1,'active'),
(2,'Literasi Lingkungan','literasi-lingkungan','Kumpulan referensi tentang kelestarian alam, ekologi, dan gaya hidup ramah lingkungan.','leaf',2,'active'),
(3,'Literasi Pertanian & Peternakan','literasi-pertanian-peternakan','Panduan praktis dan ilmiah di bidang pertanian modern, budidaya, dan peternakan.','plant',3,'active'),
(4,'Literasi Kesehatan & Keluarga','literasi-kesehatan-keluarga','Referensi kesehatan, gizi, pola hidup sehat, dan panduan parenting bagi keluarga.','heartbeat',4,'active'),
(5,'Literasi Pendidikan & Sains','literasi-pendidikan-sains','Sumber belajar kurikuler dan ilmiah untuk mendukung proses belajar-mengajar.','graduation-cap',5,'active'),
(6,'Literasi Ekonomi & Kewirausahaan','literasi-ekonomi-kewirausahaan','Panduan dasar ekonomi, manajemen usaha, dan kewirausahaan berbasis masyarakat desa.','currency-circle-dollar',6,'active'),
(7,'Literasi Pengembangan Diri','literasi-pengembangan-diri','Referensi untuk meningkatkan keterampilan, motivasi, kepemimpinan, dan produktivitas.','lightning',7,'active'),
(8,'Literasi Sejarah, Budaya & Keagamaan','literasi-sejarah-budaya-keagamaan','Panduan mengenal warisan sejarah lokal, budaya Nusantara, dan khazanah keagamaan.','mosque',8,'active');

-- 2. Topik
INSERT INTO `pathfinder_topics` (`id`,`category_id`,`name`,`slug`,`description`,`icon`,`sort_order`,`status`) VALUES
(1,2,'Pengelolaan Sampah','pengelolaan-sampah','Panduan reduce, reuse, recycle dan pengelolaan sampah rumah tangga.','trash',1,'active'),
(2,2,'Perubahan Iklim','perubahan-iklim','Memahami dampak perubahan iklim dan langkah mitigasi di tingkat lokal.','thermometer',2,'active'),
(3,2,'Energi Terbarukan','energi-terbarukan','Pengantar energi surya, angin, biogas, dan sumber energi alternatif.','sun',3,'active'),
(4,2,'Hidroponik','hidroponik','Teknik budidaya tanaman tanpa tanah yang cocok untuk lahan terbatas.','potted-plant',4,'active'),
(5,2,'Konservasi Alam','konservasi-alam','Upaya pelestarian keanekaragaman hayati dan ekosistem lokal.','tree-evergreen',5,'active'),
(6,3,'Pertanian Organik','pertanian-organik','Metode bertani ramah lingkungan tanpa pestisida dan pupuk kimia.','sprout',1,'active'),
(7,3,'Budidaya Ikan Lele','budidaya-ikan-lele','Panduan lengkap budidaya lele dari pembenihan hingga panen.','fish',2,'active'),
(8,4,'Gizi Seimbang','gizi-seimbang','Panduan pola makan sehat dan memenuhi kebutuhan gizi harian.','bowl-food',1,'active'),
(9,4,'Kesehatan Ibu & Anak','kesehatan-ibu-anak','Referensi kesehatan ibu hamil, menyusui, dan tumbuh kembang anak.','baby',2,'active'),
(10,5,'Matematika Dasar','matematika-dasar','Konsep aritmatika, aljabar, dan geometri untuk tingkat SD hingga SMP.','calculator',1,'active'),
(11,5,'Ilmu Pengetahuan Alam','ilmu-pengetahuan-alam','Fisika, kimia, dan biologi dasar yang disajikan secara sederhana dan aplikatif.','flask',2,'active'),
(12,6,'Usaha Mikro & UMKM','usaha-mikro-umkm','Panduan memulai, mengelola, dan mengembangkan usaha mikro di lingkungan desa.','storefront',1,'active'),
(13,7,'Manajemen Waktu','manajemen-waktu','Teknik dan strategi mengelola waktu agar lebih produktif dan fokus.','clock',1,'active'),
(14,1,'Membaca Kritis','membaca-kritis','Strategi membaca aktif, memahami konteks, dan mengevaluasi sumber bacaan.','magnifying-glass',1,'active'),
(15,1,'Penulisan Kreatif','penulisan-kreatif','Dasar-dasar menulis cerita, puisi, esai, dan konten kreatif lainnya.','pencil',2,'active'),
(16,8,'Sejarah Lokal Teras','sejarah-lokal-teras','Mengenal sejarah, tokoh, dan peristiwa penting di Kecamatan Teras, Boyolali.','map-trifold',1,'active');

-- 3. Pendahuluan Topik (sampel 3 topik)
INSERT INTO `pathfinder_topic_introductions` (`topic_id`,`definition`,`learning_objectives`,`importance`,`topics_to_learn`) VALUES
(4,
 '<p><strong>Hidroponik</strong> adalah metode bercocok tanam yang menggunakan larutan nutrisi berbasis air sebagai pengganti tanah. Tanaman ditanam dalam media inert seperti rockwool, cocopeat, atau pasir steril yang berfungsi menyangga akar.</p>',
 '<ul><li>Memahami prinsip dasar sistem hidroponik</li><li>Mengenal jenis-jenis sistem hidroponik (NFT, DFT, Wick, Aeroponik)</li><li>Mampu meracik larutan nutrisi dasar</li><li>Menerapkan budidaya hidroponik skala rumah tangga</li></ul>',
 '<p>Dengan lahan pertanian yang semakin terbatas, hidroponik menjadi solusi nyata untuk memproduksi pangan secara mandiri. Teknik ini menggunakan air <strong>90% lebih efisien</strong> dibanding pertanian tanah.</p>',
 '<ul><li>Sistem Nutrisi Film Technique (NFT)</li><li>Sistem Deep Flow Technique (DFT)</li><li>Sistem Wick (Sumbu)</li><li>Media Tanam Hidroponik</li><li>Larutan Nutrisi AB Mix</li><li>Hama & Penyakit Tanaman Hidroponik</li></ul>'
),
(1,
 '<p><strong>Pengelolaan sampah</strong> adalah serangkaian kegiatan yang meliputi pengumpulan, pengangkutan, pemrosesan, dan pembuangan atau daur ulang bahan limbah.</p>',
 '<ul><li>Memahami konsep 3R: Reduce, Reuse, Recycle</li><li>Membedakan jenis sampah organik dan anorganik</li><li>Mampu membuat kompos dari sampah organik rumah tangga</li><li>Menerapkan pemilahan sampah di tingkat rumah tangga</li></ul>',
 '<p>Setiap hari rata-rata satu orang menghasilkan <strong>0,5-0,7 kg sampah</strong>. Pengelolaan yang tepat dapat mengurangi volume sampah ke TPA hingga <strong>50%</strong>.</p>',
 '<ul><li>Pemilahan Sampah Organik & Anorganik</li><li>Komposting Rumah Tangga</li><li>Bank Sampah</li><li>Daur Ulang Plastik</li><li>Zero Waste Lifestyle</li></ul>'
),
(8,
 '<p><strong>Gizi seimbang</strong> adalah susunan makanan sehari-hari yang mengandung zat gizi dalam jenis dan jumlah yang sesuai dengan kebutuhan tubuh.</p>',
 '<ul><li>Memahami konsep Isi Piringku dari Kemenkes RI</li><li>Mengenal fungsi karbohidrat, protein, lemak, vitamin, dan mineral</li><li>Mampu menyusun menu harian bergizi dan terjangkau</li><li>Mengenali tanda-tanda kekurangan gizi pada anak</li></ul>',
 '<p>Gizi yang baik adalah fondasi kesehatan dan kecerdasan. Pemahaman gizi yang baik membantu mencegah stunting dan penyakit tidak menular di tingkat keluarga.</p>',
 '<ul><li>Makronutrien: Karbohidrat, Protein, Lemak</li><li>Mikronutrien: Vitamin & Mineral</li><li>Gizi Ibu Hamil & Menyusui</li><li>Gizi Anak Balita</li><li>Menu Sehat Sehari-Hari</li></ul>'
);

-- 4. Pemetaan Topik ke Subyek SLiMS (CONTOH - sesuaikan slims_topic_id dengan data nyata)
-- Cari topic_id yang tepat dengan: SELECT topic_id, topic FROM mst_topic WHERE topic LIKE '%hidroponik%';
INSERT INTO `pathfinder_topic_mapping` (`pathfinder_topic_id`,`slims_topic_id`) VALUES
(4, 58),
(1, 479),
(6, 219);

-- 5. Related Books (CONTOH - sesuaikan biblio_id dengan data nyata)
-- Cari biblio_id dengan: SELECT biblio_id, title FROM biblio WHERE title LIKE '%hidroponik%' LIMIT 10;
INSERT INTO `pathfinder_related_books` (`biblio_id`,`related_biblio_id`,`relation_type`) VALUES
(1, 2, 'same_topic'),
(1, 3, 'recommendation'),
(2, 1, 'same_topic');

-- 6. Tips & Panduan
INSERT INTO `pathfinder_guides` (`topic_id`,`title`,`slug`,`thumbnail`,`content`,`sort_order`,`status`) VALUES
(4,'Cara Memulai Hidroponik di Rumah dengan Modal Minim','cara-memulai-hidroponik-di-rumah',
 '/custom/assets/images/guides/hidroponik-dasar.jpg',
 '<h2>Persiapan Alat dan Bahan</h2><p>Untuk memulai hidroponik skala rumah tangga, Anda hanya membutuhkan:</p><ul><li>Ember atau wadah plastik bekas</li><li>Pipa paralon atau talang air</li><li>Netpot (dapat diganti gelas plastik bekas)</li><li>Nutrisi AB Mix</li><li>Media tanam: rockwool atau cocopeat</li><li>Benih sayuran: selada, kangkung, atau bayam</li></ul><h2>Langkah-Langkah</h2><ol><li>Siapkan wadah dan lubangi tutupnya sesuai ukuran netpot</li><li>Isi wadah dengan air bersih, tambahkan nutrisi AB Mix sesuai dosis</li><li>Semai benih di rockwool selama 3-5 hari</li><li>Pindahkan bibit ke netpot setelah muncul 2 daun sejati</li><li>Pantau kepekatan nutrisi (EC) dan pH air secara rutin</li></ol>',
 1,'published'),
(1,'Cara Membuat Kompos dari Sampah Dapur dalam 30 Hari','cara-membuat-kompos-sampah-dapur',
 '/custom/assets/images/guides/kompos-dapur.jpg',
 '<h2>Apa itu Kompos?</h2><p>Kompos adalah hasil penguraian bahan organik oleh mikroorganisme. Kompos buatan sendiri dari sampah dapur merupakan pupuk alami terbaik untuk tanaman.</p><h2>Bahan yang Dibutuhkan</h2><ul><li>Sampah dapur organik: sisa sayur, kulit buah, ampas teh/kopi</li><li>Tanah biasa (sebagai sumber mikroba)</li><li>Ember atau tong dengan lubang aerasi</li></ul><h2>Proses Pembuatan</h2><ol><li>Pisahkan sampah organik dari plastik dan bahan non-organik</li><li>Masukkan lapisan sampah organik (10 cm), tutup dengan lapisan tanah tipis</li><li>Aduk tumpukan setiap 3-4 hari untuk menjaga aerasi</li><li>Kompos matang dalam 3-4 minggu, ditandai warna coklat gelap dan bau tanah</li></ol>',
 1,'published');

-- 7. File Download
INSERT INTO `pathfinder_downloads` (`topic_id`,`title`,`description`,`file_path`,`file_type`,`file_size`,`status`) VALUES
(4,'Infografis Sistem Hidroponik NFT','Penjelasan visual cara kerja sistem NFT untuk hidroponik.','/custom/uploads/pathfinder/downloads/infografis-nft-hidroponik.pdf','pdf',512000,'active'),
(1,'Brosur 3R: Reduce, Reuse, Recycle','Brosur cetak untuk sosialisasi pengelolaan sampah di lingkungan RT/RW.','/custom/uploads/pathfinder/downloads/brosur-3r-pengelolaan-sampah.pdf','pdf',256000,'active'),
(8,'Poster Isi Piringku','Poster panduan gizi seimbang dari Kementerian Kesehatan RI.','/custom/uploads/pathfinder/downloads/poster-isi-piringku.jpg','jpg',1024000,'active');

-- 8. Referensi Eksternal
INSERT INTO `pathfinder_external_resources` (`topic_id`,`title`,`resource_type`,`url`,`description`,`sort_order`,`status`) VALUES
(4,'Panduan Hidroponik - Balitsa Kementan','website','https://balitsa.litbang.pertanian.go.id/ind/index.php/teknologi/hidroponik','Teknologi hidroponik dari Balai Penelitian Tanaman Sayuran Kementan RI.',1,'active'),
(4,'Belajar Hidroponik untuk Pemula','video','https://www.youtube.com/results?search_query=hidroponik+untuk+pemula','Tutorial hidroponik berbahasa Indonesia dari kanal pertanian terpercaya.',2,'active'),
(1,'KLHK - Pengelolaan Sampah Rumah Tangga','website','https://www.menlhk.go.id/site/single_post/3983','Informasi resmi dari Kementerian Lingkungan Hidup dan Kehutanan.',1,'active'),
(1,'Video: Bank Sampah dan Manfaatnya','video','https://www.youtube.com/results?search_query=bank+sampah+desa','Tutorial pembentukan dan pengelolaan bank sampah di tingkat RT/RW.',2,'active'),
(8,'Pedoman Gizi Seimbang - Kemenkes RI','pdf','https://gizi.kemkes.go.id/katalog/pedoman-gizi-seimbang.pdf','Dokumen resmi Pedoman Gizi Seimbang dari Direktorat Gizi Kemenkes RI.',1,'active'),
(8,'Pentingnya Sarapan Bergizi untuk Anak Sekolah','article','https://www.alodokter.com/pentingnya-sarapan-pagi-bagi-anak-sekolah','Artikel kesehatan tentang manfaat sarapan pagi bagi konsentrasi anak.',2,'active');

-- ============================================================
-- CONTOH QUERY
-- ============================================================

-- Q1: Semua kategori aktif
-- SELECT id, name, slug, description, icon FROM pathfinder_categories WHERE status='active' ORDER BY sort_order;

-- Q2: Topik aktif dalam satu kategori
-- SELECT t.id, t.name, t.slug, t.description, t.icon, t.banner_image
-- FROM pathfinder_topics t JOIN pathfinder_categories c ON c.id = t.category_id
-- WHERE c.slug = 'literasi-lingkungan' AND t.status = 'active' ORDER BY t.sort_order;

-- Q3: Pendahuluan topik
-- SELECT t.name, i.definition, i.learning_objectives, i.importance, i.topics_to_learn
-- FROM pathfinder_topics t JOIN pathfinder_topic_introductions i ON i.topic_id = t.id
-- WHERE t.slug = 'hidroponik';

-- Q4: Buku SLiMS dari pemetaan topik Pathfinder
-- SELECT DISTINCT b.biblio_id, b.title, b.call_number, b.image, b.publish_year, mt.topic
-- FROM pathfinder_topics pt
-- JOIN pathfinder_topic_mapping pm ON pm.pathfinder_topic_id = pt.id
-- JOIN mst_topic mt ON mt.topic_id = pm.slims_topic_id
-- JOIN biblio_topic bt ON bt.topic_id = mt.topic_id
-- JOIN biblio b ON b.biblio_id = bt.biblio_id
-- WHERE pt.slug = 'hidroponik' AND b.opac_hide = 0 ORDER BY b.title LIMIT 20;

-- Q5: Related books
-- SELECT rb.relation_type, b_m.title AS main_title, b_r.biblio_id, b_r.title AS related_title, b_r.call_number
-- FROM pathfinder_related_books rb
-- JOIN biblio b_m ON b_m.biblio_id = rb.biblio_id
-- JOIN biblio b_r ON b_r.biblio_id = rb.related_biblio_id
-- WHERE rb.biblio_id = 1 ORDER BY rb.relation_type, b_r.title;

-- Q6: Tips & Panduan per topik
-- SELECT id, title, slug, thumbnail, sort_order FROM pathfinder_guides
-- WHERE topic_id = (SELECT id FROM pathfinder_topics WHERE slug = 'hidroponik') AND status = 'published'
-- ORDER BY sort_order;

-- Q7: File Download per topik
-- SELECT id, title, description, file_path, file_type, file_size, download_count FROM pathfinder_downloads
-- WHERE topic_id = (SELECT id FROM pathfinder_topics WHERE slug = 'pengelolaan-sampah') AND status = 'active';

-- Q8: Referensi Eksternal per topik, per jenis
-- SELECT id, title, resource_type, url, description FROM pathfinder_external_resources
-- WHERE topic_id = (SELECT id FROM pathfinder_topics WHERE slug = 'gizi-seimbang') AND status = 'active'
-- ORDER BY resource_type, sort_order;

-- Q9: Ringkasan konten per topik (admin dashboard)
-- SELECT t.id, t.name, c.name AS category,
--   (SELECT COUNT(*) FROM pathfinder_topic_mapping WHERE pathfinder_topic_id = t.id) AS slims_topics,
--   (SELECT COUNT(*) FROM pathfinder_guides WHERE topic_id = t.id AND status='published') AS guides,
--   (SELECT COUNT(*) FROM pathfinder_downloads WHERE topic_id = t.id AND status='active') AS downloads,
--   (SELECT COUNT(*) FROM pathfinder_external_resources WHERE topic_id = t.id AND status='active') AS resources
-- FROM pathfinder_topics t JOIN pathfinder_categories c ON c.id = t.category_id
-- WHERE t.status = 'active' ORDER BY c.sort_order, t.sort_order;

COMMIT;
