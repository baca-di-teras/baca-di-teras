<?php
/**
 * 404 – Halaman Tidak Ditemukan
 *
 * File    : 404.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Ditampilkan ketika Router tidak menemukan route yang cocok.
 * HTTP status code 404 sudah di-set oleh Router sebelum file ini dipanggil.
 */

// BASE_URL dan ROOT_PATH sudah didefinisikan di index.php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>404 – Halaman Tidak Ditemukan | Baca Di Teras</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary:   #1a6b2f;
            --color-bg:        #f9fafb;
            --color-text:      #111827;
            --color-muted:     #6b7280;
            --color-border:    #e5e7eb;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--color-bg);
            color: var(--color-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            text-align: center;
        }

        .bdt-404__code {
            font-size: clamp(80px, 20vw, 160px);
            font-weight: 800;
            color: var(--color-primary);
            line-height: 1;
            opacity: 0.15;
            user-select: none;
            letter-spacing: -4px;
        }

        .bdt-404__icon {
            width: 80px;
            height: 80px;
            margin: -20px auto 24px;
            color: var(--color-primary);
        }

        .bdt-404__title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .bdt-404__desc {
            font-size: 15px;
            color: var(--color-muted);
            max-width: 400px;
            line-height: 1.7;
            margin-bottom: 36px;
        }

        .bdt-404__actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .bdt-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            border: 2px solid transparent;
            cursor: pointer;
        }

        .bdt-btn--primary {
            background: var(--color-primary);
            color: #fff;
        }
        .bdt-btn--primary:hover {
            background: #155a26;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26,107,47,.3);
        }

        .bdt-btn--outline {
            border-color: var(--color-border);
            color: var(--color-muted);
            background: #fff;
        }
        .bdt-btn--outline:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .bdt-404__links {
            margin-top: 48px;
            padding-top: 32px;
            border-top: 1px solid var(--color-border);
            width: 100%;
            max-width: 480px;
        }

        .bdt-404__links-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--color-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 16px;
        }

        .bdt-404__links-grid {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .bdt-404__link {
            font-size: 13.5px;
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 20px;
            background: #e8f5ec;
            transition: background 0.2s;
        }
        .bdt-404__link:hover { background: #d0edda; }
    </style>
    <link rel="icon" type="image/png" href="<?= defined('BASE_URL') ? BASE_URL : '/baca-di-teras' ?>/custom/assets/images/logo_header.png">
</head>
<body>

    <div class="bdt-404__code" aria-hidden="true">404</div>

    <!-- Ikon buku -->
    <svg class="bdt-404__icon" xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
         aria-hidden="true">
        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
        <line x1="12" y1="7" x2="16" y2="7"/>
        <line x1="12" y1="11" x2="16" y2="11"/>
    </svg>

    <h1 class="bdt-404__title">Halaman Tidak Ditemukan</h1>

    <p class="bdt-404__desc">
        Halaman yang Anda cari tidak tersedia atau mungkin sudah dipindahkan.
        Silakan kembali ke beranda atau jelajahi layanan kami.
    </p>

    <div class="bdt-404__actions">
        <a href="<?= htmlspecialchars($baseUrl) ?>/"
           id="bdt-404-home-btn"
           class="bdt-btn bdt-btn--primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            Kembali ke Beranda
        </a>
        <a href="javascript:history.back()"
           id="bdt-404-back-btn"
           class="bdt-btn bdt-btn--outline">
            ← Halaman Sebelumnya
        </a>
    </div>

    <!-- Tautan cepat -->
    <div class="bdt-404__links">
        <p class="bdt-404__links-title">Atau kunjungi halaman lain</p>
        <div class="bdt-404__links-grid">
            <a href="<?= htmlspecialchars($baseUrl) ?>/perpustakaan"
               class="bdt-404__link" id="bdt-404-link-perpustakaan">Perpustakaan</a>
            <a href="<?= htmlspecialchars($baseUrl) ?>/katalog"
               class="bdt-404__link" id="bdt-404-link-katalog">Katalog Buku</a>
            <a href="<?= htmlspecialchars($baseUrl) ?>/berita"
               class="bdt-404__link" id="bdt-404-link-berita">Berita</a>
            <a href="<?= htmlspecialchars($baseUrl) ?>/kontak"
               class="bdt-404__link" id="bdt-404-link-kontak">Kontak</a>
        </div>
    </div>

</body>
</html>
