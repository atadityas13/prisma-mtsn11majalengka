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

// Count Students Assigned via ploting_siswa (skema baru)
$db->query("SELECT COUNT(ps.id) as count
            FROM ploting_siswa ps
            JOIN ploting_penguji pp ON ps.ploting_id = pp.id
            WHERE pp.guru_id = :guru_id AND pp.mapel_id = :mapel_id");
$db->bind(':guru_id', $guru_id);
$db->bind(':mapel_id', $mapel_id);
$total_siswa_assigned = (int) ($db->single()['count'] ?? 0);

// Count Graded
$db->query("SELECT COUNT(DISTINCT siswa_id) as count FROM nilai_praktik WHERE guru_id = :guru_id AND mapel_id = :mapel_id");
$db->bind(':guru_id', $guru_id);
$db->bind(':mapel_id', $mapel_id);
$total_graded = (int) ($db->single()['count'] ?? 0);

$percent = $total_siswa_assigned > 0 ? round(($total_graded / $total_siswa_assigned) * 100, 1) : 0;

// Fetch Jadwal
$db->query("SELECT j.tanggal, j.jam_mulai, j.jam_selesai, j.ruangan, j.keterangan
            FROM jadwal_praktik j
            JOIN ploting_penguji pp ON j.ploting_id = pp.id
            WHERE pp.guru_id = :guru_id AND pp.mapel_id = :mapel_id
            ORDER BY j.tanggal ASC, j.jam_mulai ASC");
$db->bind(':guru_id', $guru_id);
$db->bind(':mapel_id', $mapel_id);
$jadwal = $db->resultSet();

// Check if aspects are already set
$db->query("SELECT COUNT(*) as count FROM aspek_penilaian WHERE mapel_id = :mapel_id");
$db->bind(':mapel_id', $mapel_id);
$total_aspek = (int) ($db->single()['count'] ?? 0);

// Fetch Colleagues (Teachers with the same mapel)
$db->query("SELECT nama_lengkap FROM guru WHERE mapel_id = :mapel_id AND id != :guru_id");
$db->bind(':mapel_id', $mapel_id);
$db->bind(':guru_id', $guru_id);
$colleagues = $db->resultSet();
?>

<?php if ($total_aspek === 0): ?>
    <div class="row">
        <div class="col-12 mb-4">
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <span class="badge badge-center rounded-pill bg-warning me-3"><i class="bx bx-error bx-sm"></i></span>
                <div class="d-flex flex-column ps-1">
                    <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Perhatian: Kriteria Penilaian Belum
                        Ada!</h6>
                    <span>Anda belum menambahkan <strong>Aspek Penilaian</strong> untuk mata pelajaran <?= $mapel_name ?>.
                        Silakan tambahkan kriteria terlebih dahulu sebelum menginput nilai.</span>

                    <?php if (!empty($colleagues)): ?>
                        <div class="mt-2 text-sm border-top pt-2 border-dark border-opacity-10">
                            <strong>Rekan Penguji Anda:</strong>
                            <span class="text-muted"
                                style="font-weight: bold;"><?= implode(', ', array_column($colleagues, 'nama_lengkap')) ?></span>
                            <br>
                            <small>* Silahkan berkoordinasi dengan rekan Anda untuk menentukan kriteria penilaian yang
                                seragam.</small>
                        </div>
                    <?php endif; ?>

                    <div class="mt-2 text-end">
                        <a href="aspek.php" class="btn btn-sm btn-dark">Atur Aspek Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

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
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="m-0">Jadwal Ujian Praktik</h5>
                <small class="text-muted">Mata pelajaran <?= $mapel_name ?></small>
            </div>
            <div class="card-body">
                <?php if (empty($jadwal)): ?>
                    <div class="text-center py-4">
                        <i class="bx bx-calendar bx-lg text-muted mb-2"></i>
                        <p class="text-muted mb-0">Belum ada jadwal yang ditentukan</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($jadwal as $j): ?>
                        <div class="mb-3 p-3 border rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-1 fw-bold"><?= date('d/m/Y', strtotime($j['tanggal'])) ?></h6>
                                    <small class="text-muted">Tanggal Ujian</small>
                                </div>
                                <div class="avatar bg-label-primary rounded-circle p-2">
                                    <i class="bx bx-calendar text-primary"></i>
                                </div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Waktu</small>
                                <strong><?= $j['jam_mulai'] ?> - <?= $j['jam_selesai'] ?></strong>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Ruangan</small>
                                <strong><?= htmlspecialchars($j['ruangan']) ?></strong>
                            </div>
                            <?php if (!empty($j['keterangan'])): ?>
                                <div>
                                    <small class="text-muted"><?= htmlspecialchars($j['keterangan']) ?></small>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
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