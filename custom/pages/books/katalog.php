<?php
/**
 * Katalog Buku Page – Baca Di Teras
 *
 * File    : books/katalog.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Halaman katalog/daftar koleksi buku Desa Teras sesuai desain mockup.
 */

define('BASE_URL', '/baca-di-teras');

$activePage = 'katalog';

// Load config data
require_once __DIR__ . '/../../config/book-config.php';

// Filter logic
$searchQuery = $_GET['search'] ?? '';
$selectedCategories = $_GET['categories'] ?? []; // Array of categories
$selectedLibrary = $_GET['library'] ?? 'semua';
$selectedAvailability = $_GET['availability'] ?? 'semua';
$selectedPublisher = $_GET['publisher'] ?? 'semua';

// Reset/Atur Ulang Helper
if (isset($_GET['reset'])) {
    header("Location: " . BASE_URL . "/custom/pages/books/katalog.php");
    exit;
}

// Filter book list
$filteredBooks = array_filter($bookList, function($book) use ($searchQuery, $selectedCategories, $selectedLibrary, $selectedAvailability, $selectedPublisher) {
    // 1. Search Query
    if (!empty($searchQuery)) {
        $titleMatch = stripos($book['title'], $searchQuery) !== false;
        $authorMatch = stripos($book['author'], $searchQuery) !== false;
        if (!$titleMatch && !$authorMatch) {
            return false;
        }
    }

    // 2. Categories (Multiple checkboxes)
    if (!empty($selectedCategories)) {
        if (!in_array($book['category'], $selectedCategories)) {
            return false;
        }
    }

    // 3. Library
    if ($selectedLibrary !== 'semua' && !empty($selectedLibrary)) {
        if ($book['library'] !== $selectedLibrary) {
            return false;
        }
    }

    // 4. Availability
    if ($selectedAvailability !== 'semua' && !empty($selectedAvailability)) {
        if ($book['status'] !== $selectedAvailability) {
            return false;
        }
    }

    // 5. Publisher
    if ($selectedPublisher !== 'semua' && !empty($selectedPublisher)) {
        if (strtolower($book['publisher']) !== strtolower($selectedPublisher)) {
            return false;
        }
    }

    return true;
});

