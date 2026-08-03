<?php
/**
 * Library Model
 *
 * File    : library-model.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Model untuk tabel bdt_library, bdt_library_gallery,
 * bdt_library_facility, bdt_library_hour.
 *
 * Juga mengintegrasikan query ke tabel SLiMS:
 *   - mst_location (branch info)
 *   - item          (eksemplar koleksi)
 *
 * Usage:
 *   require_once __DIR__ . '/../helpers/Database.php';
 *   require_once __DIR__ . '/../services/library-model.php';
 *
 *   $libraryModel = new LibraryModel();
 *   $libraries    = $libraryModel->getAll();
 *   $library      = $libraryModel->getBySlug('perpustakaan-utama');
 */

require_once __DIR__ . '/../helpers/Database.php';

class LibraryModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ============================================================
    // QUERY PERPUSTAKAAN
    // ============================================================

    /**
     * Ambil semua perpustakaan aktif, diurutkan berdasarkan sort_order.
     *
     * @param  string $status Filter status: 'aktif' | 'nonaktif' | 'all'
     * @return array
     */
    public function getAll(string $status = 'aktif'): array
    {
        $sql = 'SELECT * FROM bdt_library';

        if ($status !== 'all') {
            $sql .= ' WHERE status = ?';
            $sql .= ' ORDER BY sort_order ASC, name ASC';
            return $this->db->fetchAll($sql, [$status]);
        }

        $sql .= ' ORDER BY sort_order ASC, name ASC';
        return $this->db->fetchAll($sql);
    }

    /**
     * Ambil perpustakaan berdasarkan slug.
     *
     * @param  string     $slug
     * @return array|false
     */
    public function getBySlug(string $slug): array|false
    {
        return $this->db->fetchOne(
            'SELECT * FROM bdt_library WHERE slug = ? LIMIT 1',
            [$slug]
        );
    }

    /**
     * Ambil perpustakaan berdasarkan ID.
     *
     * @param  int        $libraryId
     * @return array|false
     */
    public function getById(int $libraryId): array|false
    {
        return $this->db->fetchOne(
            'SELECT * FROM bdt_library WHERE library_id = ? LIMIT 1',
            [$libraryId]
        );
    }

    /**
     * Ambil perpustakaan berdasarkan slims_location_id.
     *
     * @param  string     $locationId  Contoh: 'PU', 'TB', 'PD'
     * @return array|false
     */
    public function getByLocationId(string $locationId): array|false
    {
        return $this->db->fetchOne(
            'SELECT * FROM bdt_library WHERE slims_location_id = ? LIMIT 1',
            [$locationId]
        );
    }

    /**
     * Ambil perpustakaan unggulan (badge IS NOT NULL).
     *
     * @param  int   $limit
     * @return array
     */
    public function getFeatured(int $limit = 6): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM bdt_library
              WHERE status = "aktif"
                AND badge IS NOT NULL
              ORDER BY sort_order ASC
              LIMIT ?',
            [$limit]
        );
    }

    // ============================================================
    // GALERI
    // ============================================================

    /**
     * Ambil galeri foto suatu perpustakaan.
     *
     * @param  int   $libraryId
     * @return array
     */
    public function getGallery(int $libraryId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM bdt_library_gallery
              WHERE library_id = ?
              ORDER BY sort_order ASC, gallery_id ASC',
            [$libraryId]
        );
    }

    /**
     * Tambah foto ke galeri perpustakaan.
     *
     * @param  int    $libraryId
     * @param  string $imagePath
     * @param  string $caption
     * @param  string $altText
     * @param  int    $sortOrder
     * @return int    gallery_id baru
     */
    public function addGalleryPhoto(
        int $libraryId,
        string $imagePath,
        string $caption = '',
        string $altText = '',
        int $sortOrder = 0
    ): int {
        $this->db->execute(
            'INSERT INTO bdt_library_gallery
                (library_id, image_path, caption, alt_text, sort_order)
             VALUES (?, ?, ?, ?, ?)',
            [$libraryId, $imagePath, $caption, $altText, $sortOrder]
        );
        return (int) $this->db->lastInsertId();
    }

    // ============================================================
    // FASILITAS
    // ============================================================

    /**
     * Ambil daftar fasilitas suatu perpustakaan.
     *
     * @param  int   $libraryId
     * @return array
     */
    public function getFacilities(int $libraryId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM bdt_library_facility
              WHERE library_id = ?
              ORDER BY sort_order ASC',
            [$libraryId]
        );
    }

    // ============================================================
    // JAM OPERASIONAL
    // ============================================================

    /**
     * Ambil jam operasional suatu perpustakaan (semua hari).
     *
     * @param  int   $libraryId
     * @return array  Diindeks oleh day_of_week (0=Minggu ... 6=Sabtu)
     */
    public function getHours(int $libraryId): array
    {
        $rows = $this->db->fetchAll(
            'SELECT * FROM bdt_library_hour
              WHERE library_id = ?
              ORDER BY day_of_week ASC',
            [$libraryId]
        );

        // Konversi menjadi array terindeks
        $hours = [];
        foreach ($rows as $row) {
            $hours[(int) $row['day_of_week']] = $row;
        }
        return $hours;
    }

    /**
     * Cek apakah perpustakaan sedang buka sekarang.
     *
     * @param  int  $libraryId
     * @return bool
     */
    public function isOpenNow(int $libraryId): bool
    {
        // day_of_week: 0=Minggu, 1=Senin, ..., 6=Sabtu
        $todayDow  = (int) date('w');
        $nowTime   = date('H:i:s');

        $row = $this->db->fetchOne(
            'SELECT * FROM bdt_library_hour
              WHERE library_id   = ?
                AND day_of_week  = ?
                AND is_open      = 1
              LIMIT 1',
            [$libraryId, $todayDow]
        );

        if (!$row) {
            return false;
        }

        return $nowTime >= $row['open_time'] && $nowTime <= $row['close_time'];
    }

    /**
     * Ambil status operasional hari ini.
     *
     * @param  int         $libraryId
     * @return array|false
     */
    public function getTodayHour(int $libraryId): array|false
    {
        $todayDow = (int) date('w');

        return $this->db->fetchOne(
            'SELECT * FROM bdt_library_hour
              WHERE library_id  = ?
                AND day_of_week = ?
              LIMIT 1',
            [$libraryId, $todayDow]
        );
    }

    // ============================================================
    // INTEGRASI SLIMS — Koleksi & Eksemplar
    // ============================================================

    /**
     * Hitung total eksemplar aktif di suatu perpustakaan (dari tabel item SLiMS).
     * Item dengan status WD (Withdrawn) dan MIS (Missing) tidak dihitung.
     *
     * @param  string $locationId  slims_location_id, contoh: 'PU'
     * @return int
     */
    public function countActiveItems(string $locationId): int
    {
        $count = $this->db->fetchScalar(
            'SELECT COUNT(*) FROM item
              WHERE location_id    = ?
                AND item_status_id NOT IN ("WD", "MIS")',
            [$locationId]
        );
        return (int) $count;
    }

    /**
     * Hitung total judul unik di suatu perpustakaan (dari tabel biblio + item).
     *
     * @param  string $locationId
     * @return int
     */
    public function countUniqueTitles(string $locationId): int
    {
        $count = $this->db->fetchScalar(
            'SELECT COUNT(DISTINCT biblio_id) FROM item
              WHERE location_id    = ?
                AND item_status_id NOT IN ("WD", "MIS")',
            [$locationId]
        );
        return (int) $count;
    }

    /**
     * Ambil koleksi terbaru di suatu perpustakaan (join biblio + item).
     *
     * @param  string $locationId
     * @param  int    $limit
     * @return array
     */
    public function getLatestCollections(string $locationId, int $limit = 6): array
    {
        return $this->db->fetchAll(
            'SELECT DISTINCT
                b.biblio_id,
                b.title,
                b.isbn_issn,
                b.image,
                b.input_date,
                b.promoted
             FROM biblio b
             JOIN item i ON b.biblio_id = i.biblio_id
             WHERE i.location_id    = ?
               AND i.item_status_id NOT IN ("WD", "MIS")
               AND b.opac_hide      = 0
             ORDER BY b.input_date DESC
             LIMIT ?',
            [$locationId, $limit]
        );
    }

    /**
     * Ambil data lengkap perpustakaan beserta galeri, fasilitas, jam,
     * dan statistik koleksi dari SLiMS — untuk halaman detail.
     *
     * @param  string     $slug
     * @return array|null Array berisi 'library', 'gallery', 'facilities', 'hours', 'stats'
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
                'totalEksemplar'   => $this->countActiveItems($locationId),
                'totalJudul'       => $this->countUniqueTitles($locationId),
                'totalKoleksi'     => (int) $library['total_koleksi'],
                'totalAnggota'     => (int) $library['total_anggota'],
            ],
        ];
    }

    // ============================================================
    // WRITE — Insert / Update
    // ============================================================

    /**
     * Perbarui total_koleksi dan total_anggota (sinkronisasi dari SLiMS).
     * Dipanggil oleh cron job atau setelah transaksi SLiMS.
     *
     * @param  int $libraryId
     * @param  int $totalKoleksi
     * @param  int $totalAnggota
     * @return int Baris terpengaruh
     */
    public function syncStats(int $libraryId, int $totalKoleksi, int $totalAnggota): int
    {
        return $this->db->execute(
            'UPDATE bdt_library
                SET total_koleksi = ?,
                    total_anggota = ?,
                    updated_at    = NOW()
              WHERE library_id    = ?',
            [$totalKoleksi, $totalAnggota, $libraryId]
        );
    }
}

