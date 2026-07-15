<?php
/**
 * Article Service
 *
 * File    : ArticleService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Seluruh query yang berhubungan dengan artikel dan berita.
 * Menggunakan tabel: bdt_article, bdt_article_tag, bdt_article_view,
 * dan integrasi SLiMS: user (penulis), biblio (resensi buku).
 *
 * ATURAN:
 *   - Tidak ada SQL mentah di halaman PHP.
 *   - Kembalikan selalu array PHP yang siap dipakai view.
 *
 * Usage:
 *   require_once ROOT_PATH . '/custom/services/ArticleService.php';
 *   $service  = new ArticleService();
 *   $articles = $service->getPublished(6);
 *   $article  = $service->getBySlug('festival-baca-2026');
 */

require_once __DIR__ . '/../helpers/Database.php';

class ArticleService
{
    private Database $db;

    /** Kategori valid sesuai ENUM di tabel bdt_article */
    public const CATEGORIES = [
        'berita',
        'kegiatan',
        'pengumuman',
        'resensi',
        'literasi',
        'lainnya',
    ];

    /** Label tampilan untuk setiap kategori */
    public const CATEGORY_LABELS = [
        'berita'       => 'Berita',
        'kegiatan'     => 'Kegiatan',
        'pengumuman'   => 'Pengumuman',
        'resensi'      => 'Resensi Buku',
        'literasi'     => 'Literasi',
        'lainnya'      => 'Lainnya',
    ];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Query Artikel ─────────────────────────────────────────

    /**
     * Ambil daftar artikel published, diurutkan terbaru.
     * Digunakan di: halaman /artikel, /berita.
     *
     * @param  int $limit
     * @param  int $offset
     * @return array
     */
    public function getPublished(int $limit = 10, int $offset = 0): array
    {
        return $this->db->fetchAll(
            'SELECT
                a.article_id,
                a.title,
                a.slug,
                a.excerpt,
                a.cover_image,
                a.category,
                a.publish_date,
                a.is_featured,
                a.is_pinned,
                a.view_count,
                l.name AS library_name,
                l.slug AS library_slug,
                u.realname AS author_name
             FROM bdt_article a
             LEFT JOIN bdt_library l ON a.library_id = l.library_id
             LEFT JOIN user        u ON a.created_by  = u.user_id
             WHERE a.status = "published"
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             ORDER BY a.is_pinned DESC, a.publish_date DESC
             LIMIT ? OFFSET ?',
            'ii',
            [$limit, $offset]
        );
    }

    /**
     * Ambil daftar artikel published, tidak termasuk kategori berita.
     *
     * @param  int $limit
     * @param  int $offset
     * @return array
     */
    public function getPublishedArticles(int $limit = 10, int $offset = 0): array
    {
        return $this->db->fetchAll(
            'SELECT
                a.article_id,
                a.title,
                a.slug,
                a.excerpt,
                a.cover_image,
                a.category,
                a.publish_date,
                a.is_featured,
                a.is_pinned,
                a.view_count,
                l.name AS library_name,
                l.slug AS library_slug,
                u.realname AS author_name
             FROM bdt_article a
             LEFT JOIN bdt_library l ON a.library_id = l.library_id
             LEFT JOIN user        u ON a.created_by  = u.user_id
             WHERE a.status = "published"
               AND a.category != "berita"
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             ORDER BY a.is_pinned DESC, a.publish_date DESC
             LIMIT ? OFFSET ?',
            'ii',
            [$limit, $offset]
        );
    }

    /**
     * Hitung total artikel published (untuk pagination).
     *
     * @param  string $category  Kosong = semua kategori
     * @return int
     */
    public function countPublished(string $category = ''): int
    {
        if ($category !== '' && in_array($category, self::CATEGORIES, true)) {
            $count = $this->db->fetchScalar(
                'SELECT COUNT(*)
                 FROM bdt_article
                 WHERE status   = "published"
                   AND category = ?
                   AND (publish_date IS NULL OR publish_date <= NOW())',
                's',
                [$category]
            );
        } else {
            $count = $this->db->fetchScalar(
                'SELECT COUNT(*)
                 FROM bdt_article
                 WHERE status = "published"
                   AND (publish_date IS NULL OR publish_date <= NOW())'
            );
        }

        return (int) $count;
    }

