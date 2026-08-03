<?php
/**
 * Admin Topbar Component
 */
$base_url = defined('BASE_URL') ? BASE_URL : '/baca-di-teras';
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_role = $_SESSION['admin_role'] ?? 'Unknown';
$role_label = ucwords(str_replace('_', ' ', $admin_role));

// Load Services
$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../..';

$announcements = [];
if (file_exists($libPath . '/custom/services/AnnouncementService.php')) {
    require_once $libPath . '/custom/services/AnnouncementService.php';
    $announcementService = new AnnouncementService();
    $announcements = $announcementService->getActiveAnnouncements();
}

$recentUploads = [];
if (file_exists($libPath . '/custom/services/ActivityLogService.php')) {
    require_once $libPath . '/custom/services/ActivityLogService.php';
    $activityLogService = new ActivityLogService();
    $recentUploads = $activityLogService->getRecentUploads(3); // Ambil 3 terbaru
}
?>
<header class="admin-topbar">
    <form action="<?= BASE_URL ?>/portal-admin/artikel" method="GET" class="topbar-search">
        <button type="submit" style="background:transparent; border:none; padding:0; cursor:pointer; color:inherit; display:flex; align-items:center;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button>
        <input type="text" name="search" placeholder="Cari artikel/berita..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    </form>

    <div class="topbar-actions" style="position: relative;">
        <!-- Notifikasi -->
        <button class="topbar-btn" id="btnNotification" style="position: relative;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span id="notificationDot" style="display: none; position: absolute; top: 0; right: -2px; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; border: 2px solid var(--admin-card-bg);"></span>
        </button>
        
        <!-- Dropdown Notifikasi -->
        <div id="notificationDropdown" style="display: none; position: absolute; top: 100%; right: 180px; width: 340px; background: white; border: 1px solid var(--admin-border); border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); padding: 0; margin-top: 16px; z-index: 50; overflow: hidden; max-height: 480px; overflow-y: auto;">
            <div style="padding: 16px; border-bottom: 1px solid var(--admin-border); background: var(--admin-bg);">
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 600; color: var(--admin-text-main);">Pemberitahuan</h4>
            </div>
            
            <div style="padding: 12px; display: flex; flex-direction: column; gap: 12px;" id="notificationList">
                <p id="emptyNotificationMsg" style="margin: 16px 0; font-size: 0.85rem; color: var(--admin-text-muted); text-align: center; display: <?= (empty($announcements) && empty($recentUploads)) ? 'block' : 'none' ?>;">Tidak ada pemberitahuan.</p>

                <!-- Render Pengumuman Sistem -->
                <?php foreach ($announcements as $announcement): ?>
                    <div class="notification-item" data-id="ann-<?= $announcement['id'] ?>" style="padding: 12px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; position: relative; transition: opacity 0.3s ease;">
                        <button class="dismiss-btn" data-id="ann-<?= $announcement['id'] ?>" style="position: absolute; top: 8px; right: 8px; background: transparent; border: none; cursor: pointer; color: #059669; padding: 4px; border-radius: 4px;" title="Tutup Pengumuman">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div style="display: flex; gap: 8px; margin-bottom: 4px; padding-right: 20px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #059669; flex-shrink: 0; margin-top: 2px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            <span style="font-size: 0.85rem; font-weight: 600; color: #065f46;">Pengumuman dari <?= htmlspecialchars($announcement['author_name'] ?? 'Sistem') ?></span>
                        </div>
                        <p style="margin: 0; font-size: 0.85rem; color: #065f46; line-height: 1.4; padding-right: 12px;"><?= nl2br(htmlspecialchars($announcement['message'])) ?></p>
                    </div>
                <?php endforeach; ?>

                <!-- Render Konten Baru (Recent Uploads) -->
                <?php foreach ($recentUploads as $upload): ?>
                    <div class="notification-item" data-id="upl-<?= $upload['id'] ?>" style="padding: 12px; background: #f8fafc; border: 1px solid var(--admin-border); border-radius: 8px; position: relative; transition: opacity 0.3s ease;">
                        <button class="dismiss-btn" data-id="upl-<?= $upload['id'] ?>" style="position: absolute; top: 8px; right: 8px; background: transparent; border: none; cursor: pointer; color: #64748b; padding: 4px; border-radius: 4px;" title="Tutup Pemberitahuan">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div style="display: flex; gap: 8px; margin-bottom: 4px; padding-right: 20px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #3b82f6; flex-shrink: 0; margin-top: 2px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span style="font-size: 0.85rem; font-weight: 600; color: #1e293b;">Konten Baru (<?= htmlspecialchars($upload['entity']) ?>)</span>
                        </div>
                        <p style="margin: 0; font-size: 0.85rem; color: #475569; line-height: 1.4; padding-right: 12px;">
                            <?= htmlspecialchars($upload['user_name'] ?? 'Admin') ?> telah menambahkan <?= htmlspecialchars($upload['entity']) ?>: <strong><?= htmlspecialchars($upload['entity_name']) ?></strong>
                        </p>
                        <p style="margin: 4px 0 0 0; font-size: 0.75rem; color: #94a3b8;"><?= date('d M Y, H:i', strtotime($upload['created_at'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Profil -->
        <div class="topbar-profile" id="btnProfile" style="cursor: pointer; display: flex; align-items: center; gap: 8px; position: relative; margin-left: 12px; padding: 4px; border-radius: 8px; transition: background 0.2s;">
            <!-- Dummy Avatar Image -->
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($admin_name) ?>&background=059669&color=fff" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%;">
            <span style="font-size: 0.95rem; font-weight: 500;"><?= htmlspecialchars($admin_name) ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color: var(--admin-text-muted);">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
            
            <!-- Dropdown Profil -->
            <div id="profileDropdown" style="display: none; position: absolute; top: 100%; right: 0; width: 220px; background: white; border: 1px solid var(--admin-border); border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin-top: 12px; z-index: 50; overflow: hidden; cursor: default;">
                <div style="padding: 16px; border-bottom: 1px solid var(--admin-border); background: var(--admin-bg);">
                    <div style="font-weight: 600; font-size: 0.95rem; color: var(--admin-text-main); margin-bottom: 4px;"><?= htmlspecialchars($admin_name) ?></div>
                    <div style="font-size: 0.75rem; font-weight: 600; color: var(--admin-primary); background: var(--admin-primary-light); padding: 2px 10px; border-radius: 99px; display: inline-block; border: 1px solid #6ee7b7;"><?= htmlspecialchars($role_label) ?></div>
                </div>
                <a href="javascript:void(0)" id="btnDropdownLogout" style="display: flex; align-items: center; gap: 8px; padding: 12px 16px; text-decoration: none; color: #dc2626; font-size: 0.9rem; font-weight: 500; transition: background 0.2s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar dari Sistem
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Custom Logout Modal -->
<div id="logoutModal" class="logout-modal" style="display: none;">
    <div class="logout-modal-backdrop"></div>
    <div class="logout-modal-content">
        <div style="width: 48px; height: 48px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; color: #dc2626; margin: 0 auto 16px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
        </div>
        <h3 style="font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: 8px;">Konfirmasi Keluar</h3>
        <p style="font-size: 0.9rem; color: #6b7280; margin-bottom: 24px;">Apakah Anda yakin ingin keluar dari halaman admin? Sesi Anda akan diakhiri.</p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button id="btnCancelLogout" style="padding: 10px 16px; border-radius: 8px; border: 1px solid #d1d5db; background: white; color: #374151; font-weight: 600; cursor: pointer; flex: 1;">Batal</button>
            <a href="<?= $base_url ?>/portal-admin/logout" style="padding: 10px 16px; border-radius: 8px; border: none; background: #dc2626; color: white; font-weight: 600; cursor: pointer; flex: 1; text-align: center; text-decoration: none;">Ya, Keluar</a>
        </div>
    </div>
</div>

<style>
    #btnProfile:hover { background-color: #f3f4f6; }
    #btnDropdownLogout:hover { background-color: #fef2f2; }
    .dismiss-btn:hover { background-color: rgba(5, 150, 105, 0.1) !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnProfile = document.getElementById('btnProfile');
        const profileDropdown = document.getElementById('profileDropdown');
        const btnNotification = document.getElementById('btnNotification');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationDot = document.getElementById('notificationDot');
        
        // --- Notifikasi Dismissal ---
        const DISMISSED_KEY = 'bdt_dismissed_notifications';
        let dismissedIds = [];
        
        try {
            const stored = localStorage.getItem(DISMISSED_KEY);
            if (stored) dismissedIds = JSON.parse(stored);
            if (!Array.isArray(dismissedIds)) dismissedIds = [];
        } catch (e) {
            dismissedIds = [];
        }
        
        const notificationItems = document.querySelectorAll('.notification-item');
        let hasUndismissed = false;
        
        // Sembunyikan item yang sudah di-dismiss
        notificationItems.forEach(item => {
            const id = item.getAttribute('data-id');
            if (dismissedIds.includes(id)) {
                item.style.display = 'none';
            } else {
                hasUndismissed = true;
            }
        });
        
        // Jika masih ada yang belum di-dismiss, tampilkan dot merah
        if (hasUndismissed) {
            notificationDot.style.display = 'block';
        } else {
            const emptyMsg = document.getElementById('emptyNotificationMsg');
            if (emptyMsg) emptyMsg.style.display = 'block';
        }
        
        // Tombol Dismiss ditekan
        document.querySelectorAll('.dismiss-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // Mencegah dropdown tertutup
                const id = this.getAttribute('data-id');
                const item = document.querySelector(`.notification-item[data-id="${id}"]`);
                
                if (item) {
                    item.style.opacity = '0';
                    setTimeout(() => {
                        item.style.display = 'none';
                        
                        // Cek ulang apakah masih ada yang tersisa setelah dihilangkan
                        let stillHasUndismissed = false;
                        document.querySelectorAll('.notification-item').forEach(el => {
                            if (el.style.display !== 'none' && el !== item) {
                                stillHasUndismissed = true;
                            }
                        });
                        
                        if (!stillHasUndismissed) {
                            notificationDot.style.display = 'none';
                            const emptyMsg = document.getElementById('emptyNotificationMsg');
                            if (emptyMsg) emptyMsg.style.display = 'block';
                        }
                    }, 300);
                }
                
                if (!dismissedIds.includes(id)) {
                    dismissedIds.push(id);
                    localStorage.setItem(DISMISSED_KEY, JSON.stringify(dismissedIds));
                }
            });
        });

        // --- Dropdown Toggles ---
        btnProfile.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = profileDropdown.style.display === 'none';
            notificationDropdown.style.display = 'none';
            profileDropdown.style.display = isHidden ? 'block' : 'none';
        });

        btnNotification.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = notificationDropdown.style.display === 'none';
            profileDropdown.style.display = 'none';
            notificationDropdown.style.display = isHidden ? 'block' : 'none';
        });

        document.addEventListener('click', function(e) {
            if (!btnProfile.contains(e.target)) {
                profileDropdown.style.display = 'none';
            }
            if (!btnNotification.contains(e.target)) {
                notificationDropdown.style.display = 'none';
            }
        });

        profileDropdown.addEventListener('click', function(e) { e.stopPropagation(); });
        notificationDropdown.addEventListener('click', function(e) { e.stopPropagation(); });

        // --- Logout Modal ---
        document.getElementById('btnDropdownLogout').addEventListener('click', function(e) {
            e.preventDefault();
            profileDropdown.style.display = 'none';
            document.getElementById('logoutModal').style.display = 'flex';
        });
        
        document.getElementById('btnCancelLogout').addEventListener('click', function() {
            document.getElementById('logoutModal').style.display = 'none';
        });
    });
