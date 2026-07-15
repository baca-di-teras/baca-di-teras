<?php
/**
 * Halaman Daftar Artikel – Baca Di Teras
 *
 * File    : article/index.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Menampilkan daftar artikel (semua kategori non-berita) dari bdt_article.
 * Mendukung filter berdasarkan kategori.
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage = 'artikel';
$libPath    = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';

require_once $libPath . '/custom/helpers/Database.php';
require_once $libPath . '/custom/services/ArticleService.php';

$articleService = new ArticleService();

// Filter kategori dari query string
$activeCategory = $_GET['kategori'] ?? '';
$articleCategories = array_filter(
    ArticleService::CATEGORY_LABELS,
    static fn ($label, $key): bool => $key !== 'berita',
    ARRAY_FILTER_USE_BOTH
);
if ($activeCategory && (!isset($articleCategories[$activeCategory]) || !in_array($activeCategory, ArticleService::CATEGORIES, true))) {
    $activeCategory = '';
}

// Pagination sederhana
$perPage  = 9;
$page     = max(1, (int)($_GET['halaman'] ?? 1));
$offset   = ($page - 1) * $perPage;
$total    = $articleService->countPublishedArticles($activeCategory);
$maxPage  = (int) ceil($total / $perPage);

// Ambil artikel
if ($activeCategory) {
    $articles = $articleService->getByCategory($activeCategory, $perPage, $offset);
} else {
    $articles = $articleService->getPublishedArticles($perPage, $offset);
}

// Ambil artikel hero (featured)
$heroArticle = $articleService->getHeroArticle(false);

$baseUrl = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kumpulan artikel literasi, resensi buku, tips membaca, dan kegiatan perpustakaan Desa Teras Boyolali.">
    <meta name="keywords" content="artikel literasi, resensi buku, kegiatan perpustakaan, desa teras">
    <title>Artikel – Baca Di Teras</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/custom/assets/css/landing.css">
    <style>
        body { background: #f7f6f5; }

        /* Hero */
        .bdt-article-page-hero {
            background: linear-gradient(135deg, #1a3c5e 0%, #2d6a4f 100%);
            padding: 80px 0 60px;
            text-align: center;
            color: #fff;
        }
        .bdt-article-page-hero__eyebrow {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.7);
            margin-bottom: 16px;
        }
        .bdt-article-page-hero__title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            margin: 0 0 16px;
            line-height: 1.15;
        }
        .bdt-article-page-hero__desc {
            font-size: 1.125rem;
            color: rgba(255,255,255,0.8);
            max-width: 560px;
            margin: 0 auto;
            line-height: 1.7;
        }

        .bdt-article-main { padding: 64px 0 80px; }

        /* Category Filter */
        .bdt-category-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 40px;
        }
        .bdt-category-filter__chip {
            padding: 8px 20px;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 600;
            background: #fff;
            color: #555;
            border: 1.5px solid #e0e0e0;
            text-decoration: none;
            transition: all 0.2s;
        }
        .bdt-category-filter__chip:hover,
        .bdt-category-filter__chip--active {
            background: #2d6a4f;
            color: #fff;
            border-color: #2d6a4f;
        }

        /* Featured Article */
        .bdt-article-featured {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            margin-bottom: 56px;
        }
        .bdt-article-featured__img {
            width: 100%;
            height: 340px;
            object-fit: cover;
        }
        .bdt-article-featured__body { padding: 40px 40px 40px 8px; }
        .bdt-article-featured__tag {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: #e8f5e9;
            color: #2d6a4f;
            margin-bottom: 16px;
        }
        .bdt-article-featured__title {
            font-size: 1.5rem;
            font-weight: 800;
            line-height: 1.3;
            color: #1a1a2e;
            text-decoration: none;
            display: block;
            margin-bottom: 12px;
            transition: color 0.2s;
        }
        .bdt-article-featured__title:hover { color: #2d6a4f; }
        .bdt-article-featured__excerpt {
            font-size: 0.95rem;
            color: #666;
            line-height: 1.7;
            margin-bottom: 20px;
        }
        .bdt-article-featured__meta {
            display: flex;
            gap: 16px;
            font-size: 0.8rem;
            color: #888;
        }
        .bdt-article-featured__meta svg {
            width: 14px; height: 14px;
            vertical-align: middle;
            margin-right: 4px;
        }

        /* Article Grid */
        .bdt-article-grid-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1a1a2e;
            margin: 0 0 28px;
            padding-bottom: 16px;
            border-bottom: 2px solid #e8f5e9;
        }
        .bdt-article-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }
        .bdt-article-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            transition: transform 0.25s, box-shadow 0.25s;
            display: flex;
            flex-direction: column;
        }
        .bdt-article-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        .bdt-article-card__img-wrap {
            overflow: hidden;
            height: 200px;
        }
        .bdt-article-card__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s;
        }
        .bdt-article-card:hover .bdt-article-card__img { transform: scale(1.05); }
        .bdt-article-card__body {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .bdt-article-card__tag {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: #f0fdf4;
            color: #2d6a4f;
            margin-bottom: 10px;
            width: fit-content;
        }
        .bdt-article-card__title {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.4;
            color: #1a1a2e;
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
            transition: color 0.2s;
        }
        .bdt-article-card__title:hover { color: #2d6a4f; }
        .bdt-article-card__excerpt {
            font-size: 0.85rem;
            color: #777;
            line-height: 1.6;
            flex: 1;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .bdt-article-card__meta {
            font-size: 0.75rem;
            color: #aaa;
        }
        .bdt-article-card__meta svg {
            width: 12px; height: 12px;
            vertical-align: middle;
            margin-right: 3px;
        }

        /* Pagination */
        .bdt-pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 48px;
        }
        .bdt-pagination a, .bdt-pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px; height: 40px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            border: 1.5px solid #e0e0e0;
            color: #555;
            transition: all 0.2s;
        }
        .bdt-pagination a:hover { background: #f0fdf4; border-color: #2d6a4f; color: #2d6a4f; }
        .bdt-pagination .active { background: #2d6a4f; border-color: #2d6a4f; color: #fff; }

        /* Empty */
        .bdt-article-empty {
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }
        .bdt-article-empty h3 { font-size: 1.25rem; margin-bottom: 8px; color: #555; }

        @media (max-width: 900px) {
            .bdt-article-featured { grid-template-columns: 1fr; }
            .bdt-article-featured__img { height: 220px; }
            .bdt-article-featured__body { padding: 24px; }
            .bdt-article-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .bdt-article-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<?php include $libPath . '/custom/components/navbar.php'; ?>

<!-- Hero -->
<section class="bdt-article-page-hero">
    <div class="bdt-container">
        <span class="bdt-article-page-hero__eyebrow">Wawasan &amp; Literasi</span>
        <h1 class="bdt-article-page-hero__title">Artikel &amp; Kegiatan</h1>
        <p class="bdt-article-page-hero__desc">
            Temukan artikel literasi, resensi buku, pengumuman kegiatan,
            dan cerita inspiratif dari perpustakaan Desa Teras.
        </p>
    </div>
</section>

<main class="bdt-article-main" id="bdt-article-main">
    <div class="bdt-container">

        <!-- Category Filter -->
        <div class="bdt-category-filter" id="bdt-category-filter" role="navigation" aria-label="Filter kategori">
            <a href="<?= $baseUrl ?>/artikel"
               class="bdt-category-filter__chip <?= $activeCategory === '' ? 'bdt-category-filter__chip--active' : '' ?>"
               id="bdt-filter-semua">Semua</a>
            <?php foreach ($articleCategories as $key => $label) : ?>
            <a href="<?= $baseUrl ?>/artikel?kategori=<?= urlencode($key) ?>"
               class="bdt-category-filter__chip <?= $activeCategory === $key ? 'bdt-category-filter__chip--active' : '' ?>"
               id="bdt-filter-<?= htmlspecialchars($key) ?>">
                <?= htmlspecialchars($label) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Featured (hanya tampil di halaman 1 tanpa filter) -->
        <?php if ($heroArticle && $page === 1 && $activeCategory === '') : ?>
        <article class="bdt-article-featured" id="bdt-article-featured">
            <img src="<?= htmlspecialchars($heroArticle['cover_image'] ?? $baseUrl . '/custom/assets/images/news-featured.png') ?>"
                 alt="<?= htmlspecialchars($heroArticle['title']) ?>"
                 class="bdt-article-featured__img"
                 loading="eager"
                 width="600" height="340">
            <div class="bdt-article-featured__body">
                <span class="bdt-article-featured__tag">
                    <?= htmlspecialchars(ArticleService::CATEGORY_LABELS[$heroArticle['category']] ?? $heroArticle['category']) ?>
                </span>
                <a href="<?= $baseUrl ?>/artikel/<?= htmlspecialchars($heroArticle['slug']) ?>"
                   id="bdt-article-featured-link"
                   class="bdt-article-featured__title">
                    <?= htmlspecialchars($heroArticle['title']) ?>
                </a>
                <p class="bdt-article-featured__excerpt">
                    <?= htmlspecialchars($heroArticle['excerpt'] ?? '') ?>
                </p>
                <div class="bdt-article-featured__meta">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <?= ArticleService::formatDate($heroArticle['publish_date']) ?>
                    </span>
                    <?php if (!empty($heroArticle['author_name'])) : ?>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <?= htmlspecialchars($heroArticle['author_name']) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php endif; ?>

        <!-- Article Grid -->
        <h2 class="bdt-article-grid-title">
            <?= $activeCategory ? htmlspecialchars(ArticleService::CATEGORY_LABELS[$activeCategory]) : 'Semua Artikel' ?>
        </h2>

        <?php if (!empty($articles)) : ?>
        <div class="bdt-article-grid" id="bdt-article-grid">
            <?php foreach ($articles as $i => $art) : ?>
            <article class="bdt-article-card" id="bdt-article-card-<?= $i + 1 ?>">
                <div class="bdt-article-card__img-wrap">
                    <img src="<?= htmlspecialchars($art['cover_image'] ?? $baseUrl . '/custom/assets/images/news-small.png') ?>"
                         alt="<?= htmlspecialchars($art['title']) ?>"
                         class="bdt-article-card__img"
                         loading="lazy"
                         width="400" height="200">
                </div>
                <div class="bdt-article-card__body">
                    <span class="bdt-article-card__tag">
                        <?= htmlspecialchars(ArticleService::CATEGORY_LABELS[$art['category']] ?? $art['category']) ?>
                    </span>
                    <a href="<?= $baseUrl ?>/artikel/<?= htmlspecialchars($art['slug']) ?>"
                       id="bdt-article-card-link-<?= $i + 1 ?>"
                       class="bdt-article-card__title">
                        <?= htmlspecialchars($art['title']) ?>
                    </a>
                    <p class="bdt-article-card__excerpt">
                        <?= htmlspecialchars($art['excerpt'] ?? '') ?>
                    </p>
                    <div class="bdt-article-card__meta">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <?= ArticleService::formatDate($art['publish_date']) ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($maxPage > 1) : ?>
        <nav class="bdt-pagination" aria-label="Navigasi halaman">
            <?php for ($p = 1; $p <= $maxPage; $p++) : ?>
                <?php if ($p === $page) : ?>
                <span class="active" aria-current="page"><?= $p ?></span>
                <?php else : ?>
                <a href="<?= $baseUrl ?>/artikel?halaman=<?= $p ?><?= $activeCategory ? '&kategori=' . urlencode($activeCategory) : '' ?>"
                   id="bdt-page-<?= $p ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>

        <?php else : ?>
        <div class="bdt-article-empty">
            <h3>Belum ada artikel tersedia</h3>
            <p>Artikel akan segera hadir. Silakan kembali lagi nanti.</p>
        </div>
        <?php endif; ?>

    </div>
</main>

<?php include $libPath . '/custom/components/footer.php'; ?>
</body>
</html>
