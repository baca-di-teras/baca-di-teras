<?php
/**
 * Admin – Kelola Donasi
 *
 * File    : manage_donasi.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Halaman admin untuk mengelola donatur dan partner logo.
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

// ── Handle POST Actions ────────────────────────────────────────
$successMsg = '';
$errorMsg   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Hapus donatur
    if ($action === 'delete_donatur' && isset($_POST['donatur_id'])) {
        $id = (int) $_POST['donatur_id'];
        if ($donasiService->deleteDonatur($id)) {
            $successMsg = 'Donatur berhasil dihapus.';
        } else {
            $errorMsg = 'Gagal menghapus donatur.';
        }
    }

    // Toggle visibility donatur
    if ($action === 'toggle_donatur' && isset($_POST['donatur_id'])) {
        $id      = (int) $_POST['donatur_id'];
        $current = $donasiService->getDonaturById($id);
        if ($current) {
            $newVisible = $current['is_visible'] ? 0 : 1;
            $donasiService->updateDonatur($id, array_merge($current, ['is_visible' => $newVisible]));
            $successMsg = 'Status donatur diperbarui.';
        }
    }

    // Hapus partner
    if ($action === 'delete_partner' && isset($_POST['partner_id'])) {
        $id = (int) $_POST['partner_id'];
        if ($donasiService->deletePartner($id)) {
            $successMsg = 'Partner berhasil dihapus.';
        } else {
            $errorMsg = 'Gagal menghapus partner.';
        }
    }

    // Toggle visibility partner
    if ($action === 'toggle_partner' && isset($_POST['partner_id'])) {
        $id      = (int) $_POST['partner_id'];
        $current = $donasiService->getPartnerById($id);
        if ($current) {
            $newVisible = $current['is_visible'] ? 0 : 1;
            $donasiService->updatePartner($id, array_merge($current, ['is_visible' => $newVisible]));
            $successMsg = 'Status partner diperbarui.';
        }
    }
}

// Ambil tab aktif
$tab = in_array($_GET['tab'] ?? '', ['donatur', 'partner']) ? $_GET['tab'] : 'donatur';

$donaturList = $donasiService->getAllDonatur();
$partnerList = $donasiService->getAllPartners();

$admin_active_page = 'donasi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Donasi – Admin Portal</title>
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
                    <h1>Kelola Donasi</h1>
                    <p>Manajemen data donatur dan logo partner halaman donasi.</p>
                </div>
                <?php if ($tab === 'donatur'): ?>
                <a href="<?= BASE_URL ?>/portal-admin/donasi/tambah-donatur" class="btn btn-primary" style="text-decoration:none;">
                    + Tambah Donatur
                </a>
                <?php else: ?>
                <a href="<?= BASE_URL ?>/portal-admin/donasi/tambah-partner" class="btn btn-primary" style="text-decoration:none;">
                    + Tambah Partner
                </a>
                <?php endif; ?>
            </div>

            <!-- Feedback Messages -->
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

            <!-- Tabs -->
            <div style="display:flex; gap:4px; margin-bottom:24px; background:#f3f4f6; padding:4px; border-radius:10px; width:fit-content;">
                <a href="?tab=donatur"
                   style="text-decoration:none; padding:8px 20px; border-radius:8px; font-size:0.9rem; font-weight:600; transition:all 0.15s;
                          <?= $tab === 'donatur' ? 'background:#fff; color:#202124; box-shadow:0 1px 4px rgba(0,0,0,0.1);' : 'color:#6b7280;' ?>">
                    Donatur (<?= count($donaturList) ?>)
                </a>
                <a href="?tab=partner"
                   style="text-decoration:none; padding:8px 20px; border-radius:8px; font-size:0.9rem; font-weight:600; transition:all 0.15s;
                          <?= $tab === 'partner' ? 'background:#fff; color:#202124; box-shadow:0 1px 4px rgba(0,0,0,0.1);' : 'color:#6b7280;' ?>">
                    Partner Logo (<?= count($partnerList) ?>)
                </a>
            </div>

            <!-- ── Tab: Donatur ─────────────────────────────── -->
            <?php if ($tab === 'donatur'): ?>
            <div class="card" style="padding:0; overflow:hidden;">
                <table class="data-table" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f9fafb; border-bottom:1px solid var(--admin-border); text-align:left;">
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase;">Nama</th>
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase;">Tanggal</th>
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase;">Jumlah</th>
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase;">Status</th>
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase; text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($donaturList)): ?>
                        <tr>
                            <td colspan="5" style="padding:32px; text-align:center; color:var(--admin-text-muted);">Belum ada donatur.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($donaturList as $d): ?>
                            <tr style="border-bottom:1px solid var(--admin-border);">
                                <td style="padding:14px 16px; font-weight:600; color:var(--admin-text-main);">
                                    <?= htmlspecialchars($d['name']) ?>
                                </td>
                                <td style="padding:14px 16px; color:var(--admin-text-muted); font-size:0.9rem;">
                                    <?= date('d M Y', strtotime($d['donated_at'])) ?>
                                </td>
                                <td style="padding:14px 16px; color:var(--admin-text-muted); font-size:0.9rem;">
                                    <?= $d['amount'] ? 'Rp ' . number_format($d['amount'], 0, ',', '.') : '—' ?>
                                </td>
                                <td style="padding:14px 16px;">
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="toggle_donatur">
                                        <input type="hidden" name="donatur_id" value="<?= $d['donatur_id'] ?>">
                                        <button type="submit" style="border:none; background:none; cursor:pointer; padding:0;">
                                            <?php if ($d['is_visible']): ?>
                                                <span style="display:inline-block; padding:3px 10px; border-radius:9999px; font-size:0.75rem; font-weight:600; background:#d1fae5; color:#065f46;">Tampil</span>
                                            <?php else: ?>
                                                <span style="display:inline-block; padding:3px 10px; border-radius:9999px; font-size:0.75rem; font-weight:600; background:#f3f4f6; color:#6b7280;">Tersembunyi</span>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                </td>
                                <td style="padding:14px 16px; text-align:right;">
                                    <a href="<?= BASE_URL ?>/portal-admin/donasi/edit-donatur?id=<?= $d['donatur_id'] ?>" style="color:var(--admin-primary); text-decoration:none; font-size:0.9rem; font-weight:500; margin-right:12px;">Edit</a>
                                    <form method="post" style="display:inline;" onsubmit="return confirm('Hapus donatur ini?')">
                                        <input type="hidden" name="action" value="delete_donatur">
                                        <input type="hidden" name="donatur_id" value="<?= $d['donatur_id'] ?>">
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

            <!-- ── Tab: Partner Logo ─────────────────────────── -->
            <?php else: ?>
            <div class="card" style="padding:0; overflow:hidden;">
                <table class="data-table" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f9fafb; border-bottom:1px solid var(--admin-border); text-align:left;">
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase;">Logo</th>
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase;">Nama</th>
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase;">Urutan</th>
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase;">Status</th>
                            <th style="padding:14px 16px; font-weight:600; color:var(--admin-text-muted); font-size:0.82rem; text-transform:uppercase; text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($partnerList)): ?>
                        <tr>
                            <td colspan="5" style="padding:32px; text-align:center; color:var(--admin-text-muted);">Belum ada partner logo.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($partnerList as $p): ?>
                            <tr style="border-bottom:1px solid var(--admin-border);">
                                <td style="padding:10px 16px;">
                                    <?php if (!empty($p['logo_path'])): ?>
                                        <img src="<?= BASE_URL . '/' . ltrim(htmlspecialchars($p['logo_path']), '/') ?>"
                                             alt="Logo <?= htmlspecialchars($p['name']) ?>"
                                             style="max-height:44px; max-width:80px; object-fit:contain;">
                                    <?php else: ?>
                                        <span style="color:var(--admin-text-muted); font-size:0.8rem;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:14px 16px; font-weight:600; color:var(--admin-text-main);">
                                    <?= htmlspecialchars($p['name']) ?>
                                    <?php if (!empty($p['website_url'])): ?>
                                    <br><a href="<?= htmlspecialchars($p['website_url']) ?>" target="_blank" rel="noopener"
                                           style="font-size:0.78rem; color:var(--admin-primary); font-weight:400; text-decoration:none;">
                                        <?= htmlspecialchars($p['website_url']) ?>
                                    </a>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:14px 16px; color:var(--admin-text-muted);">
                                    <?= (int)$p['sort_order'] ?>
                                </td>
                                <td style="padding:14px 16px;">
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="toggle_partner">
                                        <input type="hidden" name="partner_id" value="<?= $p['partner_id'] ?>">
                                        <button type="submit" style="border:none; background:none; cursor:pointer; padding:0;">
                                            <?php if ($p['is_visible']): ?>
                                                <span style="display:inline-block; padding:3px 10px; border-radius:9999px; font-size:0.75rem; font-weight:600; background:#d1fae5; color:#065f46;">Tampil</span>
                                            <?php else: ?>
                                                <span style="display:inline-block; padding:3px 10px; border-radius:9999px; font-size:0.75rem; font-weight:600; background:#f3f4f6; color:#6b7280;">Tersembunyi</span>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                </td>
                                <td style="padding:14px 16px; text-align:right;">
                                    <a href="<?= BASE_URL ?>/portal-admin/donasi/edit-partner?id=<?= $p['partner_id'] ?>" style="color:var(--admin-primary); text-decoration:none; font-size:0.9rem; font-weight:500; margin-right:12px;">Edit</a>
                                    <form method="post" style="display:inline;" onsubmit="return confirm('Hapus partner ini?')">
                                        <input type="hidden" name="action" value="delete_partner">
                                        <input type="hidden" name="partner_id" value="<?= $p['partner_id'] ?>">
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
            <?php endif; ?>

        </div><!-- /.admin-content -->
    </div><!-- /.admin-main -->
</body>
</html>
