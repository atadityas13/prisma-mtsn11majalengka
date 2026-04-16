<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

Auth::restrictTo('admin');

$db = new Database();
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $nisn = $_POST['nisn'] ?? '';
    $nama = $_POST['nama_lengkap'] ?? '';
    $nopes = $_POST['nomor_peserta'] ?? '';
    $jk = $_POST['jenis_kelamin'] ?? 'L';
    $kelas = $_POST['kelas'] ?? '';
    $tahun = DEFAULT_YEAR;
    
    $password_raw = $_POST['password'] ?? '';
    if (empty($password_raw)) {
        $password_raw = $nisn;
    }
    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    try {
        // Create User
        $db->query("INSERT INTO users (username, password, role) VALUES (:username, :password, 'siswa')");
        $db->bind(':username', $nisn);
        $db->bind(':password', $password);
        $db->execute();
        $user_id = $db->lastInsertId();

        // Create Siswa
        $db->query("INSERT INTO siswa (user_id, nisn, nama_lengkap, nomor_peserta, jenis_kelamin, kelas, tahun_ajaran) 
                    VALUES (:user_id, :nisn, :nama, :nopes, :jk, :kelas, :tahun)");
        $db->bind(':user_id', $user_id);
        $db->bind(':nisn', $nisn);
        $db->bind(':nama', $nama);
        $db->bind(':nopes', $nopes);
        $db->bind(':jk', $jk);
        $db->bind(':kelas', $kelas);
        $db->bind(':tahun', $tahun);
        $db->execute();

        Auth::log("Admin menambahkan siswa: $nama (NISN: $nisn)", 'system', $db);
        echo json_encode(['status' => 'success', 'message' => 'Siswa berhasil ditambahkan']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'NISN sudah ada atau data tidak valid.']);
    }

} elseif ($action === 'update') {
    $id = $_POST['id'] ?? '';
    $nama = $_POST['nama_lengkap'] ?? '';
    $nopes = $_POST['nomor_peserta'] ?? '';
    $jk = $_POST['jenis_kelamin'] ?? 'L';
    $kelas = $_POST['kelas'] ?? '';
    $new_password = $_POST['password'] ?? '';

    try {
        // Update Siswa Data
        $db->query("UPDATE siswa SET nama_lengkap = :nama, nomor_peserta = :nopes, jenis_kelamin = :jk, kelas = :kelas WHERE id = :id");
        $db->bind(':nama', $nama);
        $db->bind(':nopes', $nopes);
        $db->bind(':jk', $jk);
        $db->bind(':kelas', $kelas);
        $db->bind(':id', $id);
        $db->execute();

        // Update Password if provided
        if (!empty($new_password)) {
            $db->query("SELECT user_id FROM siswa WHERE id = :id");
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

        Auth::log("Admin mengupdate data siswa ID: $id ($nama)", 'system', $db);
        echo json_encode(['status' => 'success', 'message' => 'Data siswa berhasil diupdate']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} elseif ($action === 'import') {
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] != 0) {
        echo json_encode(['status' => 'error', 'message' => 'File tidak ditemukan']);
        exit;
    }

    $file = $_FILES['excel_file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();
        $count = 0;

        // Header: NISN, Nama Lengkap, No Peserta, JK (L/P), Kelas
        // Start from index 1 (skip header)
        for ($i = 1; $i < count($data); $i++) {
            $nisn = $data[$i][0] ?? '';
            $nama = $data[$i][1] ?? '';
            $nopes = $data[$i][2] ?? '';
            $jk = strtoupper($data[$i][3] ?? 'L');
            $kelas = $data[$i][4] ?? '';

            if (empty($nisn) || empty($nama)) continue;

            // Check if NISN already ada di tabel siswa
            $db->query("SELECT s.id as siswa_id, u.id as user_id
                        FROM siswa s
                        JOIN users u ON s.user_id = u.id
                        WHERE s.nisn = :nisn");
            $db->bind(':nisn', $nisn);
            $existing = $db->single();

            if ($existing) {
                // Update existing siswa data
                $db->query("UPDATE siswa SET nama_lengkap = :nama, nomor_peserta = :nopes,
                            jenis_kelamin = :jk, kelas = :kelas, tahun_ajaran = :tahun
                            WHERE id = :id");
                $db->bind(':nama', $nama);
                $db->bind(':nopes', $nopes);
                $db->bind(':jk', $jk);
                $db->bind(':kelas', $kelas);
                $db->bind(':tahun', DEFAULT_YEAR);
                $db->bind(':id', $existing['siswa_id']);
                $db->execute();
                $count++;
                continue;
            }

            // If user exists but siswa belum dibuat, buat siswa baru
            $db->query("SELECT id FROM users WHERE username = :username");
            $db->bind(':username', $nisn);
            $user = $db->single();

            if ($user) {
                $user_id = $user['id'];
            } else {
                // Create User
                $password = password_hash($nisn, PASSWORD_DEFAULT);
                $db->query("INSERT INTO users (username, password, role) VALUES (:username, :password, 'siswa')");
                $db->bind(':username', $nisn);
                $db->bind(':password', $password);
                $db->execute();
                $user_id = $db->lastInsertId();
            }

            // Create Siswa
            $db->query("INSERT INTO siswa (user_id, nisn, nama_lengkap, nomor_peserta, jenis_kelamin, kelas, tahun_ajaran) 
                        VALUES (:user_id, :nisn, :nama, :nopes, :jk, :kelas, :tahun)");
            $db->bind(':user_id', $user_id);
            $db->bind(':nisn', $nisn);
            $db->bind(':nama', $nama);
            $db->bind(':nopes', $nopes);
            $db->bind(':jk', $jk);
            $db->bind(':kelas', $kelas);
            $db->bind(':tahun', DEFAULT_YEAR);
            $db->execute();
            $count++;
        }

        Auth::log("Admin mengimport $count data siswa", 'system', $db);
        echo json_encode(['status' => 'success', 'message' => "$count data siswa berhasil diimport"]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    try {
        $db->query("SELECT user_id FROM siswa WHERE id = :id");
        $db->bind(':id', $id);
        $res = $db->single();
        if ($res) {
            $db->query("DELETE FROM users WHERE id = :user_id");
            $db->bind(':user_id', $res['user_id']);
            $db->execute();
            Auth::log("Admin menghapus siswa ID: $id", 'system', $db);
            echo json_encode(['status' => 'success']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
