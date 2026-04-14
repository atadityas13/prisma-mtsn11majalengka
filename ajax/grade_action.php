<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::restrictTo('guru');

$db = new Database();
$guru_id = $_SESSION['guru_id'];
$mapel_id = $_SESSION['mapel_id'];
$action = $_POST['action'] ?? '';

if ($action === 'save_score') {
    $siswa_id = $_POST['siswa_id'] ?? '';
    $nilai_array = $_POST['nilai'] ?? []; // aspects mapping [id => value]
    $catatan = $_POST['catatan'] ?? '';

    try {
        foreach ($nilai_array as $aspek_id => $nilai) {
            // Check if exists
            $db->query("SELECT id FROM nilai_praktik WHERE siswa_id = :sid AND guru_id = :gid AND mapel_id = :mid AND aspek_id = :aid");
            $db->bind(':sid', $siswa_id);
            $db->bind(':gid', $guru_id);
            $db->bind(':mid', $mapel_id);
            $db->bind(':aid', $aspek_id);
            $existing = $db->single();

            if ($existing) {
                // Update
                $db->query("UPDATE nilai_praktik SET nilai_angka = :nilai, catatan = :catatan WHERE id = :id");
                $db->bind(':nilai', $nilai);
                $db->bind(':catatan', $catatan);
                $db->bind(':id', $existing['id']);
                $db->execute();
            } else {
                // Insert
                $db->query("INSERT INTO nilai_praktik (siswa_id, guru_id, mapel_id, aspek_id, nilai_angka, catatan) 
                            VALUES (:sid, :gid, :mid, :aid, :nilai, :catatan)");
                $db->bind(':sid', $siswa_id);
                $db->bind(':gid', $guru_id);
                $db->bind(':mid', $mapel_id);
                $db->bind(':aid', $aspek_id);
                $db->bind(':nilai', $nilai);
                $db->bind(':catatan', $catatan);
                $db->execute();
            }
        }
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} elseif ($action === 'reset_score') {
    $siswa_id = $_POST['siswa_id'] ?? '';

    if (empty($siswa_id)) {
        echo json_encode(['status' => 'error', 'message' => 'ID Siswa tidak ditemukan.']);
        exit;
    }

    try {
        $db->query("DELETE FROM nilai_praktik WHERE siswa_id = :sid AND guru_id = :gid AND mapel_id = :mid");
        $db->bind(':sid', $siswa_id);
        $db->bind(':gid', $guru_id);
        $db->bind(':mid', $mapel_id);
        $db->execute();
        
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
