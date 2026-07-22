<?php
/**
 * Pathfinder Service
 *
 * File    : PathfinderService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Mengelola data Pathfinder Perpustakaan.
 * Pathfinder adalah panduan riset yang mengkurasi sumber daya
 * terbaik (buku, jurnal, sumber internet) per topik tertentu.
 *
 * Saat ini menggunakan static data (hardcoded) karena belum
 * ada tabel database khusus pathfinder. Ketika tabel sudah
 * tersedia, ubah method agar query ke database.
 */

class PathfinderService
{
    // ── Kategori ─────────────────────────────────────────────

    /**
     * Data kategori pathfinder
     */
    private array $categories = [
        [
            'id'          => 1,
            'slug'        => 'teknologi',
            'label'       => 'Teknologi',
            'icon'        => 'globe',
            'description' => 'Jelajahi kumpulan panduan literasi dan referensi pilihan di bidang teknologi informasi, mulai dari dasar-dasar komputasi hingga inovasi terkini dalam kecerdasan buatan dan keamanan siber.',
            'tags'        => ['AI & Robotics', 'Software Engineering', 'Data Science', 'Cybersecurity', 'Cloud Computing'],
        ],
        [
            'id'          => 2,
            'slug'        => 'sains',
            'label'       => 'Sains',
            'icon'        => 'flask',
            'description' => 'Temukan referensi ilmiah terkurasi di bidang sains murni dan terapan, dari fisika kuantum hingga biologi molekuler dan ilmu lingkungan.',
            'tags'        => ['Fisika', 'Biologi', 'Kimia', 'Matematika', 'Lingkungan'],
        ],
        [
            'id'          => 3,
            'slug'        => 'seni-humaniora',
            'label'       => 'Seni & Humaniora',
            'icon'        => 'palette',
            'description' => 'Eksplorasi dunia seni, sastra, sejarah, dan filsafat melalui panduan literasi yang dirancang untuk memperkaya perspektif kemanusiaan Anda.',
            'tags'        => ['Sastra', 'Sejarah', 'Filsafat', 'Seni Rupa', 'Musik'],
        ],
        [
            'id'          => 4,
            'slug'        => 'ekonomi',
            'label'       => 'Ekonomi',
            'icon'        => 'chart',
            'description' => 'Akses panduan komprehensif tentang ekonomi, bisnis, dan keuangan — dari teori ekonomi makro hingga strategi manajemen operasional.',
            'tags'        => ['Manajemen', 'Akuntansi', 'Pemasaran', 'Keuangan', 'Kewirausahaan'],
        ],
        [
            'id'          => 5,
            'slug'        => 'hukum',
            'label'       => 'Hukum',
            'icon'        => 'scale',
            'description' => 'Panduan literasi hukum yang mengkurasi referensi penting mulai dari hukum perdata, pidana, hingga hukum internasional dan hak asasi manusia.',
            'tags'        => ['Hukum Perdata', 'Hukum Pidana', 'Hukum Tata Negara', 'HAM', 'Hukum Bisnis'],
        ],
    ];

    // ── Pathfinder Data ──────────────────────────────────────

