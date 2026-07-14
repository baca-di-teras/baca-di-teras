<?php
/**
 * Village Service
 *
 * File    : VillageService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Mengelola data profil desa dan fitur-fitur layanan terpusat desa.
 */

require_once __DIR__ . '/../helpers/Database.php';

class VillageService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Mengambil profil utama Desa
     * 
     * @return array|null
     */
    public function getProfile(): ?array
    {
        // Mengambil profil desa dari tabel bdt_village_profile
        // Umumnya tabel ini hanya memiliki 1 baris record
        return $this->db->fetchOne(
            'SELECT * FROM bdt_village_profile LIMIT 1'
        );
    }

    /**
     * Mengambil daftar fitur / layanan utama desa 
     * (Ditampilkan di landing page bagian fitur)
     * 
     * @return array
     */
    public function getFeatures(): array
    {
        // Mengambil daftar fitur dari bdt_feature (sebelumnya di feature-config.php)
        return $this->db->fetchAll(
            'SELECT icon, title, description AS `desc`, href 
             FROM bdt_feature 
             WHERE status = "aktif" 
             ORDER BY sort_order ASC'
        );
    }
}
