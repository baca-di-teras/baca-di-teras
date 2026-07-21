<?php
/**
 * Database — Mysqli Connection Singleton & Query Helpers
 *
 * File    : Database.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Kelas ini bertanggung jawab atas:
 *   1. Singleton koneksi mysqli
 *   2. Prepared statement wrappers
 *   3. Helper query (fetchAll, fetchOne, fetchScalar, execute)
 *   4. Error handling terpusat
 *
 * ATURAN:
 *   - Kelas ini HANYA digunakan oleh Service Layer.
 *   - Jangan pernah memanggil Database::getInstance() dari file halaman.
 *   - Seluruh query SQL berada di Service Layer, bukan di sini.
 *
 * Usage (di Service Layer):
 *   $db   = Database::getInstance();
 *   $rows = $db->fetchAll('SELECT * FROM bdt_library WHERE status = ?', 's', ['aktif']);
 */

// Muat konfigurasi database jika belum dimuat
if (!defined('DB_HOST')) {
    $configPath = defined('ROOT_PATH')
        ? ROOT_PATH . '/custom/config/database.php'
        : __DIR__ . '/../config/database.php';
    require_once $configPath;
}

class Database
{
    /** @var Database|null Instance tunggal (Singleton) */
    private static ?Database $instance = null;

    /** @var mysqli Koneksi aktif */
    private mysqli $connection;

    // ── Constructor ───────────────────────────────────────────

    /**
     * Constructor private — cegah instansiasi langsung.
     * Gunakan Database::getInstance().
     */
    private function __construct()
    {
        // Matikan error reporting mysqli agar ditangani secara manual
        mysqli_report(MYSQLI_REPORT_OFF);

        $this->connection = new mysqli(
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME,
            DB_PORT
        );

        if ($this->connection->connect_errno) {
            $this->handleError(
                'Koneksi database gagal',
                $this->connection->connect_error,
                $this->connection->connect_errno
            );
        }

        // Set charset agar mendukung karakter Unicode penuh
        if (!$this->connection->set_charset(DB_CHARSET)) {
            $this->handleError('Gagal set charset', $this->connection->error);
        }
    }

    /** Cegah clone agar singleton tetap terjaga */
    private function __clone() {}

    // ── Singleton ─────────────────────────────────────────────

    /**
     * Dapatkan instance singleton Database.
     *
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Dapatkan objek mysqli mentah (untuk kasus khusus di Service Layer).
     *
     * @return mysqli
     */
    public function getConnection(): mysqli
    {
        return $this->connection;
    }

    // ── Query Helpers ─────────────────────────────────────────

    /**
     * Ambil semua baris dari sebuah query.
     *
     * @param  string $sql     Query SQL dengan placeholder ?
     * @param  string $types   String tipe parameter: 's'=string, 'i'=int, 'd'=double, 'b'=blob
     * @param  array  $params  Array nilai parameter
     * @return array           Array of associative arrays
     *
     * Contoh:
     *   $rows = $db->fetchAll(
     *       'SELECT * FROM bdt_library WHERE status = ? ORDER BY sort_order',
     *       's',
     *       ['aktif']
     *   );
     */
    public function fetchAll(string $sql, string $types = '', array $params = []): array
    {
        $stmt   = $this->prepare($sql);
        $result = $this->executeStatement($stmt, $types, $params);
        $rows   = $result->fetch_all(MYSQLI_ASSOC);

        $result->free();
        $stmt->close();

        return $rows ?: [];
    }

