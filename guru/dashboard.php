<?php
$page_title = 'Beranda Guru';
include_once __DIR__ . '/layout/header.php';

$db = new Database();
$guru_id = $_SESSION['guru_id'];
$mapel_id = $_SESSION['mapel_id'];

// Fetch Mapel Info
$db->query("SELECT nama_mapel FROM mapel WHERE id = :id");
$db->bind(':id', $mapel_id);
$mapel_name = $db->single()['nama_mapel'];

// Count Students Assigned
// We need to check ploting_penguji
$db->query("SELECT * FROM ploting_penguji WHERE guru_id = :guru_id AND mapel_id = :mapel_id");
$db->bind(':guru_id', $guru_id);
$db->bind(':mapel_id', $mapel_id);
$plots = $db->resultSet();

$total_siswa_assigned = 0;
foreach ($plots as $p) {
    if ($p['siswa_id_start']) {
        // Range plotting
        $db->query("SELECT COUNT(*) as count FROM siswa WHERE kelas = :kelas AND id BETWEEN :start AND :end");
        $db->bind(':kelas', $p['kelas']);
        $db->bind(':start', $p['siswa_id_start']);
        $db->bind(':end', $p['siswa_id_end']);
        $total_siswa_assigned += $db->single()['count'];
    } else {
        // Full class plotting
        $db->query("SELECT COUNT(*) as count FROM siswa WHERE kelas = :kelas");
        $db->bind(':kelas', $p['kelas']);
        $total_siswa_assigned += $db->single()['count'];
    }
}

// Count Graded
$db->query("SELECT COUNT(DISTINCT siswa_id) as count FROM nilai_praktik WHERE guru_id = :guru_id AND mapel_id = :mapel_id");
$db->bind(':guru_id', $guru_id);
$db->bind(':mapel_id', $mapel_id);
$total_graded = $db->single()['count'];

$percent = $total_siswa_assigned > 0 ? round(($total_graded / $total_siswa_assigned) * 100, 1) : 0;
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="text-white mb-0">Assalamu'alaikum Bapak/Ibu,<br> <?= $_SESSION['nama_lengkap'] ?>!
                        </h4>
                        <p class="mb-0">Penguji <strong><?= $mapel_name ?></strong></p>
                    </div>
                    <div class="avatar avatar-lg bg-white bg-opacity-25 rounded p-2">
                        <i class="bx bx-award bx-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar bg-label-primary p-2 mb-3 mx-auto">
                    <i class="bx bx-group bx-sm"></i>
                </div>
                <h5 class="mb-1">Siswa Diuji</h5>
                <h2 class="fw-bold mb-2"><?= $total_siswa_assigned ?></h2>
                <small class="text-muted">Total siswa yang harus dinilai</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 border-success">
            <div class="card-body text-center">
                <div class="avatar bg-label-success p-2 mb-3 mx-auto">
                    <i class="bx bx-check-double bx-sm"></i>
                </div>
                <h5 class="mb-1">Sudah Dinilai</h5>
                <h2 class="fw-bold mb-2 text-success"><?= $total_graded ?></h2>
                <small class="text-muted">Siswa yang sudah memiliki nilai</small>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Progres Penilaian</h5>
                <div class="progress mb-2" style="height: 20px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar"
                        style="width: <?= $percent ?>%" aria-valuenow="<?= $percent ?>" aria-valuemin="0"
                        aria-valuemax="100"><?= $percent ?>%</div>
                </div>
                <p class="small text-muted mb-0">Anda telah menyelesaikan <?= $percent ?>% dari total beban pengujian.
                </p>
                <div class="mt-4">
                    <a href="penilaian.php" class="btn btn-primary btn-sm w-100">Lanjutkan Penilaian <i
                            class="bx bx-right-arrow-alt"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="m-0">Langkah Memulai</h5>
            </div>
            <div class="card-body">
                <ol class="list-group list-group-flush list-group-numbered">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Atur Aspek Penilaian</div>
                            Tambahkan kriteria penilaian untuk mata pelajaran <?= $mapel_name ?>.
                        </div>
                        <a href="aspek.php" class="btn btn-xs btn-outline-info">Buka</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Input Nilai Siswa</div>
                            Klik tombol "Nilai" pada daftar siswa yang muncul di halaman penilaian.
                        </div>
                        <a href="penilaian.php" class="btn btn-xs btn-outline-info">Buka</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Selesai & Unduh</div>
                            Unduh rekap nilai dalam format Excel jika semua siswa sudah dinilai.
                        </div>
                        <a href="laporan.php" class="btn btn-xs btn-outline-info">Buka</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/layout/footer.php'; ?>