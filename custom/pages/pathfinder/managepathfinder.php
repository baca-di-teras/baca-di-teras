<?php
/**
 * Pathfinder – Manajemen Topik Pathfinder (V2)
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

// Handle Form Submission for Create Topic and Delete Topic
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $data = [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'name' => $_POST['name'] ?? '',
            'slug' => $_POST['slug'] ?? '',
            'description' => $_POST['description'] ?? '',
            'icon' => $_POST['icon'] ?? '',
            'banner_image' => $_POST['banner_image'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'sort_order' => (int)($_POST['sort_order'] ?? 0)
        ];
        if ($pfService->createTopic($data)) {
            $success_msg = 'Topik berhasil ditambahkan.';
        } else {
            $error_msg = 'Gagal menambahkan topik.';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($pfService->deleteTopic($id)) {
            $success_msg = 'Topik berhasil dihapus.';
        } else {
            $error_msg = 'Gagal menghapus topik.';
        }
    }
}

// Fetch all categories with topics
$categories = $pfService->getCategories();

// Ambil semua topik tanpa batasan status untuk dikelola di portal admin
$topics = $pfService->getAllAdminTopics();

// Group topics by category
$groupedTopics = [];
foreach ($categories as $c) {
    $groupedTopics[$c['id']] = [
        'category_name' => $c['name'],
        'topics' => []
    ];
}
foreach ($topics as $t) {
    $catId = $t['category_id'];
    if (isset($groupedTopics[$catId])) {
        $groupedTopics[$catId]['topics'][] = $t;
    } else {
        $groupedTopics[$catId] = [
            'category_name' => $t['category_name'] ?? 'Kategori Tidak Diketahui',
            'topics' => [$t]
        ];
    }
}

$total_topics = count($topics);
$total_published = count(array_filter($topics, function($t) { return ($t['status'] ?? 'published') === 'active' || ($t['status'] ?? '') === 'published'; }));
$total_drafts = $total_topics - $total_published;

$admin_active_page = 'pathfinder';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pathfinder - Baca di Teras</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Admin styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/modal.css?v=<?= time() ?>">
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
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Manajemen Pathfinder (V2)</h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;">Kelola dan terbitkan topik panduan literasi pathfinder.</p>
                </div>
                <div style="display: flex; gap: 12px;">
                    <a href="<?= BASE_URL ?>/portal-admin/pathfinder/kategori" class="btn-secondary" style="background: white; border: 1px solid var(--admin-border); color: var(--admin-text-main); padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        Kelola Kategori
                    </a>
                    <button type="button" onclick="openModal('modalForm')" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; background-color: var(--admin-primary); color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; cursor:pointer; border:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Buat Topik Baru
                    </button>
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

            <!-- Stats Overview -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-bottom: 32px;">
                <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 24px; background: white; border-radius: 12px; border: 1px solid var(--admin-border);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #ecfdf5; display: flex; align-items: center; justify-content: center; color: #059669;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Total Topik</div>
                        <div style="font-size: 1.75rem; font-weight: 700; color: var(--admin-text-main); line-height: 1;"><?= $total_topics ?></div>
                    </div>
                </div>

                <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 24px; background: white; border-radius: 12px; border: 1px solid var(--admin-border);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #ecfdf5; display: flex; align-items: center; justify-content: center; color: #059669;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Aktif/Diterbitkan</div>
                        <div style="font-size: 1.75rem; font-weight: 700; color: var(--admin-text-main); line-height: 1;"><?= $total_published ?></div>
                    </div>
                </div>

                <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 24px; background: white; border-radius: 12px; border: 1px solid var(--admin-border);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #b45309;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Draf / Nonaktif</div>
                        <div style="font-size: 1.75rem; font-weight: 700; color: var(--admin-text-main); line-height: 1;"><?= $total_drafts ?></div>
                    </div>
                </div>
            </div>

            <!-- Table Section (Grouped by Category) -->
            <div class="card" style="padding: 0; background: white; border-radius: 12px; border: 1px solid var(--admin-border); overflow: hidden;">
                <div style="padding: 16px 24px; border-bottom: 1px solid var(--admin-border);">
                    <h2 style="font-size: 1.1rem; font-weight: 600; margin: 0; color: var(--admin-text-main);">Daftar Topik (Berdasarkan Kategori)</h2>
                </div>
                
                <?php if (empty($groupedTopics)): ?>
                    <div style="padding: 24px; text-align: center; color: var(--admin-text-muted);">Belum ada kategori atau topik.</div>
                <?php else: ?>
                    <?php foreach($groupedTopics as $catId => $group): ?>
                        <div class="category-group" style="border-bottom: 1px solid var(--admin-border);">
                            <div class="category-header" onclick="toggleCategory('<?= $catId ?>')" style="padding: 16px 24px; background: #fafafa; font-weight: 600; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: background 0.2s;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <svg id="icon-cat-<?= $catId ?>" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="transition: transform 0.2s; transform: rotate(180deg); color: var(--admin-text-muted);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    <span style="color: var(--admin-text-main); font-size: 0.95rem;"><?= htmlspecialchars($group['category_name']) ?> <span style="color: var(--admin-text-muted); font-weight: 400; font-size: 0.85rem; margin-left: 4px;">(<?= count($group['topics']) ?> Topik)</span></span>
                                </div>
                            </div>
                            <div id="cat-<?= $catId ?>" style="display: block;">
                                <?php if (empty($group['topics'])): ?>
                                    <div style="padding: 16px 24px; text-align: center; color: var(--admin-text-muted); font-size: 0.9rem; background: white;">Belum ada topik di kategori ini.</div>
                                <?php else: ?>
                                    <table style="width: 100%; border-collapse: collapse; text-align: left; background: white;">
                                        <thead>
                                            <tr>
                                                <th style="padding: 12px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid var(--admin-border); width: 80px;">ID</th>
                                                <th style="padding: 12px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid var(--admin-border);">Nama Topik</th>
                                                <th style="padding: 12px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid var(--admin-border); width: 120px;">Status</th>
                                                <th style="padding: 12px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid var(--admin-border); text-align: right; width: 150px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($group['topics'] as $t): 
                                                $isPublished = ($t['status'] ?? '') === 'active' || ($t['status'] ?? '') === 'published';
                                                $statusBadge = $isPublished ? 'badge-published' : 'badge-draft';
                                                $statusText = $isPublished ? 'Aktif' : 'Draf';
                                            ?>
                                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                                <td style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.9rem;">
                                                    #<?= htmlspecialchars($t['id']) ?>
                                                </td>
                                                <td style="padding: 16px 24px;">
                                                    <div style="font-weight: 600; color: var(--admin-text-main); font-size: 0.95rem; margin-bottom: 4px;"><?= htmlspecialchars($t['name']) ?></div>
                                                    <div style="font-size: 0.85rem; color: var(--admin-text-muted);"><code style="background:#f3f4f6; padding:2px 4px; border-radius:4px;"><?= htmlspecialchars($t['slug']) ?></code></div>
                                                </td>
                                                <td style="padding: 16px 24px;">
                                                    <span class="badge <?= $statusBadge ?>"><?= $statusText ?></span>
                                                </td>
                                                <td style="padding: 16px 24px; text-align: right; display: flex; justify-content: flex-end; gap: 8px;">
                                                    <a href="<?= BASE_URL ?>/portal-admin/pathfinder/edit?id=<?= $t['id'] ?>" style="background: none; border: none; color: var(--admin-primary); text-decoration:none; cursor: pointer; padding: 4px; border-radius: 4px; font-size: 0.9rem;" title="Kelola">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                    </a>
                                                    <form method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin menghapus topik ini secara permanen?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                                        <button type="submit" style="background: none; border: none; color: #dc2626; cursor: pointer; padding: 4px; border-radius: 4px; font-size: 0.9rem;" title="Hapus">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div> <!-- End Content -->
    </div> <!-- End Main Wrapper -->

    <!-- Modal Form Topik -->
    <div id="modalForm" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 id="modalTitle" style="margin:0; font-size: 1.25rem;">Tambah Topik</h3>
                <span class="close" onclick="closeModal('modalForm')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="topicForm" method="POST">
                    <input type="hidden" name="action" id="formAction" value="create">
                    
                    <div style="margin-bottom: 16px;">
                        <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Kategori</label>
                        <select name="category_id" id="formCategoryId" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
                            <?php foreach($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Nama Topik</label>
                        <input type="text" name="name" id="formName" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Slug (URL Friendly)</label>
                        <input type="text" name="slug" id="formSlug" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Deskripsi Singkat</label>
                        <textarea name="description" id="formDescription" rows="2" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;"></textarea>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Nama Icon</label>
                        <?php
                        $commonIcons = ['book', 'book-open', 'book-open-text', 'books', 'leaf', 'paw-print', 'potted-plant', 'globe', 'heart', 'star', 'bookmark', 'briefcase', 'building', 'calculator', 'camera', 'compass', 'cpu', 'feather', 'flag', 'flask', 'graduation-cap', 'history', 'library', 'lightbulb', 'map', 'microscope', 'monitor', 'music', 'palette', 'pen-tool', 'telescope', 'terminal', 'users'];
                        ?>
                        <select name="icon" id="formIcon" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
                            <option value="">-- Pilih Icon --</option>
                            <?php foreach($commonIcons as $ic): ?>
                                <option value="<?= $ic ?>"><?= $ic ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Status</label>
                            <select name="status" id="formStatus" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
                                <option value="active">Aktif</option>
                                <option value="draft">Draf</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Urutan (Sort Order)</label>
                            <input type="number" name="sort_order" id="formSortOrder" value="0" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; box-sizing:border-box;">
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <button type="button" onclick="closeModal('modalForm')" style="padding: 10px 16px; border: 1px solid #d1d5db; background: white; border-radius: 6px; cursor: pointer; margin-right: 8px;">Batal</button>
                        <button type="submit" style="padding: 10px 16px; background: var(--admin-primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Simpan Topik</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'block';
            if (id === 'modalForm') {
                document.getElementById('topicForm').reset();
            }
        }
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        function toggleCategory(catId) {
            const content = document.getElementById('cat-' + catId);
            const icon = document.getElementById('icon-cat-' + catId);
            if (content.style.display === 'none') {
                content.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Auto generate slug from name
        document.getElementById('formName').addEventListener('input', function() {
            let title = this.value;
            let slug = title.toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/[\s-]+/g, '-');
            document.getElementById('formSlug').value = slug;
        });
    </script>
</body>
</html>
