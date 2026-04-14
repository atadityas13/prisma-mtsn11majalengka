<?php
include_once __DIR__ . '/layout/header.php';

$db = new Database();
$siswa_id = $_SESSION['siswa_id'];

// Fetch Siswa Profile
$db->query("SELECT * FROM siswa WHERE id = :id");
$db->bind(':id', $siswa_id);
$profile = $db->single();

// Fetch Scores
// We join with Mapel and Guru
$db->query("SELECT m.nama_mapel, g.nama_lengkap as nama_penguji, 
            AVG(n.nilai_angka) as avg_nilai, MAX(n.catatan) as catatan
            FROM nilai_praktik n
            JOIN mapel m ON n.mapel_id = m.id
            JOIN guru g ON n.guru_id = g.id
            WHERE n.siswa_id = :siswa_id
            GROUP BY n.mapel_id, n.guru_id");
$db->bind(':siswa_id', $siswa_id);
$scores = $db->resultSet();
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($profile['nama_lengkap']) ?>&background=random&size=100" class="rounded-circle mb-3">
                <h5 class="fw-bold mb-0"><?= $profile['nama_lengkap'] ?></h5>
                <p class="text-muted small mb-3">NISN: <?= $profile['nisn'] ?></p>
                <hr>
                <div class="d-flex justify-content-around text-start">
                    <div>
                        <small class="text-muted d-block">Kelas</small>
                        <span class="fw-semibold"><?= $profile['kelas'] ?></span>
                    </div>
                    <div>
                        <small class="text-muted d-block">No Peserta</small>
                        <span class="fw-semibold"><?= $profile['nomor_peserta'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-white">Hasil Ujian Praktik</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Penguji</th>
                                <th class="text-center">Nilai Akhir</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($scores) > 0): ?>
                                <?php foreach ($scores as $s): ?>
                                <tr>
                                    <td><strong><?= $s['nama_mapel'] ?></strong></td>
                                    <td><?= $s['nama_penguji'] ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-label-primary fs-6"><?= round($s['avg_nilai'], 2) ?></span>
                                    </td>
                                    <td><small class="text-muted italic"><?= $s['catatan'] ?: '-' ?></small></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada nilai yang diinput oleh guru penguji.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="alert alert-info py-2" style="font-size: 0.85rem;">
            <i class="bx bx-info-circle me-1"></i> Nilai akhir dihitung berdasarkan rata-rata dari seluruh aspek penilaian yang diberikan oleh guru penguji.
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/layout/footer.php'; ?>
