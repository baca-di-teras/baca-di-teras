<?php
/**
 * Library Card Component
 *
 * Usage:
 * $libraryCard = [
 *     'title' => 'Perpustakaan Teras Utama',
 *     'description' => 'Perpustakaan unggulan kami yang melestarikan catatan leluhur desa bersama...',
 *     'address' => 'Jl. Raya Teras No. 12, Alun-alun Pusat',
 *     'hours' => '08:00 AM - 08:00 PM (Setiap Hari)',
 *     'badge' => 'Pusat Utama',
 *     'metric' => '8.4rb',
 *     'href' => '#',
 *     'variant' => 'default'
 * ];
 * include 'custom/components/library-card.php';
 */

$baseUrl = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';

$libraryCardDefaults = [
    'title' => 'Perpustakaan Teras Utama',
    'description' => 'Perpustakaan unggulan kami yang melestarikan catatan leluhur desa bersama...',
    'address' => 'Jl. Raya Teras No. 12, Alun-alun Pusat',
    'hours' => '08:00 AM - 08:00 PM (Setiap Hari)',
    'badge' => 'Pusat Utama',
    'metric' => '8.4rb',
    'href' => '#',
    'image' => $baseUrl . '/custom/assets/images/library-teras-utama.jpg',
    'image_alt' => 'Interior Perpustakaan Teras Utama',
    'button_label' => 'Lihat Perpustakaan',
    'variant' => 'default',
];

$libraryCard = array_merge($libraryCardDefaults, $libraryCard ?? []);
$libraryCardVariant = (string) ($libraryCard['variant'] ?? 'default');
$libraryCardClass = 'bdt-library-card' . ($libraryCardVariant === 'compact' ? ' bdt-library-card--compact' : '');
$libraryCardBadge = trim((string) ($libraryCard['badge'] ?? ''));

if (!function_exists('bdt_library_card_escape')) {
    function bdt_library_card_escape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!defined('BDT_LIBRARY_CARD_STYLE_LOADED')) :
    define('BDT_LIBRARY_CARD_STYLE_LOADED', true);
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.bdt-library-card {
    width: min(100%, 492px);
    overflow: hidden;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 26px rgba(16, 24, 20, 0.08);
    color: #202124;
    font-family: 'Inter', Arial, sans-serif;
}

.bdt-library-card,
.bdt-library-card * {
    box-sizing: border-box;
}

.bdt-library-card__media {
    position: relative;
    width: 100%;
    aspect-ratio: 492 / 328;
    overflow: hidden;
    background: #d9ded7;
}

.bdt-library-card__image {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.bdt-library-card__badge {
    position: absolute;
    top: 20px;
    right: 20px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-height: 32px;
    padding: 5px 11px 5px 10px;
    border-radius: 10px;
    background: #f8faf5;
    box-shadow: 0 2px 8px rgba(14, 28, 18, 0.12);
    color: #232826;
    font-size: 16px;
    font-weight: 700;
    line-height: 1;
    white-space: nowrap;
}

.bdt-library-card__badge svg {
    width: 20px;
    height: 20px;
    color: #087125;
    flex: 0 0 auto;
}

.bdt-library-card__body {
    padding: 40px 31px 32px;
    background: #ffffff;
}

.bdt-library-card__headline {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
}

.bdt-library-card__title {
    max-width: 320px;
    margin: 0;
    color: #1f2022;
    font-size: 32px;
    font-weight: 800;
    line-height: 1.28;
    letter-spacing: 0;
}

.bdt-library-card__metric {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    min-height: 30px;
    margin-top: -2px;
    padding: 5px 9px;
    border-radius: 10px;
    background: #edf3ec;
    color: #66706a;
    font-size: 16px;
    font-weight: 500;
    line-height: 1;
    white-space: nowrap;
}

.bdt-library-card__metric svg {
    width: 15px;
    height: 15px;
    color: #66706a;
    flex: 0 0 auto;
}

.bdt-library-card__description {
    margin: 13px 0 0;
    max-width: 430px;
    color: #515750;
    font-size: 20px;
    font-weight: 400;
    line-height: 1.55;
    letter-spacing: 0;
}

.bdt-library-card__meta {
    display: grid;
    gap: 14px;
    margin-top: 29px;
}

.bdt-library-card__meta-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    min-width: 0;
    color: #4f574f;
    font-size: 20px;
    font-weight: 400;
    line-height: 1.32;
    letter-spacing: 0;
}

.bdt-library-card__meta-row svg {
    width: 22px;
    height: 22px;
    margin-top: 1px;
    color: #087125;
    flex: 0 0 auto;
}

.bdt-library-card__action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    min-height: 72px;
    margin-top: 36px;
    padding: 0 24px;
    border-radius: 14px;
    background: #086b20;
    color: #ffffff;
    font-size: 21px;
    font-weight: 500;
    line-height: 1;
    text-align: center;
    text-decoration: none;
    transition: background-color 0.18s ease, transform 0.18s ease;
}

.bdt-library-card__action:hover {
    background: #075d1c;
    color: #ffffff;
    text-decoration: none;
    transform: translateY(-1px);
}

.bdt-library-card__action svg {
    width: 22px;
    height: 22px;
    flex: 0 0 auto;
}

