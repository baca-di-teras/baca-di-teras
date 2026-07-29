<?php
/**
 * Admin - Riwayat Aktivitas
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/ActivityLogService.php';

$auth = new AuthService();
$auth->requireRole('super_admin'); // Hanya super admin

$activityService = new ActivityLogService();
$logs = $activityService->getRecentLogs(100);

$admin_active_page = 'activity';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas Sistem - Admin Portal</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-tambah { background: #d1fae5; color: #065f46; }
        .badge-ubah { background: #dbeafe; color: #1e40af; }
        .badge-hapus { background: #fee2e2; color: #991b1b; }
        .badge-login { background: #f3f4f6; color: #374151; }
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
                    <h1>Aktivitas Sistem</h1>
                    <p>Pantau riwayat perubahan data oleh semua admin.</p>
                </div>
            </div>

            <div class="card" style="padding: 0; overflow: hidden;">
                <table class="data-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 1px solid var(--admin-border); text-align: left;">
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase;">Waktu</th>
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase;">Admin</th>
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase;">Aksi</th>
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase;">Entitas</th>
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="5" style="padding: 32px; text-align: center; color: var(--admin-text-muted);">Belum ada riwayat aktivitas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): 
                                $action_class = 'badge-login';
                                if (strtolower($log['action']) === 'menambahkan' || strtolower($log['action']) === 'tambah') $action_class = 'badge-tambah';
                                if (strtolower($log['action']) === 'mengubah' || strtolower($log['action']) === 'edit') $action_class = 'badge-ubah';
                                if (strtolower($log['action']) === 'menghapus' || strtolower($log['action']) === 'hapus') $action_class = 'badge-hapus';
                            ?>
                                <tr style="border-bottom: 1px solid var(--admin-border);">
                                    <td style="padding: 16px; color: var(--admin-text-muted); font-size: 0.9rem; white-space: nowrap;">
                                        <?= date('d M Y, H:i', strtotime($log['created_at'])) ?>
                                    </td>
                                    <td style="padding: 16px;">
                                        <div style="font-weight: 600; color: var(--admin-text-main);"><?= htmlspecialchars($log['user_name'] ?? 'Sistem') ?></div>
                                        <div style="font-size: 0.75rem; color: var(--admin-text-muted);"><?= ucwords(str_replace('_', ' ', $log['user_role'] ?? '-')) ?></div>
                                    </td>
                                    <td style="padding: 16px;">
                                        <span class="badge <?= $action_class ?>"><?= htmlspecialchars(ucfirst($log['action'])) ?></span>
                                    </td>
                                    <td style="padding: 16px; font-weight: 500; color: var(--admin-text-main);">
                                        <?= htmlspecialchars($log['entity']) ?>
                                    </td>
                                    <td style="padding: 16px; color: var(--admin-text-muted); font-size: 0.9rem;">
                                        <?= htmlspecialchars($log['entity_name']) ?>
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