    /**
     * Hitung artikel published, tidak termasuk kategori berita.
     *
     * @param  string $category
     * @return int
     */
    public function countPublishedArticles(string $category = ''): int
    {
        if ($category !== '' && $category !== 'berita' && in_array($category, self::CATEGORIES, true)) {
            $count = $this->db->fetchScalar(
                'SELECT COUNT(*)
                 FROM bdt_article
                 WHERE status   = "published"
                   AND category = ?
                   AND (publish_date IS NULL OR publish_date <= NOW())',
                's',
                [$category]
            );
        } else {
            $count = $this->db->fetchScalar(
                'SELECT COUNT(*)
                 FROM bdt_article
                 WHERE status = "published"
                   AND category != "berita"
                   AND (publish_date IS NULL OR publish_date <= NOW())'
            );
        }

        return (int) $count;
    }

    /**
     * Ambil artikel unggulan (is_featured = 1).
     * Digunakan di: landing page — seksi "Berita & Kegiatan".
     *
     * @param  int $limit
     * @return array
     */
    public function getFeatured(int $limit = 3): array
    {
        return $this->db->fetchAll(
            'SELECT
                a.article_id,
                a.title,
                a.slug,
                a.excerpt,
                a.cover_image,
                a.category,
                a.publish_date,
                a.view_count,
                l.name AS library_name,
                l.slug AS library_slug
             FROM bdt_article a
             LEFT JOIN bdt_library l ON a.library_id = l.library_id
             WHERE a.status      = "published"
               AND a.is_featured = 1
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             ORDER BY a.publish_date DESC
             LIMIT ?',
            'i',
            [$limit]
        );
    }

    /**
     * Ambil satu artikel featured sebagai artikel utama (hero).
     * Digunakan di: landing page, halaman /berita.
     *
     * @return array|null
     */
    public function getHeroArticle(bool $includeNews = true): ?array
    {
        $categoryCondition = $includeNews ? '' : 'AND a.category != "berita"';

        return $this->db->fetchOne(
            'SELECT
                a.article_id,
                a.title,
                a.slug,
                a.excerpt,
                a.cover_image,
                a.category,
                a.publish_date,
                a.view_count,
                l.name     AS library_name,
                u.realname AS author_name
             FROM bdt_article a
             LEFT JOIN bdt_library l ON a.library_id = l.library_id
             LEFT JOIN user        u ON a.created_by  = u.user_id
             WHERE a.status      = "published"
               AND a.is_featured = 1
               ' . $categoryCondition . '
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             ORDER BY a.is_pinned DESC, a.publish_date DESC
             LIMIT 1'
        );
    }

    /**
     * Ambil artikel terbaru (non-featured) untuk sidebar/grid.
     * Digunakan di: landing page, sidebar halaman /berita.
     *
     * @param  int $limit
     * @param  int $excludeId  ID artikel yang dikecualikan (biasanya hero)
     * @return array
     */
    public function getRecent(int $limit = 5, int $excludeId = 0): array
    {
        return $this->db->fetchAll(
            'SELECT
                a.article_id,
                a.title,
                a.slug,
                a.excerpt,
                a.cover_image,
                a.category,
                a.publish_date,
                a.view_count
             FROM bdt_article a
             WHERE a.status      = "published"
               AND a.article_id != ?
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             ORDER BY a.publish_date DESC
             LIMIT ?',
            'ii',
            [$excludeId, $limit]
        );
    }

    /**
     * Ambil artikel terpopuler dalam N hari terakhir.
     * Digunakan di: sidebar halaman artikel.
     *
     * @param  int $limit
     * @param  int $days
     * @return array
     */
    public function getPopular(int $limit = 5, int $days = 30): array
    {
        return $this->db->fetchAll(
            'SELECT
                a.article_id,
                a.title,
                a.slug,
                a.cover_image,
                a.category,
                a.publish_date,
                COALESCE(SUM(v.view_count), 0) AS total_views
             FROM bdt_article a
             LEFT JOIN bdt_article_view v
                    ON a.article_id = v.article_id
                   AND v.view_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             WHERE a.status = "published"
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             GROUP BY a.article_id
             ORDER BY total_views DESC, a.publish_date DESC
             LIMIT ?',
            'ii',
            [$days, $limit]
        );
    }

