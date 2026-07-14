<?php
/**
 * News Config
 *
 * File    : news-config.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Berisi data statis berita dan artikel sesuai desain mockup.
 */

// Berita Utama (Featured News)
$featuredNews = [
    'title'    => 'Desa Teras Resmikan Pusat Literasi Digital Baru',
    'category' => 'Berita Utama',
    'excerpt'  => 'Pusat baru ini bertujuan untuk menyediakan internet berkecepatan tinggi dan lokakarya coding gratis bagi pemuda Desa Teras, menjembatani kesenjangan...',
    'date'     => '24 Okt 2023',
    'author'   => 'Admin Desa',
    'image'    => '/custom/assets/images/news-featured.png',
    'href'     => '/custom/pages/news/detail.php?id=resmikan-pusat-literasi'
];

// Daftar Berita Pendukung (Sidebar News)
$newsList = [
    [
        'title'    => 'Peluang beasiswa untuk tahun 2024 diumumkan',
        'category' => 'Pendidikan',
        'excerpt'  => 'Kabar baik bagi pelajar berprestasi di Desa Teras. Beasiswa pendidikan tingkat menengah dan tinggi kini telah resmi dibuka pendaftarannya.',
        'date'     => '2 hari yang lalu',
        'read_time'=> '5 menit baca',
        'image'    => '/custom/assets/images/news-small.png',
        'href'     => '/custom/pages/news/detail.php?id=peluang-beasiswa'
    ],
    [
        'title'    => 'Lokakarya pertanian berkelanjutan dimulai minggu depan',
        'category' => 'Pertanian',
        'excerpt'  => 'Kelompok Tani Desa Teras bekerja sama dengan dinas terkait menyelenggarakan lokakarya teknik bercocok tanam ramah lingkungan.',
        'date'     => '20 Okt 2023',
        'read_time'=> '8 menit baca',
        'image'    => '/custom/assets/images/about-village.png',
        'href'     => '/custom/pages/news/detail.php?id=lokakarya-pertanian'
    ],
    [
        'title'    => 'Pertemuan Rutin Warga: Membahas Program Literasi',
        'category' => 'Komunitas',
        'excerpt'  => 'Rapat bulanan warga membahas perkembangan fasilitas baca di masing-masing dusun dan rencana penambahan koleksi buku.',
        'date'     => '18 Okt 2023',
        'read_time'=> '4 menit baca',
        'image'    => '/custom/assets/images/event-workshop.png',
        'href'     => '/custom/pages/news/detail.php?id=pertemuan-rutin'
    ]
];

// Daftar Artikel (Standard articles grid)
$articleList = [
    [
        'id'       => 'perpustakaan-rumah-anak',
        'title'    => 'Cara Mengelola Perpustakaan Rumah untuk Anak',
        'category' => 'Tips Literasi',
        'excerpt'  => 'Saran praktis dalam memilih buku yang sesuai usia dan menciptakan lingkungan membaca yang nyaman di teras.',
        'author'   => 'Admin',
        'image'    => '/custom/assets/images/gallery-reading-room.png',
        'href'     => '/custom/pages/news/detail.php?id=perpustakaan-rumah-anak',
        'style'    => 'tips' // Khusus gaya kartu tips di desain (background cokelat lembut)
    ],
    [
        'id'       => 'menavigasi-internet-aman',
        'title'    => 'Menavigasi Internet dengan Aman: Panduan untuk Keluarga Pedesaan',
        'category' => 'Wawasan Teknologi',
        'excerpt'  => 'Bagaimana mendampingi anak dalam berselancar di internet serta mengenal platform edukasi digital.',
        'author'   => 'Tim Tekno',
        'date'     => '12 menit baca',
        'image'    => '/custom/assets/images/news-featured.png',
        'href'     => '/custom/pages/news/detail.php?id=menavigasi-internet-aman',
        'style'    => 'hero' // Kartu lebar di tengah dengan gambar latar
    ],
    [
        'id'       => 'melestarikan-cerita-rakyat',
        'title'    => 'Melestarikan Cerita Rakyat Desa melalui Storytelling Digital',
        'category' => 'Budaya',
        'excerpt'  => 'Menggunakan rekaman audio dan fotografi untuk menjaga warisan lokal kita tetap hidup bagi generasi mendatang.',
        'author'   => 'Pak Jatmiko',
        'image'    => '/custom/assets/images/gallery-study-corner.png',
        'href'     => '/custom/pages/news/detail.php?id=melestarikan-cerita-rakyat',
        'style'    => 'budaya' // Kartu dengan footer author khusus
    ],
    [
        'id'       => 'klub-buku-bulan-ini',
        'title'    => 'Pilihan Klub Buku Bulan Ini',
        'category' => 'Rekomendasi',
        'excerpt'  => 'Temukan apa yang sedang dibaca tetangga Anda bulan ini. Bergabunglah dalam diskusi di pusat literasi.',
        'image'    => '',
        'href'     => '/custom/pages/books/katalog.php',
        'style'    => 'klub' // Kartu minimalis dengan icon buku
    ],
    [
        'id'       => 'dasar-menulis-kreatif',
        'title'    => 'Dasar-Dasar Menulis Kreatif',
        'category' => 'Panduan',
        'excerpt'  => 'Panduan pemula sederhana bagi penulis desa yang bercita-cita mendokumentasikan kehidupan dan impian mereka.',
        'image'    => '',
        'href'     => '#',
        'style'    => 'menulis' // Kartu panduan dengan icon unduh
    ]
];
