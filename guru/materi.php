<?php
$page_title = 'Materi Uji';
include_once __DIR__ . '/layout/header.php';

$db = new Database();
$guru_id = $_SESSION['guru_id'];
$mapel_id = $_SESSION['mapel_id'];

// Fetch All Materi for this mapel
$db->query("SELECT * FROM materi_penilaian WHERE mapel_id = :mapel_id ORDER BY id ASC");
$db->bind(':mapel_id', $mapel_id);
$materis = $db->resultSet();

// Fetch Colleagues
$db->query("SELECT nama_lengkap FROM guru WHERE mapel_id = :mapel_id AND id != :guru_id");
$db->bind(':mapel_id', $mapel_id);
$db->bind(':guru_id', $guru_id);
$colleagues = $db->resultSet();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Guru /</span> Materi Uji</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMateriModal">
        <i class="bx bx-plus me-1"></i> Tambah Materi
    </button>
</div>

<!-- Coordination Info -->
<div class="alert alert-info d-flex" role="alert">
    <span class="badge badge-center rounded-pill bg-info me-3"><i class="bx bx-info-circle"></i></span>
    <div class="d-flex flex-column ps-1">
        <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Informasi Materi</h6>
        <span>Materi Uji adalah kelompok besar dari poin-poin yang akan dinilai. <strong>Satu materi bisa memiliki banyak aspek penilaian.</strong></span>
        <?php if (!empty($colleagues)): ?>
            <div class="mt-2 text-sm border-top pt-2 border-dark border-opacity-10">
                <strong>Rekan Penguji :</strong> 
                <span class="text-muted" style="font-weight: bold;"><?= implode(', ', array_column($colleagues, 'nama_lengkap')) ?></span>
                <br>
                <small>* Silakan berkoordinasi dengan rekan Anda agar materi yang diujikan seragam.</small>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Materi Uji</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php if (empty($materis)): ?>
                        <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada materi. Klik "Tambah Materi" untuk memulai.</td></tr>
                    <?php else: ?>
                        <?php foreach ($materis as $idx => $m): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= htmlspecialchars($m['nama_materi']) ?></strong></td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger delete-materi-btn" data-id="<?= $m['id'] ?>">
                                    <i class="bx bx-trash me-1"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addMateriModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addMateriForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Materi Uji</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Materi</label>
                        <input type="text" name="nama_materi" class="form-control" placeholder="Misal: Shalat Jenazah" required />
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Save
    document.getElementById('addMateriForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'add');
        fetch('../ajax/materi_action.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') location.reload();
            else alert(data.message);
        });
    });

    // Delete
    document.querySelectorAll('.delete-materi-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Hapus materi ini? Semua aspek penilaian di dalamnya juga akan terpengaruh.')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', this.dataset.id);
                fetch('../ajax/materi_action.php', { method: 'POST', body: formData })
                .then(r => r.json()).then(data => {
                    if (data.status === 'success') location.reload();
                    else alert(data.message);
                });
            }
        });
    });
});
</script>

<?php include_once __DIR__ . '/layout/footer.php'; ?>
