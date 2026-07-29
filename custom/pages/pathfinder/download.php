<?php
/**
 * Pathfinder PDF Download
 *
 * File    : download.php
 * Project : Baca Di Teras
 *
 * Generate PDF menggunakan mPDF (via SLiMS autoload).
 */

require_once ROOT_PATH . '/custom/services/PathfinderV2Service.php';
require_once ROOT_PATH . '/slims/lib/autoload.php';

$slug = $routeParams['slug'] ?? '';
if (empty($slug)) {
    http_response_code(404);
    die('Topik tidak ditemukan.');
}

$pathfinderService = new PathfinderV2Service();
$topic = $pathfinderService->getTopicBySlug($slug);

if (!$topic) {
    http_response_code(404);
    die('Topik tidak ditemukan.');
}

$topicId = (int)$topic['id'];
$introduction = $pathfinderService->getIntroduction($topicId);
$books = $pathfinderService->getBooksByTopicId($topicId, 100);
$ext_resources = $pathfinderService->getExternalResourcesByTopicId($topicId);

// Setup mPDF
try {
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 15,
        'margin_bottom' => 15,
        'margin_header' => 0,
        'margin_footer' => 10,
    ]);
} catch (\Mpdf\MpdfException $e) {
    die("Error initializing mPDF: " . $e->getMessage());
}

$logoPath = ROOT_PATH . '/custom/assets/images/logo.png';
$logoHtml = '';
if (file_exists($logoPath)) {
    // Encode image to base64 so mPDF handles it smoothly without path issues
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoHtml = '<img src="data:image/png;base64,' . $logoData . '" style="height: 40px; margin-right: 15px; vertical-align: middle;">';
} else {
    $logoHtml = '<span style="font-size: 24px; font-weight: bold; margin-right: 15px; vertical-align: middle;">BACA DI TERAS</span>';
}

$html = '
<style>
    body {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        color: #333333;
        line-height: 1.6;
        font-size: 11pt;
    }
    .header-banner {
        background-color: #0b4a20; /* Dark green */
        color: #ffffff;
        padding: 20px;
        margin-top: -15px;
        margin-left: -15px;
        margin-right: -15px;
        margin-bottom: 30px;
    }
    .header-content {
        width: 100%;
    }
    .header-title {
        font-size: 24px;
        font-weight: bold;
        color: #ffffff;
        margin: 0;
        padding: 0;
    }
    .page-number {
        text-align: right;
        color: #ffffff;
        font-size: 16px;
    }
    h1 {
        color: #0b4a20;
        font-size: 24pt;
        text-transform: uppercase;
        margin-bottom: 15px;
        border-bottom: 2px solid #e0e0e0;
        padding-bottom: 10px;
    }
    h2 {
        color: #0b4a20;
        font-size: 16pt;
        text-transform: uppercase;
        margin-top: 30px;
        margin-bottom: 15px;
        border-bottom: 2px solid #e0e0e0;
        padding-bottom: 5px;
    }
    .section-title {
        border-left: 5px solid #0b4a20;
        padding-left: 10px;
        font-weight: bold;
        font-size: 12pt;
        color: #0b4a20;
        margin-top: 20px;
        margin-bottom: 10px;
    }
    .book-item {
        margin-bottom: 20px;
        page-break-inside: avoid;
    }
    .book-title {
        font-weight: bold;
        color: #333333;
        font-size: 11pt;
        display: inline;
    }
    .book-meta {
        font-size: 10pt;
        color: #555555;
        line-height: 1.4;
    }
    .book-meta span {
        display: block;
    }
    .book-url {
        font-size: 10pt;
        color: #2b7a78;
        text-decoration: underline;
        margin-top: 5px;
    }
    a {
        color: #2b7a78;
        text-decoration: underline;
    }
    .quote-box {
        font-style: italic;
        padding: 10px 15px;
        border-left: 3px solid #0b4a20;
        background-color: #f9f9f9;
        margin-bottom: 20px;
    }
    .footer {
        background-color: #0b4a20;
        color: #ffffff;
        padding: 15px;
        margin-left: -15px;
        margin-right: -15px;
        margin-bottom: -15px;
        font-size: 9pt;
        border-bottom: 5px solid #cc0000;
    }
    .footer table {
        width: 100%;
    }
    .footer td {
        color: #ffffff;
    }
