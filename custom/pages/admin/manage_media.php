<?php
/**
 * CMS – Manajemen Rilis Media
 *
 * File    : manage_media.php
 * Project : Baca Di Teras
 * Version : 1.1.0
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/MediaService.php';
require_once $libPath . '/custom/services/ActivityLogService.php';

// Auth Check
$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin', 'kontributor']);

$mediaService = new MediaService();
$activityService = new ActivityLogService();
$admin_active_page = 'media';

// Hapus Rilis Media
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $media_id = (int)$_POST['media_id'];
    $item = $mediaService->getMediaById($media_id);
    if ($mediaService->deleteMedia($media_id)) {
        if ($item) {
            $activityService->log('menghapus', 'Rilis Media', $item['title']);
        }
        header("Location: " . BASE_URL . "/portal-admin/media?success=deleted");
        exit;
    }
}

// Parameter Filter & Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;
$filter_cat = $_GET['category'] ?? '';
$filter_status = $_GET['status'] ?? '';
$search_query = $_GET['search'] ?? '';

$filters = [];
if ($filter_cat) $filters['category'] = $filter_cat;
if ($filter_status) $filters['status'] = $filter_status;
if ($search_query) $filters['search'] = $search_query;

$offset = ($page - 1) * $per_page;
$total = $mediaService->countAdminMedia($filters);
$total_pages = max(1, ceil($total / $per_page));

$mediaList = $mediaService->getAdminMedia($filters, $per_page, $offset);

/**
 * Helper class badge kategori
 */
