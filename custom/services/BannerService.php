<?php
/**
 * Banner Service
 *
 * File    : BannerService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Mengambil data dari tabel `website_banner`.
 */

require_once __DIR__ . '/../helpers/Database.php';

class BannerService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil daftar banner yang aktif untuk carousel/slider.
     *
     * @return array
     */
    public function getActiveBanners(): array
    {
        return $this->db->fetchAll(
            'SELECT
                id,
                title,
                image_path,
                link_url,
                status,
                sort_order
             FROM website_banner
             WHERE status = "aktif"
             ORDER BY sort_order ASC, id DESC'
        );
    }
}

