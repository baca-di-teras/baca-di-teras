<?php
/**
 * Pathfinder – Buat Pathfinder Baru (Article Management)
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/PathfinderService.php';

// Auth Check
$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin']);

$pfService = new PathfinderService();
$categories = $pfService->getCategories();

$admin_active_page = 'article';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    // Auto-generate slug if not provided
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    $data = [
        'title' => $title,
        'slug' => $slug,
        'category_id' => (int)($_POST['category_id'] ?? 1),
        'status' => $_POST['status'] ?? 'draft',
        'badge' => '',
        'author' => '',
        'catalog_url' => '',
        'description' => $_POST['description'] ?? '',
        'broader_terms' => $_POST['tags'] ?? '[]', // Map tags to broader_terms
        'narrower_terms' => '[]',
        'related_terms' => '[]',
        'books' => '[]',
    ];

    if (empty($data['title'])) {
        $error = "Article Title is required.";
    } else {
        try {
            $pfService->createPathfinder($data);
            header("Location: " . BASE_URL . "/custom/pages/pathfinder/managepathfinder.php?success=created");
            exit;
        } catch (Exception $e) {
            $error = "Gagal menyimpan data: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pathfinder Baru - Baca di Teras</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .page-bg { background-color: #f9fafb; min-height: 100vh; }
        .form-label { display: block; font-weight: 600; margin-bottom: 8px; color: #374151; font-size: 0.85rem; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-family: inherit; font-size: 0.95rem; box-sizing: border-box; background: #fff; outline: none; }
        .form-control:focus { border-color: #10b981; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 500; }
        .card-panel { background: white; border-radius: 8px; border: 1px solid #e5e7eb; box-sizing: border-box; }
        
        .btn-draft { background: #f3f4f6; color: #374151; border: none; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-draft:hover { background: #e5e7eb; }
        .btn-publish { background: #065f46; color: white; border: none; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; }
        .btn-publish:hover { background: #047857; }
        
        .toolbar-icon { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 4px; cursor: pointer; color: #6b7280; }
        .toolbar-icon:hover { background: #f3f4f6; color: #374151; }
        
        .upload-area { border: 2px dashed #d1d5db; border-radius: 8px; padding: 24px 16px; text-align: center; cursor: pointer; transition: 0.2s; background: #fff; }
        .upload-area:hover { border-color: #10b981; }
        
        /* Tag styling */
        .tag-chip { background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 16px; padding: 4px 12px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 6px; color: #374151; }
        .tag-chip span { cursor: pointer; color: #9ca3af; }
        .tag-chip span:hover { color: #4b5563; }
        
        .input-title { width: 100%; border: none; border-bottom: 1px solid #e5e7eb; padding: 8px 0 16px 0; font-size: 1.15rem; font-weight: 500; outline: none; color: #111827; }
        .input-title::placeholder { color: #d1d5db; font-weight: 400; }
    </style>
    <link rel="icon" type="image/png" href="<?= defined('BASE_URL') ? BASE_URL : '/baca-di-teras' ?>/custom/assets/images/logo_header.png">
</head>
<body class="page-bg">

    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content" style="padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">
            
            <div style="margin-bottom: 24px;">
                <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 8px;">
                    <a href="<?= BASE_URL ?>/custom/pages/pathfinder/managepathfinder.php" style="color: inherit; text-decoration: none; font-weight: 600;">Manajemen Pathfinder</a> &rsaquo; 
                    <span style="font-weight: 600; color: #059669;">Buat Baru</span>
                </div>
                <h1 style="font-size: 1.4rem; font-weight: 700; margin: 0; color: #111827;">Buat Pathfinder Baru</h1>
            </div>

            <?php if ($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="" method="POST" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
                
                <!-- Left Column -->
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    
                    <!-- Title Section -->
                    <div class="card-panel" style="padding: 24px;">
                        <label class="form-label" style="color: #6b7280; font-weight: 600; margin-bottom: 16px;">Judul Pathfinder</label>
                        <input type="text" name="title" class="input-title" placeholder="Masukkan judul yang menarik..." value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                    </div>

                    <!-- Editor Section -->
                    <div class="card-panel" style="overflow: hidden;">
                        <div style="background: #f9fafb; padding: 12px 24px; border-bottom: 1px solid #e5e7eb; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 6px; padding-right: 16px; border-right: 1px solid #e5e7eb; cursor: pointer; color: #6b7280;">
                                <span style="font-size: 0.85rem; font-weight: 600;">Paragraph</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                            
                            <div class="toolbar-icon" style="font-weight: 700;">B</div>
                            <div class="toolbar-icon" style="font-style: italic; font-weight: 700;">I</div>
                            <div class="toolbar-icon" style="text-decoration: underline; font-weight: 700;">U</div>
                            
                            <div style="width: 1px; height: 16px; background: #e5e7eb; margin: 0 4px;"></div>
                            
                            <div class="toolbar-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                            </div>
                            <div class="toolbar-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                            </div>
                            
                            <div style="width: 1px; height: 16px; background: #e5e7eb; margin: 0 4px;"></div>
                            
                            <div class="toolbar-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                            </div>
                            <div class="toolbar-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                        </div>
                        <textarea name="description" placeholder="Mulai menulis deskripsi pathfinder di sini..." style="width: 100%; min-height: 400px; padding: 24px; border: none; font-size: 0.95rem; line-height: 1.6; resize: vertical; outline: none; box-sizing: border-box; font-family: inherit; color: #374151;"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                </div>

                <!-- Right Column -->
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    
                    <!-- Details Card -->
                    <div class="card-panel" style="padding: 24px;">
                        <h3 style="margin: 0 0 24px 0; font-size: 1rem; font-weight: 700; color: #111827; border-bottom: 1px solid #e5e7eb; padding-bottom: 12px;">Detail Publikasi</h3>
                        
                        <!-- Upload Image -->
                        <div style="margin-bottom: 24px;">
                            <label class="form-label">Hero Image</label>
                            <div class="upload-area">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" style="margin: 0 auto 12px auto; display: block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <div style="font-size: 0.85rem; font-weight: 600; color: #4b5563; margin-bottom: 4px;">Klik untuk mengunggah atau seret & lepas</div>
                                <div style="font-size: 0.7rem; color: #9ca3af;">SVG, PNG, JPG atau GIF (maks. 800x400px)</div>
                            </div>
                        </div>

                        <!-- Category -->
                        <div style="margin-bottom: 24px;">
                            <label class="form-label">Kategori</label>
                            <div style="position: relative;">
                                <select name="category_id" class="form-control" style="appearance: none; cursor: pointer; color: #4b5563; background: #f9fafb;">
                                    <option value="" disabled selected>Pilih kategori...</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['category_id'] ?>" <?= isset($_POST['category_id']) && $_POST['category_id'] == $cat['category_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['label']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" style="position: absolute; right: 12px; top: 12px; pointer-events: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div style="margin-bottom: 24px;">
                            <label class="form-label">Tags</label>
                            <div style="display: flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 8px; padding: 6px 14px; background: #f9fafb; flex-wrap: wrap; gap: 4px;" id="tags-container">
                                <input type="text" id="tag-input" placeholder="Tambah tag" style="border: none; background: transparent; outline: none; flex: 1; min-width: 120px; font-size: 0.95rem; font-family: inherit; color: #4b5563;">
                                <input type="hidden" name="tags" id="hidden-tags" value='[]'>
                            </div>
                        </div>

                        <!-- Schedule Publish Date -->
                        <div style="margin-bottom: 8px;">
                            <label class="form-label">Jadwal Tanggal Publikasi</label>
                            <input type="date" name="publish_date" class="form-control" style="background: #f9fafb; color: #4b5563;">
                        </div>
                        
                    </div>

                    <!-- Actions Card -->
                    <div class="card-panel" style="padding: 20px; display: flex; flex-direction: column; gap: 12px;">
                        <button type="submit" name="status" value="draft" class="btn-draft" style="width: 100%; padding: 12px; border-radius: 6px;">
                            Simpan sebagai Draf
                        </button>
                        <button type="submit" name="status" value="published" class="btn-publish" style="width: 100%; padding: 12px; border-radius: 6px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                            Terbitkan Pathfinder
                        </button>
                    </div>

                </div>

            </form>
        </div> 
    </div> 

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tagInput = document.getElementById('tag-input');
            const tagsContainer = document.getElementById('tags-container');
            const hiddenTags = document.getElementById('hidden-tags');
            
            let tags = JSON.parse(hiddenTags.value || '[]');

            function renderTags() {
                // Remove existing chips
                const existingChips = tagsContainer.querySelectorAll('.tag-chip');
                existingChips.forEach(chip => chip.remove());
                
                // Add chips back
                tags.forEach((tag, index) => {
                    const chip = document.createElement('span');
                    chip.className = 'tag-chip';
                    chip.innerHTML = `${tag} <span data-index="${index}">&times;</span>`;
                    tagsContainer.insertBefore(chip, tagInput);
                });
                
                hiddenTags.value = JSON.stringify(tags);
            }
            
            tagInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const value = tagInput.value.trim();
                    if (value && !tags.includes(value)) {
                        tags.push(value);
                        tagInput.value = '';
                        renderTags();
                    }
                }
            });
            
            tagsContainer.addEventListener('click', function(e) {
                if (e.target.tagName === 'SPAN' && e.target.hasAttribute('data-index')) {
                    const index = parseInt(e.target.getAttribute('data-index'));
                    tags.splice(index, 1);
                    renderTags();
                }
            });
            
            // Initial render
            renderTags();
        });
    </script>
</body>
</html>
