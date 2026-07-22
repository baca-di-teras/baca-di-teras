<?php
/**
 * Book Detail Page
 *
 * File    : book-detail.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Menampilkan detail lengkap buku beserta ketersediaannya di tiap perpustakaan.
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

// ── Service Layer ──────────────────────────────────────────────
$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/helpers/Database.php';
require_once $libPath . '/custom/services/BookService.php';

$bookService = new BookService();

// Ambil ID dari URL (contoh: /katalog/123)
$biblioId = (int) ($routeParams['slug'] ?? $_GET['id'] ?? 0);

if (!$biblioId) {
    http_response_code(404);
    require defined('ROOT_PATH') ? ROOT_PATH . '/custom/pages/404.php' : __DIR__ . '/../../404.php';
    exit;
}

$book = $bookService->getByBiblioId($biblioId);

if (!$book) {
    http_response_code(404);
    require defined('ROOT_PATH') ? ROOT_PATH . '/custom/pages/404.php' : __DIR__ . '/../../404.php';
    exit;
}

$book = array_merge([
    'classification' => '',
    'gmd' => '',
    'language' => '',
    'notes' => '',
], $book);

// Ambil buku terkait
$relatedBooks = $bookService->getRelated($biblioId, 4);

$activePage = 'perpustakaan';
$baseUrl    = BASE_URL;
$imageSrc   = (str_starts_with($book['image'], 'http') || str_starts_with($book['image'], '/')) ? $book['image'] : $baseUrl . $book['image'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Detail buku <?= htmlspecialchars($book['title']) ?> oleh <?= htmlspecialchars($book['author']) ?>">
    <title><?= htmlspecialchars($book['title']) ?> – Baca Di Teras</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/custom/assets/css/landing.css">
    <style>
        .bdt-book-detail {
            padding: 120px 0 80px;
            background: #f7f6f5;
        }
        .bdt-book-detail__grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 48px;
            align-items: start;
        }
        .bdt-book-detail__cover-wrap {
            position: sticky;
            top: 100px;
            background: #fff;
            padding: 24px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            text-align: center;
        }
        .bdt-book-detail__cover {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }
        .bdt-book-detail__actions {
            display: flex;
            gap: 12px;
            flex-direction: column;
        }
        .bdt-book-detail__btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .bdt-book-detail__btn--primary {
            background: #d35400;
            color: #fff;
            border: none;
        }
        .bdt-book-detail__btn--primary:hover {
            background: #b54700;
            transform: translateY(-2px);
        }
        .bdt-book-detail__btn--outline {
            background: transparent;
            color: #202124;
            border: 2px solid #e0e0e0;
        }
        .bdt-book-detail__btn--outline:hover {
            border-color: #d35400;
            color: #d35400;
        }
        
        .bdt-book-detail__info h1 {
            font-size: 40px;
            font-weight: 700;
            line-height: 1.2;
            color: #202124;
            margin: 0 0 12px 0;
            letter-spacing: -0.02em;
        }
        .bdt-book-detail__author {
            font-size: 20px;
            color: #5f6368;
            margin-bottom: 24px;
        }
        .bdt-book-detail__meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            padding: 24px;
            background: #fff;
            border-radius: 16px;
            margin-bottom: 40px;
        }
        .bdt-meta-item__label {
            font-size: 13px;
            color: #80868b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
            font-weight: 600;
        }
        .bdt-meta-item__value {
            font-size: 16px;
            color: #202124;
            font-weight: 500;
        }
        
        .bdt-book-detail__section-title {
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 20px 0;
            color: #202124;
        }
        
        .bdt-availability-list {
            list-style: none;
            padding: 0;
            margin: 0 0 40px 0;
        }
        .bdt-availability-item {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #f1f3f4;
            transition: all 0.2s ease;
        }
        .bdt-availability-item:hover {
            border-color: #d35400;
            box-shadow: 0 4px 12px rgba(211,84,0,0.08);
        }
        .bdt-availability-item__lib {
            font-size: 18px;
            font-weight: 600;
            color: #202124;
            margin-bottom: 4px;
        }
        .bdt-availability-item__address {
            font-size: 14px;
            color: #5f6368;
        }
        .bdt-availability-badge {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
        }
        .bdt-availability-badge--available {
            background: #e6f4ea;
            color: #137333;
        }
        .bdt-availability-badge--borrowed {
            background: #fce8e6;
            color: #c5221f;
        }

        .bdt-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 40px;
        }
        .bdt-tag {
            padding: 6px 16px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 50px;
            font-size: 14px;
            color: #5f6368;
        }
        
        @media (max-width: 980px) {
            .bdt-book-detail__grid {
                grid-template-columns: 1fr;
            }
            .bdt-book-detail__cover-wrap {
                position: relative;
                top: 0;
                max-width: 400px;
                margin: 0 auto;
            }
            .bdt-book-detail__info h1 {
                font-size: 32px;
                margin-top: 24px;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../components/navbar.php'; ?>

<main class="bdt-book-detail">
    <div class="bdt-container">
        
        <div class="bdt-book-detail__grid">
            <!-- Kolom Kiri: Cover & Actions -->
            <aside>
                <div class="bdt-book-detail__cover-wrap">
                    <img src="<?= htmlspecialchars($imageSrc) ?>" 
                         alt="Sampul Buku <?= htmlspecialchars($book['title']) ?>" 
                         class="bdt-book-detail__cover">
                    <div class="bdt-book-detail__actions">
                        <a href="#" class="bdt-book-detail__btn bdt-book-detail__btn--primary">
                            Pinjam Buku Ini
                        </a>
                        <button class="bdt-book-detail__btn bdt-book-detail__btn--outline">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                            Simpan ke Daftar Bacaan
                        </button>
                    </div>
                </div>
            </aside>

            <!-- Kolom Kanan: Info Detail -->
            <article class="bdt-book-detail__info">
                <h1><?= htmlspecialchars($book['title']) ?></h1>
                <p class="bdt-book-detail__author">oleh <?= htmlspecialchars($book['author']) ?></p>
                
                <div class="bdt-book-detail__meta-grid">
                    <div>
                        <div class="bdt-meta-item__label">Penerbit</div>
                        <div class="bdt-meta-item__value"><?= htmlspecialchars($book['publisher'] ?: '-') ?></div>
                    </div>
                    <div>
                        <div class="bdt-meta-item__label">Tahun Terbit</div>
                        <div class="bdt-meta-item__value"><?= htmlspecialchars($book['publishYear'] ?: '-') ?></div>
                    </div>
                    <div>
                        <div class="bdt-meta-item__label">ISBN/ISSN</div>
                        <div class="bdt-meta-item__value"><?= htmlspecialchars($book['isbn'] ?: '-') ?></div>
                    </div>
                    <div>
                        <div class="bdt-meta-item__label">Klasifikasi</div>
                        <div class="bdt-meta-item__value"><?= htmlspecialchars($book['classification'] ?: '-') ?></div>
                    </div>
                    <div>
                        <div class="bdt-meta-item__label">GMD</div>
                        <div class="bdt-meta-item__value"><?= htmlspecialchars($book['gmd'] ?: '-') ?></div>
                    </div>
                    <div>
                        <div class="bdt-meta-item__label">Bahasa</div>
                        <div class="bdt-meta-item__value"><?= htmlspecialchars($book['language'] ?: '-') ?></div>
                    </div>
                </div>

                <!-- Ketersediaan -->
                <h2 class="bdt-book-detail__section-title">Ketersediaan</h2>
                <ul class="bdt-availability-list">
                    <?php if (empty($book['availability'])): ?>
                        <li class="bdt-availability-item">
                            <div>
                                <div class="bdt-availability-item__lib">Informasi Belum Tersedia</div>
                                <div class="bdt-availability-item__address">Silakan hubungi pustakawan.</div>
                            </div>
                        </li>
                    <?php else: ?>
                        <?php 
                        // Kelompokkan per perpustakaan
                        $groupedAvail = [];
                        foreach ($book['availability'] as $item) {
                            $libId = $item['location_id'];
                            if (!isset($groupedAvail[$libId])) {
                                $groupedAvail[$libId] = [
                                    'name' => $item['library_name'] ?: $item['location_name'],
                                    'address' => $item['library_address'],
                                    'total' => $item['total_di_lokasi'],
                                    'available' => $item['tersedia_di_lokasi']
                                ];
                            }
                        }
                        ?>
                        <?php foreach ($groupedAvail as $loc): ?>
                        <li class="bdt-availability-item">
                            <div>
                                <div class="bdt-availability-item__lib"><?= htmlspecialchars($loc['name']) ?></div>
                                <?php if ($loc['address']): ?>
                                    <div class="bdt-availability-item__address"><?= htmlspecialchars($loc['address']) ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if ($loc['available'] > 0): ?>
                                <span class="bdt-availability-badge bdt-availability-badge--available">
                                    <?= $loc['available'] ?> Tersedia
                                </span>
                            <?php else: ?>
                                <span class="bdt-availability-badge bdt-availability-badge--borrowed">
                                    Dipinjam
                                </span>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <!-- Topik / Subjek -->
                <?php if (!empty($book['topics'])): ?>
                    <h2 class="bdt-book-detail__section-title">Topik Terkait</h2>
                    <div class="bdt-tags">
                        <?php foreach ($book['topics'] as $topic): ?>
                            <span class="bdt-tag"><?= htmlspecialchars($topic) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($book['notes']): ?>
                    <h2 class="bdt-book-detail__section-title">Catatan</h2>
                    <p style="color: #5f6368; line-height: 1.6; margin-bottom: 40px;">
                        <?= nl2br(htmlspecialchars($book['notes'])) ?>
                    </p>
                <?php endif; ?>
            </article>
        </div>

    </div>
</main>

<!-- Buku Terkait -->
<?php if (!empty($relatedBooks)): ?>
<section class="bdt-section" style="background: #fff;">
    <div class="bdt-container">
        <h2 class="bdt-section__title" style="margin-bottom: 32px;">Pembaca Juga Menyukai</h2>
        <div class="bdt-book-grid bdt-book-grid--detail">
            <?php foreach ($relatedBooks as $bookData) : ?>
                <?php include __DIR__ . '/../../components/book-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/../../components/footer.php'; ?>

</body>
</html>
