<?php
$page_title = 'Laporan & Borang';
include_once __DIR__ . '/layout/header.php';

$db = new Database();

// Fetch Ploting entries for Borang
$db->query("SELECT pp.id, g.nama_lengkap as nama_guru, m.nama_mapel
            FROM ploting_penguji pp
            JOIN guru g ON pp.guru_id = g.id
            JOIN mapel m ON pp.mapel_id = m.id
            ORDER BY m.nama_mapel ASC, g.nama_lengkap ASC");
$plotings = $db->resultSet();
?>

<div class="row">
    <!-- Rekap Nilai Card -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">Rekap Nilai Akhir</h5>
                <i class="bx bx-file-blank text-primary fs-3"></i>
            </div>
            <div class="card-body">
                <p>Unduh rekapitulasi nilai seluruh siswa dari semua mata pelajaran praktikum dalam format Excel.</p>
                <form action="../ajax/export_rekap.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <select name="tahun_ajaran" class="form-select">
                            <option value="2025/2026">2025/2026</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-download me-1"></i> Download Rekap Excel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Cetak Borang Card -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">Cetak Borang Penilaian</h5>
                <i class="bx bx-printer text-success fs-3"></i>
            </div>
            <div class="card-body">
                <p>Cetak lembar penilaian kosong berdasarkan guru yang sudah diploting, bukan per kelas.</p>
                <form action="cetak_borang.php" method="GET" target="_blank">
                    <div class="mb-3">
                        <label class="form-label">Pilih Guru Penguji / Mapel</label>
                        <select name="ploting_id" class="form-select" required>
                            <option value="">Pilih Ploting</option>
                            <?php foreach ($plotings as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_guru'] . ' — ' . $p['nama_mapel']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bx bx-printer me-1"></i> Cetak Borang Kosong
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Student Reports -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Cetak Rapor Praktik (Kolektif)</h5>
            </div>
            <div class="card-body">
                <p>Cetak hasil penilaian ujian praktik untuk setiap siswa dalam satu kelas.</p>
                <div class="row g-3">
                    <?php foreach ($kelas_list as $k): ?>
                        <div class="col-md-3 col-6">
                            <a href="cetak_rapor.php?kelas=<?= urlencode($k['kelas']) ?>" target="_blank" class="btn btn-outline-info w-100">
                                <i class="bx bx-printer me-1"></i> Kelas <?= $k['kelas'] ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/layout/footer.php'; ?>
