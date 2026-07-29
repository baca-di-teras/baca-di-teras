<?php
/**
 * Admin - Buat Pengumuman Baru
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/AnnouncementService.php';

$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    
    if (empty($message)) {
        $error = 'Pesan pengumuman tidak boleh kosong.';
    } else {
        $announcementService = new AnnouncementService();
        $author_id = (int)$_SESSION['admin_id'];
        
        if ($announcementService->createAnnouncement($author_id, $message)) {
            $success = 'Pengumuman berhasil disiarkan.';
        } else {
            $error = 'Gagal menyimpan pengumuman.';
        }
    }
}

$admin_active_page = 'announcement';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pengumuman - Admin Portal</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--admin-text-main); font-weight: 500; }
        .form-control { width: 100%; padding: 10px 16px; border: 1px solid var(--admin-border); border-radius: 8px; font-family: inherit; font-size: 0.95rem; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
    </style>
    <link rel="icon" type="image/png" href="<?= defined('BASE_URL') ? BASE_URL : '/baca-di-teras' ?>/custom/assets/images/logo_header.png">
</head>
<body>
    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content">
            <div class="page-header">
                <div class="page-title">
                    <a href="<?= BASE_URL ?>/portal-admin/pengumuman" style="color: var(--admin-text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-size: 0.9rem; margin-bottom: 8px; font-weight: 500;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali ke Pengumuman Sistem
                    </a>
                    <h1>Buat Pengumuman</h1>
                    <p style="margin-top: 8px; color: var(--admin-text-muted);">Siarkan pesan baru ke seluruh staf.</p>
                </div>
            </div>

            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 500;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div style="background: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 500;">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <div class="card" style="max-width: 600px;">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="message">Pesan Pengumuman</label>
                        <textarea name="message" id="message" rows="5" class="form-control" required></textarea>
                        <p style="font-size: 0.8rem; color: var(--admin-text-muted); margin-top: 6px;">Pesan ini akan menggantikan pengumuman aktif sebelumnya dan memunculkan notifikasi merah bagi semua admin.</p>
                    </div>

                    <div style="margin-top: 16px; display: flex; justify-content: flex-end; gap: 12px;">
                        <a href="<?= BASE_URL ?>/portal-admin/pengumuman" class="btn-secondary" style="padding: 10px 16px; text-decoration: none; border: 1px solid var(--admin-border); border-radius: 8px; color: var(--admin-text-main); font-weight: 600;">Batal</a>
                        <button type="submit" class="btn-primary" style="padding: 10px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: inherit;">Siarkan Pengumuman</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</body>
</html>