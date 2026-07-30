<?php
/**
 * Admin – Tambah Partner Logo Donasi
 *
 * File    : create_donasi_partner.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/DonasiService.php';

$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin']);

$donasiService = new DonasiService();

$successMsg = '';
$errorMsg   = '';
$errors     = [];

// ── Handle POST ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $website_url = trim($_POST['website_url'] ?? '');
    $sort_order  = (int) ($_POST['sort_order'] ?? 0);
    $is_visible  = isset($_POST['is_visible']) ? 1 : 0;

    // Validasi
    if (empty($name)) {
        $errors[] = 'Nama partner wajib diisi.';
    }

    // Handle upload logo
    $logo_path = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file     = $_FILES['logo'];
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif'];
        $maxSize  = 2 * 1024 * 1024; // 2MB

        if (!in_array($ext, $allowed)) {
            $errors[] = 'Format file tidak didukung. Gunakan JPG, PNG, WEBP, SVG, atau GIF.';
        } elseif ($file['size'] > $maxSize) {
            $errors[] = 'Ukuran file terlalu besar. Maksimal 2MB.';
        } else {
            $uploadDir = $libPath . '/custom/uploads/donasi/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName  = 'partner_' . time() . '_' . uniqid() . '.' . $ext;
            $destPath  = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                $logo_path = 'custom/uploads/donasi/' . $fileName;
            } else {
                $errors[] = 'Gagal menyimpan file. Pastikan direktori memiliki izin tulis.';
            }
        }
    }

    if (empty($errors)) {
        $result = $donasiService->createPartner([
            'name'        => $name,
            'logo_path'   => $logo_path,
            'website_url' => $website_url !== '' ? $website_url : null,
            'sort_order'  => $sort_order,
            'is_visible'  => $is_visible,
        ]);

        if ($result) {
            header('Location: ' . BASE_URL . '/portal-admin/donasi?tab=partner&success=created');
            exit;
        } else {
            $errorMsg = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
        }
    }
}

$admin_active_page = 'donasi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Partner Logo – Admin Portal</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
</head>
<body>
    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content">
            <div class="page-header">
                <div class="page-title">
                    <h1>Tambah Partner Logo</h1>
                    <p>
                        <a href="<?= BASE_URL ?>/portal-admin/donasi?tab=partner"
                           style="color:var(--admin-primary); text-decoration:none;">← Kembali ke Kelola Donasi</a>
                    </p>
                </div>
            </div>

            <!-- Feedback Messages -->
            <?php if (!empty($errors)): ?>
            <div style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; border-radius:8px; padding:14px 16px; margin-bottom:20px;">
                <strong>Terdapat kesalahan:</strong>
                <ul style="margin:8px 0 0; padding-left:18px;">
                    <?php foreach ($errors as $e): ?>
                        <li style="font-size:0.9rem;"><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php if ($errorMsg): ?>
            <div style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:0.9rem;">
                <?= htmlspecialchars($errorMsg) ?>
            </div>
            <?php endif; ?>

            <div class="card" style="max-width:580px;">
                <form method="post" enctype="multipart/form-data" novalidate>
                    <!-- Nama -->
                    <div style="margin-bottom:20px;">
                        <label for="name" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            Nama Partner / Organisasi <span style="color:#dc2626;">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                            placeholder="Contoh: Hivos, BEKRAF, RRI"
                            style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; box-sizing:border-box;"
                            required
                        >
                    </div>

                    <!-- Upload Logo -->
                    <div style="margin-bottom:20px;">
                        <label for="logo" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            File Logo <span style="color:#6b7280; font-weight:400;">— opsional (JPG, PNG, WEBP, SVG, maks. 2MB)</span>
                        </label>
                        <input
                            type="file"
                            id="logo"
                            name="logo"
                            accept=".jpg,.jpeg,.png,.webp,.svg,.gif"
                            style="width:100%; padding:8px 0; font-size:0.9rem; font-family:inherit;"
                        >
                        <!-- Preview -->
                        <div id="logo-preview" style="display:none; margin-top:10px;">
                            <img id="logo-preview-img" src="" alt="Preview logo"
                                 style="max-height:60px; max-width:160px; object-fit:contain; border:1px solid #e5e7eb; border-radius:6px; padding:4px;">
                        </div>
                    </div>

                    <!-- URL Website -->
                    <div style="margin-bottom:20px;">
                        <label for="website_url" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            URL Website <span style="color:#6b7280; font-weight:400;">— opsional</span>
                        </label>
                        <input
                            type="url"
                            id="website_url"
                            name="website_url"
                            value="<?= htmlspecialchars($_POST['website_url'] ?? '') ?>"
                            placeholder="https://contoh.com"
                            style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; box-sizing:border-box;"
                        >
                    </div>

                    <!-- Sort Order -->
                    <div style="margin-bottom:20px;">
                        <label for="sort_order" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            Urutan Tampil
                        </label>
                        <input
                            type="number"
                            id="sort_order"
                            name="sort_order"
                            value="<?= htmlspecialchars($_POST['sort_order'] ?? '0') ?>"
                            min="0"
                            style="width:120px; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none;"
                        >
                        <p style="font-size:0.8rem; color:var(--admin-text-muted); margin:4px 0 0;">Angka lebih kecil muncul lebih awal.</p>
                    </div>

                    <!-- Visibility -->
                    <div style="margin-bottom:28px;">
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:0.95rem; font-weight:500; color:var(--admin-text-main);">
                            <input
                                type="checkbox"
                                id="is_visible"
                                name="is_visible"
                                <?= isset($_POST['is_visible']) || !isset($_POST['name']) ? 'checked' : '' ?>
                                style="width:16px; height:16px; accent-color:var(--admin-primary);"
                            >
                            Tampilkan di halaman donasi publik
                        </label>
                    </div>

                    <div style="display:flex; gap:12px;">
                        <button type="submit" class="btn btn-primary">
                            Simpan Partner
                        </button>
                        <a href="<?= BASE_URL ?>/portal-admin/donasi?tab=partner"
                           style="padding:10px 20px; border-radius:8px; border:1px solid var(--admin-border); background:#fff; color:var(--admin-text-main); font-size:0.9rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center;">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div><!-- /.admin-content -->
    </div><!-- /.admin-main -->

    <script>
        // Live preview logo
        document.getElementById('logo').addEventListener('change', function() {
            const file = this.files[0];
            const previewDiv = document.getElementById('logo-preview');
            const previewImg = document.getElementById('logo-preview-img');
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    previewImg.src = e.target.result;
                    previewDiv.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>
