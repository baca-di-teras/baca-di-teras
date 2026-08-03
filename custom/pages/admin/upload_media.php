<?php
/**
 * AJAX Endpoint for Quill Media Uploads
 */

define('ROOT_PATH', realpath(__DIR__ . '/../../..'));
require_once ROOT_PATH . '/custom/services/AuthService.php';
require_once ROOT_PATH . '/custom/helpers/UploadHelper.php';

header('Content-Type: application/json');

if (isset($_POST['base_url']) && !defined('BASE_URL')) {
    define('BASE_URL', $_POST['base_url']);
}

// Auth Check (only authenticated admins/contributors)
$auth = new AuthService();
if (!$auth->hasRole(['super_admin', 'admin', 'kontributor'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Akses ditolak.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metode tidak diizinkan.']);
    exit;
}

$type = $_POST['type'] ?? 'image';
if (!in_array($type, ['image', 'video'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Tipe media tidak valid.']);
    exit;
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Tidak ada file yang diunggah.']);
    exit;
}

try {
    $url = UploadHelper::uploadArticleMedia($_FILES['file'], $type);
    if ($url) {
        echo json_encode(['success' => true, 'url' => $url]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Gagal mengunggah file.']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
