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
    $nama = $_POST['nama_aspek'] ?? '';
    $bobot = $_POST['bobot_nilai'] ?? 1;
    $guru_id = ($role === 'guru') ? $_SESSION['guru_id'] : null;

    if (empty($mapel_id) || empty($nama)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    try {
        $db->query("INSERT INTO aspek_penilaian (guru_id, mapel_id, nama_aspek, bobot_nilai) VALUES (:guru_id, :mapel_id, :nama, :bobot)");
        $db->bind(':guru_id', $guru_id);
        $db->bind(':mapel_id', $mapel_id);
        $db->bind(':nama', $nama);
        $db->bind(':bobot', $bobot);
        $db->execute();
        Auth::log("Menambahkan aspek penilaian: $nama", 'assessment', $db);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    try {
        if ($role === 'guru') {
            // For Guru, they can only delete aspects in their current mapel (which are shared anyway)
            // To prevent accidental cross-mapel deletion
            $db->query("DELETE FROM aspek_penilaian WHERE id = :id AND mapel_id = :mapel_id");
            $db->bind(':id', $id);
            $db->bind(':mapel_id', $mapel_id);
        } else {
            // Admin can delete any
            $db->query("DELETE FROM aspek_penilaian WHERE id = :id");
            $db->bind(':id', $id);
        }
        $db->execute();
        Auth::log("Menghapus aspek penilaian ID: $id", 'assessment', $db);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
