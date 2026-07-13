<?php
/**
 * Library Config
 *
 * File    : library-config.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Berisi data statis daftar perpustakaan unggulan
 * yang ditampilkan pada landing page.
 */

$libraryList = [
    [
        'id'        => 'perpustakaan-utama',
        'name'      => 'Perpustakaan Utama Desa',
        'address'   => 'Jl. Raya Teras No. 1, Desa Teras, Boyolali',
        'image'     => '/custom/assets/images/library-1.png',
        'badge'     => 'Unggulan',
        'totalBuku' => '3.200 Koleksi',
        'href'      => '/perpustakaan/perpustakaan-utama',
    ],
    [
        'id'        => 'taman-baca-komunitas',
        'name'      => 'Taman Baca Komunitas',
        'address'   => 'Jl. Pendidikan No. 5, RT 02 RW 03, Teras',
        'image'     => '/custom/assets/images/library-2.png',
        'badge'     => 'Unggulan',
        'totalBuku' => '1.800 Koleksi',
        'href'      => '/perpustakaan/taman-baca-komunitas',
    ],
    [
        'id'        => 'perpustakaan-digital',
        'name'      => 'Perpustakaan Digital Teras',
        'address'   => 'Jl. Merdeka No. 12, Pusat Desa Teras',
        'image'     => '/custom/assets/images/library-3.png',
        'badge'     => 'Digital',
        'totalBuku' => '6.500 Koleksi',
        'href'      => '/perpustakaan/perpustakaan-digital',
    ],
];
