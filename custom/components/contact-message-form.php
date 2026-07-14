<?php
/**
 * Contact Message Form Component
 *
 * Usage:
 * $contactMessageForm = [
 *     'action' => '#',
 *     'method' => 'post'
 * ];
 * include 'custom/components/contact-message-form.php';
 */

$contactMessageFormDefaults = [
    'title' => 'Kirim Pesan',
    'action' => '#',
    'method' => 'post',
    'name_label' => 'Nama Lengkap',
    'name_placeholder' => 'Nama Anda',
    'email_label' => 'Alamat Email',
    'email_placeholder' => 'contoh@mail.com',
    'subject_label' => 'Subjek',
    'subject_placeholder' => 'Bagaimana kami bisa membantumu?',
    'message_label' => 'Pesan',
    'message_placeholder' => 'Tulis pesan Anda di sini...',
    'button_label' => 'Kirim Pesan',
];

$contactMessageForm = array_merge($contactMessageFormDefaults, $contactMessageForm ?? []);

if (!function_exists('bdtEscapeText')) {
    function bdtEscapeText($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!defined('BDT_CONTACT_MESSAGE_FORM_STYLE_LOADED')) :
    define('BDT_CONTACT_MESSAGE_FORM_STYLE_LOADED', true);
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.bdt-contact-message,
.bdt-contact-message * {
    box-sizing: border-box;
}

.bdt-contact-message {
    width: 100%;
    min-height: 675px;
    padding: 36px 34px 48px;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 18px 44px rgba(24, 28, 25, 0.08);
    color: #202124;
    font-family: 'Inter', Arial, sans-serif;
}

.bdt-contact-message__title {
    margin: 0 0 34px;
    color: #086b20;
    font-size: 24px;
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: 0;
}

.bdt-contact-message__form {
    display: grid;
    gap: 27px;
}

.bdt-contact-message__row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 28px;
}

.bdt-contact-message__field {
    display: grid;
    gap: 10px;
}

.bdt-contact-message__label {
    color: #3f463f;
    font-size: 14px;
    font-weight: 500;
    line-height: 1.2;
    letter-spacing: 0;
}

.bdt-contact-message__input,
.bdt-contact-message__textarea {
    width: 100%;
    border: 0;
    outline: 0;
    border-radius: 10px;
    background: #f3f1f1;
    color: #202124;
    font: inherit;
    font-size: 15px;
    font-weight: 400;
    letter-spacing: 0;
}

.bdt-contact-message__input {
    height: 54px;
    padding: 0 23px;
}

.bdt-contact-message__textarea {
    min-height: 138px;
    resize: none;
    padding: 19px 23px;
    line-height: 1.5;
}

.bdt-contact-message__input::placeholder,
.bdt-contact-message__textarea::placeholder {
    color: #737989;
    opacity: 1;
}

.bdt-contact-message__input:focus,
.bdt-contact-message__textarea:focus {
    box-shadow: 0 0 0 2px rgba(8, 107, 32, 0.22);
}

.bdt-contact-message__button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 190px;
    min-height: 52px;
    margin-top: 2px;
    border: 0;
    border-radius: 9px;
    background: #086b20;
    color: #ffffff;
    cursor: pointer;
    font: inherit;
    font-size: 14px;
    font-weight: 500;
    line-height: 1;
    letter-spacing: 0;
    box-shadow: 0 7px 14px rgba(8, 107, 32, 0.18);
    transition: background-color 0.18s ease, transform 0.18s ease;
}

.bdt-contact-message__button:hover {
    background: #075d1c;
    transform: translateY(-1px);
}

.bdt-contact-message__button svg {
    width: 20px;
    height: 20px;
    flex: 0 0 auto;
}

@media (max-width: 768px) {
    .bdt-contact-message {
        min-height: auto;
        padding: 28px 22px 32px;
    }

    .bdt-contact-message__title {
        margin-bottom: 26px;
        font-size: 22px;
    }

    .bdt-contact-message__row {
        grid-template-columns: 1fr;
        gap: 22px;
    }

    .bdt-contact-message__form {
        gap: 22px;
    }

    .bdt-contact-message__button {
        width: 100%;
    }
}
</style>
<?php endif; ?>

<section class="bdt-contact-message" aria-labelledby="bdt-contact-message-title">
    <h2 class="bdt-contact-message__title" id="bdt-contact-message-title">
        <?= bdtEscapeText($contactMessageForm['title']) ?>
    </h2>

    <form
        class="bdt-contact-message__form"
        action="<?= bdtEscapeText($contactMessageForm['action']) ?>"
        method="<?= bdtEscapeText($contactMessageForm['method']) ?>"
    >
        <div class="bdt-contact-message__row">
            <label class="bdt-contact-message__field">
                <span class="bdt-contact-message__label"><?= bdtEscapeText($contactMessageForm['name_label']) ?></span>
                <input
                    class="bdt-contact-message__input"
                    type="text"
                    name="fullName"
                    placeholder="<?= bdtEscapeText($contactMessageForm['name_placeholder']) ?>"
                >
            </label>

            <label class="bdt-contact-message__field">
                <span class="bdt-contact-message__label"><?= bdtEscapeText($contactMessageForm['email_label']) ?></span>
                <input
                    class="bdt-contact-message__input"
                    type="email"
                    name="emailAddress"
                    placeholder="<?= bdtEscapeText($contactMessageForm['email_placeholder']) ?>"
                >
            </label>
        </div>

        <label class="bdt-contact-message__field">
            <span class="bdt-contact-message__label"><?= bdtEscapeText($contactMessageForm['subject_label']) ?></span>
            <input
                class="bdt-contact-message__input"
                type="text"
                name="subject"
                placeholder="<?= bdtEscapeText($contactMessageForm['subject_placeholder']) ?>"
            >
        </label>

        <label class="bdt-contact-message__field">
            <span class="bdt-contact-message__label"><?= bdtEscapeText($contactMessageForm['message_label']) ?></span>
            <textarea
                class="bdt-contact-message__textarea"
                name="message"
                placeholder="<?= bdtEscapeText($contactMessageForm['message_placeholder']) ?>"
            ></textarea>
        </label>

        <button class="bdt-contact-message__button" type="submit">
            <span><?= bdtEscapeText($contactMessageForm['button_label']) ?></span>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4.5 5.25 20 12 4.5 18.75V13.5L14 12 4.5 10.5V5.25Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
        </button>
    </form>
</section>
