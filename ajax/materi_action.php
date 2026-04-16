<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';

// Allow both guru and admin
if (!Auth::isLoggedIn() || !in_array($_SESSION['role'], ['admin', 'guru'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$db = new Database();
$action = $_POST['action'] ?? '';
$role = $_SESSION['role'];

// For gurus, mapel_id is from session. For admins, it's from POST.
$mapel_id = ($role === 'guru') ? $_SESSION['mapel_id'] : ($_POST['mapel_id'] ?? '');

if ($action === 'add') {
    $nama = $_POST['nama_materi'] ?? '';

    if (empty($mapel_id) || empty($nama)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    try {
        $db->query("INSERT INTO materi_penilaian (mapel_id, nama_materi) VALUES (:mapel_id, :nama)");
        $db->bind(':mapel_id', $mapel_id);
        $db->bind(':nama', $nama);
        $db->execute();
        
        Auth::log("Menambahkan materi penilaian: $nama", 'assessment', $db);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    try {
        if ($role === 'guru') {
            $db->query("DELETE FROM materi_penilaian WHERE id = :id AND mapel_id = :mapel_id");
            $db->bind(':id', $id);
            $db->bind(':mapel_id', $mapel_id);
        } else {
            $db->query("DELETE FROM materi_penilaian WHERE id = :id");
            $db->bind(':id', $id);
        }
        $db->execute();
        
        Auth::log("Menghapus materi penilaian ID: $id", 'assessment', $db);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} elseif ($action === 'edit') {
    $id = $_POST['id'] ?? '';
    $nama = $_POST['nama_materi'] ?? '';

    if (empty($id) || empty($nama)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        exit;
    }

    try {
        if ($role === 'guru') {
            $db->query("UPDATE materi_penilaian SET nama_materi = :nama WHERE id = :id AND mapel_id = :mapel_id");
            $db->bind(':id', $id);
            $db->bind(':nama', $nama);
            $db->bind(':mapel_id', $mapel_id);
        } else {
            $db->query("UPDATE materi_penilaian SET nama_materi = :nama WHERE id = :id");
            $db->bind(':id', $id);
            $db->bind(':nama', $nama);
        }
        $db->execute();
        
        Auth::log("Mengubah materi penilaian ID: $id menjadi $nama", 'assessment', $db);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
