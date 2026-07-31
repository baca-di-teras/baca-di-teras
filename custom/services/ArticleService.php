<?php
/**
 * Article Service
 *
 * File    : ArticleService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 */

require_once __DIR__ . '/../helpers/Database.php';
require_once __DIR__ . '/ActivityLogService.php';

class ArticleService
{
    private Database $db;
    private ActivityLogService $activityLog;

    public const CATEGORIES = [
        'berita',
        'kegiatan',
        'pengumuman',
        'resensi',
        'literasi',
        'lainnya',
    ];

    public const CATEGORY_LABELS = [
        'berita'     => 'Berita',
        'kegiatan'   => 'Kegiatan',
        'pengumuman' => 'Pengumuman',
        'resensi'    => 'Resensi Buku',
        'literasi'   => 'Artikel Literasi',
        'lainnya'    => 'Lainnya'
    ];

    public const ARTICLE_CATEGORIES = ['resensi', 'literasi', 'lainnya'];
    public const NEWS_CATEGORIES = ['berita', 'kegiatan', 'pengumuman'];

    public static function formatDate(?string $date): string
    {
        if (!$date) return '-';
        $time = strtotime($date);
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $d = date('d', $time);
        $m = $months[date('n', $time) - 1];
        $y = date('Y', $time);
        return "$d $m $y";
    }

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->activityLog = new ActivityLogService();
    }

    public function getAllArticles(): array
    {
        // Join dengan tabel user untuk mengambil author, dan join bdt_article_tag
        $sql = "SELECT a.*, 
                (SELECT GROUP_CONCAT(tag_name SEPARATOR ',') FROM bdt_article_tag t WHERE t.article_id = a.article_id) as tags
                FROM bdt_article a 
                ORDER BY a.created_at DESC";
                
        $data = $this->db->fetchAll($sql);
        
        foreach ($data as &$row) {
            $row['tags'] = $row['tags'] ? explode(',', $row['tags']) : [];
        }
        
        return $data;
    }

    public function getAdminArticles(array $filters = [], int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT a.*, 
                (SELECT GROUP_CONCAT(tag_name SEPARATOR ',') FROM bdt_article_tag t WHERE t.article_id = a.article_id) as tags
                FROM bdt_article a 
                WHERE 1=1";
        $types = "";
        $params = [];

        if (!empty($filters['category'])) {
            $sql .= " AND a.category = ?";
            $types .= "s";
            $params[] = $filters['category'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $types .= "s";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (a.title LIKE ? OR a.body LIKE ? OR a.excerpt LIKE ?)";
            $types .= "sss";
            $searchStr = '%' . $filters['search'] . '%';
            $params[] = $searchStr;
            $params[] = $searchStr;
            $params[] = $searchStr;
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;

        $data = $this->db->fetchAll($sql, $types, $params);
        foreach ($data as &$row) {
            $row['tags'] = $row['tags'] ? explode(',', $row['tags']) : [];
        }
        return $data;
    }

    public function countAdminArticles(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM bdt_article WHERE 1=1";
        $types = "";
        $params = [];

        if (!empty($filters['category'])) {
            $sql .= " AND category = ?";
            $types .= "s";
            $params[] = $filters['category'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $types .= "s";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (title LIKE ? OR body LIKE ? OR excerpt LIKE ?)";
            $types .= "sss";
            $searchStr = '%' . $filters['search'] . '%';
            $params[] = $searchStr;
            $params[] = $searchStr;
            $params[] = $searchStr;
        }

        if (empty($params)) {
            return (int) $this->db->fetchScalar($sql);
        } else {
            return (int) $this->db->fetchScalar($sql, $types, $params);
        }
    }

    public function countPublished(string $category = ''): int
    {
        if ($category) {
            $sql = "SELECT COUNT(*) FROM bdt_article WHERE status = 'published' AND category = ?";
            return (int) $this->db->fetchScalar($sql, 's', [$category]);
        } else {
            $sql = "SELECT COUNT(*) FROM bdt_article WHERE status = 'published' AND category IN ('resensi', 'literasi', 'lainnya')";
            return (int) $this->db->fetchScalar($sql);
        }
    }

    public function getPublished(int $limit = 9, int $offset = 0, int $excludeId = 0): array
    {
        $sql = "SELECT a.*, l.name AS library_name, u.name AS author_name, u.role AS author_role 
                FROM bdt_article a 
                LEFT JOIN bdt_library l ON a.library_id = l.library_id 
                LEFT JOIN bdt_admins u ON a.created_by = u.id 
                WHERE a.status = 'published' 
                AND a.category IN ('resensi', 'literasi', 'lainnya')
                AND a.article_id != ?
                ORDER BY a.is_pinned DESC, a.publish_date DESC LIMIT ? OFFSET ?";
        return $this->db->fetchAll($sql, 'iii', [$excludeId, $limit, $offset]);
    }

    public function getByCategory(string $category, int $limit = 9, int $offset = 0, int $excludeId = 0): array
    {
        $sql = "SELECT a.*, l.name AS library_name, u.name AS author_name, u.role AS author_role 
                FROM bdt_article a 
                LEFT JOIN bdt_library l ON a.library_id = l.library_id 
                LEFT JOIN bdt_admins u ON a.created_by = u.id 
                WHERE a.status = 'published' AND a.category = ? 
                AND a.article_id != ?
                ORDER BY a.is_pinned DESC, a.publish_date DESC LIMIT ? OFFSET ?";
        return $this->db->fetchAll($sql, 'siii', [$category, $excludeId, $limit, $offset]);
    }

    public function getHeroArticle(string $category = ''): ?array
    {
        if ($category) {
            $sql = "SELECT a.*, l.name AS library_name, u.name AS author_name, u.role AS author_role 
                    FROM bdt_article a 
                    LEFT JOIN bdt_library l ON a.library_id = l.library_id 
                    LEFT JOIN bdt_admins u ON a.created_by = u.id 
                    WHERE a.status = 'published' AND a.category = ? AND a.is_featured = 1 
                    ORDER BY a.publish_date DESC LIMIT 1";
            return $this->db->fetchOne($sql, 's', [$category]);
        } else {
            $sql = "SELECT a.*, l.name AS library_name, u.name AS author_name, u.role AS author_role 
                    FROM bdt_article a 
                    LEFT JOIN bdt_library l ON a.library_id = l.library_id 
                    LEFT JOIN bdt_admins u ON a.created_by = u.id 
                    WHERE a.status = 'published' AND a.is_featured = 1 
                    AND a.category IN ('resensi', 'literasi', 'lainnya')
                    ORDER BY a.publish_date DESC LIMIT 1";
            return $this->db->fetchOne($sql);
        }
    }

    public function getArticleById(int $article_id): ?array
    {
        $sql = "SELECT a.*, u.name AS author_name, u.role AS author_role, 
                (SELECT GROUP_CONCAT(tag_name SEPARATOR ',') FROM bdt_article_tag t WHERE t.article_id = a.article_id) as tags
                FROM bdt_article a 
                LEFT JOIN bdt_admins u ON a.created_by = u.id
                WHERE a.article_id = ? LIMIT 1";
                
        $stmt = $this->db->getConnection()->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param('i', $article_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $article = $result->fetch_assoc();
        $stmt->close();

        if ($article) {
            $article['tags'] = $article['tags'] ? explode(',', $article['tags']) : [];
        }

        return $article;
    }

    public function createArticle(array $data): bool
    {
        $this->db->getConnection()->begin_transaction();

        try {
            $sql = "INSERT INTO bdt_article (title, slug, excerpt, body, cover_image, category, status, publish_date, is_featured, is_pinned, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->getConnection()->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed");

            $title = $data['title'];
            $slug = $data['slug'] ?? $this->generateSlug($title);
            $excerpt = $data['excerpt'] ?? '';
            $body = $data['body'] ?? '';
            $cover_image = $data['cover_image'] ?? null;
            $category = $data['category'] ?? 'berita';
            $status = $data['status'] ?? 'draft';
            $publish_date = $data['publish_date'] ?? date('Y-m-d H:i:s');
            $is_featured = $data['is_featured'] ?? 0;
            $is_pinned = $data['is_pinned'] ?? 0;
            $created_by = $data['created_by'] ?? null;

            $stmt->bind_param('ssssssssiii', $title, $slug, $excerpt, $body, $cover_image, $category, $status, $publish_date, $is_featured, $is_pinned, $created_by);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $article_id = $stmt->insert_id;
            $stmt->close();

            // Insert tags
            if (!empty($data['tags']) && is_array($data['tags'])) {
                $this->syncTags($article_id, $data['tags']);
            }

            $this->db->getConnection()->commit();

            $this->activityLog->log('menambahkan', 'Artikel', $title);

            return true;
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            error_log("ArticleService::createArticle Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateArticle(int $article_id, array $data): bool
    {
        $this->db->getConnection()->begin_transaction();

        try {
            $sql = "UPDATE bdt_article 
                    SET title = ?, slug = ?, excerpt = ?, body = ?, cover_image = ?, category = ?, status = ?, publish_date = ?, is_featured = ?, is_pinned = ? 
                    WHERE article_id = ?";
            
            $stmt = $this->db->getConnection()->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed");

            $title = $data['title'];
            $slug = $data['slug'] ?? $this->generateSlug($title);
            $excerpt = $data['excerpt'] ?? '';
            $body = $data['body'] ?? '';
            $cover_image = $data['cover_image'] ?? null;
            $category = $data['category'] ?? 'berita';
            $status = $data['status'] ?? 'draft';
            $publish_date = $data['publish_date'] ?? date('Y-m-d H:i:s');
            $is_featured = $data['is_featured'] ?? 0;
            $is_pinned = $data['is_pinned'] ?? 0;

            $stmt->bind_param('ssssssssiii', $title, $slug, $excerpt, $body, $cover_image, $category, $status, $publish_date, $is_featured, $is_pinned, $article_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            $stmt->close();

            // Sync tags
            if (isset($data['tags']) && is_array($data['tags'])) {
                $this->syncTags($article_id, $data['tags']);
            }

            $this->db->getConnection()->commit();

            $this->activityLog->log('mengubah', 'Artikel', $title);

            return true;
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            error_log("ArticleService::updateArticle Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteArticle(int $article_id): bool
    {
        // Ambil judul artikel sebelum dihapus untuk keperluan log
        $article = $this->getArticleById($article_id);
        $title = $article ? $article['title'] : "ID: $article_id";

        $sql = "DELETE FROM bdt_article WHERE article_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('i', $article_id);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            $this->activityLog->log('menghapus', 'Artikel', $title);
        }

        return $result;
    }

    private function syncTags(int $article_id, array $tags): void
    {
        // Delete old tags
        $stmtDel = $this->db->getConnection()->prepare("DELETE FROM bdt_article_tag WHERE article_id = ?");
        $stmtDel->bind_param('i', $article_id);
        $stmtDel->execute();
        $stmtDel->close();

        // Insert new tags
        if (empty($tags)) return;
        
        $stmtIns = $this->db->getConnection()->prepare("INSERT IGNORE INTO bdt_article_tag (article_id, tag_name) VALUES (?, ?)");
        foreach ($tags as $tag) {
            $t = trim($tag);
            if ($t !== '') {
                $stmtIns->bind_param('is', $article_id, $t);
                $stmtIns->execute();
            }
        }
        $stmtIns->close();
    }

    private function generateSlug(string $title): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = preg_replace('/-+/', '-', $slug);
        return $slug;
    }

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
            's',
            [$slug]
        );
    }

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
            'i',
            [$limit]
        );
    }

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
            'ii',
            [$days, $limit]
        );
    }

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
            'isii',
            [$articleId, $category, $libraryId ?: 0, $limit]
        );
    }

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
            'sssi',
            [$likeQuery, $likeQuery, $likeQuery, $limit]
        );
    }

    public function getTags(int $articleId): array
    {
        $rows = $this->db->fetchAll(
            'SELECT tag_name FROM bdt_article_tag WHERE article_id = ? ORDER BY tag_name',
            'i',
            [$articleId]
        );

        return array_column($rows, 'tag_name');
    }

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
            'si',
            [$tagName, $limit]
        );
    }

    public function recordView(int $articleId): void
    {
        $this->db->execute(
            'INSERT INTO bdt_article_view (article_id, view_date, view_count)
             VALUES (?, CURDATE(), 1)
             ON DUPLICATE KEY UPDATE view_count = view_count + 1',
            'i',
            [$articleId]
        );

        $this->db->execute(
            'UPDATE bdt_article SET view_count = view_count + 1 WHERE article_id = ?',
            'i',
            [$articleId]
        );
    }

    public function getViewStats(int $articleId, int $days = 30): array
    {
        return $this->db->fetchAll(
            'SELECT view_date, view_count
               FROM bdt_article_view
              WHERE article_id = ?
                AND view_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
              ORDER BY view_date ASC',
            'ii',
            [$articleId, $days]
        );
    }

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
            'i',
            [$limit]
        );
    }
}