    /**
     * Ambil artikel berdasarkan kategori.
     *
     * @param  string $category
     * @param  int    $limit
     * @param  int    $offset
     * @return array
     */
    public function getByCategory(string $category, int $limit = 10, int $offset = 0): array
    {
        if (!in_array($category, self::CATEGORIES, true)) {
            return [];
        }

        return $this->db->fetchAll(
            'SELECT
                a.article_id,
                a.title,
                a.slug,
                a.excerpt,
                a.cover_image,
                a.category,
                a.publish_date,
                a.view_count,
                l.name AS library_name
             FROM bdt_article a
             LEFT JOIN bdt_library l ON a.library_id = l.library_id
             WHERE a.status   = "published"
               AND a.category = ?
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             ORDER BY a.is_pinned DESC, a.publish_date DESC
             LIMIT ? OFFSET ?',
            'sii',
            [$category, $limit, $offset]
        );
    }

    // ── Detail Artikel ────────────────────────────────────────

    /**
     * Ambil detail satu artikel berdasarkan slug URL.
     * Digunakan di: halaman /berita/{slug}, /artikel/{slug}.
     *
     * @param  string    $slug
     * @return array|null
     */
    public function getBySlug(string $slug): ?array
    {
        $article = $this->db->fetchOne(
            'SELECT
                a.*,
                l.name      AS library_name,
                l.slug      AS library_slug,
                l.address   AS library_address,
                u.realname  AS author_name
             FROM bdt_article a
             LEFT JOIN bdt_library l ON a.library_id = l.library_id
             LEFT JOIN user        u ON a.created_by  = u.user_id
             WHERE a.slug   = ?
               AND a.status = "published"
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             LIMIT 1',
            's',
            [$slug]
        );

        if (!$article) {
            return null;
        }

        // Tambahkan tag
        $article['tags'] = $this->getTags((int) $article['article_id']);

        return $article;
    }

    /**
     * Ambil artikel terkait berdasarkan kategori atau perpustakaan yang sama.
     * Digunakan di: halaman detail artikel — seksi "Artikel Terkait".
     *
     * @param  int    $articleId   Artikel saat ini (dikecualikan)
     * @param  string $category
     * @param  int    $libraryId   0 jika tidak ada relasi perpus
     * @param  int    $limit
     * @return array
     */
    public function getRelated(int $articleId, string $category, int $libraryId = 0, int $limit = 4): array
    {
        return $this->db->fetchAll(
            'SELECT
                a.article_id,
                a.title,
                a.slug,
                a.excerpt,
                a.cover_image,
                a.category,
                a.publish_date,
                l.name AS library_name
             FROM bdt_article a
             LEFT JOIN bdt_library l ON a.library_id = l.library_id
             WHERE a.status      = "published"
               AND a.article_id != ?
               AND (a.category   = ? OR a.library_id = ?)
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             ORDER BY a.publish_date DESC
             LIMIT ?',
            'isii',
            [$articleId, $category, $libraryId ?: 0, $limit]
        );
    }

    // ── Tag ───────────────────────────────────────────────────

    /**
     * Ambil semua tag suatu artikel.
     *
     * @param  int $articleId
     * @return array  Array of strings
     */
    public function getTags(int $articleId): array
    {
        $rows = $this->db->fetchAll(
            'SELECT tag_name FROM bdt_article_tag WHERE article_id = ? ORDER BY tag_name ASC',
            'i',
            [$articleId]
        );

        return array_column($rows, 'tag_name');
    }

    /**
     * Ambil artikel berdasarkan tag.
     *
     * @param  string $tagName
     * @param  int    $limit
     * @return array
     */
    public function getByTag(string $tagName, int $limit = 10): array
    {
        return $this->db->fetchAll(
            'SELECT
                a.article_id,
                a.title,
                a.slug,
                a.excerpt,
                a.cover_image,
                a.category,
                a.publish_date,
                l.name AS library_name
             FROM bdt_article a
             JOIN bdt_article_tag  t ON a.article_id = t.article_id
             LEFT JOIN bdt_library l ON a.library_id  = l.library_id
             WHERE a.status   = "published"
               AND t.tag_name = ?
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             ORDER BY a.publish_date DESC
             LIMIT ?',
            'si',
            [$tagName, $limit]
        );
    }

