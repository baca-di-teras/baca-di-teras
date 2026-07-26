<?php
/**
 * Upload Helper
 *
 * File    : UploadHelper.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 */

class UploadHelper
{
    /**
     * Upload article cover image
     *
     * @param array $fileArray Data from $_FILES['input_name']
     * @param string $uploadDir Relative path from BASE_URL (e.g. '/custom/uploads/articles/')
     * @return string|false Path file yang tersimpan atau false jika gagal
     * @throws Exception Jika validasi gagal
     */
    public static function uploadArticleCover(array $fileArray, string $uploadDir = '/custom/uploads/articles/')
    {
        // Pastikan tidak ada error bawaan
        if ($fileArray['error'] !== UPLOAD_ERR_OK) {
            if ($fileArray['error'] === UPLOAD_ERR_NO_FILE) {
                return false; // Tidak ada file diunggah
            }
            throw new Exception("Terjadi kesalahan saat mengunggah file (Error Code: " . $fileArray['error'] . ")");
        }

        // Validasi Ukuran (Max 2MB)
        $maxSize = 2 * 1024 * 1024; // 2 MB
        if ($fileArray['size'] > $maxSize) {
            throw new Exception("Ukuran file tidak boleh lebih dari 2MB.");
        }

        // Validasi Ekstensi & MIME Type
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileArray['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new Exception("Format file tidak didukung. Harap unggah file JPEG, PNG, WEBP, atau GIF.");
        }

        $extension = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
        if (!$extension) {
            // Coba tebak dari MIME
            $mimeMap = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'image/gif'  => 'gif',
            ];
            $extension = $mimeMap[$mimeType] ?? 'jpg';
        }

        // Generate nama file unik
        $fileName = 'cover_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . strtolower($extension);
        
        // Setup direktori absolut
        $baseDir = defined('ROOT_PATH') ? ROOT_PATH : realpath(__DIR__ . '/../..');
        $targetDir = rtrim($baseDir, '/') . '/' . trim($uploadDir, '/');
        
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new Exception("Gagal membuat direktori upload.");
            }
        }

        $targetPath = $targetDir . '/' . $fileName;

        // Pindahkan file
        if (!move_uploaded_file($fileArray['tmp_name'], $targetPath)) {
            throw new Exception("Gagal menyimpan file ke server.");
        }

        // Return path absolut relatif dari BASE_URL, contoh: /baca-di-teras/custom/uploads/articles/namafile.jpg
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/baca-di-teras';
        return rtrim($baseUrl, '/') . '/' . trim($uploadDir, '/') . '/' . $fileName;
    }
}
