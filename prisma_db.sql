-- PRISMA - Penilaian Ujian Praktik Siswa
-- Database Schema


-- 1. Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'guru', 'siswa') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Admins profile
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Subjects (Mapel)
CREATE TABLE IF NOT EXISTS mapel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_mapel VARCHAR(100) NOT NULL,
    kode_mapel VARCHAR(20) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- 4. Guru (Examiners)
CREATE TABLE IF NOT EXISTS guru (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nip VARCHAR(30) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100),
    mapel_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (mapel_id) REFERENCES mapel(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Siswa (Students)
CREATE TABLE IF NOT EXISTS siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nisn VARCHAR(20) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(100) NOT NULL,
    nomor_peserta VARCHAR(50),
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    tahun_ajaran VARCHAR(20) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Assessment Aspects (Aspek Penilaian)
CREATE TABLE IF NOT EXISTS aspek_penilaian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guru_id INT NOT NULL,
    mapel_id INT NOT NULL,
    nama_aspek VARCHAR(100) NOT NULL,
    bobot_nilai INT DEFAULT 1, -- used for weighted average
    FOREIGN KEY (guru_id) REFERENCES guru(id) ON DELETE CASCADE,
    FOREIGN KEY (mapel_id) REFERENCES mapel(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Ploting Penguji (Assignment)
CREATE TABLE IF NOT EXISTS ploting_penguji (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guru_id INT NOT NULL,
    mapel_id INT NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    siswa_id_start INT NULL,
    siswa_id_end INT NULL,
    FOREIGN KEY (guru_id) REFERENCES guru(id) ON DELETE CASCADE,
    FOREIGN KEY (mapel_id) REFERENCES mapel(id) ON DELETE CASCADE,
    FOREIGN KEY (siswa_id_start) REFERENCES siswa(id) ON DELETE SET NULL,
    FOREIGN KEY (siswa_id_end) REFERENCES siswa(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 8. Scores (Nilai Praktik)
CREATE TABLE IF NOT EXISTS nilai_praktik (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT NOT NULL,
    guru_id INT NOT NULL,
    mapel_id INT NOT NULL,
    aspek_id INT NOT NULL,
    nilai_angka DECIMAL(5,2) NOT NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (guru_id) REFERENCES guru(id) ON DELETE CASCADE,
    FOREIGN KEY (mapel_id) REFERENCES mapel(id) ON DELETE CASCADE,
    FOREIGN KEY (aspek_id) REFERENCES aspek_penilaian(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Activity Log
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 10. System Settings
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NULL
) ENGINE=InnoDB;

-- Initial data
INSERT INTO users (username, password, role) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); -- password: password
INSERT INTO admins (user_id, nama_lengkap) VALUES (1, 'Administrator PRISMA');
INSERT INTO system_settings (setting_key, setting_value) VALUES ('tahun_ajaran_default', '2025/2026');
INSERT INTO system_settings (setting_key, setting_value) VALUES ('nama_madrasah', 'MTsN 11 Majalengka');
INSERT INTO system_settings (setting_key, setting_value) VALUES ('lock_nilai', '0');
