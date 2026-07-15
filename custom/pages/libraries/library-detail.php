<?php
/**
 * Library Detail Page
 *
 * File    : libraries/library-detail.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Menampilkan detail satu perpustakaan.
 * Slug perpustakaan diambil dari parameter URL dinamis:
 *   /perpustakaan/{slug}  →  $routeParams['slug']
 *
 * Data dimuat dari config/library-detail-config.php (metadata per perpus).
 */

// BASE_URL sudah didefinisikan di index.php (Front Controller).
// Definisikan hanya jika file ini diakses langsung (tanpa router).
if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

// ── Service Layer ──────────────────────────────────────────────
$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/helpers/Database.php';
require_once $libPath . '/custom/services/LibraryService.php';
require_once $libPath . '/custom/services/BookService.php';

$libraryService = new LibraryService();
$bookService    = new BookService();

// Ambil slug dari route dinamis (/perpustakaan/{slug})
// atau fallback ke query string untuk backward-compatibility
$librarySlug = $routeParams['slug'] ?? $_GET['id'] ?? 'perpustakaan-utama';

// Dapatkan detail perpustakaan dari service
$detail = $libraryService->getDetailBySlug($librarySlug);

// Jika slug tidak ditemukan → tampilkan halaman 404
if (!$detail) {
    http_response_code(404);
    $baseUrl = defined('BASE_URL') ? BASE_URL : '';
    require defined('ROOT_PATH') ? ROOT_PATH . '/custom/pages/404.php' : __DIR__ . '/../../404.php';
    exit;
}

$baseUrl = defined('BASE_URL') ? BASE_URL : '';

// Shortcut variabel
$activePage     = 'perpustakaan';
$libName        = $detail['library']['name'];
$libBadge       = $detail['library']['badge'] ?? '';
$libDesc        = $detail['library']['description'] ?? '';
$libHeroImage   = $detail['library']['cover_image'] ?? '';

// Format fasilitas
$facilityList = [];
foreach ($detail['facilities'] as $fac) {
    $facilityList[] = $fac['name'];
}

// Format fakta menarik (stats)
$statList = [
    ['nilai' => number_format((int)$detail['stats']['totalKoleksi'], 0, ',', '.'), 'keterangan' => 'Koleksi Buku'],
    ['nilai' => number_format((int)$detail['stats']['totalAnggota'], 0, ',', '.'), 'keterangan' => 'Anggota Aktif'],
    ['nilai' => number_format((int)$detail['stats']['totalEksemplar'], 0, ',', '.'), 'keterangan' => 'Eksemplar Buku'],
    ['nilai' => number_format((int)$detail['stats']['totalJudul'], 0, ',', '.'), 'keterangan' => 'Judul Buku'],
];

// Format jam operasional
$hourList = [];
$hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
for ($i = 0; $i <= 6; $i++) {
    $jam = $detail['hours'][$i] ?? null;
    $hourList[] = [
        'hari'  => $hariIndo[$i],
        'jam'   => ($jam && $jam['is_open']) ? date('H:i', strtotime($jam['open_time'])) . ' - ' . date('H:i', strtotime($jam['close_time'])) : 'Tutup',
        'tutup' => (!$jam || !$jam['is_open'])
    ];
}

$isOpen = $detail['isOpenNow'];
$hourNote = $detail['todayHour']['note'] ?? '';

// Galeri
$galleryList = [];
foreach ($detail['gallery'] as $img) {
    $galleryList[] = [
        'image' => $img['image_path'],
        'alt'   => $img['alt_text'] ?? 'Galeri Perpustakaan'
    ];
}

// Data Lokasi
$mapsQuery = trim($libName . ' ' . ($detail['library']['address'] ?? 'Desa Teras Boyolali'));
$mapsLink = $detail['library']['google_maps_url']
    ?: 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($mapsQuery);
$mapsEmbed = str_contains($mapsLink, '/maps/embed')
    ? $mapsLink
    : 'https://maps.google.com/maps?q=' . rawurlencode($mapsQuery) . '&output=embed';

$locationData = [
    'alamat'       => $detail['library']['address'] ?? '',
    'transportasi' => 'Dapat diakses dengan kendaraan pribadi maupun angkutan umum', // Default
    'mapsLink'     => $mapsLink,
    'mapsEmbed'    => $mapsEmbed
];

