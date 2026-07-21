<?php
/**
 * Library Card Component
 *
 * File    : library-card.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Usage:
 *   $libraryData = [
 *       'id'          => 'perpustakaan-utama',
 *       'name'        => 'Perpustakaan Utama Desa',
 *       'address'     => 'Jl. Raya Teras No. 1',
 *       'image'       => '/custom/assets/images/library-1.png',
 *       'badge'       => 'Unggulan',
 *       'totalBuku'   => '3.200 Koleksi',
 *       'href'        => '/perpustakaan/perpustakaan-utama',
 *   ];
 *   include 'custom/components/library-card.php';
 */

// Guard: pastikan variabel wajib tersedia
if (!isset($libraryData) || !is_array($libraryData)) {
    return;
}

// Destructure — support key DB (slug, thumbnail_image, total_koleksi)
// sekaligus backward-compatible dengan key lama (id, image, totalBuku)
$cardId        = $libraryData['slug']            ?? $libraryData['id']        ?? 'library';
$cardName      = $libraryData['name']            ?? 'Perpustakaan';
$cardAddress   = $libraryData['address']         ?? '';
$cardImage     = $libraryData['thumbnail_image'] ?? $libraryData['image']     ?? '';
$cardBadge     = $libraryData['badge']           ?? null;
$cardTotalBuku = isset($libraryData['total_koleksi'])
    ? number_format((int)$libraryData['total_koleksi'], 0, ',', '.') . ' Koleksi'
    : ($libraryData['totalBuku'] ?? '');
$cardHref      = $libraryData['href']            ?? BASE_URL . '/perpustakaan/' . $cardId;
$baseUrl       = defined('BASE_URL') ? BASE_URL : '';
?>

<article class="bdt-library-card" id="bdt-library-card-<?= htmlspecialchars($cardId) ?>">

    <!-- Card Image -->
    <div class="bdt-library-card__image-wrap">
        <img src="<?= htmlspecialchars($baseUrl . $cardImage) ?>"
             alt="Foto <?= htmlspecialchars($cardName) ?>"
             class="bdt-library-card__image"
             loading="lazy"
             width="400"
             height="250">
        <?php if ($cardBadge) : ?>
            <span class="bdt-library-card__badge">
                <?= htmlspecialchars($cardBadge) ?>
            </span>
        <?php endif; ?>
    </div>

    <!-- Card Body -->
    <div class="bdt-library-card__body">
        <h3 class="bdt-library-card__name">
            <?= htmlspecialchars($cardName) ?>
        </h3>

        <?php if ($cardAddress) : ?>
            <p class="bdt-library-card__address">
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     aria-hidden="true">
                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <?= htmlspecialchars($cardAddress) ?>
            </p>
        <?php endif; ?>

        <!-- Card Footer -->
        <div class="bdt-library-card__footer">
            <?php if ($cardTotalBuku) : ?>
                <span class="bdt-library-card__meta">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         aria-hidden="true">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                    <?= htmlspecialchars($cardTotalBuku) ?>
                </span>
            <?php endif; ?>

            <a href="<?= htmlspecialchars($baseUrl . $cardHref) ?>"
               id="bdt-library-link-<?= htmlspecialchars($cardId) ?>"
               class="bdt-library-card__link"
               aria-label="Lihat detail <?= htmlspecialchars($cardName) ?>">
                Lihat perpustakaan
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="14" height="14"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2.5"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
        </div>
    </div>

</article>
