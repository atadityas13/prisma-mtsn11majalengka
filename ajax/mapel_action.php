<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::restrictTo('admin');

$db = new Database();
$action = $_POST['action'] ?? 'add';

if ($action === 'add') {
    $kode = $_POST['kode_mapel'] ?? '';
    $nama = $_POST['nama_mapel'] ?? '';

    try {
        $db->query("INSERT INTO mapel (kode_mapel, nama_mapel) VALUES (:kode, :nama)");
        $db->bind(':kode', $kode);
        $db->bind(':nama', $nama);
        $db->execute();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Kode Mapel sudah ada atau error: ' . $e->getMessage()]);
    }

} elseif ($action === 'update') {
    $id = $_POST['id'] ?? '';
    $kode = $_POST['kode_mapel'] ?? '';
    $nama = $_POST['nama_mapel'] ?? '';

    try {
        $db->query("UPDATE mapel SET kode_mapel = :kode, nama_mapel = :nama WHERE id = :id");
        $db->bind(':kode', $kode);
        $db->bind(':nama', $nama);
        $db->bind(':id', $id);
        $db->execute();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';

    try {
        $db->query("DELETE FROM mapel WHERE id = :id");
        $db->bind(':id', $id);
        $db->execute();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus. Kemungkinan ada data guru/plotting yang terikat.']);
    }
}
