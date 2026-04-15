<?php
$page_title = 'Input Nilai Praktik';
include_once __DIR__ . '/layout/header.php';

$db = new Database();
$guru_id = $_SESSION['guru_id'];
$mapel_id = $_SESSION['mapel_id'];

// Fetch All Aspeks for this mapel (Global)
$db->query("SELECT * FROM aspek_penilaian WHERE mapel_id = :mapel_id ORDER BY id ASC");
$db->bind(':mapel_id', $mapel_id);
$aspeks = $db->resultSet();

// Fetch Mapel Name
$db->query("SELECT nama_mapel FROM mapel WHERE id = :id");
$db->bind(':id', $mapel_id);
$mapel_name = $db->single()['nama_mapel'] ?? 'Mata Pelajaran';

if (count($aspeks) == 0) {
    echo "<div class='alert alert-danger'>Silakan tentukan <a href='aspek.php'>Aspek Penilaian</a> terlebih dahulu sebelum mulai menilai.</div>";
    include_once __DIR__ . '/layout/footer.php';
    exit;
}

// Fetch Students assigned to this guru via ploting_siswa
$db->query("SELECT s.* FROM siswa s
            JOIN ploting_siswa ps  ON ps.siswa_id = s.id
            JOIN ploting_penguji pp ON ps.ploting_id = pp.id
            WHERE pp.guru_id = :guru_id AND pp.mapel_id = :mapel_id
            ORDER BY s.nomor_peserta ASC");
$db->bind(':guru_id', $guru_id);
$db->bind(':mapel_id', $mapel_id);
$siswas = $db->resultSet();

?>

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Guru /</span> Penilaian Siswa</h4>

<?php
// Calculate counts and prepare JS data
$count_sudah = 0;
$count_belum = 0;
$js_students = [];

foreach ($siswas as $s) {
    $db->query("SELECT id FROM nilai_praktik WHERE siswa_id = :sid AND guru_id = :gid AND mapel_id = :mid LIMIT 1");
    $db->bind(':sid', $s['id']);
    $db->bind(':gid', $guru_id);
    $db->bind(':mid', $mapel_id);
    $graded = $db->single();
    
    if ($graded) {
        $count_sudah++;
    } else {
        $count_belum++;
    }
    
    $js_students[] = [
        'id' => $s['id'],
        'nama' => $s['nama_lengkap'],
        'nopes' => $s['nomor_peserta'] ?? '-',
        'kelas' => $s['kelas'],
        'is_graded' => (bool)$graded
    ];
}
?>

<!-- Mobile Quick Grading -->
<div id="quickGradingCard" class="card mb-4 d-xl-none bg-label-primary border-primary">
    <div class="card-body">
        <div id="qgContent">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 text-primary"><i class="bx bx-bolt-circle me-1"></i> Penilaian Praktik <?= $mapel_name ?></h5>
            </div>
            
            <div class="bg-white p-3 rounded mb-3 shadow-sm">
                <div class="mb-1"><small class="text-muted">Nama Siswa:</small> <div id="qgNama" class="fw-bold text-dark"></div></div>
                <div class="row">
                    <div class="col-6"><small class="text-muted">No. Peserta:</small> <div id="qgNopes" class="fw-semibold"></div></div>
                    <div class="col-6"><small class="text-muted">Kelas:</small> <div id="qgKelas" class="fw-semibold"></div></div>
                </div>
            </div>

            <form id="qgForm">
                <input type="hidden" id="qgSiswaId">
                <div id="qgFields">
                    <?php foreach ($aspeks as $a): ?>
                        <div class="mb-2">
                            <label class="form-label small mb-1"><?= $a['nama_aspek'] ?></label>
                            <input type="number" name="nilai[<?= $a['id'] ?>]" class="form-control form-control-sm qg-input" min="0" max="100" required placeholder="0-100">
                        </div>
                    <?php endforeach; ?>
                    <div class="mb-2">
                        <label class="form-label small mb-1">Catatan (Opsional)</label>
                        <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Catatan penilaian..."></textarea>
                    </div>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Simpan & Lanjut
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="qgNext">
                        Lanjut (Skip) <i class="bx bx-chevron-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
        
        <div id="qgDone" style="display: none;" class="text-center py-3">
            <div class="avatar avatar-lg mx-auto mb-3">
                <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-check-double bx-md"></i></span>
            </div>
            <h5 class="mb-1">Luar Biasa!</h5>
            <p class="mb-3 text-muted small">Semua siswa sudah dinilai. silahkan cetak hasil ujian praktik untuk diserahkan kepada panitia</p>
            <a href="laporan.php" class="btn btn-success btn-sm">
                <i class="bx bx-printer me-1"></i> Buka Menu Laporan
            </a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header p-3 border-bottom">
        <ul class="nav nav-pills" id="filterTabs">
            <li class="nav-item">
                <a href="javascript:void(0);" class="nav-link active" data-filter="" style="cursor: pointer !important;">
                    Semua <span class="badge badge-center rounded-pill bg-label-secondary ms-1"><?= count($siswas) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0);" class="nav-link" data-filter="Selesai" style="cursor: pointer !important;">
                    Sudah <span class="badge badge-center rounded-pill bg-label-success ms-1"><?= $count_sudah ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0);" class="nav-link" data-filter="Belum" style="cursor: pointer !important;">
                    Belum <span class="badge badge-center rounded-pill bg-label-danger ms-1"><?= $count_belum ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body pt-4">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover" id="tablePenilaian">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Nilai Rata-rata</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siswas as $s): 
                        // Check if already graded
                        $db->query("SELECT AVG(nilai_angka) as avg_val FROM nilai_praktik WHERE siswa_id = :sid AND guru_id = :gid AND mapel_id = :mid");
                        $db->bind(':sid', $s['id']);
                        $db->bind(':gid', $guru_id);
                        $db->bind(':mid', $mapel_id);
                        $score_res = $db->single();
                        $avg = $score_res['avg_val'];
                        $is_graded = !is_null($avg);
                    ?>
                    <tr>
                        <td><strong><?= $s['nama_lengkap'] ?></strong></td>
                        <td><?= $s['nisn'] ?></td>
                        <td><?= $s['kelas'] ?></td>
                        <td>
                            <?php if ($is_graded): ?>
                                <span class="badge bg-label-success">Selesai</span>
                            <?php else: ?>
                                <span class="badge bg-label-danger">Belum Dinilai</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $is_graded ? round($avg, 2) : '-' ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-primary grade-btn" 
                                        data-id="<?= $s['id'] ?>" 
                                        data-nama="<?= $s['nama_lengkap'] ?>">
                                    <i class="bx bx-edit-alt me-1"></i> Nilai
                                </button>
                                <?php if ($is_graded): ?>
                                    <button class="btn btn-sm btn-outline-danger reset-btn" 
                                            data-id="<?= $s['id'] ?>" 
                                            data-nama="<?= $s['nama_lengkap'] ?>"
                                            title="Reset Nilai">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Penilaian -->
