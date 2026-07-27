<?php
/**
 * CMS – Manajemen Informasi
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/InformationService.php';

// Auth Check
$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin']);

$infoService = new InformationService();
$admin_active_page = 'info';

// Hapus Informasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $info_id = (int)$_POST['info_id'];
    $infoService->deleteInformation($info_id);
    header("Location: " . BASE_URL . "/portal-admin/informasi");
    exit;
}

$informations = $infoService->getAllInformation();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Informasi - Baca di Teras</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .table-wrapper { background: white; border-radius: 12px; border: 1px solid var(--admin-border); overflow: hidden; }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { background: #f9fafb; padding: 14px 20px; text-align: left; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-muted); border-bottom: 1px solid var(--admin-border); text-transform: uppercase; letter-spacing: 0.05em; }
        .admin-table td { padding: 16px 20px; font-size: 0.9rem; color: var(--admin-text-main); border-bottom: 1px solid var(--admin-border); vertical-align: top; }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover { background-color: #f9fafb; }
        
        .badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-faq { background: #dbeafe; color: #1e3a8a; }
        .badge-service { background: #dcfce7; color: #166534; }
        .badge-download { background: #fef3c7; color: #92400e; }
        .badge-lainnya { background: #f3f4f6; color: #4b5563; }
        .badge-aktif { background: #dcfce7; color: #166534; }
        .badge-nonaktif { background: #fee2e2; color: #991b1b; }

        .btn-action { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; border: none; cursor: pointer; transition: 0.2s; background: transparent; }
        .btn-edit { color: #2563eb; }
        .btn-edit:hover { background: #dbeafe; }
        .btn-delete { color: #dc2626; }
        .btn-delete:hover { background: #fee2e2; }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content" style="padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">
            
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <div class="page-title">
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Manajemen Informasi</h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;">Kelola FAQ, layanan, aturan, dan file unduhan.</p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/portal-admin/informasi/create" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; background-color: var(--admin-primary); color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Tambah Informasi
                    </a>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Tipe</th>
                            <th>Judul</th>
                            <th>Status</th>
                            <th>Urutan</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($informations)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 32px; color: var(--admin-text-muted);">
                                Belum ada data informasi.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($informations as $info): ?>
                            <tr>
                                <td>
                                    <?php 
                                        $badgeClass = 'badge-lainnya';
                                        if ($info['type'] == 'faq') $badgeClass = 'badge-faq';
                                        if ($info['type'] == 'service') $badgeClass = 'badge-service';
                                        if ($info['type'] == 'download') $badgeClass = 'badge-download';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= strtoupper($info['type']) ?></span>
                                </td>
                                <td>
                                    <div style="font-weight: 600; margin-bottom: 4px;"><?= htmlspecialchars($info['title']) ?></div>
                                    <div style="font-size: 0.8rem; color: var(--admin-text-muted); max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= htmlspecialchars(strip_tags($info['content'] ?? '')) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= $info['status'] === 'aktif' ? 'badge-aktif' : 'badge-nonaktif' ?>">
                                        <?= ucfirst($info['status']) ?>
                                    </span>
                                </td>
                                <td><?= (int)$info['sort_order'] ?></td>
                                <td style="text-align: right;">
                                    <a href="<?= BASE_URL ?>/portal-admin/informasi/edit?id=<?= $info['info_id'] ?>" class="btn-action btn-edit" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form action="<?= BASE_URL ?>/portal-admin/informasi" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus informasi ini?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="info_id" value="<?= $info['info_id'] ?>">
                                        <button type="submit" class="btn-action btn-delete" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
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
