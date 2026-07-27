<?php
/**
 * Admin - Tambah Akun
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/AccountService.php';

$auth = new AuthService();
$auth->requireRole('super_admin');

$accountService = new AccountService();

$admin_active_page = 'accounts';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'kontributor';

    if (empty($name) || empty($username) || empty($password)) {
        $error = 'Nama, username, dan password wajib diisi.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($accountService->isUsernameExists($username)) {
        $error = 'Username sudah digunakan, silakan pilih yang lain.';
    } else {
        if ($accountService->createAccount($username, $password, $name, $role)) {
            header("Location: " . BASE_URL . "/portal-admin/akun");
            exit;
        } else {
            $error = 'Gagal membuat akun, silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Akun - Admin Portal</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--admin-text-main); font-weight: 500; }
        .form-control { width: 100%; padding: 10px 16px; border: 1px solid var(--admin-border); border-radius: 8px; font-family: inherit; font-size: 0.95rem; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content">
            <div class="page-header">
                <div class="page-title">
                    <a href="<?= BASE_URL ?>/portal-admin/akun" style="color: var(--admin-text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-size: 0.9rem; margin-bottom: 8px; font-weight: 500;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali ke Manajemen Akun
                    </a>
                    <h1>Tambah Akun</h1>
                </div>
            </div>

            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 500;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <form action="" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div>
                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div>
                            <div class="form-group">
                                <label for="role">Hak Akses (Role)</label>
                                <select id="role" name="role" class="form-control" required>
                                    <option value="kontributor" <?= (($_POST['role'] ?? '') === 'kontributor') ? 'selected' : '' ?>>Kontributor (Hanya Artikel)</option>
                                    <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin / Pustakawan (Kelola Web)</option>
                                    <option value="super_admin" <?= (($_POST['role'] ?? '') === 'super_admin') ? 'selected' : '' ?>>Super Admin (Akses Penuh)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" class="form-control" required minlength="6">
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 16px; display: flex; justify-content: flex-end; gap: 12px;">
                        <a href="<?= BASE_URL ?>/portal-admin/akun" class="btn-secondary" style="padding: 10px 16px; text-decoration: none; border: 1px solid var(--admin-border); border-radius: 8px; color: var(--admin-text-main); font-weight: 600;">Batal</a>
                        <button type="submit" class="btn-primary" style="padding: 10px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: inherit;">Simpan Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>