<?php
/**
 * Pathfinder – Halaman Kategori
 *
 * File    : category.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Menampilkan daftar pathfinder yang difilter berdasarkan kategori.
 * URL: /pathfinder/kategori/{slug}
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage = 'pathfinder';

// ── Service Layer ──────────────────────────────────────────────
$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/PathfinderService.php';

$pfService = new PathfinderService();

// Ambil slug kategori dari route params
$categorySlug = $routeParams['slug'] ?? '';
$category     = $pfService->getCategoryBySlug($categorySlug);

$tags = isset($category['tags']) && is_string($category['tags']) ? json_decode($category['tags'], true) : ($category['tags'] ?? []);
$category['tags'] = is_array($tags) ? $tags : [];

// Redirect ke 404 jika kategori tidak ditemukan
if ($category === null) {
    http_response_code(404);
    include $libPath . '/custom/pages/404.php';
    exit;
}

// Ambil pathfinder per kategori (dengan paginasi)
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$result      = $pfService->getPathfindersByCategory($categorySlug, $currentPage, 6);

$pathfinders = $result['items'];
$totalItems  = $result['total'];
$totalPages  = $result['total_pages'];
$perPage     = $result['per_page'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kategori <?= htmlspecialchars($category['label']) ?> – Pathfinder Perpustakaan Baca Di Teras. <?= htmlspecialchars(mb_substr($category['description'], 0, 120)) ?>">
    <title>Kategori: <?= htmlspecialchars($category['label']) ?> – Pathfinder – Baca Di Teras</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Main styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/landing.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/pathfinder.css">
</head>
<body>
    <?php include __DIR__ . '/../../components/navbar.php'; ?>

    <div class="pf-wrapper">

        <!-- ============================================================
             Breadcrumb
             ============================================================ -->
        <nav class="pf-breadcrumb" id="pf-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="vertical-align: -2px;">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Beranda
            </a>
            <span class="pf-breadcrumb__sep" aria-hidden="true">&rsaquo;</span>
            <a href="<?= BASE_URL ?>/pathfinder">Eksplorasi</a>
            <span class="pf-breadcrumb__sep" aria-hidden="true">&rsaquo;</span>
            <span class="pf-breadcrumb__current">Kategori: <?= htmlspecialchars($category['label']) ?></span>
        </nav>

        <!-- ============================================================
             Category Hero
             ============================================================ -->
        <header class="pf-cat-hero" id="pf-cat-hero">
            <div class="pf-cat-hero__info">
                <h1 class="pf-cat-hero__title">Kategori: <?= htmlspecialchars($category['label']) ?></h1>
                <p class="pf-cat-hero__desc"><?= htmlspecialchars($category['description']) ?></p>
            </div>
            <div class="pf-cat-hero__action">
                <button class="pf-btn-bookmark" id="pf-btn-save-category" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                    </svg>
                    Simpan Kategori
                </button>
            </div>
        </header>

        <!-- ============================================================
             Search & Filter Bar
             ============================================================ -->
        <div class="pf-filter-bar" id="pf-filter-bar">
            <div class="pf-filter-bar__top">
                <div class="pf-filter-bar__search">
                    <svg class="pf-filter-bar__search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text"
                           class="pf-filter-bar__search-input"
                           id="pf-cat-search-input"
                           placeholder="Cari topik dalam kategori <?= htmlspecialchars($category['label']) ?>..."
                           autocomplete="off">
                </div>
                <button class="pf-filter-btn" id="pf-filter-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="4" y1="21" x2="4" y2="14"></line>
                        <line x1="4" y1="10" x2="4" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12" y2="3"></line>
                        <line x1="20" y1="21" x2="20" y2="16"></line>
                        <line x1="20" y1="12" x2="20" y2="3"></line>
                        <line x1="1" y1="14" x2="7" y2="14"></line>
                        <line x1="9" y1="8" x2="15" y2="8"></line>
                        <line x1="17" y1="16" x2="23" y2="16"></line>
                    </svg>
                    Filter
                </button>
                <button class="pf-filter-btn" id="pf-sort-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <polyline points="19 12 12 19 5 12"></polyline>
                    </svg>
                    Urutkan
                </button>
            </div>

            <!-- Tags -->
            <div class="pf-filter-bar__tags">
                <?php foreach ($category['tags'] as $tag) : ?>
                    <span class="pf-tag"><?= htmlspecialchars($tag) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ============================================================
             Pathfinder Grid
             ============================================================ -->
        <div class="pf-cat-grid" id="pf-cat-grid">
            <?php if (empty($pathfinders)) : ?>
                <p style="grid-column: 1/-1; text-align: center; color: var(--pf-gray-500); padding: 40px 0;">
                    Belum ada pathfinder dalam kategori ini.
                </p>
            <?php else : ?>
                <?php foreach ($pathfinders as $pf) : ?>
                    <article class="pf-cat-card" id="pf-cat-card-<?= htmlspecialchars($pf['slug']) ?>">
                        <!-- Thumbnail -->
                        <div class="pf-cat-card__img-wrap">
                            <div class="pf-placeholder-img">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                            </div>
                            <?php if (!empty($pf['badge'])) : ?>
                                <span class="pf-cat-card__badge"><?= htmlspecialchars($pf['badge']) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="pf-cat-card__body">
                            <h3 class="pf-cat-card__title">
                                <a href="<?= BASE_URL ?>/pathfinder/<?= htmlspecialchars($pf['slug']) ?>"
                                   id="pf-cat-link-<?= htmlspecialchars($pf['slug']) ?>">
                                    <?= htmlspecialchars($pf['title']) ?>
                                </a>
                            </h3>
                            <p class="pf-cat-card__desc">
                                <?= htmlspecialchars(mb_substr($pf['description'], 0, 150)) ?>...
                            </p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- ============================================================
             Pagination
             ============================================================ -->
        <?php if ($totalPages > 1) : ?>
        <nav class="pf-pagination" id="pf-pagination" aria-label="Navigasi Halaman">
            <!-- Prev -->
            <a href="<?= $currentPage > 1 ? BASE_URL . '/pathfinder/kategori/' . htmlspecialchars($categorySlug) . '?page=' . ($currentPage - 1) : '#' ?>"
               class="pf-pagination__btn <?= $currentPage <= 1 ? 'disabled' : '' ?>"
               <?= $currentPage <= 1 ? 'aria-disabled="true" tabindex="-1"' : '' ?>
               aria-label="Halaman Sebelumnya">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </a>

            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <?php if ($i === 1 || $i === $totalPages || abs($i - $currentPage) <= 1) : ?>
                    <a href="<?= BASE_URL ?>/pathfinder/kategori/<?= htmlspecialchars($categorySlug) ?>?page=<?= $i ?>"
                       class="pf-pagination__btn <?= $i === $currentPage ? 'active' : '' ?>"
                       <?= $i === $currentPage ? 'aria-current="page"' : '' ?>>
                        <?= $i ?>
                    </a>
                <?php elseif ($i === 2 || $i === $totalPages - 1) : ?>
                    <span class="pf-pagination__dots">...</span>
                <?php endif; ?>
            <?php endfor; ?>

            <!-- Next -->
            <a href="<?= $currentPage < $totalPages ? BASE_URL . '/pathfinder/kategori/' . htmlspecialchars($categorySlug) . '?page=' . ($currentPage + 1) : '#' ?>"
               class="pf-pagination__btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>"
               <?= $currentPage >= $totalPages ? 'aria-disabled="true" tabindex="-1"' : '' ?>
               aria-label="Halaman Berikutnya">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
        </nav>
        <?php endif; ?>

    </div><!-- /.pf-wrapper -->

    <?php include __DIR__ . '/../../components/footer.php'; ?>

    <!-- Category search filter script -->
    <script>
    (function() {
        'use strict';
        var searchInput = document.getElementById('pf-cat-search-input');
        var grid = document.getElementById('pf-cat-grid');

        if (!searchInput || !grid) return;

        searchInput.addEventListener('input', function() {
            var query = this.value.toLowerCase().trim();
            var cards = grid.querySelectorAll('.pf-cat-card');

            cards.forEach(function(card) {
                var title = (card.querySelector('.pf-cat-card__title') || {}).textContent || '';
                var desc  = (card.querySelector('.pf-cat-card__desc') || {}).textContent || '';
                var match = title.toLowerCase().includes(query) || desc.toLowerCase().includes(query);
                card.style.display = match ? '' : 'none';
            });
        });
    })();
    </script>

</body>
</html>
