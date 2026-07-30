<?php
/**
 * PathfinderV2Service
 *
 * File    : PathfinderV2Service.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Service layer untuk tabel-tabel Pathfinder baru:
 *   pathfinder_categories, pathfinder_topics,
 *   pathfinder_topic_introductions, pathfinder_topic_mapping,
 *   pathfinder_guides, pathfinder_downloads,
 *   pathfinder_external_resources, pathfinder_related_books
 */

require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/ActivityLogService.php';

class PathfinderV2Service
{
    private Database $db;
    private ActivityLogService $activityLog;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->activityLog = new ActivityLogService();
    }

    // ── Kategori ──────────────────────────────────────────────

    /**
     * Semua kategori aktif, urut sort_order.
     */
    public function getCategories(): array
    {
        $sql = 'SELECT id, name, slug, description, icon, sort_order
                FROM pathfinder_categories
                WHERE status = "active"
                ORDER BY sort_order ASC';
        return $this->db->fetchAll($sql) ?? [];
    }

    /**
     * Satu kategori berdasarkan slug.
     */
    public function getCategoryBySlug(string $slug): ?array
    {
        $sql = 'SELECT id, name, slug, description, icon
                FROM pathfinder_categories
                WHERE slug = ? AND status = "active"';
        return $this->db->fetchOne($sql, 's', [$slug]);
    }

    /**
     * Satu kategori berdasarkan ID.
     */
    public function getCategoryById(int $id): ?array
    {
        $sql = 'SELECT id, name, slug, description, icon
                FROM pathfinder_categories
                WHERE id = ? AND status = "active"';
        return $this->db->fetchOne($sql, 'i', [$id]);
    }

    // ── Topik ─────────────────────────────────────────────────

    /**
     * Semua topik dalam satu kategori (by slug), urut sort_order.
     */
    public function getTopicsByCategory(string $categorySlug): array
    {
        $sql = 'SELECT t.id, t.name, t.slug, t.description, t.icon, t.banner_image, t.sort_order
                FROM pathfinder_topics t
                JOIN pathfinder_categories c ON c.id = t.category_id
                WHERE c.slug = ? AND t.status = "active"
                ORDER BY t.sort_order ASC';
        return $this->db->fetchAll($sql, 's', [$categorySlug]) ?? [];
    }

    /**
     * Semua topik dalam satu kategori (by ID).
     */
    public function getTopicsByCategoryId(int $categoryId): array
    {
        $sql = 'SELECT id, name, slug, description, icon, banner_image, sort_order
                FROM pathfinder_topics
                WHERE category_id = ? AND status = "active"
                ORDER BY sort_order ASC';
        return $this->db->fetchAll($sql, 'i', [$categoryId]) ?? [];
    }

    public function getTopicById(int $topicId): ?array
    {
        $sql = 'SELECT * FROM pathfinder_topics WHERE id = ?';
        return $this->db->fetchOne($sql, 'i', [$topicId]) ?: null;
    }

    /**
     * Satu topik berdasarkan slug, beserta info kategorinya.
     */
    public function getTopicBySlug(string $slug): ?array
    {
        $sql = 'SELECT t.*, c.name AS category_name, c.slug AS category_slug
                FROM pathfinder_topics t
                JOIN pathfinder_categories c ON c.id = t.category_id
                WHERE t.slug = ? AND t.status = "active"';
        return $this->db->fetchOne($sql, 's', [$slug]);
    }

    /**
     * Mengambil semua topik tanpa filter status, lengkap dengan nama kategori.
     * Khusus untuk halaman portal admin.
     */
    public function getAllAdminTopics(): array
    {
        $sql = 'SELECT t.*, c.name AS category_name, c.slug AS category_slug
                FROM pathfinder_topics t
                LEFT JOIN pathfinder_categories c ON c.id = t.category_id
                ORDER BY c.sort_order ASC, t.sort_order ASC';
        return $this->db->fetchAll($sql) ?? [];
    }

    /**
     * Semua kategori beserta topik-topiknya (nested array).
     * Digunakan untuk render sidebar sekaligus.
     */
    public function getCategoriesWithTopics(): array
    {
        $categories = $this->getCategories();
        foreach ($categories as &$cat) {
            $cat['topics'] = $this->getTopicsByCategoryId((int) $cat['id']);
        }
        return $categories;
    }

    // ── Pendahuluan (Introduction) ────────────────────────────

    /**
     * Konten pendahuluan untuk satu topik.
     */
    public function getIntroduction(int $topicId): ?array
    {
        $sql = 'SELECT definition, learning_objectives, importance, topics_to_learn
                FROM pathfinder_topic_introductions
                WHERE topic_id = ?';
        return $this->db->fetchOne($sql, 'i', [$topicId]);
    }

    // ── Buku dari SLiMS via topic_mapping ─────────────────────

    /**
     * Daftar buku SLiMS yang terhubung ke topik Pathfinder
     * melalui pathfinder_topic_mapping → mst_topic → biblio_topic → biblio.
     */
    public function getBooksByTopicId(int $pathfinderTopicId, int $limit = 20): array
    {
        $sql = 'SELECT DISTINCT
                    b.biblio_id,
                    b.title,
                    b.sor,
                    b.notes,
                    b.call_number,
                    b.publish_year,
                    b.image,
                    b.isbn_issn,
                    mt.topic AS slims_topic,
                    (SELECT GROUP_CONCAT(ma.author_name SEPARATOR \'; \')
                     FROM biblio_author ba
                     JOIN mst_author ma ON ma.author_id = ba.author_id
                     WHERE ba.biblio_id = b.biblio_id) AS author_name,
                    (SELECT COUNT(*) FROM item i WHERE i.biblio_id = b.biblio_id) AS total_eksemplar
                FROM pathfinder_topic_mapping pm
                JOIN mst_topic mt  ON mt.topic_id  = pm.slims_topic_id
                JOIN biblio_topic bt ON bt.topic_id = mt.topic_id
                JOIN biblio b    ON b.biblio_id   = bt.biblio_id
                WHERE pm.pathfinder_topic_id = ?
                  AND b.opac_hide = 0
                ORDER BY b.title ASC
                LIMIT ?';
        return $this->db->fetchAll($sql, 'ii', [$pathfinderTopicId, $limit]) ?? [];
    }

    // ── Guides (Tips & Panduan) ───────────────────────────────

    public function getGuidesByTopicId(int $topicId): array
    {
        $sql = 'SELECT id, title, slug, thumbnail, sort_order
                FROM pathfinder_guides
                WHERE topic_id = ? AND status = "published"
                ORDER BY sort_order ASC';
        return $this->db->fetchAll($sql, 'i', [$topicId]) ?? [];
    }

    public function getGuideBySlug(string $slug): ?array
    {
        $sql = 'SELECT g.*, t.name AS topic_name, t.slug AS topic_slug,
                       c.name AS category_name, c.slug AS category_slug
                FROM pathfinder_guides g
                JOIN pathfinder_topics t ON t.id = g.topic_id
                JOIN pathfinder_categories c ON c.id = t.category_id
                WHERE g.slug = ? AND g.status = "published"';
        return $this->db->fetchOne($sql, 's', [$slug]);
    }

    // ── Downloads ────────────────────────────────────────────

    public function getDownloadsByTopicId(int $topicId): array
    {
        $sql = 'SELECT id, title, description, file_path, file_type, file_size, download_count
                FROM pathfinder_downloads
                WHERE topic_id = ? AND status = "active"
                ORDER BY id ASC';
        return $this->db->fetchAll($sql, 'i', [$topicId]) ?? [];
    }

    // ── External Resources ───────────────────────────────────

    public function getExternalResourcesByTopicId(int $topicId): array
    {
        $sql = 'SELECT id, title, resource_type, url, description, sort_order
                FROM pathfinder_external_resources
                WHERE topic_id = ? AND status = "active"
                ORDER BY resource_type, sort_order ASC';
        return $this->db->fetchAll($sql, 'i', [$topicId]) ?? [];
    }

    // ── Related Books ────────────────────────────────────────

    public function getRelatedBooks(int $biblioId): array
    {
        $sql = 'SELECT rb.relation_type, b.biblio_id, b.title, b.call_number, b.image, b.publish_year
                FROM pathfinder_related_books rb
                JOIN biblio b ON b.biblio_id = rb.related_biblio_id
                WHERE rb.biblio_id = ?
                ORDER BY rb.relation_type, b.title ASC';
        return $this->db->fetchAll($sql, 'i', [$biblioId]) ?? [];
    }

    // ── Ringkasan konten per topik (untuk admin dashboard) ───

    public function getTopicSummary(int $topicId): array
    {
        $intro     = $this->getIntroduction($topicId);
        $books     = $this->getBooksByTopicId($topicId, 5);
        $guides    = $this->getGuidesByTopicId($topicId);
        $downloads = $this->getDownloadsByTopicId($topicId);
        $ext       = $this->getExternalResourcesByTopicId($topicId);
        return [
            'has_intro'      => $intro !== null,
            'total_books'    => count($books),
            'total_guides'   => count($guides),
            'total_downloads'=> count($downloads),
            'total_ext'      => count($ext),
        ];
    }

    // ── Increment download count ──────────────────────────────

    public function incrementDownload(int $downloadId): void
    {
        $sql = 'UPDATE pathfinder_downloads SET download_count = download_count + 1 WHERE id = ?';
        $this->db->execute($sql, 'i', [$downloadId]);
    }

    // ── Admin CRUD Methods ────────────────────────────────────

    public function createCategory(array $data): int
    {
        $sql = 'INSERT INTO pathfinder_categories (name, slug, description, icon, status, sort_order) VALUES (?, ?, ?, ?, ?, ?)';
        $this->db->execute($sql, 'sssssi', [
            $data['name'] ?? '',
            $data['slug'] ?? '',
            $data['description'] ?? '',
            $data['icon'] ?? '',
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0
        ]);
        $id = $this->db->lastInsertId();
        $this->activityLog->log('menambahkan', 'Kategori Pathfinder V2', $data['name'] ?? '');
        return $id;
    }

    public function updateCategory(int $id, array $data): bool
    {
        $sql = 'UPDATE pathfinder_categories SET name = ?, slug = ?, description = ?, icon = ?, status = ?, sort_order = ? WHERE id = ?';
        $res = $this->db->execute($sql, 'sssssii', [
            $data['name'] ?? '',
            $data['slug'] ?? '',
            $data['description'] ?? '',
            $data['icon'] ?? '',
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
            $id
        ]);
        if ($res) {
            $this->activityLog->log('mengubah', 'Kategori Pathfinder V2', $data['name'] ?? '');
        }
        return $res;
    }

    public function deleteCategory(int $id): bool
    {
        $cat = $this->getCategoryById($id);
        $sql = 'DELETE FROM pathfinder_categories WHERE id = ?';
        $res = $this->db->execute($sql, 'i', [$id]);
        if ($res && $cat) {
            $this->activityLog->log('menghapus', 'Kategori Pathfinder V2', $cat['name'] ?? '');
        }
        return $res;
    }

    public function createTopic(array $data): int
    {
        $sql = 'INSERT INTO pathfinder_topics (category_id, name, slug, description, icon, banner_image, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $this->db->execute($sql, 'isssssis', [
            $data['category_id'] ?? 0,
            $data['name'] ?? '',
            $data['slug'] ?? '',
            $data['description'] ?? '',
            $data['icon'] ?? '',
            $data['banner_image'] ?? '',
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active'
        ]);
        $id = $this->db->lastInsertId();
        $this->activityLog->log('menambahkan', 'Topik Pathfinder V2', $data['name'] ?? '');
        return $id;
    }

    public function updateTopic(int $id, array $data): bool
    {
        $sql = 'UPDATE pathfinder_topics SET category_id = ?, name = ?, slug = ?, description = ?, icon = ?, banner_image = ?, status = ?, sort_order = ? WHERE id = ?';
        $res = $this->db->execute($sql, 'issssssii', [
            $data['category_id'] ?? 0,
            $data['name'] ?? '',
            $data['slug'] ?? '',
            $data['description'] ?? '',
            $data['icon'] ?? '',
            $data['banner_image'] ?? '',
            $data['status'] ?? 'draft',
            $data['sort_order'] ?? 0,
            $id
        ]);
        
        if ($res >= 0) {
            $this->activityLog->log('memperbarui', 'Topik Pathfinder V2', $data['name'] ?? '');
            return true;
        }
        return false;
    }

    public function deleteTopic(int $id): bool
    {
        // Topic data
        $sqlFetch = 'SELECT name FROM pathfinder_topics WHERE id = ?';
        $topic = $this->db->fetchOne($sqlFetch, 'i', [$id]);

        $sql = 'DELETE FROM pathfinder_topics WHERE id = ?';
        $res = $this->db->execute($sql, 'i', [$id]);
        if ($res && $topic) {
            $this->activityLog->log('menghapus', 'Topik Pathfinder V2', $topic['name'] ?? '');
        }
        return $res;
    }

    public function upsertIntroduction(int $topicId, array $data): bool
    {
        $intro = $this->getIntroduction($topicId);
        if ($intro) {
            $sql = 'UPDATE pathfinder_topic_introductions SET definition = ?, learning_objectives = ?, importance = ?, topics_to_learn = ? WHERE topic_id = ?';
            $res = $this->db->execute($sql, 'ssssi', [
                $data['definition'] ?? '',
                $data['learning_objectives'] ?? '',
                $data['importance'] ?? '',
                $data['topics_to_learn'] ?? '',
                $topicId
            ]);
            return $res >= 0;
        } else {
            $sql = 'INSERT INTO pathfinder_topic_introductions (topic_id, definition, learning_objectives, importance, topics_to_learn) VALUES (?, ?, ?, ?, ?)';
            $res = $this->db->execute($sql, 'issss', [
                $topicId,
                $data['definition'] ?? '',
                $data['learning_objectives'] ?? '',
                $data['importance'] ?? '',
                $data['topics_to_learn'] ?? ''
            ]);
            return $res > 0;
        }
    }
}
