<?php
/**
 * CMS – Tambah Artikel & Berita
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
        'cover_image'  => $_POST['cover_image'] ?? null,
        'category'     => $_POST['category'] ?? 'berita',
        'status'       => $_POST['status'] ?? 'draft',
        'publish_date' => !empty($_POST['publish_date']) ? $_POST['publish_date'] : date('Y-m-d H:i:s'),
        'is_featured'  => isset($_POST['is_featured']) ? 1 : 0,
        'is_pinned'    => isset($_POST['is_pinned']) ? 1 : 0,
        'tags'         => $tags,
        'created_by'   => $_SESSION['admin_id'] ?? null
    ];

    // Handle Upload
    if (isset($_FILES['cover_file']) && !empty($_FILES['cover_file']['name'][0])) {
        try {
            $uploadedPaths = UploadHelper::uploadMultipleArticleCovers($_FILES['cover_file']);
            if (!empty($uploadedPaths)) {
                $data['cover_image'] = $uploadedPaths[0];
                if (count($uploadedPaths) > 1) {
                    $additional = array_slice($uploadedPaths, 1);
                    $data['additional_images'] = json_encode($additional);
                }
            } else {
                $data['cover_image'] = '/custom/assets/images/news-small.png';
            }
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
        }
    } else {
        $data['cover_image'] = '/custom/assets/images/news-small.png';
    }

    if (empty($errorMsg)) {
        if ($articleService->createArticle($data)) {
            $slug = empty($data['slug']) ? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['title']))) : $data['slug'];
            $slug = preg_replace('/-+/', '-', $slug);
            header("Location: " . BASE_URL . "/portal-admin/artikel?success=upload&slug=" . urlencode($slug) . "&cat=" . urlencode($data['category']) . "&status=" . urlencode($data['status']));
            exit;
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
    <title>Tambah Artikel - Baca di Teras</title>
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
        
        /* Quill adjustments */
        .ql-container { font-family: 'Inter', sans-serif; font-size: 1rem; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; }
        .ql-toolbar { border-top-left-radius: 8px; border-top-right-radius: 8px; font-family: 'Inter', sans-serif; }
    </style>
    <!-- Quill JS CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-editor img {
            max-width: 100%;
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content" style="padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">
            
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <div class="page-title">
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Tambah Artikel Baru</h1>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/portal-admin/artikel" class="btn-secondary" style="background: white; border: 1px solid var(--admin-border); color: var(--admin-text-main); padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        Batal
                    </a>
                </div>
            </div>
            
            <?php if ($errorMsg): ?>
                <div class="alert-error"><?= htmlspecialchars($errorMsg) ?></div>
            <?php endif; ?>

            <form id="articleForm" action="<?= BASE_URL ?>/portal-admin/artikel/create" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="left-col">
                        <div class="card">
                            <h2 class="card-title">Konten Utama</h2>
                            <div class="form-group">
                                <label class="form-label" style="display: flex; justify-content: space-between;">Judul Artikel <span id="titleCharCount" style="color: #6b7280; font-weight: normal;">0/180</span></label>
                                <input type="text" id="titleInput" name="title" class="form-control" required placeholder="Masukkan judul..." maxlength="180">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Slug (Opsional)</label>
                                <input type="text" id="slugInput" name="slug" class="form-control" placeholder="Dikosongkan untuk generate otomatis">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ringkasan (Excerpt)</label>
                                <textarea name="excerpt" class="form-control" placeholder="Ringkasan singkat untuk ditampilkan di card"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Isi Artikel</label>
                                <textarea name="body" id="bodyHidden" style="display:none;"></textarea>
                                <div id="editor-container" style="min-height: 400px; background: white;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="right-col">
                        <div class="card">
                            <h2 class="card-title">Pengaturan Publikasi</h2>
                            
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Kategori</label>
                                <select name="category" class="form-control">
                                    <option value="berita">Berita</option>
                                    <option value="kegiatan">Kegiatan</option>
                                    <option value="pengumuman">Pengumuman</option>
                                    <option value="resensi">Resensi</option>
                                    <option value="literasi">Literasi</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tanggal Publikasi</label>
                                <input type="datetime-local" name="publish_date" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Foto (Satu atau Lebih, Foto Pertama menjadi Cover)</label>
                                <div class="upload-area" id="uploadArea">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="upload-icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div class="upload-text">Klik atau Tarik foto ke sini (bisa lebih dari satu)</div>
                                    <div class="upload-hint">Maksimal 2MB per foto (JPEG, PNG, WEBP)</div>
                                    <input type="file" name="cover_file[]" id="coverFileInput" accept="image/jpeg, image/png, image/webp, image/gif" multiple>
                                </div>
                                <div class="upload-preview" id="uploadPreview" style="display: none;">
                                    <div id="previewImagesContainer" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
                                    <div style="display: flex; gap: 12px; margin-top: 16px;">
                                        <button type="button" class="btn-outline" id="btnAddMorePhotos" style="flex: 1;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                            Tambah Foto
                                        </button>
                                        <button type="button" class="btn-outline-danger" id="btnRemovePreview" style="flex: 1;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Ganti Semua Foto
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 0.9rem; cursor: pointer; margin-bottom: 12px;">
                                    <input type="checkbox" name="is_featured" value="1">
                                    Tampilkan di Sorotan Utama (Featured)
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 0.9rem; cursor: pointer;">
                                    <input type="checkbox" name="is_pinned" value="1">
                                    Sematkan di Atas (Pinned)
                                </label>
                            </div>

                            <div class="form-group" style="margin-top: 24px; border-top: 1px solid var(--admin-border); padding-top: 16px;">
                                <label class="form-label">Tags</label>
                                <div style="display: flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 8px; padding: 6px; background: #f9fafb; flex-wrap: wrap; gap: 4px;" id="tags-container">
                                    <input type="text" id="tag-input" placeholder="Tambah tag (tekan Enter)..." style="border: none; background: transparent; outline: none; flex: 1; min-width: 120px; font-size: 0.85rem; color: #4b5563;">
                                    <input type="hidden" name="tags" id="hidden-tags" value='[]'>
                                </div>
                            </div>

                            <div style="margin-top: 32px;">
                                <button type="submit" class="btn-primary" style="width: 100%; background-color: var(--admin-primary); color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem;">
                                    Simpan Artikel
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

    <!-- Link Modal -->
    <div id="linkModal" class="logout-modal" style="display: none;">
        <div class="logout-modal-backdrop" id="linkModalBackdrop"></div>
        <div class="logout-modal-content">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: 16px;">Sisipkan Tautan</h3>
            <div class="form-group" style="margin-bottom: 16px; text-align: left;">
                <label class="form-label">Teks Tautan</label>
                <input type="text" id="linkText" class="form-control" placeholder="Teks yang ditampilkan">
            </div>
            <div class="form-group" style="margin-bottom: 24px; text-align: left;">
                <label class="form-label">URL Tujuan</label>
                <input type="text" id="linkUrl" class="form-control" placeholder="misal: google.com">
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" id="btnCancelLink" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--admin-border); background: white; color: var(--admin-text-main); font-weight: 600; cursor: pointer;">Batal</button>
                <button type="button" id="btnSaveLink" style="padding: 10px 16px; border-radius: 8px; border: none; background: var(--admin-primary); color: white; font-weight: 600; cursor: pointer;">Sisipkan</button>
            </div>
        </div>
    </div>

    <script>
        const tagInput = document.getElementById('tag-input');
        const tagsContainer = document.getElementById('tags-container');
        const hiddenTags = document.getElementById('hidden-tags');
        let tags = [];

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
        let allSelectedFiles = []; // Track all files

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        coverFileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files, append = false) {
            if (files.length === 0) return;
            
            if (!append) {
                allSelectedFiles = [];
            }
            
            Array.from(files).forEach((file) => {
                // Validasi di sisi client
                if (file.size > 2 * 1024 * 1024) {
                    showCustomAlert(`Ukuran foto ${file.name} terlalu besar. Maksimal 2MB.`);
                    return;
                }
                
                if (!file.type.startsWith('image/')) {
                    showCustomAlert(`Tipe file ${file.name} tidak didukung. Harap unggah gambar.`);
                    return;
                }
                allSelectedFiles.push(file);
            });

            // Update file input using DataTransfer
            const dt = new DataTransfer();
            allSelectedFiles.forEach(f => dt.items.add(f));
            coverFileInput.files = dt.files;

            renderPreviews();
        }

        function renderPreviews() {
            const previewContainer = document.getElementById('previewImagesContainer');
            previewContainer.innerHTML = '';
            
            if (allSelectedFiles.length === 0) {
                uploadArea.style.display = 'block';
                uploadPreview.style.display = 'none';
                coverFileInput.value = '';
                return;
            }

            allSelectedFiles.forEach((file, index) => {
                const wrap = document.createElement('div');
                wrap.style.position = 'relative';
                wrap.style.display = 'inline-block';
                
                const img = document.createElement('img');
                img.style.width = '120px';
                img.style.height = '80px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '4px';
                img.style.border = index === 0 ? '2px solid var(--admin-primary)' : '1px solid var(--admin-border)';
                img.title = index === 0 ? 'Cover Image' : 'Tambahan';
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.innerHTML = '×';
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '4px';
                removeBtn.style.right = '4px';
                removeBtn.style.width = '20px';
                removeBtn.style.height = '20px';
                removeBtn.style.borderRadius = '50%';
                removeBtn.style.background = 'rgba(239, 68, 68, 0.9)';
                removeBtn.style.color = 'white';
                removeBtn.style.border = 'none';
                removeBtn.style.cursor = 'pointer';
                removeBtn.style.display = 'flex';
                removeBtn.style.alignItems = 'center';
                removeBtn.style.justifyContent = 'center';
                removeBtn.style.fontSize = '14px';
                removeBtn.style.lineHeight = '1';
                removeBtn.style.padding = '0';
                removeBtn.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
                
                removeBtn.addEventListener('click', function() {
                    allSelectedFiles.splice(index, 1);
                    const dt = new DataTransfer();
                    allSelectedFiles.forEach(f => dt.items.add(f));
                    coverFileInput.files = dt.files;
                    renderPreviews();
                });
                
                wrap.appendChild(img);
                wrap.appendChild(removeBtn);
                previewContainer.appendChild(wrap);

                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onloadend = function() {
                    img.src = reader.result;
                }
            });

            uploadArea.style.display = 'none';
            uploadPreview.style.display = 'block';
        }

        document.getElementById('btnAddMorePhotos').addEventListener('click', function() {
            const tempInput = document.createElement('input');
            tempInput.type = 'file';
            tempInput.multiple = true;
            tempInput.accept = "image/jpeg, image/png, image/webp, image/gif";
            tempInput.addEventListener('change', function() {
                handleFiles(this.files, true);
            });
            tempInput.click();
        });

        btnRemovePreview.addEventListener('click', function() {
            allSelectedFiles = [];
            coverFileInput.value = '';
            document.getElementById('previewImagesContainer').innerHTML = '';
            uploadPreview.style.display = 'none';
            uploadArea.style.display = 'block';
        });

        // Slug Autogeneration & Title Char Count Logic
        const titleInput = document.getElementById("titleInput");
        const slugInput = document.getElementById("slugInput");
        const titleCharCount = document.getElementById("titleCharCount");
        let isSlugCustomized = false;

        if (titleInput) {
            if (titleCharCount) {
                titleInput.addEventListener("input", function() {
                    titleCharCount.innerText = this.value.length + '/180';
                });
            }
            if (slugInput) {
                slugInput.addEventListener("input", function() {
                    isSlugCustomized = true;
                });

                titleInput.addEventListener("input", function() {
                    if (!isSlugCustomized) {
                        let slug = titleInput.value.toLowerCase().trim()
                            .replace(/[^a-z0-9\s-]/g, '')
                            .replace(/\s+/g, '-')
                            .replace(/-+/g, '-');
                        slugInput.value = slug;
                    }
                });
            }
        }
    </script>
    
    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>window.Quill = Quill;</script>
    <script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>
    <script src="https://unpkg.com/quill-magic-url@3.0.0/dist/index.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Handler for custom image upload
            function selectLocalImage() {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = () => {
                    const file = input.files[0];
                    if (/^image\//.test(file.type)) {
                        uploadImageToServer(file);
                    } else {
                        showCustomAlert('Anda hanya bisa mengunggah file gambar.');
                    }
                };
            }

            function createProgressImage(percentage) {
                const canvas = document.createElement('canvas');
                canvas.width = 600;
                canvas.height = 400;
                const ctx = canvas.getContext('2d');
                
                // Background
                ctx.fillStyle = '#1f2937';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                // Text
                ctx.fillStyle = '#f3f4f6';
                ctx.font = 'bold 48px Inter, sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(percentage + '%', canvas.width / 2, canvas.height / 2 - 10);
                
                // Subtext
                ctx.fillStyle = '#9ca3af';
                ctx.font = '24px Inter, sans-serif';
                ctx.fillText('Mengunggah...', canvas.width / 2, canvas.height / 2 + 40);
                
                return canvas.toDataURL('image/jpeg', 0.8);
            }

            function uploadImageToServer(file) {
                const range = quill.getSelection(true);
                let index = range ? range.index : 0;
                
                let currentSrc = createProgressImage(0);
                quill.insertEmbed(index, 'image', currentSrc);
                quill.setSelection(index + 1);

                let uploadingImg = null;
                setTimeout(() => {
                    const imgs = document.getElementById('editor-container').querySelectorAll('img');
                    for (let img of imgs) {
                        if (img.getAttribute('src') === currentSrc || img.src === currentSrc) {
                            uploadingImg = img;
                            break;
                        }
                    }
                }, 50);

                const fd = new FormData();
                fd.append('file', file);
                fd.append('type', 'image');
                fd.append('base_url', '<?= BASE_URL ?>');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '<?= BASE_URL ?>/custom/pages/admin/upload_media.php', true);
                
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        const newSrc = createProgressImage(percent);
                        
                        if (uploadingImg && document.body.contains(uploadingImg)) {
                            uploadingImg.src = newSrc;
                        } else {
                            const imgs = document.getElementById('editor-container').querySelectorAll('img');
                            for (let img of imgs) {
                                if (img.getAttribute('src') === currentSrc || img.src === currentSrc) {
                                    uploadingImg = img;
                                    uploadingImg.src = newSrc;
                                    break;
                                }
                            }
                        }
                        currentSrc = newSrc;
                    }
                };
                
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            const result = JSON.parse(xhr.responseText);
                            if (result.success && result.url) {
                                if (uploadingImg && document.body.contains(uploadingImg)) {
                                    uploadingImg.src = result.url;
                                } else {
                                    const imgs = document.getElementById('editor-container').querySelectorAll('img');
                                    for (let img of imgs) {
                                        if (img.getAttribute('src') === currentSrc || img.src === currentSrc) {
                                            img.src = result.url;
                                            break;
                                        }
                                    }
                                }
                            } else {
                                removePlaceholder(currentSrc);
                                showCustomAlert(result.error || 'Gagal mengunggah gambar.');
                            }
                        } catch (e) {
                            removePlaceholder(currentSrc);
                            showCustomAlert('Terjadi kesalahan saat memproses respons.');
                        }
                    } else {
                        removePlaceholder(currentSrc);
                        showCustomAlert('Terjadi kesalahan saat mengunggah gambar.');
                    }
                };
                
                xhr.onerror = function() {
                    removePlaceholder(currentSrc);
                    showCustomAlert('Terjadi kesalahan jaringan.');
                };
                
                xhr.send(fd);
            }

            function removePlaceholder(src) {
                const imgs = document.getElementById('editor-container').querySelectorAll('img');
                for (let img of imgs) {
                    if (img.getAttribute('src') === src || img.src === src) {
                        const blot = Quill.find(img);
                        if (blot) {
                            const idx = quill.getIndex(blot);
                            quill.deleteText(idx, 1);
                        } else {
                            img.remove();
                        }
                        break;
                    }
                }
            }

            // Inisialisasi Quill Editor
            var quill = new Quill('#editor-container', {
                theme: 'snow',
                placeholder: 'Isi artikel...',
                modules: {
                    magicUrl: true,
                    imageResize: {
                        displaySize: true
                    },
                    toolbar: {
                        container: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['link', 'image'],
                            ['clean']
                        ],
                        handlers: {
                            image: selectLocalImage
                        }
                    }
                }
            });

            const toolbar = quill.getModule('toolbar');

            // 1. Custom Link Handler Modal Logic
            const linkModal = document.getElementById('linkModal');
            const linkText = document.getElementById('linkText');
            const linkUrl = document.getElementById('linkUrl');
            const btnSaveLink = document.getElementById('btnSaveLink');
            const btnCancelLink = document.getElementById('btnCancelLink');
            const linkModalBackdrop = document.getElementById('linkModalBackdrop');

            let currentLinkRange = null;

            function closeLinkModal() {
                linkModal.style.display = 'none';
                linkText.value = '';
                linkUrl.value = '';
                currentLinkRange = null;
            }

            btnCancelLink.addEventListener('click', closeLinkModal);
            linkModalBackdrop.addEventListener('click', closeLinkModal);

            btnSaveLink.addEventListener('click', function() {
                let text = linkText.value.trim();
                let url = linkUrl.value.trim();

                if (!url) {
                    showCustomAlert('URL tujuan tidak boleh kosong!');
                    return;
                }
                
                if (!url.startsWith('http://') && !url.startsWith('https://') && !url.startsWith('/')) {
                    url = 'https://' + url;
                }

                if (currentLinkRange.length === 0) {
                    if (!text) {
                        showCustomAlert('Teks tautan tidak boleh kosong!');
                        return;
                    }
                    quill.insertText(currentLinkRange.index, text, 'link', url);
                    quill.setSelection(currentLinkRange.index + text.length);
                } else {
                    quill.formatText(currentLinkRange.index, currentLinkRange.length, 'link', url);
                    quill.setSelection(currentLinkRange.index + currentLinkRange.length);
                }
                
                closeLinkModal();
            });

            toolbar.addHandler('link', function(value) {
                currentLinkRange = quill.getSelection();
                if (!currentLinkRange) return;

                if (currentLinkRange.length > 0) {
                    linkText.value = quill.getText(currentLinkRange.index, currentLinkRange.length);
                    linkText.disabled = true; // prevent changing text if they selected something
                    linkText.style.background = '#f3f4f6';
                } else {
                    linkText.value = '';
                    linkText.disabled = false;
                    linkText.style.background = 'white';
                }

                linkUrl.value = '';
                linkModal.style.display = 'flex';
                if (currentLinkRange.length === 0) {
                    linkText.focus();
                } else {
                    linkUrl.focus();
                }
            });


            // Form submit sync for Quill
            const form = document.getElementById('articleForm');
            form.addEventListener('submit', function(e) {
                const bodyHidden = document.getElementById('bodyHidden');
                bodyHidden.value = quill.root.innerHTML;
                if (quill.getText().trim().length === 0) {
                    e.preventDefault();
                    showCustomAlert("Isi artikel tidak boleh kosong!");
                }
            });
        });
    </script>
</body>
</html>
