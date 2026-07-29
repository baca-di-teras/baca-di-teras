<?php
/**
 * Pathfinder Service
 *
 * File    : PathfinderService.php
 * Project : Baca Di Teras
 * Version : 1.1.0
 *
 * Mengelola data Pathfinder Perpustakaan.
 * Terintegrasi dengan database bdt_pathfinder dan bdt_pathfinder_category.
 */

require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/ActivityLogService.php';

class PathfinderService
{
    private Database $db;
    private ActivityLogService $activityLog;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->activityLog = new ActivityLogService();
    }

    // ── Public Methods ───────────────────────────────────────

    /**
     * Mengambil daftar semua kategori pathfinder.
     *
     * @return array
     */
    public function getCategories(): array
    {
        $sql = "SELECT * FROM bdt_pathfinder_category ORDER BY category_id ASC";
        return $this->db->fetchAll($sql) ?? [];
    }

    /**
     * Mengambil data satu kategori berdasarkan slug.
     *
     * @param  string     $slug
     * @return array|null
     */
    public function getCategoryBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM bdt_pathfinder_category WHERE slug = ?";
        return $this->db->fetchOne($sql, 's', [$slug]);
    }

    /**
     * Mengambil pathfinder populer (untuk halaman utama).
     * Mengembalikan pathfinder dengan rekomendasi terbanyak.
     *
     * @param  int   $limit
     * @return array
     */
    public function getPopularPathfinders(int $limit = 4): array
    {
        $sql = "
            SELECT p.*, c.slug AS category_slug, c.label AS category_label, c.icon AS category_icon
            FROM bdt_pathfinder p
            JOIN bdt_pathfinder_category c ON p.category_id = c.category_id
            ORDER BY p.recommendations DESC
            LIMIT ?
        ";
        return $this->db->fetchAll($sql, 'i', [$limit]) ?? [];
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
        $sqlCount = "
            SELECT COUNT(*) as total 
            FROM bdt_pathfinder p 
            JOIN bdt_pathfinder_category c ON p.category_id = c.category_id 
            WHERE c.slug = ?
        ";
        $countRow = $this->db->fetchOne($sqlCount, 's', [$categorySlug]);
        $total = $countRow['total'] ?? 0;

        $totalPages = max(1, (int) ceil($total / $perPage));
        $page       = max(1, min($page, $totalPages));
        $offset     = ($page - 1) * $perPage;

        $sql = "
            SELECT p.*, c.slug AS category_slug, c.label AS category_label, c.icon AS category_icon
            FROM bdt_pathfinder p
            JOIN bdt_pathfinder_category c ON p.category_id = c.category_id
            WHERE c.slug = ?
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ";
        $items = $this->db->fetchAll($sql, 'sii', [$categorySlug, $perPage, $offset]) ?? [];

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
        $sql = "
            SELECT p.*, c.slug AS category_slug, c.label AS category_label, c.icon AS category_icon
            FROM bdt_pathfinder p
            JOIN bdt_pathfinder_category c ON p.category_id = c.category_id
            WHERE p.slug = ?
        ";
        return $this->db->fetchOne($sql, 's', [$slug]);
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
            return $this->getAllPathfinders();
        }

        $keywordLike = '%' . trim($keyword) . '%';
        $sql = "
            SELECT p.*, c.slug AS category_slug, c.label AS category_label, c.icon AS category_icon
            FROM bdt_pathfinder p
            JOIN bdt_pathfinder_category c ON p.category_id = c.category_id
            WHERE p.title LIKE ? OR p.description LIKE ? OR c.label LIKE ?
            ORDER BY p.created_at DESC
        ";
        return $this->db->fetchAll($sql, 'sss', [$keywordLike, $keywordLike, $keywordLike]) ?? [];
    }

    /**
     * Mengambil semua pathfinder (untuk keperluan listing).
     *
     * @return array
     */
    public function getAllPathfinders(): array
    {
        $sql = "
            SELECT p.*, c.slug AS category_slug, c.label AS category_label, c.icon AS category_icon
            FROM bdt_pathfinder p
            JOIN bdt_pathfinder_category c ON p.category_id = c.category_id
            ORDER BY p.created_at DESC
        ";
        return $this->db->fetchAll($sql) ?? [];
    }

    /**
     * Mengambil data detail satu pathfinder berdasarkan ID.
     *
     * @param  int $id
     * @return array|null
     */
    public function getPathfinderById(int $id): ?array
    {
        $sql = "
            SELECT p.*, c.slug AS category_slug, c.label AS category_label, c.icon AS category_icon
            FROM bdt_pathfinder p
            JOIN bdt_pathfinder_category c ON p.category_id = c.category_id
            WHERE p.pathfinder_id = ?
        ";
        return $this->db->fetchOne($sql, 'i', [$id]);
    }

    /**
     * Membuat pathfinder baru.
     *
     * @param array $data
     * @return int Insert ID
     */
    public function createPathfinder(array $data): int
    {
        $sql = "
            INSERT INTO bdt_pathfinder (
                category_id, slug, title, status, badge, description, 
                author, catalog_url, broader_terms, narrower_terms, related_terms, books
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        $this->db->execute($sql, 'isssssssssss', [
            $data['category_id'] ?? 1,
            $data['slug'] ?? '',
            $data['title'] ?? '',
            $data['status'] ?? 'draft',
            $data['badge'] ?? '',
            $data['description'] ?? '',
            $data['author'] ?? '',
            $data['catalog_url'] ?? '',
            $data['broader_terms'] ?? '[]',
            $data['narrower_terms'] ?? '[]',
            $data['related_terms'] ?? '[]',
            $data['books'] ?? '[]'
        ]);

        $insertId = $this->db->lastInsertId();

        $this->activityLog->log('menambahkan', 'Pathfinder', $data['title'] ?? '');

        return $insertId;
    }

    /**
     * Mengubah pathfinder yang sudah ada.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updatePathfinder(int $id, array $data): bool
    {
        $sql = "
            UPDATE bdt_pathfinder SET 
                category_id = ?, slug = ?, title = ?, status = ?, badge = ?, description = ?, 
                author = ?, catalog_url = ?, broader_terms = ?, narrower_terms = ?, related_terms = ?, books = ?
            WHERE pathfinder_id = ?
        ";
        
        $result = $this->db->execute($sql, 'isssssssssssi', [
            $data['category_id'] ?? 1,
            $data['slug'] ?? '',
            $data['title'] ?? '',
            $data['status'] ?? 'draft',
            $data['badge'] ?? '',
            $data['description'] ?? '',
            $data['author'] ?? '',
            $data['catalog_url'] ?? '',
            $data['broader_terms'] ?? '[]',
            $data['narrower_terms'] ?? '[]',
            $data['related_terms'] ?? '[]',
            $data['books'] ?? '[]',
            $id
        ]);

        if ($result) {
            $this->activityLog->log('mengubah', 'Pathfinder', $data['title'] ?? '');
        }

        return $result;
    }
}
