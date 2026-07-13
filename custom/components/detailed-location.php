<?php
/**
 * Detailed Location Component
 *
 * Usage:
 * $detailedLocationItems = [
 *     ['title' => 'Pusat Utama (Teras Center)', 'address' => 'Jl. Utama...'],
 * ];
 * include 'custom/components/detailed-location.php';
 */

$detailedLocationTitle = $detailedLocationTitle ?? 'Lokasi Perpustakaan';
$detailedLocationItems = $detailedLocationItems ?? [
    [
        'title' => 'Pusat Utama (Teras Center)',
        'address' => 'Jl. Utama Desa Teras No. 1, Boyolali, Jawa Tengah',
    ],
    [
        'title' => 'Stasiun Perpustakaan Digital',
        'address' => 'Aula Komunitas, Dusun Kidul, Desa Teras',
    ],
];
$detailedLocationSocials = $detailedLocationSocials ?? [
    ['type' => 'globe', 'label' => 'Website', 'href' => '#'],
    ['type' => 'video', 'label' => 'Video', 'href' => '#'],
    ['type' => 'camera', 'label' => 'Galeri', 'href' => '#'],
];

if (!function_exists('bdtEscapeText')) {
    function bdtEscapeText($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('bdtDetailedLocationSocialIcon')) {
    function bdtDetailedLocationSocialIcon($type)
    {
        $icons = [
            'globe' => '<path d="M12 20.5a8.5 8.5 0 1 0 0-17 8.5 8.5 0 0 0 0 17Z" stroke="currentColor" stroke-width="1.8"/><path d="M3.8 10.2h16.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M12 3.5c2.35 2.15 3.55 4.97 3.55 8.5S14.35 18.35 12 20.5c-2.35-2.15-3.55-4.98-3.55-8.5S9.65 5.65 12 3.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
            'video' => '<path d="M4.75 6.75h10.5v10.5H4.75V6.75Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m15.25 10 4-2.25v8.5l-4-2.25V10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m8.9 9.35 3.35 2.65-3.35 2.65v-5.3Z" fill="currentColor"/>',
            'camera' => '<path d="M5.25 8h3l1.4-2h4.7l1.4 2h3v10.25H5.25V8Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 15.75a3.35 3.35 0 1 0 0-6.7 3.35 3.35 0 0 0 0 6.7Z" stroke="currentColor" stroke-width="1.8"/>',
        ];

        return $icons[$type] ?? $icons['globe'];
    }
}

if (!defined('BDT_DETAILED_LOCATION_STYLE_LOADED')) :
    define('BDT_DETAILED_LOCATION_STYLE_LOADED', true);
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.bdt-detailed-location,
.bdt-detailed-location * {
    box-sizing: border-box;
}

.bdt-detailed-location {
    width: 100%;
    min-height: 360px;
    padding: 32px;
    border: 1px solid #ded9d5;
    border-radius: 14px;
    background: #f2eeee;
    box-shadow: 0 10px 25px rgba(24, 28, 25, 0.05);
    color: #202124;
    font-family: 'Inter', Arial, sans-serif;
}

.bdt-detailed-location__title {
    margin: 0 0 27px;
    color: #086b20;
    font-size: 16px;
    font-weight: 500;
    line-height: 1.2;
    letter-spacing: 0;
    text-transform: uppercase;
}

.bdt-detailed-location__list {
    display: grid;
    gap: 23px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.bdt-detailed-location__item {
    display: grid;
    grid-template-columns: 24px minmax(0, 1fr);
    gap: 15px;
}

.bdt-detailed-location__pin {
    width: 22px;
    height: 22px;
    margin-top: 2px;
    color: #086b20;
}

.bdt-detailed-location__name {
    display: block;
    color: #232826;
    font-size: 16px;
    font-weight: 500;
    line-height: 1.35;
    letter-spacing: 0;
}

.bdt-detailed-location__address {
    display: block;
    margin-top: 4px;
    color: #4f574f;
    font-size: 15px;
    font-weight: 400;
    line-height: 1.45;
    letter-spacing: 0;
}

.bdt-detailed-location__divider {
    height: 1px;
    margin: 28px 0 28px;
    background: #d9d3ce;
}

.bdt-detailed-location__follow {
    margin: 0 0 17px;
    color: #666d66;
    font-size: 15px;
    font-weight: 400;
    line-height: 1.35;
    letter-spacing: 0;
}

.bdt-detailed-location__socials {
    display: flex;
    align-items: center;
    gap: 13px;
}

.bdt-detailed-location__social {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 999px;
    background: #e1e3df;
    color: #086b20;
    text-decoration: none;
    transition: transform 0.18s ease, background-color 0.18s ease;
}

.bdt-detailed-location__social:hover {
    background: #d7e7da;
    color: #086b20;
    transform: translateY(-1px);
}

.bdt-detailed-location__social svg {
    width: 19px;
    height: 19px;
}

@media (max-width: 768px) {
    .bdt-detailed-location {
        min-height: auto;
        padding: 28px 24px;
    }
}
</style>
<?php endif; ?>

<section class="bdt-detailed-location" aria-labelledby="bdt-detailed-location-title">
    <h2 class="bdt-detailed-location__title" id="bdt-detailed-location-title">
        <?= bdtEscapeText($detailedLocationTitle) ?>
    </h2>

    <ul class="bdt-detailed-location__list">
        <?php foreach ($detailedLocationItems as $detailedLocationItem) : ?>
            <li class="bdt-detailed-location__item">
                <svg class="bdt-detailed-location__pin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 21s7-5.05 7-11a7 7 0 1 0-14 0c0 5.95 7 11 7 11Z" stroke="currentColor" stroke-width="2.3" stroke-linejoin="round"/>
                    <path d="M12 12.35a2.35 2.35 0 1 0 0-4.7 2.35 2.35 0 0 0 0 4.7Z" stroke="currentColor" stroke-width="2.3"/>
                </svg>
                <span>
                    <span class="bdt-detailed-location__name"><?= bdtEscapeText($detailedLocationItem['title'] ?? '') ?></span>
                    <span class="bdt-detailed-location__address"><?= bdtEscapeText($detailedLocationItem['address'] ?? '') ?></span>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="bdt-detailed-location__divider" aria-hidden="true"></div>

    <p class="bdt-detailed-location__follow">Ikuti perkembangan kami</p>

    <div class="bdt-detailed-location__socials" aria-label="Media sosial">
        <?php foreach ($detailedLocationSocials as $detailedLocationSocial) :
            $detailedLocationSocialType = (string) ($detailedLocationSocial['type'] ?? 'globe');
        ?>
            <a
                class="bdt-detailed-location__social"
                href="<?= bdtEscapeText($detailedLocationSocial['href'] ?? '#') ?>"
                aria-label="<?= bdtEscapeText($detailedLocationSocial['label'] ?? '') ?>"
            >
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <?= bdtDetailedLocationSocialIcon($detailedLocationSocialType) ?>
                </svg>
            </a>
        <?php endforeach; ?>
    </div>
</section>