    /**
     * Data lengkap pathfinder
     */
    private array $pathfinders = [
        [
            'id'             => 1,
            'slug'           => 'pemrograman-web',
            'title'          => 'Pemrograman Web: Fundamental ke Modern',
            'category_slug'  => 'teknologi',
            'category_label' => 'Teknologi',
            'category_icon'  => 'code',
            'badge'          => 'TERPOPULER',
            'recommendations'=> 24,
            'description'    => 'Pemrograman Web mencakup pemahaman mendalam tentang arsitektur client-server, manipulasi DOM, dan manajemen state untuk membangun aplikasi interaktif. Bidang ini memengaruhi cara kita berinteraksi dengan informasi dan layanan digital di era modern. Pemahaman tentang standar web sangat penting bagi pengembang di setiap tahapan, mulai dari fundamental hingga implementasi skala besar. Pathfinder ini bertujuan untuk memfasilitasi studi dan riset mengenai Pemrograman Web. Silakan klik pada bagian lain di halaman ini untuk melihat koleksi kami tentang \'Pemrograman Web\' baik dalam format cetak maupun online. Panduan ini juga dapat diunduh dalam format PDF yang dilengkapi dengan Hyperlink untuk referensi di masa mendatang.',
            'broader_terms'  => ['Ilmu Komputer', 'Rekayasa Perangkat Lunak'],
            'narrower_terms' => ['Pengembangan Frontend', 'Sistem Backend'],
            'related_terms'  => ['Desain UX/UI', 'Aksesibilitas Web'],
            'catalog_url'    => 'bacaditeras.my.id/katalog',
            'author'         => 'Nicholas Sio Pradiva',
            'updated_at'     => '11/2024',
            'books'          => [
                [
                    'title'      => 'Learning Web Design',
                    'author'     => 'Jennifer Robbins',
                    'year'       => 2018,
                    'publisher'  => "O'Reilly Media",
                    'call_number'=> '006.7 ROB l',
                    'isbn'       => '978-1-491-96020-2',
                    'location'   => 'Perpustakaan Utama - Bagian Teknologi',
                ],
                [
                    'title'      => 'Eloquent JavaScript',
                    'author'     => 'Marijn Haverbeke',
                    'year'       => 2018,
                    'publisher'  => 'No Starch Press',
                    'call_number'=> '005.133 HAV e',
                    'isbn'       => '978-1-593-27950-9',
                    'location'   => 'Perpustakaan Utama - Ilmu Komputer',
                ],
                [
                    'title'      => "Don't Make Me Think",
                    'author'     => 'Steve Krug',
                    'year'       => 2014,
                    'publisher'  => 'New Riders',
                    'call_number'=> '006.7 KRU d',
                    'isbn'       => '978-0-321-96551-6',
                    'location'   => 'Perpustakaan Desain & Seni',
                ],
            ],
            'web_resources'  => [
                [
                    'title'       => 'MDN Web Docs',
                    'description' => 'Sumber terpercaya untuk dokumentasi teknologi web terbuka.',
                    'url'         => 'https://developer.mozilla.org',
                ],
                [
                    'title'       => 'W3Schools Online Web Tutorials',
                    'description' => 'Panduan belajar interaktif untuk pemula dalam pemrograman web.',
                    'url'         => 'https://www.w3schools.com',
                ],
            ],
        ],
        [
            'id'             => 2,
            'slug'           => 'psikologi-pendidikan',
            'title'          => 'Psikologi Pendidikan: Teori Belajar Kognitif',
            'category_slug'  => 'sains',
            'category_label' => 'Sains',
            'category_icon'  => 'brain',
            'badge'          => '',
            'recommendations'=> 18,
            'description'    => 'Psikologi Pendidikan mempelajari bagaimana manusia belajar dan mengembangkan pengetahuan melalui proses kognitif. Bidang ini mencakup teori-teori belajar klasik hingga pendekatan modern yang melibatkan neurosains dan teknologi pendidikan. Pathfinder ini dirancang untuk membantu mahasiswa, pendidik, dan peneliti dalam memahami fondasi psikologis dari proses pembelajaran.',
            'broader_terms'  => ['Psikologi', 'Ilmu Pendidikan'],
            'narrower_terms' => ['Psikologi Perkembangan', 'Teori Motivasi'],
            'related_terms'  => ['Kurikulum', 'Metode Pembelajaran'],
            'catalog_url'    => 'bacaditeras.my.id/katalog',
            'author'         => 'Nicholas Sio Pradiva',
            'updated_at'     => '10/2024',
            'books'          => [
                [
                    'title'      => 'Educational Psychology',
                    'author'     => 'Anita Woolfolk',
                    'year'       => 2019,
                    'publisher'  => 'Pearson',
                    'call_number'=> '370.15 WOO e',
                    'isbn'       => '978-0-134-77410-9',
                    'location'   => 'Perpustakaan Utama - Pendidikan',
                ],
                [
                    'title'      => 'How People Learn II',
                    'author'     => 'National Academies',
                    'year'       => 2018,
                    'publisher'  => 'NAP',
                    'call_number'=> '370.15 NAT h',
                    'isbn'       => '978-0-309-45964-8',
                    'location'   => 'Perpustakaan Utama - Pendidikan',
                ],
            ],
            'web_resources'  => [
                [
                    'title'       => 'APA PsycInfo',
                    'description' => 'Database artikel psikologi terbesar dari American Psychological Association.',
                    'url'         => 'https://www.apa.org/pubs/databases/psycinfo',
                ],
            ],
        ],
        [
            'id'             => 3,
            'slug'           => 'manajemen-bisnis',
            'title'          => 'Manajemen Bisnis: Strategi Operasional UKM',
            'category_slug'  => 'ekonomi',
            'category_label' => 'Ekonomi',
            'category_icon'  => 'briefcase',
            'badge'          => '',
            'recommendations'=> 15,
            'description'    => 'Manajemen Bisnis untuk UKM mencakup perencanaan strategis, pengelolaan operasional, dan pengembangan usaha kecil menengah. Pathfinder ini menyajikan referensi terpilih yang membantu wirausahawan dan pengelola bisnis memahami praktik terbaik dalam menjalankan dan mengembangkan usaha.',
            'broader_terms'  => ['Ilmu Manajemen', 'Ekonomi Bisnis'],
            'narrower_terms' => ['Manajemen Keuangan UKM', 'Pemasaran Digital'],
            'related_terms'  => ['Kewirausahaan', 'E-Commerce'],
            'catalog_url'    => 'bacaditeras.my.id/katalog',
            'author'         => 'Nicholas Sio Pradiva',
            'updated_at'     => '09/2024',
            'books'          => [
                [
                    'title'      => 'The Lean Startup',
                    'author'     => 'Eric Ries',
                    'year'       => 2011,
                    'publisher'  => 'Crown Business',
                    'call_number'=> '658.11 RIE t',
                    'isbn'       => '978-0-307-88789-4',
                    'location'   => 'Perpustakaan Utama - Bisnis',
                ],
                [
                    'title'      => 'Good to Great',
                    'author'     => 'Jim Collins',
                    'year'       => 2001,
                    'publisher'  => 'HarperBusiness',
                    'call_number'=> '658 COL g',
                    'isbn'       => '978-0-066-62099-2',
                    'location'   => 'Perpustakaan Utama - Bisnis',
                ],
            ],
            'web_resources'  => [
                [
                    'title'       => 'Harvard Business Review',
                    'description' => 'Artikel dan riset terkini seputar manajemen dan bisnis global.',
                    'url'         => 'https://hbr.org',
                ],
            ],
        ],
        [
            'id'             => 4,
            'slug'           => 'arsitektur-modern',
            'title'          => 'Arsitektur Modern: Pasca Perang Dunia II',
            'category_slug'  => 'seni-humaniora',
            'category_label' => 'Seni & Humaniora',
            'category_icon'  => 'building',
            'badge'          => '',
            'recommendations'=> 31,
            'description'    => 'Arsitektur Modern pasca Perang Dunia II menandai era perubahan besar dalam desain bangunan. Dari gerakan Brutalist hingga High-Tech Architecture, pathfinder ini mengkurasi referensi penting untuk memahami evolusi arsitektur kontemporer dan dampaknya terhadap lingkungan urban.',
            'broader_terms'  => ['Sejarah Seni', 'Desain Lingkungan'],
            'narrower_terms' => ['Arsitektur Brutalis', 'Arsitektur Berkelanjutan'],
            'related_terms'  => ['Urban Planning', 'Interior Design'],
            'catalog_url'    => 'bacaditeras.my.id/katalog',
            'author'         => 'Nicholas Sio Pradiva',
            'updated_at'     => '08/2024',
            'books'          => [
                [
                    'title'      => 'Modern Architecture: A Critical History',
                    'author'     => 'Kenneth Frampton',
                    'year'       => 2020,
                    'publisher'  => 'Thames & Hudson',
                    'call_number'=> '720.9 FRA m',
                    'isbn'       => '978-0-500-20440-9',
                    'location'   => 'Perpustakaan Desain & Seni',
                ],
            ],
            'web_resources'  => [
                [
                    'title'       => 'ArchDaily',
                    'description' => 'Portal arsitektur terbesar di dunia dengan ribuan proyek dan artikel.',
                    'url'         => 'https://www.archdaily.com',
                ],
            ],
        ],
        [
            'id'             => 5,
            'slug'           => 'kecerdasan-buatan',
            'title'          => 'Kecerdasan Buatan (AI)',
            'category_slug'  => 'teknologi',
            'category_label' => 'Teknologi',
            'category_icon'  => 'cpu',
            'badge'          => 'TERPOPULER',
            'recommendations'=> 42,
            'description'    => 'Panduan komprehensif mengenai sejarah, algoritma pembelajaran mesin, dan penerapan etis AI dalam penelitian modern. Kecerdasan Buatan telah merevolusi berbagai industri mulai dari healthcare hingga transportasi.',
            'broader_terms'  => ['Ilmu Komputer', 'Matematika Terapan'],
            'narrower_terms' => ['Machine Learning', 'Deep Learning', 'NLP'],
            'related_terms'  => ['Data Science', 'Robotika'],
            'catalog_url'    => 'bacaditeras.my.id/katalog',
            'author'         => 'Nicholas Sio Pradiva',
            'updated_at'     => '11/2024',
            'books'          => [
                [
                    'title'      => 'Artificial Intelligence: A Modern Approach',
                    'author'     => 'Stuart Russell & Peter Norvig',
                    'year'       => 2020,
                    'publisher'  => 'Pearson',
                    'call_number'=> '006.3 RUS a',
                    'isbn'       => '978-0-134-61099-3',
                    'location'   => 'Perpustakaan Utama - Teknologi',
                ],
            ],
            'web_resources'  => [
                [
                    'title'       => 'Papers with Code',
                    'description' => 'Repositori paper AI terbaru beserta implementasi kode open source.',
                    'url'         => 'https://paperswithcode.com',
                ],
            ],
        ],
        [
            'id'             => 6,
            'slug'           => 'cybersecurity-basics',
            'title'          => 'Cybersecurity Basics',
            'category_slug'  => 'teknologi',
            'category_label' => 'Teknologi',
            'category_icon'  => 'shield',
            'badge'          => 'KEAMANAN',
            'recommendations'=> 20,
            'description'    => 'Pelajari protokol keamanan siber, manajemen ancaman, dan cara melindungi integritas data digital dalam infrastruktur jaringan.',
            'broader_terms'  => ['Ilmu Komputer', 'Keamanan Informasi'],
            'narrower_terms' => ['Kriptografi', 'Network Security'],
            'related_terms'  => ['Forensik Digital', 'Ethical Hacking'],
            'catalog_url'    => 'bacaditeras.my.id/katalog',
            'author'         => 'Nicholas Sio Pradiva',
            'updated_at'     => '10/2024',
            'books'          => [
                [
                    'title'      => 'Cybersecurity Essentials',
                    'author'     => 'Charles J. Brooks',
                    'year'       => 2018,
                    'publisher'  => 'Sybex',
                    'call_number'=> '005.8 BRO c',
                    'isbn'       => '978-1-119-36239-5',
                    'location'   => 'Perpustakaan Utama - Teknologi',
                ],
            ],
            'web_resources'  => [
                [
                    'title'       => 'OWASP Foundation',
                    'description' => 'Panduan keamanan aplikasi web dan best practices.',
                    'url'         => 'https://owasp.org',
                ],
            ],
        ],
        [
            'id'             => 7,
            'slug'           => 'cloud-computing',
            'title'          => 'Cloud Computing',
            'category_slug'  => 'teknologi',
            'category_label' => 'Teknologi',
            'category_icon'  => 'cloud',
            'badge'          => 'INFRASTRUKTUR',
            'recommendations'=> 16,
            'description'    => 'Eksplorasi model layanan awan (IaaS, PaaS, SaaS) dan bagaimana teknologi ini merevolusi skalabilitas sistem informasi global.',
            'broader_terms'  => ['Ilmu Komputer', 'Infrastruktur TI'],
            'narrower_terms' => ['AWS', 'Azure', 'Google Cloud'],
            'related_terms'  => ['DevOps', 'Containerization'],
            'catalog_url'    => 'bacaditeras.my.id/katalog',
            'author'         => 'Nicholas Sio Pradiva',
            'updated_at'     => '09/2024',
            'books'          => [
                [
                    'title'      => 'Cloud Computing: Concepts, Technology & Architecture',
                    'author'     => 'Thomas Erl',
                    'year'       => 2013,
                    'publisher'  => 'Prentice Hall',
                    'call_number'=> '004.67 ERL c',
                    'isbn'       => '978-0-133-38752-0',
                    'location'   => 'Perpustakaan Utama - Teknologi',
                ],
            ],
            'web_resources'  => [
                [
                    'title'       => 'AWS Documentation',
                    'description' => 'Dokumentasi resmi Amazon Web Services untuk semua layanan cloud.',
                    'url'         => 'https://docs.aws.amazon.com',
                ],
            ],
        ],
        [
            'id'             => 8,
            'slug'           => 'software-development',
            'title'          => 'Software Development',
            'category_slug'  => 'teknologi',
            'category_label' => 'Teknologi',
            'category_icon'  => 'code',
            'badge'          => '',
            'recommendations'=> 22,
            'description'    => 'Metodologi Agile, pemodelan sistem menggunakan UML, dan praktik terbaik dalam siklus hidup pengembangan perangkat lunak modern.',
            'broader_terms'  => ['Ilmu Komputer', 'Rekayasa Perangkat Lunak'],
            'narrower_terms' => ['Agile Methodology', 'DevOps'],
            'related_terms'  => ['Project Management', 'Quality Assurance'],
            'catalog_url'    => 'bacaditeras.my.id/katalog',
            'author'         => 'Nicholas Sio Pradiva',
            'updated_at'     => '11/2024',
            'books'          => [
                [
                    'title'      => 'Clean Code',
                    'author'     => 'Robert C. Martin',
                    'year'       => 2008,
                    'publisher'  => 'Prentice Hall',
                    'call_number'=> '005.1 MAR c',
                    'isbn'       => '978-0-132-35088-4',
                    'location'   => 'Perpustakaan Utama - Teknologi',
                ],
            ],
            'web_resources'  => [
                [
                    'title'       => 'GitHub',
                    'description' => 'Platform kolaborasi kode dan version control terbesar di dunia.',
                    'url'         => 'https://github.com',
                ],
            ],
        ],
        [
            'id'             => 9,
            'slug'           => 'internet-of-things',
            'title'          => 'Internet of Things (IoT)',
            'category_slug'  => 'teknologi',
            'category_label' => 'Teknologi',
            'category_icon'  => 'wifi',
            'badge'          => '',
            'recommendations'=> 14,
            'description'    => 'Memahami ekosistem sensor, konektivitas cerdas, dan interaksi antara perangkat fisik dengan sistem komputasi terdistribusi.',
            'broader_terms'  => ['Ilmu Komputer', 'Elektronika'],
            'narrower_terms' => ['Smart Home', 'Industrial IoT'],
            'related_terms'  => ['Embedded Systems', 'Edge Computing'],
            'catalog_url'    => 'bacaditeras.my.id/katalog',
            'author'         => 'Nicholas Sio Pradiva',
            'updated_at'     => '08/2024',
            'books'          => [
                [
                    'title'      => 'Building the Internet of Things',
                    'author'     => 'Maciej Kranz',
                    'year'       => 2017,
                    'publisher'  => 'Wiley',
                    'call_number'=> '004.678 KRA b',
                    'isbn'       => '978-1-119-28563-1',
                    'location'   => 'Perpustakaan Utama - Teknologi',
                ],
            ],
            'web_resources'  => [
                [
                    'title'       => 'IoT For All',
                    'description' => 'Media dan komunitas untuk berita dan edukasi Internet of Things.',
                    'url'         => 'https://www.iotforall.com',
                ],
            ],
        ],
        [
            'id'             => 10,
            'slug'           => 'analisis-data-besar',
            'title'          => 'Analisis Data Besar',
            'category_slug'  => 'teknologi',
            'category_label' => 'Teknologi',
            'category_icon'  => 'database',
            'badge'          => '',
            'recommendations'=> 19,
            'description'    => 'Teknik pengolahan Big Data menggunakan Hadoop dan Spark, serta cara mengekstrak wawasan berharga dari volume data yang masif.',
            'broader_terms'  => ['Ilmu Komputer', 'Statistika'],
            'narrower_terms' => ['Data Mining', 'Data Visualization'],
            'related_terms'  => ['Machine Learning', 'Business Intelligence'],
            'catalog_url'    => 'bacaditeras.my.id/katalog',
            'author'         => 'Nicholas Sio Pradiva',
            'updated_at'     => '10/2024',
            'books'          => [
                [
                    'title'      => 'Big Data: A Revolution',
                    'author'     => 'Viktor Mayer-Schönberger',
                    'year'       => 2013,
                    'publisher'  => 'Mariner Books',
                    'call_number'=> '006.31 MAY b',
                    'isbn'       => '978-0-544-00269-2',
                    'location'   => 'Perpustakaan Utama - Teknologi',
                ],
            ],
            'web_resources'  => [
                [
                    'title'       => 'Kaggle',
                    'description' => 'Platform komunitas data science dengan dataset dan kompetisi.',
                    'url'         => 'https://www.kaggle.com',
                ],
            ],
        ],
        [
            'id'             => 11,
            'slug'           => 'hukum-perdata-indonesia',
            'title'          => 'Hukum Perdata Indonesia',
            'category_slug'  => 'hukum',
            'category_label' => 'Hukum',
            'category_icon'  => 'scale',
            'badge'          => '',
            'recommendations'=> 12,
            'description'    => 'Panduan literasi hukum perdata di Indonesia mencakup hukum perjanjian, hukum benda, dan hukum keluarga berdasarkan KUHPerdata dan peraturan terkait.',
            'broader_terms'  => ['Ilmu Hukum', 'Hukum Indonesia'],
            'narrower_terms' => ['Hukum Perjanjian', 'Hukum Waris'],
            'related_terms'  => ['Hukum Acara Perdata', 'Hukum Bisnis'],
            'catalog_url'    => 'bacaditeras.my.id/katalog',
            'author'         => 'Nicholas Sio Pradiva',
            'updated_at'     => '07/2024',
            'books'          => [
                [
                    'title'      => 'Hukum Perdata Indonesia',
                    'author'     => 'P.N.H. Simanjuntak',
                    'year'       => 2017,
                    'publisher'  => 'Kencana',
                    'call_number'=> '346.598 SIM h',
                    'isbn'       => '978-602-422-123-4',
                    'location'   => 'Perpustakaan Utama - Hukum',
                ],
            ],
            'web_resources'  => [
                [
                    'title'       => 'Hukumonline',
                    'description' => 'Portal hukum terlengkap di Indonesia dengan database peraturan.',
                    'url'         => 'https://www.hukumonline.com',
                ],
            ],
        ],
    ];

