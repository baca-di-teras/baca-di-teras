<?php
/**
 * Landing Page – Baca Di Teras
 *
 * File    : landing.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Halaman utama portal literasi Desa Teras.
 * Menyusun komponen: Navbar, Hero, Stats, Perpustakaan Unggulan,
 * Koleksi Terbaru, CTA Banner, Berita, Fitur, Tentang, Kontak, Footer.
 */

// BASE_URL sudah didefinisikan di index.php (Front Controller).
// Definisikan hanya jika file ini diakses langsung (tanpa router).
if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage  = 'beranda';
$currentYear = date('Y');

// ── Service Layer — ambil data dari database ──────────────────
$libPath     = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..';
require_once $libPath . '/custom/helpers/Database.php';
require_once $libPath . '/custom/services/LibraryService.php';
require_once $libPath . '/custom/services/BookService.php';
require_once $libPath . '/custom/services/NewsService.php';
require_once $libPath . '/custom/services/VillageService.php';

$libraryService = new LibraryService();
$bookService    = new BookService();
$newsService    = new NewsService();
$villageService = new VillageService();

// Data untuk setiap seksi landing page
$libraryList  = $libraryService->getFeatured(5);
$stats        = $libraryService->getOverallStats();
$bookList     = $bookService->getLatest(6);
$featuredNews = $newsService->getFeaturedNews();
$newsList     = $newsService->getRecentNews(3, 0);
$featureList  = $villageService->getFeatures();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Baca Di Teras – Portal Literasi Digital Desa Teras. Temukan perpustakaan, koleksi buku, dan program literasi untuk memberdayakan masyarakat melalui pengetahuan dan konektivitas.">
    <meta name="keywords" content="perpustakaan desa, literasi digital, baca di teras, desa teras, boyolali, buku gratis">
    <meta name="author" content="Desa Teras">
    <title>Baca Di Teras – Portal Literasi Digital Desa Teras</title>
    <!-- Landing Page CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/landing.css">
</head>
<body>

<?php include __DIR__ . '/../components/navbar.php'; ?>

<!-- ============================================================
     HERO SECTION
     ============================================================ -->
<section class="bdt-hero" id="bdt-hero" aria-label="Hero">
    <div class="bdt-hero__container">

        <!-- Content -->
        <div class="bdt-hero__content">
            <span class="bdt-hero__badge">
                <span class="bdt-hero__badge-dot"></span>
                Platform Literasi Digital
            </span>

            <h1 class="bdt-hero__title">
                Membaca Lebih Mudah,<br>
                Belajar Lebih <em>Seru</em>
            </h1>

            <p class="bdt-hero__desc">
                Temukan perpustakaan, koleksi buku, dan program literasi
                di Desa Teras. Kami hadir untuk memberdayakan masyarakat
                melalui pengetahuan dan konektivitas.
            </p>

            <div class="bdt-hero__actions">
                <a href="<?= BASE_URL ?>/perpustakaan"
                   id="bdt-hero-btn-library"
                   class="bdt-btn bdt-btn--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                    Jelajahi Perpustakaan
                </a>
                <a href="<?= BASE_URL ?>/katalog"
                   id="bdt-hero-btn-catalog"
                   class="bdt-btn bdt-btn--outline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Katalog Digital
                </a>
            </div>
        </div>

        <!-- Media -->
        <div class="bdt-hero__media">
            <div class="bdt-hero__image-wrap">
                <img src="<?= BASE_URL ?>/custom/assets/images/hero-library.png"
                     alt="Suasana perpustakaan Baca Di Teras yang nyaman"
                     class="bdt-hero__image"
                     width="640" height="480">
            </div>
            <!-- Floating card -->
            <div class="bdt-hero__float-card" aria-hidden="true">
                <div class="bdt-hero__float-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                </div>
                <div>
                    <p class="bdt-hero__float-card-label">Total Koleksi Buku</p>
                    <p class="bdt-hero__float-card-value"><?= number_format((int)($stats['totalKoleksi'] ?? 0), 0, ',', '.') ?>+ Koleksi</p>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ============================================================
     STATS SECTION
     ============================================================ -->
