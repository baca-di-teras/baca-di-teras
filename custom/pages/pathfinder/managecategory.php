<?php
/**
 * Pathfinder – Manajemen Kategori
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
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/modal.css">
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
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Manajemen Kategori</h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;">Kelola kategori pathfinder dan metadata kategorisasi.</p>
                </div>
                <div style="display: flex; gap: 12px;">
                    <a href="<?= BASE_URL ?>/portal-admin/pathfinder" class="btn-secondary" style="background: white; border: 1px solid var(--admin-border); color: var(--admin-text-main); padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali ke Pathfinder
                    </a>
                    <button type="button" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; background-color: var(--admin-primary); color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; cursor: pointer; border: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Tambah Kategori
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div style="display: grid; grid-template-columns: 1fr; gap: 24px; margin-bottom: 32px;">
                <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 24px; background: white; border-radius: 12px; border: 1px solid var(--admin-border);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #e0e7ff; display: flex; align-items: center; justify-content: center; color: #4338ca;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Total Kategori</div>
                        <div style="font-size: 1.75rem; font-weight: 700; color: var(--admin-text-main); line-height: 1;"><?= $total_categories ?></div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="card" style="padding: 0; background: white; border-radius: 12px; border: 1px solid var(--admin-border); overflow: hidden;">
                <div style="padding: 16px 24px; border-bottom: 1px solid var(--admin-border); display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; gap: 24px;">
                        <a href="#" style="color: var(--admin-primary); font-weight: 600; text-decoration: none; border-bottom: 2px solid var(--admin-primary); padding-bottom: 17px; margin-bottom: -17px;">Semua</a>
                    </div>
                    <form action="<?= BASE_URL ?>/custom/scripts/kategorisasi_buku.php" method="POST" style="margin: 0;">
                        <button type="submit" style="background: var(--admin-primary); color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: 0.2s;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            Jalankan Kategorisasi Buku
                        </button>
                    </form>
                </div>
                
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: #fafafa;">
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--admin-border);">ID</th>
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--admin-border);">Kategori</th>
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--admin-border);">Slug / Ikon</th>
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--admin-border);">Tags Topik</th>
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--admin-border); text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="5" style="padding: 24px; text-align: center; color: var(--admin-text-muted);">Belum ada kategori pathfinder.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($categories as $cat): 
                                $tags = is_string($cat['tags']) ? json_decode($cat['tags'], true) : $cat['tags'];
                                if(!is_array($tags)) $tags = [];
                            ?>
                            <tr style="border-bottom: 1px solid var(--admin-border);">
                                <td style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.9rem;">
                                    #<?= htmlspecialchars($cat['category_id']) ?>
                                </td>
                                <td style="padding: 16px 24px;">
                                    <div style="font-weight: 600; color: var(--admin-text-main); font-size: 0.95rem; margin-bottom: 4px;"><?= htmlspecialchars($cat['label']) ?></div>
                                    <div style="font-size: 0.85rem; color: var(--admin-text-muted); max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($cat['description']) ?></div>
                                </td>
                                <td style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.9rem;">
                                    <div style="margin-bottom: 4px;"><code><?= htmlspecialchars($cat['slug']) ?></code></div>
                                    <div style="display: flex; align-items: center; gap: 6px; font-size: 0.85rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <?= htmlspecialchars($cat['icon']) ?>
                                    </div>
                                </td>
                                <td style="padding: 16px 24px;">
                                    <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                        <?php foreach(array_slice($tags, 0, 3) as $t): ?>
                                            <span style="background: #f3f4f6; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; color: #4b5563; font-weight: 500; border: 1px solid #e5e7eb;"><?= htmlspecialchars($t) ?></span>
                                        <?php endforeach; ?>
                                        <?php if(count($tags) > 3): ?>
                                            <span style="background: #f3f4f6; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; color: #4b5563; font-weight: 500; border: 1px solid #e5e7eb;">+<?= count($tags) - 3 ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="padding: 16px 24px; text-align: right;">
                                    <button style="background: none; border: none; color: var(--admin-text-muted); cursor: pointer; padding: 4px; border-radius: 4px; transition: 0.2s;" onmouseover="this.style.color='var(--admin-primary)'" onmouseout="this.style.color='var(--admin-text-muted)'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div> <!-- End Content -->
    </div> <!-- End Main Wrapper -->

</body>
</html>