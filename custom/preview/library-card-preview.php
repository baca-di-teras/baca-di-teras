<?php
/**
 * Library Card Preview
 * Development-only preview for custom/components/library-card.php.
 */

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseUrl = str_replace('/custom/preview/library-card-preview.php', '', $scriptName);
define('BASE_URL', rtrim($baseUrl, '/'));

$libraryCard = [
    'title' => 'Perpustakaan Teras Utama',
    'description' => 'Perpustakaan unggulan kami yang melestarikan catatan leluhur desa bersama...',
    'address' => 'Jl. Raya Teras No. 12, Alun-alun Pusat',
    'hours' => '08:00 AM - 08:00 PM (Setiap Hari)',
    'badge' => 'Pusat Utama',
    'metric' => '8.4rb',
    'href' => '#',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview - Library Card | Baca Di Teras</title>
    <style>
        html,
        body {
            min-height: 100%;
        }

        body {
            margin: 0;
            background: #f7f7f6;
        }

        .bdt-card-preview {
            width: 508px;
            min-height: 100vh;
            margin: 0 auto;
            padding: 35px 8px;
            background: #f7f7f6;
        }

        @media (max-width: 508px) {
            .bdt-card-preview {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <main class="bdt-card-preview">
        <?php include __DIR__ . '/../components/library-card.php'; ?>
    </main>
</body>
</html>