// Buku terbaru di perpustakaan ini
$bookList = $bookService->getCatalog(
    ['location' => $detail['library']['slims_location_id'], 'sort' => 'terbaru'],
    6,
    0
);

// Kegiatan Mendatang (Belum ada EventService, set array kosong untuk saat ini)
$eventList = [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($libDesc) ?>">
    <title><?= htmlspecialchars($libName) ?> – Baca Di Teras</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/custom/assets/css/library-detail.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/custom/assets/css/landing.css">
</head>
<body>

<?php include __DIR__ . '/../../components/navbar.php'; ?>

<!-- ============================================================
     HERO SECTION
     ============================================================ -->
<section class="bdt-lib-hero" id="bdt-lib-hero" aria-label="Foto Perpustakaan">
    <img src="<?= htmlspecialchars($baseUrl . $libHeroImage) ?>"
         alt="Suasana <?= htmlspecialchars($libName) ?>"
         class="bdt-lib-hero__image"
         width="1920" height="520">
    <div class="bdt-lib-hero__content">
        <div class="bdt-container">
            <span class="bdt-lib-hero__badge">
                <span class="bdt-lib-hero__badge-dot"></span>
                <?= htmlspecialchars($libBadge) ?>
            </span>
            <h1 class="bdt-lib-hero__title">
                <?= htmlspecialchars($libName) ?>
            </h1>
            <p class="bdt-lib-hero__desc">
                <?= htmlspecialchars($libDesc) ?>
            </p>
        </div>
    </div>
</section>

<!-- ============================================================
     INFO CARDS — Fasilitas | Fakta Menarik | Jam Operasional
     ============================================================ -->
<section class="bdt-lib-info" id="bdt-lib-info" aria-label="Informasi Perpustakaan">
    <div class="bdt-container">
        <div class="bdt-lib-info__grid">

            <!-- Card 1: Fasilitas Modern -->
            <div class="bdt-info-card" id="bdt-info-fasilitas">
                <div class="bdt-info-card__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                </div>
                <h2 class="bdt-info-card__title">Fasilitas Modern</h2>
                <ul class="bdt-facility-list" role="list">
                    <?php foreach ($facilityList as $facilityIdx => $facilityItem) : ?>
                        <li class="bdt-facility-list__item"
                            id="bdt-facility-<?= $facilityIdx + 1 ?>">
                            <span class="bdt-facility-list__check" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="3"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </span>
                            <?= htmlspecialchars($facilityItem) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Card 2: Fakta Menarik (green bg) -->
            <div class="bdt-info-card bdt-info-card--fact" id="bdt-info-fakta">
                <h2 class="bdt-info-card__title">Fakta Menarik</h2>
                <ul class="bdt-fact-list" role="list">
                    <?php foreach ($statList as $statIdx => $statItem) : ?>
                        <li id="bdt-stat-<?= $statIdx + 1 ?>">
                            <p class="bdt-fact-list__number">
                                <?= htmlspecialchars($statItem['nilai']) ?>
                            </p>
                            <p class="bdt-fact-list__label">
                                <?= htmlspecialchars($statItem['keterangan']) ?>
                            </p>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?= $baseUrl ?>/daftar"
                   id="bdt-info-fakta-btn"
                   class="bdt-btn--fact">
                    Daftar Antar Libras
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </div>

            <!-- Card 3: Jam Operasional -->
            <div class="bdt-info-card" id="bdt-info-jam">
                <h2 class="bdt-info-card__title">Jam Operasional</h2>
                <div class="bdt-hours-status">
                    <span class="bdt-hours-status__dot" aria-hidden="true"></span>
                    <?= $isOpen ? 'Sedang Buka' : 'Tutup Sekarang' ?>
                </div>
                <table class="bdt-hours-table" aria-label="Jadwal jam buka perpustakaan">
                    <tbody>
                        <?php foreach ($hourList as $hourItem) : ?>
                            <tr>
                                <td><?= htmlspecialchars($hourItem['hari']) ?></td>
                                <td class="<?= $hourItem['tutup'] ? 'bdt-hours-closed' : '' ?>">
                                    <?= htmlspecialchars($hourItem['jam']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if ($hourNote) : ?>
                    <p class="bdt-hours-note">
                        <?= htmlspecialchars($hourNote) ?>
                    </p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     KOLEKSI TERBARU
     ============================================================ -->
<?php if (!empty($bookList)) : ?>
<section class="bdt-section" id="bdt-lib-books" aria-label="Koleksi Terbaru">
    <div class="bdt-container">

        <div class="bdt-section__header">
            <div>
                <h2 class="bdt-section__title">Koleksi Terbaru</h2>
                <p class="bdt-section__subtitle">
                    Buku-buku baru yang bisa diakses tiap minggunya.
                </p>
            </div>
            <a href="<?= $baseUrl ?>/katalog"
               id="bdt-lib-books-see-all"
               class="bdt-section__link-all">
                Lihat Katalog Lengkap
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
        </div>

        <div class="bdt-book-grid bdt-book-grid--detail">
            <?php foreach ($bookList as $bookData) : ?>
                <?php include __DIR__ . '/../../components/book-card.php'; ?>
            <?php endforeach; ?>
        </div>

    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     KEGIATAN MENDATANG
     ============================================================ -->
<?php if (!empty($eventList)) : ?>
<section class="bdt-section bdt-section--gray" id="bdt-lib-events" aria-label="Kegiatan Mendatang">
    <div class="bdt-container">

        <div class="bdt-section__header">
            <div>
                <span class="bdt-section__tag">Agenda</span>
                <h2 class="bdt-section__title">Kegiatan Mendatang</h2>
                <p class="bdt-section__subtitle">
                    Ikuti berbagai kegiatan literasi yang kami selenggarakan setiap bulannya.
                </p>
            </div>
        </div>

        <div class="bdt-events__grid">
            <?php foreach ($eventList as $eventIdx => $eventItem) : ?>
                <article class="bdt-event-card"
                         id="bdt-event-<?= htmlspecialchars($eventItem['id']) ?>">

                    <!-- Event Image -->
                    <div class="bdt-event-card__image-wrap">
                        <img src="<?= htmlspecialchars($baseUrl . $eventItem['image']) ?>"
                             alt="Kegiatan <?= htmlspecialchars($eventItem['judul']) ?>"
                             class="bdt-event-card__image"
                             loading="lazy"
                             width="400" height="225">
                        <span class="bdt-event-card__type bdt-event-card__type--<?= htmlspecialchars($eventItem['tipeCss']) ?>">
                            <?= htmlspecialchars($eventItem['tipe']) ?>
                        </span>
                    </div>

                    <!-- Event Body -->
                    <div class="bdt-event-card__body">
                        <div class="bdt-event-card__datetime">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round"
                                 aria-hidden="true">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <?= htmlspecialchars($eventItem['tanggal']) ?>
                            &bull;
                            <?= htmlspecialchars($eventItem['waktu']) ?>
                        </div>
                        <h3 class="bdt-event-card__title">
                            <?= htmlspecialchars($eventItem['judul']) ?>
                        </h3>
                        <p class="bdt-event-card__desc">
                            <?= htmlspecialchars($eventItem['deskripsi']) ?>
                        </p>
                        <div class="bdt-event-card__footer">
                            <a href="<?= htmlspecialchars($baseUrl . $eventItem['ctaHref']) ?>"
                               id="bdt-event-btn-<?= htmlspecialchars($eventItem['id']) ?>"
                               class="bdt-btn bdt-btn--outline bdt-btn--sm">
                                <?= htmlspecialchars($eventItem['ctaLabel']) ?>
                            </a>
                        </div>
                    </div>

                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     GALLERY — Rasakan Ruang Kami
     ============================================================ -->
<?php if (!empty($galleryList)) : ?>
<section class="bdt-section" id="bdt-lib-gallery" aria-label="Galeri Ruangan">
    <div class="bdt-container">

        <div class="bdt-section__header">
            <div>
                <h2 class="bdt-section__title">Rasakan Ruang Kami</h2>
                <p class="bdt-section__subtitle">
                    Temukan suasana belajar yang nyaman, inspiratif, dan menyenangkan bersama kami.
                </p>
            </div>
        </div>

        <div class="bdt-gallery__grid">

            <!-- Gambar utama (kiri, span 2 baris) -->
            <div class="bdt-gallery__item bdt-gallery__item--main"
                 id="bdt-gallery-main">
                <img src="<?= htmlspecialchars($baseUrl . ($galleryList[0]['image'] ?? '')) ?>"
                     alt="<?= htmlspecialchars($galleryList[0]['alt'] ?? 'Galeri perpustakaan') ?>"
                     class="bdt-gallery__img"
                     loading="lazy">
            </div>

            <!-- Gambar kanan atas -->
            <?php if (!empty($galleryList[1])) : ?>
            <div class="bdt-gallery__item bdt-gallery__item--top-right"
                 id="bdt-gallery-2">
                <img src="<?= htmlspecialchars($baseUrl . $galleryList[1]['image']) ?>"
                     alt="<?= htmlspecialchars($galleryList[1]['alt']) ?>"
                     class="bdt-gallery__img"
                     loading="lazy">
            </div>
            <?php endif; ?>

            <!-- Gambar kanan bawah (2 kolom kecil) -->
            <div class="bdt-gallery__item bdt-gallery__item--bottom-right">
                <?php if (!empty($galleryList[2])) : ?>
                <div class="bdt-gallery__sub" id="bdt-gallery-3">
                    <img src="<?= htmlspecialchars($baseUrl . $galleryList[2]['image']) ?>"
                         alt="<?= htmlspecialchars($galleryList[2]['alt']) ?>"
                         class="bdt-gallery__img"
                         loading="lazy">
                </div>
                <?php endif; ?>
                <?php if (!empty($galleryList[3])) : ?>
                <div class="bdt-gallery__sub" id="bdt-gallery-4"
                     style="position:relative;">
                    <img src="<?= htmlspecialchars($baseUrl . $galleryList[3]['image']) ?>"
                         alt="<?= htmlspecialchars($galleryList[3]['alt']) ?>"
                         class="bdt-gallery__img"
                         loading="lazy">
                    <?php if (count($galleryList) > 4) : ?>
                        <a href="<?= $baseUrl ?>/galeri/<?= htmlspecialchars($librarySlug) ?>"
                           class="bdt-gallery__count-overlay"
                           id="bdt-gallery-more"
                           aria-label="Lihat <?= count($galleryList) - 4 ?> foto lainnya">
                            +<?= count($galleryList) - 4 ?> Foto
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     LOKASI
     ============================================================ -->
<?php if (!empty($locationData)) : ?>
<section class="bdt-section bdt-section--gray" id="bdt-lib-location" aria-label="Lokasi Perpustakaan">
    <div class="bdt-container">
        <div class="bdt-location__grid">

            <!-- Info Lokasi -->
            <div class="bdt-location__content">
                <span class="bdt-section__tag">Lokasi</span>
                <h2 class="bdt-section__title">Temukan Kami di Sini</h2>
                <p class="bdt-section__subtitle" style="margin-bottom:32px;">
                    Terletak strategis di jantung Desa Teras, mudah diakses dengan berbagai transportasi.
                </p>

                <ul class="bdt-location__list">

                    <li class="bdt-location__item" id="bdt-location-alamat">
                        <div class="bdt-location__item-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div>
                            <p class="bdt-location__item-label">Alamat</p>
                            <p class="bdt-location__item-value">
                                <?= htmlspecialchars($locationData['alamat']) ?>
                            </p>
                        </div>
                    </li>

                    <li class="bdt-location__item" id="bdt-location-transport">
                        <div class="bdt-location__item-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="3" width="15" height="13"/>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                                <circle cx="5.5" cy="18.5" r="2.5"/>
                                <circle cx="18.5" cy="18.5" r="2.5"/>
                            </svg>
                        </div>
                        <div>
                            <p class="bdt-location__item-label">Transportasi</p>
                            <p class="bdt-location__item-value">
                                <?= htmlspecialchars($locationData['transportasi']) ?>
                            </p>
                        </div>
                    </li>

                </ul>

                <a href="<?= htmlspecialchars($locationData['mapsLink']) ?>"
                   id="bdt-location-maps-btn"
                   class="bdt-btn bdt-btn--primary"
                   target="_blank"
                   rel="noopener noreferrer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true">
                        <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                    </svg>
                    Petunjuk Arah
                </a>
            </div>

            <!-- Google Maps Embed -->
            <div class="bdt-location__map" id="bdt-location-map">
                <iframe
                    src="<?= htmlspecialchars($locationData['mapsEmbed']) ?>"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Peta Lokasi <?= htmlspecialchars($libName) ?>">
                </iframe>
            </div>

        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/../../components/footer.php'; ?>

</body>
</html>
