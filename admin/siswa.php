<?php
$page_title = 'Manajemen Siswa';
include_once __DIR__ . '/layout/header.php';

$db = new Database();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Master Data /</span> Data Siswa</h4>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importExcelModal">
            <i class="bx bx-file me-1"></i> Import Excel
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSiswaModal">
            <i class="bx bx-plus me-1"></i> Tambah Siswa
        </button>
    </div>
</div>

<!-- Siswa Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover datatable" id="siswaTable">
                <thead>
                    <tr>
                        <th>NISN (User)</th>
                        <th>Nama Lengkap</th>
                        <th>No Peserta</th>
                        <th>JK</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    $db->query("SELECT * FROM siswa ORDER BY nama_lengkap ASC");
                    $siswas = $db->resultSet();
                    foreach ($siswas as $s):
                    ?>
                    <tr>
                        <td><strong><?= $s['nisn'] ?></strong></td>
                        <td><?= $s['nama_lengkap'] ?></td>
                        <td><?= $s['nomor_peserta'] ?></td>
                        <td><?= $s['jenis_kelamin'] ?></td>
                        <td><span class="badge bg-label-secondary"><?= $s['kelas'] ?></span></td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item edit-btn" href="javascript:void(0);" 
                                       data-id="<?= $s['id'] ?>" 
                                       data-nisn="<?= $s['nisn'] ?>"
                                       data-nama="<?= $s['nama_lengkap'] ?>"
                                       data-nopes="<?= $s['nomor_peserta'] ?>"
                                       data-jk="<?= $s['jenis_kelamin'] ?>"
                                       data-kelas="<?= $s['kelas'] ?>">
                                       <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>
                                    <a class="dropdown-item delete-btn" href="javascript:void(0);" data-id="<?= $s['id'] ?>">
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
<div class="modal fade" id="addSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addSiswaForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Siswa Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label">NISN (Username)</label>
                            <input type="text" name="nisn" class="form-control" placeholder="10 Digit NISN" required />
                        </div>
                        <div class="col">
                            <label class="form-label">Nomor Peserta</label>
                            <input type="text" name="nomor_peserta" class="form-control" placeholder="No Ujian" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Sesuai Ijazah" required />
                    </div>
                    <div class="row g-2">
                        <div class="col">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas" class="form-control" placeholder="Contoh: IX-A" required />
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Password (Opsional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan untuk menggunakan NISN sebagai default" />
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

<!-- Import Modal -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="importExcelForm" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Siswa (Excel)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <a href="download_template_siswa.php" class="btn btn-sm btn-label-secondary mb-3">
                            <i class="bx bx-download me-1"></i> Unduh Template Excel
                        </a>
                    </div>
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Pilih File Excel (.xlsx / .xls)</label>
                        <input class="form-control" type="file" id="excel_file" name="excel_file" accept=".xlsx, .xls" required>
                    </div>
                    <div class="alert alert-warning py-2" style="font-size: 0.8rem;">
                        <i class="bx bx-error me-1"></i> Pastikan format kolom sesuai dengan template. Password default adalah NISN.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Upload & Proses</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="editSiswaForm">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">NISN</label>
                        <input type="text" id="edit_nisn" name="nisn" class="form-control" readonly />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" id="edit_nama" name="nama_lengkap" class="form-control" required />
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label">Nomor Peserta</label>
                            <input type="text" id="edit_nopes" name="nomor_peserta" class="form-control" />
                        </div>
                        <div class="col">
                            <label class="form-label">Jenis Kelamin</label>
                            <select id="edit_jk" name="jenis_kelamin" class="form-select" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <input type="text" id="edit_kelas" name="kelas" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah" />
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
    // Add Siswa
    document.getElementById('addSiswaForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'add');
        fetch('../ajax/siswa_action.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') location.reload(); else alert(data.message);
        });
    });

    // Import Excel
    document.getElementById('importExcelForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'import');
        fetch('../ajax/siswa_action.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload();
            } else alert(data.message);
        });
    });

    // Edit Button Click
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_nisn').value = this.dataset.nisn;
            document.getElementById('edit_nama').value = this.dataset.nama;
            document.getElementById('edit_nopes').value = this.dataset.nopes;
            document.getElementById('edit_jk').value = this.dataset.jk;
            document.getElementById('edit_kelas').value = this.dataset.kelas;
            new bootstrap.Modal(document.getElementById('editSiswaModal')).show();
        });
    });

    // Update Siswa
    document.getElementById('editSiswaForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'update');
        fetch('../ajax/siswa_action.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') location.reload(); else alert(data.message);
        });
    });

    // Delete
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Hapus data siswa ini? Akun user juga akan terhapus.')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', this.dataset.id);
                fetch('../ajax/siswa_action.php', { method: 'POST', body: formData })
                .then(r => r.json()).then(data => {
                    if (data.status === 'success') location.reload(); else alert(data.message);
                });
            }
        });
    });
});
</script>

<?php include_once __DIR__ . '/layout/footer.php'; ?>
