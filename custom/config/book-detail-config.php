<?php
/**
 * Book Detail Config
 *
 * File    : book-detail-config.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Berisi detail metadata lengkap untuk masing-masing buku,
 * termasuk status ketersediaan di jaringan perpustakaan desa.
 */

$bookDetailMap = [
    'the-architecture-of-growth' => [
        'id'          => 'the-architecture-of-growth',
        'title'       => 'The Architecture of Growth',
        'author'      => 'Dr. Aris Setiawan',
        'category'    => 'Pengembangan Diri',
        'category_slug'=> 'pengembangan-diri',
        'badge'       => null,
        'image'       => '/custom/assets/images/book-cover-1.png',
        'rating'      => '4.8 (124 ulasan)',
        'pages'       => '342 Halaman',
        'publisher'   => 'Teras Press',
        'pub_date'    => 'Okt 2023',
        'isbn'        => '978-602-1234-56-7',
        'language'    => 'Bahasa Indonesia',
        'synopsis'    => 'Sebuah eksplorasi mendalam tentang bagaimana komunitas pedesaan kecil dapat mendorong pertumbuhan berkelanjutan melalui literasi digital dan arsitektur pemikiran modern. Dr. Setiawan menggabungkan kearifan desa tradisional dari Desa Teras dengan tren teknologi global, menawarkan peta jalan untuk evolusi komunal yang menghormati warisan sambil merangkul masa depan. Buku ini berfungsi sebagai landasan bagi gerakan pemberdayaan digital di desa tersebut.',
        'availability'=> [
            [
                'library_name'=> 'Perpustakaan Teras Utama',
                'location'    => 'Balai Desa, Pusat Komunitas',
                'status'      => 'tersedia',
                'status_text' => '2 Eksemplar Tersedia',
                'map_query'   => 'Balai Desa Teras, Boyolali'
            ],
            [
                'library_name'=> 'Pojok Baca Dusun II',
                'location'    => 'Sayap Sekolah Dasar',
                'status'      => 'habis',
                'status_text' => 'Stok Habis',
                'note'        => 'Diharapkan 30 Okt',
                'map_query'   => 'SD Negeri 2 Teras, Boyolali'
            ]
        ],
        'action_status'=> 'tersedia'
    ],
    'digital-teras-navigating-the-web' => [
        'id'          => 'digital-teras-navigating-the-web',
        'title'       => 'Digital Teras: Navigating the Web',
        'author'      => 'Dr. Ahmad Santoso',
        'category'    => 'Teknologi',
        'category_slug'=> 'teknologi',
        'badge'       => 'Baru',
        'image'       => '/custom/assets/images/book-cover-1.png',
        'rating'      => '4.7 (89 ulasan)',
        'pages'       => '288 Halaman',
        'publisher'   => 'Local Press',
        'pub_date'    => 'Jan 2024',
        'isbn'        => '978-602-9876-54-3',
        'language'    => 'Bahasa Indonesia',
        'synopsis'    => 'Panduan praktis untuk warga desa dalam menavigasi dunia internet secara sehat, aman, dan produktif. Membahas dasar-dasar digital, keamanan data pribadi, dan pemanfaatan web untuk ekonomi kreatif desa.',
        'availability'=> [
            [
                'library_name'=> 'Perpustakaan Digital Teras',
                'location'    => 'Sains Techno Park, Gerbang Timur',
                'status'      => 'tersedia',
                'status_text' => '4 Eksemplar Tersedia',
                'map_query'   => 'Teras, Boyolali'
            ]
        ],
        'action_status'=> 'tersedia'
    ],
    'modern-farmers-journal-2024' => [
        'id'          => 'modern-farmers-journal-2024',
        'title'       => "Modern Farmer's Journal 2024",
        'author'      => 'Teras Agrotech Team',
        'category'    => 'Pertanian',
        'category_slug'=> 'pertanian',
        'badge'       => null,
        'image'       => '/custom/assets/images/book-cover-2.png',
        'rating'      => '4.9 (45 ulasan)',
        'pages'       => '184 Halaman',
        'publisher'   => 'Local Press',
        'pub_date'    => 'Mar 2024',
        'isbn'        => '978-602-5432-10-9',
        'language'    => 'Bahasa Indonesia',
        'synopsis'    => 'Jurnal berkala yang mendokumentasikan riset pertanian hidroponik, organik, dan pemanfaatan sensor IoT untuk monitoring lahan pertanian di wilayah Desa Teras.',
        'availability'=> [
            [
                'library_name'=> 'Agro-Perpustakaan Teras',
                'location'    => 'Distrik Pertanian Lembah Hijau',
                'status'      => 'habis',
                'status_text' => 'Dipinjam',
                'note'        => 'Diharapkan 20 Okt',
                'map_query'   => 'Teras, Boyolali'
            ]
        ],
        'action_status'=> 'antrean'
    ]
];
