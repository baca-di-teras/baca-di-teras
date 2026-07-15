<?php
/**
 * Catalog Page
 *
 * File    : catalog.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Halaman katalog buku dengan filter sidebar (kategori, perpustakaan,
 * ketersediaan, penerbit), pencarian, pengurutan, dan pagination.
 * Semua filter ditangani client-side dengan JavaScript.
 *
 * Data dimuat dari config/catalog-config.php.
 */

// BASE_URL sudah didefinisikan di index.php (Front Controller).
// Definisikan hanya jika file ini diakses langsung (tanpa router).
if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

// ── Service Layer ──────────────────────────────────────────────
$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/helpers/Database.php';
require_once $libPath . '/custom/services/BookService.php';
require_once $libPath . '/custom/services/LibraryService.php';

$bookService    = new BookService();
$libraryService = new LibraryService();

$baseUrl = defined('BASE_URL') ? BASE_URL : '';

$searchQuery    = trim((string) ($_GET['q'] ?? ''));
$categoryFilter = $_GET['kategori'] ?? [];
$categoryFilter = is_array($categoryFilter) ? $categoryFilter : [$categoryFilter];
$categoryFilter = array_values(array_filter(array_map('intval', $categoryFilter)));
$locationFilter = trim((string) ($_GET['location'] ?? ''));
$statusFilter   = $_GET['status'] ?? [];
$statusFilter   = is_array($statusFilter) ? $statusFilter : [$statusFilter];
$statusFilter   = array_values(array_intersect(array_map('strval', $statusFilter), ['tersedia', 'dipesan', 'dipinjam']));
$publisherFilter = max(0, (int) ($_GET['publisher'] ?? 0));
$sortOrder       = (string) ($_GET['sort'] ?? 'terbaru');
if (!in_array($sortOrder, ['terbaru', 'terpopuler', 'a-z', 'z-a'], true)) {
    $sortOrder = 'terbaru';
}

$itemsPerPage = 12;
$page         = max(1, (int) ($_GET['halaman'] ?? 1));
$filters      = [
    'q'         => $searchQuery,
    'category'  => $categoryFilter,
    'location'  => $locationFilter,
    'status'    => $statusFilter,
    'publisher' => $publisherFilter,
    'sort'      => $sortOrder,
];

$totalBuku = $bookService->countCatalog($filters);
$maxPage   = max(1, (int) ceil($totalBuku / $itemsPerPage));
$page      = min($page, $maxPage);
$offset    = ($page - 1) * $itemsPerPage;

$catalogBookList = $bookService->getCatalog($filters, $itemsPerPage, $offset);
$visibleStart    = $totalBuku === 0 ? 0 : $offset + 1;
$visibleEnd      = min($offset + count($catalogBookList), $totalBuku);

// Ambil daftar perpustakaan untuk filter
$libraries = $libraryService->getAllActive();
$libraryFilterList = [['id' => '', 'label' => 'Semua Perpustakaan']];
foreach ($libraries as $lib) {
    $libraryFilterList[] = ['id' => $lib['slims_location_id'], 'label' => $lib['name']];
}

// Daftar Kategori dari database
$dbCategories = $bookService->getCategories();
$categoryList = array_merge([['id' => 'semua', 'label' => 'Semua Kategori']], $dbCategories);

// Daftar Penerbit dari database
$dbPublishers = $bookService->getPublishers();
$publisherList = array_merge([['id' => 'semua', 'label' => 'Semua Penerbit']], $dbPublishers);

// Opsi Urutan
$sortOptionList = [
    ['id' => 'terbaru', 'label' => 'Terbaru'],
    ['id' => 'terpopuler', 'label' => 'Terpopuler'],
    ['id' => 'a-z', 'label' => 'A - Z'],
    ['id' => 'z-a', 'label' => 'Z - A'],
];

$activePage        = 'perpustakaan';
$pageTitle         = 'Katalog Buku';
$pageDescription   = 'Jelajahi ribuan koleksi buku perpustakaan digital Desa Teras. Filter berdasarkan kategori, ketersediaan, penerbit, dan perpustakaan.';

