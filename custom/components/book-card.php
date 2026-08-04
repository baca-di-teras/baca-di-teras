<?php
/**
 * Book Card Component
 *
 * File    : book-card.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Usage:
 *   $bookData = [
 *       'id'       => 'untaian-kisah-lembut',
 *       'title'    => 'Untaian Kisah Lembut',
 *       'author'   => 'Rina Sari',
 *       'category' => 'Fiksi',
 *       'badge'    => 'Baru',        // 'Baru' | 'Populer' | null
 *       'image'    => '/custom/assets/images/book-cover-1.png',
 *       'href'     => '/katalog/untaian-kisah-lembut',
 *   ];
 *   include 'custom/components/book-card.php';
 */

// Guard: pastikan variabel wajib tersedia
if (!isset($bookData) || !is_array($bookData)) {
    return;
}

// Destructure dengan default value
// BookService mengembalikan 'image' sebagai URL penuh, bukan path relatif
$bookId       = $bookData['id']       ?? 'book';
$bookTitle    = $bookData['title']    ?? 'Judul Buku';
$bookAuthor   = $bookData['author']   ?? '';
$bookCategory = $bookData['category'] ?? '';
$bookBadge    = $bookData['badge']    ?? null;
$bookImage    = $bookData['image']    ?? '';
$bookHref     = $bookData['href']     ?? '#';
$baseUrl      = defined('BASE_URL') ? BASE_URL : '';

if (!function_exists('bdt_book_card_url')) {
    function bdt_book_card_url(string $href, string $baseUrl): string
    {
        if ($href === '#') {
            return $href;
        }
        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
            return $href;
        }
        $baseUrl = rtrim($baseUrl, '/');
        if ($baseUrl !== '' && ($href === $baseUrl || str_starts_with($href, $baseUrl . '/'))) {
            return $href;
        }
        return $baseUrl . '/' . ltrim($href, '/');
    }
}

$bookHrefUrl = bdt_book_card_url((string) $bookHref, $baseUrl);

// Jika image sudah URL penuh (http/https atau path /baca-di-teras/slims/...)
// jangan prefiks baseUrl lagi
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
?>

<article class="bdt-book-card" id="bdt-book-card-<?= htmlspecialchars($bookId) ?>">

    <!-- Book Cover -->
    <div class="bdt-book-card__cover-wrap">
        <img src="<?= htmlspecialchars($bookImageSrc) ?>"
             alt="Sampul buku <?= htmlspecialchars($bookTitle) ?>"
             class="bdt-book-card__cover"
             loading="lazy"
             width="200"
             height="300">
        <?php if ($bookBadge && $badgeClass) : ?>
            <span class="bdt-book-card__badge <?= $badgeClass ?>">
                <?= htmlspecialchars($bookBadge) ?>
            </span>
        <?php endif; ?>
    </div>

    <!-- Book Body -->
    <div class="bdt-book-card__body">
        <?php if ($bookCategory) : ?>
            <p class="bdt-book-card__category">
                <?= htmlspecialchars($bookCategory) ?>
            </p>
        <?php endif; ?>

        <h3 class="bdt-book-card__title">
            <a href="<?= htmlspecialchars($bookHrefUrl) ?>"
               id="bdt-book-title-<?= htmlspecialchars($bookId) ?>"
               style="color: inherit; text-decoration: none;">
                <?= htmlspecialchars($bookTitle) ?>
            </a>
        </h3>

        <?php if ($bookAuthor) : ?>
            <p class="bdt-book-card__author">
                <?= htmlspecialchars($bookAuthor) ?>
            </p>
        <?php endif; ?>

        <a href="<?= htmlspecialchars($bookHrefUrl) ?>"
           id="bdt-book-link-<?= htmlspecialchars($bookId) ?>"
           class="bdt-book-card__link"
           aria-label="Pinjam buku <?= htmlspecialchars($bookTitle) ?>">
            Pinjam Buku
            <svg xmlns="http://www.w3.org/2000/svg"
                 width="12" height="12"
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

</article>
