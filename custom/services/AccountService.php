<?php
/**
 * Account Management Service
 *
 * Mengelola data akun admin/user untuk portal Baca Di Teras.
 */

require_once __DIR__ . '/../helpers/Database.php';
require_once __DIR__ . '/ActivityLogService.php';

class AccountService
{
    private Database $db;
    private ActivityLogService $activityLog;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->activityLog = new ActivityLogService();
    }

    /**
     * Mendapatkan semua daftar akun admin/user
     */
    public function getAllAccounts(): array
    {
        $sql = "SELECT id, username, name, role, created_at FROM bdt_admins ORDER BY created_at DESC";
        $result = $this->db->getConnection()->query($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mendapatkan detail akun berdasarkan ID
     */
    public function getAccountById(int $id): ?array
    {
        $sql = "SELECT id, username, name, role, created_at FROM bdt_admins WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $account = $result->fetch_assoc();
        $stmt->close();

        return $account ?: null;
    }

    /**
     * Mengecek apakah username sudah digunakan (kecuali oleh user tertentu)
     */
    public function isUsernameExists(string $username, ?int $exclude_id = null): bool
    {
        if ($exclude_id) {
            $sql = "SELECT id FROM bdt_admins WHERE username = ? AND id != ?";
            $stmt = $this->db->getConnection()->prepare($sql);
            if (!$stmt) return true;
            $stmt->bind_param('si', $username, $exclude_id);
        } else {
            $sql = "SELECT id FROM bdt_admins WHERE username = ?";
            $stmt = $this->db->getConnection()->prepare($sql);
            if (!$stmt) return true;
            $stmt->bind_param('s', $username);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    /**
     * Membuat akun baru
     */
    public function createAccount(string $username, string $password, string $name, string $role): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO bdt_admins (username, password_hash, name, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->getConnection()->prepare($sql);
        
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('ssss', $username, $hash, $name, $role);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            $this->activityLog->log('menambahkan', 'Akun', $username);
        }

        return $success;
    }

    /**
     * Update data akun (dan password opsional)
     */
    public function updateAccount(int $id, string $username, string $name, string $role, ?string $password = null): bool
    {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE bdt_admins SET username = ?, name = ?, role = ?, password_hash = ? WHERE id = ?";
            $stmt = $this->db->getConnection()->prepare($sql);
            if (!$stmt) return false;
            $stmt->bind_param('ssssi', $username, $name, $role, $hash, $id);
        } else {
            $sql = "UPDATE bdt_admins SET username = ?, name = ?, role = ? WHERE id = ?";
            $stmt = $this->db->getConnection()->prepare($sql);
            if (!$stmt) return false;
            $stmt->bind_param('sssi', $username, $name, $role, $id);
        }

        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            $this->activityLog->log('mengubah', 'Akun', $username);
        }

        return $success;
    }

    /**
     * Menghapus akun (mencegah hapus diri sendiri)
     */
    public function deleteAccount(int $id, int $current_user_id): bool
    {
        if ($id === $current_user_id) {
            return false; // Tidak boleh menghapus diri sendiri
        }

        $account = $this->getAccountById($id);
        $name = $account ? $account['name'] : "ID: $id";

        $sql = "DELETE FROM bdt_admins WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        
        if (!$stmt) return false;

        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            try {
                $this->activityLog->log('menghapus', 'Akun', $name);
            } catch (Exception $e) {}
        }

        return $success;
    }
}

