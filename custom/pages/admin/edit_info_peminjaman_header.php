<?php
if (!defined('BASE_URL')) define('BASE_URL', '/baca-di-teras');
$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/InformationService.php';

$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin']);

$infoService = new InformationService();
$admin_active_page = 'info';
$errorMsg = '';

$info_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$info = null;

if ($info_id > 0) {
    $info = $infoService->getInformationById($info_id);
    if (!$info || $info['id_name'] !== 'peminjaman_header') {
        header("Location: " . BASE_URL . "/portal-admin/informasi");
        exit;
    }
}

$extra = $info ? (json_decode($info['extra_data'], true) ?: []) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $extraData = [
        'action_label' => $_POST['action_label'] ?? '',
        'action_href' => $_POST['action_href'] ?? ''
    ];
    $data = [
        'id_name'    => 'peminjaman_header',
        'type'       => 'service',
        'title'      => $_POST['title'] ?? 'Panduan Peminjaman',
        'content'    => $_POST['content'] ?? '',
        'extra_data' => json_encode($extraData),
        'status'     => $_POST['status'] ?? 'aktif',
        'sort_order' => 0
    ];

    $success = false;
    if ($info_id > 0) {
        $success = $infoService->updateInformation($info_id, $data);
    } else {
        $success = $infoService->createInformation($data);
    }

    if ($success) {
        header("Location: " . BASE_URL . "/portal-admin/informasi"); exit;
    } else { 
        $errorMsg = 'Gagal menyimpan data.'; 
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Judul & Narasi Peminjaman</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-main); }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--admin-border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 0.9rem; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1); }
        textarea.form-control { min-height: 100px; resize: vertical; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; }
        .form-section-title { font-size: 1.1rem; font-weight: 700; color: #1a1a2e; margin: 32px 0 16px; padding-bottom: 8px; border-bottom: 2px solid #eee; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>
    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>
        <div class="admin-content" style="padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <div class="page-title">
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Edit Judul & Narasi Peminjaman</h1>
                </div>
                <div><a href="<?= BASE_URL ?>/portal-admin/informasi" class="btn-secondary" style="background: white; border: 1px solid var(--admin-border); color: var(--admin-text-main); padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none;">Kembali</a></div>
            </div>
            <?php if ($errorMsg): ?><div class="alert-error"><?= htmlspecialchars($errorMsg) ?></div><?php endif; ?>
            <form method="POST" class="card" style="padding: 32px; background: white; border-radius: 12px; border: 1px solid var(--admin-border);">
                
                <div class="form-group">
                    <label class="form-label">Judul Layanan</label>
                    <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($info['title'] ?? 'Panduan Peminjaman') ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Deskripsi Singkat (Narasi)</label>
                    <textarea name="content" class="form-control" style="min-height: 80px;" required><?= htmlspecialchars($info['content'] ?? '') ?></textarea>
                </div>

                <h3 class="form-section-title">Aksi (Tombol)</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Teks Tombol Aksi</label>
                        <input type="text" name="action_label" class="form-control" value="<?= htmlspecialchars($extra['action_label'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tautan (URL) Tombol</label>
                        <input type="text" name="action_href" class="form-control" value="<?= htmlspecialchars($extra['action_href'] ?? '') ?>">
                    </div>
                </div>
                
                <div style="display: flex; justify-content: flex-end; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--admin-border);">
                    <button type="submit" class="btn-primary" style="background-color: var(--admin-primary); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
