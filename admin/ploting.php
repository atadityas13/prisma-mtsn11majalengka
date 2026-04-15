<?php
$page_title = 'Ploting Penguji';
include_once __DIR__ . '/layout/header.php';

$db = new Database();

// Fetch Gurus with their mapel
$db->query("SELECT g.id, g.nama_lengkap, m.nama_mapel, g.mapel_id 
            FROM guru g 
            JOIN mapel m ON g.mapel_id = m.id 
            ORDER BY g.nama_lengkap ASC");
$gurus = $db->resultSet();

// Fetch ploting list: guru + mapel + jumlah siswa
$db->query("SELECT pp.id, pp.guru_id, pp.mapel_id,
                   g.nama_lengkap as nama_guru,
                   m.nama_mapel,
                   COUNT(ps.id) as jumlah_siswa
            FROM ploting_penguji pp
            JOIN guru g ON pp.guru_id = g.id
            JOIN mapel m ON pp.mapel_id = m.id
            LEFT JOIN ploting_siswa ps ON ps.ploting_id = pp.id
            GROUP BY pp.id, pp.guru_id, pp.mapel_id, g.nama_lengkap, m.nama_mapel
            ORDER BY m.nama_mapel ASC, g.nama_lengkap ASC");
$plotings = $db->resultSet();

// Fetch statistik per mapel
$db->query("SELECT m.id as mapel_id, m.nama_mapel,
                   COUNT(DISTINCT s.id) as total_siswa,
                   COUNT(DISTINCT ps.siswa_id) as sudah_plot
            FROM mapel m
            CROSS JOIN siswa s
            LEFT JOIN ploting_penguji pp ON pp.mapel_id = m.id
            LEFT JOIN ploting_siswa ps ON ps.ploting_id = pp.id AND ps.siswa_id = s.id
            WHERE EXISTS (SELECT 1 FROM guru g WHERE g.mapel_id = m.id)
            GROUP BY m.id, m.nama_mapel
            ORDER BY m.nama_mapel ASC");
$stats_per_mapel = $db->resultSet();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Manajemen /</span> Ploting Penguji</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPlotingModal">
        <i class="bx bx-git-repo-forked me-1"></i> Tambah Penguji
    </button>
</div>

<!-- Summary Cards Per Mapel -->
<?php if (!empty($stats_per_mapel)): ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-5 mb-4 g-3">
        <?php foreach ($stats_per_mapel as $stat):
            $belum = $stat['total_siswa'] - $stat['sudah_plot'];
            $pct = $stat['total_siswa'] > 0 ? round($stat['sudah_plot'] / $stat['total_siswa'] * 100) : 0;
            ?>
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body pb-2">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-label-info text-wrap" style="white-space: normal; word-break: break-word; max-width: 160px;">
                                <?= htmlspecialchars($stat['nama_mapel']) ?>
                            </span>
                            <small class="text-muted"><?= $pct ?>%</small>
                        </div>
                        <div class="progress mb-2" style="height:6px;">
                            <div class="progress-bar <?= $pct == 100 ? 'bg-success' : 'bg-primary' ?>"
                                style="width:<?= $pct ?>%"></div>
                        </div>
                        <div class="d-flex justify-content-between small mt-1">
                            <span class="text-muted">Total: <strong
                                    class="text-dark"><?= $stat['total_siswa'] ?></strong></span>
                            <span class="text-success">Di-plot: <strong><?= $stat['sudah_plot'] ?></strong></span>
                            <span class="<?= $belum > 0 ? 'text-danger' : 'text-muted' ?>">Sisa:
                                <strong><?= $belum ?></strong></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<div class="card">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Guru Penguji</th>
                        <th>Mata Pelajaran</th>
                        <th>Jumlah Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plotings as $p): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            <?= strtoupper(substr($p['nama_guru'], 0, 1)) ?>
                                        </span>
                                    </div>
                                    <strong><?= htmlspecialchars($p['nama_guru']) ?></strong>
                                </div>
                            </td>
                            <td><span class="badge bg-label-info"><?= htmlspecialchars($p['nama_mapel']) ?></span></td>
                            <td>
                                <?php if ($p['jumlah_siswa'] > 0): ?>
                                    <button class="btn btn-sm btn-outline-primary view-siswa-btn"
                                        data-ploting-id="<?= $p['id'] ?>" data-guru="<?= htmlspecialchars($p['nama_guru']) ?>"
                                        data-mapel="<?= htmlspecialchars($p['nama_mapel']) ?>">
                                        <i class="bx bx-group me-1"></i>
                                        <span class="badge bg-primary rounded-pill"><?= $p['jumlah_siswa'] ?></span>
                                        siswa
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted fst-italic small">Belum ada siswa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-success add-more-btn"
                                        data-guru-id="<?= $p['guru_id'] ?>"
                                        data-guru="<?= htmlspecialchars($p['nama_guru']) ?>"
                                        data-mapel="<?= htmlspecialchars($p['nama_mapel']) ?>"
                                        title="Tambah siswa untuk guru ini">
                                        <i class="bx bx-plus"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-plot-btn" data-id="<?= $p['id'] ?>"
                                        title="Hapus seluruh ploting guru ini">
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

<!-- ===== Modal Tambah Penguji ===== -->
<div class="modal fade" id="addPlotingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addPlotingForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-git-repo-forked me-2 text-primary"></i>Tambah Penguji</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Pilih Guru -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Guru Penguji <span class="text-danger">*</span></label>
                        <select id="select_guru" name="guru_id" class="form-select" required>
                            <option value="">-- Pilih Guru Penguji --</option>
                            <?php foreach ($gurus as $g): ?>
                                <option value="<?= $g['id'] ?>" data-mapel="<?= htmlspecialchars($g['nama_mapel']) ?>">
                                    <?= htmlspecialchars($g['nama_lengkap']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Info Mapel (auto-fill) -->
                    <div class="mb-3" id="mapel_info_area" style="display:none;">
                        <label class="form-label fw-semibold">Mata Pelajaran</label>
                        <div class="form-control bg-light" id="mapel_info_text">-</div>
                        <small class="text-muted">Otomatis sesuai mapel guru yang dipilih</small>
                    </div>

                    <!-- Jumlah Siswa -->
                    <div class="mb-3" id="jumlah_area" style="display:none;">
                        <label class="form-label fw-semibold">Jumlah Siswa yang Diuji <span
                                class="text-danger">*</span></label>
                        <input type="number" id="jumlah_siswa" name="jumlah_siswa" class="form-control" min="1"
                            placeholder="Masukkan jumlah siswa" required>
                        <div id="sisa_info" class="mt-2"></div>
                    </div>

                    <div class="alert alert-info py-2 d-none" id="info_alert" style="font-size: 0.82rem;">
                        <i class="bx bx-info-circle me-1"></i>
                        Siswa akan dipilih otomatis secara berurutan berdasarkan <strong>nomor peserta</strong> yang
                        belum memiliki penguji.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanPlot">
                        <i class="bx bx-save me-1"></i> Simpan Ploting
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal Daftar Siswa ===== -->
<div class="modal fade" id="siswaListModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-group me-2 text-primary"></i>
                    Daftar Siswa — <span id="siswa_modal_guru" class="fw-bold"></span>
                    <small class="text-muted ms-1" id="siswa_modal_mapel"></small>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="siswa_list_loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="mt-2 text-muted small">Memuat data siswa...</div>
                </div>
                <div id="siswa_list_content" style="display:none;">
                    <table class="table table-hover mb-0" id="tableSiswaPlot">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>Nama Siswa</th>
                                <th>No. Peserta</th>
                                <th>Kelas</th>
                                <th style="width:120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="siswa_list_tbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <span class="text-muted small me-auto" id="siswa_list_count"></span>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Guru select → tampilkan info mapel & jumlah area ──────────────────
        const selectGuru = document.getElementById('select_guru');
        const mapelArea = document.getElementById('mapel_info_area');
        const mapelText = document.getElementById('mapel_info_text');
        const jumlahArea = document.getElementById('jumlah_area');
        const infoAlert = document.getElementById('info_alert');
        const sisaInfo = document.getElementById('sisa_info');
        const jumlahInput = document.getElementById('jumlah_siswa');

        selectGuru.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (!this.value) {
                mapelArea.style.display = 'none';
                jumlahArea.style.display = 'none';
                infoAlert.classList.add('d-none');
                return;
            }
            mapelText.innerText = opt.dataset.mapel || '-';
            mapelArea.style.display = '';
            jumlahArea.style.display = '';
            infoAlert.classList.remove('d-none');
            sisaInfo.innerHTML = '<span class="spinner-border spinner-border-sm text-secondary"></span> memuat...';

            // Fetch sisa siswa
            fetch('../ajax/ploting_action.php?action=get_sisa&guru_id=' + this.value)
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        const sudah = data.sudah_dimiliki;
                        const sisa = data.sisa;
                        let badge = sisa > 0
                            ? `<span class="badge bg-label-success">${sisa} tersedia</span>`
                            : `<span class="badge bg-label-danger">0 tersedia</span>`;
                        let sudahBadge = sudah > 0
                            ? `<span class="badge bg-label-secondary ms-2">sudah dimiliki: ${sudah}</span>`
                            : '';
                        sisaInfo.innerHTML = `<small class="text-muted">Siswa belum punya penguji: ${badge}${sudahBadge}</small>`;
                        jumlahInput.max = sisa;
                    } else {
                        sisaInfo.innerHTML = '<small class="text-danger">Gagal memuat info sisa siswa.</small>';
                    }
                });
        });

        // ── Reset modal ketika dibuka ulang ───────────────────────────────────
        let pendingGuruId = null; // simpan guru yang dipilih dari tombol "+" di tabel

        document.getElementById('addPlotingModal').addEventListener('show.bs.modal', function () {
            document.getElementById('addPlotingForm').reset();
            mapelArea.style.display = 'none';
            jumlahArea.style.display = 'none';
            infoAlert.classList.add('d-none');
            sisaInfo.innerHTML = '';
        });

        // Terapkan pendingGuruId SETELAH modal selesai ditampilkan (setelah reset)
        document.getElementById('addPlotingModal').addEventListener('shown.bs.modal', function () {
            if (pendingGuruId) {
                selectGuru.value = pendingGuruId;
                selectGuru.dispatchEvent(new Event('change'));
                pendingGuruId = null;
            }
        });

        // ── Add More (dari tabel aksi) ────────────────────────────────────────
        document.querySelectorAll('.add-more-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                pendingGuruId = this.dataset.guruId; // simpan dulu, jangan langsung set
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('addPlotingModal'));
                modal.show();
            });
        });

        // ── Simpan Ploting ────────────────────────────────────────────────────
        document.getElementById('addPlotingForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('btnSimpanPlot');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            const formData = new FormData(this);
            formData.append('action', 'save_plot');

            fetch('../ajax/ploting_action.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bx bx-save me-1"></i> Simpan Ploting';
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert('Gagal: ' + data.message);
                    }
                });
        });

        // ── Hapus seluruh ploting 1 guru ──────────────────────────────────────
        document.querySelectorAll('.delete-plot-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                if (!confirm('Hapus semua ploting guru ini? Seluruh siswa yang di-assign akan dilepas.')) return;
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', this.dataset.id);
                fetch('../ajax/ploting_action.php', { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') location.reload();
                    });
            });
        });

        // ── Lihat daftar siswa (klik kolom Jumlah Siswa) ──────────────────────
        let currentPlotingId = null;

        document.querySelectorAll('.view-siswa-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                currentPlotingId = this.dataset.plotingId;
                document.getElementById('siswa_modal_guru').innerText = this.dataset.guru;
                document.getElementById('siswa_modal_mapel').innerText = '— ' + this.dataset.mapel;

                // Reset modal
                document.getElementById('siswa_list_loading').style.display = '';
                document.getElementById('siswa_list_content').style.display = 'none';
                document.getElementById('siswa_list_count').innerText = '';

                const modal = new bootstrap.Modal(document.getElementById('siswaListModal'));
                modal.show();

                // Fetch data
                fetch('../ajax/ploting_action.php?action=get_siswa_plot&ploting_id=' + currentPlotingId)
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('siswa_list_loading').style.display = 'none';
                        const tbody = document.getElementById('siswa_list_tbody');
                        tbody.innerHTML = '';

                        if (data.status === 'success' && data.siswa.length > 0) {
                            data.siswa.forEach((s, i) => {
                                tbody.insertAdjacentHTML('beforeend', `
                                <tr id="row-ps-${s.ploting_siswa_id}">
                                    <td class="text-muted">${i + 1}</td>
                                    <td><strong>${escHtml(s.nama_lengkap)}</strong></td>
                                    <td><span class="badge bg-label-secondary">${escHtml(s.nomor_peserta || '-')}</span></td>
                                    <td>${escHtml(s.kelas || '-')}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger reset-siswa-btn"
                                                data-ps-id="${s.ploting_siswa_id}"
                                                data-nama="${escHtml(s.nama_lengkap)}"
                                                title="Keluarkan dari penguji ini">
                                            <i class="bx bx-user-minus me-1"></i>Keluarkan
                                        </button>
                                    </td>
                                </tr>
                            `);
                            });
                            document.getElementById('siswa_list_count').innerText = `Total: ${data.siswa.length} siswa`;
                            document.getElementById('siswa_list_content').style.display = '';
                        } else {
                            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada siswa</td></tr>';
                            document.getElementById('siswa_list_content').style.display = '';
                        }
                    });
            });
        });

        // ── Reset 1 siswa dari ploting ────────────────────────────────────────
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.reset-siswa-btn');
            if (!btn) return;

            const psId = btn.dataset.psId;
            const nama = btn.dataset.nama;

            if (!confirm(`Keluarkan "${nama}" dari penguji ini?\nSiswa akan kembali ke status belum punya penguji.`)) return;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            const formData = new FormData();
            formData.append('action', 'reset_siswa');
            formData.append('ploting_siswa_id', psId);

            fetch('../ajax/ploting_action.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Hapus baris dari tabel modal
                        const row = document.getElementById('row-ps-' + psId);
                        if (row) row.remove();

                        // Update counter di modal
                        const remaining = document.querySelectorAll('#siswa_list_tbody tr').length;
                        document.getElementById('siswa_list_count').innerText = `Total: ${remaining} siswa`;

                        // Renomor
                        document.querySelectorAll('#siswa_list_tbody tr').forEach((tr, i) => {
                            tr.cells[0].innerText = i + 1;
                        });
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bx bx-user-minus me-1"></i>Reset';
                        alert('Gagal: ' + data.message);
                    }
                });
        });

        // Helper: escape HTML
        function escHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    });
</script>

<?php include_once __DIR__ . '/layout/footer.php'; ?>