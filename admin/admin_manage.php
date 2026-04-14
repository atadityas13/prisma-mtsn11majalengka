<?php
$page_title = 'Manajemen Admin';
include_once __DIR__ . '/layout/header.php';

$db = new Database();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Sistem /</span> Manajemen Admin</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
        <i class="bx bx-plus me-1"></i> Tambah Admin
    </button>
</div>

<!-- Admin Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover datatable" id="adminTable">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Dibuat Pada</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    $db->query("SELECT a.*, u.username, u.created_at FROM admins a JOIN users u ON a.user_id = u.id ORDER BY a.nama_lengkap ASC");
                    $admins = $db->resultSet();
                    foreach ($admins as $admin):
                    ?>
                    <tr id="row-<?= $admin['id'] ?>">
                        <td><strong><?= $admin['username'] ?></strong></td>
                        <td><?= $admin['nama_lengkap'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($admin['created_at'])) ?></td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item edit-btn" href="javascript:void(0);" 
                                       data-id="<?= $admin['id'] ?>" 
                                       data-username="<?= $admin['username'] ?>"
                                       data-nama="<?= $admin['nama_lengkap'] ?>">
                                       <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>
                                    <a class="dropdown-item delete-btn" href="javascript:void(0);" data-id="<?= $admin['id'] ?>">
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
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addAdminForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Administrator Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Username untuk login" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap Admin" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan untuk menggunakan Username sebagai default" />
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
<div class="modal fade" id="editAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editAdminForm">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Administrator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" id="edit_username" class="form-control" readonly />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" id="edit_nama" name="nama_lengkap" class="form-control" required />
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
    // Add Admin
    document.getElementById('addAdminForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'add');
        fetch('../ajax/admin_action.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') location.reload(); else alert(data.message);
        });
    });

    // Edit Button Click
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_username').value = this.dataset.username;
            document.getElementById('edit_nama').value = this.dataset.nama;
            new bootstrap.Modal(document.getElementById('editAdminModal')).show();
        });
    });

    // Update Admin
    document.getElementById('editAdminForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'update');
        fetch('../ajax/admin_action.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if (data.status === 'success') location.reload(); else alert(data.message);
        });
    });

    // Delete Admin
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Hapus administrator ini? Akun user juga akan terhapus.')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', this.dataset.id);
                fetch('../ajax/admin_action.php', { method: 'POST', body: formData })
                .then(r => r.json()).then(data => {
                    if (data.status === 'success') location.reload(); else alert(data.message);
                });
            }
        });
    });
});
</script>

<?php include_once __DIR__ . '/layout/footer.php'; ?>
