<?php
/**
 * Halaman Donasi – Baca Di Teras
 *
 * File    : donasi.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Halaman publik donasi: QR pembayaran, daftar partner, dan daftar donatur.
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$activePage = 'donasi';

// ── Service Layer ──────────────────────────────────────────────
$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..';
require_once $libPath . '/custom/services/DonasiService.php';

$donasiService = new DonasiService();

// ── Pagination & Sorting ───────────────────────────────────────
$limit   = 10;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$sortBy  = in_array($_GET['sort'] ?? '', ['terbaru', 'terlama']) ? $_GET['sort'] : 'terbaru';

$totalDonatur = $donasiService->getDonaturCount();
$donatur      = $donasiService->getDonatur($page, $limit, $sortBy);
$partners     = $donasiService->getPartners();

$totalPages = (int) ceil($totalDonatur / $limit);
$totalPages = max(1, $totalPages);
if ($page > $totalPages) $page = $totalPages;

// Helper untuk URL pagination
function donasiPageUrl(int $p, string $sort): string {
    $base = defined('BASE_URL') ? BASE_URL : '/baca-di-teras';
    return $base . '/donasi?sort=' . urlencode($sort) . '&page=' . $p;
}

// Informasi rekening bank (bisa dikonfigurasi ke DB jika perlu)
$bankInfo = [
    'bank_name'    => 'Mandiri',
    'account_no'   => '128-00-3333444-1',
    'account_name' => 'a.n Yayasan Irama Nusantara',
    'swift_code'   => 'BMRIIDJAS51',
    'bank_address' => 'KCP Kenang Plaza 12611 Jl. Kenang Raya No. 15C, Bangka, Mampang Prpt, Jakarta Selatan – 12780',
    'phone'        => '+021-5268777',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dukung literasi desa melalui donasi buku dan dana. Bersama kita wujudkan perpustakaan desa yang lebih baik untuk generasi mendatang.">
    <title>Donasi – Baca Di Teras</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/navbar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/donasi.css">
    <style>
        html, body { margin: 0; background: #f7f6f5; }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="donasi-page">
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <!-- ── Hero Section ─────────────────────────────────────── -->
    <section class="donasi-hero" aria-label="Informasi donasi">
        <div class="donasi-hero__inner">

            <!-- Teks kiri -->
            <div class="donasi-hero__text">
                <p class="donasi-hero__eyebrow">Satu Donasi, Banyak Manfaat</p>
                <p class="donasi-hero__desc">
                    Buku memiliki kekuatan untuk membuka wawasan, menumbuhkan mimpi, dan mengubah masa depan.
                    Melalui donasi yang Anda berikan, Perpustakaan Desa Teras dapat menambah koleksi buku yang
                    dibutuhkan masyarakat, mulai dari buku anak, pendidikan, keterampilan, hingga literasi umum.
                </p>
                <p class="donasi-hero__desc">
                    Setiap rupiah yang terkumpul akan digunakan untuk pengadaan buku dan pengembangan koleksi
                    perpustakaan agar semakin banyak masyarakat yang dapat memperoleh akses terhadap sumber
                    belajar yang berkualitas.
                </p>
                <p class="donasi-hero__desc">
                    Mari menjadi bagian dari gerakan literasi Desa Teras. Bersama, kita dapat menghadirkan
                    lebih banyak buku dan lebih banyak kesempatan belajar bagi generasi mendatang.
                </p>
            </div>

            <!-- QR Card kanan -->
            <div class="donasi-hero__qr-card" aria-label="Informasi rekening donasi">
                <img
                    src="<?= BASE_URL ?>/custom/assets/images/qr_donasi.jpeg"
                    alt="QR Code QRIS untuk donasi Baca Di Teras"
                    class="donasi-hero__qr-img"
                    loading="lazy"
                >
            </div>

        </div>
    </section>

    <!-- ── Terima Kasih Kepada ───────────────────────────────── -->
    <?php if (!empty($partners)): ?>
    <section class="donasi-section donasi-partners" aria-label="Daftar partner donasi">
        <h2 class="donasi-section__title">Terima Kasih Kepada</h2>

        <div class="donasi-partners__grid">
            <?php foreach ($partners as $partner): ?>
                <div class="donasi-partner-item">
                    <?php $tag = !empty($partner['website_url']) ? 'a' : 'div'; ?>
                    <<?= $tag ?><?= !empty($partner['website_url']) ? ' href="' . htmlspecialchars($partner['website_url']) . '" target="_blank" rel="noopener noreferrer"' : '' ?>>
                        <?php if (!empty($partner['logo_path'])): ?>
                            <img
                                src="<?= BASE_URL . '/' . ltrim(htmlspecialchars($partner['logo_path']), '/') ?>"
                                alt="Logo <?= htmlspecialchars($partner['name']) ?>"
                                loading="lazy"
                            >
                        <?php else: ?>
                            <div class="donasi-partner-item--placeholder" style="width:72px;height:72px;">
                                <?= htmlspecialchars($partner['name']) ?>
                            </div>
                        <?php endif; ?>
                    </<?= $tag ?>>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Donatur ───────────────────────────────────────────── -->
    <section class="donasi-section donasi-donatur" aria-label="Daftar donatur">
        <div class="donasi-donatur__header">
            <span class="donasi-donatur__count">
                Donatur (<?= number_format($totalDonatur) ?>)
            </span>
            <div class="donasi-donatur__sort">
                <label for="donasi-sort-select">Urutkan</label>
                <select
                    id="donasi-sort-select"
                    name="sort"
                    onchange="window.location.href='<?= BASE_URL ?>/donasi?sort=' + this.value + '&page=1'"
                >
                    <option value="terbaru" <?= $sortBy === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
                    <option value="terlama" <?= $sortBy === 'terlama' ? 'selected' : '' ?>>Terlama</option>
                </select>
            </div>
        </div>

        <?php if (empty($donatur)): ?>
            <p class="donasi-empty">Belum ada donatur yang terdaftar.</p>
        <?php else: ?>
            <ul class="donasi-donatur__list" aria-label="Nama-nama donatur">
                <?php foreach ($donatur as $d): ?>
                    <li class="donasi-donatur__item">
                        <?= htmlspecialchars($d['name']) ?>
                    </li>
                <?php endforeach; ?>
                <?php
                // Jika jumlah item ganjil, tambah item kosong agar grid rapi
                if (count($donatur) % 2 !== 0): ?>
                    <li class="donasi-donatur__item" aria-hidden="true"></li>
                <?php endif; ?>
            </ul>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav class="donasi-pagination" aria-label="Navigasi halaman donatur">
                <?php
                // Generate page numbers to show
                $range  = 2;
                $pages  = [];
                for ($i = 1; $i <= $totalPages; $i++) {
                    if ($i === 1 || $i === $totalPages || abs($i - $page) <= $range) {
                        $pages[] = $i;
                    }
                }
                $prev = null;
                foreach ($pages as $p):
                    // Ellipsis
                    if ($prev !== null && $p - $prev > 1): ?>
                        <span class="donasi-pagination__ellipsis" aria-hidden="true">…</span>
                    <?php endif; ?>
                    <a href="<?= donasiPageUrl($p, $sortBy) ?>"
                       class="donasi-pagination__btn <?= $p === $page ? 'donasi-pagination__btn--active' : '' ?>"
                       <?= $p === $page ? 'aria-current="page"' : '' ?>
                    ><?= $p ?></a>
                <?php
                    $prev = $p;
                endforeach; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= donasiPageUrl($totalPages, $sortBy) ?>"
                       class="donasi-pagination__btn">Terakhir</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
