<?php
/**
 * Halaman Detail Berita – Baca Di Teras
 *
 * File    : news/detail.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Menampilkan isi lengkap satu berita berdasarkan slug URL.
 * Route: /berita/{slug}  →  $routeParams['slug']
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage = 'berita';
$libPath    = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';

require_once $libPath . '/custom/helpers/Database.php';
require_once $libPath . '/custom/services/ArticleService.php';
require_once $libPath . '/custom/services/NewsService.php';

$newsService    = new NewsService();
$articleService = new ArticleService();

// Ambil slug dari URL dinamis
$slug = $routeParams['slug'] ?? $_GET['slug'] ?? '';

if (!$slug) {
    http_response_code(301);
    header('Location: ' . BASE_URL . '/berita');
    exit;
}

// Ambil data artikel via NewsService (kategori = berita)
$news = $newsService->getNewsBySlug($slug);

if (!$news) {
    http_response_code(404);
    require $libPath . '/custom/pages/404.php';
    exit;
}

// Catat kunjungan
$articleService->recordView((int) $news['article_id']);

// Artikel terkait
$related = $articleService->getRelated(
    (int) $news['article_id'],
    $news['category'],
    (int) ($news['library_id'] ?? 0),
    3
);

$baseUrl      = defined('BASE_URL') ? BASE_URL : '';
$coverImage   = $news['cover_image'] ?? '/custom/assets/images/news-featured.png';
if (strpos($coverImage, '/custom/') === 0 && strpos($coverImage, $baseUrl) !== 0) {
    $coverImage = rtrim($baseUrl, '/') . $coverImage;
}
$publishDate  = ArticleService::formatDate($news['publish_date'] ?? null);
$categoryLabel = ArticleService::CATEGORY_LABELS[$news['category']] ?? $news['category'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($news['meta_description'] ?? $news['excerpt'] ?? '') ?>">
    <title><?= htmlspecialchars($news['title']) ?> – Baca Di Teras</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/custom/assets/css/landing.css">
    <style>
        body { background: #f7f6f5; }

        .bdt-article-page {
            padding: 100px 0 80px;
        }
        .bdt-article-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 48px;
            align-items: start;
        }

        /* Cover Hero */
        .bdt-article-cover {
            width: 100%;
            height: 420px;
            object-fit: cover;
            border-radius: 20px;
            margin-bottom: 32px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        /* Header */
        .bdt-article-header { margin-bottom: 28px; }
        .bdt-article-tag {
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
        .bdt-article-title {
            font-size: clamp(1.75rem, 3vw, 2.5rem);
            font-weight: 800;
            line-height: 1.2;
            color: #1a1a2e;
            margin: 0 0 20px;
        }
        .bdt-article-meta {
            display: flex;
            gap: 20px;
            font-size: 0.85rem;
            color: #888;
            flex-wrap: wrap;
            padding-bottom: 24px;
            border-bottom: 1px solid #eee;
            margin-bottom: 32px;
        }
        .bdt-article-meta svg {
            width: 15px; height: 15px;
            vertical-align: middle;
            margin-right: 5px;
        }

        /* Body */
        .bdt-article-body {
            font-size: 1.05rem;
            line-height: 1.9;
            color: #333;
        }
        .bdt-article-body p { margin: 0 0 1.4em; }
        .bdt-article-body h2 { font-size: 1.4rem; font-weight: 700; color: #1a1a2e; margin: 2em 0 0.8em; }
        .bdt-article-body h3 { font-size: 1.15rem; font-weight: 700; color: #1a1a2e; margin: 1.5em 0 0.6em; }
        .bdt-article-body img { max-width: 100%; border-radius: 12px; margin: 1.5em 0; }
        .bdt-article-body ul, .bdt-article-body ol { padding-left: 1.5em; margin: 1em 0 1.5em; }
        .bdt-article-body li { margin-bottom: 0.5em; }
        .bdt-article-body blockquote {
            border-left: 4px solid #2d6a4f;
            margin: 1.5em 0;
            padding: 12px 20px;
            background: #f0fdf4;
            border-radius: 0 8px 8px 0;
            font-style: italic;
            color: #555;
        }

        /* Tags */
        .bdt-article-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid #eee;
        }
        .bdt-article-tag-item {
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
        .bdt-article-tag-item:hover { background: #e8f5e9; }

        /* Sidebar */
        .bdt-article-sidebar {
            position: sticky;
            top: 100px;
        }
        .bdt-sidebar-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            margin-bottom: 24px;
        }
        .bdt-sidebar-card__title {
            font-size: 1rem;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0 0 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e8f5e9;
        }
        .bdt-sidebar-related-item {
            display: flex;
            gap: 12px;
            align-items: start;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
            text-decoration: none;
            color: inherit;
            transition: background 0.15s;
        }
        .bdt-sidebar-related-item:last-child { border-bottom: none; }
        .bdt-sidebar-related-item:hover .bdt-sidebar-related-item__title { color: #2d6a4f; }
        .bdt-sidebar-related-img {
            width: 72px; height: 52px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .bdt-sidebar-related-item__title {
            font-size: 0.85rem;
            font-weight: 600;
            line-height: 1.4;
            color: #333;
            transition: color 0.2s;
        }
        .bdt-sidebar-related-item__date {
            font-size: 0.72rem;
            color: #aaa;
            margin-top: 4px;
        }

        /* Breadcrumb */
        .bdt-breadcrumb {
            display: flex;
            gap: 8px;
            align-items: center;
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 40px;
        }
        .bdt-breadcrumb a { color: #2d6a4f; text-decoration: none; }
        .bdt-breadcrumb a:hover { text-decoration: underline; }
        .bdt-breadcrumb span { color: #ccc; }

        @media (max-width: 900px) {
            .bdt-article-layout { grid-template-columns: 1fr; }
            .bdt-article-sidebar { position: static; }
        }
    </style>
    <link rel="icon" type="image/png" href="<?= defined('BASE_URL') ? BASE_URL : '/baca-di-teras' ?>/custom/assets/images/logo_header.png">
</head>
<body>
<?php include $libPath . '/custom/components/navbar.php'; ?>

<main class="bdt-article-page">
    <div class="bdt-container">

        <!-- Breadcrumb -->
        <nav class="bdt-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= $baseUrl ?>">Beranda</a>
            <span>/</span>
            <a href="<?= $baseUrl ?>/berita">Berita</a>
            <span>/</span>
            <span><?= htmlspecialchars(mb_strimwidth($news['title'], 0, 50, '...')) ?></span>
        </nav>

        <div class="bdt-article-layout">

            <!-- Main Content -->
            <article id="bdt-news-article">
                <img src="<?= htmlspecialchars($coverImage) ?>"
                     alt="<?= htmlspecialchars($news['title']) ?>"
                     class="bdt-article-cover"
                     width="800" height="420"
                     loading="eager">

                <header class="bdt-article-header">
                    <span class="bdt-article-tag"><?= htmlspecialchars($categoryLabel) ?></span>
                    <h1 class="bdt-article-title"><?= htmlspecialchars($news['title']) ?></h1>

                    <div class="bdt-article-meta">
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
                        <?php if (!empty($news['author']) || !empty($news['author_name'])) : ?>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <?= htmlspecialchars($news['author'] ?? $news['author_name'] ?? '') ?>
                        </span>
                        <?php endif; ?>
                        <?php if (!empty($news['library_name'])) : ?>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                            </svg>
                            <?= htmlspecialchars($news['library_name']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </header>

                <div class="bdt-article-body" id="bdt-news-body">
                    <?= $news['body'] ?? '' ?>
                </div>

                <!-- Tags -->
                <?php if (!empty($news['tags'])) : ?>
                <div class="bdt-article-tags" id="bdt-news-tags">
                    <?php foreach ($news['tags'] as $tag) : ?>
                    <span class="bdt-article-tag-item">#<?= htmlspecialchars($tag) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </article>

            <!-- Sidebar -->
            <aside class="bdt-article-sidebar" id="bdt-news-sidebar">
                <?php if (!empty($related)) : ?>
                <div class="bdt-sidebar-card">
                    <h2 class="bdt-sidebar-card__title">Berita Terkait</h2>
                    <?php foreach ($related as $ri => $rel) : ?>
                    <a href="<?= $baseUrl ?>/berita/<?= htmlspecialchars($rel['slug']) ?>"
                       class="bdt-sidebar-related-item"
                       id="bdt-related-news-<?= $ri + 1 ?>">
                        <?php 
                            $relImg = $rel['cover_image'] ?? '/custom/assets/images/news-small.png';
                            if (strpos($relImg, '/custom/') === 0 && strpos($relImg, $baseUrl) !== 0) {
                                $relImg = rtrim($baseUrl, '/') . $relImg;
                            }
                        ?>
                        <img src="<?= htmlspecialchars($relImg) ?>"
                             alt="<?= htmlspecialchars($rel['title']) ?>"
                             class="bdt-sidebar-related-img"
                             loading="lazy"
                             width="72" height="52">
                        <div>
                            <div class="bdt-sidebar-related-item__title">
                                <?= htmlspecialchars(mb_strimwidth($rel['title'], 0, 70, '...')) ?>
                            </div>
                            <div class="bdt-sidebar-related-item__date">
                                <?= ArticleService::formatDate($rel['publish_date']) ?>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Back to list -->
                <div class="bdt-sidebar-card" style="text-align:center;">
                    <a href="<?= $baseUrl ?>/berita"
                       id="bdt-back-to-news"
                       class="bdt-btn bdt-btn--primary"
                       style="display:inline-block; width:100%; text-align:center; padding: 14px 20px;">
                        ← Semua Berita
                    </a>
                </div>
            </aside>

        </div>
    </div>
</main>

<?php include $libPath . '/custom/components/footer.php'; ?>
</body>
</html>
