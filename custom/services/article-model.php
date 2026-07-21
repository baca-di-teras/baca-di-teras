<?php
/**
 * Article Model
 *
 * File    : article-model.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Model untuk tabel bdt_article, bdt_article_tag, bdt_article_view.
 *
 * Juga mengintegrasikan query ke tabel SLiMS:
 *   - user   (penulis artikel = pustakawan SLiMS)
 *   - biblio (resensi buku)
 *
 * Usage:
 *   require_once __DIR__ . '/../helpers/database.php';
 *   require_once __DIR__ . '/../services/article-model.php';
 *
 *   $articleModel = new ArticleModel();
 *   $articles     = $articleModel->getPublished(6);
 *   $article      = $articleModel->getBySlug('festival-baca-2026');
 */

require_once __DIR__ . '/../helpers/database.php';

class ArticleModel
{
    private Database $db;

    /** Kategori valid sesuai schema bdt_article */
    public const CATEGORIES = [
        'berita',
        'kegiatan',
        'pengumuman',
        'resensi',
        'literasi',
        'lainnya',
    ];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ============================================================
    // QUERY ARTIKEL
    // ============================================================

    /**
     * Ambil artikel published, diurutkan terbaru.
     *
     * @param  int   $limit
     * @param  int   $offset
     * @return array
     */
    public function getPublished(int $limit = 10, int $offset = 0): array
    {
        return $this->db->fetchAll(
            'SELECT a.*,
                    l.name AS library_name,
                    l.slug AS library_slug
               FROM bdt_article a
               LEFT JOIN bdt_library l ON a.library_id = l.library_id
              WHERE a.status       = "published"
                AND (a.publish_date IS NULL OR a.publish_date <= NOW())
              ORDER BY a.is_pinned DESC, a.publish_date DESC
              LIMIT ? OFFSET ?',
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
        if ($category && in_array($category, self::CATEGORIES)) {
            $count = $this->db->fetchScalar(
                'SELECT COUNT(*) FROM bdt_article
                  WHERE status   = "published"
                    AND category = ?
                    AND (publish_date IS NULL OR publish_date <= NOW())',
                [$category]
            );
        } else {
            $count = $this->db->fetchScalar(
                'SELECT COUNT(*) FROM bdt_article
                  WHERE status = "published"
                    AND (publish_date IS NULL OR publish_date <= NOW())'
            );
        }
        return (int) $count;
    }

    /**
     * Ambil artikel berdasarkan slug.
     *
     * @param  string     $slug
     * @return array|false
     */
    public function getBySlug(string $slug): array|false
    {
        return $this->db->fetchOne(
            'SELECT a.*,
                    l.name AS library_name,
                    l.slug AS library_slug,
                    u.realname AS author_name
               FROM bdt_article a
               LEFT JOIN bdt_library l ON a.library_id  = l.library_id
               LEFT JOIN user        u ON a.created_by  = u.user_id
              WHERE a.slug   = ?
                AND a.status = "published"
              LIMIT 1',
            [$slug]
        );
    }

    /**
     * Ambil artikel berdasarkan ID.
     *
     * @param  int        $articleId
     * @return array|false
     */
    public function getById(int $articleId): array|false
    {
        return $this->db->fetchOne(
            'SELECT a.*,
                    l.name AS library_name,
                    u.realname AS author_name
               FROM bdt_article a
               LEFT JOIN bdt_library l ON a.library_id = l.library_id
               LEFT JOIN user        u ON a.created_by = u.user_id
              WHERE a.article_id = ?
              LIMIT 1',
            [$articleId]
        );
    }

    /**
     * Ambil artikel unggulan (is_featured = 1).
     *
     * @param  int   $limit
     * @return array
     */
    public function getFeatured(int $limit = 3): array
    {
        return $this->db->fetchAll(
            'SELECT a.*,
                    l.name AS library_name,
                    l.slug AS library_slug
               FROM bdt_article a
               LEFT JOIN bdt_library l ON a.library_id = l.library_id
              WHERE a.status      = "published"
                AND a.is_featured = 1
                AND (a.publish_date IS NULL OR a.publish_date <= NOW())
              ORDER BY a.publish_date DESC
              LIMIT ?',
            [$limit]
        );
    }

    /**
     * Ambil artikel terbaru.
     *
     * @param  int   $limit
     * @return array
     */
    public function getRecent(int $limit = 5): array
    {
        return $this->db->fetchAll(
            'SELECT a.*,
                    l.name AS library_name
               FROM bdt_article a
               LEFT JOIN bdt_library l ON a.library_id = l.library_id
              WHERE a.status = "published"
                AND (a.publish_date IS NULL OR a.publish_date <= NOW())
              ORDER BY a.publish_date DESC
              LIMIT ?',
            [$limit]
        );
    }

    /**
     * Ambil artikel terpopuler berdasarkan jumlah pembacaan.
     *
     * @param  int $limit
     * @param  int $days   Rentang hari terakhir (default: 30 hari)
     * @return array
     */
    public function getPopular(int $limit = 5, int $days = 30): array
    {
        return $this->db->fetchAll(
            'SELECT a.*,
                    l.name AS library_name,
                    COALESCE(SUM(v.view_count), 0) AS total_views
               FROM bdt_article a
               LEFT JOIN bdt_library     l ON a.library_id  = l.library_id
               LEFT JOIN bdt_article_view v ON a.article_id = v.article_id
                                           AND v.view_date  >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
              WHERE a.status = "published"
                AND (a.publish_date IS NULL OR a.publish_date <= NOW())
              GROUP BY a.article_id
              ORDER BY total_views DESC, a.publish_date DESC
              LIMIT ?',
            [$days, $limit]
        );
    }

    /**
     * Ambil artikel berdasarkan kategori.
     *
     * @param  string $category  Nilai dari self::CATEGORIES
     * @param  int    $limit
     * @param  int    $offset
     * @return array
     */
    public function getByCategory(string $category, int $limit = 10, int $offset = 0): array
    {
        if (!in_array($category, self::CATEGORIES)) {
            return [];
        }

        return $this->db->fetchAll(
            'SELECT a.*,
                    l.name AS library_name
               FROM bdt_article a
               LEFT JOIN bdt_library l ON a.library_id = l.library_id
              WHERE a.status   = "published"
                AND a.category = ?
                AND (a.publish_date IS NULL OR a.publish_date <= NOW())
              ORDER BY a.is_pinned DESC, a.publish_date DESC
              LIMIT ? OFFSET ?',
            [$category, $limit, $offset]
        );
    }

    /**
     * Ambil artikel milik perpustakaan tertentu.
     *
     * @param  int   $libraryId
     * @param  int   $limit
     * @param  int   $offset
     * @return array
     */
    public function getByLibrary(int $libraryId, int $limit = 10, int $offset = 0): array
    {
        return $this->db->fetchAll(
            'SELECT a.*,
                    l.name AS library_name
               FROM bdt_article a
               LEFT JOIN bdt_library l ON a.library_id = l.library_id
              WHERE a.status     = "published"
                AND a.library_id = ?
                AND (a.publish_date IS NULL OR a.publish_date <= NOW())
              ORDER BY a.publish_date DESC
              LIMIT ? OFFSET ?',
            [$libraryId, $limit, $offset]
        );
    }

    /**
     * Ambil artikel terkait berdasarkan:
     *   1. Kategori yang sama, atau
     *   2. Library yang sama.
     *
     * @param  int    $articleId   Artikel saat ini (dikecualikan)
     * @param  string $category
     * @param  int    $libraryId   0 jika tidak ada relasi perpustakaan
     * @param  int    $limit
     * @return array
     */
    public function getRelated(
        int    $articleId,
        string $category,
        int    $libraryId = 0,
        int    $limit     = 4
    ): array {
        return $this->db->fetchAll(
            'SELECT a.*,
                    l.name AS library_name
               FROM bdt_article a
               LEFT JOIN bdt_library l ON a.library_id = l.library_id
              WHERE a.status     = "published"
                AND a.article_id != ?
                AND (a.category   = ? OR a.library_id = ?)
                AND (a.publish_date IS NULL OR a.publish_date <= NOW())
              ORDER BY a.publish_date DESC
              LIMIT ?',
            [$articleId, $category, $libraryId ?: 0, $limit]
        );
    }

    /**
     * Pencarian fulltext artikel (judul, isi, ringkasan).
     *
     * @param  string $query   Kata kunci
     * @param  int    $limit
     * @return array
     */
    public function search(string $query, int $limit = 10): array
    {
        $likeQuery = '%' . $query . '%';

        return $this->db->fetchAll(
            'SELECT a.*,
                    l.name AS library_name
               FROM bdt_article a
               LEFT JOIN bdt_library l ON a.library_id = l.library_id
              WHERE a.status = "published"
                AND (a.publish_date IS NULL OR a.publish_date <= NOW())
                AND (
                    a.title   LIKE ? OR
                    a.body    LIKE ? OR
                    a.excerpt LIKE ?
                )
              ORDER BY a.publish_date DESC
              LIMIT ?',
            [$likeQuery, $likeQuery, $likeQuery, $limit]
        );
    }

    // ============================================================
    // TAG
    // ============================================================

    /**
     * Ambil semua tag suatu artikel.
     *
     * @param  int   $articleId
     * @return array Array string tag
     */
    public function getTags(int $articleId): array
    {
        $rows = $this->db->fetchAll(
            'SELECT tag_name FROM bdt_article_tag WHERE article_id = ? ORDER BY tag_name',
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
            'SELECT a.*,
                    l.name AS library_name
               FROM bdt_article a
               JOIN bdt_article_tag  t ON a.article_id  = t.article_id
               LEFT JOIN bdt_library l ON a.library_id  = l.library_id
              WHERE a.status   = "published"
                AND t.tag_name = ?
                AND (a.publish_date IS NULL OR a.publish_date <= NOW())
              ORDER BY a.publish_date DESC
              LIMIT ?',
            [$tagName, $limit]
        );
    }

    // ============================================================
    // STATISTIK PEMBACAAN
    // ============================================================

    /**
     * Catat satu pembacaan artikel.
     * Menggunakan INSERT ... ON DUPLICATE KEY UPDATE agar
     * tidak ada duplikasi per hari.
     *
     * @param  int $articleId
     * @return void
     */
    public function recordView(int $articleId): void
    {
        // Tambah view_count di bdt_article_view (per hari)
        $this->db->execute(
            'INSERT INTO bdt_article_view (article_id, view_date, view_count)
             VALUES (?, CURDATE(), 1)
             ON DUPLICATE KEY UPDATE view_count = view_count + 1',
            [$articleId]
        );

        // Tambah view_count total di bdt_article (denormalized, cepat)
        $this->db->execute(
            'UPDATE bdt_article SET view_count = view_count + 1 WHERE article_id = ?',
            [$articleId]
        );
    }

    /**
     * Ambil statistik pembacaan artikel per hari dalam rentang tertentu.
     *
     * @param  int $articleId
     * @param  int $days
     * @return array  [ ['view_date' => ..., 'view_count' => ...], ... ]
     */
    public function getViewStats(int $articleId, int $days = 30): array
    {
        return $this->db->fetchAll(
            'SELECT view_date, view_count
               FROM bdt_article_view
              WHERE article_id = ?
                AND view_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
              ORDER BY view_date ASC',
            [$articleId, $days]
        );
    }

    // ============================================================
    // INTEGRASI SLIMS — Resensi Buku
    // ============================================================

    /**
     * Ambil artikel resensi beserta data buku dari SLiMS (biblio).
     *
     * @param  int   $limit
     * @return array
     */
    public function getReviews(int $limit = 5): array
    {
        return $this->db->fetchAll(
            'SELECT a.*,
                    b.title       AS biblio_title,
                    b.isbn_issn   AS biblio_isbn,
                    b.image       AS biblio_image,
                    l.name        AS library_name
               FROM bdt_article a
               LEFT JOIN biblio      b ON a.biblio_id   = b.biblio_id
               LEFT JOIN bdt_library l ON a.library_id  = l.library_id
              WHERE a.status   = "published"
                AND a.category = "resensi"
                AND (a.publish_date IS NULL OR a.publish_date <= NOW())
              ORDER BY a.publish_date DESC
              LIMIT ?',
            [$limit]
        );
    }

    // ============================================================
    // WRITE — Insert / Update
    // ============================================================

    /**
     * Buat artikel baru.
     *
     * @param  array $data  Array field artikel
     * @return int   article_id baru
     */
    public function create(array $data): int
    {
        $this->db->execute(
            'INSERT INTO bdt_article
                (title, slug, excerpt, body, cover_image, category,
                 status, publish_date, is_featured, is_pinned,
                 created_by, library_id, biblio_id,
                 meta_description, meta_keywords)
             VALUES
                (:title, :slug, :excerpt, :body, :cover_image, :category,
                 :status, :publish_date, :is_featured, :is_pinned,
                 :created_by, :library_id, :biblio_id,
                 :meta_description, :meta_keywords)',
            [
                ':title'           => $data['title']           ?? '',
                ':slug'            => $data['slug']            ?? '',
                ':excerpt'         => $data['excerpt']         ?? null,
                ':body'            => $data['body']            ?? '',
                ':cover_image'     => $data['cover_image']     ?? null,
                ':category'        => $data['category']        ?? 'berita',
                ':status'          => $data['status']          ?? 'draft',
                ':publish_date'    => $data['publish_date']    ?? null,
                ':is_featured'     => $data['is_featured']     ?? 0,
                ':is_pinned'       => $data['is_pinned']       ?? 0,
                ':created_by'      => $data['created_by']      ?? null,
                ':library_id'      => $data['library_id']      ?? null,
                ':biblio_id'       => $data['biblio_id']       ?? null,
                ':meta_description'=> $data['meta_description']?? null,
                ':meta_keywords'   => $data['meta_keywords']   ?? null,
            ]
        );

        $articleId = (int) $this->db->lastInsertId();

        // Simpan tag jika ada
        if (!empty($data['tags']) && is_array($data['tags'])) {
            $this->saveTags($articleId, $data['tags']);
        }

        return $articleId;
    }

    /**
     * Update artikel.
     *
     * @param  int   $articleId
     * @param  array $data
     * @return int   Baris terpengaruh
     */
    public function update(int $articleId, array $data): int
    {
        $allowedFields = [
            'title', 'slug', 'excerpt', 'body', 'cover_image', 'category',
            'status', 'publish_date', 'is_featured', 'is_pinned',
            'updated_by', 'library_id', 'biblio_id',
            'meta_description', 'meta_keywords',
        ];

        $setClauses = [];
        $params     = [];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $setClauses[] = "`{$field}` = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($setClauses)) {
            return 0;
        }

        $setClauses[]       = '`updated_at` = NOW()';
        $params[':article_id'] = $articleId;

        $sql = 'UPDATE bdt_article SET ' . implode(', ', $setClauses) . ' WHERE article_id = :article_id';

        $affected = $this->db->execute($sql, $params);

        // Update tag jika disertakan
        if (array_key_exists('tags', $data)) {
            $this->db->execute('DELETE FROM bdt_article_tag WHERE article_id = ?', [$articleId]);
            if (!empty($data['tags'])) {
                $this->saveTags($articleId, $data['tags']);
            }
        }

        return $affected;
    }

