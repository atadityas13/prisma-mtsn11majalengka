<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::restrictTo('admin');

$db = new Database();
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $username = $_POST['username'] ?? '';
    $nama = $_POST['nama_lengkap'] ?? '';
    $password_raw = $_POST['password'] ?? '';
    
    // Default password is username if not provided
    if (empty($password_raw)) {
        $password_raw = $username;
    }
    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    try {
        // 1. Check if user exists
        $db->query("SELECT id FROM users WHERE username = :username");
        $db->bind(':username', $username);
        if ($db->single()) {
            echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan!']);
            exit;
        }

        // 2. Create User
        $db->query("INSERT INTO users (username, password, role) VALUES (:username, :password, 'admin')");
        $db->bind(':username', $username);
        $db->bind(':password', $password);
        $db->execute();
        $user_id = $db->lastInsertId();

        // 3. Create Admin Profile
        $db->query("INSERT INTO admins (user_id, nama_lengkap) VALUES (:user_id, :nama)");
        $db->bind(':user_id', $user_id);
        $db->bind(':nama', $nama);
        $db->execute();

        echo json_encode(['status' => 'success', 'message' => 'Admin berhasil ditambahkan']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} elseif ($action === 'update') {
    $id = $_POST['id'] ?? '';
    $nama = $_POST['nama_lengkap'] ?? '';
    $password_raw = $_POST['password'] ?? '';

    try {
        // Update profile
        $db->query("UPDATE admins SET nama_lengkap = :nama WHERE id = :id");
        $db->bind(':nama', $nama);
        $db->bind(':id', $id);
        $db->execute();

        // Update password if provided
        if (!empty($password_raw)) {
            $db->query("SELECT user_id FROM admins WHERE id = :id");
            $db->bind(':id', $id);
            $res = $db->single();
            
            if ($res) {
                $password = password_hash($password_raw, PASSWORD_DEFAULT);
                $db->query("UPDATE users SET password = :pass WHERE id = :user_id");
                $db->bind(':pass', $password);
                $db->bind(':user_id', $res['user_id']);
                $db->execute();
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Data admin berhasil diupdate']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';

    try {
        // 1. Check if it's the last admin
        $db->query("SELECT COUNT(*) as total FROM admins");
        $count = $db->single();
        if ($count['total'] <= 1) {
            echo json_encode(['status' => 'error', 'message' => 'Tidak dapat menghapus admin terakhir!']);
            exit;
        }

        // 2. Get user_id
        $db->query("SELECT user_id FROM admins WHERE id = :id");
        $db->bind(':id', $id);
        $res = $db->single();
        
        if ($res) {
            // Check if deleting self (optional but recommended)
            // if ($res['user_id'] == $_SESSION['user_id']) { ... }

            $db->query("DELETE FROM users WHERE id = :user_id");
            $db->bind(':user_id', $res['user_id']);
            $db->execute();
            echo json_encode(['status' => 'success', 'message' => 'Admin berhasil dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} elseif ($action === 'clear_logs') {
    try {
        $db->query("TRUNCATE TABLE activity_log");
        $db->execute();
        
        // Log that logs were cleared (optional, but it will be the first and only entry)
        Auth::log("Admin membersihkan seluruh log aktivitas", 'system', $db);
        
        echo json_encode(['status' => 'success', 'message' => 'Seluruh log berhasil dibersihkan']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal membersihkan log: ' . $e->getMessage()]);
    }
}