.bdt-library-card--compact {
    width: 100%;
    border-radius: 8px;
    box-shadow: 0 8px 20px rgba(16, 24, 20, 0.08);
}

.bdt-library-card--compact .bdt-library-card__media {
    aspect-ratio: 312 / 195;
}

.bdt-library-card--compact .bdt-library-card__badge {
    top: 10px;
    right: 10px;
    gap: 4px;
    min-height: 22px;
    padding: 3px 8px;
    border-radius: 7px;
    font-size: 10px;
}

.bdt-library-card--compact .bdt-library-card__badge svg {
    width: 13px;
    height: 13px;
}

.bdt-library-card--compact .bdt-library-card__body {
    padding: 18px 18px 20px;
}

.bdt-library-card--compact .bdt-library-card__headline {
    gap: 10px;
}

.bdt-library-card--compact .bdt-library-card__title {
    max-width: 210px;
    font-size: 20px;
    line-height: 1.18;
}

.bdt-library-card--compact .bdt-library-card__metric {
    gap: 3px;
    min-height: 20px;
    margin-top: 1px;
    padding: 3px 6px;
    border-radius: 6px;
    font-size: 10px;
}

.bdt-library-card--compact .bdt-library-card__metric svg {
    width: 10px;
    height: 10px;
}

.bdt-library-card--compact .bdt-library-card__description {
    display: -webkit-box;
    max-width: none;
    margin-top: 10px;
    overflow: hidden;
    color: #535b54;
    font-size: 12px;
    line-height: 1.45;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

.bdt-library-card--compact .bdt-library-card__meta {
    gap: 8px;
    margin-top: 14px;
}

.bdt-library-card--compact .bdt-library-card__meta-row {
    gap: 6px;
    font-size: 11px;
    line-height: 1.35;
}

.bdt-library-card--compact .bdt-library-card__meta-row svg {
    width: 13px;
    height: 13px;
    margin-top: 1px;
}

.bdt-library-card--compact .bdt-library-card__action {
    gap: 6px;
    min-height: 45px;
    margin-top: 16px;
    padding: 0 12px;
    border-radius: 7px;
    font-size: 11px;
}

.bdt-library-card--compact .bdt-library-card__action svg {
    width: 13px;
    height: 13px;
}

@media (max-width: 420px) {
    .bdt-library-card__body {
        padding: 28px 24px 28px;
    }

    .bdt-library-card__title {
        font-size: 28px;
        max-width: 250px;
    }

    .bdt-library-card__description {
        font-size: 19px;
    }

    .bdt-library-card__meta-row {
        font-size: 18px;
    }
}
</style>
<?php endif; ?>

<article class="<?= bdt_library_card_escape($libraryCardClass) ?>">
    <div class="bdt-library-card__media">
        <img
            class="bdt-library-card__image"
            src="<?= bdt_library_card_escape($libraryCard['image']) ?>"
            alt="<?= bdt_library_card_escape($libraryCard['image_alt']) ?>"
            loading="lazy"
        >
        <?php if ($libraryCardBadge !== '') : ?>
            <div class="bdt-library-card__badge" aria-label="<?= bdt_library_card_escape($libraryCardBadge) ?>">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2.25l2.8 6.03 6.6.8-4.86 4.54 1.28 6.53L12 16.92l-5.82 3.23 1.28-6.53L2.6 9.08l6.6-.8L12 2.25z"/>
                </svg>
                <span><?= bdt_library_card_escape($libraryCardBadge) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <div class="bdt-library-card__body">
        <div class="bdt-library-card__headline">
            <h2 class="bdt-library-card__title"><?= bdt_library_card_escape($libraryCard['title']) ?></h2>
            <div class="bdt-library-card__metric" aria-label="<?= bdt_library_card_escape($libraryCard['metric']) ?> koleksi">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M7 4.75h8.5A1.75 1.75 0 0 1 17.25 6.5v12.75l-3.4-2.05-3.35 2.05-3.5-2.05V5A.25.25 0 0 1 7.25 4.75Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M9.5 8.25h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span><?= bdt_library_card_escape($libraryCard['metric']) ?></span>
            </div>
        </div>

        <p class="bdt-library-card__description"><?= bdt_library_card_escape($libraryCard['description']) ?></p>

        <div class="bdt-library-card__meta" aria-label="Informasi perpustakaan">
            <div class="bdt-library-card__meta-row">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 21s7-5.05 7-11a7 7 0 1 0-14 0c0 5.95 7 11 7 11Z" stroke="currentColor" stroke-width="2.4" stroke-linejoin="round"/>
                    <path d="M12 12.4a2.4 2.4 0 1 0 0-4.8 2.4 2.4 0 0 0 0 4.8Z" stroke="currentColor" stroke-width="2.4"/>
                </svg>
                <span><?= bdt_library_card_escape($libraryCard['address']) ?></span>
            </div>

            <div class="bdt-library-card__meta-row">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" stroke="currentColor" stroke-width="2.4"/>
                    <path d="M12 7.5v5.2l3.6 2.1" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span><?= bdt_library_card_escape($libraryCard['hours']) ?></span>
            </div>
        </div>

        <a class="bdt-library-card__action" href="<?= bdt_library_card_escape($libraryCard['href']) ?>">
            <span><?= bdt_library_card_escape($libraryCard['button_label']) ?></span>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 12h14" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                <path d="M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </div>
</article>
