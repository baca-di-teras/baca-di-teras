<?php
/**
 * Script untuk Kategorisasi Buku (Pathfinder)
 * Membaca tags dari bdt_pathfinder_category, mencari buku yang relevan di tabel biblio (berdasarkan topic), 
 * lalu memasukkannya ke bdt_pathfinder secara otomatis.
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..';
require_once $libPath . '/custom/helpers/database.php';
require_once $libPath . '/custom/services/AuthService.php';

// Cek autentikasi admin
$auth = new AuthService();
$auth->requireLogin();

$db = Database::getInstance();

// 1. Ambil semua kategori pathfinder
$categories = $db->fetchAll("SELECT * FROM bdt_pathfinder_category");
$added = 0;

if ($categories) {
    foreach ($categories as $cat) {
        $tags = is_string($cat['tags']) ? json_decode($cat['tags'], true) : $cat['tags'];
        if (!is_array($tags) || empty($tags)) continue;
        
        $topicIds = [];
        // Cari id topik di mst_topic yang mirip dengan tags kategori ini
        foreach ($tags as $tag) {
            $stmt = "SELECT topic_id FROM mst_topic WHERE topic LIKE ?";
            $res = $db->fetchAll($stmt, 's', ['%' . trim($tag) . '%']);
            if ($res) {
                foreach ($res as $r) {
                    $topicIds[] = $r['topic_id'];
                }
            }
        }
        
        if (empty($topicIds)) continue;
        
        $topicIds = array_unique($topicIds);
        $inClause = implode(',', array_map('intval', $topicIds));
        
        // Ambil buku yang memiliki topik tersebut
        $sqlBooks = "
            SELECT b.biblio_id, b.title, b.sor as author, b.publish_year as year, p.publisher_name as publisher, b.call_number, b.isbn_issn as isbn 
            FROM biblio b
            JOIN biblio_topic bt ON b.biblio_id = bt.biblio_id
            LEFT JOIN mst_publisher p ON b.publisher_id = p.publisher_id
            WHERE bt.topic_id IN ($inClause)
            GROUP BY b.biblio_id
            LIMIT 20
        ";
        
        $booksRaw = $db->fetchAll($sqlBooks);
        if (empty($booksRaw)) continue;
        
        $booksJson = [];
        foreach ($booksRaw as $br) {
            $booksJson[] = [
                'title' => $br['title'],
                'author' => $br['author'] ?: 'Unknown',
                'year' => (int)$br['year'],
                'publisher' => $br['publisher'] ?: '',
                'call_number' => $br['call_number'] ?: '',
                'isbn' => $br['isbn'] ?: '',
                'location' => 'Perpustakaan Desa Teras'
            ];
        }
        
        $booksStr = json_encode($booksJson);
        $slug = $cat['slug'] . '-koleksi';
        $title = "Koleksi Rekomendasi: " . $cat['label'];
        $desc = "Daftar buku pilihan yang otomatis dikategorikan untuk bidang " . $cat['label'] . ".";
        $recom = count($booksJson);
        
        // Insert atau update pathfinder
        $sqlUpsert = "
            INSERT INTO bdt_pathfinder (category_id, slug, title, status, badge, recommendations, description, books)
            VALUES (?, ?, ?, 'published', 'Koleksi Otomatis', ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                title = VALUES(title),
                books = VALUES(books),
                recommendations = VALUES(recommendations),
                description = VALUES(description),
                updated_at = current_timestamp()
        ";
        
        $db->execute($sqlUpsert, 'isssis', [
            $cat['category_id'],
            $slug,
            $title,
            $recom,
            $desc,
            $booksStr
        ]);
        
        $added++;
    }
}

// Kembali ke halaman manage category
header("Location: " . BASE_URL . "/custom/pages/pathfinder/managecategory.php?success=" . $added);
exit;
