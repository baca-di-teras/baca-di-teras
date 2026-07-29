<?php
/**
 * Halaman Detail Artikel – Baca Di Teras
 *
 * File    : article/detail.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Menampilkan isi lengkap satu artikel berdasarkan slug URL.
 * Route: /artikel/{slug}  →  $routeParams['slug']
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage = 'artikel';
$libPath    = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';

require_once $libPath . '/custom/helpers/Database.php';
require_once $libPath . '/custom/services/ArticleService.php';

$articleService = new ArticleService();

// Ambil slug dari route dinamis
$slug = $routeParams['slug'] ?? $_GET['slug'] ?? '';

if (!$slug) {
    http_response_code(301);
    header('Location: ' . BASE_URL . '/artikel');
    exit;
}

// Ambil data artikel
$article = $articleService->getBySlug($slug);

if (!$article) {
    http_response_code(404);
    require $libPath . '/custom/pages/404.php';
    exit;
}

// Catat kunjungan
$articleService->recordView((int) $article['article_id']);

// Artikel terkait
$related = $articleService->getRelated(
    (int) $article['article_id'],
    $article['category'],
    (int) ($article['library_id'] ?? 0),
    3
);

// Artikel populer untuk sidebar
$popular = $articleService->getPopular(5, 30);

$baseUrl       = defined('BASE_URL') ? BASE_URL : '';
$coverImage    = $article['cover_image'] ?? '/custom/assets/images/news-featured.png';
if (strpos($coverImage, '/custom/') === 0 && strpos($coverImage, $baseUrl) !== 0) {
    $coverImage = rtrim($baseUrl, '/') . $coverImage;
}
$publishDate   = ArticleService::formatDate($article['publish_date'] ?? null);
$categoryLabel = ArticleService::CATEGORY_LABELS[$article['category']] ?? $article['category'];
$tags          = $article['tags'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($article['meta_description'] ?? $article['excerpt'] ?? '') ?>">
    <?php if (!empty($article['meta_keywords'])) : ?>
    <meta name="keywords" content="<?= htmlspecialchars($article['meta_keywords']) ?>">
    <?php endif; ?>
    <title><?= htmlspecialchars($article['title']) ?> – Baca Di Teras</title>

    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($article['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($article['excerpt'] ?? '') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($coverImage) ?>">
    <meta property="og:type" content="article">

    <link rel="stylesheet" href="<?= $baseUrl ?>/custom/assets/css/landing.css">
    <style>
        body { background: #f7f6f5; }

        .bdt-art-page { padding: 100px 0 80px; }
        .bdt-art-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 48px;
            align-items: start;
        }

        /* Breadcrumb */
        .bdt-breadcrumb {
            display: flex;
            gap: 8px;
            align-items: center;
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        .bdt-breadcrumb a { color: #2d6a4f; text-decoration: none; }
        .bdt-breadcrumb a:hover { text-decoration: underline; }
        .bdt-breadcrumb span { color: #ccc; }

        /* Cover */
        .bdt-art-cover {
            width: 100%;
            height: 420px;
            object-fit: cover;
            border-radius: 20px;
            margin-bottom: 32px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        /* Header */
        .bdt-art-tag {
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
        .bdt-art-title {
            font-size: clamp(1.75rem, 3vw, 2.5rem);
            font-weight: 800;
            line-height: 1.2;
            color: #1a1a2e;
            margin: 0 0 20px;
        }
        .bdt-art-meta {
            display: flex;
            gap: 20px;
            font-size: 0.85rem;
            color: #888;
            flex-wrap: wrap;
            padding-bottom: 24px;
            border-bottom: 1px solid #eee;
            margin-bottom: 32px;
            align-items: center;
        }
        .bdt-art-meta svg {
            width: 15px; height: 15px;
            vertical-align: middle;
            margin-right: 5px;
        }

        /* Body */
        .bdt-art-body {
            font-size: 1.05rem;
            line-height: 1.9;
            color: #333;
        }
        .bdt-art-body p { margin: 0 0 1.4em; }
        .bdt-art-body h2 { font-size: 1.4rem; font-weight: 700; color: #1a1a2e; margin: 2em 0 0.8em; }
        .bdt-art-body h3 { font-size: 1.15rem; font-weight: 700; color: #1a1a2e; margin: 1.5em 0 0.6em; }
        .bdt-art-body img { max-width: 100%; border-radius: 12px; margin: 1.5em 0; }
        .bdt-art-body ul, .bdt-art-body ol { padding-left: 1.5em; margin: 1em 0 1.5em; }
        .bdt-art-body li { margin-bottom: 0.5em; }
        .bdt-art-body blockquote {
            border-left: 4px solid #2d6a4f;
            margin: 1.5em 0;
            padding: 12px 20px;
            background: #f0fdf4;
            border-radius: 0 8px 8px 0;
            font-style: italic;
            color: #555;
        }

        /* Tags */
        .bdt-art-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid #eee;
        }
        .bdt-art-tag-item {
            padding: 4px 14px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #f0fdf4;
            color: #2d6a4f;
            border: 1px solid #c8e6c9;
            text-decoration: none;
            transition: background 0.2s;
        }
        .bdt-art-tag-item:hover { background: #e8f5e9; }

        /* Library info badge */
        .bdt-art-library-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: #f0fdf4;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #2d6a4f;
            margin-top: 12px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .bdt-art-library-badge:hover { background: #e8f5e9; }
        .bdt-art-library-badge svg { width: 14px; height: 14px; }

        /* Sidebar */
        .bdt-art-sidebar { position: sticky; top: 100px; }
        .bdt-sidebar-box {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            margin-bottom: 24px;
        }
        .bdt-sidebar-box__title {
            font-size: 1rem;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0 0 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e8f5e9;
        }
        .bdt-sidebar-item {
            display: flex;
            gap: 12px;
            align-items: start;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
            text-decoration: none;
            color: inherit;
        }
        .bdt-sidebar-item:last-child { border-bottom: none; }
        .bdt-sidebar-item:hover .bdt-sidebar-item__title { color: #2d6a4f; }
        .bdt-sidebar-item__img {
            width: 72px; height: 52px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .bdt-sidebar-item__title {
            font-size: 0.85rem;
            font-weight: 600;
            line-height: 1.4;
            color: #333;
            transition: color 0.2s;
        }
        .bdt-sidebar-item__sub {
            font-size: 0.72rem;
            color: #aaa;
            margin-top: 4px;
        }

        @media (max-width: 900px) {
            .bdt-art-layout { grid-template-columns: 1fr; }
            .bdt-art-sidebar { position: static; }
            .bdt-art-cover { height: 260px; }
        }
    </style>
    <link rel="icon" type="image/png" href="<?= defined('BASE_URL') ? BASE_URL : '/baca-di-teras' ?>/custom/assets/images/logo_header.png">
</head>
<body>
<?php include $libPath . '/custom/components/navbar.php'; ?>

<main class="bdt-art-page">
    <div class="bdt-container">

        <!-- Breadcrumb -->
        <nav class="bdt-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= $baseUrl ?>">Beranda</a>
            <span>/</span>
            <a href="<?= $baseUrl ?>/artikel">Artikel</a>
            <?php if ($article['category']) : ?>
            <span>/</span>
            <a href="<?= $baseUrl ?>/artikel?kategori=<?= urlencode($article['category']) ?>">
                <?= htmlspecialchars($categoryLabel) ?>
            </a>
            <?php endif; ?>
            <span>/</span>
            <span><?= htmlspecialchars(mb_strimwidth($article['title'], 0, 50, '...')) ?></span>
        </nav>

        <div class="bdt-art-layout">

            <!-- Main Article -->
            <article id="bdt-article-detail">
                <img src="<?= htmlspecialchars($coverImage) ?>"
                     alt="<?= htmlspecialchars($article['title']) ?>"
                     class="bdt-art-cover"
                     width="800" height="420"
                     loading="eager">

                <header>
                    <span class="bdt-art-tag"><?= htmlspecialchars($categoryLabel) ?></span>
                    <h1 class="bdt-art-title"><?= htmlspecialchars($article['title']) ?></h1>

                    <div class="bdt-art-meta">
                        <?php if ($publishDate) : ?>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <?= htmlspecialchars($publishDate) ?>
                        </span>
                        <?php endif; ?>
                        <?php if (!empty($article['author_name'])) : ?>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <?= htmlspecialchars($article['author_name']) ?>
                        </span>
                        <?php endif; ?>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <?= number_format((int)($article['view_count'] ?? 0)) ?> dibaca
                        </span>
                    </div>
                </header>

                <!-- Konten artikel -->
                <div class="bdt-art-body" id="bdt-article-body">
                    <?= $article['body'] ?? '' ?>
                </div>

                <!-- Library badge -->
                <?php if (!empty($article['library_name'])) : ?>
                <a href="<?= $baseUrl ?>/perpustakaan/<?= htmlspecialchars($article['library_slug'] ?? '') ?>"
                   class="bdt-art-library-badge"
                   id="bdt-article-library-link">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                    <?= htmlspecialchars($article['library_name']) ?>
                </a>
                <?php endif; ?>

                <!-- Tags -->
                <?php if (!empty($tags)) : ?>
                <div class="bdt-art-tags" id="bdt-article-tags">
                    <?php foreach ($tags as $tag) : ?>
                    <span class="bdt-art-tag-item">#<?= htmlspecialchars($tag) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </article>

            <!-- Sidebar -->
            <aside class="bdt-art-sidebar" id="bdt-article-sidebar">

                <!-- Artikel Terkait -->
                <?php if (!empty($related)) : ?>
                <div class="bdt-sidebar-box">
                    <h2 class="bdt-sidebar-box__title">Artikel Terkait</h2>
                    <?php foreach ($related as $ri => $rel) : ?>
                    <a href="<?= $baseUrl ?>/artikel/<?= htmlspecialchars($rel['slug']) ?>"
                       class="bdt-sidebar-item"
                       id="bdt-related-article-<?= $ri + 1 ?>">
                        <?php 
                            $relImg = $rel['cover_image'] ?? '/custom/assets/images/news-small.png';
                            if (strpos($relImg, '/custom/') === 0 && strpos($relImg, $baseUrl) !== 0) {
                                $relImg = rtrim($baseUrl, '/') . $relImg;
                            }
                        ?>
                        <img src="<?= htmlspecialchars($relImg) ?>"
                             alt="<?= htmlspecialchars($rel['title']) ?>"
                             class="bdt-sidebar-item__img"
                             loading="lazy"
                             width="72" height="52">
                        <div>
                            <div class="bdt-sidebar-item__title">
                                <?= htmlspecialchars(mb_strimwidth($rel['title'], 0, 70, '...')) ?>
                            </div>
                            <div class="bdt-sidebar-item__sub">
                                <?= ArticleService::formatDate($rel['publish_date']) ?>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Artikel Populer -->
                <?php if (!empty($popular)) : ?>
                <div class="bdt-sidebar-box">
                    <h2 class="bdt-sidebar-box__title">Artikel Populer</h2>
                    <?php foreach ($popular as $pi => $pop) : ?>
                    <a href="<?= $baseUrl ?>/artikel/<?= htmlspecialchars($pop['slug']) ?>"
                       class="bdt-sidebar-item"
                       id="bdt-popular-article-<?= $pi + 1 ?>">
                        <?php 
                            $popImg = $pop['cover_image'] ?? '/custom/assets/images/news-small.png';
                            if (strpos($popImg, '/custom/') === 0 && strpos($popImg, $baseUrl) !== 0) {
                                $popImg = rtrim($baseUrl, '/') . $popImg;
                            }
                        ?>
                        <img src="<?= htmlspecialchars($popImg) ?>"
                             alt="<?= htmlspecialchars($pop['title']) ?>"
                             class="bdt-sidebar-item__img"
                             loading="lazy"
                             width="72" height="52">
                        <div>
                            <div class="bdt-sidebar-item__title">
                                <?= htmlspecialchars(mb_strimwidth($pop['title'], 0, 70, '...')) ?>
                            </div>
                            <div class="bdt-sidebar-item__sub">
                                <?= number_format((int)($pop['total_views'] ?? 0)) ?> kali dibaca
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Back to list -->
                <div class="bdt-sidebar-box" style="text-align:center;">
                    <a href="<?= $baseUrl ?>/artikel"
                       id="bdt-back-to-article"
                       class="bdt-btn bdt-btn--primary"
                       style="display:inline-block; width:100%; text-align:center; padding: 14px 20px;">
                        ← Semua Artikel
                    </a>
                </div>

            </aside>

        </div>
    </div>
</main>

<?php include $libPath . '/custom/components/footer.php'; ?>
</body>
</html>
