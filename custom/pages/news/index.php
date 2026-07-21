<?php
/**
 * Halaman Daftar Berita – Baca Di Teras
 *
 * File    : news/index.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Menampilkan daftar seluruh berita dari tabel bdt_article (category = berita).
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage = 'berita';

// ── Service Layer ──────────────────────────────────────────────
$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/helpers/Database.php';
require_once $libPath . '/custom/services/NewsService.php';
require_once $libPath . '/custom/services/ArticleService.php';

$newsService = new NewsService();

$featuredNews = $newsService->getFeaturedNews();
$newsList     = $newsService->getRecentNews(9, 0);
$baseUrl      = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Baca berita terbaru seputar kegiatan literasi, program perpustakaan, dan perkembangan Desa Teras Boyolali.">
    <meta name="keywords" content="berita desa teras, literasi, perpustakaan, kegiatan boyolali">
    <title>Berita – Baca Di Teras</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/custom/assets/css/landing.css">
    <style>
        body { background: #f7f6f5; }

        .bdt-news-page {
            padding: 120px 0 80px;
        }
        .bdt-news-page__hero {
            background: linear-gradient(135deg, #1a3c5e 0%, #2d6a4f 100%);
            padding: 80px 0 60px;
            text-align: center;
            color: #fff;
        }
        .bdt-news-page__hero-eyebrow {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.7);
            margin-bottom: 16px;
        }
        .bdt-news-page__hero-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            margin: 0 0 16px;
            line-height: 1.15;
        }
        .bdt-news-page__hero-desc {
            font-size: 1.125rem;
            color: rgba(255,255,255,0.8);
            max-width: 560px;
            margin: 0 auto;
            line-height: 1.7;
        }

        .bdt-news-main {
            padding: 64px 0 80px;
        }

        /* Featured */
        .bdt-news-featured {
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
        .bdt-news-featured__img {
            width: 100%;
            height: 340px;
            object-fit: cover;
        }
        .bdt-news-featured__body {
            padding: 40px 40px 40px 8px;
        }
        .bdt-news-featured__tag {
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
        .bdt-news-featured__title {
            font-size: 1.5rem;
            font-weight: 800;
            line-height: 1.3;
            color: #1a1a2e;
            text-decoration: none;
            display: block;
            margin-bottom: 12px;
            transition: color 0.2s;
        }
        .bdt-news-featured__title:hover { color: #2d6a4f; }
        .bdt-news-featured__excerpt {
            font-size: 0.95rem;
            color: #666;
            line-height: 1.7;
            margin-bottom: 20px;
        }
        .bdt-news-featured__meta {
            display: flex;
            gap: 16px;
            font-size: 0.8rem;
            color: #888;
            align-items: center;
        }
        .bdt-news-featured__meta svg {
            width: 14px; height: 14px;
            vertical-align: middle;
            margin-right: 4px;
        }

        /* News Grid */
        .bdt-news-section-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1a1a2e;
            margin: 0 0 32px;
            padding-bottom: 16px;
            border-bottom: 2px solid #e8f5e9;
        }
        .bdt-news-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .bdt-news-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            transition: transform 0.25s, box-shadow 0.25s;
            display: flex;
            flex-direction: column;
        }
        .bdt-news-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        .bdt-news-card__img-wrap {
            overflow: hidden;
            height: 200px;
        }
        .bdt-news-card__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s;
        }
        .bdt-news-card:hover .bdt-news-card__img { transform: scale(1.05); }
        .bdt-news-card__body {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .bdt-news-card__tag {
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
        .bdt-news-card__title {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.4;
            color: #1a1a2e;
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
            transition: color 0.2s;
        }
        .bdt-news-card__title:hover { color: #2d6a4f; }
        .bdt-news-card__excerpt {
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
        .bdt-news-card__meta {
            font-size: 0.75rem;
            color: #aaa;
            display: flex;
            gap: 12px;
        }
        .bdt-news-card__meta svg {
            width: 12px; height: 12px;
            vertical-align: middle;
            margin-right: 3px;
        }

        /* Empty state */
        .bdt-news-empty {
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }
        .bdt-news-empty h3 { font-size: 1.25rem; margin-bottom: 8px; color: #555; }

        @media (max-width: 900px) {
            .bdt-news-featured { grid-template-columns: 1fr; }
            .bdt-news-featured__img { height: 220px; }
            .bdt-news-featured__body { padding: 24px; }
            .bdt-news-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .bdt-news-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<?php include $libPath . '/custom/components/navbar.php'; ?>

<!-- Hero -->
<section class="bdt-news-page__hero">
    <div class="bdt-container">
        <span class="bdt-news-page__hero-eyebrow">Informasi &amp; Kabar</span>
        <h1 class="bdt-news-page__hero-title">Berita Terkini</h1>
        <p class="bdt-news-page__hero-desc">
            Ikuti perkembangan terbaru seputar kegiatan literasi,
            program perpustakaan, dan dinamika Desa Teras.
        </p>
    </div>
</section>

<!-- Main Content -->
<main class="bdt-news-main" id="bdt-news-main">
    <div class="bdt-container">

        <?php if ($featuredNews) : ?>
        <!-- Featured News -->
        <article class="bdt-news-featured" id="bdt-news-featured">
            <img src="<?= htmlspecialchars($featuredNews['image'] ?? $baseUrl . '/custom/assets/images/news-featured.png') ?>"
                 alt="<?= htmlspecialchars($featuredNews['title']) ?>"
                 class="bdt-news-featured__img"
                 loading="eager"
                 width="600" height="340">
            <div class="bdt-news-featured__body">
                <span class="bdt-news-featured__tag">
                    <?= htmlspecialchars(ArticleService::CATEGORY_LABELS[$featuredNews['category']] ?? $featuredNews['category']) ?>
                </span>
                <a href="<?= $baseUrl ?>/berita/<?= htmlspecialchars($featuredNews['slug'] ?? '') ?>"
                   id="bdt-news-featured-link"
                   class="bdt-news-featured__title">
                    <?= htmlspecialchars($featuredNews['title']) ?>
                </a>
                <p class="bdt-news-featured__excerpt">
                    <?= htmlspecialchars($featuredNews['excerpt'] ?? '') ?>
                </p>
                <div class="bdt-news-featured__meta">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <?= ArticleService::formatDate($featuredNews['date'] ?? $featuredNews['publish_date'] ?? null) ?>
                    </span>
                    <?php if (!empty($featuredNews['author'])) : ?>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <?= htmlspecialchars($featuredNews['author']) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php endif; ?>

        <!-- News Grid -->
        <h2 class="bdt-news-section-title">Semua Berita</h2>

        <?php if (!empty($newsList)) : ?>
        <div class="bdt-news-grid" id="bdt-news-grid">
            <?php foreach ($newsList as $i => $item) : ?>
            <article class="bdt-news-card" id="bdt-news-card-<?= $i + 1 ?>">
                <div class="bdt-news-card__img-wrap">
                    <img src="<?= htmlspecialchars($item['image'] ?? $baseUrl . '/custom/assets/images/news-small.png') ?>"
                         alt="<?= htmlspecialchars($item['title']) ?>"
                         class="bdt-news-card__img"
                         loading="lazy"
                         width="400" height="200">
                </div>
                <div class="bdt-news-card__body">
                    <span class="bdt-news-card__tag">
                        <?= htmlspecialchars(ArticleService::CATEGORY_LABELS[$item['category']] ?? $item['category']) ?>
                    </span>
                    <a href="<?= $baseUrl ?>/berita/<?= htmlspecialchars($item['slug'] ?? '') ?>"
                       id="bdt-news-card-link-<?= $i + 1 ?>"
                       class="bdt-news-card__title">
                        <?= htmlspecialchars($item['title']) ?>
                    </a>
                    <p class="bdt-news-card__excerpt">
                        <?= htmlspecialchars($item['excerpt'] ?? '') ?>
                    </p>
                    <div class="bdt-news-card__meta">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <?= ArticleService::formatDate($item['date'] ?? $item['publish_date'] ?? null) ?>
                        </span>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else : ?>
        <div class="bdt-news-empty">
            <h3>Belum ada berita tersedia</h3>
            <p>Berita terbaru akan segera hadir. Silakan kembali lagi nanti.</p>
        </div>
        <?php endif; ?>

    </div>
</main>

<?php include $libPath . '/custom/components/footer.php'; ?>
</body>
</html>
