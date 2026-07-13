<?php
/**
 * Detail Buku Page – Baca Di Teras
 *
 * File    : books/detail.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Menampilkan detail satu koleksi buku berdasarkan query string:
 *   ?id=the-architecture-of-growth
 */

define('BASE_URL', '/baca-di-teras');

$activePage = 'katalog';

// Load config data
require_once __DIR__ . '/../../config/book-config.php';
require_once __DIR__ . '/../../config/book-detail-config.php';

// Get book slug from query
$bookSlug = $_GET['id'] ?? 'the-architecture-of-growth';
$book = $bookDetailMap[$bookSlug] ?? null;

// Fallback if not found in detail map (read from basic book-config as fallback)
if (!$book) {
    foreach ($bookList as $item) {
        if ($item['id'] === $bookSlug) {
            $book = [
                'id'          => $item['id'],
                'title'       => $item['title'],
                'author'      => $item['author'],
                'category'    => $item['category'],
                'badge'       => $item['badge'],
                'image'       => $item['image'],
                'rating'      => '4.5 (24 ulasan)',
                'pages'       => '250 Halaman',
                'publisher'   => $item['publisher'] ?? 'Penerbit Desa',
                'pub_date'    => '2024',
                'isbn'        => '978-000-0000-00-0',
                'language'    => 'Bahasa Indonesia',
                'synopsis'    => 'Informasi sinopsis buku belum tersedia untuk koleksi ini. Silakan hubungi perpustakaan terkait untuk informasi lebih lanjut.',
                'availability'=> [
                    [
                        'library_name'=> 'Perpustakaan Desa Teras',
                        'location'    => 'Pusat Layanan Terintegrasi',
                        'status'      => $item['status'] ?? 'tersedia',
                        'status_text' => $item['status_text'] ?? 'Tersedia',
                        'map_query'   => 'Desa Teras, Boyolali'
                    ]
                ],
                'action_status'=> $item['status'] ?? 'tersedia'
            ];
            break;
        }
    }
}

// 404-like fallback jika tetap tidak ada
if (!$book) {
    http_response_code(404);
    die('<h1>Buku tidak ditemukan.</h1>');
}

// Filter recommendations: same category, exclude current book
$recommendations = array_filter($bookList, function($item) use ($book) {
    return $item['category'] === $book['category'] && $item['id'] !== $book['id'];
});

