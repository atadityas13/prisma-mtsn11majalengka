<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::restrictTo('admin');

$db = new Database();
$action = $_POST['action'] ?? '';

if ($action === 'save_plot') {
    $mapel_id = $_POST['mapel_id'] ?? '';
    $kelas = $_POST['kelas'] ?? '';
    $metode = $_POST['metode'] ?? '1';
    
    // Fetch students in this class ordered by Name or NISN
    $db->query("SELECT id FROM siswa WHERE kelas = :kelas ORDER BY nama_lengkap ASC");
    $db->bind(':kelas', $kelas);
    $students = $db->resultSet();
    $total_students = count($students);

    if ($total_students === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada siswa di kelas ini!']);
        exit;
    }

    try {
        if ($metode === '1') {
            // Whole class to 1 guru
            $guru_id = $_POST['guru_id_1'] ?? '';
            $db->query("INSERT INTO ploting_penguji (guru_id, mapel_id, kelas) VALUES (:guru_id, :mapel_id, :kelas)");
            $db->bind(':guru_id', $guru_id);
            $db->bind(':mapel_id', $mapel_id);
            $db->bind(':kelas', $kelas);
            $db->execute();
        } else {
            // Split into two
            $guru_id_1 = $_POST['guru_id_1'] ?? '';
            $guru_id_2 = $_POST['guru_id_2'] ?? '';

            if (empty($guru_id_2)) {
                echo json_encode(['status' => 'error', 'message' => 'Guru kedua harus dipilih!']);
                exit;
            }

            // Part 1: Students 0-15 (1-16)
            $start1 = $students[0]['id'];
            $end1 = $students[min(15, $total_students - 1)]['id'];
            
            $db->query("INSERT INTO ploting_penguji (guru_id, mapel_id, kelas, siswa_id_start, siswa_id_end) 
                        VALUES (:guru_id, :mapel_id, :kelas, :start, :end)");
            $db->bind(':guru_id', $guru_id_1);
            $db->bind(':mapel_id', $mapel_id);
            $db->bind(':kelas', $kelas);
            $db->bind(':start', $start1);
            $db->bind(':end', $end1);
            $db->execute();

            // Part 2: Students 16-end (17-...)
            if ($total_students > 16) {
                $start2 = $students[16]['id'];
                $end2 = $students[$total_students - 1]['id'];

                $db->query("INSERT INTO ploting_penguji (guru_id, mapel_id, kelas, siswa_id_start, siswa_id_end) 
                            VALUES (:guru_id, :mapel_id, :kelas, :start, :end)");
                $db->bind(':guru_id', $guru_id_2);
                $db->bind(':mapel_id', $mapel_id);
                $db->bind(':kelas', $kelas);
                $db->bind(':start', $start2);
                $db->bind(':end', $end2);
                $db->execute();
            }
        }
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    $db->query("DELETE FROM ploting_penguji WHERE id = :id");
    $db->bind(':id', $id);
    $db->execute();
    echo json_encode(['status' => 'success']);
}
