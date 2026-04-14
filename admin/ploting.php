<?php
$page_title = 'Ploting Penguji';
include_once __DIR__ . '/layout/header.php';

$db = new Database();

// Fetch Mapels
$db->query("SELECT * FROM mapel ORDER BY nama_mapel ASC");
$mapels = $db->resultSet();

// Fetch Gurus
$db->query("SELECT * FROM guru ORDER BY nama_lengkap ASC");
$gurus = $db->resultSet();

// Fetch unique classes from siswa table
$db->query("SELECT DISTINCT kelas FROM siswa ORDER BY kelas ASC");
$kelas_list = $db->resultSet();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Manajemen /</span> Ploting Penguji</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPlotingModal">
        <i class="bx bx-git-repo-forked me-1"></i> Buat Ploting Baru
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Guru Penguji</th>
                        <th>Rentang Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $db->query("SELECT p.*, g.nama_lengkap as nama_guru, m.nama_mapel, 
                                s1.nama_lengkap as start_nama, s2.nama_lengkap as end_nama 
                                FROM ploting_penguji p 
                                JOIN guru g ON p.guru_id = g.id 
                                JOIN mapel m ON p.mapel_id = m.id 
                                LEFT JOIN siswa s1 ON p.siswa_id_start = s1.id 
                                LEFT JOIN siswa s2 ON p.siswa_id_end = s2.id
                                ORDER BY m.nama_mapel ASC, p.kelas ASC");
                    $plotings = $db->resultSet();
                    foreach ($plotings as $p):
                    ?>
                    <tr>
                        <td><strong><?= $p['nama_mapel'] ?></strong></td>
                        <td><span class="badge bg-label-secondary"><?= $p['kelas'] ?></span></td>
                        <td><?= $p['nama_guru'] ?></td>
                        <td>
                            <?php if ($p['siswa_id_start']): ?>
                                Sesuai urutan (ID <?= $p['siswa_id_start'] ?> - <?= $p['siswa_id_end'] ?>)
                            <?php else: ?>
                                Seluruh Kelas
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger delete-plot-btn" data-id="<?= $p['id'] ?>">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addPlotingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addPlotingForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atur Ploting Penguji</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="mapel_id" class="form-select" required>
                            <option value="">Pilih Mapel</option>
                            <?php foreach ($mapels as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= $m['nama_mapel'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas" class="form-select" required>
                            <option value="">Pilih Kelas</option>
                            <?php foreach ($kelas_list as $k): ?>
                                <option value="<?= $k['kelas'] ?>"><?= $k['kelas'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Metode Pembagian</label>
                        <select id="metode" name="metode" class="form-select">
                            <option value="1">1 Mapel = 1 Guru (Seluruh Kelas)</option>
                            <option value="2">1 Mapel = 2 Guru (Dibagi Rata)</option>
                        </select>
                    </div>
                    
                    <div id="guru_selection_area">
                        <div class="mb-3" id="guru1_area">
                            <label class="form-label" id="label_guru1">Pilih Guru Penguji</label>
                            <select name="guru_id_1" class="form-select" required>
                                <option value="">Pilih Guru</option>
                                <?php foreach ($gurus as $g): ?>
                                    <option value="<?= $g['id'] ?>"><?= $g['nama_lengkap'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="guru2_area">
                            <label class="form-label">Pilih Guru Penguji Kedua</label>
                            <select name="guru_id_2" class="form-select">
                                <option value="">Pilih Guru</option>
                                <?php foreach ($gurus as $g): ?>
                                    <option value="<?= $g['id'] ?>"><?= $g['nama_lengkap'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info py-2" style="font-size: 0.8rem;">
                        <i class="bx bx-info-circle me-1"></i> Jika dibagi 2 guru, sistem akan otomatis membagi siswa berdasarkan nomor urut (1-16 dan 17-akhir).
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Ploting</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const metodeSelect = document.getElementById('metode');
    const guru2Area = document.getElementById('guru2_area');
    const labelGuru1 = document.getElementById('label_guru1');

    metodeSelect.addEventListener('change', function() {
        if (this.value === '2') {
            guru2Area.classList.remove('d-none');
            labelGuru1.innerText = "Pilih Guru Penguji Pertama (Siswa 1-16)";
        } else {
            guru2Area.classList.add('d-none');
            labelGuru1.innerText = "Pilih Guru Penguji";
        }
    });

    // Save Ploting
    document.getElementById('addPlotingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save_plot');
        
        fetch('../ajax/ploting_action.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') location.reload(); else alert(data.message);
        });
    });

    // Delete
    document.querySelectorAll('.delete-plot-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Hapus ploting ini?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', this.dataset.id);
                fetch('../ajax/ploting_action.php', { method: 'POST', body: formData })
                .then(r => r.json()).then(data => {
                    if (data.status === 'success') location.reload();
                });
            }
        });
    });
});
</script>

<?php include_once __DIR__ . '/layout/footer.php'; ?>
