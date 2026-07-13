<?php
/**
 * News Card Component
 *
 * File    : news-card.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Komponen kartu berita/artikel yang mendukung berbagai variasi tampilan.
 *
 * Usage:
 *   $newsCardData = [
 *       'title'    => 'Desa Teras Resmikan Pusat Literasi Digital Baru',
 *       'category' => 'Berita',
 *       'excerpt'  => 'Pusat baru ini bertujuan untuk...',
 *       'date'     => '24 Okt 2023',
 *       'author'   => 'Admin Desa',
 *       'image'    => '/custom/assets/images/news-featured.png',
 *       'href'     => '#',
 *       'style'    => 'featured' // 'featured' | 'small-horizontal' | 'tips' | 'hero' | 'budaya' | 'klub' | 'menulis'
 *   ];
 *   include 'custom/components/news-card.php';
 */

if (!isset($newsCardData) || !is_array($newsCardData)) {
    return;
}

$title    = $newsCardData['title']    ?? 'Judul Berita';
$category = $newsCardData['category'] ?? 'Umum';
$excerpt  = $newsCardData['excerpt']  ?? '';
$date     = $newsCardData['date']     ?? '';
$readTime = $newsCardData['read_time']?? '';
$author   = $newsCardData['author']   ?? '';
$image    = $newsCardData['image']    ?? '';
$href     = $newsCardData['href']     ?? '#';
$style    = $newsCardData['style']    ?? 'standard';
$baseUrl  = defined('BASE_URL') ? BASE_URL : '';
?>

<?php if ($style === 'featured') : ?>
    <!-- 1. FEATURED NEWS CARD -->
    <article class="bdt-news-card-featured">
        <div class="bdt-news-card-featured__image-wrap">
            <img src="<?= htmlspecialchars($baseUrl . $image) ?>" alt="<?= htmlspecialchars($title) ?>" class="bdt-news-card-featured__img" loading="lazy">
            <span class="bdt-news-card-featured__tag">BERITA UTAMA</span>
        </div>
        <div class="bdt-news-card-featured__body">
            <div class="bdt-news-card-featured__meta">
                <span class="bdt-news-card-featured__meta-item">
                    <!-- Calendar Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <?= htmlspecialchars($date) ?>
                </span>
                <span class="bdt-news-card-featured__meta-item">
                    <!-- User Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <?= htmlspecialchars($author) ?>
                </span>
            </div>
            <h3 class="bdt-news-card-featured__title">
                <a href="<?= htmlspecialchars($baseUrl . $href) ?>"><?= htmlspecialchars($title) ?></a>
            </h3>
            <p class="bdt-news-card-featured__excerpt"><?= htmlspecialchars($excerpt) ?></p>
            <a href="<?= htmlspecialchars($baseUrl . $href) ?>" class="bdt-news-card-featured__link">
                Baca Selengkapnya
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </article>

<?php elseif ($style === 'small-horizontal') : ?>
    <!-- 2. SMALL SIDEBAR NEWS CARD -->
    <article class="bdt-news-card-small">
        <div class="bdt-news-card-small__image-wrap">
            <img src="<?= htmlspecialchars($baseUrl . $image) ?>" alt="<?= htmlspecialchars($title) ?>" class="bdt-news-card-small__img" loading="lazy">
        </div>
        <div class="bdt-news-card-small__body">
            <span class="bdt-news-card-small__category"><?= htmlspecialchars($category) ?></span>
            <h4 class="bdt-news-card-small__title">
                <a href="<?= htmlspecialchars($baseUrl . $href) ?>"><?= htmlspecialchars($title) ?></a>
            </h4>
            <div class="bdt-news-card-small__meta">
                <?= htmlspecialchars($date) ?> • <?= htmlspecialchars($readTime) ?>
            </div>
        </div>
    </article>

