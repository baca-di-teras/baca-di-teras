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
$info = $infoService->getInformationById($info_id);
if (!$info || $info['id_name'] !== 'jam_operasional') { header("Location: " . BASE_URL . "/portal-admin/informasi"); exit; }
$extra = json_decode($info['extra_data'], true) ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule = [];
    if (isset($_POST['schedule_day']) && is_array($_POST['schedule_day'])) {
        for ($i = 0; $i < count($_POST['schedule_day']); $i++) {
            $day = trim($_POST['schedule_day'][$i]);
            $time_open = $_POST['schedule_open'][$i] ?? '';
            $time_close = $_POST['schedule_close'][$i] ?? '';
            $isClosed = (isset($_POST['schedule_is_closed_val'][$i]) && $_POST['schedule_is_closed_val'][$i] == '1');
            $highlight = (isset($_POST['schedule_highlight_val'][$i]) && $_POST['schedule_highlight_val'][$i] == '1');
            
            if ($day) {
                $timeStr = $isClosed ? 'Libur (Tutup)' : ($time_open && $time_close ? "$time_open - $time_close" : '');
                if (!$timeStr) $timeStr = '00:00 - 00:00';
                $schedule[] = ['day' => $day, 'time' => $timeStr, 'highlight' => $highlight];
            }
        }
    }
    $extraData = [
        'schedule' => $schedule
    ];
    $data = [
        'id_name'    => 'jam_operasional',
        'type'       => 'schedule',
        'title'      => $_POST['title'] ?? 'Jam Operasional',
        'content'    => '',
        'extra_data' => json_encode($extraData),
        'status'     => $_POST['status'] ?? 'aktif',
        'sort_order' => 0
    ];

    if ($infoService->updateInformation($info_id, $data)) {
        header("Location: " . BASE_URL . "/portal-admin/informasi"); exit;
    } else { $errorMsg = 'Gagal memperbarui data.'; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Jam Operasional</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/custom/assets/css/admin.css">
    <style>
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 0.85rem; font-weight: 600; color: var(--admin-text-main); }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--admin-border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 0.9rem; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1); }
        textarea.form-control { min-height: 100px; resize: vertical; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; }
        .dynamic-list { margin-bottom: 16px; border: 1px solid #eee; border-radius: 8px; padding: 16px; background: #fafafa; }
        .dynamic-item { display: flex; gap: 12px; margin-bottom: 12px; align-items: flex-start; }
        .dynamic-item .form-control { flex: 1; }
        .btn-remove { background: #fee2e2; color: #b91c1c; border: none; padding: 10px 14px; border-radius: 8px; font-weight: 600; cursor: pointer; white-space: nowrap; }
        .btn-add { background: #e0f2fe; color: #0284c7; border: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
        .input-group { display: flex; align-items: center; }
        .input-group-text { padding: 10px 14px; background: #f3f4f6; border: 1px solid var(--admin-border); border-right: none; border-radius: 8px 0 0 8px; font-weight: 600; color: #555; }
        .input-group .form-control { border-radius: 0 8px 8px 0; }
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
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Edit Jam Operasional</h1>
                </div>
                <div><a href="<?= BASE_URL ?>/portal-admin/informasi" class="btn-secondary" style="background: white; border: 1px solid var(--admin-border); color: var(--admin-text-main); padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none;">Kembali</a></div>
            </div>
            <?php if ($errorMsg): ?><div class="alert-error"><?= htmlspecialchars($errorMsg) ?></div><?php endif; ?>
            <form method="POST" class="card" style="padding: 32px; background: white; border-radius: 12px; border: 1px solid var(--admin-border);">
                
                <div class="form-group" style="margin-bottom: 32px;">
                    <label class="form-label">Judul Jadwal</label>
                    <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($info['title'] ?? 'Jam Operasional') ?>">
                </div>

                <h3 class="form-section-title">Jadwal Harian</h3>
                <div class="form-group">
                    <div class="dynamic-list" id="scheduleList">
                        <?php $schedules = $extra['schedule'] ?? [['day' => '', 'time' => '', 'highlight' => false]]; ?>
                        <?php foreach ($schedules as $sch): 
                            $isClosed = strpos(strtolower($sch['time']), 'tutup') !== false || strpos(strtolower($sch['time']), 'libur') !== false;
                            $timeParts = $isClosed ? ['', ''] : explode(' - ', $sch['time']);
                            $tOpen = trim($timeParts[0] ?? '');
                            $tClose = trim($timeParts[1] ?? '');
                        ?>
                        <div class="dynamic-item" style="flex-wrap: wrap; background: white; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div style="flex: 1; min-width: 150px;">
                                <label class="form-label" style="font-size: 0.75rem;">Hari</label>
                                <input type="text" name="schedule_day[]" class="form-control" value="<?= htmlspecialchars($sch['day']) ?>" placeholder="Misal: Senin - Kamis">
                            </div>
                            <div style="flex: 1; min-width: 120px;">
                                <label class="form-label" style="font-size: 0.75rem;">Jam Buka</label>
                                <input type="time" name="schedule_open[]" class="form-control" value="<?= htmlspecialchars($tOpen) ?>">
                            </div>
                            <div style="flex: 1; min-width: 120px;">
                                <label class="form-label" style="font-size: 0.75rem;">Jam Tutup</label>
                                <input type="time" name="schedule_close[]" class="form-control" value="<?= htmlspecialchars($tClose) ?>">
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-top: 24px;">
                                <label style="font-size: 0.85rem; display: flex; align-items: center; gap: 4px;">
                                    <input type="hidden" name="schedule_is_closed_val[]" value="<?= $isClosed ? '1' : '0' ?>">
                                    <input type="checkbox" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'" <?= $isClosed ? 'checked' : '' ?>> Libur
                                </label>
                                <label style="font-size: 0.85rem; display: flex; align-items: center; gap: 4px; margin-left: 12px;">
                                    <input type="hidden" name="schedule_highlight_val[]" value="<?= $sch['highlight'] ? '1' : '0' ?>">
                                    <input type="checkbox" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'" <?= $sch['highlight'] ? 'checked' : '' ?>> Highlight
                                </label>
                            </div>
                            <div style="margin-top: 24px; padding-left: 16px;">
                                <button type="button" class="btn-remove" onclick="this.parentElement.parentElement.remove()">Hapus</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn-add" onclick="addSchedule()">+ Tambah Jadwal</button>
                </div>

                <div class="form-group" style="margin-top: 24px; max-width: 200px;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="aktif" <?= ($info['status']??'') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= ($info['status']??'') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--admin-border);">
                    <button type="submit" class="btn-primary" style="background-color: var(--admin-primary); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function addSchedule() {
            const list = document.getElementById('scheduleList');
            const div = document.createElement('div');
            div.className = 'dynamic-item';
            div.style = "flex-wrap: wrap; background: white; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb;";
            div.innerHTML = `
                <div style="flex: 1; min-width: 150px;"><label class="form-label" style="font-size: 0.75rem;">Hari</label><input type="text" name="schedule_day[]" class="form-control"></div>
                <div style="flex: 1; min-width: 120px;"><label class="form-label" style="font-size: 0.75rem;">Jam Buka</label><input type="time" name="schedule_open[]" class="form-control"></div>
                <div style="flex: 1; min-width: 120px;"><label class="form-label" style="font-size: 0.75rem;">Jam Tutup</label><input type="time" name="schedule_close[]" class="form-control"></div>
                <div style="display: flex; align-items: center; gap: 8px; margin-top: 24px;">
                    <label style="font-size: 0.85rem;"><input type="hidden" name="schedule_is_closed_val[]" value="0"><input type="checkbox" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'"> Libur</label>
                    <label style="font-size: 0.85rem; margin-left: 12px;"><input type="hidden" name="schedule_highlight_val[]" value="0"><input type="checkbox" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'"> Highlight</label>
                </div>
                <div style="margin-top: 24px; padding-left: 16px;"><button type="button" class="btn-remove" onclick="this.parentElement.parentElement.remove()">Hapus</button></div>
            `;
            list.appendChild(div);
        }
    </script>
</body>
</html>