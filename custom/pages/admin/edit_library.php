<?php
/**
 * CMS – Edit Perpustakaan
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/LibraryService.php';
require_once $libPath . '/custom/helpers/UploadHelper.php';

// Auth Check
$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin']);

$libraryService = new LibraryService();
$admin_active_page = 'library';
$errorMsg = '';
$successMsg = '';

$library_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$lib = $libraryService->getLibraryById($library_id);
$existingHours = $libraryService->getHours($library_id);

if (!$lib) {
    header("Location: " . BASE_URL . "/portal-admin/perpustakaan");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'slims_location_id' => $_POST['slims_location_id'] ?? '',
        'name'              => $_POST['name'] ?? '',
        'slug'              => $_POST['slug'] ?? '',
        'tagline'           => $_POST['tagline'] ?? '',
        'badge'             => $_POST['badge'] ?? '',
        'status'            => $_POST['status'] ?? 'aktif',
        'address'           => $_POST['address'] ?? '',
        'village'           => $_POST['village'] ?? '',
        'phone'             => $_POST['phone'] ?? '',
        'email'             => $_POST['email'] ?? '',
        'whatsapp'          => $_POST['whatsapp'] ?? '',
        'google_maps_url'   => $_POST['google_maps_url'] ?? '',
        'latitude'          => $_POST['latitude'] ?? '',
        'longitude'         => $_POST['longitude'] ?? '',
        'cover_image'       => (isset($_POST['remove_cover']) && $_POST['remove_cover'] === '1') ? '' : $lib['cover_image'],
        'description'       => $_POST['description'] ?? ''
    ];

    if (isset($_FILES['cover_file']) && !empty($_FILES['cover_file']['name'])) {
        try {
            $uploadedPath = UploadHelper::uploadArticleCover($_FILES['cover_file'], '/custom/uploads/libraries/');
            if ($uploadedPath) {
                $data['cover_image'] = $uploadedPath;
            }
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
        }
    }

    if (empty($errorMsg)) {
        if ($libraryService->updateLibrary($library_id, $data)) {
            // Save operational hours if provided
            if (isset($_POST['hours']) && is_array($_POST['hours'])) {
                $libraryService->saveHours($library_id, $_POST['hours']);
            }
            $successMsg = 'Data perpustakaan berhasil diperbarui.';
            // Refresh data
            $lib = $libraryService->getLibraryById($library_id);
            $existingHours = $libraryService->getHours($library_id);
        } else {
            $errorMsg = 'Gagal menyimpan perubahan. Pastikan isian sudah benar.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Perpustakaan - Baca di Teras</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-main); }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--admin-border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 0.9rem; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1); }
        textarea.form-control { min-height: 100px; resize: vertical; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; }
        .alert-success { background: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; }
        
        .section-title { font-size: 1.1rem; margin-top: 32px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--admin-border); }
        .section-title:first-child { margin-top: 0; }
        
        /* Drag & Drop Upload Styles */
        .upload-area { border: 2px dashed var(--admin-border); border-radius: 8px; padding: 32px 16px; text-align: center; background: #f9fafb; cursor: pointer; transition: all 0.2s ease; position: relative; }
        .upload-area:hover, .upload-area.dragover { border-color: var(--admin-primary); background: #f0fdf4; }
        .upload-area input[type="file"] { position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer; }
        .upload-icon { color: #9ca3af; margin-bottom: 12px; }
        .upload-text { font-size: 0.9rem; color: #4b5563; font-weight: 500; margin-bottom: 4px; }
        .upload-hint { font-size: 0.75rem; color: #9ca3af; }
        .upload-preview { display: none; margin-top: 16px; position: relative; border-radius: 8px; overflow: hidden; border: 1px solid var(--admin-border); width: fit-content; }
        .upload-preview img { width: auto; max-width: 100%; max-height: 250px; display: block; object-fit: cover; }
        .remove-preview { position: absolute; top: 8px; right: 8px; background: rgba(0,0,0,0.5); color: white; border: none; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s; }
        .remove-preview:hover { background: rgba(220, 38, 38, 0.9); }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content" style="padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">
            
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <div class="page-title">
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Edit Perpustakaan</h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;">Perbarui data perpustakaan jejaring yang ada.</p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/portal-admin/perpustakaan" class="btn-secondary" style="background: white; border: 1px solid var(--admin-border); color: var(--admin-text-main); padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        Kembali
                    </a>
                </div>
            </div>
            
            <?php if ($errorMsg): ?>
                <div class="alert-error"><?= htmlspecialchars($errorMsg) ?></div>
            <?php endif; ?>
            <?php if ($successMsg): ?>
                <div class="alert-success"><?= htmlspecialchars($successMsg) ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/portal-admin/perpustakaan/edit?id=<?= $lib['library_id'] ?>" method="POST" enctype="multipart/form-data" class="card" style="padding: 32px; background: white; border-radius: 12px; border: 1px solid var(--admin-border);">
                
                <h2 class="section-title">Informasi Dasar</h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nama Perpustakaan</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($lib['name']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kode Lokasi SLiMS (Opsional)</label>
                        <input type="text" name="slims_location_id" class="form-control" value="<?= htmlspecialchars($lib['slims_location_id'] ?? '') ?>">
                        <small style="color: var(--admin-text-muted);">Sesuai dengan ID Lokasi di sistem SLiMS (mst_location).</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="slugInput" class="form-control" value="<?= htmlspecialchars($lib['slug']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slogan / Tagline</label>
                        <input type="text" name="tagline" class="form-control" value="<?= htmlspecialchars($lib['tagline'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" maxlength="180" placeholder="Maksimal 180 karakter"><?= htmlspecialchars($lib['description'] ?? '') ?></textarea>
                </div>

                <h2 class="section-title">Jam Operasional</h2>
                <div class="form-grid">
                    <?php 
                    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    for ($i = 0; $i < 7; $i++): 
                        $hData = $existingHours[$i] ?? [];
                        $isOpen = isset($hData['is_open']) ? (int)$hData['is_open'] : 1;
                        $hOpen = !empty($hData['open_time']) ? substr($hData['open_time'], 0, 5) : '';
                        $hClose = !empty($hData['close_time']) ? substr($hData['close_time'], 0, 5) : '';
                    ?>
                    <div class="form-group" style="display: flex; gap: 12px; align-items: center; grid-column: span 2; margin-bottom: 12px;">
                        <div style="width: 80px; font-weight: 600; font-size: 0.9rem;"><?= $days[$i] ?></div>
                        <input type="time" name="hours[<?= $i ?>][open]" class="form-control" value="<?= htmlspecialchars($hOpen) ?>" style="width: 140px;">
                        <span style="color: #666;">-</span>
                        <input type="time" name="hours[<?= $i ?>][close]" class="form-control" value="<?= htmlspecialchars($hClose) ?>" style="width: 140px;">
                        <label style="display: flex; align-items: center; gap: 6px; margin-left: 16px; cursor: pointer;">
                            <input type="hidden" name="hours[<?= $i ?>][is_closed]" value="<?= $isOpen ? '0' : '1' ?>">
                            <input type="checkbox" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'" <?= !$isOpen ? 'checked' : '' ?>> Libur
                        </label>
                    </div>
                    <?php endfor; ?>
                </div>

                <h2 class="section-title">Kontak & Lokasi</h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($lib['address'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Dusun/RT (Village)</label>
                        <input type="text" name="village" class="form-control" value="<?= htmlspecialchars($lib['village'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($lib['phone'] ?? '') ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="form-group">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($lib['whatsapp'] ?? '') ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($lib['email'] ?? '') ?>">
                    </div>
                </div>

                <h2 class="section-title">Pengaturan Tambahan</h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="aktif" <?= $lib['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= $lib['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            <option value="sementara-tutup" <?= $lib['status'] === 'sementara-tutup' ? 'selected' : '' ?>>Sementara Tutup</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Label (Badge)</label>
                        <input type="text" name="badge" class="form-control" value="<?= htmlspecialchars($lib['badge'] ?? '') ?>">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Foto / Cover</label>
                        <?php $hasCover = !empty($lib['cover_image']); ?>
                        <div class="upload-area" id="uploadArea" style="<?= $hasCover ? 'display: none;' : '' ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="upload-icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div class="upload-text">Klik atau Tarik foto ke sini</div>
                            <div class="upload-hint">Maksimal 2MB (JPEG, PNG, WEBP)</div>
                            <input type="file" name="cover_file" id="coverFileInput" accept="image/jpeg, image/png, image/webp">
                        </div>
                        <div class="upload-preview" id="uploadPreview" style="<?= $hasCover ? 'display: block;' : 'display: none;' ?>">
                            <img id="previewImg" src="<?= $hasCover ? htmlspecialchars(BASE_URL . $lib['cover_image']) : '' ?>" alt="Preview">
                            <button type="button" class="remove-preview" id="btnRemovePreview" title="Hapus foto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <input type="hidden" name="remove_cover" id="removeCoverInput" value="0">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--admin-border);">
                    <button type="submit" class="btn-primary" style="background-color: var(--admin-primary); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const nameInput = document.querySelector('input[name="name"]');
            const slugInput = document.getElementById('slugInput');
            let isSlugCustomized = !!slugInput.value;

            if (nameInput && slugInput) {
                slugInput.addEventListener("input", function() {
                    isSlugCustomized = true;
                });

                nameInput.addEventListener("input", function() {
                    if (!isSlugCustomized) {
                        let slug = nameInput.value.toLowerCase().trim()
                            .replace(/[^a-z0-9\s-]/g, '')
                            .replace(/\s+/g, '-')
                            .replace(/-+/g, '-');
                        slugInput.value = slug;
                    }
                });
            }
            
            // Drag & Drop Upload
            const uploadArea = document.getElementById('uploadArea');
            const coverFileInput = document.getElementById('coverFileInput');
            const uploadPreview = document.getElementById('uploadPreview');
            const previewImg = document.getElementById('previewImg');
            const btnRemovePreview = document.getElementById('btnRemovePreview');
            const removeCoverInput = document.getElementById('removeCoverInput');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => uploadArea.classList.add('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => uploadArea.classList.remove('dragover'), false);
            });

            uploadArea.addEventListener('drop', (e) => handleFiles(e.dataTransfer.files), false);
            coverFileInput.addEventListener('change', function() { handleFiles(this.files); });

            function handleFiles(files) {
                if (files.length === 0) return;
                const file = files[0];
                
                if (file.size > 2 * 1024 * 1024) {
                    alert(`Ukuran foto ${file.name} terlalu besar. Maksimal 2MB.`);
                    return;
                }
                if (!file.type.startsWith('image/')) {
                    alert(`Tipe file ${file.name} tidak didukung. Harap unggah gambar.`);
                    return;
                }

                const dt = new DataTransfer();
                dt.items.add(file);
                coverFileInput.files = dt.files;
                removeCoverInput.value = '0'; // Reset in case they dropped a new image

                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onloadend = function() {
                    previewImg.src = reader.result;
                    uploadArea.style.display = 'none';
                    uploadPreview.style.display = 'block';
                }
            }

            btnRemovePreview.addEventListener('click', function() {
                coverFileInput.value = '';
                previewImg.src = '';
                uploadPreview.style.display = 'none';
                uploadArea.style.display = 'block';
                removeCoverInput.value = '1';
            });
        });
    </script>
</body>
</html>
