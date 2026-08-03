<?php
/**
 * Pathfinder – Manajemen Detail Topik (V2)
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/PathfinderV2Service.php';

// Auth Check
$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin']);

$pfService = new PathfinderV2Service();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$topic = $pfService->getTopicById($id);

if (!$topic) {
    die('Topik tidak ditemukan.');
}

$intro = $pfService->getIntroduction($id) ?: [];

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_topic') {
        $data = [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'name' => $_POST['name'] ?? '',
            'slug' => $_POST['slug'] ?? '',
            'description' => $_POST['description'] ?? '',
            'icon' => $_POST['icon'] ?? '',
            'banner_image' => $_POST['banner_image'] ?? '',
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status' => $_POST['status'] ?? 'draft'
        ];
        if ($pfService->updateTopic($id, $data)) {
            $success_msg = 'Detail topik berhasil diperbarui.';
            // Refresh topic data
            $topic = $pfService->getTopicById($id);
        } else {
            $error_msg = 'Gagal memperbarui detail topik.';
        }
    } elseif ($action === 'update_intro') {
        $data = [
            'definition' => $_POST['definition'] ?? '',
            'learning_objectives' => $_POST['learning_objectives'] ?? '',
            'importance' => $_POST['importance'] ?? '',
            'topics_to_learn' => $_POST['topics_to_learn'] ?? ''
        ];
        if ($pfService->upsertIntroduction($id, $data)) {
            $success_msg = 'Pendahuluan topik berhasil diperbarui.';
            $intro = $pfService->getIntroduction($id);
        } else {
            $error_msg = 'Gagal memperbarui pendahuluan.';
        }
    }
}

$categories = $pfService->getCategories();

$admin_active_page = 'pathfinder';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Topik - <?= htmlspecialchars($topic['name']) ?> - Baca di Teras</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Admin styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    
    <style>
        .editor-wrapper {
            border: 1px solid var(--admin-border, #d1d5db);
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            margin-top: 8px;
        }
        .editor-toolbar {
            display: flex;
            gap: 4px;
            padding: 8px;
            background: #f9fafb;
            border-bottom: 1px solid var(--admin-border, #d1d5db);
            flex-wrap: wrap;
        }
        .editor-btn {
            background: none;
            border: 1px solid transparent;
            padding: 6px;
            border-radius: 4px;
            cursor: pointer;
            color: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
            font-weight: bold;
            font-family: serif;
        }
        .editor-btn:hover {
            background: #e5e7eb;
        }
        .editor-btn svg {
            width: 16px;
            height: 16px;
            stroke-width: 2;
        }
        .editor-divider {
            width: 1px;
            background: #d1d5db;
            margin: 0 4px;
        }
        .editor-content {
            min-height: 120px;
            padding: 12px 14px;
            font-size: 0.95rem;
            font-family: inherit;
            outline: none;
            overflow-y: auto;
            resize: vertical;
        }
        .editor-content:empty:before {
            content: attr(data-placeholder);
            color: #9ca3af;
        }
        .editor-content p { margin: 0 0 10px 0; }
        .editor-content ul, .editor-content ol { margin: 0 0 10px 20px; padding: 0; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <!-- Main Wrapper -->
    <div class="admin-main">
        
        <!-- Topbar -->
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <!-- Content -->
        <div class="admin-content" style="padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">
            
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <div class="page-title">
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Kelola Topik: <?= htmlspecialchars($topic['name']) ?></h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;">Kelola detail dan pendahuluan untuk topik ini.</p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/portal-admin/pathfinder" class="btn-secondary" style="background: white; border: 1px solid var(--admin-border); color: var(--admin-text-main); padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali ke Pathfinder
                    </a>
                </div>
            </div>

            <?php if ($success_msg): ?>
                <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px;">
                    <?= htmlspecialchars($success_msg) ?>
                </div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
                <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px;">
                    <?= htmlspecialchars($error_msg) ?>
                </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
                <!-- Left Column: Topic Metadata -->
                <div class="card" style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--admin-border); height: fit-content;">
                    <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 20px;">Detail Topik</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_topic">
                        
                        <div style="margin-bottom: 16px;">
                            <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Kategori</label>
                            <select name="category_id" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
                                <?php foreach($categories as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $c['id'] == $topic['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Nama Topik</label>
                            <input type="text" name="name" id="formName" value="<?= htmlspecialchars($topic['name']) ?>" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Slug</label>
                            <input type="text" name="slug" id="formSlug" value="<?= htmlspecialchars($topic['slug']) ?>" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Deskripsi Singkat</label>
                            <textarea name="description" rows="3" style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; box-sizing:border-box; resize:vertical;"><?= htmlspecialchars($topic['description']) ?></textarea>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Nama Icon</label>
                            <?php
                            $commonIcons = ['book', 'book-open', 'book-open-text', 'books', 'leaf', 'paw-print', 'potted-plant', 'globe', 'heart', 'star', 'bookmark', 'briefcase', 'building', 'calculator', 'camera', 'compass', 'cpu', 'feather', 'flag', 'flask', 'graduation-cap', 'history', 'library', 'lightbulb', 'map', 'microscope', 'monitor', 'music', 'palette', 'pen-tool', 'telescope', 'terminal', 'users'];
                            ?>
                            <select name="icon" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
                                <option value="">-- Pilih Icon --</option>
                                <?php foreach($commonIcons as $ic): ?>
                                    <option value="<?= $ic ?>" <?= $topic['icon'] == $ic ? 'selected' : '' ?>><?= $ic ?></option>
                                <?php endforeach; ?>
                                <?php if($topic['icon'] && !in_array($topic['icon'], $commonIcons)): ?>
                                    <option value="<?= htmlspecialchars($topic['icon']) ?>" selected><?= htmlspecialchars($topic['icon']) ?> (Kustom)</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Status</label>
                            <select name="status" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
                                <option value="active" <?= ($topic['status'] ?? '') === 'active' || ($topic['status'] ?? '') === 'published' ? 'selected' : '' ?>>Aktif</option>
                                <option value="draft" <?= ($topic['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draf</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 24px;">
                            <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Urutan (Sort Order)</label>
                            <input type="number" name="sort_order" value="<?= htmlspecialchars($topic['sort_order']) ?>" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
                        </div>

                        <button type="submit" style="width: 100%; padding: 10px 16px; background: var(--admin-primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Update Detail</button>
                    </form>
                </div>

                <!-- Right Column: Topic Introduction -->
                <div class="card" style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--admin-border);">
                    <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 20px;">Pendahuluan Topik (Introduction)</h2>
                    <form method="POST" id="introForm">
                        <input type="hidden" name="action" value="update_intro">
                        
                        <?php 
                            // Reusable custom editor template
                            function renderEditor($id, $label, $value, $description = '') {
                        ?>
                        <div style="margin-bottom: 24px;">
                            <label style="display:block; font-weight:600; font-size:0.9rem;"><?= htmlspecialchars($label) ?></label>
                            <?php if ($description): ?>
                            <div style="font-size: 0.8rem; color: var(--admin-text-muted); margin-top: 4px; margin-bottom: 8px;"><?= htmlspecialchars($description) ?></div>
                            <?php endif; ?>
                            
                            <div class="editor-wrapper">
                                <div class="editor-toolbar">
                                    <button type="button" class="editor-btn" onclick="formatText('bold')" title="Bold (Ctrl+B)">B</button>
                                    <button type="button" class="editor-btn" onclick="formatText('italic')" style="font-style:italic;" title="Italic (Ctrl+I)">I</button>
                                    <button type="button" class="editor-btn" onclick="formatText('underline')" style="text-decoration:underline;" title="Underline (Ctrl+U)">U</button>
                                    <div class="editor-divider"></div>
                                    <button type="button" class="editor-btn" onclick="formatText('insertUnorderedList')" title="Bullet List">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                    </button>
                                    <button type="button" class="editor-btn" onclick="formatText('insertOrderedList')" title="Numbered List">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h13M7 13h13M7 19h13M3 7h.01M3 13h.01M3 19h.01M3 4v3" /></svg>
                                    </button>
                                    <div class="editor-divider"></div>
                                    <button type="button" class="editor-btn" onclick="formatText('justifyLeft')" title="Align Left">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h16" /></svg>
                                    </button>
                                    <button type="button" class="editor-btn" onclick="formatText('justifyCenter')" title="Align Center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M7 12h10M4 18h16" /></svg>
                                    </button>
                                    <button type="button" class="editor-btn" onclick="formatText('justifyRight')" title="Align Right">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M10 12h10M4 18h16" /></svg>
                                    </button>
                                </div>
                                <div class="editor-content" id="editor-<?= $id ?>" contenteditable="true" oninput="syncEditor('<?= $id ?>')"><?= $value // Intentionally rendering HTML ?></div>
                                <textarea name="<?= $id ?>" id="hidden-<?= $id ?>" style="display:none;"><?= htmlspecialchars($value) ?></textarea>
                            </div>
                        </div>
                        <?php } ?>

                        <?php 
                        renderEditor('definition', 'Definisi', $intro['definition'] ?? '', 'Penjelasan umum mengenai topik ini.');
                        renderEditor('learning_objectives', 'Tujuan Pembelajaran (Learning Objectives)', $intro['learning_objectives'] ?? '', 'Apa yang diharapkan dipelajari oleh pembaca.');
                        renderEditor('importance', 'Kenapa Penting (Importance)', $intro['importance'] ?? '');
                        renderEditor('topics_to_learn', 'Topik/Sub-topik yang Perlu Dipelajari', $intro['topics_to_learn'] ?? '');
                        ?>

                        <button type="submit" style="padding: 10px 24px; background: #059669; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Simpan Pendahuluan</button>
                    </form>
                </div>
            </div>

        </div> <!-- End Content -->
    </div> <!-- End Main Wrapper -->

    <script>
    function formatText(command) {
        document.execCommand(command, false, null);
    }
    
    function syncEditor(id) {
        document.getElementById('hidden-' + id).value = document.getElementById('editor-' + id).innerHTML;
    }
    
    // Fallback sync on form submit
    document.getElementById('introForm').addEventListener('submit', function() {
        ['definition', 'learning_objectives', 'importance', 'topics_to_learn'].forEach(id => {
            syncEditor(id);
        });
    });

    // Auto generate slug from name
    document.getElementById('formName').addEventListener('input', function() {
        let title = this.value;
        let slug = title.toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/[\s-]+/g, '-');
        document.getElementById('formSlug').value = slug;
    });
    </script>
</body>
</html>
