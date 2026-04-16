<?php
$page_title = 'Aspek Penilaian';
include_once __DIR__ . '/layout/header.php';

$db = new Database();
$guru_id = $_SESSION['guru_id'];
$mapel_id = $_SESSION['mapel_id'];

// Fetch All Aspek for this mapel (Global)
$db->query("SELECT * FROM aspek_penilaian WHERE mapel_id = :mapel_id ORDER BY id ASC");
$db->bind(':mapel_id', $mapel_id);
$aspeks = $db->resultSet();

// Fetch Colleagues (Teachers with the same mapel)
$db->query("SELECT nama_lengkap FROM guru WHERE mapel_id = :mapel_id AND id != :guru_id");
$db->bind(':mapel_id', $mapel_id);
$db->bind(':guru_id', $guru_id);
$colleagues = $db->resultSet();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Guru /</span> Aspek Penilaian</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAspekModal">
        <i class="bx bx-plus me-1"></i> Tambah Aspek
    </button>
</div>

<!-- Coordination Info -->
<div class="alert alert-info d-flex" role="alert">
    <span class="badge badge-center rounded-pill bg-info me-3"><i class="bx bx-info-circle"></i></span>
    <div class="d-flex flex-column ps-1">
        <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Informasi</h6>
        <span>Aspek penilaian ini berlaku <strong>secara global</strong> untuk mata pelajaran yang Anda uji. Perubahan
            pada aspek ini akan memengaruhi penilaian seluruh siswa di mata pelajaran tersebut.</span>
        <?php if (!empty($colleagues)): ?>
            <div class="mt-2 text-sm">
                <strong>Rekan Penguji :</strong>
                <span class="text-muted"
                    style="font-style: italic;"><?= implode(', ', array_column($colleagues, 'nama_lengkap')) ?></span>
                <br>
                <small>* Silakan berkoordinasi dengan rekan Anda untuk menentukan kriteria penilaian yang seragam.</small>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Alert for Empty Aspek -->
<?php if (count($aspeks) == 0): ?>
    <div class="alert alert-warning d-flex" role="alert">
        <span class="badge badge-center rounded-pill bg-warning me-3"><i class="bx bx-error"></i></span>
        <div class="d-flex flex-column ps-1">
            <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Peringatan: Aspek Belum Diisi</h6>
            <span>Anda belum menambahkan aspek penilaian. Silakan tambah minimal 1 aspek agar bisa mulai menilai
                siswa.</span>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Aspek (Kriteria)</th>
                        <th>Bobot</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php foreach ($aspeks as $idx => $a): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= $a['nama_aspek'] ?></strong></td>
                            <td><?= $a['bobot_nilai'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger delete-aspek-btn" data-id="<?= $a['id'] ?>">
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
<div class="modal fade" id="addAspekModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addAspekForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kriteria Penilaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Aspek</label>
                        <input type="text" name="nama_aspek" class="form-control"
                            placeholder="Misal: Kelancaran Membaca" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bobot Nilai</label>
                        <input type="number" name="bobot_nilai" class="form-control" value="1" min="1" required />
                        <small class="text-muted">Gunakan 1 jika semua aspek memiliki bobot yang sama.</small>
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
    document.addEventListener('DOMContentLoaded', function () {
        // Save
        document.getElementById('addAspekForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add');
            fetch('../ajax/aspek_action.php', { method: 'POST', body: formData })
                .then(r => r.json()).then(data => {
                    if (data.status === 'success') location.reload();
                    else alert(data.message);
                });
        });

        // Delete
        document.querySelectorAll('.delete-aspek-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                if (confirm('Hapus aspek ini? Nilai siswa yang terkait aspek ini juga akan terhapus.')) {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id', this.dataset.id);
                    fetch('../ajax/aspek_action.php', { method: 'POST', body: formData })
                        .then(r => r.json()).then(data => {
                            if (data.status === 'success') location.reload();
                        });
                }
            });
        });
    });
</script>

<?php include_once __DIR__ . '/layout/footer.php'; ?>