    /**
     * Publish artikel (ubah status menjadi published).
     *
     * @param  int $articleId
     * @return int
     */
    public function publish(int $articleId): int
    {
        return $this->db->execute(
            'UPDATE bdt_article
                SET status       = "published",
                    publish_date = IF(publish_date IS NULL OR publish_date > NOW(), NOW(), publish_date),
                    updated_at   = NOW()
              WHERE article_id   = ?',
            [$articleId]
        );
    }

    /**
     * Arsipkan artikel (ubah status menjadi archived).
     *
     * @param  int $articleId
     * @return int
     */
    public function archive(int $articleId): int
    {
        return $this->db->execute(
            'UPDATE bdt_article
                SET status     = "archived",
                    updated_at = NOW()
              WHERE article_id = ?',
            [$articleId]
        );
    }

    // ============================================================
    // HELPER INTERNAL
    // ============================================================

    /**
     * Simpan tag untuk artikel (INSERT batch).
     *
     * @param  int   $articleId
     * @param  array $tags
     * @return void
     */
    private function saveTags(int $articleId, array $tags): void
    {
        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if ($tagName === '') {
                continue;
            }
            $this->db->execute(
                'INSERT IGNORE INTO bdt_article_tag (article_id, tag_name) VALUES (?, ?)',
                [$articleId, mb_strtolower($tagName)]
            );
        }
    }

    /**
     * Generate slug dari judul artikel.
     * Cocok untuk insert otomatis.
     *
     * @param  string $title
     * @return string
     */
    public static function generateSlug(string $title): string
    {
        // Konversi ke lowercase & ganti spasi ke dash
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
    }
}
