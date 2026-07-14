<?php
/**
 * Footer Preview
 * File ini hanya untuk keperluan development preview.
 * JANGAN di-deploy ke production.
 */

define('BASE_URL', '/baca-di-teras');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview – Footer | Baca Di Teras</title>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f0f0f0;
        }
        .preview-spacer {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #9ca3af;
            border: 2px dashed #d1d5db;
            margin: 24px;
            border-radius: 12px;
            background: #fff;
        }
    </style>
</head>
<body>

    <!-- Simulasi konten halaman di atas footer -->
    <div class="preview-spacer">↓ Konten halaman — Footer ada di bawah ↓</div>

    <!-- Footer Component -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

</body>
</html>
