<?php
/**
 * Gallery Service
 *
 * File    : GalleryService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Mengambil data dari tabel `website_gallery`.
 */

require_once __DIR__ . '/../helpers/Database.php';

class GalleryService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil daftar foto galeri, diurutkan berdasarkan sort_order atau ID terbaru.
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getGallery(int $limit = 12, int $offset = 0): array
    {
        return $this->db->fetchAll(
            'SELECT
                id,
                title,
                image_path,
                description,
                sort_order
             FROM website_gallery
             ORDER BY sort_order ASC, id DESC
             LIMIT ? OFFSET ?',
            'ii',
            [$limit, $offset]
        );
    }
}

