<?php
/**
 * Front Controller — Entry Point
 *
 * File    : index.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Semua request HTTP masuk melalui file ini (kecuali /slims/).
 * Tugas file ini:
 *   1. Definisikan konstanta global
 *   2. Load Router
 *   3. Load daftar route (web.php)
 *   4. Dispatch request
 *
 * JANGAN menambahkan logika bisnis di sini.
 * Logika bisnis ada di custom/pages/*.php dan custom/services/*.php
 */

// ── Keamanan dasar ────────────────────────────────────────────
// Cegah akses langsung jika seseorang mengetik /index.php
// (opsional — .htaccess sudah menangani ini)

// ── 1. Konstanta global ───────────────────────────────────────

/** Absolute path ke root project di filesystem */
define('ROOT_PATH', __DIR__);

/**
 * Base URL — sesuaikan jika project dipindah ke subfolder lain
 * atau ke domain root (ubah ke '').
 *
 * Contoh sub-folder : '/baca-di-teras'
 * Contoh domain root: ''
 */
define('BASE_URL', '/baca-di-teras');

/** Versi aplikasi */
define('APP_VERSION', '1.0.0');

/** Environment: 'development' | 'production' */
define('APP_ENV', 'development');

// ── 1b. Load konfigurasi database ────────────────────────────
// Konstanta DB_* (DB_HOST, DB_NAME, dst.) dimuat di sini
// sehingga tersedia untuk semua Service Layer.
require_once ROOT_PATH . '/custom/config/database.php';

// ── 2. Error reporting sesuai environment ─────────────────────
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// ── 3. Load Router ───────────────────────────────────────────
require_once ROOT_PATH . '/custom/helpers/router.php';

// Inisialisasi router dengan base path yang sesuai .htaccess RewriteBase
$router = new Router(BASE_URL);

// ── 4. Load definisi route ────────────────────────────────────
require_once ROOT_PATH . '/custom/routes/web.php';

// ── 5. Dispatch request → jalankan route yang cocok ──────────
$router->dispatch();
