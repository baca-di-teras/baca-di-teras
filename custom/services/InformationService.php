<?php
/**
 * Information Service
 *
 * File    : InformationService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 */

require_once __DIR__ . '/../helpers/database.php';

class InformationService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getDb(): Database
    {
        return $this->db;
    }

    /**
     * Mendapatkan semua informasi
     */
    public function getAllInformation(): array
    {
        $sql = "SELECT * FROM bdt_information ORDER BY type ASC, sort_order ASC, info_id DESC";
        return $this->db->fetchAll($sql);
    }

    public function getFaqList(): array
    {
        $sql = "SELECT * FROM bdt_information WHERE type = 'faq' AND status = 'aktif' ORDER BY sort_order ASC, info_id DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Mendapatkan informasi berdasarkan ID
     */
    public function getInformationById(int $info_id): ?array
    {
        $sql = "SELECT * FROM bdt_information WHERE info_id = ? LIMIT 1";
        $stmt = $this->db->getConnection()->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param('i', $info_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $info = $result->fetch_assoc();
        $stmt->close();

        return $info;
    }

    /**
     * Membuat informasi baru
     */
    public function createInformation(array $data): bool
    {
        $sql = "INSERT INTO bdt_information (id_name, type, title, content, extra_data, status, sort_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->getConnection()->prepare($sql);
        if (!$stmt) return false;

        $id_name = $data['id_name'] ?? null;
        $type = $data['type'] ?? 'lainnya';
        $title = $data['title'] ?? '';
        $content = $data['content'] ?? null;
        $extra_data = isset($data['extra_data']) && !empty($data['extra_data']) ? $data['extra_data'] : null;
        $status = $data['status'] ?? 'aktif';
        $sort_order = $data['sort_order'] ?? 0;

        $stmt->bind_param('ssssssi', $id_name, $type, $title, $content, $extra_data, $status, $sort_order);
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Memperbarui informasi
     */
    public function updateInformation(int $info_id, array $data): bool
    {
        $sql = "UPDATE bdt_information 
                SET id_name = ?, type = ?, title = ?, content = ?, extra_data = ?, status = ?, sort_order = ? 
                WHERE info_id = ?";
        
        $stmt = $this->db->getConnection()->prepare($sql);
        if (!$stmt) return false;

        $id_name = $data['id_name'] ?? null;
        $type = $data['type'] ?? 'lainnya';
        $title = $data['title'] ?? '';
        $content = $data['content'] ?? null;
        $extra_data = isset($data['extra_data']) && !empty($data['extra_data']) ? $data['extra_data'] : null;
        $status = $data['status'] ?? 'aktif';
        $sort_order = $data['sort_order'] ?? 0;

        $stmt->bind_param('ssssssii', $id_name, $type, $title, $content, $extra_data, $status, $sort_order, $info_id);
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    public function deleteInformation(int $info_id): bool
    {
        $sql = "DELETE FROM bdt_information WHERE info_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('i', $info_id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function getServicesInfo(): array
    {
        // Kunci-kunci yang diharapkan untuk halaman frontend
        $keys = ['peminjaman_header', 'jam_operasional', 'keanggotaan', 'unduhan', 'tata_tertib_header'];
        
        $sql = "SELECT id_name, title, content, extra_data FROM bdt_information 
                WHERE id_name IN ('" . implode("','", $keys) . "') AND status = 'aktif'";
        $results = $this->db->fetchAll($sql);
        
        $info = [];
        
        // Mem-parsing hasil database
        foreach ($results as $row) {
            $id = $row['id_name'];
            if ($id === 'peminjaman_header') $id = 'peminjaman';
            if ($id === 'tata_tertib_header') $id = 'tata_tertib';
            $extra = !empty($row['extra_data']) ? json_decode($row['extra_data'], true) : [];
            
            $info[$id] = [
                'title' => $row['title'],
                'desc' => $row['content'] ?? ''
            ];
            
            // Menggabungkan ekstra data ke dalam array utama agar strukturnya sama persis
            if (is_array($extra)) {
                $info[$id] = array_merge($info[$id], $extra);
            }
        }
        
        // Fallback (Cadangan) jika data terhapus secara tidak sengaja dari DB
        if (!isset($info['peminjaman'])) {
            $info['peminjaman'] = [
                'title' => 'Panduan Peminjaman',
                'desc' => 'Berikut adalah ketentuan peminjaman koleksi.',
                'points' => ['Maksimal 3 buku', 'Durasi 7 hari'],
                'action_href' => '#',
                'action_label' => 'Panduan'
            ];
        }
        if (!isset($info['jam_operasional'])) {
            $info['jam_operasional'] = [
                'title' => 'Jam Operasional',
                'schedule' => [['day' => 'Setiap Hari', 'time' => 'Buka', 'highlight' => false]]
            ];
        }
        if (!isset($info['keanggotaan'])) {
            $info['keanggotaan'] = [
                'title' => 'Keanggotaan',
                'desc' => 'Daftar menjadi anggota.',
                'action_href' => (defined('BASE_URL') ? BASE_URL : '') . '/daftar',
                'action_label' => 'Daftar'
            ];
        }
        if (!isset($info['unduhan'])) {
            $info['unduhan'] = [
                'title' => 'Unduhan',
                'files' => []
            ];
        }
        if (!isset($info['tata_tertib'])) {
            $info['tata_tertib'] = [
                'title' => 'Tata Tertib',
                'desc' => 'Patuhi aturan.',
                'badges' => []
            ];
        }

        // Load peminjaman points if peminjaman exists
        $sql_points = "SELECT title FROM bdt_information WHERE id_name = 'peminjaman_point' AND status = 'aktif' ORDER BY sort_order ASC, info_id DESC";
        $point_results = $this->db->fetchAll($sql_points);
        if ($point_results) {
            $info['peminjaman']['points'] = array_column($point_results, 'title');
        }

        // Load tata tertib points if tata tertib exists
        $sql_tt_points = "SELECT title FROM bdt_information WHERE id_name = 'tata_tertib_point' AND status = 'aktif' ORDER BY sort_order ASC, info_id DESC";
        $tt_point_results = $this->db->fetchAll($sql_tt_points);
        if ($tt_point_results) {
            $info['tata_tertib']['badges'] = array_column($tt_point_results, 'title');
        }
        
        return $info;
    }
}
