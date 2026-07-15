<?php
/**
 * Navbar Component
 *
 * File    : navbar.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Usage   : <?php include 'custom/components/navbar.php'; ?>
 *
 * @param string $activePage  Set active menu. Options: 'beranda', 'profil',
 *                            'perpustakaan', 'berita', 'artikel', 'informasi'
 *                            Example: <?php $activePage = 'beranda'; ?>
 */

// Default active page
$activePage = $activePage ?? 'beranda';

// Base URL helper (adjust if project uses a different constant)
$baseUrl = defined('BASE_URL') ? BASE_URL : '';

// Navigation menu items
$navItems = [
    ['id' => 'beranda',       'label' => 'Beranda',       'href' => $baseUrl . '/'],
    ['id' => 'profil',        'label' => 'Profil Desa',   'href' => $baseUrl . '/profil'],
    ['id' => 'perpustakaan',  'label' => 'Perpustakaan',  'href' => $baseUrl . '/perpustakaan'],
    ['id' => 'berita',        'label' => 'Berita',        'href' => $baseUrl . '/berita'],
    ['id' => 'artikel',       'label' => 'Artikel',       'href' => $baseUrl . '/artikel'],
    ['id' => 'informasi',     'label' => 'Informasi',     'href' => $baseUrl . '/informasi'],
];
?>

<!-- Navbar Stylesheet -->
<link rel="stylesheet" href="<?= $baseUrl ?>/custom/assets/css/navbar.css">

<!-- ============================================================
     Navbar Component
     ============================================================ -->
<header class="bdt-navbar" id="bdt-navbar" role="banner">
    <div class="bdt-navbar__container">

        <!-- Logo -->
        <a href="<?= htmlspecialchars($baseUrl . '/') ?>"
           class="bdt-navbar__logo"
           id="bdt-navbar-logo"
           aria-label="Baca Di Teras – Halaman Utama">
            <img src="<?= htmlspecialchars($baseUrl . '/custom/assets/images/logo.png') ?>"
                 alt="Logo Baca Di Teras"
                 class="bdt-navbar__logo-img"
                 width="120"
                 height="40">
        </a>

        <!-- Desktop Navigation Menu -->
        <nav aria-label="Menu Utama">
            <ul class="bdt-navbar__nav" id="bdt-navbar-nav" role="list">
                <?php foreach ($navItems as $item) : ?>
                    <li class="bdt-navbar__nav-item">
                        <a href="<?= htmlspecialchars($item['href']) ?>"
                           id="bdt-nav-<?= htmlspecialchars($item['id']) ?>"
                           class="bdt-navbar__nav-link<?= $activePage === $item['id'] ? ' active' : '' ?>"
                           <?= $activePage === $item['id'] ? 'aria-current="page"' : '' ?>>
                            <?= htmlspecialchars($item['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <!-- Desktop Right Actions -->
        <div class="bdt-navbar__actions" aria-label="Aksi tambahan">

            <!-- Kontak -->
            <a href="<?= htmlspecialchars($baseUrl . '/kontak') ?>"
               id="bdt-navbar-kontak"
               class="bdt-navbar__kontak"
               aria-label="Halaman Kontak">
                <!-- Location pin icon (inline SVG) -->
                <svg class="bdt-navbar__kontak-icon"
                     xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     aria-hidden="true">
                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                Kontak
            </a>

            <!-- Login Keanggotaan Button -->
            <a href="<?= htmlspecialchars($baseUrl . '/login') ?>"
               id="bdt-navbar-login"
               class="bdt-navbar__btn-login"
               aria-label="Login Keanggotaan">
                Login Keanggotaan
            </a>
        </div>

        <!-- Hamburger Button (Mobile) -->
        <button class="bdt-navbar__hamburger"
                id="bdt-navbar-hamburger"
                type="button"
                aria-label="Buka menu navigasi"
                aria-expanded="false"
                aria-controls="bdt-navbar-mobile-menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div><!-- /.bdt-navbar__container -->

    <!-- Mobile Menu -->
    <div class="bdt-navbar__mobile-menu"
         id="bdt-navbar-mobile-menu"
         role="navigation"
         aria-label="Menu Mobile">

        <!-- Mobile Nav Links -->
        <ul class="bdt-navbar__mobile-nav" role="list">
            <?php foreach ($navItems as $item) : ?>
                <li>
                    <a href="<?= htmlspecialchars($item['href']) ?>"
                       id="bdt-mobile-nav-<?= htmlspecialchars($item['id']) ?>"
                       class="bdt-navbar__mobile-link<?= $activePage === $item['id'] ? ' active' : '' ?>"
                       <?= $activePage === $item['id'] ? 'aria-current="page"' : '' ?>>
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="bdt-navbar__mobile-divider" aria-hidden="true"></div>

        <!-- Mobile Actions -->
        <div class="bdt-navbar__mobile-actions">
            <a href="<?= htmlspecialchars($baseUrl . '/kontak') ?>"
               id="bdt-mobile-kontak"
               class="bdt-navbar__mobile-kontak">
                <svg class="bdt-navbar__kontak-icon"
                     xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     aria-hidden="true">
                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                Kontak
            </a>
            <a href="<?= htmlspecialchars($baseUrl . '/login') ?>"
               id="bdt-mobile-login"
               class="bdt-navbar__btn-login--mobile">
                Login Keanggotaan
            </a>
        </div>

    </div><!-- /.bdt-navbar__mobile-menu -->
</header>
<!-- ============================================================
     End Navbar Component
     ============================================================ -->

<!-- Navbar Script -->
<script>
(function () {
    'use strict';

    var hamburger  = document.getElementById('bdt-navbar-hamburger');
    var mobileMenu = document.getElementById('bdt-navbar-mobile-menu');

    if (!hamburger || !mobileMenu) return;

    /**
     * toggleMobileMenu – membuka / menutup mobile menu
     */
    function toggleMobileMenu() {
        var isOpen = mobileMenu.classList.toggle('is-open');
        hamburger.classList.toggle('is-open', isOpen);
        hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        hamburger.setAttribute('aria-label', isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi');
    }

    hamburger.addEventListener('click', toggleMobileMenu);

    // Tutup mobile menu saat klik di luar navbar
    document.addEventListener('click', function (e) {
        var navbar = document.getElementById('bdt-navbar');
        if (navbar && !navbar.contains(e.target)) {
            mobileMenu.classList.remove('is-open');
            hamburger.classList.remove('is-open');
            hamburger.setAttribute('aria-expanded', 'false');
            hamburger.setAttribute('aria-label', 'Buka menu navigasi');
        }
    });

    // Tutup mobile menu saat resize ke desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            mobileMenu.classList.remove('is-open');
            hamburger.classList.remove('is-open');
            hamburger.setAttribute('aria-expanded', 'false');
        }
    });
}());
</script>
