<?php
/**
 * Book Config
 *
 * File    : book-config.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Berisi data statis daftar koleksi buku yang
 * disesuaikan dengan desain Katalog Buku.
 */

$bookList = [
    [
        'id'          => 'digital-teras-navigating-the-web',
        'title'       => 'Digital Teras: Navigating the Web',
        'author'      => 'Dr. Ahmad Santoso',
        'category'    => 'Teknologi',
        'badge'       => 'Baru',
        'image'       => '/custom/assets/images/book-cover-1.png',
        'href'        => '/custom/pages/books/detail.php?id=digital-teras-navigating-the-web',
        'status'      => 'tersedia',
        'status_text' => 'Tersedia (4 eksemplar)',
        'publisher'   => 'Local Press',
        'library'     => 'perpustakaan-digital'
    ],
    [
        'id'          => 'modern-farmers-journal-2024',
        'title'       => "Modern Farmer's Journal 2024",
        'author'      => 'Teras Agrotech Team',
        'category'    => 'Pertanian',
        'badge'       => null,
        'image'       => '/custom/assets/images/book-cover-2.png',
        'href'        => '/custom/pages/books/detail.php?id=modern-farmers-journal-2024',
        'status'      => 'dipinjam',
        'status_text' => 'Dipinjam',
        'publisher'   => 'Local Press',
        'library'     => 'agro-perpustakaan'
    ],
    [
        'id'          => 'folklore-of-the-valley',
        'title'       => 'Folklore of the Valley',
        'author'      => 'Maria Sastrawan',
        'category'    => 'Sastra Anak',
        'badge'       => null,
        'image'       => '/custom/assets/images/book-cover-3.png',
        'href'        => '/custom/pages/books/detail.php?id=folklore-of-the-valley',
        'status'      => 'dipesan',
        'status_text' => '2 Dipesan',
        'publisher'   => 'Bentang Pustaka',
        'library'     => 'sd-negeri-2-teras'
    ],
    [
        'id'          => 'global-finance-for-villages',
        'title'       => 'Global Finance for Villages',
        'author'      => 'Prof. Hendra Wijaya',
        'category'    => 'Teknologi',
        'badge'       => null,
        'image'       => '/custom/assets/images/book-cover-4.png',
        'href'        => '/custom/pages/books/detail.php?id=global-finance-for-villages',
        'status'      => 'tersedia',
        'status_text' => 'Tersedia (1 eksemplar)',
        'publisher'   => 'Mizan',
        'library'     => 'perpustakaan-utama'
    ],
    [
        'id'          => 'history-of-teras-settlement',
        'title'       => 'History of Teras Settlement',
        'author'      => 'Dr. S. Margono',
        'category'    => 'Budaya Lokal',
        'badge'       => null,
        'image'       => '/custom/assets/images/book-cover-5.png',
        'href'        => '/custom/pages/books/detail.php?id=history-of-teras-settlement',
        'status'      => 'tersedia',
        'status_text' => 'Tersedia (3 eksemplar)',
        'publisher'   => 'Gramedia',
        'library'     => 'perpustakaan-utama'
    ],
    [
        'id'          => 'culinary-heritage-of-teras',
        'title'       => 'Culinary Heritage of Teras',
        'author'      => 'Ibu Siti Rahayu',
        'category'    => 'Budaya Lokal',
        'badge'       => null,
        'image'       => '/custom/assets/images/book-cover-1.png', // Fallback to cover 1
        'href'        => '/custom/pages/books/detail.php?id=culinary-heritage-of-teras',
        'status'      => 'tersedia',
        'status_text' => 'Tersedia (2 eksemplar)',
        'publisher'   => 'Gramedia',
        'library'     => 'teras-south-commons'
    ]
];
