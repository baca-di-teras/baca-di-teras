<?php
/**
 * Library Directory Page – Baca Di Teras
 *
 * File    : libraries/directory.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Halaman direktori perpustakaan desa.
 */

define('BASE_URL', '/baca-di-teras');

$activePage = 'perpustakaan';

$heroSection = [
    'eyebrow' => 'INFRASTRUKTUR LITERASI',
    'title' => 'Jaringan Perpustakaan Desa',
    'description' => "Jelajahi ruang pengetahuan yang dikurasi di seluruh Desa Teras.\nSetiap perpustakaan dirancang untuk mendorong pembelajaran,\ndialog komunitas, dan eksplorasi digital.",
];

$searchBar = [
    'placeholder' => 'Cari berdasarkan nama atau jalan...',
    'name' => 'librarySearch',
    'action' => '#',
];

$activeChip = 'semua-perpustakaan';
$chipFilterItems = [
    ['id' => 'semua-perpustakaan', 'label' => 'Semua Perpustakaan', 'href' => '#'],
    ['id' => 'distrik-pusat', 'label' => 'Distrik Pusat', 'href' => '#'],
    ['id' => 'wilayah-perbukitan', 'label' => 'Wilayah Perbukitan', 'href' => '#'],
    ['id' => 'pusat-digital', 'label' => 'Pusat Digital', 'href' => '#'],
];

