<?php
/**
 * Admin Settings
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/BackupService.php';

$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin', 'kontributor']);

$role = $_SESSION['admin_role'] ?? '';
$isSuperAdmin = ($role === 'super_admin');

$admin_id = $_SESSION['admin_id'] ?? 0;
$current_language = $_SESSION['admin_language'] ?? 'id';
$current_theme = $_SESSION['admin_theme'] ?? 'light';

// Load maintenance config
$maintenanceFile = $libPath . '/custom/config/maintenance.json';
$maintenanceConfig = [];
if (file_exists($maintenanceFile)) {
    $maintenanceConfig = json_decode(file_get_contents($maintenanceFile), true)['maintenance'] ?? [];
}

// Load footer config
$footerFile = $libPath . '/custom/config/footer.json';
$footerConfig = [];
if (file_exists($footerFile)) {
    $footerConfig = json_decode(file_get_contents($footerFile), true);
}

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_preferences') {
        $language = $_POST['language'] ?? 'id';
        $theme = $_POST['theme'] ?? 'light';
        
        if ($auth->updatePreferences($admin_id, $language, $theme)) {
            $current_language = $language;
            $current_theme = $theme;
            $successMsg = 'Preferensi berhasil disimpan.';
        } else {
            $errorMsg = 'Gagal menyimpan preferensi.';
        }
    } 
    elseif ($action === 'save_maintenance' && $isSuperAdmin) {
        $global = isset($_POST['m_global']) ? true : false;
        
        $pages = [
            'landing' => isset($_POST['m_landing']) ? true : false,
            'artikel' => isset($_POST['m_artikel']) ? true : false,
            'berita' => isset($_POST['m_berita']) ? true : false,
            'pathfinder' => isset($_POST['m_pathfinder']) ? true : false,
            'katalog' => isset($_POST['m_katalog']) ? true : false,
            'informasi' => isset($_POST['m_informasi']) ? true : false,
            'kontak' => isset($_POST['m_kontak']) ? true : false,
            'profil' => isset($_POST['m_profil']) ? true : false,
        ];

        $newConfig = [
            'maintenance' => [
                'global' => $global,
                'pages' => $pages
            ]
        ];

        if (file_put_contents($maintenanceFile, json_encode($newConfig, JSON_PRETTY_PRINT))) {
            $maintenanceConfig = $newConfig['maintenance'];
            $successMsg = 'Pengaturan Maintenance Mode berhasil disimpan.';
        } else {
            $errorMsg = 'Gagal menyimpan pengaturan Maintenance Mode.';
        }
    }
    elseif ($action === 'save_footer' && $isSuperAdmin) {
        $socialPlatforms = $_POST['social_platform'] ?? [];
        $socialUrls = $_POST['social_url'] ?? [];
        $socials = [];
        
        for ($i = 0; $i < count($socialPlatforms); $i++) {
            if (!empty($socialPlatforms[$i]) && !empty($socialUrls[$i])) {
                $socials[] = [
                    'platform' => trim($socialPlatforms[$i]),
                    'url' => trim($socialUrls[$i])
                ];
            }
        }
        
        $rawPhone = trim($_POST['phone'] ?? '');
        if (str_starts_with($rawPhone, '0')) {
            $rawPhone = substr($rawPhone, 1);
        }
        $finalPhone = !empty($rawPhone) ? '+62 ' . ltrim($rawPhone) : '';

        $newFooterConfig = [
            'contact' => [
                'phone' => $finalPhone,
                'email' => trim($_POST['email'] ?? ''),
                'schedule' => trim($_POST['schedule'] ?? '')
            ],
            'social' => $socials,
            'policies' => [
                'privacy' => trim($_POST['privacy'] ?? ''),
                'terms' => trim($_POST['terms'] ?? '')
            ]
        ];

        if (file_put_contents($footerFile, json_encode($newFooterConfig, JSON_PRETTY_PRINT))) {
            $footerConfig = $newFooterConfig;
            $successMsg = 'Pengaturan Footer & Situs berhasil disimpan.';
        } else {
            $errorMsg = 'Gagal menyimpan pengaturan Footer.';
        }
    }
}

// Handle Backup download (GET request from route)
if (isset($_GET['action']) && $_GET['action'] === 'backup' && $isSuperAdmin) {
    $backupService = new BackupService();
    $backupService->downloadBackup();
    exit;
}

$admin_active_page = 'settings';
?>
<!DOCTYPE html>
<html lang="<?= $current_language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Baca di Teras</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .settings-grid {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 32px;
        }
        .settings-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .settings-nav-item {
            padding: 10px 16px;
            border-radius: 8px;
            color: var(--admin-text-muted);
            font-weight: 500;
            font-size: 0.95rem;
            text-decoration: none;
            transition: 0.2s;
            cursor: pointer;
        }
        .settings-nav-item:hover, .settings-nav-item.active {
            background: #f3f4f6;
            color: var(--admin-primary);
            font-weight: 600;
        }
        .settings-section {
            display: none;
        }
        .settings-section.active {
            display: block;
        }
        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--admin-border);
            padding: 24px;
            margin-bottom: 24px;
        }
        .card-header {
            border-bottom: 1px solid var(--admin-border);
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .card-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--admin-text-main);
        }
        .form-group {
            margin-bottom: 24px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--admin-text-main);
        }
        .form-control, .form-select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            box-sizing: border-box;
        }
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #059669;
        }
        input:checked + .slider:before {
            transform: translateX(20px);
        }
        .alert-success { background: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; }
        
        /* Dark Theme Styles */
        body.dark-theme {
            --admin-bg: #111827;
            --admin-card-bg: #1f2937;
            --admin-border: #374151;
            --admin-text-main: #f9fafb;
            --admin-text-muted: #9ca3af;
            background-color: var(--admin-bg);
            color: var(--admin-text-main);
        }
        body.dark-theme .card {
            background: var(--admin-card-bg);
        }
        body.dark-theme .form-control, body.dark-theme .form-select {
            background: #374151;
            color: white;
            border-color: #4b5563;
        }
        body.dark-theme .settings-nav-item:hover, body.dark-theme .settings-nav-item.active {
            background: #374151;
        }
    </style>
