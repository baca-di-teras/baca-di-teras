<?php
/**
 * Pathfinder — Halaman Jelajahi (Two-Panel Layout)
 *
 * File    : browse.php
 * Project : Baca Di Teras
 * Version : 2.0.0
 *
 * Layout:
 *  - Hero Section dengan gambar latar sawah/alam
 *  - Panel Kiri  : Daftar kategori (sidebar), klik untuk aktif
 *  - Panel Kanan : Breadcrumb + accordion topik, setiap topik
 *                  memiliki 4 tab: Pendahuluan | Koleksi Buku |
 *                  Tips & Panduan | Download
 *
 * Komponen accordion reusable: custom/components/pathfinder-topic-card.php
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage = 'pathfinder';
$libPath    = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';

require_once $libPath . '/custom/services/PathfinderV2Service.php';
require_once $libPath . '/custom/services/BookService.php';

$svc = new PathfinderV2Service();
$bookSvc = new BookService();

// ── Tentukan buku & topik aktif jika dalam mode detail buku ──
$activeBookId = 0;
if (!empty($routeParams['id'])) {
    $activeBookId = (int) $routeParams['id'];
} elseif (!empty($_GET['buku'])) {
    $activeBookId = (int) $_GET['buku'];
}

$activeBookDetail = null;
if ($activeBookId > 0) {
    $activeBookDetail = $bookSvc->getByBiblioId($activeBookId);
}

$activeTopicSlug = !empty($routeParams['topik']) ? $routeParams['topik'] : (!empty($_GET['topik']) ? trim($_GET['topik']) : '');

// ── Tentukan kategori aktif ───────────────────────────────────
$activeSlug = '';
if (!empty($routeParams['slug'])) {
    $activeSlug = $routeParams['slug'];
} elseif (!empty($_GET['kategori'])) {
    $activeSlug = trim($_GET['kategori']);
}

// Semua kategori + topik untuk sidebar
$categoriesWithTopics = $svc->getCategoriesWithTopics();

$activeCategory = null;
foreach ($categoriesWithTopics as $cat) {
    if ($cat['slug'] === $activeSlug) {
        $activeCategory = $cat;
        break;
    }
}
if ($activeCategory === null && !empty($categoriesWithTopics)) {
    $activeCategory = $categoriesWithTopics[0];
    $activeSlug     = $activeCategory['slug'];
}

// ── Kumpulkan semua data per topik (4 sumber) ────────────────
$topicCards = [];
$activeTopicName = '';
if ($activeCategory) {
    foreach ($activeCategory['topics'] as $topic) {
        $id = (int) $topic['id'];
        $books = $svc->getBooksByTopicId($id, 50);
        if ($activeBookId > 0 && ($topic['slug'] === $activeTopicSlug || array_filter($books, fn($b) => (int)$b['biblio_id'] === $activeBookId))) {
            $activeTopicName = $topic['name'];
            $activeTopicSlug = $topic['slug'];
        }
        $topicCards[] = array_merge($topic, [
            'category_slug' => $activeCategory['slug'],
            'category_name' => $activeCategory['name'],
            'books'     => $books,
            'intro'     => $svc->getIntroduction($id),
            'guides'    => $svc->getGuidesByTopicId($id),
            'downloads' => $svc->getDownloadsByTopicId($id),
        ]);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Jelajahi Pathfinder Perpustakaan Baca Di Teras – panduan literasi terkurasi berdasarkan kategori dan topik, terintegrasi dengan koleksi buku SLiMS.">
    <title>Jelajahi Pathfinder – Baca Di Teras</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/landing.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/pathfinder.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/browse.css">
</head>
<body>
    <?php include __DIR__ . '/../../components/navbar.php'; ?>

    <!-- ============================================================
         HERO
         ============================================================ -->
    <section class="browse-hero" id="browse-hero" aria-label="Pathfinder Hero">
        <div class="browse-hero__overlay"></div>
        <div class="browse-hero__inner">
            <div class="browse-hero__badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 256 256" aria-hidden="true">
                    <path d="M224,48H160a40,40,0,0,0-32,16A40,40,0,0,0,96,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96a24,24,0,0,1,24,24,8,8,0,0,0,16,0,24,24,0,0,1,24-24h64a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48Z"/>
                </svg>
                Infrastruktur Literasi
            </div>
            <h1 class="browse-hero__title">Perpustakaan Desa Teras<br><span>Pathfinder</span></h1>
            <p class="browse-hero__desc">
                <?= $activeCategory
                    ? 'Belajar ' . htmlspecialchars($activeCategory['name']) . ' melalui koleksi buku dan panduan pilihan Baca di Teras'
                    : 'Jelajahi panduan literasi terkurasi dari koleksi buku perpustakaan Desa Teras'
                ?>
            </p>
        </div>
    </section>

    <!-- ============================================================
         MAIN — Two-Panel Layout
         ============================================================ -->
    <main class="browse-main" id="browse-main">
        <div class="browse-layout">

            <!-- ── Sidebar (Kategori) ──────────────────────────── -->
            <aside class="browse-sidebar" id="browse-sidebar" aria-label="Daftar Kategori">
                <div class="browse-sidebar__header">KATEGORI</div>
                <nav class="browse-sidebar__nav" role="navigation" aria-label="Navigasi kategori">
                    <?php foreach ($categoriesWithTopics as $cat): ?>
                    <a href="<?= BASE_URL ?>/pathfinder/jelajahi/<?= htmlspecialchars($cat['slug']) ?>"
                       class="browse-cat-item<?= ($cat['slug'] === $activeSlug) ? ' browse-cat-item--active' : '' ?>"
                       id="cat-<?= htmlspecialchars($cat['slug']) ?>"
                       aria-current="<?= ($cat['slug'] === $activeSlug) ? 'page' : 'false' ?>">
                        <span class="browse-cat-item__name"><?= htmlspecialchars($cat['name']) ?></span>
                        <svg class="browse-cat-item__arrow" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <?php endforeach; ?>
                </nav>
            </aside>

            <!-- ── Content Panel ──────────────────────────────── -->
            <section class="browse-content" id="browse-content" aria-label="Daftar Topik">

                <!-- Breadcrumb -->
                <nav class="browse-breadcrumb" aria-label="Breadcrumb">
                    <a href="<?= BASE_URL ?>/pathfinder/jelajahi" class="browse-breadcrumb__link">Pathfinder</a>
                    <?php if ($activeCategory): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round"
                         class="browse-breadcrumb__sep" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    <?php if (!empty($activeTopicName) || !empty($activeBookDetail)): ?>
                    <a href="<?= BASE_URL ?>/pathfinder/jelajahi/<?= htmlspecialchars($activeCategory['slug']) ?>" class="browse-breadcrumb__link"><?= htmlspecialchars($activeCategory['name']) ?></a>
                    <?php else: ?>
                    <span class="browse-breadcrumb__current"><?= htmlspecialchars($activeCategory['name']) ?></span>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if (!empty($activeTopicName)): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round"
                         class="browse-breadcrumb__sep" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    <?php if (!empty($activeBookDetail)): ?>
                    <a href="<?= BASE_URL ?>/pathfinder/jelajahi/<?= htmlspecialchars($activeCategory['slug'] ?? '') ?>" class="browse-breadcrumb__link"><?= htmlspecialchars($activeTopicName) ?></a>
                    <?php else: ?>
                    <span class="browse-breadcrumb__current"><?= htmlspecialchars($activeTopicName) ?></span>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if (!empty($activeBookDetail)): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round"
                         class="browse-breadcrumb__sep" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    <span class="browse-breadcrumb__current" style="color: var(--br-green-700); font-weight: 700;"><?= htmlspecialchars($activeBookDetail['title']) ?></span>
                    <?php endif; ?>
                </nav>

                <!-- Accordion — Topik Cards -->
                <?php if (empty($topicCards)): ?>
                <div class="browse-empty">
                    <p>Belum ada topik dalam kategori ini.</p>
                </div>
                <?php else: ?>
                <div class="ptc-list" id="ptc-list" role="list">
                    <?php foreach ($topicCards as $i => $topic):
                        $isFirst = ($i === 0);
                        include $libPath . '/custom/components/pathfinder-topic-card.php';
                    endforeach; ?>
                </div>
                <?php endif; ?>

            </section><!-- /browse-content -->

        </div><!-- /browse-layout -->
    </main>

    <?php include __DIR__ . '/../../components/footer.php'; ?>

    <!-- ============================================================
         JS: Accordion + Tab switching
         ============================================================ -->
    <script>
    (function () {
        'use strict';

        // ── Accordion ──────────────────────────────────────────
        document.querySelectorAll('.ptc-header').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const panelId = this.getAttribute('aria-controls');
                const panel   = document.getElementById(panelId);
                const isOpen  = this.getAttribute('aria-expanded') === 'true';

                if (isOpen) {
                    this.setAttribute('aria-expanded', 'false');
                    this.classList.remove('ptc-header--open');
                    panel.classList.remove('ptc-panel--open');
                } else {
                    this.setAttribute('aria-expanded', 'true');
                    this.classList.add('ptc-header--open');
                    panel.classList.add('ptc-panel--open');
                }
            });
        });

        // ── Tab switching ──────────────────────────────────────
        // Each accordion item has its own isolated tablist
        document.querySelectorAll('.ptc-tabs').forEach(function (tablist) {
            const tabs   = tablist.querySelectorAll('.ptc-tab');
            const panel  = tablist.closest('.ptc-panel');
            const panels = panel.querySelectorAll('.ptc-tabpanel');

            tabs.forEach(function (tab, idx) {
                tab.addEventListener('click', function () {
                    // Deactivate all
                    tabs.forEach(function (t) {
                        t.classList.remove('ptc-tab--active');
                        t.setAttribute('aria-selected', 'false');
                    });
                    panels.forEach(function (p) {
                        p.classList.remove('ptc-tabpanel--active');
                    });

                    // Activate clicked tab
                    this.classList.add('ptc-tab--active');
                    this.setAttribute('aria-selected', 'true');

                    const targetId = this.getAttribute('aria-controls');
                    const target   = document.getElementById(targetId);
                    if (target) {
                        target.classList.add('ptc-tabpanel--active');
                    }
                });

                // Keyboard: arrow navigation within tablist
                tab.addEventListener('keydown', function (e) {
                    let newIdx = idx;
                    if (e.key === 'ArrowRight') { newIdx = (idx + 1) % tabs.length; }
                    else if (e.key === 'ArrowLeft') { newIdx = (idx - 1 + tabs.length) % tabs.length; }
                    else { return; }
                    e.preventDefault();
                    tabs[newIdx].focus();
                    tabs[newIdx].click();
                });
            });
        });

    })();
    </script>

</body>
</html>
