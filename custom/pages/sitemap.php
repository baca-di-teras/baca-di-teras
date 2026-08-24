<?php
/**
 * XML Sitemap Generator
 */
header("Content-Type: application/xml; charset=utf-8");

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..';
if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

require_once $libPath . '/custom/helpers/Database.php';

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . '://' . $host . BASE_URL;

$db = Database::getInstance();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Function to print a url node
function printUrl($url, $lastmod = null, $changefreq = 'weekly', $priority = '0.8') {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($url) . "</loc>\n";
    if ($lastmod) {
        echo "    <lastmod>" . date('Y-m-d', strtotime($lastmod)) . "</lastmod>\n";
    }
    echo "    <changefreq>$changefreq</changefreq>\n";
    echo "    <priority>$priority</priority>\n";
    echo "  </url>\n";
}

// 1. Static Pages
printUrl($baseUrl . '/', date('c'), 'daily', '1.0');
printUrl($baseUrl . '/profil', date('c'), 'monthly', '0.8');
printUrl($baseUrl . '/perpustakaan', date('c'), 'weekly', '0.8');
printUrl($baseUrl . '/katalog', date('c'), 'daily', '0.9');
printUrl($baseUrl . '/berita', date('c'), 'daily', '0.9');
printUrl($baseUrl . '/artikel', date('c'), 'daily', '0.9');
printUrl($baseUrl . '/informasi', date('c'), 'monthly', '0.7');
printUrl($baseUrl . '/kontak', date('c'), 'monthly', '0.7');
printUrl($baseUrl . '/donasi', date('c'), 'monthly', '0.7');
printUrl($baseUrl . '/produk', date('c'), 'weekly', '0.8');
printUrl($baseUrl . '/media', date('c'), 'weekly', '0.8');

// 2. Dynamic Articles (Artikel)
$articles = $db->fetchAll("SELECT slug, updated_at FROM bdt_article WHERE status = 'published' AND category IN ('resensi', 'literasi', 'lainnya')");
foreach ($articles as $a) {
    printUrl($baseUrl . '/artikel/' . $a['slug'], $a['updated_at'], 'monthly', '0.7');
}

// 3. Dynamic News (Berita)
$news = $db->fetchAll("SELECT slug, updated_at FROM bdt_article WHERE status = 'published' AND category IN ('berita', 'kegiatan', 'pengumuman')");
foreach ($news as $n) {
    printUrl($baseUrl . '/berita/' . $n['slug'], $n['updated_at'], 'monthly', '0.8');
}

// 4. Dynamic Libraries (Perpustakaan)
$libraries = $db->fetchAll("SELECT slug FROM bdt_library");
foreach ($libraries as $l) {
    printUrl($baseUrl . '/perpustakaan/' . $l['slug'], date('c'), 'monthly', '0.7');
}

echo "</urlset>\n";
