<?php
/**
 * Feature Config
 *
 * File    : feature-config.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Berisi data statis daftar fitur layanan
 * yang ditampilkan pada landing page.
 */

$featureList = [
    [
        'icon'  => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
        'title' => 'Layar Peminjaman',
        'desc'  => 'Pinjam dan kembalikan buku secara digital tanpa perlu antri. Semua proses cukup lewat smartphone Anda.',
        'href'  => '/layanan/peminjaman',
    ],
    [
        'icon'  => '<rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>',
        'title' => 'Perpustakaan Digital',
        'desc'  => 'Akses ribuan koleksi buku, jurnal, dan artikel kapan saja dan di mana saja secara gratis.',
        'href'  => '/layanan/digital',
    ],
    [
        'icon'  => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'title' => 'Acara &amp; Workshop',
        'desc'  => 'Kegiatan literasi rutin setiap bulan untuk semua kalangan: anak-anak, remaja, hingga orang tua.',
        'href'  => '/layanan/acara',
    ],
    [
        'icon'  => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
        'title' => 'Keanggotaan Gratis',
        'desc'  => 'Daftar menjadi anggota secara gratis dan nikmati seluruh fasilitas perpustakaan tanpa biaya apapun.',
        'href'  => '/daftar',
    ],
];
