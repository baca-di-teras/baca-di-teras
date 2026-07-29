<?php
/**
 * CMS – Edit Perpustakaan
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/LibraryService.php';

// Auth Check
$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin']);

$libraryService = new LibraryService();
$admin_active_page = 'library';
$errorMsg = '';
$successMsg = '';

$library_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$lib = $libraryService->getLibraryById($library_id);

if (!$lib) {
    header("Location: " . BASE_URL . "/portal-admin/perpustakaan");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'slims_location_id' => $_POST['slims_location_id'] ?? '',
        'name'              => $_POST['name'] ?? '',
        'slug'              => $_POST['slug'] ?? '',
        'tagline'           => $_POST['tagline'] ?? '',
        'badge'             => $_POST['badge'] ?? '',
        'status'            => $_POST['status'] ?? 'aktif',
        'address'           => $_POST['address'] ?? '',
        'village'           => $_POST['village'] ?? '',
        'phone'             => $_POST['phone'] ?? '',
        'email'             => $_POST['email'] ?? '',
        'whatsapp'          => $_POST['whatsapp'] ?? '',
        'google_maps_url'   => $_POST['google_maps_url'] ?? '',
        'latitude'          => $_POST['latitude'] ?? '',
        'longitude'         => $_POST['longitude'] ?? '',
        'cover_image'       => $_POST['cover_image'] ?? '',
        'description'       => $_POST['description'] ?? '',
        'sort_order'        => (int)($_POST['sort_order'] ?? 0)
    ];

    if ($libraryService->updateLibrary($library_id, $data)) {
        $successMsg = 'Perpustakaan berhasil diperbarui.';
        $lib = $libraryService->getLibraryById($library_id);
    } else {
        $errorMsg = 'Gagal menyimpan data perpustakaan. Pastikan isian sudah benar.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Perpustakaan - Baca di Teras</title>
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
        .alert-success { background: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; }
        
        .section-title { font-size: 1.1rem; margin-top: 32px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--admin-border); }
        .section-title:first-child { margin-top: 0; }
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
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Edit Perpustakaan</h1>
                    <p style="color: var(--admin-text-muted); margin: 0; font-size: 0.95rem;">Perbarui data perpustakaan jejaring yang ada.</p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/portal-admin/perpustakaan" class="btn-secondary" style="background: white; border: 1px solid var(--admin-border); color: var(--admin-text-main); padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        Kembali
                    </a>
                </div>
            </div>
            
            <?php if ($errorMsg): ?>
                <div class="alert-error"><?= htmlspecialchars($errorMsg) ?></div>
            <?php endif; ?>
            <?php if ($successMsg): ?>
                <div class="alert-success"><?= htmlspecialchars($successMsg) ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/portal-admin/perpustakaan/edit?id=<?= $lib['library_id'] ?>" method="POST" class="card" style="padding: 32px; background: white; border-radius: 12px; border: 1px solid var(--admin-border);">
                
                <h2 class="section-title">Informasi Dasar</h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nama Perpustakaan</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($lib['name']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kode Lokasi SLiMS (Opsional)</label>
                        <input type="text" name="slims_location_id" class="form-control" value="<?= htmlspecialchars($lib['slims_location_id'] ?? '') ?>">
                        <small style="color: var(--admin-text-muted);">Sesuai dengan ID Lokasi di sistem SLiMS (mst_location).</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($lib['slug']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slogan / Tagline</label>
                        <input type="text" name="tagline" class="form-control" value="<?= htmlspecialchars($lib['tagline'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control"><?= htmlspecialchars($lib['description'] ?? '') ?></textarea>
                </div>

                <h2 class="section-title">Kontak & Lokasi</h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($lib['address'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Dusun/RT (Village)</label>
                        <input type="text" name="village" class="form-control" value="<?= htmlspecialchars($lib['village'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($lib['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($lib['whatsapp'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($lib['email'] ?? '') ?>">
                    </div>
                </div>

                <h2 class="section-title">Pengaturan Tambahan</h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="aktif" <?= $lib['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= $lib['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            <option value="sementara-tutup" <?= $lib['status'] === 'sementara-tutup' ? 'selected' : '' ?>>Sementara Tutup</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Label (Badge)</label>
                        <input type="text" name="badge" class="form-control" value="<?= htmlspecialchars($lib['badge'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cover Image URL</label>
                        <input type="text" name="cover_image" class="form-control" value="<?= htmlspecialchars($lib['cover_image'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Urutan Tampil (Sort Order)</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= (int)$lib['sort_order'] ?>">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--admin-border);">
                    <button type="submit" class="btn-primary" style="background-color: var(--admin-primary); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>
