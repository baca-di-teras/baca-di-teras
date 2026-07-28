<?php
/**
 * Pathfinder Topic Card Component
 *
 * File    : pathfinder-topic-card.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Komponen reusable untuk setiap topik dalam accordion Pathfinder browse.
 * Menampilkan header accordion + panel dengan 4 tab:
 *   1. Pendahuluan  (introduction: definition, learning objectives, importance)
 *   2. Koleksi Buku (daftar buku SLiMS via topic_mapping)
 *   3. Tips & Panduan (guides)
 *   4. Download     (downloadable files)
 *
 * Required variables (inject sebelum include):
 *   @var array  $topic           Data topik dari pathfinder_topics
 *   @var array  $topic['books']  Buku dari PathfinderV2Service::getBooksByTopicId()
 *   @var array  $topic['intro']  Data dari PathfinderV2Service::getIntroduction()
 *   @var array  $topic['guides'] Data dari PathfinderV2Service::getGuidesByTopicId()
 *   @var array  $topic['downloads'] Data dari PathfinderV2Service::getDownloadsByTopicId()
 *   @var bool   $isFirst         True jika topik pertama (auto-expand)
 *   @var string BASE_URL
 */

$slug        = htmlspecialchars($topic['slug']);
$topicId     = (int) $topic['id'];
$isOpen      = $isFirst ?? false;
$books       = $topic['books']       ?? [];
$intro       = $topic['intro']       ?? null;
$guides      = $topic['guides']      ?? [];
$downloads   = $topic['downloads']   ?? [];
$totalBooks  = count($books);
$catSlug     = $topic['category_slug'] ?? $activeSlug ?? 'literasi-lingkungan';
$catName     = $topic['category_name'] ?? 'Literasi Lingkungan';