    // ── Public Methods ───────────────────────────────────────

    /**
     * Mengambil daftar semua kategori pathfinder.
     *
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Mengambil data satu kategori berdasarkan slug.
     *
     * @param  string     $slug
     * @return array|null
     */
    public function getCategoryBySlug(string $slug): ?array
    {
        foreach ($this->categories as $cat) {
            if ($cat['slug'] === $slug) {
                return $cat;
            }
        }
        return null;
    }

    /**
     * Mengambil pathfinder populer (untuk halaman utama).
     * Mengembalikan 4 pathfinder dengan rekomendasi terbanyak.
     *
     * @param  int   $limit
     * @return array
     */
    public function getPopularPathfinders(int $limit = 4): array
    {
        $sorted = $this->pathfinders;
        usort($sorted, fn($a, $b) => $b['recommendations'] <=> $a['recommendations']);
        return array_slice($sorted, 0, $limit);
    }

    /**
     * Mengambil daftar pathfinder per kategori.
     *
     * @param  string $categorySlug
     * @param  int    $page
     * @param  int    $perPage
     * @return array  ['items' => [...], 'total' => int, 'page' => int, 'per_page' => int, 'total_pages' => int]
     */
    public function getPathfindersByCategory(string $categorySlug, int $page = 1, int $perPage = 6): array
    {
        $filtered = array_filter(
            $this->pathfinders,
            fn($p) => $p['category_slug'] === $categorySlug
        );
        $filtered = array_values($filtered);

        $total      = count($filtered);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page       = max(1, min($page, $totalPages));
        $offset     = ($page - 1) * $perPage;
        $items      = array_slice($filtered, $offset, $perPage);

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
        ];
    }

    /**
     * Mengambil data detail satu pathfinder berdasarkan slug.
     *
     * @param  string     $slug
     * @return array|null
     */
    public function getPathfinderBySlug(string $slug): ?array
    {
        foreach ($this->pathfinders as $pf) {
            if ($pf['slug'] === $slug) {
                return $pf;
            }
        }
        return null;
    }

    /**
     * Mencari pathfinder berdasarkan keyword (judul atau deskripsi).
     *
     * @param  string $keyword
     * @return array
     */
    public function searchPathfinders(string $keyword): array
    {
        if (trim($keyword) === '') {
            return $this->pathfinders;
        }

        $keyword = mb_strtolower(trim($keyword));

        return array_values(array_filter(
            $this->pathfinders,
            function ($pf) use ($keyword) {
                return str_contains(mb_strtolower($pf['title']), $keyword)
                    || str_contains(mb_strtolower($pf['description']), $keyword)
                    || str_contains(mb_strtolower($pf['category_label']), $keyword);
            }
        ));
    }

    /**
     * Mengambil semua pathfinder (untuk keperluan listing).
     *
     * @return array
     */
    public function getAllPathfinders(): array
    {
        return $this->pathfinders;
    }
}
