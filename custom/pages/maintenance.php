<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang Dalam Perbaikan - Baca di Teras</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            max-width: 600px;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
        }
        svg {
            color: #f59e0b;
            margin-bottom: 24px;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 16px 0;
            color: #111827;
        }
        p {
            font-size: 1rem;
            color: #4b5563;
            line-height: 1.6;
            margin: 0 0 24px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #059669;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.2s;
        }
        .btn:hover {
            background-color: #047857;
        }
    </style>
</head>
<body>
    <div class="container">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.053c.427-.521.573-1.229.384-1.879l-1.536-5.26a1.18 1.18 0 00-1.896-.532l-1.391 1.392c-.378.378-1.042.308-1.341-.127L7.04 4.093A1.18 1.18 0 005.143 4.62l-1.535 5.26c-.19.65.043 1.358.47 1.88l2.496 3.053m8.846-8.846l1.242-1.242c.8-.8 2.096-.8 2.896 0 .8.8.8 2.096 0 2.896l-1.242 1.242m-14.498 7.373a3.5 3.5 0 014.95-4.95l1.046 1.046a3.5 3.5 0 01-4.95 4.95L3.38 18.236z" />
        </svg>
        <h1>Website Sedang Dalam Perbaikan</h1>
        <p>Maaf, halaman ini sedang dalam masa pemeliharaan (maintenance mode). Silakan kembali sesaat lagi. Kami sedang bekerja untuk memberikan pengalaman terbaik untuk Anda.</p>
        <a href="<?= defined('BASE_URL') ? BASE_URL : '/baca-di-teras' ?>/" class="btn">Kembali ke Beranda</a>
    </div>
</body>
</html>
