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
require_once $libPath . '/custom/services/ActivityLogService.php';

$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin', 'kontributor']);

$produkService = new ProdukService();
$activityService = new ActivityLogService();

$successMsg = '';
$errorMsg   = '';
$errors     = [];

// ── Handle POST ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $group_name  = trim($_POST['group_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status      = $_POST['status'] ?? 'publish';

    // Validasi
    if (empty($title)) {
        $errors[] = 'Judul produk wajib diisi.';
    }
    if (empty($group_name)) {
        $errors[] = 'Nama pembuat (Dibuat oleh) wajib diisi.';
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
        $maxSize     = 2 * 1024 * 1024; // 2MB

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
                    $errors[] = "File $fileKey: Ukuran terlalu besar (Maks 2MB).";
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
                'image_1'     => $uploadedImages['image_1'] ?? '',
                'image_2'     => $uploadedImages['image_2'] ?? '',
                'image_3'     => $uploadedImages['image_3'] ?? '',
                'status'      => $status, // Gunakan status yang dipilih
                'created_by'  => $_SESSION['admin_id'] ?? 0
            ]);

            if ($result) {
                // Log activity
                $activityService->log('menambahkan', 'Produk', $title);
                
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
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
        }
        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        .drop-zone {
            border: 2px dashed var(--admin-border);
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            background: #f9fafb;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            margin-bottom: 20px;
        }
        .drop-zone:hover, .drop-zone.dragover {
            background: #eff6ff;
            border-color: #3b82f6;
        }
        .drop-zone input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }
        .drop-zone-text {
            color: var(--admin-text-muted);
            font-size: 0.95rem;
            pointer-events: none;
        }
        .drop-zone-icon {
            font-size: 2.5rem;
            color: #9ca3af;
            margin-bottom: 10px;
            display: block;
            pointer-events: none;
        }
        .image-preview {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 15px;
            display: none;
        }
        .remove-image-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(220, 38, 38, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            line-height: 1;
            z-index: 10;
        }
        .remove-image-btn:hover {
            background: rgb(185, 28, 28);
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

            <div class="card" style="max-width:1100px;">
                <form method="post" enctype="multipart/form-data" novalidate>
                    
                    <div class="form-grid">
                        <!-- KOLOM KIRI: Informasi Teks -->
                        <div>
                            <h3 style="font-size:1.1rem; color:var(--admin-text-main); margin-top:0; margin-bottom:20px; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px;">Informasi Produk</h3>
                            
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
                                    Dibuat oleh <span style="color:#dc2626;">*</span>
                                </label>
                                <input type="text" id="group_name" name="group_name" value="<?= htmlspecialchars($_POST['group_name'] ?? '') ?>" placeholder="Contoh: Nama Anda / Komunitas" style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; box-sizing:border-box;" required>
                            </div>

                            <!-- Deskripsi -->
                            <div style="margin-bottom:20px;">
                                <label for="description" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                                    Deskripsi Singkat <span style="color:#6b7280; font-weight:400;">— opsional</span>
                                </label>
                                <textarea id="description" name="description" rows="5" placeholder="Tuliskan deskripsi produk..." style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; box-sizing:border-box; resize:vertical;"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            </div>

                            <!-- Visibility -->
                            <div style="margin-top:20px;">
                                <label for="status" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                                    Status Publikasi
                                </label>
                                <select id="status" name="status" style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; background:#fff; cursor:pointer;">
                                    <option value="publish" <?= (isset($_POST['status']) && $_POST['status'] == 'publish') || !isset($_POST['status']) ? 'selected' : '' ?>>Publish (Tampil di Publik)</option>
                                    <option value="draft" <?= (isset($_POST['status']) && $_POST['status'] == 'draft') ? 'selected' : '' ?>>Draft (Sembunyikan)</option>
                                    <option value="archive" <?= (isset($_POST['status']) && $_POST['status'] == 'archive') ? 'selected' : '' ?>>Archive (Arsip)</option>
                                </select>
                            </div>
                        </div>

                        <!-- KOLOM KANAN: Upload Gambar -->
                        <div>
                            <h3 style="font-size:1.1rem; color:var(--admin-text-main); margin-top:0; margin-bottom:20px; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px;">Media Foto</h3>
                            <p style="font-size:0.85rem; color:var(--admin-text-muted); margin-bottom:20px;">Unggah foto produk dengan format JPG/PNG/WEBP (Maks. 3MB per file). Gambar 1 adalah foto utama yang akan mendominasi tampilan.</p>

                            <!-- Gambar 1 -->
                            <div style="margin-bottom:24px;">
                                <label style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:8px;">
                                    Gambar Utama 1 <span style="color:#dc2626;">*</span>
                                </label>
                                <div class="drop-zone" id="drop_zone_1">
                                    <span class="drop-zone-icon">📷</span>
                                    <span class="drop-zone-text" id="text_preview_1">Tarik & Lepas file di sini, atau klik untuk memilih</span>
                                    <input type="file" id="image_1" name="image_1" accept=".jpg,.jpeg,.png,.webp" required onchange="previewImage(this, 'preview_1', 'text_preview_1', 'drop_zone_1', 'remove_1')">
                                    <img id="preview_1" class="image-preview" src="" alt="Preview">
                                    <button type="button" id="remove_1" class="remove-image-btn" onclick="removeImage('image_1', 'preview_1', 'text_preview_1', 'drop_zone_1', 'remove_1', true)" title="Hapus foto">✕</button>
                                </div>
                            </div>

                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <!-- Gambar 2 -->
                                <div>
                                    <label style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:8px;">
                                        Gambar 2 <span style="color:#6b7280; font-weight:400;">— opsional</span>
                                    </label>
                                    <div class="drop-zone" id="drop_zone_2" style="padding: 20px 10px;">
                                        <span class="drop-zone-icon" style="font-size:1.5rem;">📷</span>
                                        <span class="drop-zone-text" id="text_preview_2" style="font-size:0.8rem;">Pilih gambar</span>
                                        <input type="file" id="image_2" name="image_2" accept=".jpg,.jpeg,.png,.webp" onchange="previewImage(this, 'preview_2', 'text_preview_2', 'drop_zone_2', 'remove_2')">
                                        <img id="preview_2" class="image-preview" src="" alt="Preview" style="height:120px;">
                                        <button type="button" id="remove_2" class="remove-image-btn" onclick="removeImage('image_2', 'preview_2', 'text_preview_2', 'drop_zone_2', 'remove_2', false)" title="Hapus foto">✕</button>
                                    </div>
                                </div>

                                <!-- Gambar 3 -->
                                <div>
                                    <label style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:8px;">
                                        Gambar 3 <span style="color:#6b7280; font-weight:400;">— opsional</span>
                                    </label>
                                    <div class="drop-zone" id="drop_zone_3" style="padding: 20px 10px;">
                                        <span class="drop-zone-icon" style="font-size:1.5rem;">📷</span>
                                        <span class="drop-zone-text" id="text_preview_3" style="font-size:0.8rem;">Pilih gambar</span>
                                        <input type="file" id="image_3" name="image_3" accept=".jpg,.jpeg,.png,.webp" onchange="previewImage(this, 'preview_3', 'text_preview_3', 'drop_zone_3', 'remove_3')">
                                        <img id="preview_3" class="image-preview" src="" alt="Preview" style="height:120px;">
                                        <button type="button" id="remove_3" class="remove-image-btn" onclick="removeImage('image_3', 'preview_3', 'text_preview_3', 'drop_zone_3', 'remove_3', false)" title="Hapus foto">✕</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border:none; border-top:1px solid var(--admin-border); margin:32px 0;">

                    <div style="display:flex; justify-content:flex-end; gap:16px;">
                        <a href="<?= BASE_URL ?>/portal-admin/produk"
                           style="padding:12px 24px; border-radius:8px; border:1px solid var(--admin-border); background:#fff; color:var(--admin-text-main); font-size:0.95rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center;">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary" style="padding:12px 24px; font-size:0.95rem;">
                            Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>

    <!-- Custom Alert Modal -->
    <div id="alertModal" class="logout-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; align-items: center; justify-content: center;">
        <div class="logout-modal-backdrop" id="alertModalBackdrop" style="position: absolute; inset: 0; background: rgba(0,0,0,0.5);"></div>
        <div class="logout-modal-content" style="position: relative; background: white; padding: 24px; border-radius: 12px; width: 90%; max-width: 400px; z-index: 10000; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; color: #dc2626; margin: 0 auto 16px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: 8px; text-align: center;">Peringatan</h3>
            <p id="alertMessage" style="font-size: 0.9rem; color: #6b7280; margin-bottom: 24px; text-align: center;">Pesan error di sini.</p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button type="button" id="btnDismissAlert" style="padding: 10px 16px; border-radius: 8px; border: none; background: #dc2626; color: white; font-weight: 600; cursor: pointer; flex: 1; text-align: center;">Mengerti</button>
            </div>
        </div>
    </div>

    <script>
        // Custom Alert Logic
        function showCustomAlert(message) {
            document.getElementById('alertMessage').innerText = message;
            document.getElementById('alertModal').style.display = 'flex';
        }
        document.getElementById('btnDismissAlert').addEventListener('click', function() {
            document.getElementById('alertModal').style.display = 'none';
        });
        document.getElementById('alertModalBackdrop').addEventListener('click', function() {
            document.getElementById('alertModal').style.display = 'none';
        });

        function previewImage(input, previewId, textId, zoneId, removeBtnId) {
            const preview = document.getElementById(previewId);
            const text = document.getElementById(textId);
            const zone = document.getElementById(zoneId);
            const removeBtn = document.getElementById(removeBtnId);
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                if (file.size > 2 * 1024 * 1024) {
                    showCustomAlert(`Ukuran foto ${file.name} terlalu besar. Maksimal 2MB.`);
                    input.value = '';
                    return;
                }
                
                if (!file.type.startsWith('image/')) {
                    showCustomAlert(`Tipe file ${file.name} tidak didukung. Harap unggah gambar.`);
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if(removeBtn) removeBtn.style.display = 'flex';
                    if(text) text.style.display = 'none';
                    if(zone) {
                        zone.querySelector('.drop-zone-icon').style.display = 'none';
                        zone.style.padding = '10px';
                        zone.style.background = 'transparent';
                        zone.style.borderStyle = 'solid';
                    }
                }
                reader.readAsDataURL(file);
            } else {
                removeImage(input.id, previewId, textId, zoneId, removeBtnId, zoneId === 'drop_zone_1');
            }
        }

        function removeImage(inputId, previewId, textId, zoneId, removeBtnId, isMain) {
            document.getElementById(inputId).value = '';
            document.getElementById(previewId).src = '';
            document.getElementById(previewId).style.display = 'none';
            
            const text = document.getElementById(textId);
            const zone = document.getElementById(zoneId);
            const removeBtn = document.getElementById(removeBtnId);
            
            if(removeBtn) removeBtn.style.display = 'none';
            if(text) text.style.display = 'block';
            if(zone) {
                zone.querySelector('.drop-zone-icon').style.display = 'block';
                zone.style.padding = isMain ? '30px 20px' : '20px 10px';
                zone.style.background = '#f9fafb';
                zone.style.borderStyle = 'dashed';
            }
        }

        // Add drag over effect
        document.querySelectorAll('.drop-zone').forEach(zone => {
            zone.addEventListener('dragover', (e) => {
                e.preventDefault();
                zone.classList.add('dragover');
            });
            zone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                zone.classList.remove('dragover');
            });
            zone.addEventListener('drop', (e) => {
                zone.classList.remove('dragover');
            });
        });
    </script>
</body>
</html>
