<?php
/**
 * Google Maps Preview Component
 *
 * Usage:
 * $gmapsPreview = [
 *     'label' => 'Jelajahi Perpustakaan Desa',
 *     'query' => 'Jl. Utama Desa Teras No. 1, Boyolali, Jawa Tengah'
 * ];
 * include 'custom/components/gmaps.php';
 */

$gmapsPreviewDefaults = [
    'label' => 'Jelajahi Perpustakaan Desa',
    'title' => 'Lokasi Perpustakaan Desa Teras',
    'query' => 'Jl. Utama Desa Teras No. 1, Boyolali, Jawa Tengah',
    'src' => '',
];

$gmapsPreview = array_merge($gmapsPreviewDefaults, $gmapsPreview ?? []);
$gmapsPreviewSrc = trim((string) $gmapsPreview['src']);

if ($gmapsPreviewSrc === '') {
    $gmapsPreviewSrc = 'https://www.google.com/maps?q=' . rawurlencode((string) $gmapsPreview['query']) . '&output=embed';
}

if (!function_exists('bdtEscapeText')) {
    function bdtEscapeText($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!defined('BDT_GMAPS_PREVIEW_STYLE_LOADED')) :
    define('BDT_GMAPS_PREVIEW_STYLE_LOADED', true);
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.bdt-gmaps-preview,
.bdt-gmaps-preview * {
    box-sizing: border-box;
}

.bdt-gmaps-preview {
    position: relative;
    width: 100%;
    min-height: 458px;
    overflow: hidden;
    border: 1px solid #cfd8ca;
    border-radius: 13px;
    background: #ffffff;
    box-shadow: 0 16px 35px rgba(24, 28, 25, 0.08);
    font-family: 'Inter', Arial, sans-serif;
}

.bdt-gmaps-preview__label {
    position: absolute;
    top: 30px;
    left: 18px;
    z-index: 2;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    min-height: 53px;
    padding: 0 17px;
    border-radius: 9px;
    background: rgba(255, 255, 255, 0.94);
    box-shadow: 0 9px 22px rgba(24, 28, 25, 0.14);
    color: #086b20;
    font-size: 16px;
    font-weight: 500;
    line-height: 1;
    letter-spacing: 0;
}

.bdt-gmaps-preview__label svg {
    width: 16px;
    height: 16px;
    flex: 0 0 auto;
}

.bdt-gmaps-preview__frame {
    display: block;
    width: 100%;
    height: 458px;
    border: 0;
    background: #eef1ed;
}

@media (max-width: 768px) {
    .bdt-gmaps-preview {
        min-height: 360px;
    }

    .bdt-gmaps-preview__frame {
        height: 360px;
    }

    .bdt-gmaps-preview__label {
        top: 18px;
        left: 16px;
        min-height: 44px;
        max-width: calc(100% - 32px);
        padding: 0 14px;
        font-size: 13px;
    }
}
</style>
<?php endif; ?>

<section class="bdt-gmaps-preview" aria-label="<?= bdtEscapeText($gmapsPreview['title']) ?>">
    <div class="bdt-gmaps-preview__label">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4.75 5.5c2.85 0 5.18.58 7.25 2.2v11.05c-2.07-1.62-4.4-2.2-7.25-2.2V5.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            <path d="M19.25 5.5c-2.85 0-5.18.58-7.25 2.2v11.05c2.07-1.62 4.4-2.2 7.25-2.2V5.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            <path d="M12 7.7v11.05" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span><?= bdtEscapeText($gmapsPreview['label']) ?></span>
    </div>

    <iframe
        class="bdt-gmaps-preview__frame"
        title="<?= bdtEscapeText($gmapsPreview['title']) ?>"
        src="<?= bdtEscapeText($gmapsPreviewSrc) ?>"
        loading="lazy"
        allowfullscreen
        referrerpolicy="no-referrer-when-downgrade"
    ></iframe>
</section>
