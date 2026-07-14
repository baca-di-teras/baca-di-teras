<?php
/**
 * Hero Section Component
 *
 * Usage:
 * $heroSection = [
 *     'eyebrow' => 'INFRASTRUKTUR LITERASI',
 *     'title' => 'Jaringan Perpustakaan Desa',
 *     'description' => 'Jelajahi ruang pengetahuan...'
 * ];
 * include 'custom/components/hero.php';
 */

$heroSectionDefaults = [
    'eyebrow' => 'INFRASTRUKTUR LITERASI',
    'title' => 'Jaringan Perpustakaan Desa',
    'description' => "Jelajahi ruang pengetahuan yang dikurasi di seluruh Desa Teras.\nSetiap perpustakaan dirancang untuk mendorong pembelajaran,\ndialog komunitas, dan eksplorasi digital.",
];

$heroSection = array_merge($heroSectionDefaults, $heroSection ?? []);

if (!function_exists('bdtEscapeText')) {
    function bdtEscapeText($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!defined('BDT_HERO_STYLE_LOADED')) :
    define('BDT_HERO_STYLE_LOADED', true);
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.bdt-hero-section,
.bdt-hero-section * {
    box-sizing: border-box;
}

.bdt-hero-section {
    width: 100%;
    min-height: 420px;
    background: #f7f6f5;
    border-top: 1px solid #e7e5e4;
    border-bottom: 1px solid #ecebea;
    color: #202124;
    font-family: 'Inter', Arial, sans-serif;
}

.bdt-hero-section__container {
    width: min(calc(100% - 100px), 1468px);
    margin: 0 auto;
    padding: 60px 0 76px;
}

.bdt-hero-section__label {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    min-height: 34px;
    padding: 5px 17px 5px 16px;
    border-radius: 999px;
    background: #dceee2;
    color: #667269;
    font-size: 19px;
    font-weight: 600;
    line-height: 1;
    letter-spacing: 0;
    text-transform: uppercase;
    white-space: nowrap;
}

.bdt-hero-section__label svg {
    width: 23px;
    height: 23px;
    color: #5b665e;
    flex: 0 0 auto;
}

.bdt-hero-section__title {
    max-width: 980px;
    margin: 32px 0 0;
    color: #202124;
    font-size: 60px;
    font-weight: 800;
    line-height: 1.12;
    letter-spacing: 0;
}

.bdt-hero-section__description {
    max-width: 835px;
    margin: 26px 0 0;
    color: #4f574f;
    font-size: 24px;
    font-weight: 400;
    line-height: 1.46;
    letter-spacing: 0;
    white-space: pre-line;
}

@media (max-width: 768px) {
    .bdt-hero-section {
        min-height: auto;
    }

    .bdt-hero-section__container {
        width: min(calc(100% - 36px), 1468px);
        padding: 42px 0 54px;
    }

    .bdt-hero-section__label {
        font-size: 13px;
        min-height: 28px;
        padding: 5px 12px;
    }

    .bdt-hero-section__label svg {
        width: 17px;
        height: 17px;
    }

    .bdt-hero-section__title {
        margin-top: 24px;
        font-size: 38px;
        line-height: 1.14;
    }

    .bdt-hero-section__description {
        margin-top: 18px;
        font-size: 17px;
        line-height: 1.55;
    }
}
</style>
<?php endif; ?>

<section class="bdt-hero-section" aria-labelledby="bdt-hero-section-title">
    <div class="bdt-hero-section__container">
        <div class="bdt-hero-section__label">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4.75 5.5c2.85 0 5.18.58 7.25 2.2v11.05c-2.07-1.62-4.4-2.2-7.25-2.2V5.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                <path d="M19.25 5.5c-2.85 0-5.18.58-7.25 2.2v11.05c2.07-1.62 4.4-2.2 7.25-2.2V5.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                <path d="M12 7.7v11.05" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span><?= bdtEscapeText($heroSection['eyebrow']) ?></span>
        </div>

        <h1 class="bdt-hero-section__title" id="bdt-hero-section-title">
            <?= bdtEscapeText($heroSection['title']) ?>
        </h1>

        <p class="bdt-hero-section__description"><?= bdtEscapeText(trim($heroSection['description'])) ?></p>
    </div>
</section>
