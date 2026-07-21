<?php
/**
 * News Service
 *
 * File    : NewsService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Mengelola data berita (kategori 'berita' pada tabel bdt_article).
 * Merupakan wrapper khusus agar pemisahan logika berita dan artikel umum
 * lebih jelas, sesuai arsitektur yang diminta.
 */

require_once __DIR__ . '/../helpers/Database.php';

class NewsService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil berita unggulan (featured news).
     * Biasanya ditampilkan di posisi paling atas/utama.
     *
     * @return array|null
     */
    public function getFeaturedNews(): ?array
    {
        return $this->db->fetchOne(
            'SELECT
                a.article_id,
                a.title,
                a.slug,
                a.excerpt,
                a.cover_image AS image,
                a.category,
                a.publish_date AS date,
                u.realname AS author,
                l.name AS library_name
             FROM bdt_article a
             LEFT JOIN user u ON a.created_by = u.user_id
             LEFT JOIN bdt_library l ON a.library_id = l.library_id
             WHERE a.status = "published"
               AND a.category = "berita"
               AND a.is_featured = 1
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             ORDER BY a.publish_date DESC
             LIMIT 1'
        );
    }

    /**
     * Ambil daftar berita terbaru.
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getRecentNews(int $limit = 5, int $offset = 0): array
    {
        return $this->db->fetchAll(
            'SELECT
                a.article_id,
                a.title,
                a.slug,
                a.excerpt,
                a.cover_image AS image,
                a.category,
                a.publish_date AS date,
                u.realname AS author,
                l.name AS library_name
             FROM bdt_article a
             LEFT JOIN user u ON a.created_by = u.user_id
             LEFT JOIN bdt_library l ON a.library_id = l.library_id
             WHERE a.status = "published"
               AND a.category = "berita"
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             ORDER BY a.publish_date DESC
             LIMIT ? OFFSET ?',
            'ii',
            [$limit, $offset]
        );
    }

    /**
     * Ambil detail berita berdasarkan slug.
     *
     * @param string $slug
     * @return array|null
     */
    public function getNewsBySlug(string $slug): ?array
    {
        return $this->db->fetchOne(
            'SELECT
                a.*,
                u.realname AS author,
                l.name AS library_name
             FROM bdt_article a
             LEFT JOIN user u ON a.created_by = u.user_id
             LEFT JOIN bdt_library l ON a.library_id = l.library_id
             WHERE a.status = "published"
               AND a.category = "berita"
               AND a.slug = ?
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             LIMIT 1',
            's',
            [$slug]
        );
    }
}
