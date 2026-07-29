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
    public function getFeaturedNews(string $category = ''): ?array
    {
        if ($category) {
            $sql = 'SELECT
                        a.article_id,
                        a.title,
                        a.slug,
                        a.excerpt,
                        a.body,
                        a.cover_image AS image,
                        a.category,
                        a.publish_date AS date,
                        u.name AS author,
                        u.role AS author_role,
                        l.name AS library_name
                     FROM bdt_article a
                     LEFT JOIN bdt_admins u ON a.created_by = u.id
                     LEFT JOIN bdt_library l ON a.library_id = l.library_id
                     WHERE a.status = "published"
                       AND a.category = ?
                       AND a.is_featured = 1
                       AND (a.publish_date IS NULL OR a.publish_date <= NOW())
                     ORDER BY a.publish_date DESC
                     LIMIT 1';
            return $this->db->fetchOne($sql, 's', [$category]);
        } else {
            $sql = 'SELECT
                        a.article_id,
                        a.title,
                        a.slug,
                        a.excerpt,
                        a.body,
                        a.cover_image AS image,
                        a.category,
                        a.publish_date AS date,
                        u.name AS author,
                        u.role AS author_role,
                        l.name AS library_name
                     FROM bdt_article a
                     LEFT JOIN bdt_admins u ON a.created_by = u.id
                     LEFT JOIN bdt_library l ON a.library_id = l.library_id
                     WHERE a.status = "published"
                       AND a.category IN ("berita", "kegiatan", "pengumuman")
                       AND a.is_featured = 1
                       AND (a.publish_date IS NULL OR a.publish_date <= NOW())
                     ORDER BY a.publish_date DESC
                     LIMIT 1';
            return $this->db->fetchOne($sql);
        }
    }

    /**
     * Ambil semua berita unggulan (featured news).
     *
     * @return array
     */
    public function getAllFeaturedNews(string $category = ''): array
    {
        if ($category) {
            $sql = 'SELECT
                        a.article_id,
                        a.title,
                        a.slug,
                        a.excerpt,
                        a.body,
                        a.cover_image AS image,
                        a.category,
                        a.publish_date AS date,
                        u.name AS author,
                        u.role AS author_role,
                        l.name AS library_name
                     FROM bdt_article a
                     LEFT JOIN bdt_admins u ON a.created_by = u.id
                     LEFT JOIN bdt_library l ON a.library_id = l.library_id
                     WHERE a.status = "published"
                       AND a.category = ?
                       AND a.is_featured = 1
                       AND (a.publish_date IS NULL OR a.publish_date <= NOW())
                     ORDER BY a.publish_date DESC';
            return $this->db->fetchAll($sql, 's', [$category]);
        } else {
            $sql = 'SELECT
                        a.article_id,
                        a.title,
                        a.slug,
                        a.excerpt,
                        a.body,
                        a.cover_image AS image,
                        a.category,
                        a.publish_date AS date,
                        u.name AS author,
                        u.role AS author_role,
                        l.name AS library_name
                     FROM bdt_article a
                     LEFT JOIN bdt_admins u ON a.created_by = u.id
                     LEFT JOIN bdt_library l ON a.library_id = l.library_id
                     WHERE a.status = "published"
                       AND a.category IN ("berita", "kegiatan", "pengumuman")
                       AND a.is_featured = 1
                       AND (a.publish_date IS NULL OR a.publish_date <= NOW())
                     ORDER BY a.publish_date DESC';
            return $this->db->fetchAll($sql);
        }
    }

    /**
     * Hitung total berita yang dipublish (untuk pagination).
     *
     * @return int
     */
    public function countNews(string $category = ''): int
    {
        if ($category) {
            $sql = 'SELECT COUNT(*) 
                    FROM bdt_article 
                    WHERE status = "published" 
                      AND category = ?
                      AND (publish_date IS NULL OR publish_date <= NOW())';
            return (int) $this->db->fetchScalar($sql, 's', [$category]);
        } else {
            $sql = 'SELECT COUNT(*) 
                    FROM bdt_article 
                    WHERE status = "published" 
                      AND category IN ("berita", "kegiatan", "pengumuman")
                      AND (publish_date IS NULL OR publish_date <= NOW())';
            return (int) $this->db->fetchScalar($sql);
        }
    }

    /**
     * Ambil daftar berita terbaru.
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getRecentNews(int $limit = 9, int $offset = 0, string $category = '', array $excludeIds = []): array
    {
        $excludeCondition = '';
        $params = [];
        $types = '';

        if (!empty($excludeIds)) {
            $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
            $excludeCondition = " AND a.article_id NOT IN ($placeholders)";
            foreach ($excludeIds as $id) {
                $params[] = $id;
                $types .= 'i';
            }
        }

        if ($category) {
            array_unshift($params, $category);
            $types = 's' . $types;
            $params[] = $limit;
            $params[] = $offset;
            $types .= 'ii';

            $sql = 'SELECT
                        a.article_id,
                        a.title,
                        a.slug,
                        a.excerpt,
                        a.body,
                        a.cover_image AS image,
                        a.category,
                        a.is_featured,
                        a.publish_date AS date,
                        u.name AS author,
                        u.role AS author_role,
                        l.name AS library_name
                    FROM bdt_article a
                    LEFT JOIN bdt_admins u ON a.created_by = u.id
                    LEFT JOIN bdt_library l ON a.library_id = l.library_id
                    WHERE a.status = "published"
                      AND a.category = ?' . $excludeCondition . '
                      AND (a.publish_date IS NULL OR a.publish_date <= NOW())
                    ORDER BY a.publish_date DESC
                    LIMIT ? OFFSET ?';
            return $this->db->fetchAll($sql, $types, $params);
        } else {
            $params[] = $limit;
            $params[] = $offset;
            $types .= 'ii';

            $sql = 'SELECT
                        a.article_id,
                        a.title,
                        a.slug,
                        a.excerpt,
                        a.body,
                        a.cover_image AS image,
                        a.category,
                        a.is_featured,
                        a.publish_date AS date,
                        u.name AS author,
                        u.role AS author_role,
                        l.name AS library_name
                    FROM bdt_article a
                    LEFT JOIN bdt_admins u ON a.created_by = u.id
                    LEFT JOIN bdt_library l ON a.library_id = l.library_id
                    WHERE a.status = "published"
                      AND a.category IN ("berita", "kegiatan", "pengumuman")' . $excludeCondition . '
                      AND (a.publish_date IS NULL OR a.publish_date <= NOW())
                    ORDER BY a.publish_date DESC
                    LIMIT ? OFFSET ?';
            return $this->db->fetchAll($sql, $types, $params);
        }
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
                u.name AS author,
                u.role AS author_role,
                l.name AS library_name
             FROM bdt_article a
             LEFT JOIN bdt_admins u ON a.created_by = u.id
             LEFT JOIN bdt_library l ON a.library_id = l.library_id
             WHERE a.status = "published"
               AND a.category IN ("berita", "kegiatan", "pengumuman")
               AND a.slug = ?
               AND (a.publish_date IS NULL OR a.publish_date <= NOW())
             LIMIT 1',
            's',
            [$slug]
        );
    }
}
