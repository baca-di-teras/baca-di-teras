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

// Warna acak atau berdasarkan index untuk badge
function getBadgeColor($index) {
    $colors = ['#4caf50', '#2196f3', '#ff9800', '#e91e63', '#9c27b0'];
    return $colors[$index % count($colors)];
}

// Helper untuk variasi layout isi dalam card (Pinterest style abstract)
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
    <?php
    $itemListElements = [];
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $baseUrlFull = $protocol . '://' . $_SERVER['HTTP_HOST'] . BASE_URL;
    foreach ($produks as $index => $p) {
        $imgUrl = '';
        if (!empty($p['image_1'])) {
            $imgUrl = strpos($p['image_1'], 'http') === 0 ? $p['image_1'] : $baseUrlFull . '/' . ltrim($p['image_1'], '/');
        }
        $itemListElements[] = [
            "@type" => "ListItem",
            "position" => $index + 1,
            "item" => [
                "@type" => "Product",
                "name" => $p['title'],
                "description" => $p['description'],
                "image" => $imgUrl
            ]
        ];
    }
    ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ItemList",
      "itemListElement": <?= json_encode($itemListElements) ?>
    }
    </script>
</head>
<body class="produk-page">
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="produk-header">
        <h1 class="produk-title">Galeri Produk</h1>
    </div>

    <section class="produk-stack-container" aria-label="Katalog Produk">
        <?php if (empty($produks)): ?>
            <div style="text-align:center; color:#6b7280; padding:60px 20px; width: 100%;">
                <p>Belum ada produk yang ditampilkan saat ini.</p>
            </div>
        <?php else: ?>
            <?php foreach ($produks as $index => $produk): 
                $badgeColor = getBadgeColor($index);
                $stackClass = ($index % 2 === 0) ? 'stack-left' : 'stack-right';
            ?>
            <div class="produk-stack-row <?= $stackClass ?>">
                
                <!-- Text Card (Stack Depan) -->
                <div class="stack-item stack-text-card">
                    <span class="stack-badge" style="color: <?= $badgeColor ?>; background: <?= $badgeColor ?>1A;">
                        <?= htmlspecialchars($produk['group_name']) ?>
                    </span>
                    <h2 class="stack-title"><?= htmlspecialchars($produk['title']) ?></h2>
                    <p class="stack-desc"><?= nl2br(htmlspecialchars($produk['description'])) ?></p>
                </div>

                <!-- Gambar Utama (Stack 2) -->
                <?php if (!empty($produk['image_1'])): ?>
                    <div class="stack-item stack-img">
                        <img src="<?= BASE_URL . '/' . htmlspecialchars($produk['image_1']) ?>" alt="<?= htmlspecialchars($produk['title']) ?>" loading="lazy">
                        <div class="stack-img-badge" style="background-color: <?= $badgeColor ?>;">
                            <?= htmlspecialchars($produk['group_name']) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Gambar Tambahan 1 (Stack 3) -->
                <?php if (!empty($produk['image_2'])): ?>
                    <div class="stack-item stack-img">
                        <img src="<?= BASE_URL . '/' . htmlspecialchars($produk['image_2']) ?>" alt="<?= htmlspecialchars($produk['title']) ?> detail 1" loading="lazy">
                        <div class="stack-img-badge" style="background-color: <?= $badgeColor ?>;">
                            <?= htmlspecialchars($produk['group_name']) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Gambar Tambahan 2 (Stack 4) -->
                <?php if (!empty($produk['image_3'])): ?>
                    <div class="stack-item stack-img">
                        <img src="<?= BASE_URL . '/' . htmlspecialchars($produk['image_3']) ?>" alt="<?= htmlspecialchars($produk['title']) ?> detail 2" loading="lazy">
                        <div class="stack-img-badge" style="background-color: <?= $badgeColor ?>;">
                            <?= htmlspecialchars($produk['group_name']) ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <?php include __DIR__ . '/../components/footer.php'; ?>
    
    <!-- Lightbox Modal -->
    <div id="produk-lightbox" class="produk-lightbox">
        <span class="lightbox-close">&times;</span>
        <img class="lightbox-content" id="lightbox-img">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const lightbox = document.getElementById('produk-lightbox');
            const lightboxImg = document.getElementById('lightbox-img');
            const closeBtn = document.querySelector('.lightbox-close');
            const images = document.querySelectorAll('.stack-img img');

            images.forEach(img => {
                img.addEventListener('click', (e) => {
                    lightbox.classList.add('show');
                    lightboxImg.src = e.target.src;
                });
            });

            closeBtn.addEventListener('click', () => {
                lightbox.classList.remove('show');
            });

            lightbox.addEventListener('click', (e) => {
                if (e.target === lightbox) {
                    lightbox.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>
