<?php
/**
 * Pagination Component
 *
 * File    : pagination.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Komponen reusable untuk tombol navigasi halaman (pagination).
 *
 * Usage:
 *   $currentPage = 1;
 *   $totalPages = 12;
 *   include 'custom/components/pagination.php';
 */

$currentPage = $currentPage ?? 1;
$totalPages  = $totalPages ?? 12;

// Menjaga query parameters lain di URL saat berpindah halaman
$queryParams = $_GET;
?>
<style>
    .bdt-pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 48px;
    }
    .bdt-pagination__item {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1px solid #d0dbd3;
        background: #ffffff;
        color: #334155;
        font-size: 15px;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .bdt-pagination__item:hover {
        border-color: #1a6b2f;
        color: #1a6b2f;
        background-color: #f0f9f2;
    }
    .bdt-pagination__item--active {
        background-color: #0e5e32;
        color: #ffffff;
        border-color: #0e5e32;
    }
    .bdt-pagination__item--active:hover {
        background-color: #0c4e2a;
        color: #ffffff;
    }
    .bdt-pagination__item--disabled {
        color: #cbd5e1;
        border-color: #f1f5f9;
        background-color: #f8fafc;
        cursor: not-allowed;
        pointer-events: none;
    }
</style>

<nav class="bdt-pagination" aria-label="Navigasi halaman">
    <!-- Tombol Sebelumnya -->
    <?php 
    $prevParams = array_merge($queryParams, ['page' => max(1, $currentPage - 1)]);
    $prevHref = '?' . http_build_query($prevParams);
    ?>
    <a href="<?= $currentPage <= 1 ? '#' : htmlspecialchars($prevHref) ?>" 
       class="bdt-pagination__item <?= $currentPage <= 1 ? 'bdt-pagination__item--disabled' : '' ?>" 
       aria-label="Halaman sebelumnya">&lt;</a>
    
    <!-- Halaman Utama (1-3) -->
    <?php for ($i = 1; $i <= min(3, $totalPages); $i++) : 
        $iParams = array_merge($queryParams, ['page' => $i]);
        $iHref = '?' . http_build_query($iParams);
    ?>
        <a href="<?= htmlspecialchars($iHref) ?>" 
           class="bdt-pagination__item <?= $currentPage === $i ? 'bdt-pagination__item--active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    
    <!-- Elipsis jika halaman banyak -->
    <?php if ($totalPages > 3) : ?>
        <span style="display: flex; align-items: flex-end; padding: 0 4px; color: #64748b;">...</span>
        
        <?php 
        $lastParams = array_merge($queryParams, ['page' => $totalPages]);
        $lastHref = '?' . http_build_query($lastParams);
        ?>
        <a href="<?= htmlspecialchars($lastHref) ?>" 
           class="bdt-pagination__item <?= $currentPage === $totalPages ? 'bdt-pagination__item--active' : '' ?>"><?= $totalPages ?></a>
    <?php endif; ?>
    
    <!-- Tombol Berikutnya -->
    <?php 
    $nextParams = array_merge($queryParams, ['page' => min($totalPages, $currentPage + 1)]);
    $nextHref = '?' . http_build_query($nextParams);
    ?>
    <a href="<?= $currentPage >= $totalPages ? '#' : htmlspecialchars($nextHref) ?>" 
       class="bdt-pagination__item <?= $currentPage >= $totalPages ? 'bdt-pagination__item--disabled' : '' ?>" 
       aria-label="Halaman berikutnya">&gt;</a>
</nav>
