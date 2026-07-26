<?php
/**
 * CMS – Edit Artikel & Berita
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/ArticleService.php';
require_once $libPath . '/custom/helpers/UploadHelper.php';

// Auth Check
$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin', 'kontributor']);

$articleService = new ArticleService();
$admin_active_page = 'article';
$errorMsg = '';
$successMsg = '';

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = $articleService->getArticleById($article_id);

if (!$article) {
    header("Location: " . BASE_URL . "/portal-admin/artikel");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process tags
    $tags = [];
    if (!empty($_POST['tags'])) {
        $tagsData = json_decode($_POST['tags'], true);
        if (is_array($tagsData)) {
            $tags = $tagsData;
        }
    }

    $data = [
        'title'        => $_POST['title'] ?? '',
        'slug'         => $_POST['slug'] ?? '',
        'excerpt'      => $_POST['excerpt'] ?? '',
        'body'         => $_POST['body'] ?? '',
        'cover_image'  => $_POST['cover_image_old'] ?? $article['cover_image'],
        'category'     => $_POST['category'] ?? 'berita',
        'status'       => $_POST['status'] ?? 'draft',
        'publish_date' => !empty($_POST['publish_date']) ? $_POST['publish_date'] : date('Y-m-d H:i:s'),
        'is_featured'  => isset($_POST['is_featured']) ? 1 : 0,
        'is_pinned'    => isset($_POST['is_pinned']) ? 1 : 0,
        'tags'         => $tags
    ];

    // Handle Upload
    if (isset($_FILES['cover_file']) && $_FILES['cover_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        try {
            $uploadedPath = UploadHelper::uploadArticleCover($_FILES['cover_file']);
            if ($uploadedPath) {
                $data['cover_image'] = $uploadedPath;
            }
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
        }
    }

    if (empty($errorMsg)) {
        if ($articleService->updateArticle($article_id, $data)) {
            $successMsg = 'Artikel berhasil diperbarui.';
            $article = $articleService->getArticleById($article_id); // Refresh data
        } else {
            $errorMsg = 'Gagal menyimpan artikel. Pastikan isian sudah benar.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Artikel - Baca di Teras</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .form-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 32px; }
        .form-group { margin-bottom: 24px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-main); }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--admin-border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 0.9rem; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1); }
        textarea.form-control { min-height: 100px; resize: vertical; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; }
        .alert-success { background: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; }
        .card { padding: 24px; background: white; border-radius: 12px; border: 1px solid var(--admin-border); margin-bottom: 24px; }
        .card-title { font-size: 1rem; font-weight: 700; margin-top: 0; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--admin-border); }
        
        .tag-item { display: inline-flex; align-items: center; background: #e5e7eb; color: #374151; padding: 4px 10px; border-radius: 999px; font-size: 0.8rem; margin: 2px; }
        .tag-item button { background: none; border: none; margin-left: 6px; cursor: pointer; color: #6b7280; font-size: 0.8rem; padding: 0; display: inline-flex; align-items: center; }
        .tag-item button:hover { color: #dc2626; }

        /* Drag & Drop Upload Styles */
        .upload-area { border: 2px dashed var(--admin-border); border-radius: 8px; padding: 32px 16px; text-align: center; background: #f9fafb; cursor: pointer; transition: all 0.2s ease; position: relative; }
        .upload-area:hover, .upload-area.dragover { border-color: var(--admin-primary); background: #f0fdf4; }
        .upload-area input[type="file"] { position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer; }
        .upload-icon { color: #9ca3af; margin-bottom: 12px; }
        .upload-text { font-size: 0.9rem; color: #4b5563; font-weight: 500; margin-bottom: 4px; }
        .upload-hint { font-size: 0.75rem; color: #9ca3af; }
        .upload-preview { display: none; margin-top: 16px; position: relative; border-radius: 8px; overflow: hidden; border: 1px solid var(--admin-border); }
        .upload-preview img { width: 100%; height: auto; display: block; object-fit: cover; }
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
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Edit Artikel</h1>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/portal-admin/artikel" class="btn-secondary" style="background: white; border: 1px solid var(--admin-border); color: var(--admin-text-main); padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
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

            <form action="<?= BASE_URL ?>/portal-admin/artikel/edit?id=<?= $article['article_id'] ?>" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="left-col">
                        <div class="card">
                            <h2 class="card-title">Konten Utama</h2>
                            <div class="form-group">
                                <label class="form-label">Judul Artikel</label>
                                <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($article['title']) ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Slug</label>
                                <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($article['slug']) ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ringkasan (Excerpt)</label>
                                <textarea name="excerpt" class="form-control"><?= htmlspecialchars($article['excerpt'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Isi Artikel</label>
                                <textarea name="body" class="form-control" style="min-height: 400px;" required><?= htmlspecialchars($article['body'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="right-col">
                        <div class="card">
                            <h2 class="card-title">Pengaturan Publikasi</h2>
                            
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="draft" <?= $article['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= $article['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                    <option value="archived" <?= $article['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Kategori</label>
                                <select name="category" class="form-control">
                                    <option value="berita" <?= $article['category'] === 'berita' ? 'selected' : '' ?>>Berita</option>
                                    <option value="kegiatan" <?= $article['category'] === 'kegiatan' ? 'selected' : '' ?>>Kegiatan</option>
                                    <option value="pengumuman" <?= $article['category'] === 'pengumuman' ? 'selected' : '' ?>>Pengumuman</option>
                                    <option value="resensi" <?= $article['category'] === 'resensi' ? 'selected' : '' ?>>Resensi</option>
                                    <option value="literasi" <?= $article['category'] === 'literasi' ? 'selected' : '' ?>>Literasi</option>
                                    <option value="lainnya" <?= $article['category'] === 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tanggal Publikasi</label>
                                <?php
                                    $pubDate = $article['publish_date'] ? date('Y-m-d\TH:i', strtotime($article['publish_date'])) : '';
                                ?>
                                <input type="datetime-local" name="publish_date" class="form-control" value="<?= $pubDate ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Foto Sampul (Cover Image)</label>
                                <?php $hasCover = !empty($article['cover_image']); ?>
                                
                                <div class="upload-area" id="uploadArea" style="<?= $hasCover ? 'display:none;' : 'display:block;' ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="upload-icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div class="upload-text">Klik atau Tarik foto baru ke sini</div>
                                    <div class="upload-hint">Maksimal 2MB (JPEG, PNG, WEBP)</div>
                                    <input type="file" name="cover_file" id="coverFileInput" accept="image/jpeg, image/png, image/webp, image/gif">
                                </div>
                                <div class="upload-preview" id="uploadPreview" style="<?= $hasCover ? 'display:block;' : 'display:none;' ?>">
                                    <img src="<?= $hasCover ? htmlspecialchars(BASE_URL . str_replace(BASE_URL, '', $article['cover_image'])) : '' ?>" alt="Preview" id="previewImg">
                                    <button type="button" class="remove-preview" id="btnRemovePreview" title="Hapus Foto">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <input type="hidden" name="cover_image_old" id="coverImageOld" value="<?= htmlspecialchars($article['cover_image'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 0.9rem; cursor: pointer; margin-bottom: 12px;">
                                    <input type="checkbox" name="is_featured" value="1" <?= $article['is_featured'] ? 'checked' : '' ?>>
                                    Tampilkan di Sorotan Utama (Featured)
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 0.9rem; cursor: pointer;">
                                    <input type="checkbox" name="is_pinned" value="1" <?= $article['is_pinned'] ? 'checked' : '' ?>>
                                    Sematkan di Atas (Pinned)
                                </label>
                            </div>

                            <div class="form-group" style="margin-top: 24px; border-top: 1px solid var(--admin-border); padding-top: 16px;">
                                <label class="form-label">Tags</label>
                                <div style="display: flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 8px; padding: 6px; background: #f9fafb; flex-wrap: wrap; gap: 4px;" id="tags-container">
                                    <input type="text" id="tag-input" placeholder="Tambah tag (tekan Enter)..." style="border: none; background: transparent; outline: none; flex: 1; min-width: 120px; font-size: 0.85rem; color: #4b5563;">
                                    <input type="hidden" name="tags" id="hidden-tags" value="<?= htmlspecialchars(json_encode($article['tags'] ?? [])) ?>">
                                </div>
                            </div>

                            <div style="margin-top: 32px;">
                                <button type="submit" class="btn-primary" style="width: 100%; background-color: var(--admin-primary); color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem;">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <!-- Custom Alert Modal -->
    <div id="alertModal" class="logout-modal" style="display: none;">
        <div class="logout-modal-backdrop" id="alertModalBackdrop"></div>
        <div class="logout-modal-content">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; color: #dc2626; margin: 0 auto 16px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: 8px; text-align: center;">Peringatan</h3>
            <p id="alertMessage" style="font-size: 0.9rem; color: #6b7280; margin-bottom: 24px; text-align: center;">Pesan error di sini.</p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button id="btnDismissAlert" style="padding: 10px 16px; border-radius: 8px; border: none; background: #dc2626; color: white; font-weight: 600; cursor: pointer; flex: 1; text-align: center;">Mengerti</button>
            </div>
        </div>
    </div>

    <script>
        const tagInput = document.getElementById('tag-input');
        const tagsContainer = document.getElementById('tags-container');
        const hiddenTags = document.getElementById('hidden-tags');
        
        let tags = [];
        try {
            const initialTags = JSON.parse(hiddenTags.value);
            if (Array.isArray(initialTags)) {
                tags = initialTags;
            }
        } catch(e) {}

        function renderTags() {
            // Hapus element tag lama
            const existingTags = tagsContainer.querySelectorAll('.tag-item');
            existingTags.forEach(el => el.remove());

            // Render ulang
            tags.forEach((tag, index) => {
                const tagEl = document.createElement('div');
                tagEl.className = 'tag-item';
                tagEl.innerHTML = `${tag} <button type="button" onclick="removeTag(${index})">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>`;
                tagsContainer.insertBefore(tagEl, tagInput);
            });

            hiddenTags.value = JSON.stringify(tags);
        }

        function removeTag(index) {
            tags.splice(index, 1);
            renderTags();
        }

        tagInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const val = this.value.trim();
                if (val && !tags.includes(val)) {
                    tags.push(val);
                    this.value = '';
                    renderTags();
                }
            } else if (e.key === 'Backspace' && this.value === '' && tags.length > 0) {
                tags.pop();
                renderTags();
            }
        });
        
        // Disable enter form submission on input fields
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });

        // Initial render
        renderTags();

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

        // Drag & Drop Upload Logic
        const uploadArea = document.getElementById('uploadArea');
        const coverFileInput = document.getElementById('coverFileInput');
        const uploadPreview = document.getElementById('uploadPreview');
        const previewImg = document.getElementById('previewImg');
        const btnRemovePreview = document.getElementById('btnRemovePreview');
        const coverImageOld = document.getElementById('coverImageOld');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            uploadArea.classList.add('dragover');
        }

        function unhighlight(e) {
            uploadArea.classList.remove('dragover');
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            coverFileInput.files = files;
            handleFiles(files);
        }

        coverFileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            if (files.length === 0) return;
            const file = files[0];
            
            // Validasi di sisi client
            if (file.size > 2 * 1024 * 1024) {
                showCustomAlert('Ukuran foto terlalu besar. Maksimal 2MB.');
                coverFileInput.value = '';
                return;
            }
            
            if (!file.type.startsWith('image/')) {
                showCustomAlert('Tipe file tidak didukung. Harap unggah gambar.');
                coverFileInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = function() {
                previewImg.src = reader.result;
                uploadArea.style.display = 'none';
                uploadPreview.style.display = 'block';
                // Jika user mengupload foto baru, kita bisa biarkan saja coverImageOld (nantinya tertimpa di PHP karena ada $_FILES).
            }
        }

        btnRemovePreview.addEventListener('click', function() {
            coverFileInput.value = '';
            previewImg.src = '';
            uploadPreview.style.display = 'none';
            uploadArea.style.display = 'block';
            coverImageOld.value = ''; // Hapus referensi foto lama agar di-db terhapus jika di-save
        });
    </script>
</body>
</html>