$libraryCards = [
    [
        'title' => 'Perpustakaan Teras Utama',
        'description' => 'Perpustakaan unggulan kami yang melestarikan catatan leluhur desa bersama...',
        'address' => 'Jl. Raya Teras No. 12, Alun-alun Pusat',
        'hours' => '08:00 AM - 08:00 PM (Setiap Hari)',
        'badge' => 'Pusat Utama',
        'metric' => '8.4rb',
        'image_alt' => 'Ruang baca Perpustakaan Teras Utama',
    ],
    [
        'title' => 'Perpustakaan SD Negeri 1 Teras',
        'description' => 'Pusat teknologi tinggi yang berfokus pada literasi digital, e-book, dan lokakarya...',
        'address' => 'Sains Techno Park, Gerbang Timur',
        'hours' => '09:00 AM - 09:00 PM (Sen-Sab)',
        'badge' => 'Lab Digital',
        'metric' => '2.1rb',
        'image_alt' => 'Area belajar Perpustakaan SD Negeri 1 Teras',
    ],
    [
        'title' => 'Perpustakaan SD Negeri 2 Teras',
        'description' => 'Dikurasi khusus untuk pembaca muda kami, menampilkan zona bercerita interaktif dan...',
        'address' => 'Gang Pendidikan, Teras Utara',
        'hours' => '08:00 AM - 05:00 PM (Setiap Hari)',
        'badge' => '',
        'metric' => '3.6rb',
        'image_alt' => 'Ruang anak Perpustakaan SD Negeri 2 Teras',
    ],
    [
        'title' => 'Agro-Perpustakaan Teras',
        'description' => 'Pusat pengetahuan yang didedikasikan untuk pertanian berkelanjutan, botani lokal...',
        'address' => 'Distrik Pertanian Lembah Hijau',
        'hours' => '07:00 AM - 04:00 PM (Sel-Min)',
        'badge' => '',
        'metric' => '1.8rb',
        'image_alt' => 'Ruang koleksi Agro-Perpustakaan Teras',
    ],
    [
        'title' => 'Teras South Commons',
        'description' => 'Ruang komunitas yang aktif menyelenggarakan klub membaca, kelas bahasa, dan...',
        'address' => 'Jl. Persatuan, Desa Selatan',
        'hours' => '09:00 AM - 07:00 PM (Setiap Hari)',
        'badge' => '',
        'metric' => '4.2rb',
        'image_alt' => 'Ruang komunitas Teras South Commons',
    ],
    [
        'title' => 'Sanctuary Perbukitan',
        'description' => 'Tempat peristirahatan tenang untuk studi mendalam dan meditasi, terletak di...',
        'address' => 'Puncak Menara Pandang Summit',
        'hours' => '10:00 AM - 06:00 PM (Setiap Hari)',
        'badge' => '',
        'metric' => '2.7rb',
        'image_alt' => 'Teras baca Sanctuary Perbukitan',
    ],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Jelajahi jaringan perpustakaan Desa Teras. Temukan jam operasional, lokasi, dan koleksi lengkap perpustakaan kami.">
    <title>Direktori Perpustakaan – Baca Di Teras</title>
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

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .bdt-network-page .bdt-hero-section {
            min-height: 270px;
        }

        .bdt-network-page .bdt-hero-section__container {
            width: min(calc(100% - 100px), 1024px);
            padding: 40px 0 48px;
        }

        .bdt-network-page .bdt-hero-section__label {
            gap: 5px;
            min-height: 20px;
            padding: 4px 10px;
            font-size: 10px;
        }

        .bdt-network-page .bdt-hero-section__label svg {
            width: 12px;
            height: 12px;
        }

        .bdt-network-page .bdt-hero-section__title {
            max-width: 650px;
            margin-top: 22px;
            font-size: 42px;
            line-height: 1.12;
        }

        .bdt-network-page .bdt-hero-section__description {
            max-width: 560px;
            margin-top: 18px;
            font-size: 17px;
            line-height: 1.55;
        }

        .bdt-network-page__content {
            width: min(calc(100% - 100px), 1024px);
            margin: 0 auto;
            padding: 24px 0 110px;
            display: grid;
            gap: 24px;
        }

        .bdt-network-page__toolbar {
            display: flex;
            align-items: center;
            gap: 24px;
            min-height: 70px;
            padding: 15px 20px;
            border: 1px solid #eeeeec;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 18px rgba(20, 27, 22, 0.06);
        }

        .bdt-network-page__toolbar .bdt-search-bar {
            flex: 0 0 340px;
            height: 38px;
        }

        .bdt-network-page__toolbar .bdt-search-bar__field {
            padding: 0 14px;
            border-width: 1px;
            border-radius: 5px;
        }

        .bdt-network-page__toolbar .bdt-search-bar__icon {
            width: 17px;
            height: 17px;
            margin-right: 13px;
        }

        .bdt-network-page__toolbar .bdt-search-bar__input {
            font-size: 12px;
        }

        .bdt-network-page__toolbar .bdt-chip-filter {
            flex: 1 1 auto;
            min-height: 38px;
            padding: 0;
            border: 0;
            border-radius: 0;
            box-shadow: none;
            background: transparent;
        }

        .bdt-network-page__toolbar .bdt-chip-filter__item {
            min-height: 30px;
            padding: 0 16px;
            font-size: 12px;
            font-weight: 700;
        }

        .bdt-network-page__grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }

        @media (max-width: 768px) {
            .bdt-network-page .bdt-hero-section__container {
                width: min(calc(100% - 36px), 1024px);
                padding: 34px 0 42px;
            }

            .bdt-network-page .bdt-hero-section__title {
                font-size: 34px;
            }

            .bdt-network-page .bdt-hero-section__description {
                font-size: 16px;
            }

            .bdt-network-page__content {
                width: min(calc(100% - 36px), 1468px);
                padding: 22px 0 52px;
            }

            .bdt-network-page__toolbar {
                display: grid;
                gap: 14px;
            }

            .bdt-network-page__toolbar .bdt-search-bar {
                flex: none;
            }

            .bdt-network-page__toolbar .bdt-chip-filter {
                min-height: 44px;
                overflow-x: auto;
            }

            .bdt-network-page__grid {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 769px) and (max-width: 980px) {
            .bdt-network-page__toolbar {
                display: grid;
            }

            .bdt-network-page__toolbar .bdt-search-bar {
                flex: none;
            }

            .bdt-network-page__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>
<body class="bdt-network-page">
    <?php include __DIR__ . '/../../components/navbar.php'; ?>
    <?php include __DIR__ . '/../../components/hero.php'; ?>

    <main class="bdt-network-page__content">
        <div class="bdt-network-page__toolbar">
            <?php include __DIR__ . '/../../components/search-bar.php'; ?>
            <?php include __DIR__ . '/../../components/chip-filter.php'; ?>
        </div>

        <section class="bdt-network-page__grid" aria-label="Daftar perpustakaan desa">
            <?php foreach ($libraryCards as $libraryCardItem) :
                $libraryCard = array_merge(
                    [
                        'href' => '#',
                        'variant' => 'compact',
                    ],
                    $libraryCardItem
                );
            ?>
                <?php include __DIR__ . '/../../components/library-card.php'; ?>
            <?php endforeach; ?>
        </section>
    </main>

    <?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
