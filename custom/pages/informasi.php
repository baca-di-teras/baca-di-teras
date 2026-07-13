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

define('BASE_URL', '/baca-di-teras');

$activePage = 'informasi';

// Load config data
require_once __DIR__ . '/../config/info-config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pusat Informasi & Layanan Perpustakaan Desa Teras. Panduan peminjaman, jam operasional, FAQ, tata tertib, dan kontak bantuan.">
    <title>Pusat Informasi & Layanan – Baca Di Teras</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Main styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/landing.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fafbfc;
            color: #101814;
            margin: 0;
        }

        .bdt-info-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 56px 24px 96px;
        }

        /* Hero Header */
        .bdt-info-hero {
            text-align: center;
            margin-bottom: 56px;
        }
        .bdt-info-hero__title {
            font-size: 38px;
            font-weight: 800;
            color: #0e5e32;
            margin: 0 0 16px;
            letter-spacing: -0.02em;
        }
        .bdt-info-hero__desc {
            font-size: 16px;
            color: #64748b;
            max-width: 680px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Grid Layout */
        .bdt-info-grid-top {
            display: grid;
            grid-template-columns: 1.8fr 1.2fr;
            gap: 28px;
            margin-bottom: 28px;
        }
        .bdt-info-grid-bottom {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            margin-bottom: 64px;
        }

        /* Card Styles */
        .bdt-info-card {
            background-color: #ffffff;
            border: 1px solid #eef2ed;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.01);
            display: flex;
            flex-direction: column;
        }
        .bdt-info-card__icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background-color: #f0f9f2;
            color: #1a6b2f;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }
        
        /* Card Background Modifier */
        .bdt-info-card--green-bg {
            background-color: #ecf3ee !important;
            border-color: #dbe4dd !important;
        }

        /* Icon Wrap Modifiers */
        .bdt-info-card__icon-wrap--white {
            background-color: #ffffff !important;
            color: #1a6b2f !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .bdt-info-card__icon-wrap--amber {
            background-color: #fef3c7 !important;
            color: #d97706 !important;
        }
        .bdt-info-card__icon-wrap--blue {
            background-color: #e0f2fe !important;
            color: #0284c7 !important;
        }
        .bdt-info-card__icon-wrap--teal {
            background-color: #ccfbf1 !important;
            color: #0d9488 !important;
        }

        .bdt-info-card__title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 12px;
        }
        .bdt-info-card__desc {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin: 0 0 24px;
        }

        /* List points for Peminjaman */
        .bdt-info-card__points {
            list-style: none;
            padding: 0;
            margin: 0 0 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .bdt-info-card__point-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }
        .bdt-info-card__point-item svg {
            color: #1a6b2f;
            flex-shrink: 0;
        }

        /* Schedule table for Jam Operasional */
        .bdt-info-card__schedule {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 8px;
        }
        .bdt-info-card__schedule-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 8px;
        }
        .bdt-info-card__schedule-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .bdt-info-card__schedule-day {
            font-weight: 500;
            color: #475569;
        }
        .bdt-info-card__schedule-time {
            font-weight: 700;
            color: #0f172a;
        }
        .bdt-info-card__schedule-time--highlight {
            color: #ef4444;
        }

        /* Action Links & Buttons */
        .bdt-info-card__action-link {
            font-size: 14px;
            font-weight: 700;
            color: #1a6b2f;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
            margin-top: auto;
        }
        .bdt-info-card__action-link:hover {
            color: #134e22;
        }
        .bdt-info-card__action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #5b6560;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            text-align: center;
            transition: background-color 0.2s;
            margin-top: auto;
        }
        .bdt-info-card__action-btn:hover {
            background-color: #434c48;
        }

        /* Downloads list */
        .bdt-info-card__downloads {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: auto;
        }
        .bdt-info-card__download-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 700;
            color: #1a6b2f;
            text-decoration: none;
        }
        .bdt-info-card__download-item:hover {
            text-decoration: underline;
        }
        .bdt-info-card__download-item svg {
            color: #ef4444;
        }

        /* Badges for Tata Tertib */
        .bdt-info-card__badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: auto;
        }
        .bdt-info-badge {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            background-color: #f1f5f9;
            padding: 6px 14px;
            border-radius: 9999px;
        }

        /* FAQ Section */
        .bdt-faq-section {
            background-color: #ffffff;
            border: 1px solid #eef2ed;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 56px;
        }
        .bdt-faq-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 20px;
        }
        .bdt-faq-header__icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background-color: #f0f9f2;
            color: #1a6b2f;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        .bdt-faq-header__title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .bdt-faq-header__subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 4px 0 0;
        }
        .bdt-faq-search {
            position: relative;
            width: 280px;
        }
        .bdt-faq-search__icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
        }
        .bdt-faq-search__input {
            width: 100%;
            padding: 10px 40px 10px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            background-color: #f8fafc;
        }
        .bdt-faq-search__input:focus {
            border-color: #1a6b2f;
            background-color: #ffffff;
        }

        /* FAQ Accordion Grid */
        .bdt-faq-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }
        .bdt-faq-item {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s;
        }
        .bdt-faq-item[open] {
            background-color: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }
        .bdt-faq-item__summary {
            padding: 20px;
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
            cursor: pointer;
            list-style: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            outline: none;
        }
        .bdt-faq-item__summary::-webkit-details-marker {
            display: none;
        }
        .bdt-faq-item__summary::after {
            content: '';
            width: 10px;
            height: 6px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            transition: transform 0.2s;
        }
        .bdt-faq-item[open] .bdt-faq-item__summary::after {
            transform: rotate(180deg);
        }
        .bdt-faq-item__content {
            padding: 0 20px 20px;
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            border-top: 1px solid #f1f5f9;
            padding-top: 16px;
        }

        /* Help CTA Banner */
        .bdt-help-banner {
            background-color: #ecf3ee;
            border-radius: 20px;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 32px;
        }
        .bdt-help-banner__info {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        .bdt-help-banner__avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .bdt-help-banner__title {
            font-size: 20px;
            font-weight: 800;
            color: #0e5e32;
            margin: 0 0 6px;
        }
        .bdt-help-banner__desc {
            font-size: 14px;
            color: #475569;
            margin: 0;
            max-width: 480px;
            line-height: 1.5;
        }
        .bdt-help-banner__actions {
            display: flex;
            gap: 12px;
            flex-shrink: 0;
        }
        .bdt-help-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
        }
        .bdt-help-btn--green {
            background-color: #0e5e32;
            color: #ffffff;
        }
        .bdt-help-btn--green:hover {
            background-color: #0a4625;
        }
        .bdt-help-btn--white {
            background-color: #ffffff;
            color: #334155;
            border: 1px solid #e2e8f0;
        }
        .bdt-help-btn--white:hover {
            background-color: #f8fafc;
        }

        @media (max-width: 980px) {
            .bdt-info-grid-top {
                grid-template-columns: 1fr;
            }
            .bdt-info-grid-bottom {
                grid-template-columns: 1fr;
            }
            .bdt-faq-grid {
                grid-template-columns: 1fr;
            }
            .bdt-help-banner {
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
            }
            .bdt-help-banner__info {
                flex-direction: column;
                align-items: flex-start;
            }
            .bdt-help-banner__actions {
                width: 100%;
            }
            .bdt-help-btn {
                flex: 1;
                justify-content: center;
            }
        }
    </style>
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
            <div class="bdt-info-card">
                <div class="bdt-info-card__icon-wrap" aria-hidden="true">
                    <!-- Book Open icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                </div>
                <h2 class="bdt-info-card__title"><?= htmlspecialchars($peminjaman['title']) ?></h2>
                <p class="bdt-info-card__desc"><?= htmlspecialchars($peminjaman['desc']) ?></p>
                
                <ul class="bdt-info-card__points" role="list">
                    <?php foreach ($peminjaman['points'] as $point) : ?>
                        <li class="bdt-info-card__point-item">
                            <!-- Check icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <?= htmlspecialchars($point) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <a href="<?= htmlspecialchars($peminjaman['action_href']) ?>" class="bdt-info-card__action-link">
                    <?= htmlspecialchars($peminjaman['action_label']) ?> &rarr;
                </a>
            </div>

            <!-- Card Jam Operasional -->
            <?php $jamOp = $infoServices['jam_operasional']; ?>
            <div class="bdt-info-card bdt-info-card--green-bg">
                <div class="bdt-info-card__icon-wrap bdt-info-card__icon-wrap--white" aria-hidden="true">
                    <!-- Clock icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h2 class="bdt-info-card__title"><?= htmlspecialchars($jamOp['title']) ?></h2>
                
                <div class="bdt-info-card__schedule">
                    <?php foreach ($jamOp['schedule'] as $row) : ?>
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
                <p class="bdt-info-card__desc"><?= htmlspecialchars($keanggotaan['desc']) ?></p>
                <a href="<?= htmlspecialchars($keanggotaan['action_href']) ?>" class="bdt-info-card__action-btn">
                    <?= htmlspecialchars($keanggotaan['action_label']) ?>
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
                    <?php foreach ($unduhan['files'] as $file) : ?>
                        <a href="<?= htmlspecialchars($file['href']) ?>" class="bdt-info-card__download-item">
                            <!-- PDF Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            <?= htmlspecialchars($file['label']) ?>
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
                <p class="bdt-info-card__desc"><?= htmlspecialchars($tataTertib['desc']) ?></p>
                
                <div class="bdt-info-card__badges">
                    <?php foreach ($tataTertib['badges'] as $badge) : ?>
                        <span class="bdt-info-badge"><?= htmlspecialchars($badge) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- FAQ Section -->
        <section class="bdt-faq-section" aria-label="Pertanyaan Umum">
            <div class="bdt-faq-header">
                <div>
                    <div class="bdt-faq-header__icon-wrap" aria-hidden="true">
                        <!-- Help circle icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <h2 class="bdt-faq-header__title">Pertanyaan Umum (FAQ)</h2>
                    <p class="bdt-faq-header__subtitle">Cari jawaban cepat untuk keraguan Anda.</p>
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
                            <?= htmlspecialchars($faq['question']) ?>
                        </summary>
                        <div class="bdt-faq-item__content">
                            <p><?= htmlspecialchars($faq['answer']) ?></p>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Help CTA Banner -->
        <section class="bdt-help-banner" aria-label="Spanduk Bantuan">
            <div class="bdt-help-banner__info">
                <img src="<?= BASE_URL ?>/custom/assets/images/about-village.png" alt="Pustakawan" class="bdt-help-banner__avatar">
                <div>
                    <h3 class="bdt-help-banner__title">Butuh bantuan lebih lanjut?</h3>
                    <p class="bdt-help-banner__desc">Tim pustakawan kami siap membantu menjawab pertanyaan Anda melalui layanan pesan instan atau kunjungan langsung.</p>
                </div>
            </div>
            
            <div class="bdt-help-banner__actions">
                <a href="https://wa.me/6281234567890" target="_blank" class="bdt-help-btn bdt-help-btn--green">
                    <!-- Message icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    Hubungi via WhatsApp
                </a>
                <a href="<?= BASE_URL ?>/custom/pages/contact.php" class="bdt-help-btn bdt-help-btn--white">
                    <!-- Map/Location icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon>
                        <line x1="9" y1="3" x2="9" y2="18"></line>
                        <line x1="15" y1="6" x2="15" y2="21"></line>
                    </svg>
                    Cari Lokasi Kami
                </a>
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
