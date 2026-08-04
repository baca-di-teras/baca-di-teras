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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #1a6b2f;
            --color-primary-dark: #155a26;
            --color-primary-light: #e8f5ec;
            --color-text-dark: #111827;
            --color-text-muted: #6b7280;
            --color-bg: #f9fafb;
            --color-border: #e5e7eb;
            --font-base: 'Inter', sans-serif;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: var(--font-base); 
            background: linear-gradient(135deg, #e8f5ec 0%, #f3f4f6 100%); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
        }
        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 24px;
        }
        .login-card { 
            background: white; 
            padding: 48px 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01); 
            width: 100%;
            border: 1px solid rgba(255,255,255,0.6);
        }
        .login-header { text-align: center; margin-bottom: 40px; }
        .login-header img { height: 56px; margin-bottom: 24px; object-fit: contain; }
        .login-header h1 { font-size: 1.75rem; color: var(--color-text-dark); margin-bottom: 8px; font-weight: 800; letter-spacing: -0.02em; }
        .login-header p { color: var(--color-text-muted); font-size: 0.95rem; line-height: 1.5; }
        
        .form-group { margin-bottom: 24px; position: relative; }
        .form-group label { display: block; margin-bottom: 8px; color: #374151; font-weight: 600; font-size: 0.9rem; }
        
        .input-wrapper {
            position: relative;
        }
        .input-wrapper svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            width: 20px;
            height: 20px;
            transition: color 0.2s;
            pointer-events: none;
        }
        .form-control { 
            width: 100%; 
            padding: 14px 14px 14px 44px; 
            border: 1.5px solid var(--color-border); 
            border-radius: 12px; 
            font-size: 0.95rem; 
            font-family: inherit;
            color: var(--color-text-dark);
            transition: all 0.2s; 
            background-color: #fcfcfc;
        }
        .form-control::placeholder { color: #9ca3af; }
        .form-control:focus { 
            outline: none; 
            border-color: var(--color-primary); 
            background-color: white;
            box-shadow: 0 0 0 4px rgba(26, 107, 47, 0.1); 
        }
        .form-control:focus + svg, .form-control:not(:placeholder-shown) + svg {
            color: var(--color-primary);
        }
        
        .btn-login { 
            width: 100%; 
            background: var(--color-primary); 
            color: white; 
            border: none; 
            padding: 14px; 
            border-radius: 12px; 
            font-size: 1rem; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.2s; 
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            font-family: inherit;
        }
        .btn-login:hover { 
            background: var(--color-primary-dark); 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 107, 47, 0.25);
        }
        .btn-login:active {
            transform: translateY(0);
            box-shadow: none;
        }
        .btn-login:disabled {
            opacity: 0.8;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
            background: var(--color-primary-dark);
        }
        
        .error-msg, .alert-msg { 
            padding: 14px; 
            border-radius: 12px; 
            margin-bottom: 24px; 
            font-size: 0.9rem; 
            text-align: center;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            animation: slideDown 0.3s ease;
        }
        .error-msg {
            background: #fef2f2; 
            color: #b91c1c; 
            border: 1px solid #fecaca;
        }
        .alert-msg {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
            display: none;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Loading Spinner */
        .spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            display: none;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .is-loading .spinner { display: block; }
        .is-loading .btn-text { display: none; }
        .is-loading::after { content: "Sedang memproses..."; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <img src="<?= BASE_URL ?>/custom/assets/images/logo.png" alt="Baca Di Teras Logo">
                <h1>Selamat Datang</h1>
                <p>Silakan masuk ke panel kendali Anda.</p>
            </div>

            <?php if ($error): ?>
                <div class="error-msg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <div class="alert-msg" id="alert-msg">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                <span id="alert-text">Silakan isi form terlebih dahulu.</span>
            </div>

            <form action="<?= BASE_URL ?>/portal-admin/login" method="POST" id="login-form">
                <div class="form-group">
                    <label for="username">Nama Pengguna</label>
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan nama pengguna" autocomplete="username">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi" autocomplete="current-password">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="btn-login">
                    <span class="spinner"></span>
                    <span class="btn-text">Masuk</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(e) {
            const user = document.getElementById('username').value.trim();
            const pass = document.getElementById('password').value.trim();
            const alertMsg = document.getElementById('alert-msg');
            const alertText = document.getElementById('alert-text');
            const btn = document.getElementById('btn-login');

            if (!user || !pass) {
                e.preventDefault();
                alertText.textContent = !user && !pass ? 'Nama pengguna dan kata sandi harus diisi.' : 
                                        (!user ? 'Nama pengguna tidak boleh kosong.' : 'Kata sandi tidak boleh kosong.');
                alertMsg.style.display = 'flex';
                
                // Shake animation for error
                const card = document.querySelector('.login-card');
                card.style.transform = 'translateX(-10px)';
                setTimeout(() => card.style.transform = 'translateX(10px)', 100);
                setTimeout(() => card.style.transform = 'translateX(-10px)', 200);
                setTimeout(() => card.style.transform = 'translateX(10px)', 300);
                setTimeout(() => card.style.transform = 'translateX(0)', 400);
                
                return;
            }

            // Show loading state
            alertMsg.style.display = 'none';
            btn.classList.add('is-loading');
            btn.disabled = true;
        });
        
        // Remove error message when user starts typing
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', () => {
                document.getElementById('alert-msg').style.display = 'none';
            });
        });
    </script>
</body>
</html>
