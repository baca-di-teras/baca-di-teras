<?php
/**
 * Pathfinder Admin Login Page
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';

$auth = new AuthService();
$auth->redirectIfLoggedIn();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($auth->login($username, $password)) {
        header("Location: " . BASE_URL . "/portal-admin");
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Baca Di Teras</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); width: 100%; max-width: 400px; }
        .login-header { text-align: center; margin-bottom: 32px; }
        .login-header h1 { font-size: 1.5rem; color: #111827; margin-bottom: 8px; font-weight: 700; }
        .login-header p { color: #6b7280; font-size: 0.95rem; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #374151; font-weight: 500; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; transition: border-color 0.2s; }
        .form-control:focus { outline: none; border-color: #059669; box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1); }
        .btn-login { width: 100%; background: #059669; color: white; border: none; padding: 12px; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-login:hover { background: #047857; }
        .error-msg { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; text-align: center; }
        .logo-text { font-size: 2rem; font-weight: 800; color: #059669; margin-bottom: 16px; }
    </style>
    <link rel="icon" type="image/png" href="<?= defined('BASE_URL') ? BASE_URL : '/baca-di-teras' ?>/custom/assets/images/logo_header.png">
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <img src="<?= BASE_URL ?>/custom/assets/images/logo.png" alt="Baca Di Teras Logo" style="height: 60px; margin-bottom: 16px; object-fit: contain;">
            <h1>Selamat Datang</h1>
            <p>Silakan masuk dengan akun Anda.</p>
        </div>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/portal-admin/login" method="POST">
            <div class="form-group">
                <label for="username">Nama Pengguna</label>
                <input type="text" id="username" name="username" class="form-control" required autocomplete="username">
            </div>
            
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>
</body>
</html>