// Fallback to general books if no recommendations in same category
if (count($recommendations) === 0) {
    $recommendations = array_filter($bookList, function($item) use ($book) {
        return $item['id'] !== $book['id'];
    });
}
// Limit to 5 recommendations
$recommendations = array_slice($recommendations, 0, 5);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Detail buku <?= htmlspecialchars($book['title']) ?> oleh <?= htmlspecialchars($book['author']) ?>. Cek status ketersediaan di perpustakaan Desa Teras.">
    <title><?= htmlspecialchars($book['title']) ?> – Baca Di Teras</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/landing.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fcfcfc;
            color: #101814;
            margin: 0;
        }

        /* Detail Wrapper */
        .bdt-detail-wrapper {
            max-width: 1120px;
            margin: 0 auto;
            padding: 24px 24px 80px;
        }

        /* Breadcrumb */
        .bdt-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #64748b;
            margin-bottom: 32px;
            font-weight: 500;
        }
        .bdt-breadcrumb a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s;
        }
        .bdt-breadcrumb a:hover {
            color: #1a6b2f;
        }
        .bdt-breadcrumb__separator {
            color: #cbd5e1;
        }
        .bdt-breadcrumb__current {
            color: #0f172a;
            font-weight: 600;
        }

        /* Two Column Layout */
        .bdt-detail-layout {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 48px;
            align-items: start;
        }

        /* Left Side: Cover & Stats */
        .bdt-detail-aside {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .bdt-detail-cover-wrap {
            width: 100%;
            aspect-ratio: 3/4;
            background-color: #f8fafc;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }
        .bdt-detail-cover {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.15);
        }
        .bdt-detail-quickstats {
            display: flex;
            gap: 12px;
        }
        .bdt-quickstat-badge {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 14px;
            border-radius: 10px;
            background-color: #f1f5f9;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        /* Right Side: Info details */
        .bdt-detail-info {
            display: flex;
            flex-direction: column;
        }
        .bdt-detail-info__category {
            color: #1a6b2f;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 8px;
        }
        .bdt-detail-info__title {
            font-size: 40px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 8px;
            letter-spacing: -0.02em;
            line-height: 1.15;
        }
        .bdt-detail-info__author {
            font-size: 16px;
            color: #64748b;
            margin: 0 0 32px;
            font-weight: 500;
        }
        .bdt-detail-info__author em {
            color: #1a6b2f;
            font-style: normal;
            font-weight: 600;
        }

        /* Metadata Box */
        .bdt-detail-meta-box {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 32px;
        }
        .bdt-meta-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .bdt-meta-item__label {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .bdt-meta-item__value {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            line-height: 1.4;
        }

        /* Synopsis */
        .bdt-detail-section {
            margin-bottom: 36px;
        }
        .bdt-detail-section__title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 14px;
        }
        .bdt-detail-section__content {
            font-size: 15px;
            line-height: 1.7;
            color: #475569;
        }

        /* Availability Section */
        .bdt-availability-card {
            background-color: #f8faf7;
            border: 1px solid #eef2ed;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 36px;
        }
        .bdt-availability-card__header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 700;
            color: #101814;
            margin-bottom: 20px;
        }
        .bdt-availability-card__header svg {
            color: #1a6b2f;
        }
        .bdt-availability-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .bdt-availability-item {
            background: #ffffff;
            border: 1px solid #eef2ed;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.01);
        }
        .bdt-availability-item__info {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .bdt-availability-item__icon-wrap {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
        }
        .bdt-availability-item__icon-wrap--tersedia {
            background-color: #f0f9f2;
            color: #1a6b2f;
        }
        .bdt-availability-item__details {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .bdt-availability-item__name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }
        .bdt-availability-item__loc {
            font-size: 12px;
            color: #64748b;
        }
        .bdt-availability-item__status-wrap {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        .bdt-availability-item__status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
        }
        .bdt-availability-item__status--tersedia {
            color: #1a6b2f;
        }
        .bdt-availability-item__status--habis {
            color: #ef4444;
        }
        .bdt-availability-item__note {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
            margin-left: 14px;
        }
        .bdt-availability-item__map-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #1a6b2f;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            background-color: #f0f9f2;
            transition: all 0.2s;
        }
        .bdt-availability-item__map-link:hover {
            background-color: #1a6b2f;
            color: #ffffff;
        }

        /* Action Buttons */
        .bdt-detail-actions {
            display: flex;
            gap: 16px;
            margin-top: 16px;
        }
        .bdt-detail-btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }
        .bdt-detail-btn--primary {
            background-color: #1a6b2f;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(26, 107, 47, 0.2);
        }
        .bdt-detail-btn--primary:hover {
            background-color: #134e22;
        }
        .bdt-detail-btn--secondary {
            background-color: #e2e8f0;
            color: #94a3b8;
            cursor: not-allowed;
        }
        .bdt-detail-btn__disabled-badge {
            font-size: 10px;
            background-color: #cbd5e1;
            color: #64748b;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        /* Recommended Section */
        .bdt-recommended-section {
            border-top: 1px solid #e2e8f0;
            margin-top: 64px;
            padding-top: 64px;
        }
        .bdt-recommended-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 32px;
        }
        .bdt-recommended-header__title {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 6px;
            letter-spacing: -0.01em;
        }
        .bdt-recommended-header__subtitle {
            font-size: 15px;
            color: #64748b;
            margin: 0;
        }
        .bdt-recommended-header__link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 700;
            color: #1a6b2f;
            text-decoration: none;
            transition: color 0.2s;
        }
        .bdt-recommended-header__link:hover {
            color: #134e22;
        }
        .bdt-recommended-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 24px;
        }

        @media (max-width: 980px) {
            .bdt-detail-layout {
                grid-template-columns: 1fr;
                gap: 32px;
            }
            .bdt-detail-cover-wrap {
                max-width: 320px;
                margin: 0 auto;
            }
            .bdt-detail-quickstats {
                max-width: 320px;
                margin: 0 auto;
                width: 100%;
            }
            .bdt-recommended-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .bdt-detail-meta-box {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            .bdt-detail-actions {
                flex-direction: column;
            }
            .bdt-availability-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .bdt-availability-item__status-wrap {
                width: 100%;
                justify-content: space-between;
            }
            .bdt-recommended-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../components/navbar.php'; ?>

    <div class="bdt-detail-wrapper">
        <!-- Breadcrumb -->
        <nav class="bdt-breadcrumb" aria-label="Navigasi breadcrumb">
            <a href="<?= BASE_URL ?>/">Perpustakaan</a>
            <span class="bdt-breadcrumb__separator">&gt;</span>
            <a href="<?= BASE_URL ?>/custom/pages/books/katalog.php?category=<?= urlencode($book['category']) ?>"><?= htmlspecialchars($book['category']) ?></a>
            <span class="bdt-breadcrumb__separator">&gt;</span>
            <span class="bdt-breadcrumb__current"><?= htmlspecialchars($book['title']) ?></span>
        </nav>

        <!-- Main Detail Layout -->
        <div class="bdt-detail-layout">
            
            <!-- Left Side -->
            <aside class="bdt-detail-aside">
                <div class="bdt-detail-cover-wrap">
                    <img src="<?= BASE_URL . htmlspecialchars($book['image']) ?>" alt="Cover <?= htmlspecialchars($book['title']) ?>" class="bdt-detail-cover">
                </div>
                <div class="bdt-detail-quickstats">
                    <div class="bdt-quickstat-badge">
                        <!-- Star Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #f59e0b;">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        <span><?= htmlspecialchars($book['rating']) ?></span>
                    </div>
                    <div class="bdt-quickstat-badge">
                        <!-- Book Open Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        <span><?= htmlspecialchars($book['pages']) ?></span>
                    </div>
                </div>
            </aside>

            <!-- Right Side Info -->
            <main class="bdt-detail-info">
                <span class="bdt-detail-info__category"><?= htmlspecialchars($book['category']) ?></span>
                <h1 class="bdt-detail-info__title"><?= htmlspecialchars($book['title']) ?></h1>
                <p class="bdt-detail-info__author">oleh <em><?= htmlspecialchars($book['author']) ?></em></p>

                <!-- Metadata Grid -->
                <div class="bdt-detail-meta-box">
                    <div class="bdt-meta-item">
                        <span class="bdt-meta-item__label">Penerbit</span>
                        <span class="bdt-meta-item__value"><?= htmlspecialchars($book['publisher']) ?></span>
                    </div>
                    <div class="bdt-meta-item">
                        <span class="bdt-meta-item__label">Tanggal Terbit</span>
                        <span class="bdt-meta-item__value"><?= htmlspecialchars($book['pub_date']) ?></span>
                    </div>
                    <div class="bdt-meta-item">
                        <span class="bdt-meta-item__label">ISBN</span>
                        <span class="bdt-meta-item__value"><?= htmlspecialchars($book['isbn']) ?></span>
                    </div>
                    <div class="bdt-meta-item">
                        <span class="bdt-meta-item__label">Bahasa</span>
                        <span class="bdt-meta-item__value"><?= htmlspecialchars($book['language']) ?></span>
                    </div>
                </div>

                <!-- Synopsis -->
                <div class="bdt-detail-section">
                    <h2 class="bdt-detail-section__title">Sinopsis</h2>
                    <div class="bdt-detail-section__content">
                        <p><?= nl2br(htmlspecialchars($book['synopsis'])) ?></p>
                    </div>
                </div>

                <!-- Availability Panel -->
                <div class="bdt-availability-card">
                    <h2 class="bdt-availability-card__header">
                        <!-- Location Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        Ketersediaan Saat Ini
                    </h2>
                    
                    <div class="bdt-availability-list">
                        <?php foreach ($book['availability'] as $avail) : ?>
                            <div class="bdt-availability-item">
                                <div class="bdt-availability-item__info">
                                    <div class="bdt-availability-item__icon-wrap bdt-availability-item__icon-wrap--<?= htmlspecialchars($avail['status']) ?>">
                                        <!-- Library/School Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="bdt-availability-item__details">
                                        <span class="bdt-availability-item__name"><?= htmlspecialchars($avail['library_name']) ?></span>
                                        <span class="bdt-availability-item__loc"><?= htmlspecialchars($avail['location']) ?></span>
                                    </div>
                                </div>
                                <div class="bdt-availability-item__status-wrap">
                                    <div class="bdt-availability-item__status bdt-availability-item__status--<?= htmlspecialchars($avail['status']) ?>">
                                        <span class="bdt-premium-card__dot bdt-premium-card__dot--<?= htmlspecialchars($avail['status'] === 'tersedia' ? 'tersedia' : 'dipinjam') ?>"></span>
                                        <?= htmlspecialchars($avail['status_text']) ?>
                                        <?php if (!empty($avail['note'])) : ?>
                                            <span class="bdt-availability-item__note">• <?= htmlspecialchars($avail['note']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($avail['map_query']) ?>" target="_blank" class="bdt-availability-item__map-link">
                                        Lihat di Peta
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bdt-detail-actions">
                    <?php if ($book['action_status'] === 'tersedia') : ?>
                        <button class="bdt-detail-btn bdt-detail-btn--primary">
                            <!-- Book Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            Pesan Buku Ini
                        </button>
                        <a href="#" class="bdt-detail-btn bdt-detail-btn--secondary">
                            Pinjam ke Rumah <span class="bdt-detail-btn__disabled-badge">TIDAK TERSEDIA</span>
                        </a>
                    <?php else: ?>
                        <button class="bdt-detail-btn bdt-detail-btn--secondary" disabled>
                            Stok Habis
                        </button>
                        <button class="bdt-detail-btn bdt-detail-btn--primary">
                            Ikut Antrean
                        </button>
                    <?php endif; ?>
                </div>

            </main>
        </div>

        <!-- Recommendations Section -->
        <section class="bdt-recommended-section" aria-label="Rekomendasi buku serupa">
            <div class="bdt-recommended-header">
                <div>
                    <h2 class="bdt-recommended-header__title">Pembaca juga menyukai</h2>
                    <p class="bdt-recommended-header__subtitle">Jelajahi lebih banyak literasi dalam kategori <?= htmlspecialchars($book['category']) ?></p>
                </div>
                <a href="<?= BASE_URL ?>/custom/pages/books/katalog.php?category=<?= urlencode($book['category']) ?>" class="bdt-recommended-header__link">
                    Lihat Semua
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>

            <!-- Book Grid (Using book-card.php component) -->
            <div class="bdt-recommended-grid">
                <?php foreach ($recommendations as $recBook) : 
                    $bookData = $recBook;
                    $bookData['href'] = '/custom/pages/books/detail.php?id=' . $recBook['id'];
                ?>
                    <?php include __DIR__ . '/../../components/book-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>

    </div>

    <?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
