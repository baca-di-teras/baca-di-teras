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
        return [
            'peminjaman' => [
                'title' => 'Panduan Peminjaman',
                'desc' => 'Berikut adalah ketentuan singkat peminjaman koleksi di perpustakaan kami.',
                'points' => [
                    'Maksimal meminjam 3 buku',
                    'Durasi peminjaman 7 hari',
                    'Wajib menunjukkan kartu anggota',
                    'Denda keterlambatan berlaku'
                ],
                'action_href' => '#',
                'action_label' => 'Baca Panduan Lengkap'
            ],
            'jam_operasional' => [
                'title' => 'Jam Operasional Pusat',
                'schedule' => [
                    ['day' => 'Senin - Kamis', 'time' => '08:00 - 15:00', 'highlight' => false],
                    ['day' => 'Jumat', 'time' => '08:00 - 11:30', 'highlight' => true],
                    ['day' => 'Sabtu - Minggu', 'time' => 'Libur (Tutup)', 'highlight' => false]
                ]
            ],
            'keanggotaan' => [
                'title' => 'Keanggotaan Perpustakaan',
                'desc' => 'Daftar menjadi anggota secara gratis untuk mengakses semua layanan perpustakaan Desa Teras.',
                'action_href' => (defined('BASE_URL') ? BASE_URL : '') . '/daftar',
                'action_label' => 'Daftar Sekarang'
            ],
            'unduhan' => [
                'title' => 'Formulir & Panduan',
                'files' => [
                    ['label' => 'Form Pendaftaran (PDF)', 'href' => '#'],
                    ['label' => 'Panduan Penggunaan OPAC', 'href' => '#'],
                    ['label' => 'Surat Pengajuan Bebas Pustaka', 'href' => '#']
                ]
            ],
            'tata_tertib' => [
                'title' => 'Tata Tertib',
                'desc' => 'Harap patuhi peraturan ini demi kenyamanan bersama pengunjung perpustakaan.',
                'badges' => [
                    'Dilarang Makan & Minum',
                    'Harap Tenang',
                    'Jaga Kebersihan',
                    'Dilarang Merokok'
                ]
            ]
        ];
    }
}
