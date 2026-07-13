<?php
/**
 * News Config
 *
 * File    : news-config.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Berisi data statis berita unggulan dan daftar berita terbaru
 * yang ditampilkan pada landing page.
 */

// Berita utama (featured)
$featuredNews = [
    'title'    => 'Bedah Buku di Desa Teras, 200 Warga Ikut Serta',
    'category' => 'Berita',
    'excerpt'  => 'Kegiatan bedah buku yang diadakan di Perpustakaan Utama Desa Teras berhasil menarik antusiasme ratusan warga. Acara ini menjadi momentum kebangkitan literasi di desa kami.',
    'date'     => '10 Juli 2026',
    'author'   => 'Admin Desa',
    'image'    => '/custom/assets/images/news-featured.png',
    'href'     => '/berita/bedah-buku-200-warga',
];

// Daftar berita pendukung
$newsList = [
    [
        'title'    => 'Cara Asyik Membaca Buku Lebih Cepat',
        'category' => 'Artikel',
        'excerpt'  => 'Tips dan teknik membaca efektif yang bisa Anda terapkan untuk meningkatkan produktivitas membaca.',
        'date'     => '8 Juli 2026',
        'image'    => '/custom/assets/images/news-small.png',
        'href'     => '/artikel/cara-membaca-cepat',
    ],
    [
        'title'    => 'Kunjungan Perpustakaan Digital dari Luar Negeri',
        'category' => 'Berita',
        'excerpt'  => 'Delegasi dari tiga negara berkunjung untuk mempelajari sistem pengelolaan perpustakaan digital Desa Teras.',
        'date'     => '5 Juli 2026',
        'image'    => '/custom/assets/images/about-village.png',
        'href'     => '/berita/kunjungan-luar-negeri',
    ],
];
