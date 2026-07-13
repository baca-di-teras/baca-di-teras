<?php
/**
 * Footer Component
 *
 * File    : footer.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Usage   : <?php include 'custom/components/footer.php'; ?>
 */

// Base URL helper
$baseUrl = defined('BASE_URL') ? BASE_URL : '';

// Tautan Cepat
$quickLinks = [
    ['label' => 'Tentang Kami',         'href' => $baseUrl . '/tentang-kami'],
    ['label' => 'Jaringan Perpustakaan', 'href' => $baseUrl . '/perpustakaan'],
    ['label' => 'Katalog Digital',       'href' => $baseUrl . '/katalog'],
    ['label' => 'Gabung Anggota',        'href' => $baseUrl . '/daftar'],
];

// Media Sosial
$socialLinks = [
    ['label' => 'Instagram',       'href' => 'https://instagram.com/bacaditeras'],
    ['label' => 'Facebook',        'href' => 'https://facebook.com/bacaditeras'],
    ['label' => 'Twitter / X',     'href' => 'https://x.com/bacaditeras'],
    ['label' => 'Saluran YouTube', 'href' => 'https://youtube.com/@bacaditeras'],
];

// Informasi kontak
$contactInfo = [
    [
        'icon' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.56a16 16 0 0 0 6 6l1.62-1.62a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>',
        'text' => '+62 812-3456-7890',
    ],
    [
        'icon' => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
        'text' => 'contact@desateras.id',
    ],
    [
        'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'text' => 'Sen – Sab: 08:00 – 17:00',
    ],
];

$currentYear = date('Y');
?>

<!-- Footer Stylesheet -->
<link rel="stylesheet" href="<?= $baseUrl ?>/custom/assets/css/footer.css">

<!-- ============================================================
     Footer Component
     ============================================================ -->
<footer class="bdt-footer" id="bdt-footer" role="contentinfo">

    <!-- Main Grid -->
    <div class="bdt-footer__main">

        <!-- Kolom 1: Brand -->
        <div class="bdt-footer__col" id="bdt-footer-col-brand">
            <h2 class="bdt-footer__brand-name">Baca Di Teras</h2>
            <p class="bdt-footer__brand-desc">
                Portal Literasi Digital Desa Teras.
                Memberdayakan masyarakat kami
                melalui pengetahuan dan konektivitas.
            </p>

            <!-- Icon Buttons -->
            <div class="bdt-footer__brand-icons">

                <!-- Share / Community Icon -->
                <a href="<?= htmlspecialchars($baseUrl . '/komunitas') ?>"
                   id="bdt-footer-icon-share"
                   class="bdt-footer__icon-btn"
                   aria-label="Komunitas Baca Di Teras">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         aria-hidden="true">
                        <circle cx="18" cy="5" r="3"/>
                        <circle cx="6" cy="12" r="3"/>
                        <circle cx="18" cy="19" r="3"/>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                    </svg>
                </a>

                <!-- Globe / Website Icon -->
                <a href="<?= htmlspecialchars($baseUrl . '/') ?>"
                   id="bdt-footer-icon-globe"
                   class="bdt-footer__icon-btn"
                   aria-label="Website Baca Di Teras">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                </a>

            </div><!-- /.bdt-footer__brand-icons -->
        </div><!-- /.bdt-footer__col (brand) -->

        <!-- Kolom 2: Tautan Cepat -->
        <div class="bdt-footer__col" id="bdt-footer-col-links">
            <h3 class="bdt-footer__col-title">Tautan Cepat</h3>
            <ul class="bdt-footer__links" role="list">
                <?php foreach ($quickLinks as $link) : ?>
                    <li>
                        <a href="<?= htmlspecialchars($link['href']) ?>"
                           id="bdt-footer-link-<?= htmlspecialchars(strtolower(str_replace(' ', '-', $link['label']))) ?>">
                            <?= htmlspecialchars($link['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div><!-- /.bdt-footer__col (links) -->

        <!-- Kolom 3: Media Sosial -->
        <div class="bdt-footer__col" id="bdt-footer-col-social">
            <h3 class="bdt-footer__col-title">Media Sosial</h3>
            <ul class="bdt-footer__links" role="list">
                <?php foreach ($socialLinks as $social) : ?>
                    <li>
                        <a href="<?= htmlspecialchars($social['href']) ?>"
                           id="bdt-footer-social-<?= htmlspecialchars(strtolower(str_replace([' ', '/'], '-', $social['label']))) ?>"
                           target="_blank"
                           rel="noopener noreferrer">
                            <?= htmlspecialchars($social['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div><!-- /.bdt-footer__col (social) -->

        <!-- Kolom 4: Kontak -->
        <div class="bdt-footer__col" id="bdt-footer-col-contact">
            <h3 class="bdt-footer__col-title">Kontak</h3>

            <ul class="bdt-footer__contact-list" role="list">
                <?php foreach ($contactInfo as $contact) : ?>
                    <li class="bdt-footer__contact-item">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             stroke-linecap="round"
                             stroke-linejoin="round"
                             aria-hidden="true">
                            <?= $contact['icon'] ?>
                        </svg>
                        <span><?= htmlspecialchars($contact['text']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Kebijakan Privasi -->
            <p class="bdt-footer__policy-label">Kebijakan Privasi</p>
            <a href="<?= htmlspecialchars($baseUrl . '/syarat-ketentuan') ?>"
               id="bdt-footer-syarat"
               class="bdt-footer__policy-link">
                Syarat &amp; Ketentuan
            </a>

        </div><!-- /.bdt-footer__col (contact) -->

    </div><!-- /.bdt-footer__main -->

    <!-- Bottom Bar -->
    <div class="bdt-footer__bottom">
        <div class="bdt-footer__bottom-inner">
            <p class="bdt-footer__copyright">
                &copy; <?= htmlspecialchars($currentYear) ?> Desa Teras. Portal Literasi Digital. Memberdayakan Masyarakat.
            </p>
        </div>
    </div><!-- /.bdt-footer__bottom -->

</footer>
<!-- ============================================================
     End Footer Component
     ============================================================ -->
