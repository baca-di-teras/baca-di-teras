<?php
/**
 * Halaman Rilis Media – Baca Di Teras
 *
 * File    : media.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Menampilkan dokumentasi rilis dan liputan media (Media Nasional, Web Prodi, Web Desa)
 * sesuai arahan dosen pembimbing (Pak Gani) untuk bukti monev dan publikasi KKN.
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage = 'media';

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..';
require_once $libPath . '/custom/services/MediaService.php';

$mediaService = new MediaService();
$mediaList = $mediaService->getPublicMedia();

// Hitung statistik per kategori
$counts = [
    'semua' => count($mediaList),
    'Media Nasional' => 0,
    'Web Prodi' => 0,
    'Web Desa' => 0,
    'Lainnya' => 0
];

foreach ($mediaList as $item) {
    $cat = $item['category'] ?? 'Lainnya';
    if (isset($counts[$cat])) {
        $counts[$cat]++;
    } else {
        $counts['Lainnya']++;
    }
}

/**
 * Helper untuk menentukan class badge kategori
 */
function getBadgeClass($category) {
    switch ($category) {
        case 'Media Nasional':
            return 'bdt-media-badge--nasional';
        case 'Web Prodi':
            return 'bdt-media-badge--prodi';
        case 'Web Desa':
            return 'bdt-media-badge--desa';
        default:
            return 'bdt-media-badge--nasional';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rilis Media – Baca Di Teras</title>
    <meta name="description" content="Dokumentasi publikasi dan liputan kegiatan Baca Di Teras di media nasional, portal akademik prodi, dan web desa.">
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,600;1,700&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/navbar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/media.css">
</head>
<body class="media-page">

    <!-- Header Navbar -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <main class="bdt-media-container" id="main-content">
        
        <!-- Header Page -->
        <header class="bdt-media-header">
            <h1 class="bdt-media-header__title">Rilis Media</h1>
            <p class="bdt-media-header__desc">
                Dokumentasi publikasi dan rekam jejak liputan kegiatan Baca Di Teras di berbagai media nasional, portal akademik prodi, serta media desa.
            </p>
        </header>

        <!-- Controls: Filter Chips & Search -->
        <div class="bdt-media-controls">
            <!-- Category Filter Buttons -->
            <div class="bdt-media-filters" role="tablist" aria-label="Filter Kategori Media">
                <button type="button" class="bdt-media-filter-btn active" data-filter="semua" role="tab" aria-selected="true">
                    Semua <span class="bdt-media-filter-count"><?= $counts['semua'] ?></span>
                </button>
                <button type="button" class="bdt-media-filter-btn" data-filter="Media Nasional" role="tab" aria-selected="false">
                    Media Nasional <span class="bdt-media-filter-count"><?= $counts['Media Nasional'] ?></span>
                </button>
                <button type="button" class="bdt-media-filter-btn" data-filter="Web Prodi" role="tab" aria-selected="false">
                    Web Prodi <span class="bdt-media-filter-count"><?= $counts['Web Prodi'] ?></span>
                </button>
                <button type="button" class="bdt-media-filter-btn" data-filter="Web Desa" role="tab" aria-selected="false">
                    Web Desa <span class="bdt-media-filter-count"><?= $counts['Web Desa'] ?></span>
                </button>
            </div>

            <!-- Client-side Search Input -->
            <div class="bdt-media-search">
                <svg class="bdt-media-search__icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="bdt-media-search-input" class="bdt-media-search__input" placeholder="Cari media / berita..." aria-label="Cari rilis media">
            </div>
        </div>

        <!-- Media List -->
        <section class="bdt-media-list" id="bdt-media-list" aria-label="Daftar Liputan Media">
            <?php if (empty($mediaList)): ?>
                <div class="bdt-media-empty">
                    <div class="bdt-media-empty__icon">📰</div>
                    <h3>Belum ada rilis media yang dipublikasikan.</h3>
                    <p>Silakan periksa kembali nanti.</p>
                </div>
            <?php else: ?>
                <?php foreach ($mediaList as $item): ?>
                    <article class="bdt-media-item" data-category="<?= htmlspecialchars($item['category']) ?>">
                        
                        <div class="bdt-media-item__header">
                            <h2 class="bdt-media-item__title">
                                <?= htmlspecialchars($item['title']) ?>
                            </h2>
                            <span class="bdt-media-badge <?= getBadgeClass($item['category']) ?>">
                                <?= htmlspecialchars($item['category']) ?>
                            </span>
                        </div>

                        <div class="bdt-media-item__meta">
                            <span class="bdt-media-item__source-label">Media:</span>
                            <span class="bdt-media-item__source-name"><?= htmlspecialchars($item['media_name']) ?></span>
                            <?php if (!empty($item['release_date'])): ?>
                                <span class="bdt-media-item__date">
                                    • <?= date('d M Y', strtotime($item['release_date'])) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($item['description'])): ?>
                            <p class="bdt-media-item__desc">
                                <?= htmlspecialchars($item['description']) ?>
                            </p>
                        <?php endif; ?>

                        <div class="bdt-media-item__url-wrapper">
                            <a href="<?= htmlspecialchars($item['url']) ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="bdt-media-item__url"
                               title="Buka tautan asli berita di tab baru">
                                <?= htmlspecialchars($item['url']) ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                    <polyline points="15 3 21 3 21 9"></polyline>
                                    <line x1="10" y1="14" x2="21" y2="3"></line>
                                </svg>
                            </a>
                        </div>

                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

    </main>

    <!-- Footer Component -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Interactive Filtering Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterBtns = document.querySelectorAll('.bdt-media-filter-btn');
        const mediaItems = document.querySelectorAll('.bdt-media-item');
        const searchInput = document.getElementById('bdt-media-search-input');
        
        let currentFilter = 'semua';

        function filterMedia() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

            mediaItems.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                const titleText = item.querySelector('.bdt-media-item__title').textContent.toLowerCase();
                const sourceText = item.querySelector('.bdt-media-item__source-name').textContent.toLowerCase();

                const matchesFilter = (currentFilter === 'semua' || itemCategory === currentFilter);
                const matchesSearch = (!searchTerm || titleText.includes(searchTerm) || sourceText.includes(searchTerm));

                if (matchesFilter && matchesSearch) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                filterBtns.forEach(b => {
                    b.classList.remove('active');
                    b.setAttribute('aria-selected', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                
                currentFilter = this.getAttribute('data-filter');
                filterMedia();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', filterMedia);
        }
    });
    </script>

</body>
</html>
