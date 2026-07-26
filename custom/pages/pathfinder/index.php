<?php
/**
 * Pathfinder – Halaman Utama (Index)
 *
 * File    : index.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Halaman utama Pathfinder Perpustakaan, menampilkan:
 * - Hero section dengan search bar
 * - Eksplorasi kategori (chips)
 * - Pathfinder populer (card grid)
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage = 'pathfinder';

// ── Service Layer ──────────────────────────────────────────────
$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/PathfinderService.php';

$pfService  = new PathfinderService();
$categories = $pfService->getCategories();
$popular    = $pfService->getPopularPathfinders(4);

// Handle pencarian
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$searchResults = [];
$isSearching = false;

if ($searchQuery !== '') {
    $isSearching = true;
    $searchResults = $pfService->searchPathfinders($searchQuery);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pathfinder Perpustakaan Baca Di Teras – Temukan jalan pintas menuju pengetahuan. Panduan riset terkurasi dari buku fisik hingga jurnal digital.">
    <title>Pathfinder Perpustakaan – Baca Di Teras</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Main styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/landing.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/pathfinder.css">
</head>
<body>
    <?php include __DIR__ . '/../../components/navbar.php'; ?>

    <!-- ============================================================
         Hero Section
         ============================================================ -->
    <section class="pf-hero" id="pf-hero">
        <div class="pf-hero__inner">
            <h1 class="pf-hero__title">Pathfinder Perpustakaan</h1>
            <p class="pf-hero__desc">
                Temukan jalan pintas menuju pengetahuan. Pathfinder Baca di Teras untuk membantu penelitian Anda lebih terarah dan mendalam dengan berbagai koleksi di Desa teras.
            </p>

            <!-- Search Bar -->
            <form class="pf-search" id="pf-search" action="<?= BASE_URL ?>/pathfinder" method="GET" role="search">
                <svg class="pf-search__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text"
                       class="pf-search__input"
                       id="pf-search-input"
                       name="q"
                       value="<?= htmlspecialchars($searchQuery) ?>"
                       placeholder="Cari topik penelitian atau mata kuliah..."
                       autocomplete="off">
                <button type="submit" class="pf-search__btn" id="pf-search-btn">Cari</button>
            </form>
        </div>
    </section>

    <!-- ============================================================
         Search Results (only if searching)
         ============================================================ -->
    <?php if ($isSearching) : ?>
    <div class="pf-wrapper">
        <section class="pf-popular" id="pf-search-results">
            <h2 class="pf-popular__title">
                Hasil Pencarian: "<?= htmlspecialchars($searchQuery) ?>"
                <span style="font-weight: 400; font-size: 0.85rem; color: var(--pf-gray-500); margin-left: 8px;">
                    (<?= count($searchResults) ?> ditemukan)
                </span>
            </h2>

            <?php if (empty($searchResults)) : ?>
                <p style="color: var(--pf-gray-500); font-size: 0.95rem;">
                    Tidak ada pathfinder yang cocok dengan pencarian Anda. Coba kata kunci lain.
                </p>
            <?php else : ?>
                <div class="pf-grid">
                    <?php foreach ($searchResults as $pf) : ?>
                        <article class="pf-card" id="pf-result-<?= htmlspecialchars($pf['slug']) ?>">
                            <div class="pf-card__img-wrap">
                                <div class="pf-placeholder-img">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                    </svg>
                                </div>
                                <?php if (!empty($pf['badge'])) : ?>
                                    <span class="pf-card__badge"><?= htmlspecialchars($pf['badge']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="pf-card__body">
                                <span class="pf-card__category">
                                    <?= htmlspecialchars($pf['category_label']) ?>
                                </span>
                                <h3 class="pf-card__title"><?= htmlspecialchars($pf['title']) ?></h3>
                                <div class="pf-card__footer">
                                    <span class="pf-card__recs">
                                        <svg class="pf-card__recs-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                        </svg>
                                        <?= $pf['recommendations'] ?> Rekomendasi
                                    </span>
                                    <a href="<?= BASE_URL ?>/pathfinder/<?= htmlspecialchars($pf['slug']) ?>"
                                       class="pf-card__link">
                                        Lihat
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <?php else : ?>

    <!-- ============================================================
         Eksplorasi Kategori
         ============================================================ -->
    <div class="pf-wrapper">
        <section class="pf-categories" id="pf-categories" aria-label="Eksplorasi Kategori">
            <div class="pf-categories__header">
                <h2 class="pf-categories__title">Eksplorasi Kategori</h2>
                <a href="#pf-categories" class="pf-categories__view-all" id="pf-view-all-categories">
                    Lihat Semua &rarr;
                </a>
            </div>

            <ul class="pf-categories__list" role="list">
                <?php foreach ($categories as $cat) : ?>
                    <li>
                        <a href="<?= BASE_URL ?>/pathfinder/kategori/<?= htmlspecialchars($cat['slug']) ?>"
                           class="pf-chip"
                           id="pf-chip-<?= htmlspecialchars($cat['slug']) ?>">
                            <svg class="pf-chip__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <?php
                                // Render icon berdasarkan tipe kategori
                                switch ($cat['icon']) {
                                    case 'globe':
                                        echo '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>';
                                        break;
                                    case 'flask':
                                        echo '<path d="M9 3h6v7l5 9H4l5-9V3z"/><line x1="9" y1="3" x2="15" y2="3"/>';
                                        break;
                                    case 'palette':
                                        echo '<circle cx="13.5" cy="6.5" r="0.5"/><circle cx="17.5" cy="10.5" r="0.5"/><circle cx="8.5" cy="7.5" r="0.5"/><circle cx="6.5" cy="12.5" r="0.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.555C21.965 6.012 17.461 2 12 2z"/>';
                                        break;
                                    case 'chart':
                                        echo '<line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/>';
                                        break;
                                    case 'scale':
                                        echo '<path d="M1 12L12 2l11 10"/><path d="M3 10v10h18V10"/><rect x="9" y="14" width="6" height="6"/>';
                                        break;
                                    default:
                                        echo '<circle cx="12" cy="12" r="10"/>';
                                }
                                ?>
                            </svg>
                            <?= htmlspecialchars($cat['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </div>

    <!-- ============================================================
         Pathfinder Populer
         ============================================================ -->
    <div class="pf-wrapper">
        <section class="pf-popular" id="pf-popular" aria-label="Pathfinder Populer">
            <h2 class="pf-popular__title">Pathfinder Populer</h2>

            <div class="pf-grid">
                <?php foreach ($popular as $pf) : ?>
                    <article class="pf-card" id="pf-card-<?= htmlspecialchars($pf['slug']) ?>">
                        <!-- Thumbnail -->
                        <div class="pf-card__img-wrap">
                            <div class="pf-placeholder-img">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                            </div>
                            <?php if (!empty($pf['badge'])) : ?>
                                <span class="pf-card__badge"><?= htmlspecialchars($pf['badge']) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="pf-card__body">
                            <!-- Category label -->
                            <span class="pf-card__category">
                                <svg class="pf-card__category-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <?php
                                    switch ($pf['category_icon']) {
                                        case 'code':
                                            echo '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>';
                                            break;
                                        case 'brain':
                                            echo '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>';
                                            break;
                                        case 'briefcase':
                                            echo '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>';
                                            break;
                                        case 'building':
                                            echo '<path d="M1 12L12 2l11 10"/><path d="M3 10v10h18V10"/>';
                                            break;
                                        case 'cpu':
                                            echo '<rect x="4" y="4" width="16" height="16" rx="2" ry="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/>';
                                            break;
                                        default:
                                            echo '<circle cx="12" cy="12" r="10"/>';
                                    }
                                    ?>
                                </svg>
                                <?= htmlspecialchars($pf['category_label']) ?>
                            </span>

                            <!-- Title -->
                            <h3 class="pf-card__title">
                                <?= htmlspecialchars($pf['title']) ?>
                            </h3>

                            <!-- Footer -->
                            <div class="pf-card__footer">
                                <span class="pf-card__recs">
                                    <svg class="pf-card__recs-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                    </svg>
                                    <?= $pf['recommendations'] ?> Rekomendasi
                                </span>
                                <a href="<?= BASE_URL ?>/pathfinder/<?= htmlspecialchars($pf['slug']) ?>"
                                   class="pf-card__link"
                                   id="pf-link-<?= htmlspecialchars($pf['slug']) ?>">
                                    Lihat
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <?php endif; ?>

    <?php include __DIR__ . '/../../components/footer.php'; ?>

</body>
</html>
