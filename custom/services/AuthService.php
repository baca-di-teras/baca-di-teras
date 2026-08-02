<?php
/**
 * Authentication Service
 *
 * File    : AuthService.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Mengelola autentikasi login admin untuk modul custom.
 */

require_once __DIR__ . '/../helpers/Database.php';

class AuthService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Memulai session jika belum dimulai
     */
    private function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Melakukan login admin
     * 
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function login(string $username, string $password): bool
    {
        $this->initSession();

        // Ambil data admin dari database
        $sql = "SELECT * FROM bdt_admins WHERE username = ? LIMIT 1";
        $stmt = $this->db->getConnection()->prepare($sql);
        
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();

        // Verifikasi password
        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Set data session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_language'] = $admin['language'] ?? 'id';
            $_SESSION['admin_theme'] = $admin['theme'] ?? 'light';
            $_SESSION['admin_last_activity'] = time(); // Waktu login awal
            return true;
        }

        return false;
    }

    /**
     * Mengecek apakah admin sedang login
     * 
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        $this->initSession();
        
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            // Cek expiration (3 jam = 10800 detik)
            if (isset($_SESSION['admin_last_activity']) && (time() - $_SESSION['admin_last_activity'] > 10800)) {
                $this->logout();
                return false;
            }
            // Perbarui waktu aktivitas terakhir
            $_SESSION['admin_last_activity'] = time();
            return true;
        }
        
        return false;
    }

    /**
     * Logout admin dan hapus session
     */
    public function logout(): void
    {
        $this->initSession();
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_role']);
        unset($_SESSION['admin_language']);
        unset($_SESSION['admin_theme']);
        unset($_SESSION['admin_last_activity']);
        session_destroy();
    }

    /**
     * Redirect ke halaman login jika belum login
     */
    public function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            $base_url = defined('BASE_URL') ? BASE_URL : '/baca-di-teras';
            header("Location: {$base_url}/portal-admin/login");
            exit;
        }
    }

    /**
     * Mengecek apakah admin memiliki salah satu role yang diizinkan
     * 
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles): bool
    {
        $this->initSession();
        if (!$this->isLoggedIn() || !isset($_SESSION['admin_role'])) {
            return false;
        }

        $current_role = $_SESSION['admin_role'];

        if (is_array($roles)) {
            return in_array($current_role, $roles);
        }

        return $current_role === $roles;
    }

    /**
     * Redirect ke halaman dashboard jika tidak memiliki role yang sesuai
     * 
     * @param string|array $roles
     */
    public function requireRole($roles): void
    {
        $this->requireLogin();

        if (!$this->hasRole($roles)) {
            $base_url = defined('BASE_URL') ? BASE_URL : '/baca-di-teras';
            header("Location: {$base_url}/portal-admin");
            exit;
        }
    }

    /**
     * Redirect ke dashboard jika sudah login
     */
    public function redirectIfLoggedIn(): void
    {
        if ($this->isLoggedIn()) {
            $base_url = defined('BASE_URL') ? BASE_URL : '/baca-di-teras';
            header("Location: {$base_url}/portal-admin");
            exit;
        }
    }
}

