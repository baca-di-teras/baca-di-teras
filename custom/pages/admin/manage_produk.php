<?php
/**
 * Admin – Kelola Produk (Galeri)
 *
 * File    : manage_produk.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/ProdukService.php';
require_once $libPath . '/custom/services/ActivityLogService.php';

$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin', 'kontributor']);

$produkService = new ProdukService();
$activityService = new ActivityLogService();

// ── Handle POST Actions ────────────────────────────────────────
$successMsg = '';
$errorMsg   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Hapus Produk
    if ($action === 'delete_produk' && isset($_POST['produk_id'])) {
        $id = (int) $_POST['produk_id'];
        $produkToDelete = $produkService->getProdukById($id);
        if ($produkService->deleteProduk($id)) {
            if ($produkToDelete) {
                $activityService->log('menghapus', 'Produk', $produkToDelete['title']);
            }
            $successMsg = 'Produk berhasil dihapus.';
        } else {
            $errorMsg = 'Gagal menghapus produk.';
        }
    }

    // Toggle visibility Produk
    if ($action === 'toggle_produk' && isset($_POST['produk_id'])) {
        $id      = (int) $_POST['produk_id'];
        $current = $produkService->getProdukById($id);
        if ($current) {
            $newVisible = $current['is_visible'] ? 0 : 1;
            $produkService->updateProduk($id, ['is_visible' => $newVisible]);
            $successMsg = 'Status produk diperbarui.';
        }
    }
}

$produkList = $produkService->getAllProduk();
$admin_active_page = 'produk';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk – Admin Portal</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/modal.css">
</head>
<body>
    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content">
            <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="page-title">
                    <h1>Kelola Produk (Galeri)</h1>
                    <p>Manajemen data produk yang tampil di halaman galeri produk.</p>
                </div>
                <a href="<?= BASE_URL ?>/portal-admin/produk/tambah" class="btn btn-primary" style="text-decoration:none;">
                    + Tambah Produk
                </a>
            </div>

            <!-- Feedback Messages -->
            <?php if (isset($_GET['success']) && $_GET['success'] === 'created'): ?>
            <div style="background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:0.9rem;">
                Produk berhasil ditambahkan.
            </div>
            <?php endif; ?>
            
            <?php if ($successMsg): ?>
            <div style="background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:0.9rem;">
                <?= htmlspecialchars($successMsg) ?>
            </div>
            <?php endif; ?>
            
            <?php if ($errorMsg): ?>
            <div style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:0.9rem;">
                <?= htmlspecialchars($errorMsg) ?>
            </div>
            <?php endif; ?>

            <div class="card" style="padding:0; overflow:hidden;">
                <table class="data-table" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f9fafb; border-bottom:1px solid var(--admin-border); text-align:left;">
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase;">Gambar Utama</th>
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase;">Judul Produk</th>
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase;">Dibuat Oleh</th>
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase;">Status</th>
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase; text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($produkList)): ?>
                        <tr>
                            <td colspan="5" style="padding:32px; text-align:center; color:var(--admin-text-muted);">Belum ada produk.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($produkList as $p): ?>
                            <tr style="border-bottom:1px solid var(--admin-border);">
                                <td style="padding:10px 16px;">
                                    <?php if (!empty($p['image_1'])): ?>
                                        <img src="<?= BASE_URL . '/' . htmlspecialchars($p['image_1']) ?>"
                                             alt="Thumbnail"
                                             style="max-height:44px; max-width:80px; object-fit:cover; border-radius:4px;">
                                    <?php else: ?>
                                        <span style="color:var(--admin-text-muted); font-size:0.8rem;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:14px 16px; font-weight:600; color:var(--admin-text-main);">
                                    <?= htmlspecialchars($p['title']) ?>
                                </td>
                                <td style="padding:14px 16px; color:var(--admin-text-muted); font-size:0.9rem;">
                                    <span style="background:#e5e7eb; padding:2px 8px; border-radius:4px; font-size:0.8rem;"><?= htmlspecialchars($p['group_name']) ?></span>
                                </td>
                                <td style="padding:14px 16px;">
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="toggle_produk">
                                        <input type="hidden" name="produk_id" value="<?= $p['produk_id'] ?>">
                                        <button type="submit" style="border:none; background:none; cursor:pointer; padding:0;">
                                            <?php if ($p['status'] === 'publish'): ?>
                                                <span style="display:inline-block; padding:3px 10px; border-radius:9999px; font-size:0.75rem; font-weight:600; background:#d1fae5; color:#065f46;">Publish</span>
                                            <?php elseif ($p['status'] === 'archive'): ?>
                                                <span style="display:inline-block; padding:3px 10px; border-radius:9999px; font-size:0.75rem; font-weight:600; background:#fef3c7; color:#92400e;">Archive</span>
                                            <?php else: ?>
                                                <span style="display:inline-block; padding:3px 10px; border-radius:9999px; font-size:0.75rem; font-weight:600; background:#f3f4f6; color:#6b7280;">Draft</span>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                </td>
                                <td style="padding:14px 16px; text-align:right;">
                                    <a href="<?= BASE_URL ?>/portal-admin/produk/edit?id=<?= $p['produk_id'] ?>" style="color:var(--admin-primary); text-decoration:none; font-size:0.9rem; font-weight:600; margin-right:12px;">Edit</a>
                                    <form method="post" style="display:inline;" onsubmit="return confirm('Hapus produk ini beserta seluruh gambarnya?')">
                                        <input type="hidden" name="action" value="delete_produk">
                                        <input type="hidden" name="produk_id" value="<?= $p['produk_id'] ?>">
                                        <button type="submit" style="border:none; background:none; cursor:pointer; color:#dc2626; font-size:0.9rem; font-weight:500; font-family:inherit;">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div><!-- /.admin-content -->
    </div><!-- /.admin-main -->
</body>
</html>
