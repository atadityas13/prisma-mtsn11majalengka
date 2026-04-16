<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';

$db = new Database();

try {
    echo "Running migration for 'Materi Uji' layer...\n";
    
    // 1. Create materi_penilaian table
    $db->query("CREATE TABLE IF NOT EXISTS materi_penilaian (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mapel_id INT NOT NULL,
        nama_materi VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (mapel_id) REFERENCES mapel(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    $db->execute();
    echo "- Table 'materi_penilaian' created/verified.\n";
    
    // 2. Add materi_id to aspek_penilaian
    // Check if column exists first
    $db->query("SHOW COLUMNS FROM aspek_penilaian LIKE 'materi_id'");
    $exists = $db->single();
    
    if (!$exists) {
        $db->query("ALTER TABLE aspek_penilaian ADD COLUMN materi_id INT NULL AFTER mapel_id");
        $db->execute();
        
        $db->query("ALTER TABLE aspek_penilaian ADD CONSTRAINT fk_aspek_materi FOREIGN KEY (materi_id) REFERENCES materi_penilaian(id) ON DELETE SET NULL");
        $db->execute();
        echo "- Column 'materi_id' added to 'aspek_penilaian'.\n";
    } else {
        echo "- Column 'materi_id' already exists.\n";
    }
    
    echo "Migration successful!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
