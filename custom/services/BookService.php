<?php
/**
 * Book Service
 *
 * File    : BookService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Seluruh query yang berhubungan dengan koleksi buku.
 * Menggunakan tabel SLiMS: biblio, item, author, biblio_author,
 * mst_publisher, mst_location, mst_coll_type, mst_gmd.
 *
 * ATURAN:
 *   - Tidak ada SQL mentah di halaman PHP.
 *   - Kembalikan selalu array PHP yang siap dipakai view.
 *   - Data diformat agar kompatibel dengan komponen book-card.php.
 *
 * Usage:
 *   require_once ROOT_PATH . '/custom/services/BookService.php';
 *   $service = new BookService();
 *   $books   = $service->getLatest(6);
 *   $book    = $service->getByBiblioId(42);
 */

require_once __DIR__ . '/../helpers/Database.php';

class BookService
{
    private Database $db;

    /** Base URL gambar cover SLiMS */
    private string $coverBaseUrl;

    public function __construct()
    {
        $this->db           = Database::getInstance();
        $baseUrl            = defined('BASE_URL') ? BASE_URL : '';
        $this->coverBaseUrl = $baseUrl . '/slims/images/docs/';
    }

    // ── Koleksi Baru / Unggulan ────────────────────────────────

    /**
     * Ambil koleksi buku terbaru dari SLiMS.
     * Digunakan di: landing page — seksi "Koleksi Terbaru".
     *
     * @param  int $limit
     * @return array
     */
    public function getLatest(int $limit = 6): array
    {
        $rows = $this->db->fetchAll(
            'SELECT
                b.biblio_id,
                b.title,
                b.isbn_issn,
                b.image,
                b.input_date,
                b.promoted,
                b.call_number,
                GROUP_CONCAT(a.author_name ORDER BY ba.level SEPARATOR ", ") AS author,
                p.publisher_name AS publisher,
                b.publish_year
             FROM biblio b
             LEFT JOIN biblio_author ba ON b.biblio_id  = ba.biblio_id AND ba.level = 1
             LEFT JOIN mst_author    a  ON ba.author_id = a.author_id
             LEFT JOIN mst_publisher p  ON b.publisher_id = p.publisher_id
             WHERE b.opac_hide = 0
             GROUP BY b.biblio_id
             ORDER BY b.input_date DESC
             LIMIT ?',
            'i',
            [$limit]
        );

        return array_map([$this, 'formatBook'], $rows);
    }

    /**
     * Ambil buku yang ditandai "promoted" di SLiMS.
     * Digunakan di: landing page — seksi "Koleksi Unggulan".
     *
     * @param  int $limit
     * @return array
     */
    public function getPromoted(int $limit = 6): array
    {
        $rows = $this->db->fetchAll(
            'SELECT
                b.biblio_id,
                b.title,
                b.isbn_issn,
                b.image,
                b.input_date,
                b.promoted,
                b.call_number,
                GROUP_CONCAT(a.author_name ORDER BY ba.level SEPARATOR ", ") AS author,
                p.publisher_name AS publisher,
                b.publish_year
             FROM biblio b
             LEFT JOIN biblio_author ba ON b.biblio_id  = ba.biblio_id AND ba.level = 1
             LEFT JOIN mst_author    a  ON ba.author_id = a.author_id
             LEFT JOIN mst_publisher p  ON b.publisher_id = p.publisher_id
             WHERE b.opac_hide = 0
               AND b.promoted  = 1
             GROUP BY b.biblio_id
             ORDER BY b.input_date DESC
             LIMIT ?',
            'i',
            [$limit]
        );

        return array_map([$this, 'formatBook'], $rows);
    }

    // ── Katalog & Filter ──────────────────────────────────────

