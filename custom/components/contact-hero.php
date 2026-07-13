<?php
/**
 * Contact Hero Component
 *
 * Usage:
 * $contactHero = [
 *     'eyebrow' => 'Hubungi Kami',
 *     'title' => 'Hubungi Kami',
 *     'description' => 'Memiliki pertanyaan tentang program...'
 * ];
 * include 'custom/components/contact-hero.php';
 */

$contactHeroDefaults = [
    'eyebrow' => 'Hubungi Kami',
    'title' => 'Hubungi Kami',
    'description' => 'Memiliki pertanyaan tentang program perpustakaan atau inisiatif literasi digital kami? Kami di sini untuk membantu komunitas tumbuh bersama.',
];

$contactHero = array_merge($contactHeroDefaults, $contactHero ?? []);

if (!function_exists('bdtEscapeText')) {
    function bdtEscapeText($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!defined('BDT_CONTACT_HERO_STYLE_LOADED')) :
    define('BDT_CONTACT_HERO_STYLE_LOADED', true);
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.bdt-contact-hero,
.bdt-contact-hero * {
    box-sizing: border-box;
}

.bdt-contact-hero {
    width: 100%;
    padding: 48px 20px 72px;
    border-top: 1px solid #e8e5e2;
    color: #202124;
    text-align: center;
    font-family: 'Inter', Arial, sans-serif;
}

.bdt-contact-hero__label {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 29px;
    padding: 6px 25px;
    border-radius: 999px;
    background: #dceee2;
    color: #3f5746;
    font-size: 14px;
    font-weight: 500;
    line-height: 1;
    letter-spacing: 0;
}

.bdt-contact-hero__title {
    margin: 25px 0 0;
    color: #202124;
    font-size: 48px;
    font-weight: 800;
    line-height: 1.12;
    letter-spacing: 0;
}

.bdt-contact-hero__description {
    max-width: 720px;
    margin: 22px auto 0;
    color: #4f574f;
    font-size: 17px;
    font-weight: 400;
    line-height: 1.55;
    letter-spacing: 0;
}

@media (max-width: 768px) {
    .bdt-contact-hero {
        padding: 36px 18px 48px;
    }

    .bdt-contact-hero__label {
        min-height: 26px;
        padding: 5px 18px;
        font-size: 12px;
    }

    .bdt-contact-hero__title {
        margin-top: 20px;
        font-size: 36px;
    }

    .bdt-contact-hero__description {
        margin-top: 16px;
        font-size: 15px;
    }
}
</style>
<?php endif; ?>

<section class="bdt-contact-hero" aria-labelledby="bdt-contact-hero-title">
    <span class="bdt-contact-hero__label"><?= bdtEscapeText($contactHero['eyebrow']) ?></span>
    <h1 class="bdt-contact-hero__title" id="bdt-contact-hero-title">
        <?= bdtEscapeText($contactHero['title']) ?>
    </h1>
    <p class="bdt-contact-hero__description"><?= bdtEscapeText($contactHero['description']) ?></p>
</section>
