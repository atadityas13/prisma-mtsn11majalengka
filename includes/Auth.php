<?php
/**
 * Auth Class
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Database.php';

class Auth {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Login function
    public function login($username, $password) {
        $this->db->query("SELECT * FROM users WHERE username = :username");
        $this->db->bind(':username', $username);
        $user = $this->db->single();

        if ($user && password_verify($password, $user['password'])) {
            // Setup Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Get profile info based on role
            $profile = $this->getProfile($user['id'], $user['role']);
            $_SESSION['nama_lengkap'] = $profile['nama_lengkap'] ?? $user['username'];
            if ($user['role'] === 'guru') {
                $_SESSION['guru_id'] = $profile['id'];
                $_SESSION['mapel_id'] = $profile['mapel_id'];
            } elseif ($user['role'] === 'siswa') {
                $_SESSION['siswa_id'] = $profile['id'];
                $_SESSION['kelas'] = $profile['kelas'];
            }

            $this->logActivity("User logged in");
            return true;
        }
        return false;
    }

    // Get profile details
    private function getProfile($user_id, $role) {
        if ($role === 'admin') {
            $this->db->query("SELECT * FROM admins WHERE user_id = :user_id");
        } elseif ($role === 'guru') {
            $this->db->query("SELECT * FROM guru WHERE user_id = :user_id");
        } elseif ($role === 'siswa') {
            $this->db->query("SELECT * FROM siswa WHERE user_id = :user_id");
        } else {
            return null;
        }
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Check role and redirect if unauthorized
    public static function restrictTo($roles) {
        if (!self::isLoggedIn()) {
            header("Location: " . base_url("login.php"));
            exit;
        }
        if (!is_array($roles)) $roles = [$roles];
        if (!in_array($_SESSION['role'], $roles)) {
            echo "Akses Ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.";
            exit;
        }
    }

    // Logout
    public function logout() {
        $this->logActivity("User logged out");
        session_destroy();
        header("Location: " . base_url("login.php"));
        exit;
    }

    // Log activity (Instance method)
    public function logActivity($action, $category = 'system') {
        self::log($action, $category, $this->db);
    }

    // Static log helper
    public static function log($action, $category = 'system', $db = null) {
        if (!isset($_SESSION['user_id'])) return;
        
        if (!$db) $db = new Database();
        
        $db->query("INSERT INTO activity_log (user_id, action, category, ip_address) VALUES (:user_id, :action, :category, :ip_address)");
        $db->bind(':user_id', $_SESSION['user_id']);
        $db->bind(':action', $action);
        $db->bind(':category', $category);
        $db->bind(':ip_address', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $db->execute();
    }
}
