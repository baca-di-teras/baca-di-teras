<?php
/**
 * Chip Filter Component
 *
 * Usage:
 * $activeChip = 'semua-perpustakaan';
 * $chipFilterItems = [
 *     ['id' => 'semua-perpustakaan', 'label' => 'Semua Perpustakaan', 'href' => '#'],
 * ];
 * include 'custom/components/chip-filter.php';
 */

$activeChip = $activeChip ?? 'semua-perpustakaan';
$chipFilterItems = $chipFilterItems ?? [
    ['id' => 'semua-perpustakaan', 'label' => 'Semua Perpustakaan', 'href' => '#'],
    ['id' => 'distrik-pusat', 'label' => 'Distrik Pusat', 'href' => '#'],
    ['id' => 'wilayah-perbukitan', 'label' => 'Wilayah Perbukitan', 'href' => '#'],
    ['id' => 'pusat-digital', 'label' => 'Pusat Digital', 'href' => '#'],
];

if (!function_exists('bdtEscapeText')) {
    function bdtEscapeText($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!defined('BDT_CHIP_FILTER_STYLE_LOADED')) :
    define('BDT_CHIP_FILTER_STYLE_LOADED', true);
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.bdt-chip-filter,
.bdt-chip-filter * {
    box-sizing: border-box;
}

.bdt-chip-filter {
    display: flex;
    align-items: center;
    gap: 13px;
    width: min(100%, 1084px);
    min-height: 150px;
    padding: 44px 34px;
    border: 1px solid #eeeeec;
    border-radius: 24px;
    background: #ffffff;
    box-shadow: 0 8px 20px rgba(20, 27, 22, 0.08);
    font-family: 'Inter', Arial, sans-serif;
}

.bdt-chip-filter__item {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 60px;
    padding: 0 28px;
    border-radius: 999px;
    background: #eeeae9;
    color: #444d43;
    font-size: 25px;
    font-weight: 700;
    line-height: 1;
    letter-spacing: 0;
    text-decoration: none;
    white-space: nowrap;
    transition: background-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
}

.bdt-chip-filter__item:hover {
    color: #086b20;
    text-decoration: none;
    transform: translateY(-1px);
}

.bdt-chip-filter__item.is-active {
    background: #086b20;
    color: #ffffff;
}

.bdt-chip-filter__item.is-active:hover {
    color: #ffffff;
}

@media (max-width: 1024px) {
    .bdt-chip-filter {
        overflow-x: auto;
        min-height: 104px;
        padding: 24px 18px;
        border-radius: 18px;
    }

    .bdt-chip-filter__item {
        min-height: 44px;
        padding: 0 18px;
        font-size: 16px;
    }
}
</style>
<?php endif; ?>

<nav class="bdt-chip-filter" aria-label="Filter perpustakaan">
    <?php foreach ($chipFilterItems as $chipFilterItem) :
        $chipId = (string) ($chipFilterItem['id'] ?? '');
        $isActive = $chipId === $activeChip;
    ?>
        <a
            class="bdt-chip-filter__item<?= $isActive ? ' is-active' : '' ?>"
            href="<?= bdtEscapeText($chipFilterItem['href'] ?? '#') ?>"
            <?= $isActive ? 'aria-current="page"' : '' ?>
        >
            <?= bdtEscapeText($chipFilterItem['label'] ?? '') ?>
        </a>
    <?php endforeach; ?>
</nav>
