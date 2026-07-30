<?php
/**
 * Pathfinder – Manajemen Kategori (V2)
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

// Handle Form Submission
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $data = [
            'name' => $_POST['name'] ?? '',
            'slug' => $_POST['slug'] ?? '',
            'description' => $_POST['description'] ?? '',
            'icon' => $_POST['icon'] ?? '',
            'status' => $_POST['status'] ?? 'active',
            'sort_order' => (int)($_POST['sort_order'] ?? 0)
        ];
        if ($pfService->createCategory($data)) {
            $success_msg = 'Kategori berhasil ditambahkan.';
        } else {
            $error_msg = 'Gagal menambahkan kategori.';
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name' => $_POST['name'] ?? '',
            'slug' => $_POST['slug'] ?? '',
            'description' => $_POST['description'] ?? '',
            'icon' => $_POST['icon'] ?? '',
            'status' => $_POST['status'] ?? 'active',
            'sort_order' => (int)($_POST['sort_order'] ?? 0)
        ];
        if ($pfService->updateCategory($id, $data)) {
            $success_msg = 'Kategori berhasil diperbarui.';
        } else {
            $error_msg = 'Gagal memperbarui kategori.';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($pfService->deleteCategory($id)) {
            $success_msg = 'Kategori berhasil dihapus.';
        } else {
            $error_msg = 'Gagal menghapus kategori.';
        }
    }
}

$categories = $pfService->getCategories();
$total_categories = count($categories);

$admin_active_page = 'pathfinder';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori - Baca di Teras</title>
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
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Manajemen Kategori (V2)</h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;">Kelola kategori pathfinder V2.</p>
                </div>
                <div style="display: flex; gap: 12px;">
                    <a href="<?= BASE_URL ?>/portal-admin/pathfinder" class="btn-secondary" style="background: white; border: 1px solid var(--admin-border); color: var(--admin-text-main); padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali ke Pathfinder
                    </a>
                    <button type="button" onclick="openModal('modalForm')" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; background-color: var(--admin-primary); color: white; padding: 10px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Tambah Kategori
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

            <!-- Table Section -->
            <div class="card" style="padding: 0; background: white; border-radius: 12px; border: 1px solid var(--admin-border); overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: #fafafa;">
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid var(--admin-border);">ID</th>
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid var(--admin-border);">Kategori</th>
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid var(--admin-border);">Slug / Ikon</th>
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid var(--admin-border);">Urutan</th>
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid var(--admin-border); text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="5" style="padding: 24px; text-align: center; color: var(--admin-text-muted);">Belum ada kategori pathfinder V2.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($categories as $cat): ?>
                            <tr style="border-bottom: 1px solid var(--admin-border);">
                                <td style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.9rem;">
                                    #<?= htmlspecialchars($cat['id']) ?>
                                </td>
                                <td style="padding: 16px 24px;">
                                    <div style="font-weight: 600; color: var(--admin-text-main); font-size: 0.95rem; margin-bottom: 4px;"><?= htmlspecialchars($cat['name']) ?></div>
                                    <div style="font-size: 0.85rem; color: var(--admin-text-muted); max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($cat['description']) ?></div>
                                </td>
                                <td style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.9rem;">
                                    <div style="margin-bottom: 4px;"><code><?= htmlspecialchars($cat['slug']) ?></code></div>
                                    <div style="display: flex; align-items: center; gap: 6px; font-size: 0.85rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <?= htmlspecialchars($cat['icon']) ?>
                                    </div>
                                </td>
                                <td style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.9rem;">
                                    <?= htmlspecialchars($cat['sort_order']) ?>
                                </td>
                                <td style="padding: 16px 24px; text-align: right; display: flex; justify-content: flex-end; gap: 8px;">
                                    <button type="button" onclick='editCategory(<?= json_encode($cat) ?>)' style="background: none; border: none; color: var(--admin-primary); cursor: pointer; padding: 4px; border-radius: 4px;" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <form method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Semua topik didalamnya akan hilang/terdampak!');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                        <button type="submit" style="background: none; border: none; color: #dc2626; cursor: pointer; padding: 4px; border-radius: 4px;" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form Kategori -->
    <div id="modalForm" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 id="modalTitle" style="margin:0; font-size: 1.25rem;">Tambah Kategori</h3>
                <span class="close" onclick="closeModal('modalForm')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="catForm" method="POST">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="formId" value="">
                    
                    <div style="margin-bottom: 16px;">
                        <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Nama Kategori</label>
                        <input type="text" name="name" id="formName" required style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; box-sizing:border-box;">
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Slug (URL Friendly)</label>
                        <input type="text" name="slug" id="formSlug" required style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; box-sizing:border-box;">
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Deskripsi</label>
                        <textarea name="description" id="formDescription" rows="3" style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; box-sizing:border-box; resize:vertical;"></textarea>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Nama Icon</label>
                            <?php
                            $commonIcons = ['book', 'book-open', 'book-open-text', 'books', 'leaf', 'paw-print', 'potted-plant', 'globe', 'heart', 'star', 'bookmark', 'briefcase', 'building', 'calculator', 'camera', 'compass', 'cpu', 'feather', 'flag', 'flask', 'graduation-cap', 'history', 'library', 'lightbulb', 'map', 'microscope', 'monitor', 'music', 'palette', 'pen-tool', 'telescope', 'terminal', 'users'];
                            ?>
                            <select name="icon" id="formIcon" style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; box-sizing:border-box;">
                                <option value="">-- Pilih Icon --</option>
                                <?php foreach($commonIcons as $ic): ?>
                                    <option value="<?= $ic ?>"><?= $ic ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:8px; font-size:0.9rem;">Urutan (Sort Order)</label>
                            <input type="number" name="sort_order" id="formSortOrder" value="0" style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; box-sizing:border-box;">
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <button type="button" onclick="closeModal('modalForm')" style="padding: 10px 16px; border: 1px solid #d1d5db; background: white; border-radius: 6px; cursor: pointer; margin-right: 8px;">Batal</button>
                        <button type="submit" style="padding: 10px 16px; background: var(--admin-primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'block';
            if (id === 'modalForm') {
                document.getElementById('formAction').value = 'create';
                document.getElementById('formId').value = '';
                document.getElementById('catForm').reset();
                document.getElementById('modalTitle').innerText = 'Tambah Kategori';
            }
        }
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        function editCategory(cat) {
            openModal('modalForm');
            document.getElementById('modalTitle').innerText = 'Edit Kategori';
            document.getElementById('formAction').value = 'update';
            document.getElementById('formId').value = cat.id;
            document.getElementById('formName').value = cat.name;
            document.getElementById('formSlug').value = cat.slug;
            document.getElementById('formDescription').value = cat.description;
            document.getElementById('formIcon').value = cat.icon;
            document.getElementById('formSortOrder').value = cat.sort_order;
        }

        // Auto generate slug from name
        document.getElementById('formName').addEventListener('input', function() {
            // Only auto-generate if we are creating a new category, 
            // or if the user wants it to always update, we can just update it.
            // Based on request, we update it automatically on input.
            let title = this.value;
            let slug = title.toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/[\s-]+/g, '-');
            document.getElementById('formSlug').value = slug;
        });
    </script>
</body>
</html>