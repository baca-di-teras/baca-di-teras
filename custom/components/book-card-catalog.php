<?php
/**
 * Book Card Catalog Component
 *
 * File    : book-card-catalog.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Versi card buku untuk halaman katalog — menampilkan ketersediaan,
 * stok, dan bookmark. Memperluas book-card.php dengan field tambahan.
 *
 * Usage:
 *   $bookData = [
 *       'id'            => 'digital-teras',
 *       'title'         => 'Digital Teras: Navigating the Web',
 *       'author'        => 'Dr. Ahmad Santoso',
 *       'category'      => 'Teknologi',
 *       'badge'         => 'Baru',         // 'Baru' | 'Populer' | null
 *       'image'         => '/custom/assets/images/book-cover-1.png',
 *       'href'          => '/katalog/digital-teras',
 *       'penerbit'      => 'Gramedia',
 *       'perpustakaan'  => 'perpustakaan-utama',
 *       'ketersediaan'  => 'tersedia',     // 'tersedia' | 'dipesan' | 'dipinjam'
 *       'stok'          => 4,              // jumlah eksemplar tersedia
 *       'antrian'       => 0,              // jumlah antrian
 *   ];
 *   include 'custom/components/book-card-catalog.php';
 */

// Guard: pastikan variabel wajib tersedia
if (!isset($bookData) || !is_array($bookData)) {
    return;
}

// Destructure — support output BookService + kompatibilitas data lama
$bookId           = $bookData['id']           ?? 'book';
$bookTitle        = $bookData['title']        ?? 'Judul Buku';
$bookAuthor       = $bookData['author']       ?? '';
$bookCategory     = $bookData['category']     ?? 'Fiksi'; // Mock kategori
$bookBadge        = $bookData['badge']        ?? null;
$bookImage        = $bookData['image']        ?? '';
$bookHref         = $bookData['href']         ?? '#';
$bookPenerbit     = $bookData['publisher']    ?? $bookData['penerbit'] ?? '';
$bookPerpustakaan = $bookData['perpustakaan'] ?? '';
$bookKetersediaan = $bookData['ketersediaan'] ?? 'tersedia';
$bookStok         = $bookData['stok']         ?? 1;
$bookAntrian      = $bookData['antrian']      ?? 0;
$baseUrl          = defined('BASE_URL') ? BASE_URL : '';

// Handle URL gambar yang mungkin sudah lengkap (dari BookService)
$bookImageSrc = (str_starts_with($bookImage, 'http') || str_starts_with($bookImage, '/'))
    ? $bookImage
    : $baseUrl . $bookImage;

// Badge CSS class
$badgeClass = '';
if ($bookBadge === 'Baru') {
    $badgeClass = 'bdt-book-card__badge--new';
} elseif ($bookBadge === 'Populer') {
    $badgeClass = 'bdt-book-card__badge--popular';
}

// Status ketersediaan
$statusConfig = [
    'tersedia' => [
        'dotClass'  => 'bdt-catalog-card__status-dot--available',
        'label'     => $bookStok > 0 ? "Tersedia ({$bookStok} eksemplar)" : 'Tersedia',
        'ctaLabel'  => 'Pinjam',
        'ctaClass'  => 'bdt-catalog-card__cta--pinjam',
    ],
    'dipesan'  => [
        'dotClass'  => 'bdt-catalog-card__status-dot--reserved',
        'label'     => $bookAntrian > 0 ? "{$bookAntrian} Dipesan" : 'Dipesan',
        'ctaLabel'  => 'Pinjam',
        'ctaClass'  => 'bdt-catalog-card__cta--pinjam',
    ],
    'dipinjam' => [
        'dotClass'  => 'bdt-catalog-card__status-dot--borrowed',
        'label'     => 'Dipinjam',
        'ctaLabel'  => 'Ikut Antrean',
        'ctaClass'  => 'bdt-catalog-card__cta--antri',
    ],
];

$statusInfo = $statusConfig[$bookKetersediaan] ?? $statusConfig['tersedia'];

// Data attributes untuk JavaScript filter
$dataAttrs  = 'data-category="' . htmlspecialchars($bookCategory) . '"';
$dataAttrs .= ' data-perpustakaan="' . htmlspecialchars($bookPerpustakaan) . '"';
$dataAttrs .= ' data-ketersediaan="' . htmlspecialchars($bookKetersediaan) . '"';
$dataAttrs .= ' data-penerbit="' . htmlspecialchars($bookPenerbit) . '"';
$dataAttrs .= ' data-title="' . htmlspecialchars(strtolower($bookTitle)) . '"';
$dataAttrs .= ' data-author="' . htmlspecialchars(strtolower($bookAuthor)) . '"';
?>

<article class="bdt-catalog-card"
         id="bdt-catalog-card-<?= htmlspecialchars($bookId) ?>"
         <?= $dataAttrs ?>>

    <!-- Cover -->
    <div class="bdt-catalog-card__cover-wrap">
        <img src="<?= htmlspecialchars($bookImageSrc) ?>"
             alt="Sampul buku <?= htmlspecialchars($bookTitle) ?>"
             class="bdt-catalog-card__cover"
             loading="lazy"
             width="240"
             height="320">

        <?php if ($bookBadge && $badgeClass) : ?>
            <span class="bdt-book-card__badge <?= $badgeClass ?>">
                <?= htmlspecialchars($bookBadge) ?>
            </span>
        <?php endif; ?>

        <!-- Bookmark button -->
        <button class="bdt-catalog-card__bookmark"
                id="bdt-bookmark-<?= htmlspecialchars($bookId) ?>"
                aria-label="Simpan buku <?= htmlspecialchars($bookTitle) ?>"
                title="Simpan ke daftar bacaan">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
            </svg>
        </button>
    </div>

    <!-- Body -->
    <div class="bdt-catalog-card__body">
        <h3 class="bdt-catalog-card__title">
            <a href="<?= htmlspecialchars($baseUrl . $bookHref) ?>"
               id="bdt-catalog-title-<?= htmlspecialchars($bookId) ?>">
                <?= htmlspecialchars($bookTitle) ?>
            </a>
        </h3>

        <?php if ($bookAuthor) : ?>
            <p class="bdt-catalog-card__author">
                oleh <?= htmlspecialchars($bookAuthor) ?>
            </p>
        <?php endif; ?>

        <!-- Status Ketersediaan + CTA -->
        <div class="bdt-catalog-card__footer">
            <div class="bdt-catalog-card__status">
                <span class="bdt-catalog-card__status-dot <?= $statusInfo['dotClass'] ?>"
                      aria-hidden="true"></span>
                <span class="bdt-catalog-card__status-label">
                    <?= htmlspecialchars($statusInfo['label']) ?>
                </span>
            </div>
            <a href="<?= htmlspecialchars($baseUrl . $bookHref) ?>"
               id="bdt-catalog-cta-<?= htmlspecialchars($bookId) ?>"
               class="bdt-catalog-card__cta <?= $statusInfo['ctaClass'] ?>">
                <?= htmlspecialchars($statusInfo['ctaLabel']) ?>
            </a>
        </div>
    </div>

</article>
