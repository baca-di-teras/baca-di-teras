<?php
/**
 * Web Routes
 *
 * File    : web.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Daftar seluruh route portal Baca Di Teras.
 * Tambahkan route baru di sini — tanpa mengubah file lain.
 *
 * Format:
 *   $router->get('/path', 'custom/pages/file.php');
 *   $router->get('/path/{param}', 'custom/pages/file.php');  // route dinamis
 *   $router->redirect('/path', 'https://tujuan.com');        // redirect
 *
 * Di dalam file view, parameter URL dinamis tersedia via:
 *   $routeParams['slug']   → dari route /perpustakaan/{slug}
 *   $routeParams['id']     → dari route /katalog/{id}
 */

// ── Pastikan $router sudah terdefinisi dari index.php ─────────
if (!isset($router) || !($router instanceof Router)) {
    http_response_code(500);
    die('Router tidak tersedia.');
}

// ============================================================
// HALAMAN UTAMA
// ============================================================

$router->get('/', 'custom/pages/landing.php');

// ============================================================
// PROFIL DESA
// ============================================================

$router->get('/profil', 'custom/pages/profile/index.php');

// ============================================================
// PERPUSTAKAAN
// ============================================================

// Daftar semua perpustakaan
$router->get('/perpustakaan', 'custom/pages/libraries/directory.php');

// Detail perpustakaan: /perpustakaan/perpustakaan-utama
$router->get('/perpustakaan/{slug}', 'custom/pages/libraries/library-detail.php');

// ============================================================
// KATALOG BUKU
// ============================================================

// Halaman katalog utama
$router->get('/katalog', 'custom/pages/libraries/catalog.php');

// Detail buku: /katalog/the-architecture-of-growth
$router->get('/katalog/{slug}', 'custom/pages/libraries/book-detail.php');

// ============================================================
// BERITA & ARTIKEL
// ============================================================

// Daftar berita
$router->get('/berita', 'custom/pages/news/index.php');

// Detail berita: /berita/festival-baca-2026
$router->get('/berita/{slug}', 'custom/pages/news/detail.php');

// Daftar artikel
$router->get('/artikel', 'custom/pages/article/index.php');

// Detail artikel: /artikel/tips-membaca-efektif
$router->get('/artikel/{slug}', 'custom/pages/article/detail.php');

// ============================================================
// INFORMASI & KONTAK
// ============================================================

$router->get('/informasi', 'custom/pages/informasi.php');
$router->get('/kontak',    'custom/pages/contact.php');

// ============================================================
// PATHFINDER
// ============================================================

// Admin portal login & logout
$router->get('/portal-admin/login', 'custom/pages/admin/login.php');
$router->get('/portal-admin/logout', 'custom/pages/admin/logout.php');

// Global CMS Admin Dashboard
$router->get('/portal-admin', 'custom/pages/admin/dashboard.php');

// CMS Modules (Skeletons)
$router->get('/portal-admin/artikel', 'custom/pages/admin/manage_article.php');
$router->get('/portal-admin/artikel/create', 'custom/pages/admin/create_article.php');
$router->get('/portal-admin/artikel/edit', 'custom/pages/admin/edit_article.php');
$router->get('/portal-admin/perpustakaan', 'custom/pages/admin/manage_library.php');
$router->get('/portal-admin/perpustakaan/create', 'custom/pages/admin/create_library.php');
$router->get('/portal-admin/perpustakaan/edit', 'custom/pages/admin/edit_library.php');
$router->get('/portal-admin/informasi', 'custom/pages/admin/manage_info.php');
$router->get('/portal-admin/informasi/create', 'custom/pages/admin/create_info.php');
$router->get('/portal-admin/informasi/edit', 'custom/pages/admin/edit_info.php');
$router->get('/portal-admin/profil-desa', 'custom/pages/admin/manage_profile.php');

$router->get('/portal-admin/akun', 'custom/pages/admin/manage_accounts.php');
$router->get('/portal-admin/akun/tambah', 'custom/pages/admin/create_account.php');
$router->get('/portal-admin/akun/edit', 'custom/pages/admin/edit_account.php');

// Aktivitas & Pengumuman
$router->get('/portal-admin/aktivitas', 'custom/pages/admin/manage_activity.php');
$router->get('/portal-admin/pengumuman', 'custom/pages/admin/manage_announcements.php');
$router->get('/portal-admin/pengumuman/tambah', 'custom/pages/admin/create_announcement.php');

// Pathfinder Admin Modules
$router->get('/portal-admin/pathfinder', 'custom/pages/pathfinder/managepathfinder.php');
$router->get('/portal-admin/pathfinder/kategori', 'custom/pages/pathfinder/managecategory.php');

// Halaman utama pathfinder
$router->get('/pathfinder', 'custom/pages/pathfinder/index.php');

// Kategori pathfinder: /pathfinder/kategori/teknologi
$router->get('/pathfinder/kategori/{slug}', 'custom/pages/pathfinder/category.php');

// Detail pathfinder: /pathfinder/pemrograman-web
$router->get('/pathfinder/{slug}', 'custom/pages/pathfinder/detail.php');

// ============================================================
// ADMIN — Redirect ke SLiMS Admin
// ============================================================

// /admin → redirect ke SLiMS backend
// Gunakan path absolut sesuai instalasi SLiMS
$router->redirect('/admin', BASE_URL . '/slims/admin/', 302);

// ============================================================
// 404 — Halaman tidak ditemukan
// ============================================================

$router->set404('custom/pages/404.php');
