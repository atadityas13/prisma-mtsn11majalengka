<?php
$page_title = 'Log Aktivitas Sistem';
include_once __DIR__ . '/layout/header.php';

$db = new Database();
$category_filter = $_GET['cat'] ?? '';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Sistem /</span> Log Aktivitas</h4>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-danger btn-sm" id="clearLogsBtn" title="Bersihkan Log">
            <i class="bx bx-trash"></i>
        </button>
        <span class="mx-1 text-muted">|</span>
        <a href="logs.php?cat=system" class="btn <?= $category_filter === 'system' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">Master Data</a>
        <a href="logs.php?cat=assessment" class="btn <?= $category_filter === 'assessment' ? 'btn-success' : 'btn-outline-success' ?> btn-sm">Penilaian</a>
    </div>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Riwayat Aktivitas <?= $category_filter === 'system' ? 'Master Data' : ($category_filter === 'assessment' ? 'Penilaian' : 'Pengguna') ?></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover datatable" id="logsTable">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    $where_clause = "";
                    if ($category_filter === 'system') $where_clause = "WHERE al.category = 'system'";
                    elseif ($category_filter === 'assessment') $where_clause = "WHERE al.category = 'assessment'";

                    $db->query("SELECT al.*, u.username, u.role, 
                               COALESCE(adm.nama_lengkap, g.nama_lengkap, s.nama_lengkap) as nama_display 
                        FROM activity_log al
                        LEFT JOIN users u ON al.user_id = u.id
                        LEFT JOIN admins adm ON u.id = adm.user_id AND u.role = 'admin'
                        LEFT JOIN guru g ON u.id = g.user_id AND u.role = 'guru'
                        LEFT JOIN siswa s ON u.id = s.user_id AND u.role = 'siswa'
                        $where_clause
                        ORDER BY al.created_at DESC");
                    $logs = $db->resultSet();
                    foreach ($logs as $log):
                        $cat_badge = ($log['category'] === 'assessment') ? 'bg-label-success' : 'bg-label-info';
                        $cat_icon = ($log['category'] === 'assessment') ? 'bx-edit' : 'bx-shield';
                        $cat_text = ($log['category'] === 'assessment') ? 'Penilaian' : 'Master Data';
                    ?>
                    <tr>
                        <td><span class="text-muted"><?= date('d/m/Y H:i', strtotime($log['created_at'] ?? '')) ?></span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($log['nama_display'] ?? $log['username'] ?? 'System') ?>&background=random&color=fff" class="rounded-circle">
                                </div>
                                <div class="d-flex flex-column">
                                    <strong><?= $log['nama_display'] ?? $log['username'] ?? 'System' ?></strong>
                                    <small class="text-muted"><?= $log['username'] ?? '' ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge <?= $cat_badge ?>">
                                <i class="bx <?= $cat_icon ?> me-1"></i> <?= $cat_text ?>
                            </span>
                        </td>
                        <td><?= $log['action'] ?></td>
                        <td><small class="text-muted"><?= $log['ip_address'] ?></small></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const clearBtn = document.getElementById('clearLogsBtn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin menghapus SELURUH riwayat log? Tindakan ini tidak dapat dibatalkan.')) {
                const formData = new FormData();
                formData.append('action', 'clear_logs');

                fetch('../ajax/admin_action.php', {
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
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan saat mencoba membersihkan log.');
                });
            }
        });
    }
});
</script>

<?php include_once __DIR__ . '/layout/footer.php'; ?>
