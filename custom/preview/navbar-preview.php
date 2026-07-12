<?php
/**
 * Navbar Preview
 * File ini hanya untuk keperluan development preview.
 * JANGAN di-deploy ke production.
 */

// Simulasi BASE_URL untuk localhost XAMPP
define('BASE_URL', '/bacaditeras/baca-di-teras');

// Set halaman aktif — bisa di-switch via ?active=nama-menu
$allowedPages = ['beranda', 'profil-desa', 'perpustakaan', 'berita', 'artikel', 'informasi'];
$activePage   = (isset($_GET['active']) && in_array($_GET['active'], $allowedPages))
                ? $_GET['active']
                : 'beranda';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview – Navbar | Baca Di Teras</title>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
        }
        .preview-info {
            max-width: 1280px;
            margin: 32px auto;
            padding: 0 24px;
            font-family: monospace;
            font-size: 13px;
            color: #6b7280;
        }
        .preview-info span {
            background: #e5e7eb;
            padding: 2px 8px;
            border-radius: 4px;
        }
        /* Simulasi konten halaman di bawah navbar */
        .preview-content {
            max-width: 1280px;
            margin: 24px auto;
            padding: 0 24px;
        }
        .preview-box {
            background: #ffffff;
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 48px;
            text-align: center;
            color: #9ca3af;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }
        .preview-active-switcher {
            max-width: 1280px;
            margin: 0 auto 16px;
            padding: 0 24px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            align-items: center;
        }
        .preview-active-switcher strong { color: #374151; margin-right: 4px; }
        .preview-active-switcher a {
            padding: 4px 10px;
            border-radius: 4px;
            background: #e5e7eb;
            color: #374151;
            text-decoration: none;
            transition: background 0.2s;
        }
        .preview-active-switcher a:hover { background: #d1d5db; }
        .preview-active-switcher a.current { background: #1a6b2f; color: #fff; }
    </style>
</head>
<body>

    <!-- Navbar Component -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <!-- Switcher Active Page -->
    <div class="preview-active-switcher">
        <strong>Active Page:</strong>
        <?php
        $pages = ['beranda', 'profil-desa', 'perpustakaan', 'berita', 'artikel', 'informasi'];
        foreach ($pages as $page) {
            $isCurrent = $activePage === $page ? ' class="current"' : '';
            echo "<a href=\"?active={$page}\"{$isCurrent}>{$page}</a>";
        }
        ?>
    </div>

    <!-- Preview Info -->
    <div class="preview-info">
        Active page: <span><?= htmlspecialchars($activePage) ?></span>
        &nbsp;|&nbsp;
        File: <span>custom/components/navbar.php</span>
        &nbsp;|&nbsp;
        CSS: <span>custom/assets/css/navbar.css</span>
    </div>

    <!-- Dummy Content -->
    <div class="preview-content">
        <div class="preview-box">
            ↑ Navbar Preview<br><br>
            Konten halaman ada di sini
        </div>
    </div>

</body>
</html>