<?php elseif ($style === 'tips') : ?>
    <!-- 3. TIPS LITERASI CARD (Beige background, rounded, circular avatar, arrow button) -->
    <article class="bdt-article-card bdt-article-card--tips">
        <div class="bdt-article-card__body">
            <span class="bdt-article-card__tag"><?= htmlspecialchars($category) ?></span>
            <h3 class="bdt-article-card__title">
                <a href="<?= htmlspecialchars($baseUrl . $href) ?>"><?= htmlspecialchars($title) ?></a>
            </h3>
            <p class="bdt-article-card__excerpt"><?= htmlspecialchars($excerpt) ?></p>
            
            <div class="bdt-article-card__footer">
                <div class="bdt-article-card__author-avatars">
                    <!-- Overlapping mock avatar circles -->
                    <span class="bdt-article-card__avatar" style="background-color: #cbd5e1; border: 2px solid #faf8f5;">AS</span>
                    <span class="bdt-article-card__avatar" style="background-color: #94a3b8; border: 2px solid #faf8f5; margin-left: -10px;">+3</span>
                </div>
                <a href="<?= htmlspecialchars($baseUrl . $href) ?>" class="bdt-article-card__arrow-btn" aria-label="Baca selengkapnya">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 17"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </article>

<?php elseif ($style === 'hero') : ?>
    <!-- 4. HERO ARTICLE CARD (Large image overlay, centered text, button) -->
    <article class="bdt-article-card bdt-article-card--hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.7)), url('<?= htmlspecialchars($baseUrl . $image) ?>');">
        <div class="bdt-article-card__body">
            <span class="bdt-article-card__tag"><?= htmlspecialchars($category) ?></span>
            <h3 class="bdt-article-card__title"><?= htmlspecialchars($title) ?></h3>
            
            <div class="bdt-article-card__footer">
                <span class="bdt-article-card__time">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <?= htmlspecialchars($date) ?>
                </span>
                <a href="<?= htmlspecialchars($baseUrl . $href) ?>" class="bdt-article-card__btn">Mulai Membaca</a>
            </div>
        </div>
    </article>

<?php elseif ($style === 'budaya') : ?>
    <!-- 5. BUDAYA ARTICLE CARD (Grey card, author at bottom left, arrow button) -->
    <article class="bdt-article-card bdt-article-card--budaya">
        <div class="bdt-article-card__body">
            <span class="bdt-article-card__tag"><?= htmlspecialchars($category) ?></span>
            <h3 class="bdt-article-card__title">
                <a href="<?= htmlspecialchars($baseUrl . $href) ?>"><?= htmlspecialchars($title) ?></a>
            </h3>
            <p class="bdt-article-card__excerpt"><?= htmlspecialchars($excerpt) ?></p>
            
            <div class="bdt-article-card__footer">
                <span class="bdt-article-card__author-name">Oleh <?= htmlspecialchars($author) ?></span>
                <a href="<?= htmlspecialchars($baseUrl . $href) ?>" class="bdt-article-card__arrow-btn" aria-label="Baca selengkapnya">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 17"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </article>

<?php elseif ($style === 'klub') : ?>
    <!-- 6. KLUB BUKU CARD (Icon, green title, link "Lihat Daftar") -->
    <article class="bdt-article-card bdt-article-card--action">
        <div class="bdt-article-card__body">
            <div class="bdt-article-card__icon-wrap">
                <!-- Book icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            </div>
            <h3 class="bdt-article-card__title bdt-article-card__title--green">
                <?= htmlspecialchars($title) ?>
            </h3>
            <p class="bdt-article-card__excerpt"><?= htmlspecialchars($excerpt) ?></p>
            <div class="bdt-article-card__footer">
                <a href="<?= htmlspecialchars($baseUrl . $href) ?>" class="bdt-article-card__action-link">Lihat Daftar</a>
            </div>
        </div>
    </article>

<?php elseif ($style === 'menulis') : ?>
    <!-- 7. MENULIS CARD (Icon, dark title, link "Unduh Panduan PDF") -->
    <article class="bdt-article-card bdt-article-card--action">
        <div class="bdt-article-card__body">
            <div class="bdt-article-card__icon-wrap">
                <!-- Document/File Text icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </div>
            <h3 class="bdt-article-card__title">
                <?= htmlspecialchars($title) ?>
            </h3>
            <p class="bdt-article-card__excerpt"><?= htmlspecialchars($excerpt) ?></p>
            <div class="bdt-article-card__footer">
                <a href="<?= htmlspecialchars($baseUrl . $href) ?>" class="bdt-article-card__action-link">Unduh Panduan PDF</a>
            </div>
        </div>
    </article>
<?php endif; ?>