if (!function_exists('bdt_catalog_url')) {
    function bdt_catalog_url(array $overrides = []): string
    {
        global $baseUrl, $searchQuery, $categoryFilter, $locationFilter, $statusFilter, $publisherFilter, $sortOrder;

        $query = [
            'q'         => $searchQuery,
            'kategori'  => $categoryFilter,
            'location'  => $locationFilter,
            'status'    => $statusFilter,
            'publisher' => $publisherFilter ?: '',
            'sort'      => $sortOrder !== 'terbaru' ? $sortOrder : '',
        ];

        foreach ($overrides as $key => $value) {
            $query[$key] = $value;
        }

        $query = array_filter($query, static function ($value): bool {
            return !(is_array($value) ? count($value) === 0 : $value === '' || $value === null);
        });

        $queryString = http_build_query($query);
        return $baseUrl . '/katalog' . ($queryString ? '?' . $queryString : '');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <title><?= htmlspecialchars($pageTitle) ?> – Baca Di Teras</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/custom/assets/css/catalog.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/custom/assets/css/landing.css">
</head>
<body>

<?php include __DIR__ . '/../../components/navbar.php'; ?>

<!-- ============================================================
     HERO / SEARCH
     ============================================================ -->
<section class="bdt-catalog-hero" id="bdt-catalog-hero" aria-label="Pencarian Katalog">
    <h1 class="bdt-catalog-hero__title">
        Jelajahi Perpustakaan Digital Desa Kami
    </h1>

    <form class="bdt-catalog-search"
          id="bdt-catalog-search-form"
          action="<?= $baseUrl ?>/katalog"
          method="get"
          role="search"
          aria-label="Cari buku">
        <div class="bdt-catalog-search__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </div>
        <input type="search"
               id="bdt-search-input"
               name="q"
               class="bdt-catalog-search__input"
               placeholder="Cari berdasarkan judul, penulis, atau ISBN..."
               value="<?= htmlspecialchars($searchQuery) ?>"
               autocomplete="off"
               aria-label="Kata kunci pencarian">
        <button type="submit"
                id="bdt-search-btn"
                class="bdt-catalog-search__btn">
            Cari
        </button>
    </form>
</section>

<!-- ============================================================
     MAIN LAYOUT
     ============================================================ -->
<main id="bdt-catalog-main">
    <div class="bdt-container">
        <div class="bdt-catalog-layout">

            <!-- ── SIDEBAR FILTER ──────────────────────────── -->
            <form class="bdt-catalog-sidebar"
                   id="bdt-catalog-sidebar"
                   method="get"
                   action="<?= $baseUrl ?>/katalog"
                   aria-label="Filter Katalog">
                <?php if ($searchQuery !== '') : ?>
                    <input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery) ?>">
                <?php endif; ?>
                <?php if ($sortOrder !== 'terbaru') : ?>
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sortOrder) ?>">
                <?php endif; ?>

                <div class="bdt-sidebar__header">
                    <h2 class="bdt-sidebar__title">Filter</h2>
                    <a class="bdt-sidebar__reset"
                            id="bdt-filter-reset"
                            href="<?= $baseUrl ?>/katalog"
                            aria-label="Atur ulang semua filter">
                        Atur Ulang
                    </a>
                </div>

                <!-- KATEGORI -->
                <div class="bdt-filter-group" id="bdt-filter-kategori">
                    <span class="bdt-filter-group__label">Kategori</span>
                    <?php foreach ($categoryList as $catItem) : ?>
                        <label class="bdt-filter-checkbox"
                               for="bdt-cat-<?= htmlspecialchars($catItem['id']) ?>">
                            <input type="checkbox"
                                   id="bdt-cat-<?= htmlspecialchars($catItem['id']) ?>"
                                   name="kategori[]"
                                   value="<?= htmlspecialchars($catItem['id']) ?>"
                                   <?= in_array((int) $catItem['id'], $categoryFilter, true) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($catItem['label']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <!-- PERPUSTAKAAN -->
                <div class="bdt-filter-group" id="bdt-filter-perpustakaan">
                    <span class="bdt-filter-group__label">Perpustakaan</span>
                    <select class="bdt-filter-select bdt-js-filter-library"
                            id="bdt-filter-library-select"
                            name="location"
                            aria-label="Filter berdasarkan perpustakaan">
                        <?php foreach ($libraryFilterList as $libOption) : ?>
                            <option value="<?= htmlspecialchars($libOption['id']) ?>"
                                    <?= $locationFilter === (string) $libOption['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($libOption['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- KETERSEDIAAN -->
                <div class="bdt-filter-group" id="bdt-filter-ketersediaan">
                    <span class="bdt-filter-group__label">Ketersediaan</span>
                    <div class="bdt-filter-pills" role="group" aria-label="Filter ketersediaan">
                        <label class="bdt-filter-pill <?= in_array('tersedia', $statusFilter, true) ? 'is-active' : '' ?>"
                               id="bdt-pill-tersedia">
                            <input type="checkbox" name="status[]" value="tersedia" <?= in_array('tersedia', $statusFilter, true) ? 'checked' : '' ?>>
                            Tersedia Sekarang
                        </label>
                        <label class="bdt-filter-pill <?= in_array('dipesan', $statusFilter, true) ? 'is-active' : '' ?>"
                               id="bdt-pill-dipesan">
                            <input type="checkbox" name="status[]" value="dipesan" <?= in_array('dipesan', $statusFilter, true) ? 'checked' : '' ?>>
                            Dipesan
                        </label>
                        <label class="bdt-filter-pill <?= in_array('dipinjam', $statusFilter, true) ? 'is-active' : '' ?>"
                               id="bdt-pill-dipinjam">
                            <input type="checkbox" name="status[]" value="dipinjam" <?= in_array('dipinjam', $statusFilter, true) ? 'checked' : '' ?>>
                            Dipinjam
                        </label>
                    </div>
                </div>

                <!-- PENERBIT -->
                <div class="bdt-filter-group" id="bdt-filter-penerbit">
                    <span class="bdt-filter-group__label">Penerbit</span>
                    <?php foreach ($publisherList as $pubItem) : ?>
                        <label class="bdt-filter-radio"
                               for="bdt-pub-<?= htmlspecialchars($pubItem['id']) ?>">
                            <input type="radio"
                                   id="bdt-pub-<?= htmlspecialchars($pubItem['id']) ?>"
                                   name="publisher"
                                   value="<?= htmlspecialchars($pubItem['id']) ?>"
                                   <?= (int) $pubItem['id'] === $publisherFilter ? 'checked' : '' ?>>
                            <?= htmlspecialchars($pubItem['label']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <button type="submit" class="bdt-catalog-search__btn bdt-catalog-filter-submit">
                    Terapkan Filter
                </button>

            </form>

            <!-- ── MAIN CONTENT ───────────────────────────── -->
            <div class="bdt-catalog-main">

                <!-- Topbar -->
                <div class="bdt-catalog-topbar" id="bdt-catalog-topbar">
                    <p class="bdt-catalog-topbar__info" id="bdt-catalog-count" aria-live="polite">
                        Menampilkan <strong id="bdt-count-visible"><?= $visibleStart ?>-<?= $visibleEnd ?></strong>
                        dari <strong><?= number_format($totalBuku, 0, ',', '.') ?></strong> buku
                    </p>

                    <form class="bdt-catalog-topbar__sort" action="<?= $baseUrl ?>/katalog" method="get">
                        <?php if ($searchQuery !== '') : ?>
                            <input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery) ?>">
                        <?php endif; ?>
                        <?php foreach ($categoryFilter as $categoryId) : ?>
                            <input type="hidden" name="kategori[]" value="<?= (int) $categoryId ?>">
                        <?php endforeach; ?>
                        <?php if ($locationFilter !== '') : ?>
                            <input type="hidden" name="location" value="<?= htmlspecialchars($locationFilter) ?>">
                        <?php endif; ?>
                        <?php foreach ($statusFilter as $statusValue) : ?>
                            <input type="hidden" name="status[]" value="<?= htmlspecialchars($statusValue) ?>">
                        <?php endforeach; ?>
                        <?php if ($publisherFilter > 0) : ?>
                            <input type="hidden" name="publisher" value="<?= (int) $publisherFilter ?>">
                        <?php endif; ?>
                        <label class="bdt-catalog-topbar__sort-label"
                               for="bdt-sort-select">
                            Urutkan:
                        </label>
                        <select class="bdt-catalog-topbar__sort-select"
                                name="sort"
                                id="bdt-sort-select"
                                onchange="this.form.submit()"
                                aria-label="Urutkan buku">
                            <?php foreach ($sortOptionList as $sortOption) : ?>
                                <option value="<?= htmlspecialchars($sortOption['id']) ?>"
                                        <?= $sortOrder === $sortOption['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sortOption['label']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <!-- Book Grid -->
                <div class="bdt-catalog-grid"
                     id="bdt-catalog-grid"
                     role="list"
                     aria-label="Daftar buku">

                    <?php foreach ($catalogBookList as $bookData) : ?>
                        <?php include __DIR__ . '/../../components/book-card-catalog.php'; ?>
                    <?php endforeach; ?>

                    <!-- Empty state -->
                    <?php if (empty($catalogBookList)) : ?>
                    <div class="bdt-catalog-empty" id="bdt-catalog-empty" aria-live="polite">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.5"
                             stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                        <p class="bdt-catalog-empty__title">Buku tidak ditemukan</p>
                        <p class="bdt-catalog-empty__desc">
                            Coba ubah kata kunci atau reset filter pencarian.
                        </p>
                    </div>
                    <?php endif; ?>

                </div>

                <!-- Pagination -->
                <nav class="bdt-pagination"
                     id="bdt-pagination"
                     aria-label="Halaman katalog">
                    <?php if ($maxPage > 1) : ?>
                        <?php if ($page > 1) : ?>
                            <a class="bdt-pagination__btn" id="bdt-page-prev" href="<?= htmlspecialchars(bdt_catalog_url(['halaman' => $page - 1])) ?>" aria-label="Halaman sebelumnya">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php
                            $pageStart = max(1, $page - 2);
                            $pageEnd   = min($maxPage, $page + 2);
                        ?>
                        <?php if ($pageStart > 1) : ?>
                            <a class="bdt-pagination__btn" href="<?= htmlspecialchars(bdt_catalog_url(['halaman' => 1])) ?>">1</a>
                            <?php if ($pageStart > 2) : ?><span class="bdt-pagination__dots">...</span><?php endif; ?>
                        <?php endif; ?>
                        <?php for ($pageNumber = $pageStart; $pageNumber <= $pageEnd; $pageNumber++) : ?>
                            <a class="bdt-pagination__btn <?= $pageNumber === $page ? 'is-active' : '' ?>"
                               href="<?= htmlspecialchars(bdt_catalog_url(['halaman' => $pageNumber])) ?>"
                               <?= $pageNumber === $page ? 'aria-current="page"' : '' ?>>
                                <?= $pageNumber ?>
                            </a>
                        <?php endfor; ?>
                        <?php if ($pageEnd < $maxPage) : ?>
                            <?php if ($pageEnd < $maxPage - 1) : ?><span class="bdt-pagination__dots">...</span><?php endif; ?>
                            <a class="bdt-pagination__btn" href="<?= htmlspecialchars(bdt_catalog_url(['halaman' => $maxPage])) ?>"><?= $maxPage ?></a>
                        <?php endif; ?>
                        <?php if ($page < $maxPage) : ?>
                            <a class="bdt-pagination__btn" id="bdt-page-next" href="<?= htmlspecialchars(bdt_catalog_url(['halaman' => $page + 1])) ?>" aria-label="Halaman berikutnya">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </nav>

            </div>
            <!-- / .bdt-catalog-main -->

        </div>
    </div>
</main>

<?php include __DIR__ . '/../../components/footer.php'; ?>

<!-- ============================================================
     JAVASCRIPT — Bookmark visual toggle
     ============================================================ -->
<script>
(function () {
    'use strict';

    // Bookmark toggle (visual only — no persistence yet)
    const gridEl = document.getElementById('bdt-catalog-grid');
    if (!gridEl) return;
    gridEl.addEventListener('click', function (e) {
        const btn = e.target.closest('.bdt-catalog-card__bookmark');
        if (btn) {
            btn.classList.toggle('is-saved');
            const isSaved  = btn.classList.contains('is-saved');
            btn.setAttribute('aria-label', isSaved ? 'Hapus dari daftar bacaan' : btn.getAttribute('aria-label'));
            const path = btn.querySelector('svg path');
            if (path) path.setAttribute('fill', isSaved ? 'currentColor' : 'none');
        }
    });
}());
</script>

</body>
</html>
