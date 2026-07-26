<?php
/**
 * CMS – Manajemen Artikel & Berita
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/ArticleService.php';

// Auth Check
$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin', 'kontributor']);

$articleService = new ArticleService();
$admin_active_page = 'article';

// Hapus Artikel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $article_id = (int)$_POST['article_id'];
    $articleService->deleteArticle($article_id);
    header("Location: " . BASE_URL . "/portal-admin/artikel");
    exit;
}

$articles = $articleService->getAllArticles();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Artikel - Baca di Teras</title>
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
        .badge-archived { background: #f3f4f6; color: #4b5563; }
        .badge-cat { background: #e0e7ff; color: #3730a3; }

        .btn-action { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; border: none; cursor: pointer; transition: 0.2s; background: transparent; }
        .btn-edit { color: #2563eb; }
        .btn-edit:hover { background: #dbeafe; }
        .btn-delete { color: #dc2626; }
        .btn-delete:hover { background: #fee2e2; }

        .article-thumb { width: 64px; height: 48px; object-fit: cover; border-radius: 6px; background-color: #f3f4f6; }
        .featured-icon { color: #f59e0b; display: inline-block; vertical-align: middle; margin-left: 4px; }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content" style="padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">
            
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <div class="page-title">
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Manajemen Berita & Artikel</h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;">Kelola berita, kegiatan, dan publikasi lainnya.</p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/portal-admin/artikel/create" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; background-color: var(--admin-primary); color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Buat Artikel
                    </a>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Cover</th>
                            <th>Judul Artikel</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tanggal Publikasi</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($articles)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 32px; color: var(--admin-text-muted);">
                                Belum ada artikel.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($articles as $article): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($article['cover_image'])): ?>
                                        <?php 
                                            $imgSrc = $article['cover_image'];
                                            if (strpos($imgSrc, '/custom/') === 0 && strpos($imgSrc, BASE_URL) !== 0) {
                                                $imgSrc = rtrim(BASE_URL, '/') . $imgSrc;
                                            } elseif (strpos($imgSrc, 'http') !== 0 && strpos($imgSrc, BASE_URL) !== 0) {
                                                $imgSrc = rtrim(BASE_URL, '/') . '/' . ltrim($imgSrc, '/');
                                            }
                                        ?>
                                        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="Cover" class="article-thumb">
                                    <?php else: ?>
                                        <div class="article-thumb" style="display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600; margin-bottom: 4px; display: flex; align-items: center;">
                                        <?= htmlspecialchars($article['title']) ?>
                                        <?php if ($article['is_featured']): ?>
                                            <svg class="featured-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                        <?php endif; ?>
                                        <?php if ($article['is_pinned']): ?>
                                            <svg class="featured-icon" style="color:#ef4444;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--admin-text-muted);">
                                        Ditulis: <?= date('d M Y', strtotime($article['created_at'])) ?> &bull; 
                                        Views: <?= (int)$article['view_count'] ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-cat"><?= ucfirst($article['category']) ?></span>
                                </td>
                                <td>
                                    <?php 
                                        $statusClass = 'badge-draft';
                                        if ($article['status'] === 'published') $statusClass = 'badge-published';
                                        if ($article['status'] === 'archived') $statusClass = 'badge-archived';
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= ucfirst($article['status']) ?></span>
                                </td>
                                <td style="font-size: 0.85rem;">
                                    <?= $article['publish_date'] ? date('d M Y H:i', strtotime($article['publish_date'])) : '-' ?>
                                </td>
                                <td style="text-align: right;">
                                    <a href="<?= BASE_URL ?>/portal-admin/artikel/edit?id=<?= $article['article_id'] ?>" class="btn-action btn-edit" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form action="<?= BASE_URL ?>/portal-admin/artikel" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus artikel ini?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="article_id" value="<?= $article['article_id'] ?>">
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

        </div>
    </div>

</body>
</html>
