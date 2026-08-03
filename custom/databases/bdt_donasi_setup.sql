-- ============================================================
-- Baca Di Teras — Tabel Donasi
-- ============================================================
--
-- File    : bdt_donasi_setup.sql
-- Project : Baca Di Teras
-- Version : 1.0.0
-- Database: bacaditeras
--
-- Tabel-tabel untuk fitur Donasi:
--   1. bdt_donatur       → Daftar donatur
--   2. bdt_donasi_partner → Daftar logo/partner "Terima Kasih Kepada"
--
-- Dibuat: Juli 2026
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;

-- ============================================================
-- 1. bdt_donatur
--    Menyimpan data donatur yang tampil di halaman donasi.
-- ============================================================

CREATE TABLE IF NOT EXISTS `bdt_donatur` (
    `donatur_id`    INT(11)         NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(150)    NOT NULL                COMMENT 'Nama donatur',
    `amount`        DECIMAL(15,2)   DEFAULT NULL            COMMENT 'Jumlah donasi (opsional, NULL = tidak ditampilkan)',
    `donated_at`    DATE            NOT NULL DEFAULT (CURRENT_DATE) COMMENT 'Tanggal donasi',
    `note`          TEXT            DEFAULT NULL            COMMENT 'Catatan opsional',
    `is_visible`    TINYINT(1)      NOT NULL DEFAULT 1      COMMENT '1=tampil, 0=disembunyikan',
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`donatur_id`),
    KEY `idx_is_visible` (`is_visible`),
    KEY `idx_donated_at` (`donated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Daftar donatur halaman donasi';

-- ============================================================
-- 2. bdt_donasi_partner
--    Logo-logo organisasi/sponsor untuk bagian "Terima Kasih Kepada"
-- ============================================================

CREATE TABLE IF NOT EXISTS `bdt_donasi_partner` (
    `partner_id`    INT(11)         NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(150)    NOT NULL                COMMENT 'Nama organisasi/sponsor',
    `logo_path`     VARCHAR(255)    DEFAULT NULL            COMMENT 'Path ke file logo (relatif dari root)',
    `website_url`   VARCHAR(255)    DEFAULT NULL            COMMENT 'URL website partner (opsional)',
    `sort_order`    INT(11)         NOT NULL DEFAULT 0      COMMENT 'Urutan tampil (ascending)',
    `is_visible`    TINYINT(1)      NOT NULL DEFAULT 1      COMMENT '1=tampil, 0=disembunyikan',
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`partner_id`),
    KEY `idx_sort_order` (`sort_order`),
    KEY `idx_is_visible` (`is_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Logo partner/sponsor untuk halaman donasi';

-- ============================================================
-- Sample Data (opsional, bisa dihapus)
-- ============================================================

INSERT INTO `bdt_donatur` (`name`, `donated_at`, `is_visible`) VALUES
    ('Renanda Iskandar', '2025-06-01', 1),
    ('Puja Rahin', '2025-06-03', 1),
    ('Prashandu', '2025-06-05', 1),
    ('Iman Hilman', '2025-06-08', 1),
    ('Wibowo', '2025-06-10', 1),
    ('Ade Ashnita Koestono', '2025-06-12', 1),
    ('Bernard Jaewono', '2025-06-15', 1),
    ('Ifandy Aditya', '2025-06-18', 1),
    ('Andrini Eka Diah', '2025-06-20', 1),
    ('Andi Wahyu', '2025-06-22', 1);
