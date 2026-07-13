<?php
/**
 * Contact Page Preview
 * Development-only preview for the integrated contact page.
 */

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseUrl = str_replace('/custom/preview/contact-preview.php', '', $scriptName);
define('BASE_URL', rtrim($baseUrl, '/'));

$activePage = 'kontak';

$contactHero = [
    'eyebrow' => 'Hubungi Kami',
    'title' => 'Hubungi Kami',
    'description' => 'Memiliki pertanyaan tentang program perpustakaan atau inisiatif literasi digital kami? Kami di sini untuk membantu komunitas tumbuh bersama.',
];

$contactMessageForm = [
    'action' => '#',
    'method' => 'post',
];

$contactInformationItems = [
    ['type' => 'phone', 'label' => 'Telepon', 'value' => '+62 812-3456-7890'],
    ['type' => 'message', 'label' => 'WhatsApp', 'value' => '+62 812-3456-7890'],
    ['type' => 'email', 'label' => 'Email', 'value' => 'halo@desateras.id'],
];

$detailedLocationItems = [
    [
        'title' => 'Pusat Utama (Teras Center)',
        'address' => 'Jl. Utama Desa Teras No. 1, Boyolali, Jawa Tengah',
    ],
    [
        'title' => 'Stasiun Perpustakaan Digital',
        'address' => 'Aula Komunitas, Dusun Kidul, Desa Teras',
    ],
];

$gmapsPreview = [
    'label' => 'Jelajahi Perpustakaan Desa',
    'title' => 'Google Maps lokasi Perpustakaan Desa Teras',
    'query' => 'Jl. Utama Desa Teras No. 1, Boyolali, Jawa Tengah',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:,">
    <title>Preview - Contact Page</title>
    <style>
        html,
        body {
            min-height: 100%;
        }

        body {
            margin: 0;
            background: #f7f6f5;
            color: #202124;
        }

        .bdt-contact-preview__content {
            width: min(calc(100% - 108px), 1184px);
            margin: 0 auto;
            padding: 0 0 72px;
        }

        .bdt-contact-preview__layout {
            display: grid;
            grid-template-columns: minmax(0, 1.43fr) minmax(360px, 1fr);
            gap: 32px;
            align-items: stretch;
        }

        .bdt-contact-preview__side {
            display: grid;
            gap: 30px;
            align-content: start;
        }

        .bdt-contact-preview__map {
            margin-top: 32px;
        }

        @media (max-width: 980px) {
            .bdt-contact-preview__content {
                width: min(calc(100% - 36px), 1184px);
            }

            .bdt-contact-preview__layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .bdt-contact-preview__content {
                padding-bottom: 48px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>
    <?php include __DIR__ . '/../components/contact-hero.php'; ?>

    <main class="bdt-contact-preview__content">
        <div class="bdt-contact-preview__layout">
            <?php include __DIR__ . '/../components/contact-message-form.php'; ?>

            <aside class="bdt-contact-preview__side" aria-label="Informasi kontak dan lokasi">
                <?php include __DIR__ . '/../components/contact-information.php'; ?>
                <?php include __DIR__ . '/../components/detailed-location.php'; ?>
            </aside>
        </div>

        <div class="bdt-contact-preview__map">
            <?php include __DIR__ . '/../components/gmaps.php'; ?>
        </div>
    </main>
</body>
</html>
