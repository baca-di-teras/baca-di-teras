<?php
/**
 * Info Config
 *
 * File    : info-config.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Menyimpan data statis untuk halaman Pusat Informasi & Layanan.
 */

$infoServices = [
    'peminjaman' => [
        'title' => 'Panduan Peminjaman',
        'desc'  => 'Pelajari langkah mudah meminjam buku favorit Anda. Kami mendukung kemudahan akses literasi untuk seluruh warga.',
        'points'=> [
            'Maksimal 3 buku per anggota',
            'Durasi pinjam selama 14 hari',
            'Perpanjangan dapat dilakukan 1x'
        ],
        'action_label' => 'Lihat Detail Alur',
        'action_href'  => '#'
    ],
    'jam_operasional' => [
        'title' => 'Jam Operasional',
        'schedule' => [
            ['day' => 'Senin - Kamis', 'time' => '08:00 - 16:00'],
            ['day' => 'Jumat', 'time' => '08:00 - 11:30'],
            ['day' => 'Sabtu', 'time' => '09:00 - 13:00'],
            ['day' => 'Minggu & Libur Nasional', 'time' => 'Tutup', 'highlight' => true]
        ]
    ],
    'keanggotaan' => [
        'title' => 'Keanggotaan',
        'desc'  => 'Menjadi bagian dari komunitas pembaca Desa Teras sangatlah mudah dan gratis.',
        'action_label' => 'Daftar Sekarang',
        'action_href'  => '#'
    ],
    'unduhan' => [
        'title' => 'Unduhan',
        'files' => [
            ['label' => 'Formulir Pendaftaran', 'href' => '#'],
            ['label' => 'Katalog Buku 2024', 'href' => '#']
        ]
    ],
    'tata_tertib' => [
        'title' => 'Tata Tertib',
        'desc'  => 'Demi kenyamanan bersama, harap perhatikan etika di area perpustakaan.',
        'badges'=> ['Tenang', 'Bersih', 'Rapi']
    ]
];

$faqList = [
    [
        'question' => 'Bagaimana jika saya terlambat mengembalikan buku?',
        'answer'   => 'Anda akan dikenakan sanksi berupa denda administratif ringan atau pembatasan peminjaman sementara waktu sesuai tata tertib perpustakaan untuk menjaga sirkulasi buku tetap lancar.'
    ],
    [
        'question' => 'Apakah warga luar Desa Teras boleh meminjam buku?',
        'answer'   => 'Tentu saja! Siapa pun boleh membaca di tempat. Untuk peminjaman ke rumah, Anda perlu mendaftar menjadi anggota dengan menyertakan kartu identitas resmi.'
    ],
    [
        'question' => 'Bisakah saya menyumbangkan buku ke perpustakaan?',
        'answer'   => 'Sangat bisa! Kami menerima donasi buku layak baca untuk disalurkan ke 6 perpustakaan desa. Silakan hubungi pustakawan kami untuk informasi alur donasi lebih lanjut.'
    ],
    [
        'question' => 'Apakah ada akses internet/WiFi di area baca?',
        'answer'   => 'Ya, seluruh perpustakaan desa kami difasilitasi dengan koneksi internet nirkabel (WiFi) gratis untuk menunjang aktivitas belajar dan literasi digital warga.'
    ]
];
