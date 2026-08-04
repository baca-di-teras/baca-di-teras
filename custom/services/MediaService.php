<?php
/**
 * Service untuk Manajemen Rilis / Liputan Media – Baca Di Teras
 *
 * File    : MediaService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..';
require_once $libPath . '/custom/helpers/Database.php';

class MediaService {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Mengambil daftar rilis media publik (opsional filter kategori)
     */
    public function getPublicMedia(?string $category = null): array {
        if ($category && $category !== 'semua') {
            $sql = "SELECT * FROM bdt_media WHERE is_published = 1 AND category = ? ORDER BY is_pinned DESC, release_date DESC, id DESC";
            return $this->db->fetchAll($sql, 's', [$category]);
        }
        $sql = "SELECT * FROM bdt_media WHERE is_published = 1 ORDER BY is_pinned DESC, release_date DESC, id DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Mengambil semua kategori unik yang ada di database
     */
    public function getCategories(): array {
        $sql = "SELECT DISTINCT category FROM bdt_media WHERE is_published = 1 ORDER BY category ASC";
        $results = $this->db->fetchAll($sql);
        return array_column($results, 'category');
    }

    /**
     * Mengambil semua item media untuk admin
     */
    public function getAllMedia(): array {
        $sql = "SELECT * FROM bdt_media ORDER BY is_pinned DESC, release_date DESC, id DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Mengambil data media terfilter dengan pagination untuk admin
     */
    public function getAdminMedia(array $filters = [], int $limit = 10, int $offset = 0): array {
        $where = [];
        $params = [];
        $types = '';

        if (!empty($filters['category'])) {
            $where[] = "category = ?";
            $params[] = $filters['category'];
            $types .= 's';
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] === 'published') {
                $where[] = "is_published = 1";
            } elseif ($filters['status'] === 'draft') {
                $where[] = "is_published = 0";
            }
        }

        if (!empty($filters['search'])) {
            $where[] = "(title LIKE ? OR media_name LIKE ?)";
            $searchKeyword = '%' . $filters['search'] . '%';
            $params[] = $searchKeyword;
            $params[] = $searchKeyword;
            $types .= 'ss';
        }

        $sql = "SELECT * FROM bdt_media";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY is_pinned DESC, release_date DESC, id DESC LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        return $this->db->fetchAll($sql, $types, $params);
    }

    /**
     * Menghitung jumlah media yang berstatus disematkan (pinned)
     */
    public function countPinnedMedia(?int $excludeId = null): int {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as total FROM bdt_media WHERE is_pinned = 1 AND id != ?";
            $res = $this->db->fetchOne($sql, 'i', [$excludeId]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM bdt_media WHERE is_pinned = 1";
            $res = $this->db->fetchOne($sql);
        }
        return (int)($res['total'] ?? 0);
    }

    /**
     * Menghitung total data media terfilter untuk admin
     */
    public function countAdminMedia(array $filters = []): int {
        $where = [];
        $params = [];
        $types = '';

        if (!empty($filters['category'])) {
            $where[] = "category = ?";
            $params[] = $filters['category'];
            $types .= 's';
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] === 'published') {
                $where[] = "is_published = 1";
            } elseif ($filters['status'] === 'draft') {
                $where[] = "is_published = 0";
            }
        }

        if (!empty($filters['search'])) {
            $where[] = "(title LIKE ? OR media_name LIKE ?)";
            $searchKeyword = '%' . $filters['search'] . '%';
            $params[] = $searchKeyword;
            $params[] = $searchKeyword;
            $types .= 'ss';
        }

        $sql = "SELECT COUNT(*) as total FROM bdt_media";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $result = $this->db->fetchOne($sql, $types, $params);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Mengambil item media berdasarkan ID
     */
    public function getMediaById(int $id): ?array {
        $sql = "SELECT * FROM bdt_media WHERE id = ?";
        return $this->db->fetchOne($sql, 'i', [$id]);
    }

    /**
     * Menambahkan media baru
     */
    public function createMedia(array $data): int {
        $sql = "INSERT INTO bdt_media (title, media_name, category, url, release_date, description, is_published, is_pinned, sort_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->db->execute($sql, 'ssssssiii', [
            $data['title'],
            $data['media_name'],
            $data['category'] ?? 'Media Nasional',
            $data['url'],
            $data['release_date'] ?? null,
            $data['description'] ?? null,
            $data['is_published'] ?? 1,
            $data['is_pinned'] ?? 0,
            $data['sort_order'] ?? 0
        ]);
    }

    /**
     * Memperbarui media
     */
    public function updateMedia(int $id, array $data): bool {
        $sql = "UPDATE bdt_media 
                SET title = ?, media_name = ?, category = ?, url = ?, release_date = ?, description = ?, is_published = ?, is_pinned = ?, sort_order = ?
                WHERE id = ?";
        return $this->db->execute($sql, 'ssssssiiii', [
            $data['title'],
            $data['media_name'],
            $data['category'] ?? 'Media Nasional',
            $data['url'],
            $data['release_date'] ?? null,
            $data['description'] ?? null,
            $data['is_published'] ?? 1,
            $data['is_pinned'] ?? 0,
            $data['sort_order'] ?? 0,
            $id
        ]) >= 0;
    }

    /**
     * Mengubah status terbit
     */
    public function togglePublish(int $id): bool {
        $media = $this->getMediaById($id);
        if (!$media) return false;
        $newStatus = $media['is_published'] ? 0 : 1;
        $sql = "UPDATE bdt_media SET is_published = ? WHERE id = ?";
        return $this->db->execute($sql, 'ii', [$newStatus, $id]) > 0;
    }

    /**
     * Menghapus media
     */
    public function deleteMedia(int $id): bool {
        $sql = "DELETE FROM bdt_media WHERE id = ?";
        return $this->db->execute($sql, 'i', [$id]) > 0;
    }
}