    /**
     * Ambil satu baris dari sebuah query.
     *
     * @param  string     $sql
     * @param  string     $types
     * @param  array      $params
     * @return array|null  Associative array baris pertama, atau null jika kosong
     *
     * Contoh:
     *   $library = $db->fetchOne(
     *       'SELECT * FROM bdt_library WHERE slug = ? LIMIT 1',
     *       's',
     *       ['perpustakaan-utama']
     *   );
     */
    public function fetchOne(string $sql, string $types = '', array $params = []): ?array
    {
        $stmt   = $this->prepare($sql);
        $result = $this->executeStatement($stmt, $types, $params);
        $row    = $result->fetch_assoc();

        $result->free();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Ambil nilai tunggal dari sebuah query (scalar).
     * Berguna untuk COUNT, SUM, MAX, atau kolom tunggal.
     *
     * @param  string $sql
     * @param  string $types
     * @param  array  $params
     * @return mixed  Nilai kolom pertama baris pertama, atau null jika kosong
     *
     * Contoh:
     *   $total = $db->fetchScalar('SELECT COUNT(*) FROM bdt_article WHERE status = ?', 's', ['published']);
     */
    public function fetchScalar(string $sql, string $types = '', array $params = []): mixed
    {
        $stmt   = $this->prepare($sql);
        $result = $this->executeStatement($stmt, $types, $params);
        $row    = $result->fetch_row();

        $result->free();
        $stmt->close();

        return $row ? $row[0] : null;
    }

    /**
     * Jalankan query INSERT, UPDATE, atau DELETE.
     *
     * @param  string $sql
     * @param  string $types
     * @param  array  $params
     * @return int    Jumlah baris yang terpengaruh (affected_rows)
     *
     * Contoh:
     *   $affected = $db->execute(
     *       'UPDATE bdt_library SET total_koleksi = ? WHERE library_id = ?',
     *       'ii',
     *       [3500, 1]
     *   );
     */
    public function execute(string $sql, string $types = '', array $params = []): int
    {
        $stmt = $this->prepare($sql);

        if (!empty($params) && $types !== '') {
            if (!$stmt->bind_param($types, ...$params)) {
                $this->handleError('bind_param gagal', $stmt->error);
            }
        }

        if (!$stmt->execute()) {
            $this->handleError('execute gagal', $stmt->error, $stmt->errno);
        }

        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected;
    }

    /**
     * Dapatkan ID auto-increment dari INSERT terakhir.
     *
     * @return int
     */
    public function lastInsertId(): int
    {
        return (int) $this->connection->insert_id;
    }

    // ── Transaksi ────────────────────────────────────────────

    /**
     * Mulai transaksi database.
     */
    public function beginTransaction(): void
    {
        $this->connection->autocommit(false);
        $this->connection->begin_transaction();
    }

    /**
     * Commit transaksi — simpan semua perubahan.
     */
    public function commit(): void
    {
        $this->connection->commit();
        $this->connection->autocommit(true);
    }

    /**
     * Rollback transaksi — batalkan semua perubahan.
     */
    public function rollback(): void
    {
        $this->connection->rollback();
        $this->connection->autocommit(true);
    }

    // ── Internal ──────────────────────────────────────────────

    /**
     * Prepare statement dan tangani error.
     *
     * @param  string         $sql
     * @return mysqli_stmt
     */
    private function prepare(string $sql): mysqli_stmt
    {
        $stmt = $this->connection->prepare($sql);

        if ($stmt === false) {
            $this->handleError(
                'Prepare statement gagal',
                $this->connection->error,
                $this->connection->errno
            );
        }

        return $stmt;
    }

    /**
     * Bind parameter ke statement, execute, dan kembalikan result set.
     * Khusus untuk query SELECT (yang menghasilkan result set).
     *
     * @param  mysqli_stmt    $stmt
     * @param  string         $types
     * @param  array          $params
     * @return mysqli_result
     */
    private function executeStatement(mysqli_stmt $stmt, string $types, array $params): mysqli_result
    {
        if (!empty($params) && $types !== '') {
            if (!$stmt->bind_param($types, ...$params)) {
                $this->handleError('bind_param gagal', $stmt->error);
            }
        }

        if (!$stmt->execute()) {
            $this->handleError('execute gagal', $stmt->error, $stmt->errno);
        }

        $result = $stmt->get_result();

        if ($result === false) {
            $this->handleError('get_result gagal', $stmt->error);
        }

        return $result;
    }

    /**
     * Tangani error database secara terpusat.
     * - Mode development : lempar exception dengan pesan detail
     * - Mode production  : log error & tampilkan pesan generik
     *
     * @param  string $context  Deskripsi konteks error
     * @param  string $message  Pesan error dari mysqli
     * @param  int    $code     Error code (opsional)
     * @throws RuntimeException
     */
    private function handleError(string $context, string $message = '', int $code = 0): never
    {
        $fullMessage = "[BDT Database] {$context}: {$message} (Code: {$code})";

        // Selalu log ke error_log
        error_log($fullMessage);

        if (defined('DB_DEBUG') && DB_DEBUG === true) {
            // Development: tampilkan detail error
            throw new RuntimeException($fullMessage, $code);
        }

        // Production: pesan generik tanpa expose detail
        throw new RuntimeException('Terjadi kesalahan pada layanan database. Silakan coba lagi.', $code);
    }
}
