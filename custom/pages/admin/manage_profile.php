<?php
/**
 * CMS – Manajemen Profil Desa
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/ProfileService.php';

// Auth Check
$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin']);

$profileService = new ProfileService();
$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data
    $updateData = [
        'village_name' => $_POST['village_name'] ?? '',
        'tagline'      => $_POST['tagline'] ?? '',
        'description'  => $_POST['description'] ?? '',
        'sejarah'      => $_POST['sejarah'] ?? '',
        'address'      => $_POST['address'] ?? '',
        'district'     => $_POST['district'] ?? '',
        'regency'      => $_POST['regency'] ?? '',
        'province'     => $_POST['province'] ?? '',
        'phone'        => $_POST['phone'] ?? '',
        'email'        => $_POST['email'] ?? '',
        'whatsapp'     => $_POST['whatsapp'] ?? '',
        'website'      => $_POST['website'] ?? '',
        'google_maps_url' => $_POST['google_maps_url'] ?? '',
        'latitude'     => $_POST['latitude'] ?? '',
        'longitude'    => $_POST['longitude'] ?? '',
        'postal_code'  => $_POST['postal_code'] ?? ''
    ];

    if ($profileService->updateProfile($updateData)) {
        $successMsg = 'Profil desa berhasil diperbarui.';
    } else {
        $errorMsg = 'Gagal memperbarui profil desa.';
    }
}

$profile = $profileService->getProfile() ?: [];
$admin_active_page = 'profile';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Profil Desa - Baca di Teras</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-main); }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--admin-border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 0.9rem; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1); }
        textarea.form-control { min-height: 100px; resize: vertical; }
        .alert-success { background: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; }
    </style>
    <link rel="icon" type="image/png" href="<?= defined('BASE_URL') ? BASE_URL : '/baca-di-teras' ?>/custom/assets/images/logo_header.png">
</head>
<body>

    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content" style="padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">
            
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <div class="page-title">
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Manajemen Profil Desa</h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;">Perbarui data profil, sejarah, visi-misi, dan kontak utama Desa Teras.</p>
                </div>
            </div>

            <?php if ($successMsg): ?>
                <div class="alert-success"><?= htmlspecialchars($successMsg) ?></div>
            <?php endif; ?>
            
            <?php if ($errorMsg): ?>
                <div class="alert-error"><?= htmlspecialchars($errorMsg) ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/portal-admin/profil-desa" method="POST" class="card" style="padding: 32px; background: white; border-radius: 12px; border: 1px solid var(--admin-border);">
                
                <h2 style="font-size: 1.1rem; margin-top: 0; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--admin-border);">Identitas Utama</h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nama Desa</label>
                        <input type="text" name="village_name" class="form-control" value="<?= htmlspecialchars($profile['village_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slogan / Tagline</label>
                        <input type="text" name="tagline" class="form-control" value="<?= htmlspecialchars($profile['tagline'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Singkat</label>
                    <textarea name="description" class="form-control"><?= htmlspecialchars($profile['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Sejarah</label>
                    <textarea name="sejarah" class="form-control" style="min-height: 200px;"><?= htmlspecialchars($profile['sejarah'] ?? '') ?></textarea>
                </div>

                <h2 style="font-size: 1.1rem; margin-top: 32px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--admin-border);">Kontak & Lokasi</h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($profile['address'] ?? '') ?>" placeholder="Misal: Jl. Raya Teras No. 123">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" placeholder="Misal: pemdes@teras.desa.id">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" placeholder="Misal: 0271-123456">
                    </div>
                    <div class="form-group">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($profile['whatsapp'] ?? '') ?>" placeholder="Misal: 081234567890">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kecamatan</label>
                        <input type="text" name="district" class="form-control" value="<?= htmlspecialchars($profile['district'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kabupaten</label>
                        <input type="text" name="regency" class="form-control" value="<?= htmlspecialchars($profile['regency'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Provinsi</label>
                        <input type="text" name="province" class="form-control" value="<?= htmlspecialchars($profile['province'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kode Pos</label>
                        <input type="text" name="postal_code" class="form-control" value="<?= htmlspecialchars($profile['postal_code'] ?? '') ?>">
                    </div>
                </div>

                <!-- Hidden fields for technical data to prevent overwriting -->
                <input type="hidden" name="website" value="<?= htmlspecialchars($profile['website'] ?? '') ?>">
                <input type="hidden" name="latitude" value="<?= htmlspecialchars($profile['latitude'] ?? '') ?>">
                <input type="hidden" name="longitude" value="<?= htmlspecialchars($profile['longitude'] ?? '') ?>">

                <div class="form-group">
                    <label class="form-label">URL Embed Google Maps</label>
                    <textarea name="google_maps_url" class="form-control" style="min-height: 80px;"><?= htmlspecialchars($profile['google_maps_url'] ?? '') ?></textarea>
                    <small style="color: var(--admin-text-muted);">Masukkan link embed dari Google Maps (src attribute dari iframe).</small>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--admin-border);">
                    <button type="submit" class="btn-primary" style="background-color: var(--admin-primary); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>
