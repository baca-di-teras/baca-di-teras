<?php
/**
 * Donasi Service
 *
 * File    : DonasiService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Mengelola data donatur dan partner untuk halaman Donasi.
 * Terintegrasi dengan tabel bdt_donatur dan bdt_donasi_partner.
 */

require_once __DIR__ . '/../helpers/Database.php';

class DonasiService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Donatur ──────────────────────────────────────────────

    /**
     * Mengambil daftar donatur yang visible dengan pagination.
     *
     * @param int    $page    Halaman saat ini (1-indexed)
     * @param int    $limit   Jumlah per halaman
     * @param string $sortBy  'terbaru' atau 'terlama'
     * @return array
     */
    public function getDonatur(int $page = 1, int $limit = 10, string $sortBy = 'terbaru'): array
    {
        $order  = $sortBy === 'terlama' ? 'ASC' : 'DESC';
        $offset = ($page - 1) * $limit;

        $sql = "SELECT donatur_id, name, donated_at, note
                FROM bdt_donatur
                WHERE is_visible = 1
                ORDER BY donated_at {$order}, donatur_id {$order}
                LIMIT ? OFFSET ?";

        return $this->db->fetchAll($sql, 'ii', [$limit, $offset]);
    }

    /**
     * Total donatur yang visible.
     *
     * @return int
     */
    public function getDonaturCount(): int
    {
        $sql = "SELECT COUNT(*) FROM bdt_donatur WHERE is_visible = 1";
        return (int) $this->db->fetchScalar($sql);
    }

    /**
     * Mengambil semua donatur untuk admin panel.
     *
     * @return array
     */
    public function getAllDonatur(): array
    {
        $sql = "SELECT * FROM bdt_donatur ORDER BY donated_at DESC, donatur_id DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Mengambil satu donatur berdasarkan ID.
     *
     * @param int $donatur_id
     * @return array|null
     */
    public function getDonaturById(int $donatur_id): ?array
    {
        $sql = "SELECT * FROM bdt_donatur WHERE donatur_id = ? LIMIT 1";
        return $this->db->fetchOne($sql, 'i', [$donatur_id]);
    }

    /**
     * Membuat donatur baru.
     *
     * @param array $data
     * @return bool
     */
    public function createDonatur(array $data): bool
    {
        $sql = "INSERT INTO bdt_donatur (name, amount, donated_at, note, is_visible)
                VALUES (?, ?, ?, ?, ?)";

        $name       = trim($data['name'] ?? '');
        $amount     = !empty($data['amount']) ? (float) $data['amount'] : null;
        $donated_at = $data['donated_at'] ?? date('Y-m-d');
        $note       = !empty($data['note']) ? trim($data['note']) : null;
        $is_visible = isset($data['is_visible']) ? (int) $data['is_visible'] : 1;

        $stmt = $this->db->getConnection()->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('sdssi', $name, $amount, $donated_at, $note, $is_visible);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Memperbarui data donatur.
     *
     * @param int   $donatur_id
     * @param array $data
     * @return bool
     */
    public function updateDonatur(int $donatur_id, array $data): bool
    {
        $sql = "UPDATE bdt_donatur
                SET name = ?, amount = ?, donated_at = ?, note = ?, is_visible = ?
                WHERE donatur_id = ?";

        $name       = trim($data['name'] ?? '');
        $amount     = !empty($data['amount']) ? (float) $data['amount'] : null;
        $donated_at = $data['donated_at'] ?? date('Y-m-d');
        $note       = !empty($data['note']) ? trim($data['note']) : null;
        $is_visible = isset($data['is_visible']) ? (int) $data['is_visible'] : 1;

        $stmt = $this->db->getConnection()->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('sdssii', $name, $amount, $donated_at, $note, $is_visible, $donatur_id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Menghapus donatur berdasarkan ID.
     *
     * @param int $donatur_id
     * @return bool
     */
    public function deleteDonatur(int $donatur_id): bool
    {
        $affected = $this->db->execute(
            "DELETE FROM bdt_donatur WHERE donatur_id = ?",
            'i',
            [$donatur_id]
        );
        return $affected > 0;
    }

    // ── Partner / Logo ───────────────────────────────────────

    /**
     * Mengambil daftar partner yang visible untuk halaman publik.
     *
     * @return array
     */
    public function getPartners(): array
    {
        $sql = "SELECT partner_id, name, logo_path, website_url
                FROM bdt_donasi_partner
                WHERE is_visible = 1
                ORDER BY sort_order ASC, partner_id ASC";

        return $this->db->fetchAll($sql);
    }

    /**
     * Mengambil semua partner untuk admin panel.
     *
     * @return array
     */
    public function getAllPartners(): array
    {
        $sql = "SELECT * FROM bdt_donasi_partner ORDER BY sort_order ASC, partner_id ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Mengambil satu partner berdasarkan ID.
     *
     * @param int $partner_id
     * @return array|null
     */
    public function getPartnerById(int $partner_id): ?array
    {
        $sql = "SELECT * FROM bdt_donasi_partner WHERE partner_id = ? LIMIT 1";
        return $this->db->fetchOne($sql, 'i', [$partner_id]);
    }

    /**
     * Membuat partner baru.
     *
     * @param array $data
     * @return bool
     */
    public function createPartner(array $data): bool
    {
        $sql = "INSERT INTO bdt_donasi_partner (name, logo_path, website_url, sort_order, is_visible)
                VALUES (?, ?, ?, ?, ?)";

        $name        = trim($data['name'] ?? '');
        $logo_path   = !empty($data['logo_path']) ? $data['logo_path'] : null;
        $website_url = !empty($data['website_url']) ? trim($data['website_url']) : null;
        $sort_order  = (int) ($data['sort_order'] ?? 0);
        $is_visible  = isset($data['is_visible']) ? (int) $data['is_visible'] : 1;

        $affected = $this->db->execute($sql, 'sssii', [$name, $logo_path, $website_url, $sort_order, $is_visible]);
        return $affected > 0;
    }

    /**
     * Memperbarui data partner.
     *
     * @param int   $partner_id
     * @param array $data
     * @return bool
     */
    public function updatePartner(int $partner_id, array $data): bool
    {
        $name        = trim($data['name'] ?? '');
        $logo_path   = !empty($data['logo_path']) ? $data['logo_path'] : null;
        $website_url = !empty($data['website_url']) ? trim($data['website_url']) : null;
        $sort_order  = (int) ($data['sort_order'] ?? 0);
        $is_visible  = isset($data['is_visible']) ? (int) $data['is_visible'] : 1;

        // Jika ada logo baru, update dengan logo baru; jika tidak, pertahankan yang lama
        if ($logo_path !== null) {
            $sql = "UPDATE bdt_donasi_partner
                    SET name = ?, logo_path = ?, website_url = ?, sort_order = ?, is_visible = ?
                    WHERE partner_id = ?";
            $affected = $this->db->execute($sql, 'sssiii', [$name, $logo_path, $website_url, $sort_order, $is_visible, $partner_id]);
        } else {
            $sql = "UPDATE bdt_donasi_partner
                    SET name = ?, website_url = ?, sort_order = ?, is_visible = ?
                    WHERE partner_id = ?";
            $affected = $this->db->execute($sql, 'ssiii', [$name, $website_url, $sort_order, $is_visible, $partner_id]);
        }

        return $affected > 0;
    }

    /**
     * Menghapus partner berdasarkan ID.
     *
     * @param int $partner_id
     * @return bool
     */
    public function deletePartner(int $partner_id): bool
    {
        $affected = $this->db->execute(
            "DELETE FROM bdt_donasi_partner WHERE partner_id = ?",
            'i',
            [$partner_id]
        );
        return $affected > 0;
    }
}

