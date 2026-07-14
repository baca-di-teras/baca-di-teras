<?php
/**
 * Book Detail Config
 *
 * File    : book-detail-config.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *

 * Metadata statis per buku untuk halaman detail.
 * Dirancang agar mudah dipindah ke database:
 *   - books             → id, slug, title, author, category, image, ...
 *   - book_availability → per lokasi perpustakaan
 *   - book_meta         → penerbit, isbn, bahasa, halaman, dst.
 */

$bookDetailMap = [

    /* ──────────────────────────────────────────────────────────
       Digital Teras: Navigating the Web
       ────────────────────────────────────────────────────────── */
    'digital-teras' => [
        'id'            => 'digital-teras',
        'slug'          => 'digital-teras',
        'title'         => 'Digital Teras: Navigating the Web',
        'author'        => 'Dr. Ahmad Santoso',
        'category'      => 'Teknologi',
        'image'         => '/custom/assets/images/book-cover-3.png',
        'rating'        => 4.5,
        'totalUlasan'   => 87,
        'totalHalaman'  => 284,

        'meta' => [
            'penerbit'      => 'Gramedia',
            'tanggalTerbit' => 'Mar 2024',
            'isbn'          => '978-602-0301-12-3',
            'bahasa'        => 'Bahasa Indonesia',
        ],

        'sinopsis' => 'Buku ini membahas bagaimana teknologi internet dapat dimanfaatkan secara optimal oleh masyarakat pedesaan. Dr. Santoso menyajikan panduan praktis untuk mengakses layanan digital, memanfaatkan perpustakaan online, dan berpartisipasi dalam ekonomi digital tanpa harus meninggalkan desa. Ditulis dengan bahasa yang mudah dipahami, buku ini menjadi jembatan antara dunia digital dan kehidupan komunitas lokal.',

        'ketersediaan' => [
            [
                'perpustakaan' => 'Perpustakaan Pusat Teras',
                'lokasi'       => 'Jl. Raya Teras No. 45, Balai Desa',
                'status'       => 'tersedia',
                'stok'         => 4,
                'estimasi'     => null,
                'mapsHref'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
            [
                'perpustakaan' => 'Perpustakaan Digital Teras',
                'lokasi'       => 'Jl. Merdeka No. 12, Pusat Desa',
                'status'       => 'tersedia',
                'stok'         => 2,
                'estimasi'     => null,
                'mapsHref'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
        ],

        'ctaPesan'  => true,
        'ctaPinjam' => true,

        'relasiBuku' => [
            'jejak-nusantara',
            'pemikiran-tokoh-bangsa',
            'koleksi-puisi-nusantara',
            'untaian-kisah-lembut',
            'dongeng-desa-teras',
        ],
    ],

    /* ──────────────────────────────────────────────────────────
       The Architecture of Growth
       ────────────────────────────────────────────────────────── */
    'architecture-of-growth' => [
        'id'            => 'architecture-of-growth',
        'slug'          => 'architecture-of-growth',
        'title'         => 'The Architecture of Growth',
        'author'        => 'Dr. Aris Setiawan',
        'category'      => 'Pengembangan Diri',
        'image'         => '/custom/assets/images/book-cover-1.png',
        'rating'        => 4.8,
        'totalUlasan'   => 124,
        'totalHalaman'  => 342,

        'meta' => [
            'penerbit'      => 'Teras Press',
            'tanggalTerbit' => 'Okt 2023',
            'isbn'          => '978-602-1234-56-7',
            'bahasa'        => 'Bahasa Indonesia',
        ],

        'sinopsis' => 'Sebuah eksplorasi mendalam tentang bagaimana komunitas pedesaan kecil dapat mendorong pertumbuhan berkelanjutan melalui literasi digital dan arsitektur pemikiran modern. Dr. Setiawan menggabungkan kearifan desa tradisional dari Desa Teras dengan tren teknologi global, menawarkan peta jalan untuk evolusi komunal yang menghormati warisan sambil merangkul masa depan. Buku ini berfungsi sebagai landasan bagi gerakan pemberdayaan digital di desa tersebut.',

        'ketersediaan' => [
            [
                'perpustakaan' => 'Perpustakaan Teras Utama',
                'lokasi'       => 'Balai Desa, Pusat Komunitas',
                'status'       => 'tersedia',
                'stok'         => 2,
                'estimasi'     => null,
                'mapsHref'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
            [
                'perpustakaan' => 'Pojok Baca Dusun II',
                'lokasi'       => 'Sayap Sekolah Dasar',
                'status'       => 'habis',
                'stok'         => 0,
                'estimasi'     => '30 Okt',
                'mapsHref'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
        ],

        'ctaPesan'  => true,
        'ctaPinjam' => false,

        'relasiBuku' => [
            'digital-teras',
            'jejak-nusantara',
            'pemikiran-tokoh-bangsa',
            'koleksi-puisi-nusantara',
            'dongeng-desa-teras',
        ],
    ],

    /* ──────────────────────────────────────────────────────────
       Untaian Kisah Lembut
       ────────────────────────────────────────────────────────── */
    'untaian-kisah-lembut' => [
        'id'            => 'untaian-kisah-lembut',
        'slug'          => 'untaian-kisah-lembut',
        'title'         => 'Untaian Kisah Lembut',
        'author'        => 'Rina Sari',
        'category'      => 'Fiksi',
        'image'         => '/custom/assets/images/book-cover-1.png',
        'rating'        => 4.6,
        'totalUlasan'   => 56,
        'totalHalaman'  => 218,

        'meta' => [
            'penerbit'      => 'Bentang Pustaka',
            'tanggalTerbit' => 'Jan 2024',
            'isbn'          => '978-602-9911-32-1',
            'bahasa'        => 'Bahasa Indonesia',
        ],

        'sinopsis' => 'Kumpulan cerpen yang mengangkat kisah-kisah kehidupan sehari-hari masyarakat desa yang penuh dengan kehangatan, harapan, dan perjuangan. Rina Sari menyajikan narasi yang lembut namun penuh makna, menggambarkan betapa indahnya kehidupan desa dengan segala dinamikanya. Setiap cerita merupakan cerminan nyata dari kehidupan komunitas yang saling mendukung.',

        'ketersediaan' => [
            [
                'perpustakaan' => 'Perpustakaan Pusat Teras',
                'lokasi'       => 'Jl. Raya Teras No. 45',
                'status'       => 'tersedia',
                'stok'         => 5,
                'estimasi'     => null,
                'mapsHref'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
            [
                'perpustakaan' => 'Taman Baca Komunitas',
                'lokasi'       => 'Jl. Pendidikan No. 5, RT 02',
                'status'       => 'tersedia',
                'stok'         => 3,
                'estimasi'     => null,
                'mapsHref'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
        ],

        'ctaPesan'  => true,
        'ctaPinjam' => true,

        'relasiBuku' => [
            'architecture-of-growth',
            'koleksi-puisi-nusantara',
            'jejak-nusantara',
            'pemikiran-tokoh-bangsa',
            'digital-teras',
        ],
    ],

    /* ──────────────────────────────────────────────────────────
       Koleksi Puisi Nusantara
       ────────────────────────────────────────────────────────── */
    'koleksi-puisi-nusantara' => [
        'id'            => 'koleksi-puisi-nusantara',
        'slug'          => 'koleksi-puisi-nusantara',
        'title'         => 'Koleksi Puisi Nusantara',
        'author'        => 'Budi Santoso',
        'category'      => 'Sastra Anak',
        'image'         => '/custom/assets/images/book-cover-2.png',
        'rating'        => 4.7,
        'totalUlasan'   => 93,
        'totalHalaman'  => 196,

        'meta' => [
            'penerbit'      => 'Mizan',
            'tanggalTerbit' => 'Feb 2024',
            'isbn'          => '978-602-0871-44-8',
            'bahasa'        => 'Bahasa Indonesia',
        ],

        'sinopsis' => 'Antologi puisi yang merayakan kekayaan budaya dan keindahan alam Nusantara. Budi Santoso mengumpulkan karya-karya puisi terbaik dari berbagai penulis lokal yang menggambarkan keunikan tradisi, bahasa daerah, dan semangat gotong royong yang menjadi ciri khas masyarakat Indonesia.',

        'ketersediaan' => [
            [
                'perpustakaan' => 'Taman Baca Komunitas',
                'lokasi'       => 'Jl. Pendidikan No. 5, RT 02',
                'status'       => 'tersedia',
                'stok'         => 3,
                'estimasi'     => null,
                'mapsHref'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
            [
                'perpustakaan' => 'Perpustakaan Digital Teras',
                'lokasi'       => 'Jl. Merdeka No. 12',
                'status'       => 'dipesan',
                'stok'         => 1,
                'estimasi'     => '25 Jul',
                'mapsHref'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
        ],

        'ctaPesan'  => true,
        'ctaPinjam' => true,

        'relasiBuku' => [
            'untaian-kisah-lembut',
            'architecture-of-growth',
            'jejak-nusantara',
            'dongeng-desa-teras',
            'digital-teras',
        ],
    ],

    /* ──────────────────────────────────────────────────────────
       Jejak Nusantara
       ────────────────────────────────────────────────────────── */
    'jejak-nusantara' => [
        'id'            => 'jejak-nusantara',
        'slug'          => 'jejak-nusantara',
        'title'         => 'Jejak Nusantara',
        'author'        => 'Siti Rahayu',
        'category'      => 'Sejarah',
        'image'         => '/custom/assets/images/book-cover-4.png',
        'rating'        => 4.4,
        'totalUlasan'   => 42,
        'totalHalaman'  => 310,

        'meta' => [
            'penerbit'      => 'Bentang Pustaka',
            'tanggalTerbit' => 'Nov 2023',
            'isbn'          => '978-602-4415-71-2',
            'bahasa'        => 'Bahasa Indonesia',
        ],

        'sinopsis' => 'Perjalanan mendalam melalui sejarah dan kebudayaan Nusantara dari masa ke masa. Siti Rahayu membawa pembaca menyelami peristiwa-peristiwa bersejarah yang membentuk identitas bangsa, dari kerajaan kuno hingga era kemerdekaan, dengan narasi yang hidup dan penuh detail.',

        'ketersediaan' => [
            [
                'perpustakaan' => 'Perpustakaan Pusat Teras',
                'lokasi'       => 'Jl. Raya Teras No. 45',
                'status'       => 'tersedia',
                'stok'         => 2,
                'estimasi'     => null,
                'mapsHref'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
        ],

        'ctaPesan'  => true,
        'ctaPinjam' => false,

        'relasiBuku' => [
            'pemikiran-tokoh-bangsa',
            'architecture-of-growth',
            'digital-teras',
            'untaian-kisah-lembut',
            'koleksi-puisi-nusantara',
        ],
    ],

    /* ──────────────────────────────────────────────────────────
       Pemikiran Tokoh Bangsa
       ────────────────────────────────────────────────────────── */
    'pemikiran-tokoh-bangsa' => [
        'id'            => 'pemikiran-tokoh-bangsa',
        'slug'          => 'pemikiran-tokoh-bangsa',
        'title'         => 'Pemikiran Tokoh Bangsa',
        'author'        => 'Dr. Ahmad Fauzi',
        'category'      => 'Nonfiksi',
        'image'         => '/custom/assets/images/book-cover-3.png',
        'rating'        => 4.9,
        'totalUlasan'   => 201,
        'totalHalaman'  => 415,

        'meta' => [
            'penerbit'      => 'Gramedia',
            'tanggalTerbit' => 'Agu 2023',
            'isbn'          => '978-602-0301-88-5',
            'bahasa'        => 'Bahasa Indonesia',
        ],

        'sinopsis' => 'Kumpulan esai dan biografi ringkas tokoh-tokoh penting yang berkontribusi besar dalam membangun bangsa Indonesia. Dr. Ahmad Fauzi menyoroti pemikiran, perjuangan, dan warisan intelektual para pemimpin bangsa yang relevan hingga era modern ini.',

        'ketersediaan' => [
            [
                'perpustakaan' => 'Perpustakaan Digital Teras',
                'lokasi'       => 'Jl. Merdeka No. 12',
                'status'       => 'dipinjam',
                'stok'         => 0,
                'estimasi'     => '20 Jul',
                'mapsHref'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
            [
                'perpustakaan' => 'Perpustakaan Pusat Teras',
                'lokasi'       => 'Jl. Raya Teras No. 45',
                'status'       => 'tersedia',
                'stok'         => 1,
                'estimasi'     => null,
                'mapsHref'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
        ],

        'ctaPesan'  => true,
        'ctaPinjam' => false,

        'relasiBuku' => [
            'jejak-nusantara',
            'architecture-of-growth',
            'digital-teras',
            'koleksi-puisi-nusantara',
            'untaian-kisah-lembut',
        ],
    ],

    /* ──────────────────────────────────────────────────────────
       Dongeng Desa Teras
       ────────────────────────────────────────────────────────── */
    'dongeng-desa-teras' => [
        'id'            => 'dongeng-desa-teras',
        'slug'          => 'dongeng-desa-teras',
        'title'         => 'Dongeng Desa Teras',
        'author'        => 'Tim Literasi Teras',
        'category'      => 'Sastra Anak',
        'image'         => '/custom/assets/images/book-cover-5.png',
        'rating'        => 4.9,
        'totalUlasan'   => 318,
        'totalHalaman'  => 148,

        'meta' => [
            'penerbit'      => 'Local Press',
            'tanggalTerbit' => 'Jun 2024',
            'isbn'          => '978-602-9900-11-6',
            'bahasa'        => 'Bahasa Indonesia',
        ],

        'sinopsis' => 'Kumpulan dongeng dan cerita rakyat asli Desa Teras yang dikumpulkan dan ditulis ulang oleh Tim Literasi Teras. Buku ini mengabadikan kearifan lokal, legenda, dan cerita turun-temurun yang menjadi bagian dari identitas Desa Teras, cocok untuk dibaca bersama seluruh keluarga.',

        'ketersediaan' => [
            [
                'perpustakaan' => 'Taman Baca Komunitas',
                'lokasi'       => 'Jl. Pendidikan No. 5, RT 02',
                'status'       => 'tersedia',
                'stok'         => 6,
                'estimasi'     => null,
                'mapsHref'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
            [
                'perpustakaan' => 'Perpustakaan Pusat Teras',
                'lokasi'       => 'Jl. Raya Teras No. 45',
                'status'       => 'tersedia',
                'stok'         => 4,
                'estimasi'     => null,
                'mapsHref'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
        ],

        'ctaPesan'  => true,
        'ctaPinjam' => true,

        'relasiBuku' => [
            'untaian-kisah-lembut',
            'koleksi-puisi-nusantara',
            'jejak-nusantara',
            'architecture-of-growth',
            'digital-teras',
        ],
    ],

];

// ── Lookup tabel flat untuk relasi buku ──────────────────────
// Digunakan oleh halaman detail untuk menampilkan "Pembaca juga menyukai"
$bookFlatList = [];
foreach ($bookDetailMap as $slug => $bookDetail) {
    $bookFlatList[$slug] = [
        'id'       => $bookDetail['id'],
        'title'    => $bookDetail['title'],
        'author'   => $bookDetail['author'],
        'category' => $bookDetail['category'],
        'badge'    => null,
        'image'    => $bookDetail['image'],
        'href'     => '/custom/pages/libraries/book-detail.php?id=' . $slug,
    ];
}