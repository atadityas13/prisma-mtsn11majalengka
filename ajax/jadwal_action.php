<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';
Auth::restrictTo('admin');

$db     = new Database();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ── SAVE (add or update) ──────────────────────────────────────────────────
if ($action === 'save') {
    $ploting_id  = (int)($_POST['ploting_id']  ?? 0);
    $tanggal     = $_POST['tanggal']     ?? '';
    $jam_mulai   = $_POST['jam_mulai']   ?? '';
    $jam_selesai = $_POST['jam_selesai'] ?? '';
    $ruangan     = trim($_POST['ruangan']     ?? '');
    $keterangan  = trim($_POST['keterangan']  ?? '');

    if (!$ploting_id || !$tanggal || !$jam_mulai || !$jam_selesai || !$ruangan) {
        echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi!']);
        exit;
    }
    if ($jam_selesai <= $jam_mulai) {
        echo json_encode(['status' => 'error', 'message' => 'Jam selesai harus setelah jam mulai!']);
        exit;
    }

    try {
        // Upsert: insert or update on duplicate ploting_id
        $db->query("INSERT INTO jadwal_praktik (ploting_id, tanggal, jam_mulai, jam_selesai, ruangan, keterangan)
                    VALUES (:pid, :tgl, :jm, :js, :rg, :ket)
                    ON DUPLICATE KEY UPDATE
                        tanggal     = VALUES(tanggal),
                        jam_mulai   = VALUES(jam_mulai),
                        jam_selesai = VALUES(jam_selesai),
                        ruangan     = VALUES(ruangan),
                        keterangan  = VALUES(keterangan)");
        $db->bind(':pid', $ploting_id);
        $db->bind(':tgl', $tanggal);
        $db->bind(':jm',  $jam_mulai);
        $db->bind(':js',  $jam_selesai);
        $db->bind(':rg',  $ruangan);
        $db->bind(':ket', $keterangan);
        $db->execute();

        echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil disimpan.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

// ── DELETE ────────────────────────────────────────────────────────────────
} elseif ($action === 'delete') {
    $ploting_id = (int)($_POST['ploting_id'] ?? 0);
    if (!$ploting_id) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid!']);
        exit;
    }
    try {
        $db->query("DELETE FROM jadwal_praktik WHERE ploting_id = :pid");
        $db->bind(':pid', $ploting_id);
        $db->execute();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

// ── GET (fetch existing schedule for a ploting) ───────────────────────────
} elseif ($action === 'get') {
    $ploting_id = (int)($_GET['ploting_id'] ?? 0);
    $db->query("SELECT * FROM jadwal_praktik WHERE ploting_id = :pid");
    $db->bind(':pid', $ploting_id);
    $row = $db->single();
    echo json_encode(['status' => 'success', 'data' => $row ?: null]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Action tidak dikenali.']);
}