<section class="bdt-stats" id="bdt-stats" aria-label="Statistik">
    <div class="bdt-container">
        <div class="bdt-stats__grid">

            <!-- Perpustakaan -->
            <div class="bdt-stats__item">
                <div class="bdt-stats__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </div>
                <div>
                    <p class="bdt-stats__number"><?= (int)($stats['totalPerpustakaan'] ?? 0) ?></p>
                    <p class="bdt-stats__label">Perpustakaan</p>
                </div>
            </div>

            <!-- Koleksi Buku -->
            <div class="bdt-stats__item">
                <div class="bdt-stats__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                </div>
                <div>
                    <p class="bdt-stats__number"><?= number_format((int)($stats['totalKoleksi'] ?? 0), 0, ',', '.') ?>+</p>
                    <p class="bdt-stats__label">Koleksi Buku</p>
                </div>
            </div>

            <!-- Pengunjung -->
            <div class="bdt-stats__item">
                <div class="bdt-stats__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <p class="bdt-stats__number">3.500+</p>
                    <p class="bdt-stats__label">Donasi Buku</p>
                </div>
            </div>

            <!-- Artikel -->
            <div class="bdt-stats__item">
                <div class="bdt-stats__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
                <div>
                    <p class="bdt-stats__number">45</p>
                    <p class="bdt-stats__label">Artikel &amp; Berita</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     PERPUSTAKAAN UNGGULAN
     ============================================================ -->
<section class="bdt-section" id="bdt-library-section" aria-label="Perpustakaan Unggulan">
    <div class="bdt-container">

        <div class="bdt-section__header">
            <div class="bdt-section__header-text">
                <span class="bdt-section__tag">Rekomendasi</span>
                <h2 class="bdt-section__title">Perpustakaan Kami</h2>
                <p class="bdt-section__subtitle">
                    Temukan perpustakaan terbaik di Desa Teras dengan koleksi
                    terlengkap dan fasilitas paling nyaman untuk semua kalangan.
                </p>
            </div>
            <a href="<?= BASE_URL ?>/perpustakaan"
               id="bdt-library-see-all"
               class="bdt-section__link-all">
                Lihat Semua
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
        </div>

        <!-- Library Cards Grid -->
        <div class="bdt-library-grid">
            <?php foreach ($libraryList as $libraryData) : ?>
                <?php include __DIR__ . '/../components/library-card-home.php'; ?>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- ============================================================
     KOLEKSI TERBARU
     ============================================================ -->
<section class="bdt-section bdt-section--gray" id="bdt-book-section" aria-label="Koleksi Terbaru">
    <div class="bdt-container">

        <div class="bdt-section__header">
            <div class="bdt-section__header-text">
                <span class="bdt-section__tag">Koleksi</span>
                <h2 class="bdt-section__title">Koleksi Terbaru</h2>
                <p class="bdt-section__subtitle">
                    Jelajahi koleksi buku terbaru yang baru saja ditambahkan.
                    Pilihan bacaan berkualitas untuk memperluas wawasan Anda.
                </p>
            </div>
            <a href="<?= BASE_URL ?>/katalog"
               id="bdt-book-see-all"
               class="bdt-section__link-all">
                Lihat Semua
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
        </div>

        <!-- Book Cards Grid -->
        <div class="bdt-book-grid">
            <?php foreach ($bookList as $bookData) : ?>
                <?php include __DIR__ . '/../components/book-card.php'; ?>
            <?php endforeach; ?>
        </div>

        <div class="bdt-book-section__footer">
            <a href="<?= BASE_URL ?>/katalog"
               id="bdt-book-catalog-btn"
               class="bdt-btn bdt-btn--outline">
                Lihat Semua Koleksi
            </a>
        </div>

    </div>
