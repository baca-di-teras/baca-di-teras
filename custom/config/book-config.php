<?php
/**
 * Book Config
 *
 * File    : book-config.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Berisi data statis daftar koleksi buku terbaru
 * yang ditampilkan pada landing page.
 */

$bookList = [
    [
        'id'       => 'untaian-kisah-lembut',
        'title'    => 'Untaian Kisah Lembut',
        'author'   => 'Rina Sari',
        'category' => 'Fiksi',
        'badge'    => 'Baru',
        'image'    => '/custom/assets/images/book-cover-1.png',
        'href'     => '/katalog/untaian-kisah-lembut',
    ],
    [
        'id'       => 'koleksi-puisi-nusantara',
        'title'    => 'Koleksi Puisi Nusantara',
        'author'   => 'Budi Santoso',
        'category' => 'Puisi',
        'badge'    => 'Populer',
        'image'    => '/custom/assets/images/book-cover-2.png',
        'href'     => '/katalog/koleksi-puisi-nusantara',
    ],
    [
        'id'       => 'pemikiran-tokoh-bangsa',
        'title'    => 'Pemikiran Tokoh Bangsa',
        'author'   => 'Dr. Ahmad Fauzi',
        'category' => 'Nonfiksi',
        'badge'    => 'Baru',
        'image'    => '/custom/assets/images/book-cover-3.png',
        'href'     => '/katalog/pemikiran-tokoh-bangsa',
    ],
    [
        'id'       => 'jejak-nusantara',
        'title'    => 'Jejak Nusantara',
        'author'   => 'Siti Rahayu',
        'category' => 'Sejarah',
        'badge'    => null,
        'image'    => '/custom/assets/images/book-cover-4.png',
        'href'     => '/katalog/jejak-nusantara',
    ],
    [
        'id'       => 'dongeng-desa-teras',
        'title'    => 'Dongeng Desa Teras',
        'author'   => 'Tim Literasi Teras',
        'category' => 'Anak',
        'badge'    => 'Baru',
        'image'    => '/custom/assets/images/book-cover-5.png',
        'href'     => '/katalog/dongeng-desa-teras',
    ],
];
