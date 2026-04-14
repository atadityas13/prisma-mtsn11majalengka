<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';

$db = new Database();

try {
    // 1. Check if column is already nullable
    // 2. Modify column
    $sql = "ALTER TABLE aspek_penilaian MODIFY guru_id INT NULL";
    $db->query($sql);
    $db->execute();
    echo "SUCCESS: Kolom 'guru_id' di tabel 'aspek_penilaian' sekarang bisa bernilai NULL.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    
    // Sometimes foreign keys prevent direct modification
    echo "Mencoba dengan menonaktifkan Foreign Key check...\n";
    try {
        $db->query("SET FOREIGN_KEY_CHECKS = 0");
        $db->execute();
        $db->query("ALTER TABLE aspek_penilaian MODIFY guru_id INT NULL");
        $db->execute();
        $db->query("SET FOREIGN_KEY_CHECKS = 1");
        $db->execute();
        echo "SUCCESS: Migrasi berhasil setelah menonaktifkan FK check sementara.\n";
    } catch (Exception $e2) {
        echo "FATAL ERROR: " . $e2->getMessage() . "\n";
    }
}
