<?php
$page_title = 'Manajemen Mata Pelajaran';
include_once __DIR__ . '/layout/header.php';

$db = new Database();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Master Data /</span> Mata Pelajaran</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMapelModal">
        <i class="bx bx-plus me-1"></i> Tambah Mapel
    </button>
</div>

<!-- Mapel Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Kode Mapel</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    $db->query("SELECT * FROM mapel ORDER BY nama_mapel ASC");
                    $mapels = $db->resultSet();
                    foreach ($mapels as $m):
                    ?>
                    <tr>
                        <td><span class="badge bg-label-info"><?= $m['kode_mapel'] ?></span></td>
                        <td><strong><?= $m['nama_mapel'] ?></strong></td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="aspek_mapel.php?id=<?= $m['id'] ?>">
                                       <i class="bx bx-list-check me-1"></i> Kelola Aspek
                                    </a>
                                    <a class="dropdown-item edit-btn" href="javascript:void(0);" 
                                       data-id="<?= $m['id'] ?>" 
                                       data-kode="<?= $m['kode_mapel'] ?>"
                                       data-nama="<?= $m['nama_mapel'] ?>">
                                       <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>
                                    <a class="dropdown-item delete-btn" href="javascript:void(0);" data-id="<?= $m['id'] ?>">
                                        <i class="bx bx-trash me-1"></i> Delete
                                    </a>
                                </div>
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
<div class="modal fade" id="addMapelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addMapelForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kode_mapel" class="form-label">Kode Mapel</label>
                        <input type="text" id="kode_mapel" name="kode_mapel" class="form-control" placeholder="Contoh: PAI-01" required />
                    </div>
                    <div class="mb-3">
                        <label for="nama_mapel" class="form-label">Nama Mata Pelajaran</label>
                        <input type="text" id="nama_mapel" name="nama_mapel" class="form-control" placeholder="Nama Lengkap Mapel" required />
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
<div class="modal fade" id="editMapelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="editMapelForm">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_kode" class="form-label">Kode Mapel</label>
                        <input type="text" id="edit_kode" name="kode_mapel" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama" class="form-label">Nama Mata Pelajaran</label>
                        <input type="text" id="edit_nama" name="nama_mapel" class="form-control" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add Mapel
    document.getElementById('addMapelForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('../ajax/mapel_action.php', {
            method: 'POST',
            body: new FormData(this)
        }).then(r => r.json()).then(data => {
            if (data.status === 'success') location.reload();
            else alert(data.message);
        });
    });

    // Edit Button
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_kode').value = this.dataset.kode;
            document.getElementById('edit_nama').value = this.dataset.nama;
            new bootstrap.Modal(document.getElementById('editMapelModal')).show();
        });
    });

    // Update Mapel
    document.getElementById('editMapelForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'update');
        fetch('../ajax/mapel_action.php', {
            method: 'POST',
            body: formData
        }).then(r => r.json()).then(data => {
            if (data.status === 'success') location.reload();
            else alert(data.message);
        });
    });

    // Delete
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Hapus mapel ini?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', this.dataset.id);
                fetch('../ajax/mapel_action.php', {
                    method: 'POST',
                    body: formData
                }).then(r => r.json()).then(data => {
                    if (data.status === 'success') location.reload();
                    else alert(data.message);
                });
            }
        });
    });
});
</script>

<?php include_once __DIR__ . '/layout/footer.php'; ?>
