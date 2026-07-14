<?php
/**
 * Search Bar Component
 *
 * Usage:
 * $searchBar = [
 *     'placeholder' => 'Cari berdasarkan nama atau jalan...',
 *     'name' => 'search'
 * ];
 * include 'custom/components/search-bar.php';
 */

$searchBarDefaults = [
    'action' => '#',
    'method' => 'get',
    'name' => 'search',
    'value' => '',
    'placeholder' => 'Cari berdasarkan nama atau jalan...',
    'label' => 'Cari perpustakaan',
];

$searchBar = array_merge($searchBarDefaults, $searchBar ?? []);

if (!function_exists('bdtEscapeText')) {
    function bdtEscapeText($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!defined('BDT_SEARCH_BAR_STYLE_LOADED')) :
    define('BDT_SEARCH_BAR_STYLE_LOADED', true);
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.bdt-search-bar,
.bdt-search-bar * {
    box-sizing: border-box;
}

.bdt-search-bar {
    width: min(100%, 640px);
    height: 68px;
    margin: 0;
    font-family: 'Inter', Arial, sans-serif;
}

.bdt-search-bar__field {
    display: flex;
    align-items: center;
    width: 100%;
    height: 100%;
    padding: 0 22px;
    border: 2px solid #bdc9b8;
    border-radius: 12px;
    background: #fbfaf9;
}

.bdt-search-bar__icon {
    width: 32px;
    height: 32px;
    margin-right: 26px;
    color: #3f4a41;
    flex: 0 0 auto;
}

.bdt-search-bar__input {
    width: 100%;
    min-width: 0;
    border: 0;
    outline: 0;
    background: transparent;
    color: #202124;
    font: inherit;
    font-size: 27px;
    font-weight: 400;
    line-height: 1;
    letter-spacing: 0;
}

.bdt-search-bar__input::placeholder {
    color: #737989;
    opacity: 1;
}

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

@media (max-width: 768px) {
    .bdt-search-bar {
        height: 42px;
    }

    .bdt-search-bar__field {
        padding: 0 14px;
        border-radius: 7px;
        border-width: 1px;
    }

    .bdt-search-bar__icon {
        width: 18px;
        height: 18px;
        margin-right: 12px;
    }

    .bdt-search-bar__input {
        font-size: 13px;
    }
}
</style>
<?php endif; ?>

<form
    class="bdt-search-bar"
    action="<?= bdtEscapeText($searchBar['action']) ?>"
    method="<?= bdtEscapeText($searchBar['method']) ?>"
    role="search"
>
    <label class="bdt-search-bar__field">
        <span class="sr-only"><?= bdtEscapeText($searchBar['label']) ?></span>
        <svg class="bdt-search-bar__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M10.8 18.1a7.3 7.3 0 1 0 0-14.6 7.3 7.3 0 0 0 0 14.6Z" stroke="currentColor" stroke-width="2.2"/>
            <path d="M16.2 16.2 21 21" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
        </svg>
        <input
            class="bdt-search-bar__input"
            type="search"
            name="<?= bdtEscapeText($searchBar['name']) ?>"
            value="<?= bdtEscapeText($searchBar['value']) ?>"
            placeholder="<?= bdtEscapeText($searchBar['placeholder']) ?>"
            aria-label="<?= bdtEscapeText($searchBar['label']) ?>"
        >
    </label>
</form>
