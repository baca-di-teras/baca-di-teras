<?php
/**
 * Berita & Artikel Page – Baca Di Teras
 *
 * File    : news/berita.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Halaman daftar berita & artikel Desa Teras sesuai desain mockup.
 */

define('BASE_URL', '/baca-di-teras');

$activePage = 'berita';

// Load config data
require_once __DIR__ . '/../../config/news-config.php';

// Filter by category if requested
$selectedCategory = $_GET['category'] ?? 'Semua Cerita';
$searchQuery = $_GET['search'] ?? '';

// Categories list from design
$categories = ['Semua Cerita', 'Teknologi', 'Pendidikan', 'Acara Desa', 'Pertanian', 'Kesehatan'];

// Filter news logic if necessary (mock)
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Temukan berita terbaru, artikel budaya, tips literasi, dan kegiatan komunitas di Desa Teras.">
    <title>Berita & Artikel – Baca Di Teras</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Main styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/landing.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fafbfc;
            color: #101814;
            margin: 0;
        }

        .bdt-news-page-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 48px 24px 96px;
        }

        /* Hero / Header Section */
        .bdt-news-hero {
            margin-bottom: 48px;
        }
        .bdt-news-hero__eyebrow {
            color: #1a6b2f;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 12px;
            display: inline-block;
        }
        .bdt-news-hero__title {
            font-size: 42px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 16px;
            letter-spacing: -0.02em;
        }
        .bdt-news-hero__desc {
            font-size: 16px;
            color: #64748b;
            max-width: 680px;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        /* Filter bar */
        .bdt-news-filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 20px;
            flex-wrap: wrap;
            gap: 16px;
        }
        .bdt-news-filters__pills {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 4px;
        }
        .bdt-news-pill {
            padding: 10px 20px;
            border-radius: 9999px;
            background-color: #f1f5f9;
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .bdt-news-pill:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }
        .bdt-news-pill--active {
            background-color: #1a6b2f;
            color: #ffffff;
        }
        .bdt-news-pill--active:hover {
            background-color: #134e22;
            color: #ffffff;
        }
        .bdt-news-filters__btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .bdt-news-filters__btn:hover {
            background-color: #f8fafc;
        }

        /* Section Layouts */
        .bdt-news-section {
            margin-bottom: 64px;
        }
        .bdt-news-section__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }
        .bdt-news-section__title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.01em;
        }
        .bdt-news-section__arrows {
            display: flex;
            gap: 8px;
        }
        .bdt-arrow-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
        }
        .bdt-arrow-btn:hover {
            border-color: #cbd5e1;
            color: #0f172a;
            background-color: #f8fafc;
        }

        /* Grid: Featured vs Sidebar */
        .bdt-news-grid-split {
            display: grid;
            grid-template-columns: 1.8fr 1.2fr;
            gap: 32px;
        }
        .bdt-news-sidebar-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Articles grid section */
        .bdt-article-section__header-text {
            margin-bottom: 24px;
        }
        .bdt-article-section__subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 4px 0 0;
        }
        .bdt-articles-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 28px;
        }
        /* Custom layout sizes for article cards in grid */
        .bdt-articles-grid__item--wide {
            grid-column: span 2;
        }

        /* CARD COMPONENT CUSTOM CSS OVERRIDES TO MATCH SPECIFIC DESIGN */
        
        /* 1. Featured Card */
        .bdt-news-card-featured {
            background-color: #ffffff;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .bdt-news-card-featured__image-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 16/9;
            overflow: hidden;
        }
        .bdt-news-card-featured__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .bdt-news-card-featured__tag {
            position: absolute;
            top: 16px;
            left: 16px;
            background-color: #1a6b2f;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .bdt-news-card-featured__body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .bdt-news-card-featured__meta {
            display: flex;
            gap: 16px;
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 12px;
            font-weight: 500;
        }
        .bdt-news-card-featured__meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .bdt-news-card-featured__title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 12px;
            line-height: 1.3;
        }
        .bdt-news-card-featured__title a {
            color: inherit;
            text-decoration: none;
        }
        .bdt-news-card-featured__title a:hover {
            color: #1a6b2f;
        }
        .bdt-news-card-featured__excerpt {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
            margin: 0 0 20px;
        }
        .bdt-news-card-featured__link {
            margin-top: auto;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 700;
            color: #1a6b2f;
            text-decoration: none;
            transition: color 0.2s;
        }
        .bdt-news-card-featured__link:hover {
            color: #134e22;
        }

        /* 2. Small Sidebar News Card */
        .bdt-news-card-small {
            display: flex;
            gap: 16px;
            align-items: center;
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            padding: 12px;
            transition: transform 0.2s;
        }
        .bdt-news-card-small:hover {
            transform: translateY(-2px);
        }
        .bdt-news-card-small__image-wrap {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
        }
        .bdt-news-card-small__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .bdt-news-card-small__body {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .bdt-news-card-small__category {
            font-size: 10px;
            font-weight: 700;
            color: #1a6b2f;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .bdt-news-card-small__title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .bdt-news-card-small__title a {
            color: inherit;
            text-decoration: none;
        }
        .bdt-news-card-small__title a:hover {
            color: #1a6b2f;
        }
        .bdt-news-card-small__meta {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
        }

        /* 3. Article Cards */
        .bdt-article-card {
            background-color: #ffffff;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .bdt-article-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
        }
        .bdt-article-card__body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .bdt-article-card__tag {
            align-self: flex-start;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            background-color: #e2e8f0;
            padding: 4px 8px;
            border-radius: 4px;
            margin-bottom: 16px;
            text-transform: uppercase;
        }
        .bdt-article-card__title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 12px;
            line-height: 1.4;
        }
        .bdt-article-card__title a {
            color: inherit;
            text-decoration: none;
        }
        .bdt-article-card__title a:hover {
            color: #1a6b2f;
        }
        .bdt-article-card__excerpt {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
            margin: 0 0 24px;
        }
        .bdt-article-card__footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .bdt-article-card__arrow-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #0f172a;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .bdt-article-card__arrow-btn:hover {
            background-color: #1a6b2f;
        }

        /* Varian Spesifik */
        
        /* Tips Card (Beige background) */
        .bdt-article-card--tips {
            background-color: #faf8f5;
            border-color: #f1ede6;
        }
        .bdt-article-card--tips .bdt-article-card__tag {
            background-color: #f0e6d6;
            color: #8c6d3f;
        }
        .bdt-article-card__author-avatars {
            display: flex;
            align-items: center;
        }
        .bdt-article-card__avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            color: #ffffff;
        }

        /* Hero Card with image background */
        .bdt-article-card--hero {
            background-size: cover;
            background-position: center;
            border: none;
            color: #ffffff;
        }
        .bdt-article-card--hero .bdt-article-card__tag {
            background-color: rgba(255,255,255,0.2);
            color: #ffffff;
        }
        .bdt-article-card--hero .bdt-article-card__title {
            color: #ffffff;
            font-size: 26px;
            font-weight: 800;
            margin-top: 24px;
            line-height: 1.3;
        }
        .bdt-article-card--hero .bdt-article-card__time {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #cbd5e1;
        }
        .bdt-article-card--hero .bdt-article-card__btn {
            background-color: #ffffff;
            color: #0f172a;
            padding: 10px 20px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
        }
        .bdt-article-card--hero .bdt-article-card__btn:hover {
            background-color: #1a6b2f;
            color: #ffffff;
        }

        /* Budaya Card with bottom left author name */
        .bdt-article-card--budaya .bdt-article-card__tag {
            background-color: #f1f5f9;
            color: #475569;
        }
        .bdt-article-card__author-name {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
        }

        /* Action/Link Cards (Klub & Menulis) */
        .bdt-article-card--action {
            background-color: #ffffff;
            border: 1px dashed #cbd5e1;
        }
        .bdt-article-card--action .bdt-article-card__body {
            align-items: center;
            text-align: center;
        }
        .bdt-article-card__icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a6b2f;
            margin-bottom: 20px;
        }
        .bdt-article-card__title--green {
            color: #1a6b2f !important;
        }
        .bdt-article-card__action-link {
            font-size: 14px;
            font-weight: 700;
            color: #1a6b2f;
            text-decoration: none;
            border-bottom: 1px solid #1a6b2f;
            padding-bottom: 2px;
            transition: all 0.2s;
        }
        .bdt-article-card__action-link:hover {
            color: #134e22;
            border-color: #134e22;
        }

        /* Pagination Mock */
        .bdt-news-pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 56px;
        }

        @media (max-width: 980px) {
            .bdt-news-grid-split {
                grid-template-columns: 1fr;
            }
            .bdt-articles-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .bdt-articles-grid__item--wide {
                grid-column: span 1;
            }
        }
        @media (max-width: 640px) {
            .bdt-articles-grid {
                grid-template-columns: 1fr;
            }
            .bdt-news-hero__title {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../components/navbar.php'; ?>

    <div class="bdt-news-page-wrapper">
        
        <!-- Hero / Header -->
        <header class="bdt-news-hero">
            <span class="bdt-news-hero__eyebrow">Wawasan Komunitas</span>
            <h1 class="bdt-news-hero__title">Berita & Artikel Budaya Terbaru</h1>
            <p class="bdt-news-hero__desc">Memberdayakan Desa Teras melalui berbagi pengetahuan, pembaruan komunitas, dan pengembangan literasi digital.</p>
            
            <div class="bdt-news-filters">
                <nav class="bdt-news-filters__pills" aria-label="Filter kategori berita">
                    <?php foreach ($categories as $cat) : 
                        $isActive = $selectedCategory === $cat;
                        $pillClass = $isActive ? 'bdt-news-pill bdt-news-pill--active' : 'bdt-news-pill';
                    ?>
                        <a href="?category=<?= urlencode($cat) ?>" class="<?= $pillClass ?>"><?= htmlspecialchars($cat) ?></a>
                    <?php endforeach; ?>
                </nav>
                
                <button class="bdt-news-filters__btn">
                    <!-- Filter icon (inline SVG) -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
            </div>
        </header>

        <!-- Section: Berita Desa Terbaru -->
        <section class="bdt-news-section" aria-label="Berita Desa Terbaru">
            <div class="bdt-news-section__header">
                <h2 class="bdt-news-section__title">Berita Desa Terbaru</h2>
                <div class="bdt-news-section__arrows">
                    <button class="bdt-arrow-btn" aria-label="Sebelumnya">&lt;</button>
                    <button class="bdt-arrow-btn" aria-label="Berikutnya">&gt;</button>
                </div>
            </div>

            <!-- Grid Split Layout (Featured left, list right) -->
            <div class="bdt-news-grid-split">
                <!-- Left Featured Card -->
                <div>
                    <?php 
                    $newsCardData = $featuredNews;
                    $newsCardData['style'] = 'featured';
                    include __DIR__ . '/../../components/news-card.php';
                    ?>
                </div>

                <!-- Right Sidebar List -->
                <div class="bdt-news-sidebar-list">
                    <?php foreach ($newsList as $item) : 
                        $newsCardData = $item;
                        $newsCardData['style'] = 'small-horizontal';
                        include __DIR__ . '/../../components/news-card.php';
                    endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Section: Artikel Pendidikan -->
        <section class="bdt-news-section" aria-label="Artikel Pendidikan">
            <div class="bdt-news-section__header" style="justify-content: flex-start;">
                <div class="bdt-article-section__header-text">
                    <h2 class="bdt-news-section__title">Artikel Pendidikan</h2>
                    <p class="bdt-article-section__subtitle">Bacaan pilihan untuk pertumbuhan pribadi dan komunal.</p>
                </div>
                <!-- Optional "Lihat Semua" Link on right -->
                <a href="#" class="bdt-recommended-header__link" style="margin-left: auto; font-size: 14px; font-weight: 700;">
                    Lihat Semua Artikel
                </a>
            </div>

            <!-- Grid of multi-styled article cards -->
            <div class="bdt-articles-grid">
                <?php foreach ($articleList as $article) : 
                    $newsCardData = $article;
                    // Automatically add grid span classes for the wide card
                    $gridItemClass = ($article['style'] === 'hero') ? 'bdt-articles-grid__item--wide' : '';
                ?>
                    <div class="<?= $gridItemClass ?>">
                        <?php include __DIR__ . '/../../components/news-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Pagination (Mock/Statis) -->
        <nav class="bdt-news-pagination" aria-label="Navigasi halaman berita">
            <button class="bdt-pagination__item" aria-label="Halaman sebelumnya">&lt;</button>
            <button class="bdt-pagination__item bdt-pagination__item--active">1</button>
            <button class="bdt-pagination__item">2</button>
            <button class="bdt-pagination__item">3</button>
            <span style="display: flex; align-items: flex-end; padding: 0 4px; color: #64748b;">...</span>
            <button class="bdt-pagination__item">12</button>
            <button class="bdt-pagination__item" aria-label="Halaman berikutnya">&gt;</button>
        </nav>

    </div>

    <?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
