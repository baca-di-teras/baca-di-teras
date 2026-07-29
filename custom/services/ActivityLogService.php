<?php
/**
 * Activity Log Service
 */

if (!defined('BASE_URL')) {
    $configPath = defined('ROOT_PATH') ? ROOT_PATH . '/custom/config/database.php' : __DIR__ . '/../config/database.php';
    if (file_exists($configPath)) require_once $configPath;
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..';
require_once $libPath . '/custom/helpers/database.php';

class ActivityLogService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Catat aktivitas baru
     */
    public function log(string $action, string $entity, string $entity_name): void
    {
        // Hanya log jika ada admin yang login
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $user_id = (int)($_SESSION['admin_id'] ?? 0);
        if ($user_id === 0) return;

        $sql = "INSERT INTO bdt_activity_logs (user_id, action, entity, entity_name, created_at) VALUES (?, ?, ?, ?, NOW())";
        $this->db->execute($sql, 'isss', [$user_id, $action, $entity, $entity_name]);
    }

    /**
     * Ambil daftar aktivitas terbaru
     */
    public function getRecentLogs(int $limit = 50): array
    {
        $sql = "SELECT l.*, a.name as user_name, a.role as user_role 
                FROM bdt_activity_logs l
                LEFT JOIN bdt_admins a ON l.user_id = a.id
                ORDER BY l.created_at DESC 
                LIMIT ?";
        return $this->db->fetchAll($sql, 'i', [$limit]);
    }
    /**
     * Ambil aktivitas penambahan konten baru (Artikel, Pathfinder, dll)
     */
    public function getRecentUploads(int $limit = 5): array
    {
        $sql = "SELECT l.*, a.name as user_name 
                FROM bdt_activity_logs l
                LEFT JOIN bdt_admins a ON l.user_id = a.id
                WHERE l.action = 'menambahkan' AND l.entity IN ('Artikel', 'Pathfinder')
                ORDER BY l.created_at DESC 
                LIMIT ?";
        return $this->db->fetchAll($sql, 'i', [$limit]);
    }
}