</section>

<!-- ============================================================
     CTA BANNER
     ============================================================ -->
<section class="bdt-cta" id="bdt-cta" aria-label="Ajakan Bergabung">
    <div class="bdt-cta__container">

        <div class="bdt-cta__content">
            <span class="bdt-cta__tag">Bergabung Sekarang</span>
            <h2 class="bdt-cta__title">
                Jadilah Bagian dari<br>Gerakan Literasi Desa Teras
            </h2>
            <p class="bdt-cta__desc">
                Daftar sebagai anggota perpustakaan secara gratis dan nikmati
                akses penuh ke seluruh koleksi, program literasi, dan acara komunitas
                yang kami selenggarakan setiap bulannya.
            </p>
            <div class="bdt-cta__actions">
                <a href="<?= BASE_URL ?>/daftar"
                   id="bdt-cta-btn-register"
                   class="bdt-btn bdt-btn--primary"
                   style="background-color:#fff; color:#1a6b2f; border-color:#fff;">
                    Daftar Keanggotaan Gratis
                </a>
                <a href="<?= BASE_URL ?>/katalog"
                   id="bdt-cta-btn-catalog"
                   class="bdt-btn bdt-btn--outline-white">
                    Cari Koleksi
                </a>
            </div>
        </div>

        <!-- Decorative icon -->
        <div class="bdt-cta__decor" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                 fill="currentColor">
                <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            </svg>
        </div>

    </div>
</section>

<!-- ============================================================
     BERITA TERBARU
     ============================================================ -->
