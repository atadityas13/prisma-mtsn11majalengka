<?php
$page_title = 'Manajemen Guru Penguji';
include_once __DIR__ . '/layout/header.php';

$db = new Database();
$db->query("SELECT * FROM mapel ORDER BY nama_mapel ASC");
$mapels = $db->resultSet();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Master Data /</span> Guru Penguji</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGuruModal">
        <i class="bx bx-plus me-1"></i> Tambah Guru
    </button>
</div>

<!-- Guru Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover datatable" id="guruTable">
                <thead>
                    <tr>
                        <th>NIP (Username)</th>
                        <th>Nama Lengkap</th>
                        <th>Jabatan</th>
                        <th>Mata Pelajaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <!-- Data will be loaded via AJAX/PHP -->
                    <?php
                    $db->query("SELECT g.*, m.nama_mapel FROM guru g JOIN mapel m ON g.mapel_id = m.id ORDER BY g.nama_lengkap ASC");
                    $gurus = $db->resultSet();
                    foreach ($gurus as $guru):
                    ?>
                    <tr id="row-<?= $guru['id'] ?>">
                        <td><strong><?= $guru['nip'] ?></strong></td>
                        <td><?= $guru['nama_lengkap'] ?></td>
                        <td><?= $guru['jabatan'] ?></td>
                        <td><span class="badge bg-label-primary"><?= $guru['nama_mapel'] ?></span></td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item edit-btn" href="javascript:void(0);" 
                                       data-id="<?= $guru['id'] ?>" 
                                       data-nip="<?= $guru['nip'] ?>"
                                       data-nama="<?= $guru['nama_lengkap'] ?>"
                                       data-jabatan="<?= $guru['jabatan'] ?>"
                                       data-mapel="<?= $guru['mapel_id'] ?>">
                                       <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>
                                    <a class="dropdown-item delete-btn" href="javascript:void(0);" data-id="<?= $guru['id'] ?>">
                                        <i class="bx bx-trash me-1"></i> Delete
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item reset-pass-btn" href="javascript:void(0);" data-id="<?= $guru['id'] ?>" data-nama="<?= $guru['nama_lengkap'] ?>">
                                        <i class="bx bx-key me-1"></i> Reset Password
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

<!-- Add Guru Modal -->
<div class="modal fade" id="addGuruModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addGuruForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Guru Penguji</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nip" class="form-label">NIP (Akan menjadi Username)</label>
                            <input type="text" id="nip" name="nip" class="form-control" placeholder="Masukkan NIP" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap & Gelar" required />
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" id="jabatan" name="jabatan" class="form-control" placeholder="Contoh: Guru Madya" />
                        </div>
                        <div class="col mb-0">
                            <label for="mapel_id" class="form-label">Mata Pelajaran</label>
                            <select id="mapel_id" name="mapel_id" class="form-select" required>
                                <option value="">Pilih Mapel</option>
                                <?php foreach ($mapels as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['nama_mapel'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="password" class="form-label">Password (Opsional)</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan untuk menggunakan NIP sebagai default" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveGuruBtn">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Guru Modal -->
<div class="modal fade" id="editGuruModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="editGuruForm">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Guru Penguji</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="edit_nip" class="form-label">NIP</label>
                            <input type="text" id="edit_nip" name="nip" class="form-control" readonly />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="edit_nama" class="form-label">Nama Lengkap</label>
                            <input type="text" id="edit_nama" name="nama_lengkap" class="form-control" required />
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="edit_jabatan" class="form-label">Jabatan</label>
                            <input type="text" id="edit_jabatan" name="jabatan" class="form-control" />
                        </div>
                        <div class="col mb-0">
                            <label for="edit_mapel" class="form-label">Mata Pelajaran</label>
                            <select id="edit_mapel" name="mapel_id" class="form-select" required>
                                <?php foreach ($mapels as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['nama_mapel'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col mb-3">
                            <label for="edit_password" class="form-label">Password Baru (Opsional)</label>
                            <input type="password" id="edit_password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah" />
                        </div>
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
    const addForm = document.getElementById('addGuruForm');
    const editForm = document.getElementById('editGuruForm');

    // Add Guru
    addForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'add');

        fetch('../ajax/guru_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    });

    // Edit Button Click
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_nip').value = this.dataset.nip;
            document.getElementById('edit_nama').value = this.dataset.nama;
            document.getElementById('edit_jabatan').value = this.dataset.jabatan;
            document.getElementById('edit_mapel').value = this.dataset.mapel;
            new bootstrap.Modal(document.getElementById('editGuruModal')).show();
        });
    });

    // Update Guru
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'update');

        fetch('../ajax/guru_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    });

    // Delete Guru
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin menghapus guru ini? Akun user juga akan terhapus.')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', this.dataset.id);

                fetch('../ajax/guru_action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
    // Reset Password
    document.querySelectorAll('.reset-pass-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const nama = this.dataset.nama;
            if (confirm(`Apakah Anda yakin ingin me-reset password ${nama} ke default (Tanggal Lahir DDMMYYYY)?`)) {
                const formData = new FormData();
                formData.append('action', 'reset_password_default');
                formData.append('id', this.dataset.id);

                fetch('../ajax/guru_action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                    } else {
                        alert(data.message);
                    }
                });
            }
        });
    });
});
</script>

<?php include_once __DIR__ . '/layout/footer.php'; ?>