<div class="modal fade" id="gradeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="gradeForm">
            <input type="hidden" name="siswa_id" id="siswa_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Penilaian: <span id="nama_siswa_label" class="fw-bold"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="dynamicFields">
                        <?php foreach ($aspeks as $a): ?>
                            <div class="mb-3">
                                <label class="form-label"><?= $a['nama_aspek'] ?> (Skor 0-100)</label>
                                <input type="number" 
                                       name="nilai[<?= $a['id'] ?>]" 
                                       class="form-control score-input" 
                                       placeholder="Nilai 0-100" 
                                       min="0" max="100" required>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Simpan Nilai</button>
                </div>
            </div>
        </form>
    </div>
</div>


<?php include_once __DIR__ . '/layout/footer.php'; ?>

<script>
$(document).ready(function() {
    const gradeModal = new bootstrap.Modal(document.getElementById('gradeModal'));
    const gradeForm = document.getElementById('gradeForm');

    // Button Click
    document.querySelectorAll('.grade-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const sid = this.dataset.id;
            const nama = this.dataset.nama;
            document.getElementById('siswa_id').value = sid;
            document.getElementById('nama_siswa_label').innerText = nama;
            
            // Clear inputs
            gradeForm.reset();

            // Fetch existing values via AJAX
            fetch('../ajax/get_nilai.php?siswa_id=' + sid)
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success' && data.scores.length > 0) {
                        data.scores.forEach(s => {
                            const input = document.querySelector(`input[name="nilai[${s.aspek_id}]"]`);
                            if (input) input.value = s.nilai_angka;
                        });
                        document.getElementById('catatan').value = data.catatan || '';
                    }
                });

            gradeModal.show();
        });
    });
    
    // Manual initialization to avoid conflict with global .datatable
    const gradingTable = $('#tablePenilaian').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
        }
    });
    
    $(document).on('click', '#filterTabs .nav-link', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Stop event bubbling
        
        // Remove active from others
        $('#filterTabs .nav-link').removeClass('active');
        $(this).addClass('active');

        const filterValue = $(this).attr('data-filter');
        // Filter on Column 3 (Status)
        gradingTable.column(3).search(filterValue).draw();
    });

    // Mobile Quick Grading Logic
    const students = <?= json_encode($js_students) ?>;
    let currentIdx = -1;

    function findNextUnrated(startAt = 0) {
        for (let i = startAt; i < students.length; i++) {
            if (!students[i].is_graded) return i;
        }
        // If not found from startAt, check from beginning
        for (let i = 0; i < startAt; i++) {
            if (!students[i].is_graded) return i;
        }
        return -1;
    }

    function renderQuickGrading() {
        const nextIdx = findNextUnrated(currentIdx + 1);
        if (nextIdx === -1) {
            $('#qgContent').hide();
            $('#qgDone').show();
            return;
        }

        currentIdx = nextIdx;
        const s = students[currentIdx];
        
        $('#qgSiswaId').val(s.id);
        $('#qgNama').text(s.nama);
        $('#qgNopes').text(s.nopes);
        $('#qgKelas').text(s.kelas);
        
        // Reset form
        $('#qgForm')[0].reset();
    }

    // Initial render
    renderQuickGrading();

    // Skip
    $('#qgNext').on('click', renderQuickGrading);

    // QG Save
    $('#qgForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        const formData = new FormData(this);
        formData.append('siswa_id', $('#qgSiswaId').val());
        formData.append('action', 'save_score');

        fetch('../ajax/grade_action.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') {
                // Update local data
                students[currentIdx].is_graded = true;
                // Move to next student after short delay for feedback
                setTimeout(() => {
                    btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Simpan & Lanjut');
                    location.reload(); // Still reload for now to sync table, could be optimized later
                }, 500);
            } else {
                alert(data.message);
                btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Simpan & Lanjut');
            }
        });
    });

    // Reset Score logic
    $(document).on('click', '.reset-btn', function() {
        const sid = $(this).data('id');
        const nama = $(this).data('nama');
        
        if (confirm(`Apakah Anda yakin ingin menghapus (reset) semua nilai untuk ${nama}? Tindakan ini tidak dapat dibatalkan.`)) {
            const formData = new FormData();
            formData.append('siswa_id', sid);
            formData.append('action', 'reset_score');

            fetch('../ajax/grade_action.php', { method: 'POST', body: formData })
            .then(r => r.json()).then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    });

    // Save (from Modal)
    gradeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save_score');

        fetch('../ajax/grade_action.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') {
                gradeModal.hide();
                location.reload(); // Simple reload to update table
            } else {
                alert(data.message);
            }
        });
    });
});
</script>
