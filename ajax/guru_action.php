<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::restrictTo('admin');

$db = new Database();
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $nip = $_POST['nip'] ?? '';
    $nama = $_POST['nama_lengkap'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';
    $mapel_id = $_POST['mapel_id'] ?? '';
    $password_raw = $_POST['password'] ?? '';
    
    // Default password is DoB from NIP (YYYYMMDD) -> DDMMYYYY
    if (empty($password_raw)) {
        if (strlen($nip) >= 8) {
            $year = substr($nip, 0, 4);
            $month = substr($nip, 4, 2);
            $day = substr($nip, 6, 2);
            $password_raw = $day . $month . $year;
        } else {
            $password_raw = $nip;
        }
    }
    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    try {
        // Start transaction
        // (Database class doesn't have startTransaction explicitly, but we can do it via PDO)
        // For simplicity and matching current Database class, I'll do it sequentially
        
        // 1. Check if user exists
        $db->query("SELECT id FROM users WHERE username = :username");
        $db->bind(':username', $nip);
        if ($db->single()) {
            echo json_encode(['status' => 'error', 'message' => 'NIP sudah digunakan sebagai username!']);
            exit;
        }

        // 2. Create User
        $db->query("INSERT INTO users (username, password, role) VALUES (:username, :password, 'guru')");
        $db->bind(':username', $nip);
        $db->bind(':password', $password);
        $db->execute();
        $user_id = $db->lastInsertId();

        // 3. Create Guru
        $db->query("INSERT INTO guru (user_id, nip, nama_lengkap, jabatan, mapel_id) VALUES (:user_id, :nip, :nama, :jabatan, :mapel_id)");
        $db->bind(':user_id', $user_id);
        $db->bind(':nip', $nip);
        $db->bind(':nama', $nama);
        $db->bind(':jabatan', $jabatan);
        $db->bind(':mapel_id', $mapel_id);
        $db->execute();

        echo json_encode(['status' => 'success', 'message' => 'Guru berhasil ditambahkan']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} elseif ($action === 'update') {
    $id = $_POST['id'] ?? '';
    $nama = $_POST['nama_lengkap'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';
    $mapel_id = $_POST['mapel_id'] ?? '';

    try {
        $db->query("UPDATE guru SET nama_lengkap = :nama, jabatan = :jabatan, mapel_id = :mapel_id WHERE id = :id");
        $db->bind(':nama', $nama);
        $db->bind(':jabatan', $jabatan);
        $db->bind(':mapel_id', $mapel_id);
        $db->bind(':id', $id);
        $db->execute();

        // Update password if provided
        $new_password = $_POST['password'] ?? '';
        if (!empty($new_password)) {
            $db->query("SELECT user_id FROM guru WHERE id = :id");
            $db->bind(':id', $id);
            $res = $db->single();
            if ($res) {
                $hashed_pass = password_hash($new_password, PASSWORD_DEFAULT);
                $db->query("UPDATE users SET password = :pass WHERE id = :user_id");
                $db->bind(':pass', $hashed_pass);
                $db->bind(':user_id', $res['user_id']);
                $db->execute();
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Data guru berhasil diupdate']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';

    try {
        // Get user_id first for thorough cleanup if needed, 
        // though ON DELETE CASCADE handles it.
        $db->query("SELECT user_id FROM guru WHERE id = :id");
        $db->bind(':id', $id);
        $res = $db->single();
        
        if ($res) {
            $db->query("DELETE FROM users WHERE id = :user_id");
            $db->bind(':user_id', $res['user_id']);
            $db->execute();
            echo json_encode(['status' => 'success', 'message' => 'Guru berhasil dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} elseif ($action === 'reset_password_default') {
    $id = $_POST['id'] ?? '';

    try {
        $db->query("SELECT user_id, nip FROM guru WHERE id = :id");
        $db->bind(':id', $id);
        $res = $db->single();
        
        if ($res) {
            $nip = $res['nip'];
            // Logic: YYYYMMDD -> DDMMYYYY
            if (strlen($nip) >= 8) {
                $year = substr($nip, 0, 4);
                $month = substr($nip, 4, 2);
                $day = substr($nip, 6, 2);
                $def_pass = $day . $month . $year;
            } else {
                $def_pass = $nip;
            }

            $hashed = password_hash($def_pass, PASSWORD_DEFAULT);
            $db->query("UPDATE users SET password = :pass WHERE id = :uid");
            $db->bind(':pass', $hashed);
            $db->bind(':uid', $res['user_id']);
            $db->execute();

            echo json_encode(['status' => 'success', 'message' => 'Password berhasil direset ke Tanggal Lahir (DDMMYYYY)']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
