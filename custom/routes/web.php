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
$router->get('/donasi',    'custom/pages/donasi.php');
$router->get('/produk',    'custom/pages/produk.php');

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
// Specific CMS routes for each info type
$router->get('/portal-admin/informasi/create-faq', 'custom/pages/admin/create_info_faq.php');
$router->get('/portal-admin/informasi/edit-faq', 'custom/pages/admin/edit_info_faq.php');
$router->get('/portal-admin/informasi/create-peminjaman', 'custom/pages/admin/create_info_peminjaman.php');
$router->get('/portal-admin/informasi/edit-peminjaman', 'custom/pages/admin/edit_info_peminjaman.php');
$router->get('/portal-admin/informasi/edit-peminjaman-header', 'custom/pages/admin/edit_info_peminjaman_header.php');
$router->get('/portal-admin/informasi/create-jam_operasional', 'custom/pages/admin/create_info_jam_operasional.php');
$router->get('/portal-admin/informasi/edit-jam_operasional', 'custom/pages/admin/edit_info_jam_operasional.php');
$router->get('/portal-admin/informasi/create-unduhan', 'custom/pages/admin/create_info_unduhan.php');
$router->get('/portal-admin/informasi/edit-unduhan', 'custom/pages/admin/edit_info_unduhan.php');
$router->get('/portal-admin/informasi/create-tata_tertib', 'custom/pages/admin/create_info_tata_tertib.php');
$router->get('/portal-admin/informasi/edit-tata_tertib', 'custom/pages/admin/edit_info_tata_tertib.php');
$router->get('/portal-admin/informasi/edit-tata_tertib-header', 'custom/pages/admin/edit_info_tata_tertib_header.php');
$router->get('/portal-admin/informasi/create-keanggotaan', 'custom/pages/admin/create_info_keanggotaan.php');
$router->get('/portal-admin/informasi/edit-keanggotaan', 'custom/pages/admin/edit_info_keanggotaan.php');
$router->get('/portal-admin/profil-desa', 'custom/pages/admin/manage_profile.php');

$router->get('/portal-admin/akun', 'custom/pages/admin/manage_accounts.php');
$router->get('/portal-admin/akun/tambah', 'custom/pages/admin/create_account.php');
$router->get('/portal-admin/akun/edit', 'custom/pages/admin/edit_account.php');

// Settings & Backup
$router->get('/portal-admin/settings', 'custom/pages/admin/settings.php');
$router->get('/portal-admin/settings/backup', 'custom/pages/admin/settings.php'); // we can handle backup in the same file or a separate one, but let's route it to settings.php with action=backup or a new file backup.php. Let's make a dedicated route just in case.

// Aktivitas & Pengumuman
$router->get('/portal-admin/aktivitas', 'custom/pages/admin/manage_activity.php');
$router->get('/portal-admin/pengumuman', 'custom/pages/admin/manage_announcements.php');
$router->get('/portal-admin/pengumuman/tambah', 'custom/pages/admin/create_announcement.php');

// Donasi
$router->get('/portal-admin/donasi', 'custom/pages/admin/manage_donasi.php');
$router->get('/portal-admin/donasi/tambah-donatur', 'custom/pages/admin/create_donatur.php');
$router->get('/portal-admin/donasi/tambah-partner', 'custom/pages/admin/create_donasi_partner.php');
$router->get('/portal-admin/donasi/edit-donatur', 'custom/pages/admin/edit_donatur.php');
$router->get('/portal-admin/donasi/edit-partner', 'custom/pages/admin/edit_donasi_partner.php');

// Produk
$router->get('/portal-admin/produk', 'custom/pages/admin/manage_produk.php');
$router->get('/portal-admin/produk/tambah', 'custom/pages/admin/create_produk.php');
$router->get('/portal-admin/produk/edit', 'custom/pages/admin/edit_produk.php');

// Pathfinder Admin Modules
$router->get('/portal-admin/pathfinder', 'custom/pages/pathfinder/managepathfinder.php');
$router->get('/portal-admin/pathfinder/kategori', 'custom/pages/pathfinder/managecategory.php');
$router->get('/portal-admin/pathfinder/edit', 'custom/pages/pathfinder/edittopic.php');

// Halaman utama pathfinder (lama)
$router->get('/pathfinder', 'custom/pages/pathfinder/index.php');

// Halaman Pathfinder baru — dua panel (sidebar kategori + daftar topik)
// /pathfinder/jelajahi           → tampilkan semua, kategori pertama dipilih
// /pathfinder/jelajahi/{slug}    → kategori tertentu aktif di sidebar
// /pathfinder/jelajahi/{slug}/{topik}/buku/{id} → tampilan detail buku di dalam topik
$router->get('/pathfinder/jelajahi',                             'custom/pages/pathfinder/browse.php');
$router->get('/pathfinder/jelajahi/{slug}',                      'custom/pages/pathfinder/browse.php');
$router->get('/pathfinder/jelajahi/{slug}/{topik}',              'custom/pages/pathfinder/browse.php');
$router->get('/pathfinder/jelajahi/{slug}/{topik}/buku/{id}',    'custom/pages/pathfinder/browse.php');
$router->get('/pathfinder/buku/{id}',                            'custom/pages/pathfinder/browse.php');

// Kategori pathfinder: /pathfinder/kategori/teknologi
$router->get('/pathfinder/kategori/{slug}', 'custom/pages/pathfinder/category.php');

// Detail pathfinder: /pathfinder/pemrograman-web
$router->get('/pathfinder/{slug}', 'custom/pages/pathfinder/detail.php');

// Download PDF pathfinder
$router->get('/pathfinder/download/{slug}', 'custom/pages/pathfinder/download.php');

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