    // ── Statistik Pembacaan ───────────────────────────────────

    /**
     * Catat satu pembacaan artikel.
     * Idempotent per hari — menggunakan INSERT ... ON DUPLICATE KEY UPDATE.
     * Dipanggil di awal setiap halaman detail artikel.
     *
     * @param  int $articleId
     * @return void
     */
    public function recordView(int $articleId): void
    {
        // Catat ke tabel statistik harian
        $this->db->execute(
            'INSERT INTO bdt_article_view (article_id, view_date, view_count)
             VALUES (?, CURDATE(), 1)
             ON DUPLICATE KEY UPDATE view_count = view_count + 1',
            'i',
            [$articleId]
        );

        // Increment counter denormalized di bdt_article
        $this->db->execute(
            'UPDATE bdt_article SET view_count = view_count + 1 WHERE article_id = ?',
            'i',
            [$articleId]
        );
    }

    // ── Write (Insert / Update) ───────────────────────────────

    /**
     * Buat artikel baru.
     * Digunakan dari panel admin atau integrasi SLiMS.
     *
     * @param  array $data
     * @return int   article_id baru
     */
    public function create(array $data): int
    {
        $this->db->execute(
            'INSERT INTO bdt_article
                (title, slug, excerpt, body, cover_image, category,
                 status, publish_date, is_featured, is_pinned,
                 created_by, library_id, biblio_id, meta_description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            'ssssssssiiiii s',
            [
                $data['title']           ?? '',
                $data['slug']            ?? self::generateSlug($data['title'] ?? ''),
                $data['excerpt']         ?? null,
                $data['body']            ?? '',
                $data['cover_image']     ?? null,
                $data['category']        ?? 'berita',
                $data['status']          ?? 'draft',
                $data['publish_date']    ?? null,
                (int) ($data['is_featured'] ?? 0),
                (int) ($data['is_pinned']   ?? 0),
                $data['created_by']      ?? null,
                $data['library_id']      ?? null,
                $data['biblio_id']       ?? null,
                $data['meta_description']?? null,
            ]
        );

        $articleId = $this->db->lastInsertId();

        if (!empty($data['tags']) && is_array($data['tags'])) {
            $this->saveTags($articleId, $data['tags']);
        }

        return $articleId;
    }

    // ── Helper Statis ────────────────────────────────────────

    /**
     * Generate slug URL dari judul artikel.
     *
     * @param  string $title
     * @return string
     */
    public static function generateSlug(string $title): string
    {
        $slug = mb_strtolower(trim($title), 'UTF-8');
        $slug = preg_replace('/[^a-z0-9\s-]/u', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
    }

    /**
     * Kembalikan label tampilan kategori.
     *
     * @param  string $category
     * @return string
     */
    public static function getCategoryLabel(string $category): string
    {
        return self::CATEGORY_LABELS[$category] ?? ucfirst($category);
    }

    /**
     * Format tanggal publish ke bahasa Indonesia.
     * Contoh: "14 Juli 2026"
     *
     * @param  string|null $datetime  Nilai dari kolom publish_date
     * @return string
     */
    public static function formatDate(?string $datetime): string
    {
        if (!$datetime) {
            return '';
        }

        $bulan = [
            1  => 'Januari',  2  => 'Februari', 3  => 'Maret',
            4  => 'April',    5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',     8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',  11 => 'November',  12 => 'Desember',
        ];

        $ts  = strtotime($datetime);
        $d   = (int) date('j', $ts);
        $m   = (int) date('n', $ts);
        $y   = date('Y', $ts);

        return "{$d} {$bulan[$m]} {$y}";
    }

    // ── Internal ──────────────────────────────────────────────

    /**
     * Simpan tag untuk artikel (batch INSERT IGNORE).
     *
     * @param  int   $articleId
     * @param  array $tags
     */
    private function saveTags(int $articleId, array $tags): void
    {
        foreach ($tags as $tagName) {
            $tagName = mb_strtolower(trim((string) $tagName), 'UTF-8');
            if ($tagName === '') {
                continue;
            }
            $this->db->execute(
                'INSERT IGNORE INTO bdt_article_tag (article_id, tag_name) VALUES (?, ?)',
                'is',
                [$articleId, $tagName]
            );
        }
    }
}
