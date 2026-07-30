<?php
/**
 * Service untuk manajemen Produk (Galeri)
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..';
require_once $libPath . '/custom/helpers/Database.php';

class ProdukService {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Mengambil daftar produk untuk halaman publik (hanya yang visible)
     */
    public function getPublicProduk() {
        $sql = "SELECT * FROM bdt_produk WHERE is_visible = 1 ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Mengambil semua daftar produk untuk admin
     */
    public function getAllProduk() {
        $sql = "SELECT * FROM bdt_produk ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Mengambil produk berdasarkan ID
     */
    public function getProdukById($id) {
        $sql = "SELECT * FROM bdt_produk WHERE produk_id = ?";
        return $this->db->fetchOne($sql, 'i', [$id]);
    }

    /**
     * Menambahkan produk baru
     */
    public function createProduk($data) {
        $sql = "INSERT INTO bdt_produk (title, group_name, description, image_1, image_2, image_3, is_visible)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['group_name'],
            $data['description'],
            $data['image_1'],
            $data['image_2'] ?? null,
            $data['image_3'] ?? null,
            $data['is_visible'] ?? 1
        ];

        return $this->db->execute($sql, 'ssssssi', $params);
    }

    /**
     * Memperbarui produk (misal: toggle visibility)
     */
    public function updateProduk($id, $data) {
        $fields = [];
        $types  = '';
        $params = [];
        
        // Define param type map
        $typeMap = [
            'title'       => 's',
            'group_name'  => 's',
            'description' => 's',
            'image_1'     => 's',
            'image_2'     => 's',
            'image_3'     => 's',
            'is_visible'  => 'i'
        ];

        foreach ($data as $key => $value) {
            if (isset($typeMap[$key])) {
                $fields[] = "`$key` = ?";
                $types   .= $typeMap[$key];
                $params[] = $value;
            }
        }
        
        if (empty($fields)) return false;
        
        $sql = "UPDATE bdt_produk SET " . implode(', ', $fields) . " WHERE produk_id = ?";
        $types .= 'i';
        $params[] = $id;

        return $this->db->execute($sql, $types, $params);
    }

    /**
     * Menghapus produk dan file gambarnya
     */
    public function deleteProduk($id) {
        $produk = $this->getProdukById($id);
        if (!$produk) return false;

        $libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..';
        
        // Hapus file fisik
        $images = ['image_1', 'image_2', 'image_3'];
        foreach ($images as $imgCol) {
            if (!empty($produk[$imgCol])) {
                $filePath = $libPath . '/' . $produk[$imgCol];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $sql = "DELETE FROM bdt_produk WHERE produk_id = ?";
        return $this->db->execute($sql, 'i', [$id]);
    }
}