</head>
<body class="<?= $current_theme === 'dark' ? 'dark-theme' : '' ?>">

    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content" style="padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">
            
            <div class="page-header" style="margin-bottom: 32px;">
                <div class="page-title">
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;"><?= $current_language === 'en' ? 'Settings' : 'Pengaturan' ?></h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;"><?= $current_language === 'en' ? 'Manage your personal preferences and system configurations.' : 'Kelola preferensi personal dan konfigurasi sistem Anda.' ?></p>
                </div>
            </div>

            <?php if (!empty($successMsg)): ?>
                <div class="alert-success"><?= htmlspecialchars($successMsg) ?></div>
            <?php endif; ?>
            <?php if (!empty($errorMsg)): ?>
                <div class="alert-error"><?= htmlspecialchars($errorMsg) ?></div>
            <?php endif; ?>

            <div class="settings-grid">
                <!-- Sidebar Nav -->
                <div class="settings-nav">
                    <div class="settings-nav-item active" onclick="switchSection('general')"><?= $current_language === 'en' ? 'General Settings' : 'Pengaturan Umum' ?></div>
                    <?php if ($isSuperAdmin): ?>
                    <div class="settings-nav-item" onclick="switchSection('system')"><?= $current_language === 'en' ? 'System Management' : 'Manajemen Sistem' ?></div>
                    <div class="settings-nav-item" onclick="switchSection('site')"><?= $current_language === 'en' ? 'Site Settings' : 'Pengaturan Situs' ?></div>
                    <?php endif; ?>
                </div>

                <!-- Main Content -->
                <div class="settings-content">
                    
                    <!-- General Settings -->
                    <div id="section-general" class="settings-section active">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title"><?= $current_language === 'en' ? 'General Settings' : 'Pengaturan Umum' ?></h2>
                            </div>
                            <form action="" method="POST">
                                <input type="hidden" name="action" value="save_preferences">
                                
                                <div class="form-group">
                                    <label class="form-label"><?= $current_language === 'en' ? 'Primary Language' : 'Bahasa Utama' ?></label>
                                    <select name="language" class="form-select">
                                        <option value="id" <?= $current_language === 'id' ? 'selected' : '' ?>>Bahasa Indonesia</option>
                                        <option value="en" <?= $current_language === 'en' ? 'selected' : '' ?>>English</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label"><?= $current_language === 'en' ? 'Theme' : 'Tema' ?></label>
                                    <select name="theme" class="form-select">
                                        <option value="light" <?= $current_theme === 'light' ? 'selected' : '' ?>><?= $current_language === 'en' ? 'Light' : 'Terang' ?></option>
                                        <option value="dark" <?= $current_theme === 'dark' ? 'selected' : '' ?>><?= $current_language === 'en' ? 'Dark' : 'Gelap' ?></option>
                                    </select>
                                </div>

                                <button type="submit" class="btn-primary" style="padding: 10px 20px; font-weight: 600; border-radius: 8px; border: none; cursor: pointer;"><?= $current_language === 'en' ? 'Save Changes' : 'Simpan Perubahan' ?></button>
                            </form>
                        </div>
                    </div>

                    <!-- System Management (Superadmin Only) -->
                    <?php if ($isSuperAdmin): ?>
                    <div id="section-system" class="settings-section">
                        
                        <!-- Backup Data -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Data Backup</h2>
                            </div>
                            <p style="font-size: 0.9rem; color: var(--admin-text-muted); margin-bottom: 16px;">Unduh cadangan (backup) struktur dan data database Anda dalam format SQL.</p>
                            <a href="<?= BASE_URL ?>/portal-admin/settings?action=backup" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                Download Latest Backup
                            </a>
                        </div>

                        <!-- Maintenance Mode -->
                        <div class="card" style="border-color: #fca5a5; background: <?= $current_theme === 'dark' ? 'rgba(239, 68, 68, 0.1)' : '#fef2f2' ?>;">
                            <div class="card-header" style="border-color: #fca5a5; border-bottom: none; padding-bottom: 0;">
                                <h2 class="card-title" style="color: #dc2626; display: flex; align-items: center; gap: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    Maintenance Mode
                                </h2>
                            </div>
                            <form action="" method="POST" style="margin-top: 16px;">
                                <input type="hidden" name="action" value="save_maintenance">
                                <p style="font-size: 0.85rem; color: #991b1b; margin-bottom: 24px;">Aktifkan mode pemeliharaan (maintenance mode). Pengunjung situs publik akan melihat halaman "Sedang dalam perbaikan". Anda dapat memilih keseluruhan situs atau per halaman spesifik.</p>
                                
                                <div style="display: flex; flex-direction: column; gap: 16px;">
                                    
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 16px; border-bottom: 1px solid #fca5a5;">
                                        <div>
                                            <div style="font-weight: 600; color: #7f1d1d; font-size: 0.95rem;">Seluruh Website (Global)</div>
                                            <div style="font-size: 0.8rem; color: #991b1b;">Matikan akses ke seluruh fitur publik.</div>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="m_global" <?= !empty($maintenanceConfig['global']) ? 'checked' : '' ?>>
                                            <span class="slider"></span>
                                        </label>
                                    </div>

                                    <!-- Specific Pages -->
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 8px;">
                                        <?php 
                                            $pageOptions = [
                                                'landing' => 'Beranda',
                                                'artikel' => 'Artikel',
                                                'berita' => 'Berita',
                                                'pathfinder' => 'Pathfinder',
                                                'katalog' => 'Katalog Buku',
                                                'informasi' => 'Pusat Informasi',
                                                'kontak' => 'Kontak',
                                                'profil' => 'Profil Desa'
                                            ];
                                            foreach($pageOptions as $key => $label):
                                        ?>
                                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: rgba(255,255,255,0.5); border-radius: 8px; border: 1px solid #fca5a5;">
                                            <span style="font-weight: 500; font-size: 0.85rem; color: #7f1d1d;"><?= $label ?></span>
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="m_<?= $key ?>" <?= (!empty($maintenanceConfig['pages'][$key])) ? 'checked' : '' ?>>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                </div>
                                
                                <div style="margin-top: 24px; text-align: right;">
                                    <button type="submit" class="btn-primary" style="background: #dc2626; padding: 10px 20px; font-weight: 600; border-radius: 8px; border: none; cursor: pointer;">Simpan Pengaturan Maintenance</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Site Settings (Superadmin Only) -->
                    <div id="section-site" class="settings-section">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title"><?= $current_language === 'en' ? 'Footer & Site Information' : 'Informasi Footer & Situs' ?></h2>
                            </div>
                            <form action="" method="POST">
                                <input type="hidden" name="action" value="save_footer">
                                
                                <h3 style="font-size: 0.95rem; color: var(--admin-text-main); margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 8px;">Kontak & Jadwal</h3>
                                
                                <div class="form-group">
                                    <label class="form-label">Nomor Telepon</label>
                                    <div style="display: flex; align-items: center; border: 1px solid var(--admin-border); border-radius: 8px; overflow: hidden; background: var(--admin-bg);">
                                        <span style="padding: 10px 14px; background: rgba(0,0,0,0.05); color: var(--admin-text-muted); font-weight: 600; border-right: 1px solid var(--admin-border);">+62</span>
                                        <?php 
                                            $currPhone = $footerConfig['contact']['phone'] ?? '';
                                            $currPhone = trim(str_replace('+62', '', $currPhone));
                                        ?>
                                        <input type="text" name="phone" style="border: none; flex: 1; padding: 10px 14px; outline: none; background: transparent; color: inherit; font-family: 'Inter', sans-serif; font-size: 0.9rem;" value="<?= htmlspecialchars($currPhone) ?>" placeholder="812-3456-7890">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Alamat Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($footerConfig['contact']['email'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jam Operasional</label>
                                    <select name="schedule" class="form-select">
                                        <?php 
                                        $currentSchedule = $footerConfig['contact']['schedule'] ?? '';
                                        $scheduleOptions = [
                                            'Sen – Sab: 08:00 – 17:00',
                                            'Sen – Jum: 08:00 – 16:00',
                                            'Sen – Jum: 09:00 – 17:00',
                                            'Setiap Hari: 08:00 – 20:00',
                                            'Buka Setiap Hari 24 Jam'
                                        ];
                                        foreach($scheduleOptions as $opt) {
                                            $sel = ($currentSchedule === $opt) ? 'selected' : '';
                                            echo "<option value=\"$opt\" $sel>$opt</option>";
                                        }
                                        if (!in_array($currentSchedule, $scheduleOptions) && !empty($currentSchedule)) {
                                            echo "<option value=\"".htmlspecialchars($currentSchedule)."\" selected>".htmlspecialchars($currentSchedule)."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <h3 style="font-size: 0.95rem; color: var(--admin-text-main); margin-top: 32px; margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 8px;">Tautan Kebijakan</h3>
                                <div class="form-group">
                                    <label class="form-label">Tautan Kebijakan Privasi</label>
                                    <input type="text" name="privacy" class="form-control" value="<?= htmlspecialchars($footerConfig['policies']['privacy'] ?? '') ?>" placeholder="Misal: /informasi/kebijakan-privasi">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Tautan Syarat & Ketentuan</label>
                                    <input type="text" name="terms" class="form-control" value="<?= htmlspecialchars($footerConfig['policies']['terms'] ?? '') ?>" placeholder="Misal: /syarat-ketentuan">
                                </div>

                                <h3 style="font-size: 0.95rem; color: var(--admin-text-main); margin-top: 32px; margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 8px;">Media Sosial</h3>
                                <div id="social-container">
                                    <?php 
                                    $socials = $footerConfig['social'] ?? [];
                                    if (empty($socials)) {
                                        $socials[] = ['platform' => '', 'url' => '']; // default empty
                                    }
                                    foreach ($socials as $index => $social): 
                                    ?>
                                    <div class="social-item" style="display: flex; gap: 12px; margin-bottom: 12px; align-items: center;">
                                        <input type="text" name="social_platform[]" class="form-control" placeholder="Platform (e.g. Instagram)" value="<?= htmlspecialchars($social['platform']) ?>" style="width: 30%;">
                                        <input type="text" name="social_url[]" class="form-control" placeholder="URL Tautan" value="<?= htmlspecialchars($social['url']) ?>" style="flex: 1;">
                                        <button type="button" class="btn-remove-social" style="background: #fee2e2; color: #ef4444; border: none; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" /></svg>
                                        </button>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" id="btn-add-social" style="background: #e0f2fe; color: #0ea5e9; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; cursor: pointer; margin-bottom: 24px;">+ Tambah Media Sosial</button>

                                <div style="text-align: right;">
                                    <button type="submit" class="btn-primary" style="padding: 10px 20px; font-weight: 600; border-radius: 8px; border: none; cursor: pointer;">Simpan Pengaturan Situs</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>

    <script>
        function switchSection(id) {
            document.querySelectorAll('.settings-section').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.settings-nav-item').forEach(el => el.classList.remove('active'));
            document.getElementById('section-' + id).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('social-container');
            const btnAdd = document.getElementById('btn-add-social');

            if(btnAdd) {
                btnAdd.addEventListener('click', function() {
                    const div = document.createElement('div');
                    div.className = 'social-item';
                    div.style.cssText = 'display: flex; gap: 12px; margin-bottom: 12px; align-items: center;';
                    div.innerHTML = `
                        <input type="text" name="social_platform[]" class="form-control" placeholder="Platform (e.g. Instagram)" style="width: 30%;">
                        <input type="text" name="social_url[]" class="form-control" placeholder="URL Tautan" style="flex: 1;">
                        <button type="button" class="btn-remove-social" style="background: #fee2e2; color: #ef4444; border: none; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" /></svg>
                        </button>
                    `;
                    container.appendChild(div);
                });
            }

            if(container) {
                container.addEventListener('click', function(e) {
                    if (e.target.closest('.btn-remove-social')) {
                        e.target.closest('.social-item').remove();
                    }
                });
            }
        });
    </script>
</body>
</html>
