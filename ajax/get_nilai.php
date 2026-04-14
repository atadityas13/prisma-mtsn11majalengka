<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::restrictTo('guru');

$db = new Database();
$guru_id = $_SESSION['guru_id'];
$mapel_id = $_SESSION['mapel_id'];
$siswa_id = $_GET['siswa_id'] ?? '';

try {
    $db->query("SELECT aspek_id, nilai_angka, catatan FROM nilai_praktik 
                WHERE siswa_id = :sid AND guru_id = :gid AND mapel_id = :mid");
    $db->bind(':sid', $siswa_id);
    $db->bind(':gid', $guru_id);
    $db->bind(':mid', $mapel_id);
    $scores = $db->resultSet();

    $catatan = '';
    if (count($scores) > 0) {
        $catatan = $scores[0]['catatan'];
    }

    echo json_encode(['status' => 'success', 'scores' => $scores, 'catatan' => $catatan]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