</script>

<!-- Global Loading Overlay -->
<div id="bdt-global-loader" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(255, 255, 255, 0.85); z-index: 999999; align-items: center; justify-content: center; flex-direction: column; backdrop-filter: blur(4px);">
    <div style="width: 50px; height: 50px; border: 4px solid var(--admin-border, #e5e7eb); border-top-color: var(--admin-primary, #059669); border-radius: 50%; animation: bdt-spin-loader 1s linear infinite;"></div>
    <p style="margin-top: 16px; font-weight: 600; font-size: 1rem; color: var(--admin-text-main, #111827);">Sedang memproses...</p>
    <p style="margin-top: 4px; font-size: 0.85rem; color: var(--admin-text-muted, #6b7280);">Harap tunggu sebentar.</p>
</div>
<style>
@keyframes bdt-spin-loader { 
    0% { transform: rotate(0deg); } 
    100% { transform: rotate(360deg); } 
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('bdt-global-loader');
        // Find all POST forms except the search form (just in case search becomes POST)
        const forms = document.querySelectorAll('form[method="POST"], form[method="post"]');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // If the form has built-in HTML5 validation, check it first
                if (form.checkValidity && !form.checkValidity()) {
                    return; // Let the browser show the default validation tooltip
                }
                
                // Tunda sedikit untuk mengecek apakah submit dicegat oleh skrip lain (seperti konfirmasi)
                setTimeout(() => {
                    if (!e.defaultPrevented) {
                        loader.style.display = 'flex';
                    }
                }, 20);
            });
        });
    });
</script>