</style>
';

// Setup Header
$mpdf->SetHTMLHeader('
<div class="header-banner">
    <table class="header-content" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="70%" valign="middle">
                ' . $logoHtml . '
                <span style="font-size: 18px; font-weight: bold; margin-left: 10px;">Pathfinder</span>
            </td>
            <td width="30%" valign="middle" align="right">
                <div class="page-number">{PAGENO}</div>
            </td>
        </tr>
    </table>
</div>
');

// Setup Footer
$mpdf->SetHTMLFooter('
<div class="footer">
    <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="50%" align="left">
                Disusun oleh: Perpustakaan Desa Teras
            </td>
            <td width="50%" align="right">
                Diperbarui: ' . date('m/Y') . ' &mdash; Baca di Teras Pathfinder Series
            </td>
        </tr>
    </table>
</div>
');

$html .= '<h1>' . htmlspecialchars($topic['name']) . '</h1>';

// Introduction
if ($introduction) {
    if (!empty($topic['description'])) {
        $html .= '<div class="quote-box">"' . htmlspecialchars($topic['description']) . '"</div>';
    }
    
    // Clean up HTML tags slightly for mPDF (mPDF is quite strict)
    $definition = strip_tags($introduction['definition'], '<p><br><b><strong><i><em><ul><ol><li><blockquote>');
    $html .= '<div>' . $definition . '</div>';

    if (!empty($introduction['importance'])) {
        $html .= '<h2>MENGAPA INI PENTING?</h2>';
        $importance = strip_tags($introduction['importance'], '<p><br><b><strong><i><em><ul><ol><li><blockquote>');
        $html .= '<div>' . $importance . '</div>';
    }
}

// Books Collection
$html .= '<h2>KOLEKSI BACA DI TERAS</h2>';

if (empty($books)) {
    $html .= '<p>Belum ada koleksi buku untuk topik ini.</p>';
} else {
    $html .= '<div class="section-title">Buku</div>';
    foreach ($books as $book) {
        $html .= '<div class="book-item">';
        
        $authorText = !empty($book['author_name']) ? htmlspecialchars($book['author_name']) . ' ' : '';
        $yearText = !empty($book['publish_year']) ? '(' . htmlspecialchars($book['publish_year']) . '). ' : '';
        $titleText = htmlspecialchars($book['title']);
        $publisherText = !empty($book['sor']) ? ' ' . htmlspecialchars($book['sor']) . '.' : '';
        
        $html .= '<div class="book-title">' . $authorText . $yearText . '<strong>' . $titleText . '</strong>.' . $publisherText . '</div>';
        
        $html .= '<div class="book-meta">';
        if (!empty($book['call_number'])) {
            $html .= '<span>Nomor Panggil: ' . htmlspecialchars($book['call_number']) . '</span>';
        }
        if (!empty($book['isbn_issn'])) {
            $html .= '<span>ISBN: ' . htmlspecialchars($book['isbn_issn']) . '</span>';
        }
        
        // Link to Baca Di Teras Catalog
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . BASE_URL;
        $bookUrl = $baseUrl . '/pathfinder/jelajahi/' . urlencode($topic['category_slug']) . '/' . urlencode($slug) . '/buku/' . (int)$book['biblio_id'];
        
        $html .= '<span class="book-url">URL: <a href="' . $bookUrl . '">' . $bookUrl . '</a></span>';
        $html .= '</div>';
        
        $html .= '</div>'; // close book-item
    }
}

// External Resources
if (!empty($ext_resources)) {
    $html .= '<h2>SUMBER INTERNET TERBUKA</h2>';
    $html .= '<ul style="list-style: none; padding: 0; margin: 0;">';
    foreach ($ext_resources as $ext) {
        $html .= '<li style="margin-bottom: 10px;">';
        $html .= '<div><strong>' . htmlspecialchars($ext['title']) . '</strong></div>';
        $html .= '<div><a href="' . htmlspecialchars($ext['url']) . '" style="color: #059669; text-decoration: none;">' . htmlspecialchars($ext['url']) . '</a></div>';
        $html .= '</li>';
    }
    $html .= '</ul>';
}

$mpdf->WriteHTML($html);
$filename = 'Pathfinder_' . preg_replace('/[^A-Za-z0-9_-]/', '', $slug) . '.pdf';
$mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
