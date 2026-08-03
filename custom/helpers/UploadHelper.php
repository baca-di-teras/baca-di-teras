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

    /**
     * Upload media (image/video) for article content (Quill editor)
     *
     * @param array $fileArray Data from $_FILES['input_name']
     * @param string $type 'image' or 'video'
     * @return string|false Path file yang tersimpan atau false jika gagal
     * @throws Exception Jika validasi gagal
     */
    public static function uploadArticleMedia(array $fileArray, string $type = 'image')
    {
        if ($fileArray['error'] !== UPLOAD_ERR_OK) {
            if ($fileArray['error'] === UPLOAD_ERR_NO_FILE) {
                return false;
            }
            throw new Exception("Terjadi kesalahan saat mengunggah file (Error Code: " . $fileArray['error'] . ")");
        }

        $uploadDir = '/custom/uploads/media/';
        
        if ($type === 'video') {
            $maxSize = 20 * 1024 * 1024; // 20 MB
            $allowedMimeTypes = ['video/mp4', 'video/webm', 'video/ogg'];
            $mimeMap = ['video/mp4' => 'mp4', 'video/webm' => 'webm', 'video/ogg' => 'ogg'];
        } else {
            $maxSize = 2 * 1024 * 1024; // 2 MB
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $mimeMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        }

        if ($fileArray['size'] > $maxSize) {
            $maxMb = $type === 'video' ? '20MB' : '2MB';
            throw new Exception("Ukuran file tidak boleh lebih dari $maxMb.");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileArray['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimeTypes)) {
            $formats = $type === 'video' ? 'MP4, WebM, OGG' : 'JPEG, PNG, WEBP, GIF';
            throw new Exception("Format file tidak didukung. Harap unggah file $formats.");
        }

        $extension = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
        if (!$extension) {
            $extension = $mimeMap[$mimeType] ?? ($type === 'video' ? 'mp4' : 'jpg');
        }

        $fileName = 'media_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . strtolower($extension);
        
        $baseDir = defined('ROOT_PATH') ? ROOT_PATH : realpath(__DIR__ . '/../..');
        $targetDir = rtrim($baseDir, '/') . '/' . trim($uploadDir, '/');
        
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new Exception("Gagal membuat direktori upload.");
            }
        }

        $targetPath = $targetDir . '/' . $fileName;

        if (!move_uploaded_file($fileArray['tmp_name'], $targetPath)) {
            throw new Exception("Gagal menyimpan file ke server.");
        }

        $baseUrl = defined('BASE_URL') ? BASE_URL : '/baca-di-teras';
        return rtrim($baseUrl, '/') . '/' . trim($uploadDir, '/') . '/' . $fileName;
    }

    /**
     * Upload multiple article cover images
     *
     * @param array $fileArrays Data from $_FILES['input_name'] where multiple is enabled
     * @param string $uploadDir Relative path from BASE_URL
     * @return array Array of saved file paths
     * @throws Exception Jika validasi gagal
     */
    public static function uploadMultipleArticleCovers(array $fileArrays, string $uploadDir = '/custom/uploads/articles/'): array
    {
        $uploadedPaths = [];
        $fileCount = count($fileArrays['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($fileArrays['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue; // Skip empty uploads
            }

            // Create a pseudo $_FILES array structure for single file
            $singleFile = [
                'name' => $fileArrays['name'][$i],
                'type' => $fileArrays['type'][$i],
                'tmp_name' => $fileArrays['tmp_name'][$i],
                'error' => $fileArrays['error'][$i],
                'size' => $fileArrays['size'][$i],
            ];

            try {
                $path = self::uploadArticleCover($singleFile, $uploadDir);
                if ($path) {
                    $uploadedPaths[] = $path;
                }
            } catch (Exception $e) {
                throw new Exception("File '" . $fileArrays['name'][$i] . "' gagal diunggah: " . $e->getMessage());
            }
        }

        return $uploadedPaths;
    }
}
