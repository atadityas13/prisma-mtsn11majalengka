<?php
$page_title = 'Kelola Aspek Penilaian';
include_once __DIR__ . '/layout/header.php';

$db = new Database();
$mapel_id = $_GET['id'] ?? '';

// Fetch Mapel Info
$db->query("SELECT * FROM mapel WHERE id = :id");
$db->bind(':id', $mapel_id);
$mapel = $db->single();

if (!$mapel) {
    echo "<div class='alert alert-danger'>Mata Pelajaran tidak ditemukan.</div>";
    include_once __DIR__ . '/layout/footer.php';
    exit;
}

// Fetch Aspeks
$db->query("SELECT * FROM aspek_penilaian WHERE mapel_id = :mapel_id ORDER BY id ASC");
$db->bind(':mapel_id', $mapel_id);
$aspeks = $db->resultSet();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Master Data / <a href="mapel.php" class="text-muted">Mata Pelajaran</a> /</span> Aspek Penilaian <?= $mapel['nama_mapel'] ?>
    </h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAspekModal">
        <i class="bx bx-plus me-1"></i> Tambah Aspek
    </button>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Kriteria Penilaian</h5>
        <span class="badge bg-label-secondary"><?= $mapel['kode_mapel'] ?></span>
    </div>
    <div class="card-body">
        <div class="alert alert-info py-2" style="font-size: 0.85rem;">
            <i class="bx bx-info-circle me-1"></i> 
            Aspek yang ditambahkan di sini akan berlaku <strong>Global</strong> untuk semua Guru yang menguji mata pelajaran ini.
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Aspek (Kriteria)</th>
                        <th>Bobot</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php if (count($aspeks) == 0): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada aspek penilaian untuk mapel ini.</td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($aspeks as $idx => $a): 
                        // Get creator name
                        $creator = 'Admin';
                        if ($a['guru_id']) {
                            $db->query("SELECT nama_lengkap FROM guru WHERE id = :id");
                            $db->bind(':id', $a['guru_id']);
                            $g = $db->single();
                            $creator = $g ? $g['nama_lengkap'] : 'Guru';
                        }
                    ?>
                    <tr>
                        <td><?= $idx + 1 ?></td>
                        <td><strong><?= $a['nama_aspek'] ?></strong></td>
                        <td><?= $a['bobot_nilai'] ?></td>
                        <td><small class="text-muted"><?= $creator ?></small></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-warning edit-aspek-btn" 
                                        data-id="<?= $a['id'] ?>" 
                                        data-nama="<?= htmlspecialchars($a['nama_aspek']) ?>"
                                        data-bobot="<?= $a['bobot_nilai'] ?>">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-aspek-btn" data-id="<?= $a['id'] ?>">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addAspekModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addAspekForm">
            <input type="hidden" name="mapel_id" value="<?= $mapel_id ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kriteria Global</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Aspek</label>
                        <input type="text" name="nama_aspek" class="form-control" placeholder="Misal: Kelancaran Membaca" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bobot Nilai</label>
                        <input type="number" name="bobot_nilai" class="form-control" value="1" min="1" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editAspekModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editAspekForm">
            <input type="hidden" name="id" id="edit_aspek_id">
            <input type="hidden" name="mapel_id" value="<?= $mapel_id ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kriteria Global</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Aspek</label>
                        <input type="text" name="nama_aspek" id="edit_nama_aspek" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bobot Nilai</label>
                        <input type="number" name="bobot_nilai" id="edit_bobot_nilai" class="form-control" min="1" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Perbarui</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Save
    document.getElementById('addAspekForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'add');
        fetch('../ajax/aspek_action.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') location.reload(); else alert(data.message);
        });
    });

    // Open Edit Modal
    document.querySelectorAll('.edit-aspek-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_aspek_id').value = this.dataset.id;
            document.getElementById('edit_nama_aspek').value = this.dataset.nama;
            document.getElementById('edit_bobot_nilai').value = this.dataset.bobot;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editAspekModal')).show();
        });
    });

    // Save Edit
    document.getElementById('editAspekForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'edit');
        fetch('../ajax/aspek_action.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') location.reload(); else alert(data.message);
        });
    });

    // Delete
    document.querySelectorAll('.delete-aspek-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Hapus aspek ini secara GLOBAL? Seluruh nilai siswa yang terkait aspek ini dari SEMUA penguji akan ikut terhapus.')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', this.dataset.id);
                fetch('../ajax/aspek_action.php', { method: 'POST', body: formData })
                .then(r => r.json()).then(data => {
                    if (data.status === 'success') location.reload(); else alert(data.message);
                });
            }
        });
    });
});
</script>

<?php include_once __DIR__ . '/layout/footer.php'; ?>