function getMediaBadgeClass($category) {
    switch ($category) {
        case 'Media Nasional':
            return 'badge-nasional';
        case 'Web Prodi':
            return 'badge-prodi';
        case 'Web Desa':
            return 'badge-desa';
        default:
            return 'badge-prodi';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Rilis Media - Baca di Teras</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .table-wrapper { background: white; border-radius: 12px; border: 1px solid var(--admin-border); overflow: hidden; }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { background: #f9fafb; padding: 14px 20px; text-align: left; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-muted); border-bottom: 1px solid var(--admin-border); text-transform: uppercase; letter-spacing: 0.05em; }
        .admin-table td { padding: 16px 20px; font-size: 0.9rem; color: var(--admin-text-main); border-bottom: 1px solid var(--admin-border); vertical-align: middle; }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover { background-color: #f9fafb; }
        
        .badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-published { background: #dcfce7; color: #166534; }
        .badge-draft { background: #fef3c7; color: #92400e; }
        .badge-nasional { background: #ffebee; color: #c62828; }
        .badge-prodi { background: #e0e7ff; color: #3730a3; }
        .badge-desa { background: #e8f5e9; color: #2e7d32; }

        .btn-action { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; border: none; cursor: pointer; transition: 0.2s; background: transparent; text-decoration: none; }
        .btn-edit { color: #2563eb; }
        .btn-edit:hover { background: #dbeafe; }
        .btn-delete { color: #dc2626; }
        .btn-delete:hover { background: #fee2e2; }
        
        .filter-bar { display: flex; gap: 12px; margin-bottom: 24px; align-items: center; background: white; padding: 16px; border-radius: 12px; border: 1px solid var(--admin-border); flex-wrap: wrap; }
        .filter-input { padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 0.9rem; font-family: 'Inter', sans-serif; outline: none; min-width: 200px; }
        .filter-input:focus { border-color: var(--admin-primary); }
        .filter-select { padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 0.9rem; font-family: 'Inter', sans-serif; outline: none; }
        .filter-select:focus { border-color: var(--admin-primary); }
        .filter-btn { padding: 8px 16px; background: var(--admin-primary); color: white; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; }
        .filter-btn:hover { opacity: 0.9; }

        .pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 24px; }
        .pagination-info { font-size: 0.9rem; color: var(--admin-text-muted); }
        .pagination-links { display: flex; gap: 8px; }
        .pagination-links a, .pagination-links span { padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 0.9rem; text-decoration: none; color: var(--admin-text-main); background: white; }
        .pagination-links a:hover { background: #f9fafb; }
        .pagination-links span.active { background: var(--admin-primary); color: white; border-color: var(--admin-primary); }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content" style="padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">
            
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <div class="page-title">
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Manajemen Rilis Media</h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;">Kelola liputan berita, media nasional, web prodi, dan web desa.</p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/portal-admin/media/tambah" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; background-color: var(--admin-primary); color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Buat Rilis Media
                    </a>
                </div>
            </div>

            <!-- Feedback Alerts -->
            <?php if (isset($_GET['success']) && $_GET['success'] === 'created'): ?>
            <div style="background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:0.9rem;">
                Rilis media berhasil ditambahkan.
            </div>
            <?php endif; ?>
            <?php if (isset($_GET['success']) && $_GET['success'] === 'updated'): ?>
            <div style="background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:0.9rem;">
                Rilis media berhasil diperbarui.
            </div>
            <?php endif; ?>
            <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
            <div style="background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:0.9rem;">
                Rilis media berhasil dihapus.
            </div>
            <?php endif; ?>
            <?php if (isset($_GET['success']) && $_GET['success'] === 'toggled'): ?>
            <div style="background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:0.9rem;">
                Status terbit rilis media berhasil diperbarui.
            </div>
            <?php endif; ?>

            <!-- Filter & Search Settings -->
            <form method="GET" class="filter-bar">
                <input type="text" name="search" class="filter-input" placeholder="Cari judul / media..." value="<?= htmlspecialchars($search_query) ?>">

                <select name="per_page" class="filter-select">
                    <option value="10" <?= $per_page == 10 ? 'selected' : '' ?>>10 per halaman</option>
                    <option value="25" <?= $per_page == 25 ? 'selected' : '' ?>>25 per halaman</option>
                    <option value="50" <?= $per_page == 50 ? 'selected' : '' ?>>50 per halaman</option>
                </select>

                <select name="category" class="filter-select">
                    <option value="">Semua Kategori</option>
                    <option value="Media Nasional" <?= $filter_cat == 'Media Nasional' ? 'selected' : '' ?>>Media Nasional</option>
                    <option value="Web Prodi" <?= $filter_cat == 'Web Prodi' ? 'selected' : '' ?>>Web Prodi</option>
                    <option value="Web Desa" <?= $filter_cat == 'Web Desa' ? 'selected' : '' ?>>Web Desa</option>
                    <option value="Lainnya" <?= $filter_cat == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                </select>

                <select name="status" class="filter-select">
                    <option value="">Semua Status</option>
                    <option value="published" <?= $filter_status == 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= $filter_status == 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>

                <button type="submit" class="filter-btn">Terapkan Filter</button>
            </form>

            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>JUDUL RILIS MEDIA</th>
                            <th>KATEGORI</th>
                            <th>STATUS</th>
                            <th>TANGGAL PUBLIKASI</th>
                            <th style="text-align: right;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mediaList)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 32px; color: var(--admin-text-muted);">
                                Belum ada rilis media.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($mediaList as $item): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; margin-bottom: 4px; font-size: 0.95rem; color: var(--admin-text-main); display: flex; align-items: center; gap: 8px;">
                                        <?= htmlspecialchars($item['title']) ?>
                                        <?php if (!empty($item['is_pinned'])): ?>
                                            <span title="Disematkan (Pinned)" style="font-size: 0.95rem; cursor: help;">📌</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--admin-text-muted);">
                                        Media: <strong style="color: #374151;"><?= htmlspecialchars($item['media_name']) ?></strong> &bull; 
                                        <a href="<?= htmlspecialchars($item['url']) ?>" target="_blank" rel="noopener noreferrer" style="color: #2563eb; text-decoration: none;">Tautan Berita ↗</a>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= getMediaBadgeClass($item['category'] ?? '') ?>"><?= htmlspecialchars($item['category']) ?></span>
                                </td>
                                <td>
                                    <?php if ($item['is_published']): ?>
                                        <span class="badge badge-published">Published</span>
                                    <?php else: ?>
                                        <span class="badge badge-draft">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--admin-text-main); white-space: nowrap;">
                                    <?= !empty($item['release_date']) ? date('d M Y H:i', strtotime($item['release_date'])) : '-' ?>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="<?= BASE_URL ?>/portal-admin/media/edit?id=<?= $item['id'] ?>" class="btn-action btn-edit" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </a>
                                    <form action="<?= BASE_URL ?>/portal-admin/media" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus rilis media ini?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="media_id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="btn-action btn-delete" title="Hapus">
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

            <!-- Pagination UI -->
            <div class="pagination">
                <div class="pagination-info">
                    Menampilkan <?= $total > 0 ? min($offset + 1, $total) : 0 ?> - <?= min($offset + $per_page, $total) ?> dari <?= $total ?> rilis media
                </div>
                <?php if ($total_pages > 1): ?>
                <div class="pagination-links">
                    <?php 
                        $queryString = $_GET;
                        unset($queryString['page']);
                        $qs = http_build_query($queryString);
                        $qs = $qs ? '&' . $qs : '';
                    ?>
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?><?= $qs ?>">Sebelumnya</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?><?= $qs ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?><?= $qs ?>">Selanjutnya</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</body>
</html>
