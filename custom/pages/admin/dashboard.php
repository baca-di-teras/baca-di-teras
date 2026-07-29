<?php
/**
 * Pathfinder – Halaman Admin Dashboard
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/PathfinderService.php';
require_once $libPath . '/custom/services/ArticleService.php';

// Auth Check
$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin', 'kontributor']);

$pfService = new PathfinderService();
$articleService = new ArticleService();

$pathfinders = $pfService->getAllPathfinders(); 
$categories = $pfService->getCategories();

$total_categories = count($categories);
$total_pathfinders = count($pathfinders);

$role = $_SESSION['admin_role'] ?? '';
$mixed_updates = [];

// Fetch articles for everyone
$recent_articles = $articleService->getAdminArticles([], 5, 0);
foreach ($recent_articles as $art) {
    $mixed_updates[] = [
        'type'  => 'article',
        'title' => $art['title'],
        'date'  => strtotime($art['created_at']),
        'label' => ucfirst($art['category'] ?? 'Berita'),
        'desc'  => strip_tags($art['excerpt'] ?: $art['body']),
        'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>'
    ];
}

// Fetch pathfinders for admin/super_admin
if (in_array($role, ['super_admin', 'admin'])) {
    $recent_pathfinders = array_slice($pathfinders, 0, 5);
    foreach ($recent_pathfinders as $pf) {
        $mixed_updates[] = [
            'type'  => 'pathfinder',
            'title' => $pf['title'],
            'date'  => strtotime($pf['updated_at']),
            'label' => $pf['category_label'] ?? 'Umum',
            'desc'  => $pf['description'] ?? '',
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>'
        ];
    }
}

// Sort by date descending
usort($mixed_updates, function($a, $b) {
    return $b['date'] <=> $a['date'];
});

// Take top 4
$mixed_updates = array_slice($mixed_updates, 0, 4);

$admin_active_page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Admin - Baca di Teras</title>
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
        <div class="admin-content">
            <div class="page-header">
                <div class="page-title">
                    <h1>Selamat datang, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></h1>
                    <p>Berikut ringkasan aktivitas hari ini.</p>
                </div>
                <div style="display: flex; gap: 12px;">
                    <?php if (in_array($_SESSION['admin_role'] ?? '', ['super_admin', 'admin'])): ?>
                    <a href="<?= BASE_URL ?>/portal-admin/pathfinder" class="btn-secondary" style="background: white; border: 1px solid var(--admin-border); color: var(--admin-text-main); padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Kelola Pathfinder
                    </a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/portal-admin/artikel" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Tulis Artikel Baru
                    </a>
                </div>
            </div>

            <!-- Stats Overview -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <div class="card" style="position: relative;">
                    <div style="font-size: 0.75rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Total Artikel & Berita</div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--admin-text-main); line-height: 1;">42</div>
                </div>

                <?php if (in_array($_SESSION['admin_role'] ?? '', ['super_admin', 'admin'])): ?>
                <div class="card" style="position: relative;">
                    <div style="font-size: 0.75rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Total Pathfinder</div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--admin-text-main); line-height: 1;"><?= $total_pathfinders ?></div>
                </div>
                <?php endif; ?>

                <div class="card" style="position: relative;">
                    <div style="font-size: 0.75rem; color: var(--admin-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Perpustakaan Desa</div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--admin-text-main); line-height: 1;">6</div>
                </div>

                <div class="card" style="background: var(--admin-primary); color: white; border: none; position: relative;">
                    <div style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; opacity: 0.9;">Pengunjung Hari Ini</div>
                    <div style="font-size: 2rem; font-weight: 700; line-height: 1;">1,492</div>
                </div>
            </div>

            <!-- Grid Layout -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                
                <!-- Recent Updates -->
                <div class="card" style="padding: 0; display: flex; flex-direction: column;">
                    <div style="padding: 24px; border-bottom: 1px solid var(--admin-border); display: flex; justify-content: space-between; align-items: center;">
                        <h2 style="font-size: 1.1rem; font-weight: 600; margin: 0;">Pembaruan Terakhir</h2>
                        <a href="#" style="color: var(--admin-primary); font-size: 0.85rem; font-weight: 600; text-decoration: none;">Lihat Semua</a>
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        <?php if (empty($mixed_updates)): ?>
                            <div style="padding: 24px; color: var(--admin-text-muted); text-align: center;">Belum ada pembaruan aktivitas.</div>
                        <?php else: ?>
                            <?php foreach($mixed_updates as $index => $update): ?>
                                <div style="padding: 24px; <?= $index < count($mixed_updates) - 1 ? 'border-bottom: 1px solid var(--admin-border);' : '' ?> display: flex; gap: 16px;">
                                    <div style="width: 80px; height: 60px; border-radius: 8px; background: #f3f4f6; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: var(--admin-text-muted);">
                                        <?= $update['icon'] ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                            <span style="background: <?= $update['type'] === 'pathfinder' ? '#d1fae5; color: #065f46;' : '#e0e7ff; color: #3730a3;' ?> font-size: 0.7rem; font-weight: 600; padding: 2px 8px; border-radius: 999px;"><?= htmlspecialchars($update['label']) ?></span>
                                            <span style="color: var(--admin-text-muted); font-size: 0.75rem; font-weight: 500;"><?= date('d M Y', $update['date']) ?></span>
                                        </div>
                                        <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 4px;"><?= htmlspecialchars($update['title']) ?></h3>
                                        <p style="font-size: 0.85rem; color: var(--admin-text-muted); line-height: 1.4;"><?= htmlspecialchars(mb_substr($update['desc'], 0, 80)) ?>...</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column Stack -->
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    
                    <!-- Content Volume -->
                    <div class="card" style="height: 300px; display: flex; flex-direction: column;">
                        <h2 style="font-size: 1.1rem; font-weight: 600; margin-top: 0; margin-bottom: 24px;">Volume Konten</h2>
                        <!-- Mock Chart Area -->
                        <div style="flex: 1; display: flex; align-items: flex-end; justify-content: space-around; padding-bottom: 16px; position: relative;">
                            <!-- Grid lines -->
                            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 40px; border-bottom: 1px dashed var(--admin-border); pointer-events: none;"></div>
                            
                            <!-- Bars -->
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <div style="width: 24px; height: 40px; background: #e5e7eb; border-radius: 4px 4px 0 0;"></div>
                                <span style="font-size: 0.7rem; color: var(--admin-text-muted);">Jul</span>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <div style="width: 24px; height: 60px; background: #e5e7eb; border-radius: 4px 4px 0 0;"></div>
                                <span style="font-size: 0.7rem; color: var(--admin-text-muted);">Agt</span>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <div style="width: 24px; height: 50px; background: #e5e7eb; border-radius: 4px 4px 0 0;"></div>
                                <span style="font-size: 0.7rem; color: var(--admin-text-muted);">Sep</span>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <span style="font-size: 0.75rem; font-weight: 700; color: var(--admin-primary); margin-bottom: -4px;">32</span>
                                <div style="width: 24px; height: 100px; background: var(--admin-primary); border-radius: 4px 4px 0 0;"></div>
                                <span style="font-size: 0.7rem; font-weight: 600; color: var(--admin-text-main);">Okt</span>
                            </div>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="card">
                        <h2 style="font-size: 1.1rem; font-weight: 600; margin-top: 0; margin-bottom: 20px;">Status Sistem</h2>
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background: #059669;"></div>
                                    <span style="font-size: 0.9rem; color: var(--admin-text-muted);">Portal Publik</span>
                                </div>
                                <span style="font-size: 0.85rem; font-weight: 600; color: #059669;">Online</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background: #059669;"></div>
                                    <span style="font-size: 0.9rem; color: var(--admin-text-muted);">Sinkronisasi Database</span>
                                </div>
                                <span style="font-size: 0.85rem; font-weight: 600; color: #059669;">Aktif</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background: #d97706;"></div>
                                    <span style="font-size: 0.9rem; color: var(--admin-text-muted);">Cadangan Mingguan</span>
                                </div>
                                <span style="font-size: 0.85rem; font-weight: 600; color: #d97706;">Tertunda</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</body>
</html>
