<?php
/**
 * Information Service
 *
 * File    : InformationService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Mengelola data informasi umum seperti FAQ, tata tertib, jadwal,
 * dan panduan peminjaman dari tabel bdt_information.
 */

require_once __DIR__ . '/../helpers/Database.php';

class InformationService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Mengambil daftar FAQ (Pertanyaan yang Sering Diajukan)
     * 
     * @return array
     */
    public function getFaqList(): array
    {
        // Asumsi tabel bdt_information dengan type 'faq'
        // Struktur kembalian sama dengan config array sebelumnya
        return $this->db->fetchAll(
            'SELECT title AS question, content AS answer 
             FROM bdt_information 
             WHERE type = "faq" AND status = "aktif" 
             ORDER BY sort_order ASC'
        );
    }

    /**
     * Mengambil informasi tata tertib
     * 
     * @return array
     */
    public function getRules(): array
    {
        return $this->db->fetchAll(
            'SELECT title, content, extra_data 
             FROM bdt_information 
             WHERE type = "rule" AND status = "aktif" 
             ORDER BY sort_order ASC'
        );
    }

    /**
     * Mengambil informasi layanan (Peminjaman, Keanggotaan, dll)
     * 
     * @return array
     */
    public function getServicesInfo(): array
    {
        $rows = $this->db->fetchAll(
            'SELECT id_name AS id, title, content AS desc_text, extra_data 
             FROM bdt_information 
             WHERE type = "service" AND status = "aktif" 
             ORDER BY sort_order ASC'
        );
        
        $services = [];
        foreach ($rows as $row) {
            $extra = json_decode($row['extra_data'], true) ?? [];
            $services[$row['id']] = [
                'title' => $row['title'],
                'desc'  => $row['desc_text'],
                'points'=> $extra['points'] ?? [],
                'action_label' => $extra['action_label'] ?? null,
                'action_href'  => $extra['action_href'] ?? null,
            ];
        }

        $services['jam_operasional'] ??= [
            'title' => 'Jam Operasional',
            'schedule' => [
                ['day' => 'Senin - Jumat', 'time' => '08:00 - 16:00', 'highlight' => true],
                ['day' => 'Sabtu', 'time' => '08:00 - 12:00'],
                ['day' => 'Minggu', 'time' => 'Tutup'],
            ],
        ];

        $services['unduhan'] ??= [
            'title' => 'Unduhan',
            'files' => $this->getDownloads(),
        ];

        $rules = $this->getRules();
        $services['tata_tertib'] ??= [
            'title' => $rules[0]['title'] ?? 'Tata Tertib',
            'desc' => $rules[0]['content'] ?? 'Jaga ketenangan ruang baca, rawat koleksi, dan kembalikan buku tepat waktu.',
            'badges' => ['Jaga Kebersihan', 'Rawat Buku', 'Tepat Waktu'],
        ];

        return $services;
    }

    /**
     * Mengambil informasi daftar unduhan
     * 
     * @return array
     */
    public function getDownloads(): array
    {
        $rows = $this->db->fetchAll(
            'SELECT title AS label, content AS href 
             FROM bdt_information 
             WHERE type = "download" AND status = "aktif" 
             ORDER BY sort_order ASC'
        );

        return array_map(static function (array $row): array {
            return [
                'label' => $row['label'] ?? 'Dokumen',
                'href'  => $row['href'] ?: '#',
            ];
        }, $rows);
    }
}