    /**
     * Ambil daftar buku dengan filter, pencarian, dan pagination.
     * Digunakan di: halaman /katalog.
     *
     * @param  array $filters  ['category' => ..., 'location' => ..., 'status' => ..., 'q' => ...]
     * @param  int   $limit
     * @param  int   $offset
     * @return array
     */
    public function getCatalog(array $filters = [], int $limit = 12, int $offset = 0): array
    {
        $conditions = ['b.opac_hide = 0'];
        $types      = '';
        $params     = [];

        // Filter pencarian teks
        if (!empty($filters['q'])) {
            $conditions[] = '(b.title LIKE ? OR a.author_name LIKE ? OR p.publisher_name LIKE ?)';
            $likeQ  = '%' . $filters['q'] . '%';
            $types .= 'sss';
            $params = array_merge($params, [$likeQ, $likeQ, $likeQ]);
        }

        // Filter lokasi perpustakaan
        if (!empty($filters['location'])) {
            $conditions[] = 'i.location_id = ?';
            $types       .= 's';
            $params[]     = $filters['location'];
        }

        // Filter status ketersediaan
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'tersedia') {
                $conditions[] = 'i.item_status_id = "AVL"';
            } elseif ($filters['status'] === 'dipinjam') {
                $conditions[] = 'i.item_status_id = "LO"';
            }
        }

        $where = implode(' AND ', $conditions);

        // Tambahkan JOIN item jika ada filter lokasi/status
        $itemJoin = (!empty($filters['location']) || !empty($filters['status']))
            ? 'JOIN item i ON b.biblio_id = i.biblio_id'
            : 'LEFT JOIN item i ON b.biblio_id = i.biblio_id';

        $sql = "SELECT DISTINCT
                    b.biblio_id,
                    b.title,
                    b.isbn_issn,
                    b.image,
                    b.input_date,
                    b.promoted,
                    b.call_number,
                    GROUP_CONCAT(DISTINCT a.author_name ORDER BY ba.level SEPARATOR ', ') AS author,
                    p.publisher_name AS publisher,
                    b.publish_year
                FROM biblio b
                LEFT JOIN biblio_author ba ON b.biblio_id  = ba.biblio_id AND ba.level = 1
                LEFT JOIN mst_author    a  ON ba.author_id = a.author_id
                LEFT JOIN mst_publisher p  ON b.publisher_id = p.publisher_id
                {$itemJoin}
                WHERE {$where}
                GROUP BY b.biblio_id
                ORDER BY b.input_date DESC
                LIMIT ? OFFSET ?";

        $types  .= 'ii';
        $params  = array_merge($params, [$limit, $offset]);

        $rows = $this->db->fetchAll($sql, $types, $params);
        return array_map([$this, 'formatBook'], $rows);
    }

    /**
     * Hitung total buku di katalog (untuk pagination).
     *
     * @param  array $filters
     * @return int
     */
    public function countCatalog(array $filters = []): int
    {
        $conditions = ['b.opac_hide = 0'];
        $types      = '';
        $params     = [];

        if (!empty($filters['q'])) {
            $conditions[] = '(b.title LIKE ? OR a.author_name LIKE ?)';
            $likeQ  = '%' . $filters['q'] . '%';
            $types .= 'ss';
            $params = array_merge($params, [$likeQ, $likeQ]);
        }

        if (!empty($filters['location'])) {
            $conditions[] = 'i.location_id = ?';
            $types       .= 's';
            $params[]     = $filters['location'];
        }

        $where    = implode(' AND ', $conditions);
        $itemJoin = !empty($filters['location'])
            ? 'JOIN item i ON b.biblio_id = i.biblio_id'
            : 'LEFT JOIN item i ON b.biblio_id = i.biblio_id';

        $count = $this->db->fetchScalar(
            "SELECT COUNT(DISTINCT b.biblio_id)
             FROM biblio b
             LEFT JOIN biblio_author ba ON b.biblio_id  = ba.biblio_id AND ba.level = 1
             LEFT JOIN mst_author    a  ON ba.author_id = a.author_id
             {$itemJoin}
             WHERE {$where}",
            $types,
            $params
        );

        return (int) $count;
    }

    // ── Detail Buku ───────────────────────────────────────────

    /**
     * Ambil detail satu buku berdasarkan biblio_id SLiMS.
     * Digunakan di: halaman /katalog/{slug}.
     *
     * @param  int       $biblioId
     * @return array|null
     */
    public function getByBiblioId(int $biblioId): ?array
    {
        $row = $this->db->fetchOne(
            'SELECT
                b.biblio_id,
                b.title,
                b.isbn_issn,
                b.image,
                b.edition,
                b.collation,
                b.series_title,
                b.call_number,
                b.classification,
                b.notes,
                b.publish_year,
                b.input_date,
                b.promoted,
                b.opac_hide,
                GROUP_CONCAT(DISTINCT a.author_name ORDER BY ba.level SEPARATOR ", ") AS author,
                p.publisher_name AS publisher,
                pl.place_name    AS publish_place,
                g.gmd_name       AS gmd,
                l.language_name  AS language
             FROM biblio b
             LEFT JOIN biblio_author ba ON b.biblio_id    = ba.biblio_id AND ba.level = 1
             LEFT JOIN mst_author    a  ON ba.author_id   = a.author_id
             LEFT JOIN mst_publisher p  ON b.publisher_id  = p.publisher_id
             LEFT JOIN mst_place     pl ON b.publish_place_id = pl.place_id
             LEFT JOIN mst_gmd       g  ON b.gmd_id        = g.gmd_id
             LEFT JOIN mst_language  l  ON b.language_id   = l.language_id
             WHERE b.biblio_id = ?
               AND b.opac_hide = 0
             GROUP BY b.biblio_id
             LIMIT 1',
            'i',
            [$biblioId]
        );

        if (!$row) {
            return null;
        }

        $row = $this->formatBook($row);

        // Tambahkan topik / subjek
        $row['topics'] = $this->getTopics($biblioId);

        // Tambahkan ketersediaan per lokasi
        $row['availability'] = $this->getAvailability($biblioId);

        return $row;
    }

    /**
     * Ambil topik/subjek suatu buku dari SLiMS.
     *
     * @param  int $biblioId
     * @return array  Array string topik
     */
    public function getTopics(int $biblioId): array
    {
        $rows = $this->db->fetchAll(
            'SELECT t.topic
             FROM mst_topic    t
             JOIN biblio_topic bt ON t.topic_id = bt.topic_id
             WHERE bt.biblio_id = ?
             ORDER BY bt.level ASC',
            'i',
            [$biblioId]
        );

        return array_column($rows, 'topic');
    }

    /**
     * Ambil ketersediaan eksemplar buku per lokasi.
     * Menggabungkan data SLiMS (item + mst_location) dengan
     * profil perpustakaan dari bdt_library.
     *
     * @param  int $biblioId
     * @return array
     */
    public function getAvailability(int $biblioId): array
    {
        return $this->db->fetchAll(
            'SELECT
                i.item_id,
                i.item_code,
                i.item_status_id,
                ist.item_status_name,
                ml.location_id,
                ml.location_name,
                lib.name        AS library_name,
                lib.slug        AS library_slug,
                lib.address     AS library_address,
                lib.google_maps_url,
                COUNT(i.item_id) OVER (PARTITION BY i.location_id) AS total_di_lokasi,
                SUM(CASE WHEN i.item_status_id = "AVL" THEN 1 ELSE 0 END)
                    OVER (PARTITION BY i.location_id) AS tersedia_di_lokasi
             FROM item i
             JOIN mst_location  ml  ON i.location_id   = ml.location_id
             JOIN mst_item_status ist ON i.item_status_id = ist.item_status_id
             LEFT JOIN bdt_library lib ON lib.slims_location_id = i.location_id
             WHERE i.biblio_id = ?
             ORDER BY ml.location_name ASC',
            'i',
            [$biblioId]
        );
    }

    // ── Rekomendasi ───────────────────────────────────────────

    /**
     * Ambil buku terkait berdasarkan topik yang sama.
     * Digunakan di: halaman detail buku — seksi "Pembaca juga menyukai".
     *
     * @param  int $biblioId   ID buku saat ini (dikecualikan)
     * @param  int $limit
     * @return array
     */
    public function getRelated(int $biblioId, int $limit = 5): array
    {
        $rows = $this->db->fetchAll(
            'SELECT DISTINCT
                b.biblio_id,
                b.title,
                b.image,
                b.promoted,
                b.input_date,
                GROUP_CONCAT(DISTINCT a.author_name SEPARATOR ", ") AS author,
                p.publisher_name AS publisher
             FROM biblio b
             LEFT JOIN biblio_author ba ON b.biblio_id  = ba.biblio_id AND ba.level = 1
             LEFT JOIN mst_author    a  ON ba.author_id = a.author_id
             LEFT JOIN mst_publisher p  ON b.publisher_id = p.publisher_id
             WHERE b.biblio_id != ?
               AND b.opac_hide  = 0
               AND b.biblio_id IN (
                   SELECT DISTINCT bt2.biblio_id
                   FROM biblio_topic bt2
                   WHERE bt2.topic_id IN (
                       SELECT topic_id FROM biblio_topic WHERE biblio_id = ?
                   )
               )
             GROUP BY b.biblio_id
             ORDER BY b.input_date DESC
             LIMIT ?',
            'iii',
            [$biblioId, $biblioId, $limit]
        );

        return array_map([$this, 'formatBook'], $rows);
    }

    // ── Format Helper ─────────────────────────────────────────

    /**
     * Format data mentah SLiMS menjadi array standar
     * yang siap digunakan oleh komponen book-card.php dan halaman detail.
     *
     * @param  array $row  Baris mentah dari database
     * @return array
     */
    private function formatBook(array $row): array
    {
        $biblioId = (int) $row['biblio_id'];

        // URL cover: gunakan gambar SLiMS jika ada, fallback ke placeholder
        $coverImage = !empty($row['image'])
            ? $this->coverBaseUrl . $row['image']
            : (defined('BASE_URL') ? BASE_URL : '') . '/custom/assets/images/book-placeholder.png';

        // URL halaman detail — gunakan biblio_id sebagai identifier
        $detailHref = '/katalog/' . $biblioId;

        return [
            'id'             => $biblioId,
            'title'          => $row['title']          ?? '',
            'author'         => $row['author']         ?? 'Tidak diketahui',
            'publisher'      => $row['publisher']      ?? '',
            'publishYear'    => $row['publish_year']   ?? '',
            'image'          => $coverImage,
            'href'           => $detailHref,
            'isbn'           => $row['isbn_issn']      ?? '',
            'callNumber'     => $row['call_number']    ?? '',
            'classification' => $row['classification'] ?? '',
            'gmd'            => $row['gmd']            ?? '',
            'language'       => $row['language']       ?? '',
            'badge'          => ($row['promoted'] ?? 0) ? 'Unggulan' : null,
            'inputDate'      => $row['input_date']     ?? '',
        ];
    }

    /**
     * Ambil daftar kategori/topik buku untuk dropdown filter.
     * Mengembalikan array dengan format ['id' => ..., 'label' => ...]
     *
     * @return array
     */
    public function getCategories(): array
    {
        $rows = $this->db->fetchAll(
            'SELECT topic_id AS id, topic AS label 
             FROM mst_topic 
             ORDER BY topic ASC 
             LIMIT 100'
        );
        return $rows;
    }

    /**
     * Ambil daftar penerbit buku untuk dropdown filter.
     * Mengembalikan array dengan format ['id' => ..., 'label' => ...]
     *
     * @return array
     */
    public function getPublishers(): array
    {
        $rows = $this->db->fetchAll(
            'SELECT publisher_id AS id, publisher_name AS label 
             FROM mst_publisher 
             ORDER BY publisher_name ASC 
             LIMIT 100'
        );
        return $rows;
    }

    /**
     * Ambil daftar penulis buku.
     * Mengembalikan array dengan format ['id' => ..., 'label' => ...]
     *
     * @return array
     */
    public function getAuthors(): array
    {
        $rows = $this->db->fetchAll(
            'SELECT author_id AS id, author_name AS label 
             FROM mst_author 
             ORDER BY author_name ASC 
             LIMIT 100'
        );
        return $rows;
    }
}