// Helper count
$totalBooksCount = count($filteredBooks);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cari dan temukan koleksi buku menarik di jaringan perpustakaan Desa Teras. Mulai dari fiksi, teknologi, budaya lokal, hingga pertanian.">
    <title>Katalog Buku – Baca Di Teras</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Main styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/landing.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fcfcfc;
            color: #101814;
            margin: 0;
        }
        
        /* Layout */
        .bdt-catalog-wrapper {
            max-width: 1280px;
            margin: 0 auto;
            padding: 40px 24px 80px;
        }

        /* Hero / Search Section */
        .bdt-catalog-hero {
            text-align: center;
            padding: 48px 24px;
            background-color: #ffffff;
            border-bottom: 1px solid #e8e8e8;
        }
        .bdt-catalog-hero__title {
            font-size: 42px;
            font-weight: 800;
            color: #101814;
            margin-bottom: 32px;
            letter-spacing: -0.02em;
        }
        .bdt-catalog-search {
            max-width: 640px;
            margin: 0 auto;
            position: relative;
            display: flex;
            gap: 12px;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border-radius: 9999px;
            padding: 6px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
        }
        .bdt-catalog-search__input-wrapper {
            position: relative;
            flex: 1;
            display: flex;
            align-items: center;
        }
        .bdt-catalog-search__icon {
            position: absolute;
            left: 18px;
            color: #94a3b8;
            pointer-events: none;
        }
        .bdt-catalog-search__input {
            width: 100%;
            border: none;
            padding: 12px 16px 12px 48px;
            font-size: 15px;
            outline: none;
            border-radius: 9999px;
            color: #334155;
        }
        .bdt-catalog-search__btn {
            background-color: #1a6b2f;
            color: #ffffff;
            border: none;
            padding: 12px 32px;
            font-weight: 700;
            font-size: 15px;
            border-radius: 9999px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .bdt-catalog-search__btn:hover {
            background-color: #134e22;
        }

        /* Two Column Layout */
        .bdt-catalog-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 40px;
            margin-top: 40px;
        }

        /* Sidebar Filter */
        .bdt-filter-sidebar__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }
        .bdt-filter-sidebar__title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }
        .bdt-filter-sidebar__reset {
            font-size: 14px;
            color: #1a6b2f;
            text-decoration: none;
            font-weight: 600;
        }
        .bdt-filter-sidebar__reset:hover {
            text-decoration: underline;
        }
        .bdt-filter-group {
            margin-bottom: 28px;
        }
        .bdt-filter-group__title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 14px;
        }
        .bdt-filter-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            font-size: 14px;
            color: #334155;
            font-weight: 500;
        }
        .bdt-filter-checkbox input {
            width: 16px;
            height: 16px;
            accent-color: #1a6b2f;
            cursor: pointer;
        }
        .bdt-filter-select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            color: #334155;
            background-color: #ffffff;
            outline: none;
            cursor: pointer;
        }
        .bdt-filter-select:focus {
            border-color: #1a6b2f;
        }

        /* Availability Badges style */
        .bdt-filter-availability {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .bdt-availability-btn {
            padding: 8px 14px;
            border-radius: 9999px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .bdt-availability-btn:hover {
            border-color: #cbd5e1;
            color: #334155;
        }
        .bdt-availability-btn--active {
            background-color: #1a6b2f;
            border-color: #1a6b2f;
            color: #ffffff !important;
        }

        /* Right Content Area */
        .bdt-catalog-content__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .bdt-catalog-content__info {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }
        .bdt-catalog-content__sort {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #64748b;
        }
        .bdt-catalog-content__sort select {
            border: none;
            background: transparent;
            font-weight: 700;
            color: #0f172a;
            outline: none;
            cursor: pointer;
        }

        /* Book Grid Layout */
        .bdt-catalog-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 28px;
        }

        /* Premium Card style from Design */
        .bdt-premium-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .bdt-premium-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
        }
        .bdt-premium-card__img-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 3/4;
            background-color: #f8fafc;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .bdt-premium-card__img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .bdt-premium-card__badge {
            position: absolute;
            top: 16px;
            left: 16px;
            background-color: #1a6b2f;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .bdt-premium-card__bookmark {
            position: absolute;
            top: 16px;
            right: 16px;
            background: #ffffff;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            color: #64748b;
            transition: color 0.2s;
        }
        .bdt-premium-card__bookmark:hover {
            color: #1a6b2f;
        }
        .bdt-premium-card__body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .bdt-premium-card__title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 4px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 44px;
        }
        .bdt-premium-card__title a {
            color: inherit;
            text-decoration: none;
        }
        .bdt-premium-card__title a:hover {
            color: #1a6b2f;
        }
        .bdt-premium-card__author {
            font-size: 13px;
            color: #64748b;
            margin: 0 0 16px;
        }
        .bdt-premium-card__footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 14px;
        }
        .bdt-premium-card__status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
        }
        .bdt-premium-card__dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        .bdt-premium-card__dot--tersedia { background-color: #1a6b2f; }
        .bdt-premium-card__dot--dipinjam { background-color: #ef4444; }
        .bdt-premium-card__dot--dipesan { background-color: #f59e0b; }

        .bdt-premium-card__action {
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            color: #1a6b2f;
            transition: color 0.2s;
        }
        .bdt-premium-card__action:hover {
            color: #134e22;
        }
        .bdt-premium-card__action--disabled {
            color: #94a3b8;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Pagination Styles */
        .bdt-pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 48px;
        }
        .bdt-pagination__item {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .bdt-pagination__item:hover {
            border-color: #cbd5e1;
            color: #0f172a;
            background-color: #f8fafc;
        }
        .bdt-pagination__item--active {
            background-color: #1a6b2f;
            color: #ffffff;
            border-color: #1a6b2f;
        }
        .bdt-pagination__item--active:hover {
            background-color: #134e22;
            color: #ffffff;
        }

        /* Empty state */
        .bdt-catalog-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 0;
            color: #64748b;
        }
        .bdt-catalog-empty__icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        .bdt-catalog-empty__title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 8px;
        }

        @media (max-width: 980px) {
            .bdt-catalog-layout {
                grid-template-columns: 1fr;
            }
            .bdt-catalog-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 576px) {
            .bdt-catalog-grid {
                grid-template-columns: 1fr;
            }
            .bdt-catalog-hero__title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../components/navbar.php'; ?>

    <!-- Catalog Hero Section -->
    <header class="bdt-catalog-hero">
        <div class="bdt-catalog-hero__container">
            <h1 class="bdt-catalog-hero__title">Jelajahi Perpustakaan Digital Desa Kami</h1>
            
            <form class="bdt-catalog-search" method="GET" action="">
                <div class="bdt-catalog-search__input-wrapper">
                    <!-- Search Icon (inline SVG) -->
                    <svg class="bdt-catalog-search__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" name="search" class="bdt-catalog-search__input" placeholder="Cari berdasarkan judul, penulis, atau ISBN..." value="<?= htmlspecialchars($searchQuery) ?>">
                </div>
                <button type="submit" class="bdt-catalog-search__btn">Cari</button>
            </form>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="bdt-catalog-wrapper">
        <div class="bdt-catalog-layout">
            
            <!-- Sidebar Filter -->
            <aside class="bdt-filter-sidebar">
                <form id="filterForm" method="GET" action="">
                    <!-- Preserve search query -->
                    <?php if (!empty($searchQuery)) : ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($searchQuery) ?>">
                    <?php endif; ?>

                    <div class="bdt-filter-sidebar__header">
                        <span class="bdt-filter-sidebar__title">Filter</span>
                        <a href="?reset=1" class="bdt-filter-sidebar__reset">Atur Ulang</a>
                    </div>

                    <!-- Category Group -->
                    <div class="bdt-filter-group">
                        <h3 class="bdt-filter-group__title">Kategori</h3>
                        <?php 
                        $allCategories = ['Fiksi', 'Teknologi', 'Budaya Lokal', 'Pertanian', 'Sastra Anak'];
                        foreach ($allCategories as $cat) :
                            $isChecked = in_array($cat, $selectedCategories) ? 'checked' : '';
                        ?>
                            <label class="bdt-filter-checkbox">
                                <input type="checkbox" name="categories[]" value="<?= $cat ?>" <?= $isChecked ?> onchange="this.form.submit()">
                                <?= htmlspecialchars($cat) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Library Group -->
                    <div class="bdt-filter-group">
                        <h3 class="bdt-filter-group__title">Perpustakaan</h3>
                        <select name="library" class="bdt-filter-select" onchange="this.form.submit()">
                            <option value="semua" <?= $selectedLibrary === 'semua' ? 'selected' : '' ?>>Semua Perpustakaan</option>
                            <option value="perpustakaan-utama" <?= $selectedLibrary === 'perpustakaan-utama' ? 'selected' : '' ?>>Perpustakaan Utama</option>
                            <option value="perpustakaan-digital" <?= $selectedLibrary === 'perpustakaan-digital' ? 'selected' : '' ?>>Perpustakaan Digital</option>
                            <option value="agro-perpustakaan" <?= $selectedLibrary === 'agro-perpustakaan' ? 'selected' : '' ?>>Agro-Perpustakaan</option>
                            <option value="sd-negeri-2-teras" <?= $selectedLibrary === 'sd-negeri-2-teras' ? 'selected' : '' ?>>Perpustakaan SD Negeri 2</option>
                            <option value="teras-south-commons" <?= $selectedLibrary === 'teras-south-commons' ? 'selected' : '' ?>>Teras South Commons</option>
                        </select>
                    </div>

                    <!-- Availability Group -->
                    <div class="bdt-filter-group">
                        <h3 class="bdt-filter-group__title">Ketersediaan</h3>
                        <div class="bdt-filter-availability">
                            <a href="?<?= http_build_query(array_merge($_GET, ['availability' => 'semua'])) ?>" class="bdt-availability-btn <?= $selectedAvailability === 'semua' || empty($selectedAvailability) ? 'bdt-availability-btn--active' : '' ?>">Semua</a>
                            <a href="?<?= http_build_query(array_merge($_GET, ['availability' => 'tersedia'])) ?>" class="bdt-availability-btn <?= $selectedAvailability === 'tersedia' ? 'bdt-availability-btn--active' : '' ?>">Tersedia Sekarang</a>
                            <a href="?<?= http_build_query(array_merge($_GET, ['availability' => 'dipesan'])) ?>" class="bdt-availability-btn <?= $selectedAvailability === 'dipesan' ? 'bdt-availability-btn--active' : '' ?>">Dipesan</a>
                            <a href="?<?= http_build_query(array_merge($_GET, ['availability' => 'dipinjam'])) ?>" class="bdt-availability-btn <?= $selectedAvailability === 'dipinjam' ? 'bdt-availability-btn--active' : '' ?>">Dipinjam</a>
                        </div>
                    </div>

                    <!-- Publisher Group -->
                    <div class="bdt-filter-group">
                        <h3 class="bdt-filter-group__title">Penerbit</h3>
                        <?php 
                        $publishers = ['semua', 'Gramedia', 'Bentang Pustaka', 'Mizan', 'Local Press'];
                        foreach ($publishers as $pub) :
                            $isActive = $selectedPublisher === $pub || (empty($selectedPublisher) && $pub === 'semua');
                            $isChecked = $isActive ? 'checked' : '';
                        ?>
                            <label class="bdt-filter-checkbox">
                                <input type="radio" name="publisher" value="<?= $pub ?>" <?= $isChecked ?> onchange="this.form.submit()">
                                <?= htmlspecialchars(ucfirst($pub)) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </form>
            </aside>

            <!-- Main Content Section -->
            <main class="bdt-catalog-content">
                <div class="bdt-catalog-content__header">
                    <div class="bdt-catalog-content__info">
                        Menampilkan <?= $totalBooksCount ?> buku <?= !empty($selectedCategories) ? 'dalam "' . implode(', ', $selectedCategories) . '"' : '' ?>
                    </div>
                    <div class="bdt-catalog-content__sort">
                        <span>Urutkan:</span>
                        <select aria-label="Urutkan Koleksi">
                            <option>Koleksi Terbaru</option>
                            <option>Judul A-Z</option>
                            <option>Terpopuler</option>
                        </select>
                    </div>
                </div>

                <!-- Book Grid -->
                <div class="bdt-catalog-grid">
                    <?php if ($totalBooksCount > 0) : ?>
                        <?php foreach ($filteredBooks as $book) : ?>
                            <article class="bdt-premium-card">
                                <div class="bdt-premium-card__img-wrap">
                                    <img src="<?= BASE_URL . htmlspecialchars($book['image']) ?>" alt="Cover <?= htmlspecialchars($book['title']) ?>" class="bdt-premium-card__img">
                                    <?php if (!empty($book['badge'])) : ?>
                                        <span class="bdt-premium-card__badge"><?= htmlspecialchars($book['badge']) ?></span>
                                    <?php endif; ?>
                                    
                                    <!-- Bookmark Icon (inline SVG) -->
                                    <button class="bdt-premium-card__bookmark" aria-label="Simpan ke daftar bacaan">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="bdt-premium-card__body">
                                    <h3 class="bdt-premium-card__title">
                                        <a href="<?= BASE_URL . htmlspecialchars($book['href']) ?>"><?= htmlspecialchars($book['title']) ?></a>
                                    </h3>
                                    <p class="bdt-premium-card__author">oleh <?= htmlspecialchars($book['author']) ?></p>
                                    
                                    <div class="bdt-premium-card__footer">
                                        <div class="bdt-premium-card__status">
                                            <span class="bdt-premium-card__dot bdt-premium-card__dot--<?= htmlspecialchars($book['status']) ?>"></span>
                                            <?= htmlspecialchars($book['status_text']) ?>
                                        </div>
                                        
                                        <?php if ($book['status'] === 'dipinjam') : ?>
                                            <span class="bdt-premium-card__action bdt-premium-card__action--disabled">Ikut Antrean</span>
                                        <?php elseif ($book['status'] === 'dipesan') : ?>
                                            <a href="#" class="bdt-premium-card__action">Pinjam</a>
                                        <?php else : ?>
                                            <a href="#" class="bdt-premium-card__action">Pinjam</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="bdt-catalog-empty">
                            <div class="bdt-catalog-empty__icon">🔍</div>
                            <h3 class="bdt-catalog-empty__title">Buku Tidak Ditemukan</h3>
                            <p>Coba gunakan kata kunci pencarian atau sesuaikan filter Anda.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php 
                $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $totalPages = 12;
                include __DIR__ . '/../../components/pagination.php'; 
                ?>

            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
