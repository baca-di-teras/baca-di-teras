<?php
/**
 * Library Detail Config
 *
 * File    : library-detail-config.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Berisi metadata statis setiap perpustakaan.
 * Struktur ini dirancang agar mudah dipindah ke database di masa depan.
 *
 * Ketika database tersedia, setiap key di sini akan menjadi:
 *   - library_profile   → id, slug, name, badge, description, hero_image
 *   - library_facility  → tiap item di $meta['fasilitas']
 *   - library_stat      → tiap item di $meta['faktaMenarik']
 *   - library_hours     → tiap item di $meta['jamOperasional']
 *   - library_event     → tiap item di $meta['kegiatan']
 *   - library_gallery   → tiap item di $meta['gallery']
 *   - library_location  → $meta['lokasi']
 *   - library_book      → tiap item di $meta['koleksiBuku']
 */

$libraryDetailMap = [

    /* ──────────────────────────────────────────────────────────
       Perpustakaan Utama Desa
       ────────────────────────────────────────────────────────── */
    'perpustakaan-utama' => [
        'id'          => 'perpustakaan-utama',
        'slug'        => 'perpustakaan-utama',
        'name'        => 'Perpustakaan Pusat Teras',
        'badge'       => 'Cabang Pusat',
        'description' => 'Perpustakaan terbesar dan terlengkap di Desa Teras. Dilengkapi berbagai fasilitas modern untuk mendukung kegiatan literasi seluruh masyarakat dan komunitas belajar.',
        'heroImage'   => '/custom/assets/images/library-detail-hero.png',

        'meta' => [

            // Fasilitas yang tersedia
            'fasilitas' => [
                'WiFi Kecepatan Tinggi',
                'Pojok Media Digital & VR',
                '4 Area Ruangan Berbeda',
                'Teras Baca Luar Ruangan',
                'Ruang Diskusi Kedap Suara',
            ],

            // Statistik / Fakta menarik
            'faktaMenarik' => [
                ['nilai' => '12rb+', 'keterangan' => 'Buku dan Koleksi Terbanyak'],
                ['nilai' => '50rb+', 'keterangan' => 'Pengunjung Per Hari'],
            ],

            // Jam operasional
            'jamOperasional' => [
                ['hari' => 'Senin – Jumat', 'jam' => '08.00 – 20.00', 'tutup' => false],
                ['hari' => 'Sabtu',         'jam' => '09.00 – 18.00', 'tutup' => false],
                ['hari' => 'Minggu',        'jam' => 'Tutup',         'tutup' => true],
            ],
            'statusBuka'     => true,
            'catatanJam'     => 'Kunjungan grup & sekolah harap menghubungi kami terlebih dahulu untuk penjadwalan.',

            // Kegiatan / Event mendatang
            'kegiatan' => [
                [
                    'id'          => 'workshop-layanan-digital',
                    'tipe'        => 'Workshop',
                    'tipeCss'     => 'workshop',
                    'tanggal'     => '12 Jul 2026',
                    'waktu'       => '14:00 WIB',
                    'judul'       => 'Menguasai Layanan Digital',
                    'deskripsi'   => 'Pelajari cara mengakses dan memaksimalkan layanan perpustakaan digital untuk kehidupan sehari-hari dan dunia pendidikan.',
                    'image'       => '/custom/assets/images/event-workshop.png',
                    'ctaLabel'    => 'Daftar Gratis',
                    'ctaHref'     => '/kegiatan/workshop-layanan-digital',
                ],
                [
                    'id'          => 'lingkaran-sastra-klasik',
                    'tipe'        => 'Klub Baca',
                    'tipeCss'     => 'klub-baca',
                    'tanggal'     => '15 Jul 2026',
                    'waktu'       => '16:00 WIB',
                    'judul'       => 'Lingkaran Sastra Klasik',
                    'deskripsi'   => 'Diskusi terbuka karya sastra klasik Indonesia bersama para pecinta buku dari berbagai kalangan usia.',
                    'image'       => '/custom/assets/images/event-book-club.png',
                    'ctaLabel'    => 'Pesan Tempat',
                    'ctaHref'     => '/kegiatan/lingkaran-sastra-klasik',
                ],
                [
                    'id'          => 'waktu-bercerita-dan-origami',
                    'tipe'        => 'Art Space',
                    'tipeCss'     => 'art-space',
                    'tanggal'     => 'Setiap Sabtu',
                    'waktu'       => '10:00 WIB',
                    'judul'       => 'Waktu Bercerita & Origami',
                    'deskripsi'   => 'Sesi mendongeng dan kerajinan tangan origami untuk anak-anak usia 4–12 tahun bersama relawan literasi kami.',
                    'image'       => '/custom/assets/images/event-art-space.png',
                    'ctaLabel'    => 'Ikut Bergabung',
                    'ctaHref'     => '/kegiatan/waktu-bercerita-origami',
                ],
            ],

            // Galeri foto ruangan
            'gallery' => [
                ['image' => '/custom/assets/images/gallery-reading-room.png', 'alt' => 'Ruang Baca Utama'],
                ['image' => '/custom/assets/images/library-1.png',            'alt' => 'Eksterior Perpustakaan'],
                ['image' => '/custom/assets/images/gallery-study-corner.png', 'alt' => 'Pojok Belajar Digital'],
                ['image' => '/custom/assets/images/library-2.png',            'alt' => 'Taman Baca Luar Ruang'],
            ],

            // Koleksi buku terbaru (sample)
            'koleksiBuku' => [
                ['id' => 'untaian-kisah-lembut',   'title' => 'Untaian Kisah Lembut',   'author' => 'Rina Sari',        'category' => 'Fiksi',    'badge' => 'Baru',    'image' => '/custom/assets/images/book-cover-1.png', 'href' => '/katalog/untaian-kisah-lembut'],
                ['id' => 'koleksi-puisi-nusantara', 'title' => 'Koleksi Puisi Nusantara', 'author' => 'Budi Santoso',     'category' => 'Puisi',    'badge' => 'Populer', 'image' => '/custom/assets/images/book-cover-2.png', 'href' => '/katalog/koleksi-puisi-nusantara'],
                ['id' => 'pemikiran-tokoh-bangsa',  'title' => 'Pemikiran Tokoh Bangsa',  'author' => 'Dr. Ahmad Fauzi', 'category' => 'Nonfiksi', 'badge' => 'Baru',    'image' => '/custom/assets/images/book-cover-3.png', 'href' => '/katalog/pemikiran-tokoh-bangsa'],
                ['id' => 'jejak-nusantara',         'title' => 'Jejak Nusantara',         'author' => 'Siti Rahayu',      'category' => 'Sejarah',  'badge' => null,      'image' => '/custom/assets/images/book-cover-4.png', 'href' => '/katalog/jejak-nusantara'],
                ['id' => 'dongeng-desa-teras',      'title' => 'Dongeng Desa Teras',      'author' => 'Tim Literasi',    'category' => 'Anak',     'badge' => 'Baru',    'image' => '/custom/assets/images/book-cover-5.png', 'href' => '/katalog/dongeng-desa-teras'],
            ],

            // Informasi lokasi
            'lokasi' => [
                'alamat'      => 'Jl. Raya Desa Teras No. 45, Blok Tengah, Boyolali 57372',
                'transportasi'=> 'Pemberhentian bus/angkutan Teras Cemara (Bus A & C)',
                'mapsEmbed'   => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.034!2d110.648!3d-7.515!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sTeras%2C+Boyolali!5e0!3m2!1sid!2sid!4v0000000000000',
                'mapsLink'    => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
        ],
    ],

    /* ──────────────────────────────────────────────────────────
       Taman Baca Komunitas
       ────────────────────────────────────────────────────────── */
    'taman-baca-komunitas' => [
        'id'          => 'taman-baca-komunitas',
        'slug'        => 'taman-baca-komunitas',
        'name'        => 'Taman Baca Komunitas',
        'badge'       => 'Taman Baca',
        'description' => 'Ruang baca terbuka di tengah taman desa yang asri. Menjadi tempat favorit warga untuk bersantai sambil memperkaya pengetahuan bersama komunitas.',
        'heroImage'   => '/custom/assets/images/library-2.png',

        'meta' => [
            'fasilitas' => [
                'Area Baca Outdoor & Indoor',
                'WiFi Gratis',
                'Koleksi Buku Anak Lengkap',
                'Area Bermain Anak',
                'Gazebo Diskusi',
            ],
            'faktaMenarik' => [
                ['nilai' => '1.8rb+', 'keterangan' => 'Koleksi Buku Tersedia'],
                ['nilai' => '800+',   'keterangan' => 'Pengunjung Per Minggu'],
            ],
            'jamOperasional' => [
                ['hari' => 'Senin – Jumat', 'jam' => '09.00 – 18.00', 'tutup' => false],
                ['hari' => 'Sabtu',         'jam' => '08.00 – 16.00', 'tutup' => false],
                ['hari' => 'Minggu',        'jam' => '08.00 – 13.00', 'tutup' => false],
            ],
            'statusBuka'     => true,
            'catatanJam'     => 'Taman baca dapat digunakan untuk kegiatan komunitas dengan perjanjian terlebih dahulu.',
            'kegiatan'       => [],
            'gallery' => [
                ['image' => '/custom/assets/images/library-2.png',    'alt' => 'Suasana Taman Baca'],
                ['image' => '/custom/assets/images/about-village.png', 'alt' => 'Lingkungan Sekitar'],
                ['image' => '/custom/assets/images/library-1.png',    'alt' => 'Area Baca Dalam'],
                ['image' => '/custom/assets/images/news-featured.png', 'alt' => 'Kegiatan Komunitas'],
            ],
            'koleksiBuku' => [
                ['id' => 'dongeng-desa-teras',      'title' => 'Dongeng Desa Teras',      'author' => 'Tim Literasi',    'category' => 'Anak',     'badge' => 'Baru',    'image' => '/custom/assets/images/book-cover-5.png', 'href' => '/katalog/dongeng-desa-teras'],
                ['id' => 'koleksi-puisi-nusantara', 'title' => 'Koleksi Puisi Nusantara', 'author' => 'Budi Santoso',    'category' => 'Puisi',    'badge' => 'Populer', 'image' => '/custom/assets/images/book-cover-2.png', 'href' => '/katalog/koleksi-puisi-nusantara'],
                ['id' => 'jejak-nusantara',         'title' => 'Jejak Nusantara',         'author' => 'Siti Rahayu',     'category' => 'Sejarah',  'badge' => null,      'image' => '/custom/assets/images/book-cover-4.png', 'href' => '/katalog/jejak-nusantara'],
                ['id' => 'untaian-kisah-lembut',   'title' => 'Untaian Kisah Lembut',    'author' => 'Rina Sari',       'category' => 'Fiksi',    'badge' => 'Baru',    'image' => '/custom/assets/images/book-cover-1.png', 'href' => '/katalog/untaian-kisah-lembut'],
                ['id' => 'pemikiran-tokoh-bangsa',  'title' => 'Pemikiran Tokoh Bangsa',  'author' => 'Dr. Ahmad Fauzi', 'category' => 'Nonfiksi', 'badge' => null,      'image' => '/custom/assets/images/book-cover-3.png', 'href' => '/katalog/pemikiran-tokoh-bangsa'],
            ],
            'lokasi' => [
                'alamat'       => 'Jl. Pendidikan No. 5, RT 02 RW 03, Desa Teras, Boyolali',
                'transportasi' => 'Angkutan desa jalur B, turun di halte SD Teras 01',
                'mapsEmbed'    => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.034!2d110.648!3d-7.515!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sTeras%2C+Boyolali!5e0!3m2!1sid!2sid!4v0000000000000',
                'mapsLink'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
        ],
    ],

    /* ──────────────────────────────────────────────────────────
       Perpustakaan Digital Teras
       ────────────────────────────────────────────────────────── */
    'perpustakaan-digital' => [
        'id'          => 'perpustakaan-digital',
        'slug'        => 'perpustakaan-digital',
        'name'        => 'Perpustakaan Digital Teras',
        'badge'       => 'Digital',
        'description' => 'Perpustakaan berbasis teknologi digital terdepan di Desa Teras. Akses ribuan e-book, audio book, dan konten multimedia edukatif secara gratis.',
        'heroImage'   => '/custom/assets/images/library-3.png',

        'meta' => [
            'fasilitas' => [
                '30 Unit Komputer Modern',
                'E-Book Reader Station',
                'Studio Podcast Mini',
                'VR Learning Corner',
                'High-Speed Fiber WiFi',
            ],
            'faktaMenarik' => [
                ['nilai' => '6.5rb+', 'keterangan' => 'Koleksi Digital Tersedia'],
                ['nilai' => '2.000+', 'keterangan' => 'Pengguna Aktif Bulanan'],
            ],
            'jamOperasional' => [
                ['hari' => 'Senin – Jumat', 'jam' => '08.00 – 21.00', 'tutup' => false],
                ['hari' => 'Sabtu',         'jam' => '09.00 – 20.00', 'tutup' => false],
                ['hari' => 'Minggu',        'jam' => '10.00 – 16.00', 'tutup' => false],
            ],
            'statusBuka'     => true,
            'catatanJam'     => 'Akses platform digital tersedia 24/7 melalui aplikasi atau website.',
            'kegiatan'       => [],
            'gallery' => [
                ['image' => '/custom/assets/images/library-3.png',        'alt' => 'Area Komputer Digital'],
                ['image' => '/custom/assets/images/gallery-reading-room.png', 'alt' => 'Ruang Baca Modern'],
                ['image' => '/custom/assets/images/event-workshop.png',   'alt' => 'Workshop Digital'],
                ['image' => '/custom/assets/images/gallery-study-corner.png', 'alt' => 'Pojok Belajar'],
            ],
            'koleksiBuku' => [
                ['id' => 'pemikiran-tokoh-bangsa',  'title' => 'Pemikiran Tokoh Bangsa',  'author' => 'Dr. Ahmad Fauzi', 'category' => 'Nonfiksi', 'badge' => 'Baru',    'image' => '/custom/assets/images/book-cover-3.png', 'href' => '/katalog/pemikiran-tokoh-bangsa'],
                ['id' => 'jejak-nusantara',         'title' => 'Jejak Nusantara',         'author' => 'Siti Rahayu',     'category' => 'Sejarah',  'badge' => null,      'image' => '/custom/assets/images/book-cover-4.png', 'href' => '/katalog/jejak-nusantara'],
                ['id' => 'untaian-kisah-lembut',   'title' => 'Untaian Kisah Lembut',    'author' => 'Rina Sari',       'category' => 'Fiksi',    'badge' => 'Baru',    'image' => '/custom/assets/images/book-cover-1.png', 'href' => '/katalog/untaian-kisah-lembut'],
                ['id' => 'koleksi-puisi-nusantara', 'title' => 'Koleksi Puisi Nusantara', 'author' => 'Budi Santoso',    'category' => 'Puisi',    'badge' => 'Populer', 'image' => '/custom/assets/images/book-cover-2.png', 'href' => '/katalog/koleksi-puisi-nusantara'],
                ['id' => 'dongeng-desa-teras',      'title' => 'Dongeng Desa Teras',      'author' => 'Tim Literasi',    'category' => 'Anak',     'badge' => null,      'image' => '/custom/assets/images/book-cover-5.png', 'href' => '/katalog/dongeng-desa-teras'],
            ],
            'lokasi' => [
                'alamat'       => 'Jl. Merdeka No. 12, Pusat Desa Teras, Boyolali 57372',
                'transportasi' => 'Angkutan umum jalur A & C, berhenti di Kantor Desa Teras',
                'mapsEmbed'    => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.034!2d110.648!3d-7.515!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sTeras%2C+Boyolali!5e0!3m2!1sid!2sid!4v0000000000000',
                'mapsLink'     => 'https://maps.google.com/?q=Teras,Boyolali',
            ],
        ],
    ],

];
