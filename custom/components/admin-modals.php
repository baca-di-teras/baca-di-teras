<?php
/**
 * Global Admin Modals
 * Included at the end of admin-sidebar.php (or in admin body)
 */
?>
<style>
/* Modal Overlay */
.admin-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease;
}
.admin-modal-overlay.active {
    opacity: 1;
    pointer-events: auto;
}

/* Modal Box */
.admin-modal-box {
    background: #fff;
    border-radius: 16px;
    width: 90%;
    max-width: 400px;
    padding: 32px 24px 24px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    transform: scale(0.95) translateY(10px);
    transition: transform 0.25s ease;
    text-align: center;
    font-family: 'Inter', sans-serif;
}
.admin-modal-overlay.active .admin-modal-box {
    transform: scale(1) translateY(0);
}

/* Icon */
.admin-modal-icon {
    width: 64px; height: 64px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
}
.admin-modal-icon.warning { background: #fef3c7; color: #d97706; }
.admin-modal-icon.success { background: #d1fae5; color: #059669; }
.admin-modal-icon.error { background: #fee2e2; color: #dc2626; }
.admin-modal-icon svg { width: 32px; height: 32px; }

/* Text */
.admin-modal-title {
    font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 12px;
}
.admin-modal-desc {
    font-size: 0.95rem; color: #4b5563; margin: 0 0 28px; line-height: 1.5;
}

/* Actions */
.admin-modal-actions {
    display: flex; gap: 12px;
}
.admin-modal-btn {
    flex: 1;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 0.95rem; font-weight: 600;
    cursor: pointer;
    border: none;
    transition: background 0.2s;
    font-family: inherit;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.admin-modal-btn.btn-cancel {
    background: #f3f4f6; color: #374151;
}
.admin-modal-btn.btn-cancel:hover { background: #e5e7eb; }

.admin-modal-btn.btn-confirm {
    background: #dc2626; color: #fff;
}
.admin-modal-btn.btn-confirm:hover { background: #b91c1c; }

.admin-modal-btn.btn-ok {
    background: #2563eb; color: #fff;
}
.admin-modal-btn.btn-ok:hover { background: #1d4ed8; }
</style>

<!-- Confirm Modal -->
<div class="admin-modal-overlay" id="adminConfirmModal">
    <div class="admin-modal-box">
        <div class="admin-modal-icon warning">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h3 class="admin-modal-title" id="adminConfirmTitle">Konfirmasi</h3>
        <p class="admin-modal-desc" id="adminConfirmDesc">Apakah Anda yakin?</p>
        <div class="admin-modal-actions">
            <button class="admin-modal-btn btn-cancel" id="adminConfirmCancel">Batal</button>
            <button class="admin-modal-btn btn-confirm" id="adminConfirmBtn">Ya, Hapus</button>
        </div>
    </div>
</div>

<!-- Result Modal -->
<div class="admin-modal-overlay" id="adminResultModal">
    <div class="admin-modal-box">
        <div class="admin-modal-icon" id="adminResultIcon">
            <!-- SVG Injected via JS -->
        </div>
        <h3 class="admin-modal-title" id="adminResultTitle">Status</h3>
        <p class="admin-modal-desc" id="adminResultDesc">Pesan</p>
        <div class="admin-modal-actions">
            <button class="admin-modal-btn btn-ok" id="adminResultOkBtn" style="background: #1a6b2f; color: #fff;">Tutup</button>
            <a href="#" class="admin-modal-btn" id="adminResultPreviewBtn" target="_blank" style="display: none; background: #2563eb; color: #fff; text-decoration: none;">Lihat Halaman</a>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // --- CONFIRM MODAL LOGIC ---
    let pendingForm = null;
    let pendingUrl = null;
    
    const confirmOverlay = document.getElementById('adminConfirmModal');
    const confirmCancel = document.getElementById('adminConfirmCancel');
    const confirmBtn = document.getElementById('adminConfirmBtn');
    const confirmTitle = document.getElementById('adminConfirmTitle');
    const confirmDesc = document.getElementById('adminConfirmDesc');
    
    function showConfirmModal(message, isDelete) {
        confirmDesc.textContent = message;
        confirmTitle.textContent = isDelete ? "Konfirmasi Hapus" : "Konfirmasi Tindakan";
        confirmBtn.textContent = isDelete ? "Ya, Hapus" : "Ya, Lanjutkan";
        confirmBtn.className = "admin-modal-btn " + (isDelete ? "btn-confirm" : "btn-ok");
        if(!isDelete) {
            confirmBtn.style.background = '#1a6b2f';
        } else {
            confirmBtn.style.background = '';
        }
        confirmOverlay.classList.add('active');
    }
    
    function closeConfirmModal() {
        confirmOverlay.classList.remove('active');
        pendingForm = null;
        pendingUrl = null;
    }
    
    confirmCancel.addEventListener('click', closeConfirmModal);
    
    confirmBtn.addEventListener('click', function() {
        if (pendingForm) {
            const form = pendingForm;
            form.removeAttribute('onsubmit');
            form.submit();
        } else if (pendingUrl) {
            window.location.href = pendingUrl;
        }
        closeConfirmModal();
    });

    // Mencegat form dengan onsubmit="return confirm(...)"
    document.querySelectorAll('form[onsubmit*="return confirm"]').forEach(form => {
        const onsubmitAttr = form.getAttribute('onsubmit');
        const match = onsubmitAttr.match(/confirm\(\s*['"](.*?)['"]\s*\)/);
        const msg = match ? match[1] : "Apakah Anda yakin?";
        const isDelete = msg.toLowerCase().includes("hapus") || onsubmitAttr.toLowerCase().includes("delete");
        
        form.removeAttribute('onsubmit');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            pendingForm = form;
            pendingUrl = null;
            showConfirmModal(msg, isDelete);
        });
    });

    // Mencegat link dengan onclick="return confirm(...)"
    document.querySelectorAll('a[onclick*="return confirm"]').forEach(link => {
        const onclickAttr = link.getAttribute('onclick');
        const match = onclickAttr.match(/confirm\(\s*['"](.*?)['"]\s*\)/);
        const msg = match ? match[1] : "Apakah Anda yakin?";
        const isDelete = msg.toLowerCase().includes("hapus") || onclickAttr.toLowerCase().includes("delete");
        
        link.removeAttribute('onclick');
        
        link.addEventListener('click', function(e) {
            e.preventDefault();
            pendingUrl = link.href;
            pendingForm = null;
            showConfirmModal(msg, isDelete);
        });
    });
    
    
    // --- RESULT MODAL LOGIC ---
    const resultOverlay = document.getElementById('adminResultModal');
    const resultOkBtn = document.getElementById('adminResultOkBtn');
    const resultIcon = document.getElementById('adminResultIcon');
    const resultTitle = document.getElementById('adminResultTitle');
    const resultDesc = document.getElementById('adminResultDesc');
    const resultPreviewBtn = document.getElementById('adminResultPreviewBtn');
    
    const iconSuccess = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>';
    const iconError = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>';
    
    function showResultModal(type, title, message, previewUrl = null) {
        resultTitle.textContent = title;
        resultDesc.innerHTML = message; // Use innerHTML to allow line breaks or formatting if needed
        
        if (type === 'success') {
            resultIcon.className = "admin-modal-icon success";
            resultIcon.innerHTML = iconSuccess;
        } else {
            resultIcon.className = "admin-modal-icon error";
            resultIcon.innerHTML = iconError;
        }
        
        if (previewUrl) {
            resultPreviewBtn.href = previewUrl;
            resultPreviewBtn.style.display = 'inline-flex';
        } else {
            resultPreviewBtn.style.display = 'none';
        }
        
        resultOverlay.classList.add('active');
    }
    
    resultOkBtn.addEventListener('click', function() {
        resultOverlay.classList.remove('active');
        const url = new URL(window.location);
        if (url.searchParams.has('success') || url.searchParams.has('error')) {
            url.searchParams.delete('success');
            url.searchParams.delete('error');
            url.searchParams.delete('slug');
            url.searchParams.delete('cat');
            window.history.replaceState({}, document.title, url);
        }
    });
    
    // Menampilkan popup otomatis berdasar parameter URL atau Variabel PHP global
    const urlParams = new URLSearchParams(window.location.search);
    let autoShow = false;
    let autoType = '';
    let autoTitle = '';
    let autoMsg = '';
    let autoPreviewUrl = null;
    
    if (urlParams.has('success')) {
        autoShow = true;
        autoType = 'success';
        autoTitle = 'Berhasil!';
        
        const val = urlParams.get('success');
        if (val === 'deleted') autoMsg = 'Data berhasil dihapus dari sistem.';
        else if (val === 'created' || val === 'upload') autoMsg = 'Data baru berhasil disimpan.';
        else if (val === 'updated' || val === 'edit') autoMsg = 'Data berhasil diperbarui.';
        else autoMsg = 'Operasi berhasil diselesaikan.';
        
        if (urlParams.has('slug') && urlParams.has('cat')) {
            const slug = urlParams.get('slug');
            const cat = urlParams.get('cat');
            const isNews = ['berita', 'kegiatan', 'pengumuman'].includes(cat);
            const routePrefix = isNews ? '/berita/' : '/artikel/';
            autoPreviewUrl = "<?= BASE_URL ?>" + routePrefix + slug;
        }
    }
    else if (urlParams.has('error')) {
        autoShow = true;
        autoType = 'error';
        autoTitle = 'Terjadi Kesalahan';
        autoMsg = 'Gagal melakukan operasi. Silakan coba lagi.';
    }
    
    // Check PHP globals set before this script
    <?php if (isset($error_msg) && !empty($error_msg)): ?>
        autoShow = true;
        autoType = 'error';
        autoTitle = 'Gagal!';
        autoMsg = <?= json_encode($error_msg) ?>;
    <?php elseif (isset($success_msg) && !empty($success_msg)): ?>
        autoShow = true;
        autoType = 'success';
        autoTitle = 'Berhasil!';
        autoMsg = <?= json_encode($success_msg) ?>;
    <?php endif; ?>

    if (autoShow) {
        // slight delay to let CSS load properly before triggering animation
        setTimeout(() => {
            showResultModal(autoType, autoTitle, autoMsg, autoPreviewUrl);
        }, 50);
    }
});
</script>
