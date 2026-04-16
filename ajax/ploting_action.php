<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::restrictTo('admin');

$db = new Database();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ============================================================
// SAVE PLOT: assign N siswa ke 1 guru secara berurutan
// ============================================================
if ($action === 'save_plot') {
    $guru_id      = (int)($_POST['guru_id'] ?? 0);
    $jumlah_siswa = (int)($_POST['jumlah_siswa'] ?? 0);

    if (!$guru_id || $jumlah_siswa <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap!']);
        exit;
    }

    // Ambil mapel_id dari guru
    $db->query("SELECT mapel_id FROM guru WHERE id = :id");
    $db->bind(':id', $guru_id);
    $guru_row = $db->single();

    if (!$guru_row) {
        echo json_encode(['status' => 'error', 'message' => 'Guru tidak ditemukan!']);
        exit;
    }
    $mapel_id = $guru_row['mapel_id'];

    // Ambil N siswa yang belum punya penguji untuk mapel ini, urut nomor_peserta
    $db->query("SELECT s.id FROM siswa s
                WHERE s.id NOT IN (
                    SELECT ps.siswa_id FROM ploting_siswa ps
                    JOIN ploting_penguji pp ON ps.ploting_id = pp.id
                    WHERE pp.mapel_id = :mapel_id
                )
                ORDER BY s.nomor_peserta ASC
                LIMIT :jumlah");
    $db->bind(':mapel_id', $mapel_id);
    $db->bind(':jumlah', $jumlah_siswa, PDO::PARAM_INT);
    $siswa_list = $db->resultSet();

    if (count($siswa_list) === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada siswa yang tersedia (semua sudah memiliki penguji)!']);
        exit;
    }

    try {
        // Cek apakah sudah ada ploting untuk guru+mapel ini
        $db->query("SELECT id FROM ploting_penguji WHERE guru_id = :guru_id AND mapel_id = :mapel_id");
        $db->bind(':guru_id', $guru_id);
        $db->bind(':mapel_id', $mapel_id);
        $existing = $db->single();

        if ($existing) {
            $ploting_id = $existing['id'];
        } else {
            // Buat ploting baru
            $db->query("INSERT INTO ploting_penguji (guru_id, mapel_id) VALUES (:guru_id, :mapel_id)");
            $db->bind(':guru_id', $guru_id);
            $db->bind(':mapel_id', $mapel_id);
            $db->execute();
            $ploting_id = $db->lastInsertId();
        }

        // Insert siswa ke ploting_siswa
        $inserted = 0;
        foreach ($siswa_list as $s) {
            $db->query("INSERT IGNORE INTO ploting_siswa (ploting_id, siswa_id) VALUES (:ploting_id, :siswa_id)");
            $db->bind(':ploting_id', $ploting_id);
            $db->bind(':siswa_id', $s['id']);
            $db->execute();
            $inserted++;
        }

        Auth::log("Admin memploting $inserted siswa ke guru ID: $guru_id", 'system', $db);
        echo json_encode([
            'status'   => 'success',
            'inserted' => $inserted,
            'message'  => "$inserted siswa berhasil di-assign ke guru ini."
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

// ============================================================
// GET SISWA PLOT: ambil daftar siswa untuk 1 ploting_id
// ============================================================
} elseif ($action === 'get_siswa_plot') {
    $ploting_id = (int)($_GET['ploting_id'] ?? 0);

    if (!$ploting_id) {
        echo json_encode(['status' => 'error', 'message' => 'Ploting ID tidak valid!']);
        exit;
    }

    $db->query("SELECT s.id, s.nama_lengkap, s.nomor_peserta, s.kelas, s.nisn, ps.id as ploting_siswa_id
                FROM ploting_siswa ps
                JOIN siswa s ON ps.siswa_id = s.id
                WHERE ps.ploting_id = :ploting_id
                ORDER BY s.nomor_peserta ASC");
    $db->bind(':ploting_id', $ploting_id);
    $siswas = $db->resultSet();

    echo json_encode(['status' => 'success', 'siswa' => $siswas]);

// ============================================================
// GET SISA SISWA: hitung siswa yang belum punya penguji
// ============================================================
} elseif ($action === 'get_sisa') {
    $guru_id = (int)($_GET['guru_id'] ?? 0);

    if (!$guru_id) {
        echo json_encode(['status' => 'error', 'sisa' => 0]);
        exit;
    }

    $db->query("SELECT mapel_id FROM guru WHERE id = :id");
    $db->bind(':id', $guru_id);
    $g = $db->single();

    if (!$g) {
        echo json_encode(['status' => 'error', 'sisa' => 0]);
        exit;
    }

    $db->query("SELECT COUNT(*) as total FROM siswa s
                WHERE s.id NOT IN (
                    SELECT ps.siswa_id FROM ploting_siswa ps
                    JOIN ploting_penguji pp ON ps.ploting_id = pp.id
                    WHERE pp.mapel_id = :mapel_id
                )");
    $db->bind(':mapel_id', $g['mapel_id']);
    $res = $db->single();

    // Hitung juga sudah berapa siswa di guru ini
    $db->query("SELECT COUNT(*) as sudah FROM ploting_siswa ps
                JOIN ploting_penguji pp ON ps.ploting_id = pp.id
                WHERE pp.guru_id = :guru_id AND pp.mapel_id = :mapel_id");
    $db->bind(':guru_id', $guru_id);
    $db->bind(':mapel_id', $g['mapel_id']);
    $sudah = $db->single();

    echo json_encode([
        'status'         => 'success',
        'sisa'           => (int)$res['total'],
        'sudah_dimiliki' => (int)$sudah['sudah'],
        'mapel_id'       => $g['mapel_id']
    ]);

// ============================================================
// RESET SISWA: keluarkan 1 siswa dari ploting
// ============================================================
} elseif ($action === 'reset_siswa') {
    $ploting_siswa_id = (int)($_POST['ploting_siswa_id'] ?? 0);

    if (!$ploting_siswa_id) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid!']);
        exit;
    }

    $db->query("DELETE FROM ploting_siswa WHERE id = :id");
    $db->bind(':id', $ploting_siswa_id);
    $db->execute();
    Auth::log("Admin mereset ploting siswa ID: $ploting_siswa_id", 'system', $db);

    echo json_encode(['status' => 'success']);

// ============================================================
// DELETE: hapus seluruh ploting 1 guru (cascade ke ploting_siswa)
// ============================================================
} elseif ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);

    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid!']);
        exit;
    }

    $db->query("DELETE FROM ploting_penguji WHERE id = :id");
    $db->bind(':id', $id);
    $db->execute();
    Auth::log("Admin menghapus seluruh ploting ID: $id", 'system', $db);

    echo json_encode(['status' => 'success']);
}
