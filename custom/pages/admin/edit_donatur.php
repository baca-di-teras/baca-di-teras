<?php
/**
 * Admin – Edit Donatur
 *
 * File    : edit_donatur.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/baca-di-teras');
}

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';
require_once $libPath . '/custom/services/DonasiService.php';

$auth = new AuthService();
$auth->requireRole(['super_admin', 'admin']);

$donasiService = new DonasiService();

$id = (int)($_GET['id'] ?? 0);
$current = $donasiService->getDonaturById($id);

if (!$current) {
    header('Location: ' . BASE_URL . '/portal-admin/donasi?tab=donatur');
    exit;
}

$successMsg = '';
$errorMsg   = '';
$errors     = [];

// ── Handle POST ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name'] ?? '');
    $amount     = trim($_POST['amount'] ?? '');
    $donated_at = trim($_POST['donated_at'] ?? '');
    $note       = trim($_POST['note'] ?? '');
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;

    // Validasi
    if (empty($name)) {
        $errors[] = 'Nama donatur wajib diisi.';
    }
    if (empty($donated_at)) {
        $errors[] = 'Tanggal donasi wajib diisi.';
    }
    if (!empty($amount) && (!is_numeric($amount) || $amount < 0)) {
        $errors[] = 'Jumlah donasi harus berupa angka positif.';
    }

    if (empty($errors)) {
        $result = $donasiService->updateDonatur($id, [
            'name'       => $name,
            'amount'     => $amount !== '' ? $amount : null,
            'donated_at' => $donated_at,
            'note'       => $note !== '' ? $note : null,
            'is_visible' => $is_visible,
        ]);

        if ($result) {
            header('Location: ' . BASE_URL . '/portal-admin/donasi?tab=donatur&success=updated');
            exit;
        } else {
            $errorMsg = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
        }
    }
}

$admin_active_page = 'donasi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Donatur – Admin Portal</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
</head>
<body>
    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <div class="admin-main">
        <?php include __DIR__ . '/../../components/admin-topbar.php'; ?>

        <div class="admin-content">
            <div class="page-header">
                <div class="page-title">
                    <h1>Edit Donatur</h1>
                    <p>
                        <a href="<?= BASE_URL ?>/portal-admin/donasi?tab=donatur"
                           style="color:var(--admin-primary); text-decoration:none;">← Kembali ke Kelola Donasi</a>
                    </p>
                </div>
            </div>

            <!-- Feedback Messages -->
            <?php if (!empty($errors)): ?>
            <div style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; border-radius:8px; padding:14px 16px; margin-bottom:20px;">
                <strong>Terdapat kesalahan:</strong>
                <ul style="margin:8px 0 0; padding-left:18px;">
                    <?php foreach ($errors as $e): ?>
                        <li style="font-size:0.9rem;"><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php if ($errorMsg): ?>
            <div style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:0.9rem;">
                <?= htmlspecialchars($errorMsg) ?>
            </div>
            <?php endif; ?>

            <div class="card" style="max-width:580px;">
                <form method="post" novalidate>
                    <!-- Nama -->
                    <div style="margin-bottom:20px;">
                        <label for="name" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            Nama Donatur <span style="color:#dc2626;">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?= htmlspecialchars($_POST['name'] ?? $current['name']) ?>"
                            placeholder="Contoh: Budi Santoso"
                            style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; transition:border-color 0.2s; box-sizing:border-box;"
                            required
                        >
                    </div>

                    <!-- Tanggal -->
                    <div style="margin-bottom:20px;">
                        <label for="donated_at" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            Tanggal Donasi <span style="color:#dc2626;">*</span>
                        </label>
                        <input
                            type="date"
                            id="donated_at"
                            name="donated_at"
                            value="<?= htmlspecialchars($_POST['donated_at'] ?? date('Y-m-d', strtotime($current['donated_at']))) ?>"
                            style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; transition:border-color 0.2s; box-sizing:border-box;"
                            required
                        >
                    </div>

                    <!-- Jumlah (opsional) -->
                    <div style="margin-bottom:20px;">
                        <label for="amount" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            Jumlah Donasi (Rp) <span style="color:#6b7280; font-weight:400;">— opsional</span>
                        </label>
                        <input
                            type="number"
                            id="amount"
                            name="amount"
                            value="<?= htmlspecialchars($_POST['amount'] ?? $current['amount'] ?? '') ?>"
                            placeholder="Contoh: 100000"
                            min="0"
                            step="any"
                            style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; transition:border-color 0.2s; box-sizing:border-box;"
                        >
                        <p style="font-size:0.8rem; color:var(--admin-text-muted); margin:4px 0 0;">Kosongkan jika tidak ingin menampilkan jumlah.</p>
                    </div>

                    <!-- Catatan (opsional) -->
                    <div style="margin-bottom:20px;">
                        <label for="note" style="display:block; font-weight:600; font-size:0.9rem; color:var(--admin-text-main); margin-bottom:6px;">
                            Catatan <span style="color:#6b7280; font-weight:400;">— opsional</span>
                        </label>
                        <textarea
                            id="note"
                            name="note"
                            rows="3"
                            placeholder="Catatan tambahan..."
                            style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:8px; font-size:0.95rem; font-family:inherit; outline:none; transition:border-color 0.2s; box-sizing:border-box; resize:vertical;"
                        ><?= htmlspecialchars($_POST['note'] ?? $current['note'] ?? '') ?></textarea>
                    </div>

                    <!-- Visibility -->
                    <div style="margin-bottom:28px;">
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:0.95rem; font-weight:500; color:var(--admin-text-main);">
                            <input
                                type="checkbox"
                                id="is_visible"
                                name="is_visible"
                                <?= (isset($_POST['is_visible']) || (!isset($_POST['name']) && $current['is_visible'])) ? 'checked' : '' ?>
                                style="width:16px; height:16px; accent-color:var(--admin-primary);"
                            >
                            Tampilkan di halaman donasi publik
                        </label>
                    </div>

                    <div style="display:flex; gap:12px;">
                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                        <a href="<?= BASE_URL ?>/portal-admin/donasi?tab=donatur"
                           style="padding:10px 20px; border-radius:8px; border:1px solid var(--admin-border); background:#fff; color:var(--admin-text-main); font-size:0.9rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center;">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div><!-- /.admin-content -->
    </div><!-- /.admin-main -->
</body>
</html>
