<?php
/**
 * Pathfinder – Halaman Detail Topik
 *
 * File    : detail.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Menampilkan detail lengkap satu pathfinder:
 * - Deskripsi topik
 * - Sidebar kata kunci (Broader, Narrower, Related Terms)
 * - Koleksi buku terkait
 * - Sumber internet terbuka
 *
 * URL: /pathfinder/{slug}
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage = 'pathfinder';

// ── Service Layer ──────────────────────────────────────────────
$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/PathfinderService.php';

$pfService = new PathfinderService();

// Ambil slug dari route params
$pfSlug    = $routeParams['slug'] ?? '';
$pathfinder = $pfService->getPathfinderBySlug($pfSlug);

// Redirect ke 404 jika tidak ditemukan
if ($pathfinder === null) {
    http_response_code(404);
    include $libPath . '/custom/pages/404.php';
    exit;
}

// Decode JSON fields
$jsonFields = ['books', 'web_resources', 'broader_terms', 'narrower_terms', 'related_terms'];
foreach ($jsonFields as $field) {
    if (!empty($pathfinder[$field]) && is_string($pathfinder[$field])) {
        $pathfinder[$field] = json_decode($pathfinder[$field], true) ?? [];
    } elseif (empty($pathfinder[$field])) {
        $pathfinder[$field] = [];
    }
}

$books        = $pathfinder['books'];
$webResources = $pathfinder['web_resources'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars(mb_substr($pathfinder['description'], 0, 160)) ?>">
    <title><?= htmlspecialchars($pathfinder['title']) ?> – Pathfinder – Baca Di Teras</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Main styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/landing.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/pathfinder.css">
</head>
<body>
    <?php include __DIR__ . '/../../components/navbar.php'; ?>

    <div class="pf-wrapper">

        <!-- ============================================================
             Detail Layout
             ============================================================ -->
        <div class="pf-detail" id="pf-detail">
            <div class="pf-detail__layout">

                <!-- ── Main Content ──────────────────────────────── -->
                <div class="pf-detail__main">
                    <h1 class="pf-detail__title"><?= htmlspecialchars($pathfinder['title']) ?></h1>
                    <p class="pf-detail__desc"><?= htmlspecialchars($pathfinder['description']) ?></p>

                    <!-- Action Buttons -->
                    <div class="pf-detail__actions">
                        <button class="pf-action-btn" id="pf-btn-download" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Download
                        </button>
                        <button class="pf-action-btn" id="pf-btn-save-topic" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                            </svg>
                            Simpan Topic
                        </button>
                    </div>

                    <!-- ====================================================
                         Koleksi Buku
                         ==================================================== -->
                    <section class="pf-books" id="pf-books" aria-label="Koleksi Buku">
                        <div class="pf-books__header">
                            <h2 class="pf-books__title">Koleksi Buku</h2>
                            <span class="pf-books__count"><?= count($books) ?> Hasil Ditemukan</span>
                        </div>

                        <div class="pf-books__grid">
                            <?php foreach ($books as $idx => $book) : ?>
                                <article class="pf-book-card" id="pf-book-<?= $idx ?>">
                                    <div class="pf-book-card__img-wrap">
                                        <div class="pf-placeholder-img">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="pf-book-card__body">
                                        <h3 class="pf-book-card__title"><?= htmlspecialchars($book['title']) ?></h3>
                                        <p class="pf-book-card__author"><?= htmlspecialchars($book['author']) ?>, <?= $book['year'] ?></p>

                                        <div class="pf-book-card__call-number-wrap">
                                            <div>
                                                <p class="pf-book-card__call-label">Nomor Panggil</p>
                                                <span class="pf-book-card__call-number"><?= htmlspecialchars($book['call_number']) ?></span>
                                            </div>
                                            <a href="<?= BASE_URL ?>/katalog"
                                               class="pf-book-card__detail-btn"
                                               id="pf-book-detail-<?= $idx ?>">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- ====================================================
                         Sumber Internet Terbuka
                         ==================================================== -->
                    <?php if (!empty($webResources)) : ?>
                    <section class="pf-resources" id="pf-resources" aria-label="Sumber Internet Terbuka">
                        <h2 class="pf-resources__title">Sumber Internet Terbuka</h2>

                        <?php foreach ($webResources as $idx => $res) : ?>
                            <div class="pf-resource-item" id="pf-resource-<?= $idx ?>">
                                <div class="pf-resource-item__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                    </svg>
                                </div>
                                <div class="pf-resource-item__content">
                                    <h3 class="pf-resource-item__title">
                                        <a href="<?= htmlspecialchars($res['url']) ?>" target="_blank" rel="noopener noreferrer">
                                            <?= htmlspecialchars($res['title']) ?>
                                        </a>
                                    </h3>
                                    <p class="pf-resource-item__desc"><?= htmlspecialchars($res['description']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </section>
                    <?php endif; ?>

                    <!-- ====================================================
                         Detail Footer Banner
                         ==================================================== -->
                    <div class="pf-detail-footer" id="pf-detail-footer">
                        <span class="pf-detail-footer__author">
                            Disusun oleh: <?= htmlspecialchars($pathfinder['author']) ?>
                        </span>
                        <span class="pf-detail-footer__date">
                            Diperbarui: <?= htmlspecialchars($pathfinder['updated_at']) ?> — Baca Di Teras Pathfinder Series
                        </span>
                    </div>

                </div><!-- /.pf-detail__main -->

                <!-- ── Sidebar ───────────────────────────────────── -->
                <aside class="pf-sidebar" id="pf-sidebar">
                    <div class="pf-keywords">
                        <div class="pf-keywords__header">Kata Kunci</div>
                        <div class="pf-keywords__body">
                            <!-- Broader Term -->
                            <div class="pf-keywords__group">
                                <p class="pf-keywords__label">Broader Term</p>
                                <ul class="pf-keywords__list">
                                    <?php foreach ($pathfinder['broader_terms'] as $term) : ?>
                                        <li><?= htmlspecialchars($term) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <!-- Narrower Term -->
                            <div class="pf-keywords__group">
                                <p class="pf-keywords__label">Narrower Term</p>
                                <ul class="pf-keywords__list">
                                    <?php foreach ($pathfinder['narrower_terms'] as $term) : ?>
                                        <li><?= htmlspecialchars($term) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <!-- Related Term -->
                            <div class="pf-keywords__group">
                                <p class="pf-keywords__label">Related Term</p>
                                <ul class="pf-keywords__list">
                                    <?php foreach ($pathfinder['related_terms'] as $term) : ?>
                                        <li><?= htmlspecialchars($term) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <hr class="pf-keywords__divider">

                            <!-- Katalog -->
                            <div class="pf-keywords__group">
                                <p class="pf-keywords__catalog-label">Katalog</p>
                                <a href="<?= BASE_URL ?>/katalog"
                                   class="pf-keywords__catalog-link"
                                   id="pf-catalog-link">
                                    <?= htmlspecialchars($pathfinder['catalog_url']) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </aside>

            </div><!-- /.pf-detail__layout -->
        </div><!-- /.pf-detail -->

    </div><!-- /.pf-wrapper -->

    <!-- Floating Buttons -->
    <div class="pf-floating" id="pf-floating">
        <button class="pf-floating__btn pf-floating__btn--bookmark" id="pf-float-bookmark" type="button" aria-label="Simpan Pathfinder">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
            </svg>
        </button>
        <button class="pf-floating__btn pf-floating__btn--share" id="pf-float-share" type="button" aria-label="Bagikan Pathfinder">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="18" cy="5" r="3"></circle>
                <circle cx="6" cy="12" r="3"></circle>
                <circle cx="18" cy="19" r="3"></circle>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
            </svg>
        </button>
    </div>

    <?php include __DIR__ . '/../../components/footer.php'; ?>

</body>
</html>
