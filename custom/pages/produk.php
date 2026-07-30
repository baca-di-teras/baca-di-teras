<?php
/**
 * Halaman Galeri Produk – Baca Di Teras
 *
 * File    : produk.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage = 'produk';

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..';
require_once $libPath . '/custom/services/ProdukService.php';

$produkService = new ProdukService();
$produks = $produkService->getPublicProduk();

// Helper untuk memilih layout pattern berdasarkan index
function getLayoutClass($index) {
    $layouts = ['layout-a', 'layout-b', 'layout-c'];
    return $layouts[$index % count($layouts)];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Produk – Baca Di Teras</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/navbar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/produk.css">
    <style>
        html, body { margin: 0; background: #fcfbf9; }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="produk-page">
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="produk-header">
        <h1 class="produk-title">Galeri Produk</h1>
    </div>

    <section class="produk-container" aria-label="Katalog Produk">
        <?php if (empty($produks)): ?>
            <div style="text-align:center; color:#6b7280; padding:60px 20px;">
                <p>Belum ada produk yang ditampilkan saat ini.</p>
            </div>
        <?php else: ?>
            <?php foreach ($produks as $index => $produk): 
                $layoutClass = getLayoutClass($index);
            ?>
            <article class="produk-item <?= $layoutClass ?>">
                
                <!-- Gambar Utama (Large) -->
                <div class="produk-img-large">
                    <?php if (!empty($produk['image_1'])): ?>
                        <img src="<?= BASE_URL . '/' . htmlspecialchars($produk['image_1']) ?>" alt="<?= htmlspecialchars($produk['title']) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="produk-placeholder" style="height:100%;">No Image</div>
                    <?php endif; ?>
                </div>

                <!-- Text Card -->
                <div class="produk-card">
                    <span class="produk-group"><?= htmlspecialchars($produk['group_name']) ?></span>
                    <h2 class="produk-name"><?= htmlspecialchars($produk['title']) ?></h2>
                    <p class="produk-desc"><?= nl2br(htmlspecialchars($produk['description'])) ?></p>
                </div>

                <!-- Gambar Kecil 1 -->
                <div class="produk-img-small produk-img-small-1">
                    <?php if (!empty($produk['image_2'])): ?>
                        <img src="<?= BASE_URL . '/' . htmlspecialchars($produk['image_2']) ?>" alt="<?= htmlspecialchars($produk['title']) ?> detail 1" loading="lazy">
                    <?php else: ?>
                        <div class="produk-placeholder" style="height:100%;"></div>
                    <?php endif; ?>
                </div>

                <!-- Blok terakhir: Gambar Kecil 2 atau Icon Box -->
                <?php if ($layoutClass === 'layout-a' || $layoutClass === 'layout-c'): ?>
                    <!-- Menggunakan Icon Box hijau sesuai desain Tas Pustaka / Tumblr -->
                    <div class="produk-icon-box <?= $layoutClass === 'layout-a' ? 'produk-icon-box-light' : '' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                    </div>
                <?php else: ?>
                    <!-- Layout B menggunakan 2 gambar kecil (Kaos Baca) -->
                    <div class="produk-img-small produk-img-small-2">
                        <?php if (!empty($produk['image_3'])): ?>
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($produk['image_3']) ?>" alt="<?= htmlspecialchars($produk['title']) ?> detail 2" loading="lazy">
                        <?php else: ?>
                            <div class="produk-placeholder" style="height:100%;"></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
