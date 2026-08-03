<?php
/**
 * Profile Service
 *
 * File    : ProfileService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 */

require_once __DIR__ . '/../helpers/Database.php';

class ProfileService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Mengambil data profil desa utama
     * Karena hanya ada 1 profil, kita ambil record pertama.
     */
    public function getProfile(): ?array
    {
        $sql = "SELECT * FROM bdt_village_profile LIMIT 1";
        return $this->db->fetchOne($sql);
    }

    /**
     * Memperbarui data profil desa
     */
    public function updateProfile(array $data): bool
    {
        $profile = $this->getProfile();
        
        // Allowed fields to update
        $fields = [
            'village_name', 'tagline', 'description', 'sejarah', 
            'address', 'district', 'regency', 'province', 'postal_code',
            'phone', 'email', 'whatsapp', 'website', 
            'google_maps_url', 'latitude', 'longitude',
            'logo_path', 'hero_image_path'
        ];

        $updateFields = [];
        $types = '';
        $values = [];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "{$field} = ?";
                $types .= (is_numeric($data[$field]) && strpos((string)$data[$field], '.') !== false) ? 'd' : 's';
                $values[] = $data[$field] === '' ? null : $data[$field];
            }
        }

        if (empty($updateFields)) {
            return false;
        }

        if ($profile) {
            // Update
            $sql = "UPDATE bdt_village_profile SET " . implode(', ', $updateFields) . " WHERE profile_id = ?";
            $types .= 'i';
            $values[] = $profile['profile_id'];
        } else {
            // Insert if empty
            $placeholders = implode(', ', array_fill(0, count($updateFields), '?'));
            $fieldsList = implode(', ', array_keys(array_filter($data, function($k) use ($fields) { return in_array($k, $fields); }, ARRAY_FILTER_USE_KEY)));
            $sql = "INSERT INTO bdt_village_profile ({$fieldsList}) VALUES ({$placeholders})";
        }

        $stmt = $this->db->getConnection()->prepare($sql);
        if (!$stmt) {
            error_log("ProfileService Error: " . $this->db->getConnection()->error);
            return false;
        }

        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
}

