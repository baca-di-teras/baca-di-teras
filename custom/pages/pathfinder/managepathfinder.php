<?php
/**
 * Pathfinder – Manajemen Pathfinder
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
$pathfinders = $pfService->getAllPathfinders(); 

$total_pathfinders = count($pathfinders);
$total_published = count(array_filter($pathfinders, function($p) {
    return ($p['status'] ?? 'published') === 'published';
}));
$total_drafts = count(array_filter($pathfinders, function($p) {
    return ($p['status'] ?? '') === 'draft';
}));

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
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/modal.css">
    <link rel="icon" type="image/png" href="<?= defined('BASE_URL') ? BASE_URL : '/baca-di-teras' ?>/custom/assets/images/logo_header.png">
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
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Manajemen Pathfinder</h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;">Kelola dan terbitkan panduan literasi pathfinder.</p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/custom/pages/pathfinder/createpathfinder.php" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; background-color: var(--admin-primary); color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Buat Pathfinder Baru
                    </a>
                </div>
            </div>

            <!-- Stats Overview -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-bottom: 32px;">
                <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 24px; background: white; border-radius: 12px; border: 1px solid var(--admin-border);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #ecfdf5; display: flex; align-items: center; justify-content: center; color: #059669;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Total Pathfinder</div>
                        <div style="font-size: 1.75rem; font-weight: 700; color: var(--admin-text-main); line-height: 1;"><?= $total_pathfinders ?></div>
                    </div>
                </div>

                <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 24px; background: white; border-radius: 12px; border: 1px solid var(--admin-border);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #ecfdf5; display: flex; align-items: center; justify-content: center; color: #059669;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Diterbitkan</div>
                        <div style="font-size: 1.75rem; font-weight: 700; color: var(--admin-text-main); line-height: 1;"><?= $total_published ?></div>
                    </div>
                </div>

                <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 24px; background: white; border-radius: 12px; border: 1px solid var(--admin-border);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #b45309;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Draf</div>
                        <div style="font-size: 1.75rem; font-weight: 700; color: var(--admin-text-main); line-height: 1;"><?= $total_drafts ?></div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="card" style="padding: 0; background: white; border-radius: 12px; border: 1px solid var(--admin-border); overflow: hidden;">
                <div style="padding: 16px 24px; border-bottom: 1px solid var(--admin-border); display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; gap: 24px;">
                        <a href="#" style="color: var(--admin-primary); font-weight: 600; text-decoration: none; border-bottom: 2px solid var(--admin-primary); padding-bottom: 17px; margin-bottom: -17px;">Semua</a>
                        <a href="#" style="color: var(--admin-text-muted); font-weight: 500; text-decoration: none; padding-bottom: 17px; margin-bottom: -17px;">Diterbitkan</a>
                        <a href="#" style="color: var(--admin-text-muted); font-weight: 500; text-decoration: none; padding-bottom: 17px; margin-bottom: -17px;">Draf</a>
                    </div>
                    <button style="background: white; border: 1px solid var(--admin-border); color: var(--admin-text-main); padding: 8px 16px; border-radius: 6px; font-weight: 500; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                        Filter
                    </button>
                </div>
                
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: #fafafa;">
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--admin-border);">Judul</th>
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--admin-border);">Penulis</th>
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--admin-border);">Kategori</th>
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--admin-border);">Status</th>
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--admin-border);">Tanggal</th>
                            <th style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--admin-border);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pathfinders)): ?>
                            <tr>
                                <td colspan="6" style="padding: 24px; text-align: center; color: var(--admin-text-muted);">Belum ada pathfinder.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($pathfinders as $pf): 
                                $isPublished = ($pf['status'] ?? 'published') === 'published';
                                $statusBadge = $isPublished ? 'badge-published' : 'badge-draft';
                                $statusText = $isPublished ? 'Diterbitkan' : 'Draf';
                                $authorName = 'Admin'; // Bisa diambil dari tabel users jika ada
                                $formattedDate = date('M d, Y', strtotime($pf['updated_at']));
                            ?>
                            <tr style="border-bottom: 1px solid var(--admin-border);">
                                <td style="padding: 16px 24px; display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 30px; border-radius: 4px; background: #e5e7eb; display: flex; align-items: center; justify-content: center; color: var(--admin-text-muted); overflow: hidden;">
                                        <!-- Placeholder Icon Image -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                    <span style="font-weight: 600; color: var(--admin-text-main); font-size: 0.95rem;"><?= htmlspecialchars($pf['title']) ?></span>
                                </td>
                                <td style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.9rem;"><?= htmlspecialchars($authorName) ?></td>
                                <td style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.9rem;"><?= htmlspecialchars($pf['category_label'] ?? 'Umum') ?></td>
                                <td style="padding: 16px 24px;">
                                    <span class="badge <?= $statusBadge ?>"><?= $statusText ?></span>
                                </td>
                                <td style="padding: 16px 24px; color: var(--admin-text-muted); font-size: 0.9rem;">
                                    <?= $formattedDate ?>
                                </td>
                                <td style="padding: 16px 24px; color: var(--admin-text-muted);">
                                    <a href="<?= BASE_URL ?>/custom/pages/pathfinder/editpathfinder.php?id=<?= $pf['pathfinder_id'] ?>" style="background: none; border: none; color: var(--admin-text-muted); cursor: pointer; padding: 4px; border-radius: 4px; transition: 0.2s;" onmouseover="this.style.color='var(--admin-primary)'" onmouseout="this.style.color='var(--admin-text-muted)'" title="Edit Pathfinder">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div style="padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
                    <div style="color: var(--admin-text-muted); font-size: 0.85rem;">Menampilkan 1 hingga <?= $total_pathfinders ?> dari <?= $total_pathfinders ?> entri</div>
                    <div style="display: flex; gap: 4px;">
                        <button style="border: 1px solid var(--admin-border); background: white; padding: 6px 12px; border-radius: 4px; cursor: pointer; color: var(--admin-text-muted);">&lt;</button>
                        <button style="border: 1px solid var(--admin-primary); background: var(--admin-primary); color: white; padding: 6px 12px; border-radius: 4px; cursor: pointer;">1</button>
                        <button style="border: 1px solid var(--admin-border); background: white; padding: 6px 12px; border-radius: 4px; cursor: pointer; color: var(--admin-text-main);">2</button>
                        <button style="border: 1px solid var(--admin-border); background: white; padding: 6px 12px; border-radius: 4px; cursor: pointer; color: var(--admin-text-main);">3</button>
                        <button style="border: 1px solid var(--admin-border); background: white; padding: 6px 12px; border-radius: 4px; cursor: pointer; color: var(--admin-text-muted);">&gt;</button>
                    </div>
                </div>
            </div>

        </div> <!-- End Content -->
    </div> <!-- End Main Wrapper -->

</body>
</html>
