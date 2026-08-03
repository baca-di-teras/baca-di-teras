<?php
/**
 * Announcement Service
 */

if (!defined('BASE_URL')) {
    $configPath = defined('ROOT_PATH') ? ROOT_PATH . '/custom/config/database.php' : __DIR__ . '/../config/database.php';
    if (file_exists($configPath)) require_once $configPath;
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..';
require_once $libPath . '/custom/helpers/Database.php';

class AnnouncementService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Dapatkan pengumuman aktif terbaru
     */
    public function getActiveAnnouncements(): array
    {
        $sql = "SELECT a.*, admin.name as author_name 
                FROM bdt_announcements a
                LEFT JOIN bdt_admins admin ON a.author_id = admin.id
                WHERE a.is_active = 1 
                ORDER BY a.created_at DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Dapatkan semua pengumuman
     */
    public function getAllAnnouncements(): array
    {
        $sql = "SELECT a.*, admin.name as author_name 
                FROM bdt_announcements a
                LEFT JOIN bdt_admins admin ON a.author_id = admin.id
                ORDER BY a.created_at DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Tambah pengumuman baru
     */
    public function createAnnouncement(int $author_id, string $message): bool
    {
        // Tambah yang baru (tidak menonaktifkan yang lama agar bisa bertumpuk)
        $sql = "INSERT INTO bdt_announcements (author_id, message, is_active, created_at) VALUES (?, ?, 1, NOW())";
        return $this->db->execute($sql, 'is', [$author_id, $message]) > 0;
    }

    /**
     * Hapus pengumuman
     */
    public function deleteAnnouncement(int $id): bool
    {
        $sql = "DELETE FROM bdt_announcements WHERE id = ?";
        return $this->db->execute($sql, 'i', [$id]) > 0;
    }
}

