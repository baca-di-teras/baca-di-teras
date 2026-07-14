<?php
/**
 * Contact Information Component
 *
 * Usage:
 * $contactInformationItems = [
 *     ['type' => 'phone', 'label' => 'Telepon', 'value' => '+62 812-3456-7890'],
 * ];
 * include 'custom/components/contact-information.php';
 */

$contactInformationTitle = $contactInformationTitle ?? 'Informasi Kontak';
$contactInformationItems = $contactInformationItems ?? [
    ['type' => 'phone', 'label' => 'Telepon', 'value' => '+62 812-3456-7890'],
    ['type' => 'message', 'label' => 'WhatsApp', 'value' => '+62 812-3456-7890'],
    ['type' => 'email', 'label' => 'Email', 'value' => 'halo@desateras.id'],
];

if (!function_exists('bdtEscapeText')) {
    function bdtEscapeText($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('bdtContactInformationIcon')) {
    function bdtContactInformationIcon($type)
    {
        $icons = [
            'phone' => '<path d="M7.25 5.25 9.2 4.15a1.2 1.2 0 0 1 1.65.48l1.2 2.2a1.25 1.25 0 0 1-.28 1.5l-.82.78a10.42 10.42 0 0 0 3.94 3.94l.78-.82a1.25 1.25 0 0 1 1.5-.28l2.2 1.2a1.2 1.2 0 0 1 .48 1.65l-1.1 1.95c-.4.71-1.23 1.08-2.03.9C10.9 16.36 7.64 13.1 6.35 7.28c-.18-.8.19-1.63.9-2.03Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
            'message' => '<path d="M5 5.75h14v10.5H9.25L5 19.25V5.75Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8.5 9.25h7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M8.5 12.25h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
            'email' => '<path d="M4.75 6.5h14.5v11H4.75v-11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m5.5 7.25 6.5 5.25 6.5-5.25" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
        ];

        return $icons[$type] ?? $icons['phone'];
    }
}

if (!defined('BDT_CONTACT_INFORMATION_STYLE_LOADED')) :
    define('BDT_CONTACT_INFORMATION_STYLE_LOADED', true);
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.bdt-contact-information,
.bdt-contact-information * {
    box-sizing: border-box;
}

.bdt-contact-information {
    position: relative;
    width: 100%;
    min-height: 286px;
    overflow: hidden;
    padding: 32px 32px 30px;
    border-radius: 13px;
    background: #2a8433;
    box-shadow: 0 16px 30px rgba(18, 35, 22, 0.18);
    color: #ffffff;
    font-family: 'Inter', Arial, sans-serif;
}

.bdt-contact-information__mark {
    position: absolute;
    top: 22px;
    right: 25px;
    width: 58px;
    height: 58px;
    color: rgba(255, 255, 255, 0.12);
    pointer-events: none;
}

.bdt-contact-information__title {
    position: relative;
    margin: 0 0 31px;
    color: #f4fff2;
    font-size: 26px;
    font-weight: 600;
    line-height: 1.2;
    letter-spacing: 0;
}

.bdt-contact-information__list {
    position: relative;
    display: grid;
    gap: 20px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.bdt-contact-information__item {
    display: flex;
    align-items: center;
    gap: 22px;
}

.bdt-contact-information__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.18);
    color: #e9f7e8;
    flex: 0 0 auto;
}

.bdt-contact-information__icon svg {
    width: 22px;
    height: 22px;
}

.bdt-contact-information__text {
    display: grid;
    gap: 3px;
}

.bdt-contact-information__label {
    color: #bfe1bf;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.25;
    letter-spacing: 0;
}

.bdt-contact-information__value {
    color: #ffffff;
    font-size: 16px;
    font-weight: 600;
    line-height: 1.25;
    letter-spacing: 0;
}

@media (max-width: 768px) {
    .bdt-contact-information {
        min-height: auto;
        padding: 28px 24px;
    }

    .bdt-contact-information__title {
        margin-bottom: 26px;
        font-size: 23px;
    }
}
</style>
<?php endif; ?>

<section class="bdt-contact-information" aria-labelledby="bdt-contact-information-title">
    <svg class="bdt-contact-information__mark" viewBox="0 0 64 64" fill="none" aria-hidden="true">
        <path d="M32 55C19.3 55 9 44.7 9 32S19.3 9 32 9s23 10.3 23 23c0 5.1-1.65 9.8-4.45 13.62L52 55l-9.55-1.5A22.8 22.8 0 0 1 32 55Z" stroke="currentColor" stroke-width="5" stroke-linejoin="round"/>
        <path d="M25.5 25.5c.55-4 3.5-6.5 7.3-6.5 4.2 0 7.2 2.75 7.2 6.65 0 3.1-1.85 4.75-4.1 6.28-2.05 1.4-3.15 2.65-3.15 5.07" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
        <path d="M32.7 45h.05" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
    </svg>

    <h2 class="bdt-contact-information__title" id="bdt-contact-information-title">
        <?= bdtEscapeText($contactInformationTitle) ?>
    </h2>

    <ul class="bdt-contact-information__list">
        <?php foreach ($contactInformationItems as $contactInformationItem) :
            $contactInformationType = (string) ($contactInformationItem['type'] ?? 'phone');
        ?>
            <li class="bdt-contact-information__item">
                <span class="bdt-contact-information__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <?= bdtContactInformationIcon($contactInformationType) ?>
                    </svg>
                </span>
                <span class="bdt-contact-information__text">
                    <span class="bdt-contact-information__label"><?= bdtEscapeText($contactInformationItem['label'] ?? '') ?></span>
                    <span class="bdt-contact-information__value"><?= bdtEscapeText($contactInformationItem['value'] ?? '') ?></span>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
