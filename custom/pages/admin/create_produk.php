<?php
/**
 * Admin – Tambah Produk (Galeri)
 *
 * File    : create_produk.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/ProdukService.php';

$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin']);

$produkService = new ProdukService();

$successMsg = '';
$errorMsg   = '';
$errors     = [];

// ── Handle POST ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $group_name  = trim($_POST['group_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_visible  = isset($_POST['is_visible']) ? 1 : 0;

    // Validasi
    if (empty($title)) {
        $errors[] = 'Judul produk wajib diisi.';
    }
    if (empty($group_name)) {
        $errors[] = 'Kelompok produk wajib diisi.';
    }

    // Cek upload gambar 1 (wajib)
    if (!isset($_FILES['image_1']) || $_FILES['image_1']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Gambar Utama wajib diunggah.';
    }

    $uploadedImages = [];
    
    // Proses upload jika tidak ada error awal
    if (empty($errors)) {
        $uploadDir = $libPath . '/custom/uploads/produk/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
        $maxSize     = 3 * 1024 * 1024; // 3MB

        // Helper func untuk upload
        $handleUpload = function($fileKey) use ($allowedExts, $maxSize, $uploadDir, &$errors) {
            if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$fileKey];
                $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                if (!in_array($ext, $allowedExts)) {
                    $errors[] = "File $fileKey: Format tidak didukung (harus JPG/PNG/WEBP).";
                    return null;
                }
                if ($file['size'] > $maxSize) {
                    $errors[] = "File $fileKey: Ukuran terlalu besar (Maks 3MB).";
                    return null;
                }

                $fileName = 'produk_' . $fileKey . '_' . time() . '_' . uniqid() . '.' . $ext;
                $destPath = $uploadDir . $fileName;

                if (move_uploaded_file($file['tmp_name'], $destPath)) {
                    return 'custom/uploads/produk/' . $fileName;
                } else {
                    $errors[] = "Gagal menyimpan $fileKey.";
                    return null;
                }
            }
            return null;
        };

        // Upload semua gambar
        $uploadedImages['image_1'] = $handleUpload('image_1');
        $uploadedImages['image_2'] = $handleUpload('image_2');
        $uploadedImages['image_3'] = $handleUpload('image_3');

        // Jika berhasil semua (tanpa error upload)
        if (empty($errors)) {
            $result = $produkService->createProduk([
                'title'       => $title,
                'group_name'  => $group_name,
                'description' => $description,
                'image_1'     => $uploadedImages['image_1'],
                'image_2'     => $uploadedImages['image_2'],
                'image_3'     => $uploadedImages['image_3'],
                'is_visible'  => $is_visible,
            ]);

            if ($result) {
                header('Location: ' . BASE_URL . '/portal-admin/produk?success=created');
                exit;
            } else {
                $errorMsg = 'Terjadi kesalahan saat menyimpan ke database.';
            }
        }
    }
}

$admin_active_page = 'produk';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk – Admin Portal</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .image-upload-wrapper {
            border: 1px dashed var(--admin-border);
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            background: #f9fafb;
        }
        .image-preview {
            max-height: 120px;
            max-width: 100%;
            margin-top: 10px;
            border-radius: 6px;
            display: none;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content">
            <div class="page-header">
                <div class="page-title">
                    <h1>Tambah Produk</h1>
                    <p>
                        <a href="<?= BASE_URL ?>/portal-admin/produk"
                           style="color:var(--admin-primary); text-decoration:none;">← Kembali ke Kelola Produk</a>
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

            <div class="card" style="max-width:640px;">
                <form method="post" enctype="multipart/form-data" novalidate>
                    
                    <!-- Title -->
                    <div style="margin-bottom:20px;">
                        <label for="title" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            Judul Produk <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" placeholder="Contoh: Tas Pustaka" style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; box-sizing:border-box;" required>
                    </div>

                    <!-- Group Name -->
                    <div style="margin-bottom:20px;">
                        <label for="group_name" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            Kelompok <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" id="group_name" name="group_name" value="<?= htmlspecialchars($_POST['group_name'] ?? '') ?>" placeholder="Contoh: KELOMPOK 1" style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; box-sizing:border-box;" required>
                    </div>

                    <!-- Deskripsi -->
                    <div style="margin-bottom:20px;">
                        <label for="description" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            Deskripsi Singkat <span style="color:#6b7280; font-weight:400;">— opsional</span>
                        </label>
                        <textarea id="description" name="description" rows="4" placeholder="Tuliskan deskripsi produk..." style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; box-sizing:border-box; resize:vertical;"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <hr style="border:none; border-top:1px solid var(--admin-border); margin:28px 0;">

                    <!-- Image Uploads -->
                    <h3 style="font-size:1.1rem; color:var(--admin-text-main); margin-top:0; margin-bottom:16px;">Gambar Produk (Maksimal 3)</h3>
                    <p style="font-size:0.85rem; color:var(--admin-text-muted); margin-bottom:20px;">Gambar 1 wajib diisi dan akan menjadi gambar utama. Gambar 2 dan 3 opsional untuk melengkapi galeri grid. Mendukung format JPG, PNG, WEBP dengan ukuran maks 3MB per file.</p>

                    <div class="image-upload-wrapper">
                        <label for="image_1" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            Gambar Utama 1 <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="file" id="image_1" name="image_1" accept=".jpg,.jpeg,.png,.webp" style="width:100%; font-size:0.9rem;" required onchange="previewImage(this, 'preview_1')">
                        <img id="preview_1" class="image-preview" src="" alt="Preview">
                    </div>

                    <div class="image-upload-wrapper">
                        <label for="image_2" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            Gambar Tambahan 2
                        </label>
                        <input type="file" id="image_2" name="image_2" accept=".jpg,.jpeg,.png,.webp" style="width:100%; font-size:0.9rem;" onchange="previewImage(this, 'preview_2')">
                        <img id="preview_2" class="image-preview" src="" alt="Preview">
                    </div>

                    <div class="image-upload-wrapper">
                        <label for="image_3" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            Gambar Tambahan 3
                        </label>
                        <input type="file" id="image_3" name="image_3" accept=".jpg,.jpeg,.png,.webp" style="width:100%; font-size:0.9rem;" onchange="previewImage(this, 'preview_3')">
                        <img id="preview_3" class="image-preview" src="" alt="Preview">
                    </div>

                    <!-- Visibility -->
                    <div style="margin:28px 0;">
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:0.95rem; font-weight:500; color:var(--admin-text-main);">
                            <input type="checkbox" id="is_visible" name="is_visible" <?= isset($_POST['is_visible']) || !isset($_POST['title']) ? 'checked' : '' ?> style="width:16px; height:16px; accent-color:var(--admin-primary);">
                            Tampilkan di Halaman Publik
                        </label>
                    </div>

                    <div style="display:flex; gap:12px;">
                        <button type="submit" class="btn btn-primary">
                            Simpan Produk
                        </button>
                        <a href="<?= BASE_URL ?>/portal-admin/produk"
                           style="padding:10px 20px; border-radius:8px; border:1px solid var(--admin-border); background:#fff; color:var(--admin-text-main); font-size:0.9rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center;">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div><!-- /.admin-content -->
    </div><!-- /.admin-main -->

    <script>
        function previewImage(input, previewId) {
            const file = input.files[0];
            const previewImg = document.getElementById(previewId);
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewImg.style.display = 'none';
                previewImg.src = '';
            }
        }
    </script>
</body>
</html>
