<?php
/**
 * Admin - Kelola Pengumuman
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/AnnouncementService.php';

$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin']); // Super admin dan admin biasa bisa bikin pengumuman

$announcementService = new AnnouncementService();

// Tangani aksi hapus
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $announcementService->deleteAnnouncement($id);
    header("Location: " . BASE_URL . "/portal-admin/pengumuman");
    exit;
}

$announcements = $announcementService->getAllAnnouncements();
$admin_active_page = 'announcement';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman Sistem - Admin Portal</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
</head>
<body>
    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content">
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="page-title">
                    <h1>Pengumuman Sistem</h1>
                    <p>Kelola pesan pengumuman untuk seluruh staf.</p>
                </div>
                <a href="<?= BASE_URL ?>/portal-admin/pengumuman/tambah" class="btn btn-primary" style="text-decoration: none;">
                    + Buat Pengumuman Baru
                </a>
            </div>

            <div class="card" style="padding: 0; overflow: hidden;">
                <table class="data-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 1px solid var(--admin-border); text-align: left;">
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase;">Waktu Dibuat</th>
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase;">Pesan</th>
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase;">Penulis</th>
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase;">Status</th>
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($announcements)): ?>
                            <tr>
                                <td colspan="5" style="padding: 32px; text-align: center; color: var(--admin-text-muted);">Belum ada pengumuman.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($announcements as $announcement): ?>
                                <tr style="border-bottom: 1px solid var(--admin-border);">
                                    <td style="padding: 16px; color: var(--admin-text-muted); font-size: 0.9rem; white-space: nowrap;">
                                        <?= date('d M Y, H:i', strtotime($announcement['created_at'])) ?>
                                    </td>
                                    <td style="padding: 16px; color: var(--admin-text-main); font-size: 0.95rem; max-width: 400px;">
                                        <?= htmlspecialchars(mb_strimwidth($announcement['message'], 0, 100, '...')) ?>
                                    </td>
                                    <td style="padding: 16px; font-weight: 500; color: var(--admin-text-main);">
                                        <?= htmlspecialchars($announcement['author_name'] ?? 'Admin') ?>
                                    </td>
                                    <td style="padding: 16px;">
                                        <?php if ($announcement['is_active']): ?>
                                            <span style="display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #d1fae5; color: #065f46;">Aktif</span>
                                        <?php else: ?>
                                            <span style="display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #f3f4f6; color: #4b5563;">Riwayat</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 16px; text-align: right;">
                                        <a href="?action=delete&id=<?= $announcement['id'] ?>" onclick="return confirm('Hapus pengumuman ini?')" style="color: #dc2626; text-decoration: none; font-size: 0.9rem; font-weight: 500;">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</body>
</html>