$isTopicForActiveBook = false;
if (!empty($activeBookId) && $activeBookId > 0 && !empty($books)) {
    foreach ($books as $b) {
        if ((int)$b['biblio_id'] === (int)$activeBookId) {
            $isTopicForActiveBook = true;
            break;
        }
    }
} elseif (!empty($activeTopicSlug) && $activeTopicSlug === $slug) {
    $isTopicForActiveBook = true;
}
if ($isTopicForActiveBook) {
    $isOpen = true;
}
?>
<div class="ptc-item" id="ptc-<?= $slug ?>" data-topic-id="<?= $topicId ?>">

    <!-- ── Accordion Header ─────────────────────────────────── -->
    <button
        class="ptc-header<?= $isOpen ? ' ptc-header--open' : '' ?>"
        type="button"
        aria-expanded="<?= $isOpen ? 'true' : 'false' ?>"
        aria-controls="ptc-panel-<?= $slug ?>"
        id="ptc-btn-<?= $slug ?>"
    >
        <span class="ptc-header__title"><?= htmlspecialchars($topic['name']) ?></span>
        <span class="ptc-header__badge"><?= $totalBooks ?> koleksi</span>
        <svg class="ptc-header__chevron" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
    </button>

    <!-- ── Expanded Panel ───────────────────────────────────── -->
    <div
        class="ptc-panel<?= $isOpen ? ' ptc-panel--open' : '' ?>"
        id="ptc-panel-<?= $slug ?>"
        role="region"
        aria-labelledby="ptc-btn-<?= $slug ?>"
    >
        <!-- Deskripsi singkat topik -->
        <?php if (!empty($topic['description'])): ?>
        <p class="ptc-panel__desc"><?= htmlspecialchars($topic['description']) ?></p>
        <?php endif; ?>

        <!-- ── Tab Navigation ───────────────────────────────── -->
        <div class="ptc-tabs" role="tablist" aria-label="Konten topik <?= htmlspecialchars($topic['name']) ?>">
            <button class="ptc-tab<?= $isTopicForActiveBook ? '' : ' ptc-tab--active' ?>"
                    role="tab"
                    aria-selected="<?= $isTopicForActiveBook ? 'false' : 'true' ?>"
                    aria-controls="ptc-tab-intro-<?= $slug ?>"
                    id="ptc-tablink-intro-<?= $slug ?>"
                    data-tab="intro">
                Pendahuluan
            </button>
            <button class="ptc-tab<?= $isTopicForActiveBook ? ' ptc-tab--active' : '' ?>"
                    role="tab"
                    aria-selected="<?= $isTopicForActiveBook ? 'true' : 'false' ?>"
                    aria-controls="ptc-tab-books-<?= $slug ?>"
                    id="ptc-tablink-books-<?= $slug ?>"
                    data-tab="books">
                Koleksi Buku
                <?php if ($totalBooks > 0): ?>
                <span class="ptc-tab__count"><?= $totalBooks ?></span>
                <?php endif; ?>
            </button>
            <button class="ptc-tab"
                    role="tab"
                    aria-selected="false"
                    aria-controls="ptc-tab-guides-<?= $slug ?>"
                    id="ptc-tablink-guides-<?= $slug ?>"
                    data-tab="guides">
                Tips &amp; Panduan
                <?php if (!empty($guides)): ?>
                <span class="ptc-tab__count"><?= count($guides) ?></span>
                <?php endif; ?>
            </button>
            <button class="ptc-tab"
                    role="tab"
                    aria-selected="false"
                    aria-controls="ptc-tab-downloads-<?= $slug ?>"
                    id="ptc-tablink-downloads-<?= $slug ?>"
                    data-tab="downloads">
                Download
                <?php if (!empty($downloads)): ?>
                <span class="ptc-tab__count"><?= count($downloads) ?></span>
                <?php endif; ?>
            </button>
        </div>

        <!-- ── Tab Panels ───────────────────────────────────── -->

        <!-- Tab 1: Pendahuluan -->
        <div class="ptc-tabpanel<?= $isTopicForActiveBook ? '' : ' ptc-tabpanel--active' ?>"
             id="ptc-tab-intro-<?= $slug ?>"
             role="tabpanel"
             aria-labelledby="ptc-tablink-intro-<?= $slug ?>">

            <?php if ($intro): ?>
            <div class="ptc-intro">
                <?php if (!empty($intro['definition'])): ?>
                <div class="ptc-intro__section">
                    <h3 class="ptc-intro__heading"><?= htmlspecialchars($topic['name']) ?></h3>
                    <div class="ptc-intro__body"><?= $intro['definition'] ?></div>
                </div>
                <?php endif; ?>

                <?php if (!empty($intro['learning_objectives']) || !empty($intro['importance'])): ?>
                <div class="ptc-intro__grid">
                    <?php if (!empty($intro['learning_objectives'])): ?>
                    <div class="ptc-intro__card ptc-intro__card--objectives">
                        <div class="ptc-intro__card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 256 256" aria-hidden="true">
                                <path d="M243.31,90.91l-128.4-64.2a16,16,0,0,0-14.62,0L12.69,90.91a15.95,15.95,0,0,0,0,28.18L67,147.79,12.69,176a15.95,15.95,0,0,0,0,28.18l128.4,64.2a16,16,0,0,0,14.62,0l128.4-64.2a15.95,15.95,0,0,0,0-28.18L230.29,147.8l54.31-28.71a15.95,15.95,0,0,0,0-28.18Z"/>
                            </svg>
                        </div>
                        <h4>Tujuan Pembelajaran</h4>
                        <div class="ptc-intro__card-body"><?= $intro['learning_objectives'] ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($intro['importance'])): ?>
                    <div class="ptc-intro__card ptc-intro__card--importance">
                        <h4>Mengapa <?= htmlspecialchars($topic['name']) ?> Penting?</h4>
                        <div class="ptc-intro__card-body"><?= $intro['importance'] ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($intro['topics_to_learn'])): ?>
                <div class="ptc-intro__section">
                    <h4 class="ptc-intro__subheading">Topik yang Dipelajari</h4>
                    <div class="ptc-intro__body"><?= $intro['topics_to_learn'] ?></div>
                </div>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="ptc-empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 256 256" aria-hidden="true">
                    <path d="M224,48H160a40,40,0,0,0-32,16A40,40,0,0,0,96,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96a24,24,0,0,1,24,24,8,8,0,0,0,16,0,24,24,0,0,1,24-24h64a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48Z"/>
                </svg>
                <p>Pendahuluan belum tersedia untuk topik ini.</p>
                <p class="ptc-empty__hint">Pustakawan dapat menambahkan konten pendahuluan melalui Portal Admin.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tab 2: Koleksi Buku -->
        <div class="ptc-tabpanel<?= $isTopicForActiveBook ? ' ptc-tabpanel--active' : '' ?>"
             id="ptc-tab-books-<?= $slug ?>"
             role="tabpanel"
             aria-labelledby="ptc-tablink-books-<?= $slug ?>">

            <?php if ($isTopicForActiveBook && !empty($activeBookDetail)): ?>
            <!-- ── Tampilan Detail Buku di Dalam Topik Pathfinder ── -->
            <div class="ptc-book-detail">
                <!-- Left Column -->
                <div class="ptc-book-detail__left">
                    <div class="ptc-book-detail__cover-card">
                        <div class="ptc-book-detail__cover-wrap">
                            <?php if (!empty($activeBookDetail['image'])): ?>
                            <img src="<?= BASE_URL ?>/slims/images/docs/<?= htmlspecialchars($activeBookDetail['image']) ?>" alt="Sampul <?= htmlspecialchars($activeBookDetail['title']) ?>" onerror="this.src='<?= BASE_URL ?>/custom/assets/img/book-cover-placeholder.svg'">
                            <?php else: ?>
                            <div class="ptc-book-item__cover-ph" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#e2e8f0;color:#64748b;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 256 256"><path d="M224,48H160a40,40,0,0,0-32,16A40,40,0,0,0,96,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96a24,24,0,0,1,24,24,8,8,0,0,0,16,0,24,24,0,0,1,24-24h64a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48Z"/></svg>
                            </div>
                            <?php endif; ?>
                        </div>
                        <h3 class="ptc-book-detail__card-title"><?= htmlspecialchars($activeBookDetail['title']) ?></h3>
                        <div class="ptc-book-detail__card-author">Author: <?= htmlspecialchars(!empty($activeBookDetail['author']) ? $activeBookDetail['author'] : 'Tim Penulis') ?></div>
                        <div class="ptc-book-detail__card-cat">Kategory: <?= htmlspecialchars($catName) ?> / <?= htmlspecialchars($topic['name']) ?></div>
                        <div class="ptc-book-detail__card-rating">
                            <span class="ptc-stars">★★★★★</span> (4.5/5, 128 reviews)
                        </div>
                        <div class="ptc-book-detail__card-isbn">ISBN: <?= htmlspecialchars(!empty($activeBookDetail['isbn_issn']) ? $activeBookDetail['isbn_issn'] : '-') ?></div>
                        <p class="ptc-book-detail__card-snippet">
                            <?= htmlspecialchars(mb_substr(trim(strip_tags(!empty($activeBookDetail['notes']) ? $activeBookDetail['notes'] : 'Panduan praktis literasi terkurasi yang kaya dengan wawasan dan mudah diterapkan dalam kehidupan sehari-hari.')), 0, 120)) ?>...
                        </p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="ptc-book-detail__right">
                    <span class="ptc-book-detail__badge">Katalog <?= htmlspecialchars(trim(str_replace('Literasi', '', $catName))) ?></span>
                    <h2 class="ptc-book-detail__main-title"><?= htmlspecialchars(!empty($activeBookDetail['title']) ? $activeBookDetail['title'] : '') ?></h2>
                    <div class="ptc-book-detail__main-author"><?= htmlspecialchars(!empty($activeBookDetail['author']) ? $activeBookDetail['author'] : 'Tim Penulis') ?></div>

                    <!-- Metadata Grid -->
                    <div class="ptc-book-detail__grid">
                        <div class="ptc-book-detail__grid-item">
                            <span class="ptc-book-detail__grid-label">PENERBIT</span>
                            <span class="ptc-book-detail__grid-val"><?= htmlspecialchars(!empty($activeBookDetail['publisher']) ? $activeBookDetail['publisher'] : 'Teras Hijau Press') ?></span>
                        </div>
                        <div class="ptc-book-detail__grid-item">
                            <span class="ptc-book-detail__grid-label">TAHUN TERBIT</span>
                            <span class="ptc-book-detail__grid-val"><?= htmlspecialchars(!empty($activeBookDetail['publish_year']) ? $activeBookDetail['publish_year'] : '2023') ?></span>
                        </div>
                        <div class="ptc-book-detail__grid-item">
                            <span class="ptc-book-detail__grid-label">HALAMAN</span>
                            <span class="ptc-book-detail__grid-val"><?= htmlspecialchars(!empty($activeBookDetail['collation']) ? $activeBookDetail['collation'] : '184 Hal.') ?></span>
                        </div>
                        <div class="ptc-book-detail__grid-item">
                            <span class="ptc-book-detail__grid-label">BAHASA</span>
                            <span class="ptc-book-detail__grid-val"><?= htmlspecialchars(!empty($activeBookDetail['language']) ? $activeBookDetail['language'] : 'Indonesia') ?></span>
                        </div>
                    </div>

                    <!-- Sinopsis -->
                    <div class="ptc-book-detail__section">
                        <div class="ptc-book-detail__sec-header">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M224,128a8,8,0,0,1-8,8H40a8,8,0,0,1,0-16H216A8,8,0,0,1,224,128ZM40,72H216a8,8,0,0,0,0-16H40a8,8,0,0,0,0,16ZM216,184H40a8,8,0,0,0,0-16H216a8,8,0,0,0,0-16Z"/></svg>
                            <span>SINOPSIS</span>
                        </div>
                        <div class="ptc-book-detail__synopsis">
                            <?= !empty($activeBookDetail['notes']) ? nl2br(htmlspecialchars(trim(strip_tags($activeBookDetail['notes'])))) : 'Buku ini merupakan panduan praktis bagi masyarakat desa maupun perkotaan yang ingin mempelajari materi topik ini secara mendalam. Disajikan dengan bahasa yang lugas, mudah dipahami, serta dilengkapi dengan langkah-langkah implementasi nyata dalam kehidupan sehari-hari.' ?>
                        </div>
                    </div>

                    <!-- Manfaat Membaca -->
                    <div class="ptc-book-detail__section">
                        <div class="ptc-book-detail__sec-header">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm45.66,85.66-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32Z"/></svg>
                            <span>MANFAAT MEMBACA</span>
                        </div>
                        <div class="ptc-book-detail__benefits">
                            <div class="ptc-benefit-item"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm45.66,85.66-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32Z"/></svg> <span>Memahami dasar dan konsep utama <?= htmlspecialchars(strtolower($topic['name'])) ?>.</span></div>
                            <div class="ptc-benefit-item"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm45.66,85.66-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32Z"/></svg> <span>Panduan praktis yang mudah diaplikasikan sehari-hari.</span></div>
                            <div class="ptc-benefit-item"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm45.66,85.66-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32Z"/></svg> <span>Meningkatkan wawasan literasi secara efektif & mandiri.</span></div>
                            <div class="ptc-benefit-item"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm45.66,85.66-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32Z"/></svg> <span>Referensi terpercaya dari koleksi Perpustakaan Baca Di Teras.</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Related Books Section ── -->
            <?php
            $relatedBooks = [];
            if (!empty($svc) && method_exists($svc, 'getRelatedBooks')) {
                $relatedBooks = $svc->getRelatedBooks($activeBookId);
            }
            if (count($relatedBooks) < 3 && !empty($books)) {
                foreach ($books as $b) {
                    if ((int)$b['biblio_id'] !== (int)$activeBookId) {
                        $exists = false;
                        foreach ($relatedBooks as $rb) {
                            if ((int)$rb['biblio_id'] === (int)$b['biblio_id']) { $exists = true; break; }
                        }
                        if (!$exists) { $relatedBooks[] = $b; }
                    }
                }
            }
            ?>
            <div class="ptc-related-section">
                <h3 class="ptc-related-section__title">Related Books</h3>
                <div class="ptc-book-list" style="padding-top:0;">
                    <?php foreach ($relatedBooks as $book):
                        $author = !empty($book['author_name']) ? $book['author_name'] : (!empty($book['sor']) ? $book['sor'] : (!empty($book['author']) ? $book['author'] : ''));
                        $desc   = !empty($book['notes']) ? trim(strip_tags($book['notes'])) : '';
                    ?>
                    <div class="ptc-book-item" id="book-<?= (int)$book['biblio_id'] ?>">
                        <div class="ptc-book-item__cover">
                            <?php if (!empty($book['image'])): ?>
                            <img src="<?= BASE_URL ?>/slims/images/docs/<?= htmlspecialchars($book['image']) ?>" alt="Sampul <?= htmlspecialchars($book['title']) ?>" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                            <div class="ptc-book-item__cover-ph" style="display:none"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 256 256"><path d="M224,48H160a40,40,0,0,0-32,16A40,40,0,0,0,96,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96a24,24,0,0,1,24,24,8,8,0,0,0,16,0,24,24,0,0,1,24-24h64a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48Z"/></svg></div>
                            <?php else: ?>
                            <div class="ptc-book-item__cover-ph"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 256 256"><path d="M224,48H160a40,40,0,0,0-32,16A40,40,0,0,0,96,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96a24,24,0,0,1,24,24,8,8,0,0,0,16,0,24,24,0,0,1,24-24h64a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48Z"/></svg></div>
                            <?php endif; ?>
                        </div>
                        <div class="ptc-book-item__info">
                            <h4 class="ptc-book-item__title"><?= htmlspecialchars($book['title']) ?></h4>
                            <?php if ($author): ?><div class="ptc-book-item__author"><?= htmlspecialchars($author) ?></div><?php endif; ?>
                            <?php if ($desc): ?><p class="ptc-book-item__desc"><?= htmlspecialchars($desc) ?></p><?php endif; ?>
                            <div class="ptc-book-item__meta">
                                <?php if (!empty($book['call_number'])): ?><span class="ptc-book-item__callno"><?= htmlspecialchars($book['call_number']) ?></span><?php endif; ?>
                                <?php if (!empty($book['publish_year'])): ?><span class="ptc-book-item__year"><?= htmlspecialchars($book['publish_year']) ?></span><?php endif; ?>
                            </div>
                        </div>
                        <a href="<?= BASE_URL ?>/pathfinder/jelajahi/<?= htmlspecialchars($catSlug) ?>/<?= htmlspecialchars($slug) ?>/buku/<?= (int)$book['biblio_id'] ?>" class="ptc-book-item__btn">Lihat Detail</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php elseif (empty($books)): ?>
            <div class="ptc-empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 256 256" aria-hidden="true">
                    <path d="M224,48H160a40,40,0,0,0-32,16A40,40,0,0,0,96,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96a24,24,0,0,1,24,24,8,8,0,0,0,16,0,24,24,0,0,1,24-24h64a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48Z"/>
                </svg>
                <p>Belum ada koleksi buku yang dipetakan ke topik ini.</p>
                <p class="ptc-empty__hint">Pustakawan dapat menambahkan pemetaan topik melalui Portal Admin.</p>
            </div>
            <?php else: ?>
            <div class="ptc-book-list">
                <h3 class="ptc-tabpanel__heading">Koleksi Buku</h3>
                <?php foreach ($books as $book):
                    $author = !empty($book['author_name']) ? $book['author_name'] : (!empty($book['sor']) ? $book['sor'] : (!empty($book['author']) ? $book['author'] : ''));
                    $desc   = !empty($book['notes']) ? trim(strip_tags($book['notes'])) : '';
                ?>
                <div class="ptc-book-item" id="book-<?= (int)$book['biblio_id'] ?>">
                    <!-- Cover -->
                    <div class="ptc-book-item__cover">
                        <?php if (!empty($book['image'])): ?>
                        <img src="<?= BASE_URL ?>/slims/images/docs/<?= htmlspecialchars($book['image']) ?>"
                             alt="Sampul <?= htmlspecialchars($book['title']) ?>"
                             loading="lazy"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="ptc-book-item__cover-ph" style="display:none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 256 256" aria-hidden="true"><path d="M224,48H160a40,40,0,0,0-32,16A40,40,0,0,0,96,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96a24,24,0,0,1,24,24,8,8,0,0,0,16,0,24,24,0,0,1,24-24h64a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48Z"/></svg>
                        </div>
                        <?php else: ?>
                        <div class="ptc-book-item__cover-ph">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 256 256" aria-hidden="true"><path d="M224,48H160a40,40,0,0,0-32,16A40,40,0,0,0,96,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96a24,24,0,0,1,24,24,8,8,0,0,0,16,0,24,24,0,0,1,24-24h64a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48Z"/></svg>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Info -->
                    <div class="ptc-book-item__info">
                        <h4 class="ptc-book-item__title"><?= htmlspecialchars($book['title']) ?></h4>
                        <?php if ($author): ?>
                        <div class="ptc-book-item__author"><?= htmlspecialchars($author) ?></div>
                        <?php endif; ?>
                        <?php if ($desc): ?>
                        <p class="ptc-book-item__desc"><?= htmlspecialchars($desc) ?></p>
                        <?php endif; ?>
                        <div class="ptc-book-item__meta">
                            <?php if (!empty($book['call_number'])): ?>
                            <span class="ptc-book-item__callno"><?= htmlspecialchars($book['call_number']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($book['publish_year'])): ?>
                            <span class="ptc-book-item__year"><?= htmlspecialchars($book['publish_year']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($book['total_eksemplar'])): ?>
                            <span class="ptc-book-item__copies"><?= (int)$book['total_eksemplar'] ?> eksemplar</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Button Lihat Detail -->
                    <a href="<?= BASE_URL ?>/pathfinder/jelajahi/<?= htmlspecialchars($catSlug) ?>/<?= htmlspecialchars($slug) ?>/buku/<?= (int)$book['biblio_id'] ?>"
                       class="ptc-book-item__btn"
                       aria-label="Lihat detail buku <?= htmlspecialchars($book['title']) ?>">
                        Lihat Detail
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tab 3: Tips & Panduan -->
        <div class="ptc-tabpanel"
             id="ptc-tab-guides-<?= $slug ?>"
             role="tabpanel"
             aria-labelledby="ptc-tablink-guides-<?= $slug ?>">

            <!-- Section Header -->
            <div class="ptc-guides-header">
                <h2 class="ptc-guides-header__title">Tips &amp; Panduan <?= htmlspecialchars($catName) ?></h2>
                <p class="ptc-guides-header__desc">Pelajari langkah-langkah praktis untuk <?= htmlspecialchars(strtolower($topic['name'])) ?> dari rumah. Temukan berbagai panduan edukatif yang dirancang khusus untuk komunitas desa.</p>
            </div>

            <?php if (empty($guides)): ?>
            <!-- Empty state: show placeholder cards for demo purposes -->
            <div class="ptc-guide-grid">
                <?php
                // Placeholder guide cards when no guides exist
                $placeholderGuides = [
                    [
                        'badge'  => $topic['name'],
                        'title'  => 'Panduan Praktis ' . $topic['name'],
                        'desc'   => 'Temukan panduan langkah demi langkah untuk memahami dan menerapkan konsep ' . strtolower($topic['name']) . ' dalam kehidupan sehari-hari.',
                        'image'  => null,
                        'slug'   => '#',
                        'href'   => '#',
                    ],
                    [
                        'badge'  => $catName,
                        'title'  => 'Tips Memulai ' . $topic['name'],
                        'desc'   => 'Berkembang bersama komunitas literasi dengan modal pengetahuan minimal dan langkah-langkah yang mudah diikuti.',
                        'image'  => null,
                        'slug'   => '#',
                        'href'   => '#',
                    ],
                ];
                foreach ($placeholderGuides as $g):
                ?>
                <div class="ptc-guide-card">
                    <div class="ptc-guide-card__img-wrap">
                        <?php if (!empty($g['image'])): ?>
                        <img src="<?= BASE_URL . htmlspecialchars($g['image']) ?>"
                             alt="<?= htmlspecialchars($g['title']) ?>"
                             loading="lazy"
                             class="ptc-guide-card__img">
                        <?php else: ?>
                        <div class="ptc-guide-card__img-ph">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 256 256" aria-hidden="true"><path d="M232,208a8,8,0,0,1-8,8H32a8,8,0,0,1-8-8V48a8,8,0,0,1,16,0V156.69l50.34-50.35a8,8,0,0,1,11.32,0L128,132.69,180.69,80a8,8,0,0,1,11.32,0L224,111.31V48a8,8,0,0,1,16,0V208Z"/></svg>
                        </div>
                        <?php endif; ?>
                        <span class="ptc-guide-card__badge"><?= htmlspecialchars($g['badge']) ?></span>
                    </div>
                    <div class="ptc-guide-card__body">
                        <h3 class="ptc-guide-card__title"><?= htmlspecialchars($g['title']) ?></h3>
                        <p class="ptc-guide-card__desc"><?= htmlspecialchars($g['desc']) ?></p>
                        <a href="<?= htmlspecialchars($g['href']) ?>"
                           class="ptc-guide-card__link"
                           aria-label="Pelajari selengkapnya tentang <?= htmlspecialchars($g['title']) ?>">
                            Pelajari Selengkapnya
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                 stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <p class="ptc-guides-empty-note">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 256 256" aria-hidden="true"><path d="M236,128A108,108,0,1,1,128,20,108.12,108.12,0,0,1,236,128Zm-108,40a12,12,0,1,0,12,12A12,12,0,0,0,128,168Zm-4-24h8a4,4,0,0,0,4-4V84a4,4,0,0,0-4-4h-8a4,4,0,0,0-4,4v56A4,4,0,0,0,124,144Z"/></svg>
                Konten panduan nyata dapat ditambahkan melalui Portal Admin.
            </p>

            <?php else: ?>
            <!-- Real guide cards from database -->
            <div class="ptc-guide-grid">
                <?php foreach ($guides as $guide): ?>
                <div class="ptc-guide-card">
                    <div class="ptc-guide-card__img-wrap">
                        <?php if (!empty($guide['thumbnail'])): ?>
                        <img src="<?= BASE_URL . htmlspecialchars($guide['thumbnail']) ?>"
                             alt="<?= htmlspecialchars($guide['title']) ?>"
                             loading="lazy"
                             class="ptc-guide-card__img">
                        <?php else: ?>
                        <div class="ptc-guide-card__img-ph">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 256 256" aria-hidden="true"><path d="M232,208a8,8,0,0,1-8,8H32a8,8,0,0,1-8-8V48a8,8,0,0,1,16,0V156.69l50.34-50.35a8,8,0,0,1,11.32,0L128,132.69,180.69,80a8,8,0,0,1,11.32,0L224,111.31V48a8,8,0,0,1,16,0V208Z"/></svg>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($guide['category_label'])): ?>
                        <span class="ptc-guide-card__badge"><?= htmlspecialchars($guide['category_label']) ?></span>
                        <?php else: ?>
                        <span class="ptc-guide-card__badge"><?= htmlspecialchars($topic['name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="ptc-guide-card__body">
                        <h3 class="ptc-guide-card__title"><?= htmlspecialchars($guide['title']) ?></h3>
                        <?php if (!empty($guide['description'])): ?>
                        <p class="ptc-guide-card__desc"><?= htmlspecialchars(mb_substr(strip_tags($guide['description']), 0, 140)) ?></p>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/pathfinder/panduan/<?= htmlspecialchars($guide['slug']) ?>"
                           class="ptc-guide-card__link"
                           aria-label="Pelajari selengkapnya tentang <?= htmlspecialchars($guide['title']) ?>">
                            Pelajari Selengkapnya
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                 stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>


        <!-- Tab 4: Download -->
        <div class="ptc-tabpanel"
             id="ptc-tab-downloads-<?= $slug ?>"
             role="tabpanel"
             aria-labelledby="ptc-tablink-downloads-<?= $slug ?>">

            <?php if (empty($downloads)): ?>
            <div class="ptc-empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 256 256" aria-hidden="true">
                    <path d="M224,144v64a16,16,0,0,1-16,16H48a16,16,0,0,1-16-16V144a16,16,0,0,1,16-16H80a8,8,0,0,1,0,16H48v64H208V144H176a8,8,0,0,1,0-16h32A16,16,0,0,1,224,144Zm-101.66-42.34a8,8,0,0,0,11.32,0l40-40a8,8,0,0,0-11.32-11.32L136,76.69V24a8,8,0,0,0-16,0V76.69L93.66,50.34A8,8,0,0,0,82.34,61.66Z"/>
                </svg>
                <p>Belum ada file download untuk topik ini.</p>
                <p class="ptc-empty__hint">Pustakawan dapat menambahkan file download melalui Portal Admin.</p>
            </div>
            <?php else: ?>
            <div class="ptc-download-list">
                <?php
                $fileTypeIcons = [
                    'pdf'  => 'M215.88,199.29l-22.57-22.57A71.63,71.63,0,0,0,200,152a72,72,0,1,0-72,72,71.63,71.63,0,0,0,24.72-6.69l22.57,22.57a8,8,0,0,0,11.31-11.31ZM112,208a56,56,0,1,1,56-56A56.06,56.06,0,0,1,112,208Zm120-128V192a8,8,0,0,1-16,0V96H168a8,8,0,0,1-8-8V40H96v72a8,8,0,0,1-16,0V40A16,16,0,0,1,96,24h72a8,8,0,0,1,5.65,2.34l40,40A8,8,0,0,1,232,80Z',
                    'jpg'  => 'M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40ZM40,200V56H216V200ZM208,168a8,8,0,0,1-8,8H56a8,8,0,0,1-5.66-13.66l36-36a8,8,0,0,1,11.31,0l19.32,19.31L132.69,130a8,8,0,0,1,11.31,0l58.34,58.34A8,8,0,0,1,208,168Z',
                    'png'  => 'M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40ZM40,200V56H216V200ZM208,168a8,8,0,0,1-8,8H56a8,8,0,0,1-5.66-13.66l36-36a8,8,0,0,1,11.31,0l19.32,19.31L132.69,130a8,8,0,0,1,11.31,0l58.34,58.34A8,8,0,0,1,208,168Z',
                    'default' => 'M224,144v64a16,16,0,0,1-16,16H48a16,16,0,0,1-16-16V144a16,16,0,0,1,16-16H80a8,8,0,0,1,0,16H48v64H208V144H176a8,8,0,0,1,0-16h32A16,16,0,0,1,224,144Zm-101.66-42.34a8,8,0,0,0,11.32,0l40-40a8,8,0,0,0-11.32-11.32L136,76.69V24a8,8,0,0,0-16,0V76.69L93.66,50.34A8,8,0,0,0,82.34,61.66Z',
                ];
                foreach ($downloads as $dl):
                    $iconPath = $fileTypeIcons[$dl['file_type']] ?? $fileTypeIcons['default'];
                    $sizeKb   = $dl['file_size'] > 0 ? round($dl['file_size'] / 1024) . ' KB' : '';
                ?>
                <div class="ptc-download-item">
                    <div class="ptc-download-item__icon ptc-download-item__icon--<?= htmlspecialchars($dl['file_type']) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 256 256" aria-hidden="true">
                            <path d="<?= $iconPath ?>"/>
                        </svg>
                        <span class="ptc-download-item__ext"><?= strtoupper(htmlspecialchars($dl['file_type'])) ?></span>
                    </div>
                    <div class="ptc-download-item__info">
                        <h4 class="ptc-download-item__title"><?= htmlspecialchars($dl['title']) ?></h4>
                        <?php if (!empty($dl['description'])): ?>
                        <p class="ptc-download-item__desc"><?= htmlspecialchars($dl['description']) ?></p>
                        <?php endif; ?>
                        <div class="ptc-download-item__meta">
                            <?php if ($sizeKb): ?><span><?= $sizeKb ?></span><?php endif; ?>
                            <?php if ($dl['download_count'] > 0): ?>
                            <span><?= (int)$dl['download_count'] ?> unduhan</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>/pathfinder/download/<?= (int)$dl['id'] ?>"
                       class="ptc-download-item__btn"
                       download
                       aria-label="Unduh <?= htmlspecialchars($dl['title']) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 256 256" aria-hidden="true">
                            <path d="M224,144v64a16,16,0,0,1-16,16H48a16,16,0,0,1-16-16V144a16,16,0,0,1,16-16H80a8,8,0,0,1,0,16H48v64H208V144H176a8,8,0,0,1,0-16h32A16,16,0,0,1,224,144Zm-101.66-42.34a8,8,0,0,0,11.32,0l40-40a8,8,0,0,0-11.32-11.32L136,76.69V24a8,8,0,0,0-16,0V76.69L93.66,50.34A8,8,0,0,0,82.34,61.66Z"/>
                        </svg>
                        Unduh
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

    </div><!-- /ptc-panel -->
</div><!-- /ptc-item -->
