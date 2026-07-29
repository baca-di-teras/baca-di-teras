<?php
/**
 * Halaman Profil Desa – Baca Di Teras
 *
 * File    : profile/index.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Menampilkan profil Desa Teras: sejarah, visi-misi, kontak,
 * dan ringkasan statistik perpustakaan.
 * Route: /profil
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage = 'profil';
$libPath    = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';

require_once $libPath . '/custom/helpers/Database.php';
require_once $libPath . '/custom/services/VillageService.php';
require_once $libPath . '/custom/services/LibraryService.php';
require_once $libPath . '/custom/services/BookService.php';

$villageService = new VillageService();
$libraryService = new LibraryService();
$bookService    = new BookService();

// Ambil data profil desa
$profile  = $villageService->getProfile() ?? [];

// Statistik dari LibraryService
$stats    = $libraryService->getOverallStats();

// Daftar perpustakaan aktif
$libraries = $libraryService->getAllActive();

$baseUrl = defined('BASE_URL') ? BASE_URL : '';

// Fallback values jika profil belum diisi
$villageName = $profile['village_name'] ?? 'Desa Teras';
$tagline     = $profile['tagline']     ?? 'Portal Literasi Digital Desa Teras';
$description = $profile['description'] ?? 'Baca Di Teras adalah portal literasi digital Desa Teras, Boyolali.';
$sejarah     = $profile['sejarah']     ?? '';
$address     = trim(implode(', ', array_filter([
    $profile['address']  ?? 'Jl. Raya Teras',
    $profile['district'] ?? 'Teras',
    $profile['regency']  ?? 'Boyolali',
    $profile['province'] ?? 'Jawa Tengah',
])));
$phone     = $profile['phone']     ?? '+62 271-781000';
$email     = $profile['email']     ?? 'contact@desateras.id';
$whatsapp  = $profile['whatsapp']  ?? '+62 812-3456-7890';
$mapsUrl   = $profile['google_maps_url'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Profil Desa Teras Boyolali. Mengenal lebih dekat portal literasi Baca Di Teras — sejarah, visi-misi, dan program perpustakaan kami.">
    <meta name="keywords" content="profil desa teras, boyolali, perpustakaan desa, literasi">
    <title>Profil Desa – Baca Di Teras</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/custom/assets/css/landing.css">
    <style>
        body { background: #f7f6f5; }

        /* ── Hero ─────────────────────────────────────────────── */
        .bdt-profile-hero {
            background: linear-gradient(135deg, #1a3c5e 0%, #2d6a4f 100%);
            padding: 100px 0 80px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .bdt-profile-hero::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -20%;
            width: 600px; height: 600px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        .bdt-profile-hero__eyebrow {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.65);
            margin-bottom: 16px;
        }
        .bdt-profile-hero__title {
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 800;
            margin: 0 0 16px;
            line-height: 1.1;
        }
        .bdt-profile-hero__title em {
            font-style: normal;
            color: #b2f2bb;
        }
        .bdt-profile-hero__desc {
            font-size: 1.125rem;
            color: rgba(255,255,255,0.8);
            max-width: 580px;
            line-height: 1.75;
            margin: 0;
        }

        /* ── Stats Bar ────────────────────────────────────────── */
        .bdt-profile-stats {
            background: #fff;
            border-bottom: 1px solid #eee;
        }
        .bdt-profile-stats__inner {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
        }
        .bdt-profile-stat-item {
            padding: 32px 24px;
            text-align: center;
            border-right: 1px solid #f0f0f0;
        }
        .bdt-profile-stat-item:last-child { border-right: none; }
        .bdt-profile-stat-item__number {
            font-size: 2rem;
            font-weight: 800;
            color: #2d6a4f;
            margin: 0 0 4px;
        }
        .bdt-profile-stat-item__label {
            font-size: 0.85rem;
            color: #888;
            margin: 0;
        }

        /* ── Sections ─────────────────────────────────────────── */
        .bdt-profile-section {
            padding: 72px 0;
        }
        .bdt-profile-section--gray {
            background: #fff;
        }
        .bdt-profile-section__eyebrow {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #2d6a4f;
            margin-bottom: 12px;
        }
        .bdt-profile-section__title {
            font-size: clamp(1.5rem, 3vw, 2.25rem);
            font-weight: 800;
            color: #1a1a2e;
            margin: 0 0 20px;
            line-height: 1.2;
        }
        .bdt-profile-section__body {
            font-size: 1.05rem;
            line-height: 1.85;
            color: #4a4a5a;
        }
        .bdt-profile-section__body p { margin: 0 0 1.2em; }

        /* 2-column layout */
        .bdt-profile-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 64px;
            align-items: start;
        }
        .bdt-profile-2col--reverse { direction: rtl; }
        .bdt-profile-2col--reverse > * { direction: ltr; }

        /* Image card */
        .bdt-profile-img-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        }
        .bdt-profile-img-card img {
            width: 100%;
            height: 360px;
            object-fit: cover;
            display: block;
        }

        /* Library list */
        .bdt-profile-library-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: 0;
        }
        .bdt-profile-library-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: transform 0.25s, box-shadow 0.25s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .bdt-profile-library-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        .bdt-profile-library-card__badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            background: #e8f5e9;
            color: #2d6a4f;
            margin-bottom: 12px;
        }
        .bdt-profile-library-card__name {
            font-size: 1rem;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0 0 8px;
        }
        .bdt-profile-library-card__tagline {
            font-size: 0.85rem;
            color: #777;
            margin: 0 0 16px;
            line-height: 1.5;
        }
        .bdt-profile-library-card__stat {
            font-size: 0.8rem;
            color: #2d6a4f;
            font-weight: 600;
        }

        /* Kontak grid */
        .bdt-profile-contact-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }
        .bdt-profile-contact-card {
            background: #f8fff9;
            border: 1.5px solid #e8f5e9;
            border-radius: 16px;
            padding: 24px;
            text-align: center;
        }
        .bdt-profile-contact-card__icon {
            width: 48px; height: 48px;
            background: #e8f5e9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }
        .bdt-profile-contact-card__icon svg {
            width: 22px; height: 22px;
            color: #2d6a4f;
        }
        .bdt-profile-contact-card__label {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #999;
            margin: 0 0 4px;
        }
        .bdt-profile-contact-card__value {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1a1a2e;
            margin: 0;
        }

        /* Map */
        .bdt-profile-map {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .bdt-profile-map iframe {
            width: 100%;
            height: 360px;
            border: none;
            display: block;
        }

        @media (max-width: 900px) {
            .bdt-profile-2col { grid-template-columns: 1fr; gap: 32px; }
            .bdt-profile-stats__inner { grid-template-columns: repeat(2, 1fr); }
            .bdt-profile-library-grid { grid-template-columns: 1fr 1fr; }
            .bdt-profile-contact-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .bdt-profile-library-grid { grid-template-columns: 1fr; }
            .bdt-profile-stats__inner { grid-template-columns: 1fr 1fr; }
        }
    </style>
    <link rel="icon" type="image/png" href="<?= defined('BASE_URL') ? BASE_URL : '/baca-di-teras' ?>/custom/assets/images/logo_header.png">
</head>
<body>
<?php include $libPath . '/custom/components/navbar.php'; ?>

<!-- Hero -->
<section class="bdt-profile-hero" id="bdt-profile-hero">
    <div class="bdt-container">
        <span class="bdt-profile-hero__eyebrow">Tentang Kami</span>
        <h1 class="bdt-profile-hero__title">
            Profil <em><?= htmlspecialchars($villageName) ?></em>
        </h1>
        <p class="bdt-profile-hero__desc">
            <?= htmlspecialchars($tagline) ?>
        </p>
    </div>
</section>

<!-- Stats Bar -->
<section class="bdt-profile-stats" id="bdt-profile-stats" aria-label="Statistik perpustakaan">
    <div class="bdt-container">
        <div class="bdt-profile-stats__inner">
            <div class="bdt-profile-stat-item">
                <p class="bdt-profile-stat-item__number" id="bdt-stat-library">
                    <?= (int)($stats['totalPerpustakaan'] ?? 0) ?>
                </p>
                <p class="bdt-profile-stat-item__label">Perpustakaan Aktif</p>
            </div>
            <div class="bdt-profile-stat-item">
                <p class="bdt-profile-stat-item__number" id="bdt-stat-koleksi">
                    <?= number_format((int)($stats['totalKoleksi'] ?? 0), 0, ',', '.') ?>+
                </p>
                <p class="bdt-profile-stat-item__label">Koleksi Buku</p>
            </div>
            <div class="bdt-profile-stat-item">
                <p class="bdt-profile-stat-item__number" id="bdt-stat-anggota">
                    <?= number_format((int)($stats['totalAnggota'] ?? 0), 0, ',', '.') ?>+
                </p>
                <p class="bdt-profile-stat-item__label">Anggota Terdaftar</p>
            </div>
            <div class="bdt-profile-stat-item">
                <p class="bdt-profile-stat-item__number" id="bdt-stat-eksemplar">
                    <?= number_format((int)($stats['totalEksemplar'] ?? 0), 0, ',', '.') ?>+
                </p>
                <p class="bdt-profile-stat-item__label">Eksemplar Buku</p>
            </div>
        </div>
    </div>
</section>

<!-- Tentang Desa -->
<section class="bdt-profile-section" id="bdt-profile-about">
    <div class="bdt-container">
        <div class="bdt-profile-2col">
            <div>
                <span class="bdt-profile-section__eyebrow">Tentang Kami</span>
                <h2 class="bdt-profile-section__title">Mengenal <?= htmlspecialchars($villageName) ?></h2>
                <div class="bdt-profile-section__body">
                    <?php if ($description) : ?>
                        <p><?= nl2br(htmlspecialchars($description)) ?></p>
                    <?php endif; ?>
                </div>
                <a href="<?= $baseUrl ?>/perpustakaan"
                   id="bdt-profile-explore-lib"
                   class="bdt-btn bdt-btn--primary"
                   style="margin-top: 24px; display: inline-block;">
                    Jelajahi Perpustakaan →
                </a>
            </div>
            <div class="bdt-profile-img-card">
                <img src="<?= $baseUrl ?>/custom/assets/images/hero-library.png"
                     alt="Perpustakaan Desa Teras"
                     width="600" height="360"
                     loading="lazy">
            </div>
        </div>
    </div>
</section>

<!-- Sejarah -->
<?php if ($sejarah) : ?>
<section class="bdt-profile-section bdt-profile-section--gray" id="bdt-profile-history">
    <div class="bdt-container">
        <div class="bdt-profile-2col bdt-profile-2col--reverse">
            <div class="bdt-profile-img-card">
                <img src="<?= $baseUrl ?>/custom/assets/images/library-1.png"
                     alt="Sejarah Perpustakaan Desa Teras"
                     width="600" height="360"
                     loading="lazy">
            </div>
            <div>
                <span class="bdt-profile-section__eyebrow">Sejarah</span>
                <h2 class="bdt-profile-section__title">Perjalanan Literasi Kami</h2>
                <div class="bdt-profile-section__body">
                    <p><?= nl2br(htmlspecialchars($sejarah)) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Jaringan Perpustakaan -->
<?php if (!empty($libraries)) : ?>
<section class="bdt-profile-section" id="bdt-profile-libraries">
    <div class="bdt-container">
        <div style="text-align:center; margin-bottom: 48px;">
            <span class="bdt-profile-section__eyebrow">Infrastruktur Literasi</span>
            <h2 class="bdt-profile-section__title">Jaringan Perpustakaan Kami</h2>
            <p style="color:#777; max-width:540px; margin: 0 auto; font-size:0.95rem; line-height:1.7;">
                Setiap perpustakaan dirancang untuk melayani berbagai kebutuhan komunitas
                dengan koleksi dan fasilitas terbaik.
            </p>
        </div>
        <div class="bdt-profile-library-grid">
            <?php foreach ($libraries as $i => $lib) : ?>
            <a href="<?= $baseUrl ?>/perpustakaan/<?= htmlspecialchars($lib['slug']) ?>"
               class="bdt-profile-library-card"
               id="bdt-profile-lib-<?= $i + 1 ?>">
                <?php if (!empty($lib['badge'])) : ?>
                <span class="bdt-profile-library-card__badge"><?= htmlspecialchars($lib['badge']) ?></span>
                <?php endif; ?>
                <h3 class="bdt-profile-library-card__name"><?= htmlspecialchars($lib['name']) ?></h3>
                <?php if (!empty($lib['tagline'])) : ?>
                <p class="bdt-profile-library-card__tagline"><?= htmlspecialchars($lib['tagline']) ?></p>
                <?php endif; ?>
                <p class="bdt-profile-library-card__stat">
                    <?= number_format((int)($lib['total_koleksi'] ?? 0), 0, ',', '.') ?> koleksi
                    · <?= number_format((int)($lib['total_anggota'] ?? 0), 0, ',', '.') ?> anggota
                </p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Kontak -->
<section class="bdt-profile-section bdt-profile-section--gray" id="bdt-profile-contact">
    <div class="bdt-container">
        <div style="text-align:center; margin-bottom: 40px;">
            <span class="bdt-profile-section__eyebrow">Kontak</span>
            <h2 class="bdt-profile-section__title">Hubungi Kami</h2>
            <p style="color:#777; max-width:480px; margin: 0 auto; font-size:0.95rem; line-height:1.7;">
                <?= htmlspecialchars($address) ?>
            </p>
        </div>

        <div class="bdt-profile-contact-grid" id="bdt-profile-contact-grid">
            <!-- Telepon -->
            <div class="bdt-profile-contact-card">
                <div class="bdt-profile-contact-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.48 2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.54a16 16 0 0 0 5.55 5.55l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                </div>
                <p class="bdt-profile-contact-card__label">Telepon</p>
                <p class="bdt-profile-contact-card__value" id="bdt-contact-phone">
                    <?= htmlspecialchars($phone) ?>
                </p>
            </div>

            <!-- Email -->
            <div class="bdt-profile-contact-card">
                <div class="bdt-profile-contact-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <p class="bdt-profile-contact-card__label">Email</p>
                <p class="bdt-profile-contact-card__value" id="bdt-contact-email">
                    <?= htmlspecialchars($email) ?>
                </p>
            </div>

            <!-- WhatsApp -->
            <div class="bdt-profile-contact-card">
                <div class="bdt-profile-contact-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <p class="bdt-profile-contact-card__label">WhatsApp</p>
                <p class="bdt-profile-contact-card__value" id="bdt-contact-wa">
                    <?= htmlspecialchars($whatsapp) ?>
                </p>
            </div>
        </div>

        <!-- Google Maps -->
        <?php if ($mapsUrl) : ?>
        <div class="bdt-profile-map" id="bdt-profile-map">
            <iframe
                src="<?= htmlspecialchars($mapsUrl) ?>"
                title="Lokasi <?= htmlspecialchars($villageName) ?>"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php include $libPath . '/custom/components/footer.php'; ?>
</body>
</html>