<section class="bdt-section" id="bdt-news-section" aria-label="Berita Terbaru">
    <div class="bdt-container">

        <div class="bdt-section__header">
            <div class="bdt-section__header-text">
                <span class="bdt-section__tag">Informasi</span>
                <h2 class="bdt-section__title">Berita Terbaru</h2>
                <p class="bdt-section__subtitle">
                    Ikuti perkembangan terbaru seputar kegiatan literasi dan
                    program perpustakaan Desa Teras.
                </p>
            </div>
            <a href="<?= BASE_URL ?>/berita"
               id="bdt-news-see-all"
               class="bdt-section__link-all">
                Lihat Semua
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
        </div>

        <div class="bdt-news__grid">

            <!-- Featured News -->
            <article class="bdt-news-card--featured" id="bdt-news-featured">
                <div class="bdt-news-card__image-wrap">
                    <img src="<?= htmlspecialchars($featuredNews['image'] ?? BASE_URL . '/custom/assets/images/news-featured.png') ?>"
                         alt="<?= htmlspecialchars($featuredNews['title']) ?>"
                         class="bdt-news-card__image"
                         loading="lazy"
                         width="640" height="360">
                </div>
                <div class="bdt-news-card__body">
                    <span class="bdt-news-card__category-tag">
                        <?= htmlspecialchars($featuredNews['category']) ?>
                    </span>
                    <a href="<?= BASE_URL ?>/berita/<?= htmlspecialchars($featuredNews['slug'] ?? '') ?>"
                       id="bdt-news-featured-title"
                       class="bdt-news-card__title">
                        <?= htmlspecialchars($featuredNews['title']) ?>
                    </a>
                    <p class="bdt-news-card__excerpt">
                        <?= htmlspecialchars($featuredNews['excerpt']) ?>
                    </p>
                    <div class="bdt-news-card__meta">
                        <span class="bdt-news-card__meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round"
                                 aria-hidden="true">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <?php
                                require_once (defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..') . '/custom/services/ArticleService.php';
                            ?>
                            <?= ArticleService::formatDate($featuredNews['date'] ?? $featuredNews['publish_date'] ?? null) ?>
                        </span>
                        <span class="bdt-news-card__meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round"
                                 aria-hidden="true">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <?= htmlspecialchars($featuredNews['author_name'] ?? $featuredNews['author'] ?? 'Admin') ?>
                        </span>
                    </div>
                </div>
            </article>

            <!-- News Sidebar -->
            <div class="bdt-news__sidebar">
                <?php foreach ($newsList as $newsIndex => $newsItem) : ?>
                    <article class="bdt-news-card--small"
                             id="bdt-news-small-<?= $newsIndex + 1 ?>">
                        <div class="bdt-news-card__image-wrap">
                            <img src="<?= htmlspecialchars($newsItem['image'] ?? BASE_URL . '/custom/assets/images/news-small.png') ?>"
                                 alt="<?= htmlspecialchars($newsItem['title']) ?>"
                                 class="bdt-news-card__image"
                                 loading="lazy"
                                 width="400" height="200">
                        </div>
                        <div class="bdt-news-card__body">
                            <span class="bdt-news-card__category-tag">
                                <?= htmlspecialchars($newsItem['category']) ?>
                            </span>
                            <a href="<?= BASE_URL ?>/berita/<?= htmlspecialchars($newsItem['slug'] ?? '') ?>"
                               id="bdt-news-small-title-<?= $newsIndex + 1 ?>"
                               class="bdt-news-card__title bdt-news-card__title--sm">
                                <?= htmlspecialchars($newsItem['title']) ?>
                            </a>
                            <p class="bdt-news-card__excerpt" style="-webkit-line-clamp:2;">
                                <?= htmlspecialchars($newsItem['excerpt']) ?>
                            </p>
                            <div class="bdt-news-card__meta">
                                <span class="bdt-news-card__meta-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round"
                                         aria-hidden="true">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <?= ArticleService::formatDate($newsItem['date'] ?? $newsItem['publish_date'] ?? null) ?>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     FITUR LAYANAN
     ============================================================ -->
<section class="bdt-section bdt-section--gray" id="bdt-features-section" aria-label="Fitur Layanan">
    <div class="bdt-container">

        <div class="bdt-section__header">
            <div class="bdt-section__header-text">
                <span class="bdt-section__tag">Layanan</span>
                <h2 class="bdt-section__title">Fitur Layanan Kami</h2>
                <p class="bdt-section__subtitle">
                    Berbagai layanan unggulan yang kami hadirkan untuk memudahkan
                    akses literasi seluruh masyarakat Desa Teras.
                </p>
            </div>
        </div>

        <div class="bdt-features__grid">
            <?php foreach ($featureList as $featureIndex => $feature) : ?>
                <div class="bdt-feature-card"
                     id="bdt-feature-card-<?= $featureIndex + 1 ?>">
                    <div class="bdt-feature-card__icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <?= $feature['icon'] ?>
                        </svg>
                    </div>
                    <h3 class="bdt-feature-card__title">
                        <?= $feature['title'] ?>
                    </h3>
                    <p class="bdt-feature-card__desc">
                        <?= htmlspecialchars($feature['description'] ?? $feature['desc'] ?? '') ?>
                    </p>
                    <a href="<?= BASE_URL . htmlspecialchars($feature['href']) ?>"
                       id="bdt-feature-link-<?= $featureIndex + 1 ?>"
                       class="bdt-feature-card__link">
                        Selengkapnya
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- ============================================================
     TENTANG SECTION
     ============================================================ -->
<section class="bdt-section" id="bdt-about-section" aria-label="Tentang Baca Di Teras">
    <div class="bdt-about__container">

        <!-- Text Content -->
        <div class="bdt-about__content">
            <span class="bdt-section__tag">Tentang Kami</span>
            <h2 class="bdt-section__title">
                Tentang Baca Di Teras
            </h2>

            <div class="bdt-about__highlight">
                <div class="bdt-about__highlight-item">
                    <p class="bdt-about__highlight-number">10+</p>
                    <p class="bdt-about__highlight-label">Tahun Berdiri</p>
                </div>
                <div class="bdt-about__highlight-item">
                    <p class="bdt-about__highlight-number">6</p>
                    <p class="bdt-about__highlight-label">Perpustakaan</p>
                </div>
                <div class="bdt-about__highlight-item">
                    <p class="bdt-about__highlight-number">50+</p>
                    <p class="bdt-about__highlight-label">Relawan Aktif</p>
                </div>
            </div>

            <p class="bdt-about__desc">
                Baca Di Teras merupakan portal literasi digital yang lahir dari semangat
                komunitas Desa Teras, Boyolali. Kami percaya bahwa membaca adalah
                jendela dunia yang harus bisa diakses oleh semua kalangan, tanpa
                terkecuali — dari anak-anak hingga orang tua.
            </p>
            <p class="bdt-about__desc">
                Dengan jaringan 6 perpustakaan yang tersebar di seluruh wilayah desa,
                kami berkomitmen untuk terus menghadirkan layanan literasi yang
                inovatif dan berkelanjutan bagi masyarakat.
            </p>

            <a href="<?= BASE_URL ?>/tentang-kami"
               id="bdt-about-btn"
               class="bdt-btn bdt-btn--primary">
                Selengkapnya
            </a>
        </div>

        <!-- Media -->
        <div class="bdt-about__media">
            <img src="<?= BASE_URL ?>/custom/assets/images/about-village.png"
                 alt="Pemandangan Desa Teras dari udara"
                 class="bdt-about__image-main"
                 loading="lazy"
                 width="600" height="450">
            <img src="<?= BASE_URL ?>/custom/assets/images/news-featured.png"
                 alt="Kegiatan komunitas Baca Di Teras"
                 class="bdt-about__image-secondary"
                 loading="lazy"
                 width="280" height="280">
        </div>

    </div>
</section>

<!-- ============================================================
     KONTAK SECTION
     ============================================================ -->
<section class="bdt-section bdt-section--gray" id="bdt-contact-section" aria-label="Kontak">
    <div class="bdt-contact__container">

        <!-- Contact Info -->
        <div class="bdt-contact__content">
            <span class="bdt-section__tag">Hubungi Kami</span>
            <h2 class="bdt-section__title">Informasi Kontak</h2>
            <p class="bdt-section__subtitle" style="margin-bottom: 36px;">
                Kami siap membantu Anda. Hubungi kami melalui salah satu
                saluran kontak di bawah ini.
            </p>

            <ul class="bdt-contact__list">

                <li class="bdt-contact__item" id="bdt-contact-address">
                    <div class="bdt-contact__item-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div>
                        <p class="bdt-contact__item-label">Alamat</p>
                        <p class="bdt-contact__item-value">
                            Jl. Raya Teras, Desa Teras,<br>
                            Kec. Teras, Boyolali, Jawa Tengah
                        </p>
                    </div>
                </li>

                <li class="bdt-contact__item" id="bdt-contact-email">
                    <div class="bdt-contact__item-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="bdt-contact__item-label">Email</p>
                        <p class="bdt-contact__item-value">contact@desateras.id</p>
                    </div>
                </li>

                <li class="bdt-contact__item" id="bdt-contact-whatsapp">
                    <div class="bdt-contact__item-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.56a16 16 0 0 0 6 6l1.62-1.62a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="bdt-contact__item-label">WhatsApp</p>
                        <p class="bdt-contact__item-value">+62 812-3456-7890</p>
                    </div>
                </li>

            </ul>
        </div>

        <!-- Map -->
        <div class="bdt-contact__map" id="bdt-contact-map">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.034!2d110.648!3d-7.515!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sTeras%2C+Boyolali!5e0!3m2!1sid!2sid!4v0000000000000"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Peta Lokasi Desa Teras, Boyolali">
            </iframe>
        </div>

    </div>
</section>

<?php include __DIR__ . '/../components/footer.php'; ?>

</body>
</html>

