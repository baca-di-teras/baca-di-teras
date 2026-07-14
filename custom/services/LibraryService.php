<?php
/**
 * Library Service
 *
 * File    : LibraryService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Seluruh query yang berhubungan dengan perpustakaan
 * (tabel bdt_library, bdt_library_gallery, bdt_library_facility,
 *  bdt_library_hour, dan integrasi SLiMS: mst_location, item).
 *
 * ATURAN:
 *   - Semua metode mengembalikan array PHP murni (bukan object).
 *   - Halaman PHP memanggil LibraryService, BUKAN Database langsung.
 *   - Tidak ada logika tampilan (HTML) di dalam kelas ini.
 *
 * Usage:
 *   require_once ROOT_PATH . '/custom/services/LibraryService.php';
 *   $service   = new LibraryService();
 *   $libraries = $service->getAllActive();
 */

require_once __DIR__ . '/../helpers/Database.php';

class LibraryService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Daftar Perpustakaan ────────────────────────────────────

    /**
     * Ambil semua perpustakaan dengan status aktif.
     * Digunakan di: halaman /perpustakaan, landing page.
     *
     * @return array
     */
    public function getAllActive(): array
    {
        return $this->db->fetchAll(
            'SELECT
                library_id,
                slug,
                name,
                tagline,
                badge,
                address,
                phone,
                cover_image,
                thumbnail_image,
                total_koleksi,
                total_anggota,
                status,
                sort_order
             FROM bdt_library
             WHERE status = ?
             ORDER BY sort_order ASC, name ASC',
            's',
            ['aktif']
        );
    }

    /**
     * Ambil perpustakaan unggulan (badge IS NOT NULL).
     * Digunakan di: landing page — seksi "Perpustakaan Unggulan".
     *
     * @param  int $limit
     * @return array
     */
    public function getFeatured(int $limit = 3): array
    {
        return $this->db->fetchAll(
            'SELECT
                library_id,
                slug,
                name,
                address,
                badge,
                thumbnail_image,
                total_koleksi
             FROM bdt_library
             WHERE status = ?
               AND badge  IS NOT NULL
             ORDER BY sort_order ASC
             LIMIT ?',
            'si',
            ['aktif', $limit]
        );
    }

    // ── Detail Perpustakaan ────────────────────────────────────

    /**
     * Ambil satu perpustakaan berdasarkan slug URL.
     * Digunakan di: halaman /perpustakaan/{slug}.
     *
     * @param  string    $slug
     * @return array|null
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM bdt_library WHERE slug = ? AND status != ? LIMIT 1',
            'ss',
            [$slug, 'nonaktif']
        );
    }

    /**
     * Ambil detail lengkap perpustakaan beserta relasi:
     * galeri, fasilitas, jam operasional, dan statistik dari SLiMS.
     * Digunakan di: halaman /perpustakaan/{slug}.
     *
     * @param  string    $slug
     * @return array|null  Array berisi key: library, gallery, facilities, hours, isOpenNow, stats
     */
    public function getDetailBySlug(string $slug): ?array
    {
        $library = $this->getBySlug($slug);
        if (!$library) {
            return null;
        }

        $libraryId  = (int) $library['library_id'];
        $locationId = $library['slims_location_id'];

        return [
            'library'    => $library,
            'gallery'    => $this->getGallery($libraryId),
            'facilities' => $this->getFacilities($libraryId),
            'hours'      => $this->getHours($libraryId),
            'isOpenNow'  => $this->isOpenNow($libraryId),
            'todayHour'  => $this->getTodayHour($libraryId),
            'stats'      => [
                'totalEksemplar' => $this->countActiveItems($locationId),
                'totalJudul'     => $this->countUniqueTitles($locationId),
                'totalKoleksi'   => (int) $library['total_koleksi'],
                'totalAnggota'   => (int) $library['total_anggota'],
            ],
        ];
    }

    // ── Galeri ────────────────────────────────────────────────

    /**
     * Ambil semua foto galeri suatu perpustakaan.
     *
     * @param  int $libraryId
     * @return array
     */
    public function getGallery(int $libraryId): array
    {
        return $this->db->fetchAll(
            'SELECT gallery_id, image_path, caption, alt_text, sort_order
             FROM bdt_library_gallery
             WHERE library_id = ?
             ORDER BY sort_order ASC, gallery_id ASC',
            'i',
            [$libraryId]
        );
    }

    // ── Fasilitas ─────────────────────────────────────────────

    /**
     * Ambil daftar fasilitas suatu perpustakaan.
     *
     * @param  int $libraryId
     * @return array
     */
    public function getFacilities(int $libraryId): array
    {
        return $this->db->fetchAll(
            'SELECT facility_id, icon, name, description, sort_order
             FROM bdt_library_facility
             WHERE library_id = ?
             ORDER BY sort_order ASC',
            'i',
            [$libraryId]
        );
    }

    // ── Jam Operasional ───────────────────────────────────────

    /**
     * Ambil jam operasional semua hari, diindeks oleh day_of_week.
     * 0=Minggu, 1=Senin, ..., 6=Sabtu.
     *
     * @param  int $libraryId
     * @return array  Array terindeks oleh day_of_week
     */
    public function getHours(int $libraryId): array
    {
        $rows = $this->db->fetchAll(
            'SELECT day_of_week, is_open, open_time, close_time, note
             FROM bdt_library_hour
             WHERE library_id = ?
             ORDER BY day_of_week ASC',
            'i',
            [$libraryId]
        );

        $indexed = [];
        foreach ($rows as $row) {
            $indexed[(int) $row['day_of_week']] = $row;
        }
        return $indexed;
    }

    /**
     * Ambil jam operasional hari ini.
     *
     * @param  int $libraryId
     * @return array|null
     */
    public function getTodayHour(int $libraryId): ?array
    {
        // date('w') = 0 (Minggu) s/d 6 (Sabtu)
        $todayDow = (int) date('w');

        return $this->db->fetchOne(
            'SELECT * FROM bdt_library_hour WHERE library_id = ? AND day_of_week = ? LIMIT 1',
            'ii',
            [$libraryId, $todayDow]
        );
    }

    /**
     * Cek apakah perpustakaan sedang buka berdasarkan jam sekarang.
     *
     * @param  int $libraryId
     * @return bool
     */
    public function isOpenNow(int $libraryId): bool
    {
        $todayDow = (int) date('w');
        $nowTime  = date('H:i:s');

        $row = $this->db->fetchOne(
            'SELECT open_time, close_time
             FROM bdt_library_hour
             WHERE library_id  = ?
               AND day_of_week = ?
               AND is_open     = 1
             LIMIT 1',
            'ii',
            [$libraryId, $todayDow]
        );

        if (!$row) {
            return false;
        }

        return $nowTime >= $row['open_time'] && $nowTime <= $row['close_time'];
    }

    // ── Integrasi SLiMS ───────────────────────────────────────

    /**
     * Hitung total eksemplar aktif dari tabel item SLiMS.
     * Status WD (Withdrawn) dan MIS (Missing) dikecualikan.
     *
     * @param  string $locationId  slims_location_id, contoh: 'PU'
     * @return int
     */
    public function countActiveItems(string $locationId): int
    {
        $count = $this->db->fetchScalar(
            'SELECT COUNT(*)
             FROM item
             WHERE location_id    = ?
               AND item_status_id NOT IN ("WD", "MIS")',
            's',
            [$locationId]
        );
        return (int) $count;
    }

    /**
     * Hitung judul unik yang tersedia di suatu lokasi.
     *
     * @param  string $locationId
     * @return int
     */
    public function countUniqueTitles(string $locationId): int
    {
        $count = $this->db->fetchScalar(
            'SELECT COUNT(DISTINCT biblio_id)
             FROM item
             WHERE location_id    = ?
               AND item_status_id NOT IN ("WD", "MIS")',
            's',
            [$locationId]
        );
        return (int) $count;
    }

    /**
     * Ambil statistik gabungan untuk ditampilkan di landing page
     * (total seluruh perpustakaan aktif).
     *
     * @return array  Array berisi totalKoleksi, totalAnggota, totalPerpustakaan
     */
    public function getOverallStats(): array
    {
        $row = $this->db->fetchOne(
            'SELECT
                COUNT(*)               AS total_perpustakaan,
                SUM(total_koleksi)     AS total_koleksi,
                SUM(total_anggota)     AS total_anggota
             FROM bdt_library
             WHERE status = ?',
            's',
            ['aktif']
        );

        return [
            'totalPerpustakaan' => (int) ($row['total_perpustakaan'] ?? 0),
            'totalKoleksi'      => (int) ($row['total_koleksi']      ?? 0),
            'totalAnggota'      => (int) ($row['total_anggota']      ?? 0),
        ];
    }

    /**
     * Sinkronisasi total_koleksi dan total_anggota dari SLiMS.
     * Dipanggil via cron job atau setelah transaksi SLiMS.
     *
     * @param  int $libraryId
     * @param  int $totalKoleksi
     * @param  int $totalAnggota
     * @return int  Baris terpengaruh
     */
    public function syncStats(int $libraryId, int $totalKoleksi, int $totalAnggota): int
    {
        return $this->db->execute(
            'UPDATE bdt_library
             SET total_koleksi = ?,
                 total_anggota = ?,
                 updated_at    = NOW()
             WHERE library_id  = ?',
            'iii',
            [$totalKoleksi, $totalAnggota, $libraryId]
        );
    }
}
