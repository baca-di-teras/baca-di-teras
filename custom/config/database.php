<?php
/**
 * Database Configuration
 *
 * File    : database.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Konfigurasi koneksi database MySQL.
 * File ini HANYA berisi konstanta konfigurasi.
 * Tidak boleh ada logika query di sini.
 *
 * PENTING: Jangan commit file ini ke Git jika berisi
 * kredensial production. Gunakan .env atau gitignore.
 */

// ── Kredensial Database ────────────────────────────────────────
// Sesuaikan dengan konfigurasi MySQL/MariaDB di server Anda.
// Database yang digunakan adalah database yang sama dengan SLiMS.

define('DB_HOST',    'localhost');
define('DB_PORT',    3306);
define('DB_NAME',    'bacaditeras');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// ── Konfigurasi aplikasi ───────────────────────────────────────

/** Tampilkan error database saat development, sembunyikan di production */
define('DB_DEBUG', true);
