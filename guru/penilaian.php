<?php
$page_title = 'Input Nilai Praktik';
include_once __DIR__ . '/layout/header.php';

$db = new Database();
$guru_id = $_SESSION['guru_id'];
$mapel_id = $_SESSION['mapel_id'];

// Fetch All Materis for this mapel
$db->query("SELECT * FROM materi_penilaian WHERE mapel_id = :mapel_id ORDER BY id ASC");
$db->bind(':mapel_id', $mapel_id);
$materis = $db->resultSet();

// Fetch All Aspeks joined with Materi
$db->query("SELECT a.*, m.nama_materi FROM aspek_penilaian a 
            JOIN materi_penilaian m ON a.materi_id = m.id 
            WHERE a.mapel_id = :mapel_id 
            ORDER BY m.id ASC, a.id ASC");
$db->bind(':mapel_id', $mapel_id);
$aspeks = $db->resultSet();

// Group aspects by Materi for easier access
$materi_aspeks = [];
foreach ($aspeks as $a) {
    $materi_aspeks[$a['materi_id']][] = $a;
}

// Fetch Mapel Name
$db->query("SELECT nama_mapel FROM mapel WHERE id = :id");
$db->bind(':id', $mapel_id);
$mapel_name = $db->single()['nama_mapel'] ?? 'Mata Pelajaran';

if (empty($materis) || empty($aspeks)) {
    echo "<div class='alert alert-danger'>Silakan tentukan <a href='materi.php'>Materi</a> dan <a href='aspek.php'>Aspek Penilaian</a> terlebih dahulu sebelum mulai menilai.</div>";
    include_once __DIR__ . '/layout/footer.php';
    exit;
}

// Fetch Students assigned to this guru
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
// Prepare JS data and calculate counts
$count_sudah = 0;
$count_belum = 0;
$js_students = [];

foreach ($siswas as $s) {
    // Check which materials are completed
    $completed_materi_ids = [];
    foreach ($materis as $m) {
        $m_id = $m['id'];
        $aspek_ids = array_column($materi_aspeks[$m_id] ?? [], 'id');
        if (!empty($aspek_ids)) {
            $placeholders = implode(',', array_fill(0, count($aspek_ids), '?'));
            $db->query("SELECT COUNT(*) as count FROM nilai_praktik 
                        WHERE siswa_id = ? AND guru_id = ? AND mapel_id = ? AND aspek_id IN ($placeholders)");
            $params = array_merge([$s['id'], $guru_id, $mapel_id], $aspek_ids);
            
            // Re-bind manually because of the variadic IN clause
            $db->query("SELECT COUNT(*) as count FROM nilai_praktik 
                        WHERE siswa_id = :sid AND guru_id = :gid AND mapel_id = :mid AND aspek_id IN ($placeholders)");
            $db->bind(':sid', $s['id']);
            $db->bind(':gid', $guru_id);
            $db->bind(':mid', $mapel_id);
            // We need to bind each aspek_id individually for the placeholder
            foreach ($aspek_ids as $idx => $aid) {
                // PDO placeholders are 1-indexed if using ?, but here we used named for the others
                // Mixing ? and : is usually not allowed or discouraged. I'll use named for all.
            }
            // Let's simplify: fetch all scores for this student/guru/mapel once
        }
    }
    
    // Better approach: fetch all scores for this student
    $db->query("SELECT aspek_id FROM nilai_praktik WHERE siswa_id = :sid AND guru_id = :gid AND mapel_id = :mid");
    $db->bind(':sid', $s['id']);
    $db->bind(':gid', $guru_id);
    $db->bind(':mid', $mapel_id);
    $all_scores = array_column($db->resultSet(), 'aspek_id');
    
    $student_completed_materis = [];
    foreach ($materis as $m) {
        $m_id = $m['id'];
        $m_aspek_ids = array_column($materi_aspeks[$m_id] ?? [], 'id');
        if (empty($m_aspek_ids)) continue;
        
        $is_materi_complete = true;
        foreach ($m_aspek_ids as $aid) {
            if (!in_array($aid, $all_scores)) {
                $is_materi_complete = false;
                break;
            }
        }
        if ($is_materi_complete) {
            $student_completed_materis[] = $m_id;
        }
    }
    
    $is_all_done = (count($student_completed_materis) === count($materis));
    if ($is_all_done) $count_sudah++; else $count_belum++;
    
    $js_students[] = [
        'id' => $s['id'],
        'nama' => $s['nama_lengkap'],
        'nopes' => $s['nomor_peserta'] ?? '-',
        'kelas' => $s['kelas'],
        'completed_materis' => $student_completed_materis,
        'is_graded' => $is_all_done
    ];
}

$js_materis = [];
foreach ($materis as $m) {
    $js_materis[] = [
        'id' => $m['id'],
        'nama' => $m['nama_materi'],
        'aspeks' => $materi_aspeks[$m['id']] ?? []
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
                <div class="mt-2 text-primary fw-bold border-top pt-2">
                    <i class="bx bx-book-content"></i> Materi: <span id="qgMateriName"></span>
                </div>
            </div>

            <form id="qgForm">
                <input type="hidden" id="qgSiswaId">
                <input type="hidden" id="qgMateriId">
                <div id="qgFields">
                    <!-- Loaded via JS -->
                </div>
                <div class="mb-3">
                    <label class="form-label small mb-1">Catatan (Opsional)</label>
                    <textarea name="catatan" class="form-control form-control-sm" rows="1" placeholder="Catatan..."></textarea>
                </div>
                <div class="row g-2 mt-2" id="qgMultiButtons">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" id="qgNext">
                            <i class="bx bx-skip-next me-1"></i> Lewati
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="submit" id="qgSubmitLanjut" class="btn btn-primary btn-sm w-100">
                             Simpan-lanjut <i class="bx bx-right-arrow-alt"></i>
                        </button>
                    </div>
                    <div class="col-12">
                        <button type="button" id="qgSubmitSelesai" class="btn btn-success btn-sm w-100">
                            <i class="bx bx-check-double me-1"></i> SIMPAN & SELESAIKAN SISWA
                        </button>
                    </div>
                </div>
                <div class="row g-2 mt-2" id="qgSingleButtons" style="display: none;">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 qgNextSingle">
                            <i class="bx bx-skip-next me-1"></i> Lewati
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-success btn-sm w-100">
                             Simpan Nilai <i class="bx bx-check me-1"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div id="qgDone" style="display: none;" class="text-center py-3">
            <div class="avatar avatar-lg mx-auto mb-3">
                <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-check-double bx-md"></i></span>
            </div>
            <h5 class="mb-1">Alhamdulillah!</h5>
            <p class="mb-3 text-muted small">Semua siswa di daftar ini sudah memiliki nilai lengkap.</p>
            <a href="laporan.php" class="btn btn-success btn-sm">
                <i class="bx bx-printer me-1"></i> Cetak Hasil
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
                <a href="javascript:void(0);" class="nav-link" data-filter="Lengkap" style="cursor: pointer !important;">
                    Lengkap <span class="badge badge-center rounded-pill bg-label-success ms-1"><?= $count_sudah ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0);" class="nav-link" data-filter="Belum" style="cursor: pointer !important;">
                    Belum Lengkap <span class="badge badge-center rounded-pill bg-label-danger ms-1"><?= $count_belum ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body pt-4">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover" id="tablePenilaian">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Progress</th>
                        <th>Nilai Akhir (Avg Materi)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siswas as $s): 
                        // Implementation of the new calculation logic:
                        // Total Average = Average of Material Averages
                        $materi_averages = [];
                        foreach ($materis as $m) {
                            $db->query("SELECT AVG(nilai_angka) as avg_m FROM nilai_praktik 
                                        WHERE siswa_id = :sid AND guru_id = :gid AND mapel_id = :mid 
                                        AND aspek_id IN (SELECT id FROM aspek_penilaian WHERE materi_id = :m_id)");
                            $db->bind(':sid', $s['id']);
                            $db->bind(':gid', $guru_id);
                            $db->bind(':mid', $mapel_id);
                            $db->bind(':m_id', $m['id']);
                            $m_res = $db->single();
                            if (!is_null($m_res['avg_m'])) {
                                $materi_averages[] = $m_res['avg_m'];
                            }
                        }
                        
                        $count_done_materis = count($materi_averages);
                        $total_materi = count($materis);
                        $final_avg = $count_done_materis > 0 ? array_sum($materi_averages) / $total_materi : null;
                    ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?= $s['nama_lengkap'] ?></div>
                            <small class="text-muted"><?= $s['nomor_peserta'] ?></small>
                        </td>
                        <td><?= $s['kelas'] ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if ($count_done_materis == $total_materi): ?>
                                    <span class="d-none">Lengkap</span>
                                    <span class="badge bg-label-success me-2"><?= $count_done_materis ?>/<?= $total_materi ?> Materi</span>
                                    <span class="badge bg-label-success">Lengkap</span>
                                <?php else: ?>
                                    <span class="d-none">Belum</span>
                                    <span class="badge bg-label-warning me-2"><?= $count_done_materis ?>/<?= $total_materi ?> Materi</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="fw-bold text-center"><?= !is_null($final_avg) ? round($final_avg, 2) : '-' ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-primary grade-btn" 
                                        data-id="<?= $s['id'] ?>" 
                                        data-nama="<?= $s['nama_lengkap'] ?>">
                                    <i class="bx bx-edit-alt me-1"></i> Nilai
                                </button>
                                <?php if ($count_done_materis > 0): ?>
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
    <div class="modal-dialog modal-lg" role="document">
        <form id="gradeForm">
            <input type="hidden" name="siswa_id" id="siswa_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Penilaian: <span id="nama_siswa_label" class="fw-bold text-primary"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="dynamicFields">
                        <?php foreach ($materis as $m): ?>
                            <div class="card shadow-none border mb-3">
                                <div class="card-header bg-label-primary py-2 px-3 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold"><i class="bx bx-book-content me-1"></i> Materi: <?= htmlspecialchars($m['nama_materi']) ?></h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <?php if (!empty($materi_aspeks[$m['id']])): ?>
                                            <?php foreach ($materi_aspeks[$m['id']] as $a): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label small"><?= htmlspecialchars($a['nama_aspek']) ?></label>
                                                    <input type="number" 
                                                           name="nilai[<?= $a['id'] ?>]" 
                                                           class="form-control form-control-sm score-input" 
                                                           placeholder="Skor 0-100" 
                                                           min="0" max="100" required>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="col-12 text-muted small italic">Tidak ada aspek untuk materi ini.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Penilaian (Opsional)</label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Simpan Seluruh Nilai</button>
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
            
            gradeForm.reset();

            fetch('../ajax/get_nilai.php?siswa_id=' + sid)
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success' && data.scores.length > 0) {
                        data.scores.forEach(s => {
                            const input = gradeForm.querySelector(`input[name="nilai[${s.aspek_id}]"]`);
                            if (input) input.value = s.nilai_angka;
                        });
                        document.getElementById('catatan').value = data.catatan || '';
                    }
                });

            gradeModal.show();
        });
    });
    
    const gradingTable = $('#tablePenilaian').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" }
    });
    
    $(document).on('click', '#filterTabs .nav-link', function(e) {
        e.preventDefault();
        $('#filterTabs .nav-link').removeClass('active');
        $(this).addClass('active');
        const filterValue = $(this).attr('data-filter');
        
        // Filter specific column (Index 2: Progress)
        gradingTable.column(2).search(filterValue).draw();
    });

    // Mobile Quick Grading Logic Refined
    const students = <?= json_encode($js_students) ?>;
    const materis = <?= json_encode($js_materis) ?>;
    let currentStudentIdx = -1;
    let currentMateriInStudentIdx = -1;

    function findNextAvailableMateri(studentIdx) {
        const student = students[studentIdx];
        if (!student) return -1;
        // Find first materi ID not in student.completed_materis
        for (let i = 0; i < materis.length; i++) {
            if (!student.completed_materis.includes(materis[i].id)) {
                return i;
            }
        }
        return -1;
    }

    function findNextStudentWithWork(startIdx = 0) {
        for (let i = startIdx; i < students.length; i++) {
            if (!students[i].is_graded) return i;
        }
        for (let i = 0; i < startIdx; i++) {
            if (!students[i].is_graded) return i;
        }
        return -1;
    }

    function renderQuickGrading() {
        // If we are already mid-student, check if more materi is left
        if (currentStudentIdx !== -1) {
            const nextMateriIdx = findNextAvailableMateri(currentStudentIdx);
            if (nextMateriIdx !== -1) {
                currentMateriInStudentIdx = nextMateriIdx;
                displayCurrentStep();
                return;
            }
        }

        // Otherwise find next student
        const nextStudentIdx = findNextStudentWithWork(currentStudentIdx + 1);
        if (nextStudentIdx === -1) {
            $('#qgContent').hide();
            $('#qgDone').show();
            return;
        }

        currentStudentIdx = nextStudentIdx;
        currentMateriInStudentIdx = findNextAvailableMateri(currentStudentIdx);
        displayCurrentStep();
    }

    function displayCurrentStep() {
        const s = students[currentStudentIdx];
        const m = materis[currentMateriInStudentIdx];
        
        $('#qgSiswaId').val(s.id);
        $('#qgMateriId').val(m.id);
        $('#qgNama').text(s.nama);
        $('#qgNopes').text(s.nopes);
        $('#qgKelas').text(s.kelas);
        $('#qgMateriName').text(m.nama);
        
        // Show/Hide buttons based on materi count
        if (materis.length > 1) {
            $('#qgMultiButtons').show();
            $('#qgSingleButtons').hide();
        } else {
            $('#qgMultiButtons').hide();
            $('#qgSingleButtons').show();
        }

        let fieldsHtml = '';
        m.aspeks.forEach(a => {
            fieldsHtml += `
                <div class="mb-2">
                    <label class="form-label small mb-1">${a.nama_aspek}</label>
                    <input type="number" name="nilai[${a.id}]" class="form-control form-control-sm qg-input" min="0" max="100" required placeholder="0-100">
                </div>
            `;
        });
        $('#qgFields').html(fieldsHtml);
        $('#qgForm')[0].reset();
        $('#qgContent').show();
        $('#qgDone').hide();
    }

    renderQuickGrading();

    $(document).on('click', '#qgNext, .qgNextSingle', function() {
        currentStudentIdx++;
        if (currentStudentIdx >= students.length) currentStudentIdx = 0;
        renderQuickGrading();
    });

    // "Selesai" button for Quick Grading - marks student as done and moves on
    $('#qgSubmitSelesai').on('click', function() {
        if (confirm('Simpan dan selesaikan penilaian untuk siswa ini?')) {
            submitQuickGrading(true);
        }
    });

    $('#qgForm').on('submit', function(e) {
        e.preventDefault();
        submitQuickGrading(false);
    });

    function submitQuickGrading(isFinalForStudent) {
        const btn = $('#qgSubmitLanjut');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');

        const formData = new FormData(document.getElementById('qgForm'));
        formData.append('siswa_id', $('#qgSiswaId').val());
        formData.append('action', 'save_score');

        fetch('../ajax/grade_action.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') {
                const sId = $('#qgSiswaId').val();
                const mId = parseInt($('#qgMateriId').val());
                
                // Update local data
                const s = students.find(x => x.id == sId);
                if (!s.completed_materis.includes(mId)) s.completed_materis.push(mId);
                
                if (isFinalForStudent || s.completed_materis.length === materis.length) {
                    s.is_graded = true;
                    // Move to next student
                    currentStudentIdx++;
                }
                
                setTimeout(() => {
                    btn.prop('disabled', false).html('Simpan-lanjut <i class="bx bx-right-arrow-alt"></i>');
                    renderQuickGrading();
                    // Optionally alert/toast success
                }, 300);
            } else {
                alert(data.message);
                btn.prop('disabled', false).html('Simpan-lanjut');
            }
        });
    }

    // Modal Save
    gradeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save_score');
        fetch('../ajax/grade_action.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') {
                gradeModal.hide();
                location.reload();
            } else {
                alert(data.message);
            }
        });
    });

    // Reset
    $(document).on('click', '.reset-btn', function() {
        const sid = $(this).data('id');
        const nama = $(this).data('nama');
        if (confirm(`Reset semua nilai untuk ${nama}?`)) {
            const formData = new FormData();
            formData.append('siswa_id', sid);
            formData.append('action', 'reset_score');
            fetch('../ajax/grade_action.php', { method: 'POST', body: formData })
            .then(r => r.json()).then(data => {
                if (data.status === 'success') location.reload();
            });
        }
    });
});
</script>
