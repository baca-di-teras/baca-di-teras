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
        return $this->getCatalog(['sort' => 'terbaru'], $limit, 0);
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
        $conditions = [
            'b.opac_hide = 0',
            'lib.status = "aktif"',
        ];
        $types      = '';
        $params     = [];

        // Filter pencarian teks
        if (!empty($filters['q'])) {
            $conditions[] = '(b.title LIKE ? OR b.isbn_issn LIKE ? OR a.author_name LIKE ? OR p.publisher_name LIKE ?)';
            $likeQ  = '%' . $filters['q'] . '%';
            $types .= 'ssss';
            $params = array_merge($params, [$likeQ, $likeQ, $likeQ, $likeQ]);
        }

        $categoryIds = $this->normalizeIntList($filters['category'] ?? []);
        if (!empty($categoryIds)) {
            $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
            $conditions[] = "EXISTS (
                SELECT 1
                FROM biblio_topic btf
                WHERE btf.biblio_id = b.biblio_id
                  AND btf.topic_id IN ({$placeholders})
            )";
            $types .= str_repeat('i', count($categoryIds));
            $params = array_merge($params, $categoryIds);
        }

        // Filter lokasi perpustakaan
        if (!empty($filters['location'])) {
            $conditions[] = 'i.location_id = ?';
            $types       .= 's';
            $params[]     = $filters['location'];
        }

        if (!empty($filters['publisher']) && (int) $filters['publisher'] > 0) {
            $conditions[] = 'b.publisher_id = ?';
            $types       .= 'i';
            $params[]     = (int) $filters['publisher'];
        }

        // Filter status ketersediaan
        $statusList = $this->normalizeStringList($filters['status'] ?? []);
        if (!empty($statusList)) {
            $statusConditions = [];
            if (in_array('tersedia', $statusList, true)) {
                $statusConditions[] = 'i.item_status_id = "AVL"';
            }
            if (in_array('dipinjam', $statusList, true)) {
                $statusConditions[] = 'i.item_status_id = "LO"';
            }
            if (in_array('dipesan', $statusList, true)) {
                $statusConditions[] = 'EXISTS (SELECT 1 FROM reserve rsv WHERE rsv.biblio_id = b.biblio_id)';
            }
            if (!empty($statusConditions)) {
                $conditions[] = '(' . implode(' OR ', $statusConditions) . ')';
            }
        }

        $where = implode(' AND ', $conditions);

        $orderBy = match ($filters['sort'] ?? 'terbaru') {
            'a-z'       => 'b.title ASC, b.biblio_id ASC',
            'z-a'       => 'b.title DESC, b.biblio_id DESC',
            'terpopuler'=> 'total_eksemplar DESC, b.title ASC',
            default     => 'b.input_date DESC, b.biblio_id DESC',
        };

        $sql = "SELECT
                    b.biblio_id,
                    b.title,
                    b.isbn_issn,
                    b.image,
                    b.input_date,
                    b.promoted,
                    b.call_number,
                    GROUP_CONCAT(DISTINCT a.author_name ORDER BY ba.level SEPARATOR ', ') AS author,
                    p.publisher_name AS publisher,
                    b.publisher_id,
                    b.publish_year,
                    GROUP_CONCAT(DISTINCT t.topic ORDER BY bt.level, t.topic SEPARATOR ', ') AS topics,
                    SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT t.topic ORDER BY bt.level, t.topic SEPARATOR '||'), '||', 1) AS primary_topic,
                    GROUP_CONCAT(DISTINCT lib.slug ORDER BY lib.sort_order, lib.name SEPARATOR ',') AS library_slugs,
                    GROUP_CONCAT(DISTINCT lib.name ORDER BY lib.sort_order, lib.name SEPARATOR ', ') AS library_names,
                    GROUP_CONCAT(DISTINCT i.location_id ORDER BY i.location_id SEPARATOR ',') AS location_ids,
                    MIN(lib.slug) AS library_slug,
                    MIN(lib.name) AS library_name,
                    COUNT(DISTINCT i.item_id) AS total_eksemplar,
                    COUNT(DISTINCT CASE WHEN i.item_status_id = 'AVL' THEN i.item_id END) AS tersedia,
                    COUNT(DISTINCT CASE WHEN i.item_status_id = 'LO' THEN i.item_id END) AS dipinjam,
                    COUNT(DISTINCT r.reserve_id) AS dipesan
                FROM biblio b
                LEFT JOIN biblio_author ba ON b.biblio_id  = ba.biblio_id AND ba.level = 1
                LEFT JOIN mst_author    a  ON ba.author_id = a.author_id
                LEFT JOIN mst_publisher p  ON b.publisher_id = p.publisher_id
                LEFT JOIN biblio_topic bt ON b.biblio_id = bt.biblio_id
                LEFT JOIN mst_topic t ON bt.topic_id = t.topic_id
                JOIN item i ON b.biblio_id = i.biblio_id
                JOIN bdt_library lib ON lib.slims_location_id = i.location_id
                LEFT JOIN reserve r ON r.biblio_id = b.biblio_id
                WHERE {$where}
                GROUP BY b.biblio_id
                ORDER BY {$orderBy}
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
        $conditions = [
            'b.opac_hide = 0',
            'lib.status = "aktif"',
        ];
        $types      = '';
        $params     = [];

        if (!empty($filters['q'])) {
            $conditions[] = '(b.title LIKE ? OR b.isbn_issn LIKE ? OR a.author_name LIKE ? OR p.publisher_name LIKE ?)';
            $likeQ  = '%' . $filters['q'] . '%';
            $types .= 'ssss';
            $params = array_merge($params, [$likeQ, $likeQ, $likeQ, $likeQ]);
        }

        $categoryIds = $this->normalizeIntList($filters['category'] ?? []);
        if (!empty($categoryIds)) {
            $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
            $conditions[] = "EXISTS (
                SELECT 1
                FROM biblio_topic btf
                WHERE btf.biblio_id = b.biblio_id
                  AND btf.topic_id IN ({$placeholders})
            )";
            $types .= str_repeat('i', count($categoryIds));
            $params = array_merge($params, $categoryIds);
        }

        if (!empty($filters['location'])) {
            $conditions[] = 'i.location_id = ?';
            $types       .= 's';
            $params[]     = $filters['location'];
        }

        if (!empty($filters['publisher']) && (int) $filters['publisher'] > 0) {
            $conditions[] = 'b.publisher_id = ?';
            $types       .= 'i';
            $params[]     = (int) $filters['publisher'];
        }

        $statusList = $this->normalizeStringList($filters['status'] ?? []);
        if (!empty($statusList)) {
            $statusConditions = [];
            if (in_array('tersedia', $statusList, true)) {
                $statusConditions[] = 'i.item_status_id = "AVL"';
            }
            if (in_array('dipinjam', $statusList, true)) {
                $statusConditions[] = 'i.item_status_id = "LO"';
            }
            if (in_array('dipesan', $statusList, true)) {
                $statusConditions[] = 'EXISTS (SELECT 1 FROM reserve rsv WHERE rsv.biblio_id = b.biblio_id)';
            }
            if (!empty($statusConditions)) {
                $conditions[] = '(' . implode(' OR ', $statusConditions) . ')';
            }
        }

        $where    = implode(' AND ', $conditions);

        $count = $this->db->fetchScalar(
            "SELECT COUNT(DISTINCT b.biblio_id)
             FROM biblio b
             LEFT JOIN biblio_author ba ON b.biblio_id  = ba.biblio_id AND ba.level = 1
             LEFT JOIN mst_author    a  ON ba.author_id = a.author_id
             LEFT JOIN mst_publisher p ON b.publisher_id = p.publisher_id
             JOIN item i ON b.biblio_id = i.biblio_id
             JOIN bdt_library lib ON lib.slims_location_id = i.location_id
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
        $baseUrl  = defined('BASE_URL') ? BASE_URL : '';

        // URL cover: gunakan gambar SLiMS jika ada, fallback ke placeholder
        $imageFile = trim((string) ($row['image'] ?? ''));
        $imageName = $imageFile !== '' ? basename($imageFile) : '';
        $imagePath = defined('ROOT_PATH') && $imageName !== ''
            ? ROOT_PATH . '/slims/images/docs/' . $imageName
            : '';

        $coverImage = ($imageName !== '' && $imagePath !== '' && is_file($imagePath))
            ? $this->coverBaseUrl . rawurlencode($imageName)
            : $baseUrl . '/custom/assets/images/book-cover-1.png';

        // URL halaman detail — gunakan biblio_id sebagai identifier
        $detailHref = '/katalog/' . $biblioId;

        $topics = [];
        if (!empty($row['topics'])) {
            $topics = array_values(array_filter(array_map('trim', explode(',', (string) $row['topics']))));
        }

        $availableCount = (int) ($row['tersedia'] ?? 0);
        $loanedCount    = (int) ($row['dipinjam'] ?? 0);
        $reservedCount  = (int) ($row['dipesan'] ?? 0);
        $availability   = 'tersedia';
        if ($availableCount <= 0 && $reservedCount > 0) {
            $availability = 'dipesan';
        } elseif ($availableCount <= 0 && $loanedCount > 0) {
            $availability = 'dipinjam';
        }

        return [
            'id'            => $biblioId,
            'title'         => $row['title']        ?? '',
            'author'        => $row['author']        ?? 'Tidak diketahui',
            'publisher'     => $row['publisher']     ?? '',
            'publisherId'   => (int) ($row['publisher_id'] ?? 0),
            'publishYear'   => $row['publish_year']  ?? '',
            'image'         => $coverImage,
            'href'          => $detailHref,
            'isbn'          => $row['isbn_issn']     ?? '',
            'callNumber'    => $row['call_number']   ?? '',
            'classification'=> $row['classification'] ?? '',
            'gmd'           => $row['gmd'] ?? '',
            'language'      => $row['language'] ?? '',
            'notes'         => $row['notes'] ?? '',
            'edition'       => $row['edition'] ?? '',
            'collation'     => $row['collation'] ?? '',
            'seriesTitle'   => $row['series_title'] ?? '',
            'publishPlace'  => $row['publish_place'] ?? '',
            'badge'         => ($row['promoted'] ?? 0) ? 'Unggulan' : null,
            'inputDate'     => $row['input_date']    ?? '',
            'category'      => $row['primary_topic'] ?? ($topics[0] ?? ''),
            'topics'        => $topics,
            'perpustakaan'  => $row['library_slug'] ?? '',
            'libraryName'   => $row['library_name'] ?? '',
            'libraryNames'  => $row['library_names'] ?? '',
            'locationId'    => $row['location_ids'] ?? '',
            'locationIds'   => !empty($row['location_ids']) ? explode(',', (string) $row['location_ids']) : [],
            'ketersediaan'  => $availability,
            'stok'          => $availableCount,
            'totalStok'     => (int) ($row['total_eksemplar'] ?? $availableCount),
            'antrian'       => $reservedCount,
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
            'SELECT DISTINCT t.topic_id AS id, t.topic AS label
             FROM mst_topic t
             JOIN biblio_topic bt ON bt.topic_id = t.topic_id
             JOIN biblio b ON b.biblio_id = bt.biblio_id
             JOIN item i ON i.biblio_id = b.biblio_id
             JOIN bdt_library lib ON lib.slims_location_id = i.location_id
             WHERE b.opac_hide = 0
               AND lib.status = "aktif"
             ORDER BY t.topic ASC
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
            'SELECT DISTINCT p.publisher_id AS id, p.publisher_name AS label
             FROM mst_publisher p
             JOIN biblio b ON b.publisher_id = p.publisher_id
             JOIN item i ON i.biblio_id = b.biblio_id
             JOIN bdt_library lib ON lib.slims_location_id = i.location_id
             WHERE b.opac_hide = 0
               AND lib.status = "aktif"
               AND p.publisher_name IS NOT NULL
               AND p.publisher_name != ""
             ORDER BY p.publisher_name ASC
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

    private function normalizeIntList(mixed $value): array
    {
        $values = is_array($value) ? $value : [$value];
        $normalized = [];
        foreach ($values as $item) {
            $intValue = (int) $item;
            if ($intValue > 0) {
                $normalized[] = $intValue;
            }
        }
        return array_values(array_unique($normalized));
    }

    private function normalizeStringList(mixed $value): array
    {
        $values = is_array($value) ? $value : [$value];
        $allowed = ['tersedia', 'dipesan', 'dipinjam'];
        $normalized = [];
        foreach ($values as $item) {
            $stringValue = trim((string) $item);
            if (in_array($stringValue, $allowed, true)) {
                $normalized[] = $stringValue;
            }
        }
        return array_values(array_unique($normalized));
    }
}
