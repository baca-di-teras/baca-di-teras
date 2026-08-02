<?php
/**
 * Admin Sidebar Component
 */
$admin_active_page = $admin_active_page ?? 'dashboard';
$base_url = defined('BASE_URL') ? BASE_URL : '/baca-di-teras';
?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-logo">
        <div class="admin-logo-icon" style="border-radius: 6px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
        </div>
        <div class="admin-logo-text">
            <span class="admin-logo-title" style="color: var(--admin-text-main);">Teras</span>
            <span class="admin-logo-subtitle" style="text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Portal Admin</span>
        </div>
    </div>

    <nav class="admin-nav">
        <a href="<?= $base_url ?>/portal-admin" class="admin-nav-item <?= $admin_active_page === 'dashboard' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="nav-text">Dasbor</span>
        </a>

        <?php if (in_array($_SESSION['admin_role'] ?? '', ['super_admin', 'admin'])): ?>
        <a href="<?= $base_url ?>/portal-admin/pengumuman" class="admin-nav-item <?= $admin_active_page === 'announcement' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="nav-text">Pengumuman</span>
        </a>
        <?php endif; ?>
        
        <?php if (in_array($_SESSION['admin_role'] ?? '', ['super_admin', 'admin'])): ?>
        <a href="<?= $base_url ?>/portal-admin/profil-desa" class="admin-nav-item <?= $admin_active_page === 'profile' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
            </svg>
            <span class="nav-text">Profil Desa</span>
        </a>
        <a href="<?= $base_url ?>/portal-admin/perpustakaan" class="admin-nav-item <?= $admin_active_page === 'library' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span class="nav-text">Perpustakaan</span>
        </a>
        <?php endif; ?>

        <a href="<?= $base_url ?>/portal-admin/artikel" class="admin-nav-item <?= $admin_active_page === 'article' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <span class="nav-text">Berita & Artikel</span>
        </a>

        <?php if (in_array($_SESSION['admin_role'] ?? '', ['super_admin', 'admin'])): ?>
        <a href="<?= $base_url ?>/portal-admin/informasi" class="admin-nav-item <?= $admin_active_page === 'info' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="nav-text">Informasi / FAQ</span>
        </a>
        <a href="<?= $base_url ?>/portal-admin/pathfinder" class="admin-nav-item <?= $admin_active_page === 'pathfinder' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="nav-text">Manajemen Pathfinder</span>
        </a>
        <a href="<?= $base_url ?>/portal-admin/donasi" class="admin-nav-item <?= $admin_active_page === 'donasi' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <span class="nav-text">Donasi</span>
        </a>
        <?php endif; ?>

        <?php if (in_array($_SESSION['admin_role'] ?? '', ['super_admin', 'admin', 'kontributor'])): ?>
        <a href="<?= $base_url ?>/portal-admin/produk" class="admin-nav-item <?= $admin_active_page === 'produk' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span class="nav-text">Produk</span>
        </a>
        <?php endif; ?>


        <?php if (($_SESSION['admin_role'] ?? '') === 'super_admin'): ?>
        <a href="<?= $base_url ?>/portal-admin/akun" class="admin-nav-item <?= $admin_active_page === 'accounts' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="nav-text">Manajemen Akun</span>
        </a>
        <a href="<?= $base_url ?>/portal-admin/aktivitas" class="admin-nav-item <?= $admin_active_page === 'activity' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="nav-text">Aktivitas Sistem</span>
        </a>
        <?php endif; ?>
    </nav>

    <div class="admin-nav-bottom">
        <a href="<?= $base_url ?>/portal-admin/settings" class="admin-nav-item <?= $admin_active_page === 'settings' ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="nav-text">Pengaturan</span>
        </a>
        <button id="btnToggleSidebar" style="background: transparent; border: none; cursor: pointer; display: flex; align-items: center; width: 100%; padding: 12px 16px; border-radius: 8px; gap: 12px; color: var(--admin-text-muted); font-family: inherit; font-size: 0.95rem; font-weight: 500; transition: background 0.2s;">
            <svg id="iconToggleSidebar" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="nav-text">Tutup Sidebar</span>
        </button>
    </div>
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleBtn = document.getElementById("btnToggleSidebar");
        const toggleIcon = document.getElementById("iconToggleSidebar");

        // Hover effect manual since it's a button
        toggleBtn.addEventListener("mouseenter", function() {
            toggleBtn.style.backgroundColor = "#f3f4f6";
            toggleBtn.style.color = "var(--admin-text-main)";
        });
        toggleBtn.addEventListener("mouseleave", function() {
            toggleBtn.style.backgroundColor = "transparent";
            toggleBtn.style.color = "var(--admin-text-muted)";
        });

        // Load state
        if (localStorage.getItem("sidebarCollapsed") === "true") {
            document.body.classList.add("sidebar-collapsed");
            toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />';
        }

        toggleBtn.addEventListener("click", function() {
            document.body.classList.toggle("sidebar-collapsed");
            const isCollapsed = document.body.classList.contains("sidebar-collapsed");
            localStorage.setItem("sidebarCollapsed", isCollapsed);

            if (isCollapsed) {
                toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />';
            } else {
                toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />';
            }
        });
    });
</script>

<?php include __DIR__ . '/admin-modals.php'; ?>
