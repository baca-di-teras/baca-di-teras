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
    
    <?php
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $absoluteCoverImage = strpos($coverImage, 'http') === 0 ? $coverImage : $protocol . '://' . $host . $coverImage;
        $absoluteUrl = $protocol . '://' . $host . $_SERVER['REQUEST_URI'];
    ?>
    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($news['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($news['meta_description'] ?? $news['excerpt'] ?? '') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($absoluteCoverImage) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($absoluteUrl) ?>">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="Baca Di Teras">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($news['title']) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($news['meta_description'] ?? $news['excerpt'] ?? '') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($absoluteCoverImage) ?>">
    <link rel="stylesheet" href="<?= $baseUrl ?>/custom/assets/css/landing.css">
    <style>
        body { background: #f7f6f5; }

        /* Lightbox */
        .bdt-lightbox {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.85); z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
        }
        .bdt-lightbox.show { opacity: 1; pointer-events: auto; }
        .bdt-lightbox img {
            max-width: 90%; max-height: 90vh; border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            transform: scale(0.9); transition: transform 0.3s ease;
        }
        .bdt-lightbox.show img { transform: scale(1); }
        .bdt-art-body img, .bdt-article-body img, .bdt-news-body img, .bdt-article-cover { cursor: zoom-in; }

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

        /* Slider */
        .bdt-slider-container {
            position: relative;
            width: 100%;
            height: 420px;
            margin-bottom: 32px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            background: #000;
        }
        .bdt-slider {
            display: flex;
            width: 100%;
            height: 100%;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .bdt-slider::-webkit-scrollbar { display: none; }
        .bdt-slider-item {
            flex: 0 0 100%;
            width: 100%;
            height: 100%;
            object-fit: contain; /* Changed to contain to show full image without cropping */
            background: #000; /* Added background */
            scroll-snap-align: center;
            border-radius: 0;
            margin-bottom: 0;
            box-shadow: none;
        }
        .bdt-slider-nav {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
            padding: 0 16px;
            pointer-events: none;
            z-index: 10;
        }
        .bdt-slider-btn {
            background: rgba(255, 255, 255, 0.7);
            color: #1a1a2e;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            pointer-events: auto;
            backdrop-filter: blur(4px);
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .bdt-slider-btn:hover { background: rgba(255, 255, 255, 0.95); transform: scale(1.05); }
        .bdt-slider-dots {
            position: absolute;
            bottom: 16px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 8px;
            pointer-events: none;
            z-index: 10;
        }
        .bdt-slider-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transition: background 0.2s, transform 0.2s;
            cursor: pointer;
            pointer-events: auto;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .bdt-slider-dot.active {
            background: #fff;
            transform: scale(1.3);
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
        /* Quill Alignment Support */
        .bdt-article-body .ql-align-center { text-align: center; }
        .bdt-article-body .ql-align-right { text-align: right; }
        .bdt-article-body .ql-align-justify { text-align: justify; }
        
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
        /* Share & Lightbox */
        .bdt-share-buttons { display: inline-flex; gap: 8px; align-items: center; margin-left: 8px; padding-left: 16px; border-left: 1px solid #ddd; }
        .bdt-share-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%; background: #f0fdf4; color: #2d6a4f; text-decoration: none; transition: all 0.2s; }
        .bdt-share-btn:hover { background: #2d6a4f; color: #fff; }
        .bdt-share-btn svg { width: 14px !important; height: 14px !important; margin: 0 !important; }

        .bdt-lightbox { display: none; position: fixed; z-index: 9999; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); align-items: center; justify-content: center; cursor: pointer; backdrop-filter: blur(4px); opacity: 0; transition: opacity 0.3s; }
        .bdt-lightbox.show { display: flex; opacity: 1; }
        .bdt-lightbox img { max-width: 90%; max-height: 90vh; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.5); object-fit: contain; transform: scale(0.95); transition: transform 0.3s; }
        .bdt-lightbox.show img { transform: scale(1); }
        .bdt-article-body img { cursor: zoom-in; transition: opacity 0.2s; }
        .bdt-article-body img:hover { opacity: 0.9; }

        @media (max-width: 600px) {
            .bdt-article-meta { gap: 12px; }
            .bdt-share-buttons { margin-left: 0; padding-left: 0; border-left: none; width: 100%; margin-top: 8px; padding-top: 12px; border-top: 1px dashed #eee; }
            
            /* Responsive adjustments for mobile view */
            .bdt-article-cover { height: 220px; border-radius: 12px; }
            .bdt-article-title { font-size: 1.5rem; }
            .bdt-article-body { font-size: 0.95rem; line-height: 1.7; }
        }
    </style>
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
                <?php 
                    $additionalImages = !empty($news['additional_images']) ? json_decode($news['additional_images'], true) : [];
                    if (!empty($additionalImages) && is_array($additionalImages)): 
                        $allSliderImages = array_merge([$coverImage], $additionalImages);
                ?>
                    <div class="bdt-slider-container">
                        <div class="bdt-slider">
                            <?php foreach($allSliderImages as $index => $img): ?>
                                <?php 
                                    $imgUrl = $img;
                                    if (strpos($imgUrl, '/custom/') === 0 && strpos($imgUrl, $baseUrl) !== 0) {
                                        $imgUrl = rtrim($baseUrl, '/') . $imgUrl;
                                    }
                                ?>
                                <img src="<?= htmlspecialchars($imgUrl) ?>"
                                     alt="<?= htmlspecialchars($news['title']) ?> - Foto <?= $index + 1 ?>"
                                     class="bdt-article-cover bdt-slider-item"
                                     loading="<?= $index === 0 ? 'eager' : 'lazy' ?>">
                            <?php endforeach; ?>
                        </div>
                        <div class="bdt-slider-nav">
                            <button class="bdt-slider-btn prev" aria-label="Previous image">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button class="bdt-slider-btn next" aria-label="Next image">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                        <div class="bdt-slider-dots">
                            <?php foreach($allSliderImages as $index => $img): ?>
                                <span class="bdt-slider-dot <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>"></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <img src="<?= htmlspecialchars($coverImage) ?>"
                         alt="<?= htmlspecialchars($news['title']) ?>"
                         class="bdt-article-cover"
                         width="800" height="420"
                         loading="eager">
                <?php endif; ?>

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
                        <span style="display:inline-flex; align-items:center;">
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
                        
                        <?php 
                            $shareUrl = urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                            $shareTitle = urlencode($news['title']);
                        ?>
                        <span style="display:inline-flex; align-items:center; margin-left: auto;">
                            <div class="bdt-share-buttons" style="margin-left: 0; padding-left: 0; border-left: none;">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" class="bdt-share-btn" title="Bagikan ke Facebook">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/></svg>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>" target="_blank" class="bdt-share-btn" title="Bagikan ke X/Twitter">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"><path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/></svg>
                                </a>
                                <a href="https://wa.me/?text=<?= $shareTitle ?>%20<?= $shareUrl ?>" target="_blank" class="bdt-share-btn" title="Bagikan ke WhatsApp">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.49.652.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/></svg>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $shareUrl ?>" target="_blank" class="bdt-share-btn" title="Bagikan ke LinkedIn">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"><path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/></svg>
                                </a>
                                <a href="https://t.me/share/url?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>" target="_blank" class="bdt-share-btn" title="Bagikan ke Telegram">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.287 5.906c-.778.324-2.334.994-4.666 2.01-.378.15-.577.298-.595.442-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.297.26.008.546-.104.859-.336.228-.167.536-.396.887-.67.411-.32.61-.482.68-.485.025-.001.058.001.096.017.039.016.059.043.061.082.003.048-.027.108-.09.18l-.946.883c-.332.311-.53.496-.587.548-.13.118-.266.242-.137.409.135.176.326.315.52.46.208.156.425.319.664.502.483.37.9.69 1.348.868.21.085.405.127.589.117.202-.012.385-.145.47-.417.155-.494.516-2.02.723-3.14.07-.377.13-.733.167-1.01.018-.133.023-.23.016-.289a.185.185 0 0 0-.113-.135c-.104-.038-.283-.02-.562.083l-3.327 1.34z"/></svg>
                                </a>
                                <button onclick="navigator.clipboard.writeText('<?= urldecode($shareUrl) ?>'); alert('Tautan berhasil disalin!');" class="bdt-share-btn" title="Salin Tautan" style="border:none; cursor:pointer;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/><path d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/></svg>
                                </button>
                            </div>
                        </span>
                    </div>
                </header>

                <div class="bdt-article-body" id="bdt-news-body">
                    <?php 
                        $bodyHtml = $news['body'] ?? '';
                        if (strip_tags($bodyHtml) === $bodyHtml) {
                            echo nl2br(htmlspecialchars($bodyHtml));
                        } else {
                            echo $bodyHtml;
                        }
                    ?>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lightbox = document.createElement('div');
    lightbox.className = 'bdt-lightbox';
    const imgNode = document.createElement('img');
    lightbox.appendChild(imgNode);
    document.body.appendChild(lightbox);

    document.addEventListener('click', function(e) {
        if (e.target.tagName === 'IMG' && (e.target.closest('.bdt-art-body') || e.target.closest('.bdt-article-body') || e.target.closest('.bdt-news-body') || e.target.classList.contains('bdt-article-cover') || e.target.classList.contains('bdt-art-cover'))) {
            imgNode.src = e.target.src;
            lightbox.classList.add('show');
        } else if (e.target.closest('.bdt-lightbox')) {
            lightbox.classList.remove('show');
            setTimeout(() => imgNode.src = '', 300); // clear after transition
        }
    });
    // Slider Logic
    const sliderContainer = document.querySelector('.bdt-slider-container');
    if (sliderContainer) {
        const slider = sliderContainer.querySelector('.bdt-slider');
        const prevBtn = sliderContainer.querySelector('.prev');
        const nextBtn = sliderContainer.querySelector('.next');
        const dots = sliderContainer.querySelectorAll('.bdt-slider-dot');
        let isDown = false;
        let startX;
        let scrollLeft;
        let autoSlideInterval;

        const updateDots = () => {
            const index = Math.round(slider.scrollLeft / slider.clientWidth);
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
        };

        const slideNext = () => {
            let newScrollLeft = slider.scrollLeft + slider.clientWidth;
            if (newScrollLeft >= slider.scrollWidth - 10) { // Allow for some pixel rounding
                newScrollLeft = 0;
            }
            slider.scrollTo({ left: newScrollLeft, behavior: 'smooth' });
        };

        const slidePrev = () => {
            let newScrollLeft = slider.scrollLeft - slider.clientWidth;
            if (newScrollLeft < 0) {
                newScrollLeft = slider.scrollWidth - slider.clientWidth;
            }
            slider.scrollTo({ left: newScrollLeft, behavior: 'smooth' });
        };

        const startAutoSlide = () => {
            stopAutoSlide(); // Ensure we don't have multiple intervals
            autoSlideInterval = setInterval(slideNext, 3500); // 3.5 seconds
        };

        const stopAutoSlide = () => {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
            }
        };

        slider.addEventListener('scroll', () => {
            requestAnimationFrame(updateDots);
        });

        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            slidePrev();
            stopAutoSlide();
            startAutoSlide(); // reset timer
        });

        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            slideNext();
            stopAutoSlide();
            startAutoSlide(); // reset timer
        });

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                slider.scrollTo({ left: slider.clientWidth * index, behavior: 'smooth' });
                stopAutoSlide();
                startAutoSlide();
            });
        });

        // Drag to scroll
        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
            stopAutoSlide();
        });
        slider.addEventListener('mouseleave', () => {
            isDown = false;
            startAutoSlide();
        });
        slider.addEventListener('mouseup', () => {
            isDown = false;
            startAutoSlide();
        });
        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 2;
            slider.scrollLeft = scrollLeft - walk;
        });

        sliderContainer.addEventListener('touchstart', stopAutoSlide);
        sliderContainer.addEventListener('touchend', startAutoSlide);

        // Start auto slide initially
        startAutoSlide();
    }
});
</script>
</body>
</html>

