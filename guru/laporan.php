<?php
$page_title = 'Laporan Saya';
include_once __DIR__ . '/layout/header.php';

$db = new Database();
$guru_id = $_SESSION['guru_id'];
$mapel_id = $_SESSION['mapel_id'];

// Fetch Mapel Info
$db->query("SELECT * FROM mapel WHERE id = :id");
$db->bind(':id', $mapel_id);
$mapel = $db->single();

// Fetch Classes assigned to this guru
$db->query("SELECT DISTINCT kelas FROM ploting_penguji WHERE guru_id = :gid AND mapel_id = :mid ORDER BY kelas ASC");
$db->bind(':gid', $guru_id);
$db->bind(':mid', $mapel_id);
$kelas_list = $db->resultSet();
?>

<div class="row">
    <!-- Download Nilai Terisi Card -->
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-info">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">Daftar Nilai Praktik</h5>
                <i class="bx bx-check-double text-info fs-3"></i>
            </div>
            <div class="card-body">
                <p>Cetak atau lihat daftar nilai yang sudah Anda input untuk seluruh siswa (Gabungan semua kelas).</p>
                <div class="d-grid mt-3">
                    <?php if (count($kelas_list) > 0): ?>
                        <a href="<?= base_url('guru/cetak_nilai.php') ?>" 
                           target="_blank" 
                           class="btn btn-info btn-lg text-white">
                            <i class="bx bx-file me-2"></i> Cetak Seluruh Daftar Nilai
                        </a>
                    <?php else: ?>
                        <div class="alert alert-warning">Anda belum memiliki ploting penguji.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Borang Card -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">Borang Penilaian Kosong</h5>
                <i class="bx bx-printer text-primary fs-3"></i>
            </div>
            <div class="card-body">
                <p>Cetak lembar penilaian kosong (PDF) untuk seluruh siswa yang Anda uji pada mata pelajaran <strong><?= $mapel['nama_mapel'] ?></strong>.</p>
                <div class="d-grid mt-3">
                    <?php if (count($kelas_list) > 0): ?>
                        <a href="<?= base_url('guru/cetak_borang.php') ?>" 
                           target="_blank" 
                           class="btn btn-primary btn-lg">
                            <i class="bx bx-printer me-2"></i> Cetak Seluruh Borang Kosong
                        </a>
                    <?php else: ?>
                        <div class="alert alert-warning">Anda belum memiliki ploting penguji. Hubungi Admin.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/layout/footer.php'; ?>
