<?php
/**
 * Library Service
 *
 * File    : LibraryService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 */

require_once __DIR__ . '/../helpers/Database.php';
require_once __DIR__ . '/ActivityLogService.php';

class LibraryService
{
    private Database $db;
    private ActivityLogService $activityLog;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->activityLog = new ActivityLogService();
    }

    public function getAllLibraries(): array
    {
        $sql = "SELECT * FROM bdt_library ORDER BY sort_order ASC, library_id DESC";
        return $this->db->fetchAll($sql);
    }

    public function getAllActive(): array
    {
        $sql = "SELECT * FROM bdt_library WHERE status = 'aktif' ORDER BY sort_order ASC, name ASC";
        return $this->db->fetchAll($sql);
    }

    public function getFeatured(int $limit = 3): array
    {
        $sql = "SELECT * FROM bdt_library WHERE status = 'aktif' AND badge IS NOT NULL ORDER BY sort_order ASC LIMIT ?";
        return $this->db->fetchAll($sql, 'i', [$limit]);
    }

    public function getOverallStats(): array
    {
        $sql = "SELECT 
                    COUNT(DISTINCT l.library_id) as totalPerpustakaan, 
                    SUM(l.total_anggota) as totalAnggota,
                    (SELECT COUNT(i.item_id) 
                     FROM item i 
                     JOIN bdt_library lib ON i.location_id = lib.slims_location_id
                     WHERE i.item_status_id NOT IN ('WD', 'MIS') AND lib.status = 'aktif') as totalKoleksi
                FROM bdt_library l 
                WHERE l.status = 'aktif'";
        $row = $this->db->fetchOne($sql);
        return $row ?: ['totalPerpustakaan' => 0, 'totalKoleksi' => 0, 'totalAnggota' => 0];
    }

    public function getLibraryById(int $library_id): ?array
    {
        $sql = "SELECT * FROM bdt_library WHERE library_id = ? LIMIT 1";
        $stmt = $this->db->getConnection()->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param('i', $library_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $library = $result->fetch_assoc();
        $stmt->close();

        return $library;
    }

    public function createLibrary(array $data): bool
    {
        $sql = "INSERT INTO bdt_library (slims_location_id, slug, name, tagline, badge, status, address, village, phone, email, whatsapp, google_maps_url, latitude, longitude, cover_image, description, sort_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->getConnection()->prepare($sql);
        if (!$stmt) return false;

        $slims_location_id = $data['slims_location_id'] ?? '';
        $name = $data['name'];
        $slug = $data['slug'] ?? $this->generateSlug($name);
        $tagline = $data['tagline'] ?? null;
        $badge = $data['badge'] ?? null;
        $status = $data['status'] ?? 'aktif';
        $address = $data['address'] ?? null;
        $village = $data['village'] ?? null;
        $phone = $data['phone'] ?? null;
        $email = $data['email'] ?? null;
        $whatsapp = $data['whatsapp'] ?? null;
        $google_maps_url = $data['google_maps_url'] ?? null;
        $latitude = $data['latitude'] ?? null;
        $longitude = $data['longitude'] ?? null;
        $cover_image = $data['cover_image'] ?? null;
        $description = $data['description'] ?? null;
        $sort_order = $data['sort_order'] ?? 0;

        $stmt->bind_param('ssssssssssssddssi', 
            $slims_location_id, $slug, $name, $tagline, $badge, $status, 
            $address, $village, $phone, $email, $whatsapp, $google_maps_url, 
            $latitude, $longitude, $cover_image, $description, $sort_order
        );
        
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result) {
            $this->activityLog->log('menambahkan', 'Perpustakaan', $name);
        }

        return $result;
    }

    public function updateLibrary(int $library_id, array $data): bool
    {
        $sql = "UPDATE bdt_library 
                SET slims_location_id = ?, slug = ?, name = ?, tagline = ?, badge = ?, status = ?, address = ?, village = ?, phone = ?, email = ?, whatsapp = ?, google_maps_url = ?, latitude = ?, longitude = ?, cover_image = ?, description = ?, sort_order = ? 
                WHERE library_id = ?";
        
        $stmt = $this->db->getConnection()->prepare($sql);
        if (!$stmt) return false;

        $slims_location_id = $data['slims_location_id'] ?? '';
        $name = $data['name'];
        $slug = $data['slug'] ?? $this->generateSlug($name);
        $tagline = $data['tagline'] ?? null;
        $badge = $data['badge'] ?? null;
        $status = $data['status'] ?? 'aktif';
        $address = $data['address'] ?? null;
        $village = $data['village'] ?? null;
        $phone = $data['phone'] ?? null;
        $email = $data['email'] ?? null;
        $whatsapp = $data['whatsapp'] ?? null;
        $google_maps_url = $data['google_maps_url'] ?? null;
        $latitude = $data['latitude'] ?? null;
        $longitude = $data['longitude'] ?? null;
        $cover_image = $data['cover_image'] ?? null;
        $description = $data['description'] ?? null;
        $sort_order = $data['sort_order'] ?? 0;

        $stmt->bind_param('ssssssssssssddssii', 
            $slims_location_id, $slug, $name, $tagline, $badge, $status, 
            $address, $village, $phone, $email, $whatsapp, $google_maps_url, 
            $latitude, $longitude, $cover_image, $description, $sort_order,
            $library_id
        );
        
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result) {
            $this->activityLog->log('mengubah', 'Perpustakaan', $name);
        }

        return $result;
    }

    public function deleteLibrary(int $library_id): bool
    {
        $library = $this->getLibraryById($library_id);
        $name = $library ? $library['name'] : "ID: $library_id";

        $sql = "DELETE FROM bdt_library WHERE library_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('i', $library_id);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            $this->activityLog->log('menghapus', 'Perpustakaan', $name);
        }

        return $result;
    }

    public function getBySlug(string $slug): array|false
    {
        return $this->db->fetchOne(
            'SELECT * FROM bdt_library WHERE slug = ? LIMIT 1',
            's',
            [$slug]
        );
    }

    public function getGallery(int $libraryId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM bdt_library_gallery
              WHERE library_id = ?
              ORDER BY sort_order ASC, gallery_id ASC',
            'i',
            [$libraryId]
        );
    }

    public function getFacilities(int $libraryId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM bdt_library_facility
              WHERE library_id = ?
              ORDER BY sort_order ASC',
            'i',
            [$libraryId]
        );
    }

    public function getHours(int $libraryId): array
    {
        $rows = $this->db->fetchAll(
            'SELECT * FROM bdt_library_hour
              WHERE library_id = ?
              ORDER BY day_of_week ASC',
            'i',
            [$libraryId]
        );

        $hours = [];
        foreach ($rows as $row) {
            $hours[(int) $row['day_of_week']] = $row;
        }
        return $hours;
    }

    public function isOpenNow(int $libraryId): bool
    {
        $todayDow  = (int) date('w');
        $nowTime   = date('H:i:s');

        $row = $this->db->fetchOne(
            'SELECT * FROM bdt_library_hour
              WHERE library_id   = ?
                AND day_of_week  = ?
                AND is_open      = 1
              LIMIT 1',
            'ii',
            [$libraryId, $todayDow]
        );

        if (!$row) {
            return false;
        }

        return $nowTime >= $row['open_time'] && $nowTime <= $row['close_time'];
    }

    public function getTodayHour(int $libraryId): array|false
    {
        $todayDow = (int) date('w');

        return $this->db->fetchOne(
            'SELECT * FROM bdt_library_hour
              WHERE library_id  = ?
                AND day_of_week = ?
              LIMIT 1',
            'ii',
            [$libraryId, $todayDow]
        );
    }

    public function countActiveItems(string $locationId): int
    {
        $count = $this->db->fetchScalar(
            'SELECT COUNT(*) FROM item
              WHERE location_id    = ?
                AND item_status_id NOT IN ("WD", "MIS")',
            's',
            [$locationId]
        );
        return (int) $count;
    }

    public function countUniqueTitles(string $locationId): int
    {
        $count = $this->db->fetchScalar(
            'SELECT COUNT(DISTINCT biblio_id) FROM item
              WHERE location_id    = ?
                AND item_status_id NOT IN ("WD", "MIS")',
            's',
            [$locationId]
        );
        return (int) $count;
    }

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

    private function generateSlug(string $title): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = preg_replace('/-+/', '-', $slug);
        return $slug;
    }
}

