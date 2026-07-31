<?php
/**
 * Pusat Informasi & Layanan Page – Baca Di Teras
 *
 * File    : informasi.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Halaman pusat informasi, FAQ, panduan peminjaman, dan bantuan.
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage = 'informasi';

// ── Service Layer ──────────────────────────────────────────────
$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..';
require_once $libPath . '/custom/helpers/Database.php';
require_once $libPath . '/custom/services/InformationService.php';

$infoService = new InformationService();
$faqList     = $infoService->getFaqList();
$infoServices = $infoService->getServicesInfo();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pusat Informasi & Layanan Perpustakaan Desa Teras. Panduan peminjaman, jam operasional, FAQ, tata tertib, dan kontak bantuan.">
    <title>Pusat Informasi & Layanan – Baca Di Teras</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <!-- Main styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/landing.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/informasi.css">
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="bdt-info-wrapper">
        
        <!-- Hero Header -->
        <header class="bdt-info-hero">
            <h1 class="bdt-info-hero__title">Pusat Informasi & Layanan</h1>
            <p class="bdt-info-hero__desc">Segala hal yang perlu Anda ketahui tentang perpustakaan Desa Teras, mulai dari keanggotaan hingga tata tertib peminjaman buku.</p>
        </header>

        <!-- Top Grid (Peminjaman & Jam Operasional) -->
        <div class="bdt-info-grid-top">
            
            <!-- Card Peminjaman -->
            <?php $peminjaman = $infoServices['peminjaman']; ?>
            <div class="bdt-info-card bdt-info-card--featured">
                <div class="bdt-info-card__icon-wrap" aria-hidden="true">
                    <!-- Book Open icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                </div>
                <div class="bdt-info-card__content">
                    <h2 class="bdt-info-card__title"><?= htmlspecialchars($peminjaman['title']) ?></h2>
                    <p class="bdt-info-card__desc"><?= htmlspecialchars($peminjaman['desc'] ?? '') ?></p>

                    <ul class="bdt-info-card__points" role="list">
                        <?php foreach ($peminjaman['points'] ?? [] as $point) : ?>
                            <li class="bdt-info-card__point-item">
                                <!-- Check icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <?= htmlspecialchars($point) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <a href="<?= htmlspecialchars($peminjaman['action_href'] ?? '#') ?>" class="bdt-info-card__action-link">
                        <?= htmlspecialchars($peminjaman['action_label'] ?? 'Selengkapnya') ?> &rarr;
                    </a>
                </div>
            </div>

            <!-- Card Jam Operasional -->
            <?php $jamOp = $infoServices['jam_operasional']; ?>
            <div class="bdt-info-card bdt-info-card--green-bg bdt-info-card--hours">
                <div class="bdt-info-card__icon-wrap bdt-info-card__icon-wrap--white" aria-hidden="true">
                    <!-- Clock icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h2 class="bdt-info-card__title"><?= htmlspecialchars($jamOp['title']) ?></h2>
                
                <div class="bdt-info-card__schedule">
                    <?php foreach ($jamOp['schedule'] ?? [] as $row) : ?>
                        <div class="bdt-info-card__schedule-row">
                            <span class="bdt-info-card__schedule-day"><?= htmlspecialchars($row['day']) ?></span>
                            <span class="bdt-info-card__schedule-time <?= !empty($row['highlight']) ? 'bdt-info-card__schedule-time--highlight' : '' ?>">
                                <?= htmlspecialchars($row['time']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- Bottom Grid (Keanggotaan, Unduhan, Tata Tertib) -->
        <div class="bdt-info-grid-bottom">
            
            <!-- Keanggotaan -->
            <?php $keanggotaan = $infoServices['keanggotaan']; ?>
            <div class="bdt-info-card">
                <div class="bdt-info-card__icon-wrap bdt-info-card__icon-wrap--amber" aria-hidden="true">
                    <!-- User badge card icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                </div>
                <h2 class="bdt-info-card__title"><?= htmlspecialchars($keanggotaan['title']) ?></h2>
                <p class="bdt-info-card__desc"><?= htmlspecialchars($keanggotaan['desc'] ?? '') ?></p>
                <a href="<?= htmlspecialchars($keanggotaan['action_href'] ?? '#') ?>" class="bdt-info-card__action-btn">
                    <?= htmlspecialchars($keanggotaan['action_label'] ?? 'Selengkapnya') ?>
                </a>
            </div>

            <!-- Unduhan -->
            <?php $unduhan = $infoServices['unduhan']; ?>
            <div class="bdt-info-card">
                <div class="bdt-info-card__icon-wrap bdt-info-card__icon-wrap--blue" aria-hidden="true">
                    <!-- Download cloud icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                </div>
                <h2 class="bdt-info-card__title"><?= htmlspecialchars($unduhan['title']) ?></h2>
                
                <div class="bdt-info-card__downloads">
                    <?php foreach ($unduhan['files'] ?? [] as $file) : ?>
                        <a href="<?= htmlspecialchars($file['href']) ?>" class="bdt-info-card__download-item">
                            <span class="bdt-info-card__download-label">
                                <!-- PDF Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                <?= htmlspecialchars($file['label']) ?>
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tata Tertib -->
            <?php $tataTertib = $infoServices['tata_tertib']; ?>
            <div class="bdt-info-card">
                <div class="bdt-info-card__icon-wrap bdt-info-card__icon-wrap--teal" aria-hidden="true">
                    <!-- Message square edit rule icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <h2 class="bdt-info-card__title"><?= htmlspecialchars($tataTertib['title']) ?></h2>
                <p class="bdt-info-card__desc"><?= htmlspecialchars($tataTertib['desc'] ?? '') ?></p>
                
                <div class="bdt-info-card__badges">
                    <?php foreach ($tataTertib['badges'] ?? [] as $badge) : ?>
                        <span class="bdt-info-badge"><?= htmlspecialchars($badge) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- FAQ Section -->
        <section class="bdt-faq-section" aria-label="Pertanyaan Umum">
            <div class="bdt-faq-header">
                <div class="bdt-faq-header__brand">
                    <div class="bdt-faq-header__icon-wrap" aria-hidden="true">
                        <!-- Help circle icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <div>
                        <h2 class="bdt-faq-header__title">Pertanyaan Umum (FAQ)</h2>
                        <p class="bdt-faq-header__subtitle">Cari jawaban cepat untuk keraguan Anda.</p>
                    </div>
                </div>
                
                <!-- Search questions bar -->
                <div class="bdt-faq-search">
                    <input type="text" id="faqSearchInput" class="bdt-faq-search__input" placeholder="Cari pertanyaan...">
                    <!-- Search icon -->
                    <svg class="bdt-faq-search__icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            <!-- FAQ Grid (Using <details> and <summary> for clean accordions) -->
            <div class="bdt-faq-grid" id="faqGrid">
                <?php foreach ($faqList as $faq) : ?>
                    <details class="bdt-faq-item">
                        <summary class="bdt-faq-item__summary">
                            <?= htmlspecialchars($faq['title'] ?? '') ?>
                        </summary>
                        <div class="bdt-faq-item__content">
                            <p><?= nl2br(htmlspecialchars($faq['content'] ?? '')) ?></p>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Help CTA Banner -->
        <?php 
            $jamOp = $infoServices['jam_operasional'] ?? [];
            $waNumber = !empty($jamOp['whatsapp_number']) ? '62' . ltrim($jamOp['whatsapp_number'], '0') : '6281234567890';
            $emailAddr = !empty($jamOp['email_address']) ? $jamOp['email_address'] : '';
        ?>
        <section class="bdt-help-banner" aria-label="Spanduk Bantuan">
            <div class="bdt-help-banner__info">
                <img src="<?= BASE_URL ?>/custom/assets/images/about-village.png" alt="Pustakawan" class="bdt-help-banner__avatar">
                <div>
                    <h3 class="bdt-help-banner__title">Butuh bantuan lebih lanjut?</h3>
                    <p class="bdt-help-banner__desc">Tim pustakawan kami siap membantu menjawab pertanyaan Anda melalui layanan pesan instan atau kunjungan langsung.</p>
                </div>
            </div>
            
            <div class="bdt-help-banner__actions">
                <a href="https://wa.me/<?= htmlspecialchars($waNumber) ?>" target="_blank" rel="noopener noreferrer" class="bdt-help-btn bdt-help-btn--green">
                    <!-- Message icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <span><span>Hubungi via</span><span>WhatsApp</span></span>
                </a>
                
                <?php if ($emailAddr): ?>
                <a href="mailto:<?= htmlspecialchars($emailAddr) ?>" class="bdt-help-btn bdt-help-btn--white">
                    <!-- Mail icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <span><span>Kirim Pesan via</span><span>Email Utama</span></span>
                </a>
                <?php else: ?>
                <a href="<?= BASE_URL ?>/custom/pages/contact.php" class="bdt-help-btn bdt-help-btn--white">
                    <!-- Map/Location icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon>
                        <line x1="9" y1="3" x2="9" y2="18"></line>
                        <line x1="15" y1="6" x2="15" y2="21"></line>
                    </svg>
                    <span><span>Kunjungi Kami</span><span>Lihat Lokasi</span></span>
                </a>
                <?php endif; ?>
            </div>
        </section>

    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- FAQ Search JS script -->
    <script>
        document.getElementById('faqSearchInput').addEventListener('input', function(e) {
            const val = e.target.value.toLowerCase();
            const faqItems = document.querySelectorAll('.bdt-faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.bdt-faq-item__summary').textContent.toLowerCase();
                const answer = item.querySelector('.bdt-faq-item__content').textContent.toLowerCase();
                
                if (question.includes(val) || answer.includes(val)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                    item.removeAttribute('open'); // Close if open
                }
            });
        });
    </script>
</body>
</html>

