<?php
/**
 * Admin - Manajemen Akun
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/AccountService.php';

$auth = new AuthService();
$auth->requireRole('super_admin'); // Hanya super admin yang bisa mengakses

$accountService = new AccountService();
$accounts = $accountService->getAllAccounts();

$admin_active_page = 'accounts';

// Handle hapus
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id_to_delete = (int)$_GET['delete'];
    $current_user_id = (int)($_SESSION['admin_id'] ?? 0);
    
    if ($id_to_delete === $current_user_id) {
        $error_msg = "Anda tidak dapat menghapus akun Anda sendiri.";
    } else {
        if ($accountService->deleteAccount($id_to_delete, $current_user_id)) {
            header("Location: " . BASE_URL . "/portal-admin/akun?success=deleted");
            exit;
        } else {
            $error_msg = "Gagal menghapus akun.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Akun - Admin Portal</title>
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
        .badge-super_admin { background: #fee2e2; color: #991b1b; }
        .badge-admin { background: #dbeafe; color: #1e40af; }
        .badge-kontributor { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content">
            <div class="page-header">
                <div class="page-title">
                    <h1>Manajemen Akun</h1>
                    <p>Kelola akses superadmin, admin perpustakaan, dan kontributor.</p>
                </div>
                <div class="page-actions">
                    <a href="<?= BASE_URL ?>/portal-admin/akun/tambah" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Tambah Akun
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
                <div style="background: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 500;">
                    Akun berhasil dihapus.
                </div>
            <?php endif; ?>

            <?php if (isset($error_msg)): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 500;">
                    <?= htmlspecialchars($error_msg) ?>
                </div>
            <?php endif; ?>

            <div class="card" style="padding: 0; overflow: hidden;">
                <table class="data-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 1px solid var(--admin-border); text-align: left;">
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Nama</th>
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Username</th>
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Role</th>
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Terdaftar</th>
                            <th style="padding: 16px; font-weight: 600; color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($accounts)): ?>
                            <tr>
                                <td colspan="5" style="padding: 32px; text-align: center; color: var(--admin-text-muted);">Belum ada data akun.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($accounts as $acc): 
                                $is_self = $acc['id'] == ($_SESSION['admin_id'] ?? 0);
                            ?>
                                <tr style="border-bottom: 1px solid var(--admin-border);">
                                    <td style="padding: 16px;">
                                        <div style="font-weight: 600; color: var(--admin-text-main);">
                                            <?= htmlspecialchars($acc['name']) ?>
                                            <?php if ($is_self): ?>
                                                <span style="margin-left: 8px; font-size: 0.7rem; background: #e5e7eb; padding: 2px 6px; border-radius: 4px; color: #4b5563;">Anda</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="padding: 16px; color: var(--admin-text-muted);">@<?= htmlspecialchars($acc['username']) ?></td>
                                    <td style="padding: 16px;">
                                        <?php
                                            $role_label = str_replace('_', ' ', $acc['role']);
                                            $role_label = ucwords($role_label);
                                            $badge_class = 'badge-' . $acc['role'];
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= $role_label ?></span>
                                    </td>
                                    <td style="padding: 16px; color: var(--admin-text-muted); font-size: 0.9rem;">
                                        <?= date('d M Y', strtotime($acc['created_at'])) ?>
                                    </td>
                                    <td style="padding: 16px; text-align: right;">
                                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                            <a href="<?= BASE_URL ?>/portal-admin/akun/edit?id=<?= $acc['id'] ?>" class="btn-secondary" style="padding: 6px 12px; font-size: 0.85rem; border: 1px solid var(--admin-border); background: white; text-decoration: none; color: var(--admin-text-main); border-radius: 6px;">Edit</a>
                                            
                                            <?php if (!$is_self): ?>
                                            <a href="<?= BASE_URL ?>/portal-admin/akun?delete=<?= $acc['id'] ?>" class="btn-secondary" style="padding: 6px 12px; font-size: 0.85rem; border: 1px solid #fecaca; background: #fef2f2; text-decoration: none; color: #dc2626; border-radius: 6px;" onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">Hapus</a>
                                            <?php endif; ?>
                                        </div>
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