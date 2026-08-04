<?php
/**
 * Admin – Tambah Rilis Media
 *
 * File    : create_media.php
 * Project : Baca Di Teras
 * Version : 1.1.0
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/MediaService.php';
require_once $libPath . '/custom/services/ActivityLogService.php';

$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin', 'kontributor']);

$mediaService = new MediaService();
$activityService = new ActivityLogService();

$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = trim($_POST['title'] ?? '');
    $media_name   = trim($_POST['media_name'] ?? '');
    $category     = trim($_POST['category'] ?? 'Media Nasional');
    $url          = trim($_POST['url'] ?? '');
    $release_date = trim($_POST['release_date'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $is_published = (int) ($_POST['is_published'] ?? 1);
    $is_pinned    = isset($_POST['is_pinned']) ? 1 : 0;

    if (empty($title) || empty($media_name) || empty($url)) {
        $errorMsg = 'Judul, Nama Media, dan Tautan URL wajib diisi.';
    } elseif ($is_pinned === 1 && $mediaService->countPinnedMedia() >= 3) {
        $errorMsg = 'Maksimal 3 rilis media yang dapat disematkan (pin). Silakan lepas pin dari rilis media lain terlebih dahulu.';
    } else {
        $data = [
            'title'        => $title,
            'media_name'   => $media_name,
            'category'     => $category,
            'url'          => $url,
            'release_date' => !empty($release_date) ? $release_date : null,
            'description'  => $description,
            'is_published' => $is_published,
            'is_pinned'    => $is_pinned,
            'sort_order'   => 0
        ];

        if ($mediaService->createMedia($data)) {
            $activityService->log('menambahkan', 'Rilis Media', $title);
            header('Location: ' . BASE_URL . '/portal-admin/media?success=created');
            exit;
        } else {
            $errorMsg = 'Gagal menyimpan data rilis media.';
        }
    }
}

$admin_active_page = 'media';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Rilis Media – Admin Portal</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .form-card { background:#fff; border-radius:12px; padding:24px; max-width:800px; border:1px solid var(--admin-border); }
        .form-group { margin-bottom: 16px; }
        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
        .form-label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem; color: var(--admin-text-main); }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-family: 'Inter', sans-serif; font-size: 0.9rem; box-sizing: border-box; outline: none; }
        .form-control:focus { border-color: var(--admin-primary); }
        .form-checkbox-label { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600; font-size: 0.9rem; color: var(--admin-text-main); }
        .btn-cancel { padding: 10px 20px; text-decoration: none; display: inline-block; background-color: #dc2626; color: #ffffff; border-radius: 6px; font-weight: 600; font-size: 0.9rem; transition: background-color 0.2s; }
        .btn-cancel:hover { background-color: #b91c1c; }
        
        @media (max-width: 640px) {
            .form-grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content" style="padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">
            <div class="page-header" style="margin-bottom:24px;">
                <div class="page-title">
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Tambah Rilis Media</h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;">Tambahkan tautan liputan berita/media baru.</p>
                </div>
            </div>

            <?php if ($errorMsg): ?>
            <div style="background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size: 0.9rem;">
                <?= htmlspecialchars($errorMsg) ?>
            </div>
            <?php endif; ?>

            <div class="form-card">
                <form method="POST">
                    
                    <div class="form-group">
                        <label class="form-label">Judul Liputan / Berita *</label>
                        <input type="text" name="title" required class="form-control" placeholder="Contoh: Aneka Karya Sastrawan Ahmad Tohari Dipamerkan...">
                    </div>

                    <div class="form-grid-2">
                        <div>
                            <label class="form-label">Nama Media / Penerbit *</label>
                            <input type="text" name="media_name" required class="form-control" placeholder="Contoh: Detik.com / Web Prodi FIB">
                        </div>
                        <div>
                            <label class="form-label">Kategori Media *</label>
                            <select name="category" class="form-control">
                                <option value="Media Nasional">Media Nasional</option>
                                <option value="Web Prodi">Web Prodi</option>
                                <option value="Web Desa">Web Desa</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tautan URL Berita *</label>
                        <input type="url" name="url" required class="form-control" placeholder="https://www.detik.com/...">
                    </div>

                    <div class="form-grid-2">
                        <div>
                            <label class="form-label">Tanggal Rilis</label>
                            <input type="date" name="release_date" class="form-control">
                        </div>
                        <div>
                            <label class="form-label">Status Publikasi *</label>
                            <select name="is_published" class="form-control">
                                <option value="1">Published (Diterbitkan)</option>
                                <option value="0">Draft (Simpan sebagai Draft)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi Singkat (Opsional)</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Ringkasan atau gambaran umum mengenai liputan media..."></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom:24px;">
                        <label class="form-checkbox-label">
                            <input type="checkbox" name="is_pinned" value="1" style="width:18px; height:18px;">
                            <span>Sematkan Rilis Media Ini ke Atas (Pin Media - Maksimal 3)</span>
                        </label>
                    </div>

                    <div style="display:flex; gap:12px; align-items:center;">
                        <button type="submit" class="btn btn-primary" style="padding:10px 20px;">Simpan Rilis Media</button>
                        <a href="<?= BASE_URL ?>/portal-admin/media" class="btn-cancel">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
