<?php
/**
 * CMS – Tambah Informasi
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/InformationService.php';

// Auth Check
$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin']);

$infoService = new InformationService();
$admin_active_page = 'info';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id_name'    => $_POST['id_name'] ?? null,
        'type'       => $_POST['type'] ?? 'lainnya',
        'title'      => $_POST['title'] ?? '',
        'content'    => $_POST['content'] ?? '',
        'extra_data' => $_POST['extra_data'] ?? null,
        'status'     => $_POST['status'] ?? 'aktif',
        'sort_order' => (int)($_POST['sort_order'] ?? 0)
    ];

    if ($infoService->createInformation($data)) {
        header("Location: " . BASE_URL . "/portal-admin/informasi");
        exit;
    } else {
        $errorMsg = 'Gagal menyimpan informasi. Pastikan isian sudah benar.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Informasi - Baca di Teras</title>
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
    </style>
    <link rel="icon" type="image/png" href="<?= defined('BASE_URL') ? BASE_URL : '/baca-di-teras' ?>/custom/assets/images/logo_header.png">
</head>
<body>

    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content" style="padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box;">
            
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <div class="page-title">
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Tambah Informasi Baru</h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;">Tambahkan FAQ, panduan layanan, atau tautan unduhan.</p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/portal-admin/informasi" class="btn-secondary" style="background: white; border: 1px solid var(--admin-border); color: var(--admin-text-main); padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        Kembali
                    </a>
                </div>
            </div>
            
            <?php if ($errorMsg): ?>
                <div class="alert-error"><?= htmlspecialchars($errorMsg) ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/portal-admin/informasi/create" method="POST" class="card" style="padding: 32px; background: white; border-radius: 12px; border: 1px solid var(--admin-border);">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Tipe Informasi</label>
                        <select name="type" class="form-control" required>
                            <option value="faq">FAQ (Tanya Jawab)</option>
                            <option value="service">Layanan</option>
                            <option value="rule">Peraturan</option>
                            <option value="schedule">Jadwal</option>
                            <option value="download">Unduhan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">ID Unik (Opsional)</label>
                        <input type="text" name="id_name" class="form-control" placeholder="Contoh: info-peminjaman">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Judul</label>
                    <input type="text" name="title" class="form-control" required placeholder="Masukkan judul informasi">
                </div>

                <div class="form-group">
                    <label class="form-label">Konten Utama</label>
                    <textarea name="content" class="form-control" style="min-height: 150px;" placeholder="Isi informasi, bisa menggunakan teks biasa atau HTML"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Extra Data (JSON Opsional)</label>
                    <textarea name="extra_data" class="form-control" style="min-height: 100px; font-family: monospace;" placeholder='{"action_label":"Unduh","action_href":"#"}'></textarea>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Urutan (Sort Order)</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--admin-border);">
                    <button type="submit" class="btn-primary" style="background-color: var(--admin-primary); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                        Simpan Informasi
                    </button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>
