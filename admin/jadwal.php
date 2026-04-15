<?php
$page_title = 'Jadwal Ujian Praktik';
include_once __DIR__ . '/layout/header.php';

$db = new Database();

// Buat tabel jika belum ada (auto-migration)
$db->query("CREATE TABLE IF NOT EXISTS jadwal_praktik (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    ploting_id  INT NOT NULL,
    tanggal     DATE NOT NULL,
    jam_mulai   TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    ruangan     VARCHAR(100) NOT NULL,
    keterangan  TEXT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ploting_id) REFERENCES ploting_penguji(id) ON DELETE CASCADE,
    UNIQUE KEY unique_ploting (ploting_id)
) ENGINE=InnoDB");
$db->execute();

// Ambil semua ploting + info jadwal (jika ada)
$db->query("SELECT pp.id as ploting_id,
                   g.nama_lengkap as nama_guru,
                   m.nama_mapel,
                   COUNT(ps.id) as jumlah_siswa,
                   j.tanggal, j.jam_mulai, j.jam_selesai, j.ruangan, j.keterangan
            FROM ploting_penguji pp
            JOIN guru g   ON pp.guru_id   = g.id
            JOIN mapel m  ON pp.mapel_id  = m.id
            LEFT JOIN ploting_siswa ps ON ps.ploting_id = pp.id
            LEFT JOIN jadwal_praktik j ON j.ploting_id  = pp.id
            GROUP BY pp.id, g.nama_lengkap, m.nama_mapel,
                     j.tanggal, j.jam_mulai, j.jam_selesai, j.ruangan, j.keterangan
            ORDER BY j.tanggal ASC, j.jam_mulai ASC, m.nama_mapel ASC");
$plotings = $db->resultSet();

$count_terjadwal = count(array_filter($plotings, fn($p) => !empty($p['tanggal'])));
$count_belum     = count($plotings) - $count_terjadwal;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Manajemen /</span> Jadwal Ujian Praktik</h4>
    <div class="d-flex gap-2">
        <a href="cetak_daftar_hadir.php" target="_blank" class="btn btn-outline-success">
            <i class="bx bx-list-check me-1"></i> Cetak Semua Daftar Hadir
        </a>
        <a href="cetak_jadwal.php" target="_blank" class="btn btn-outline-primary">
            <i class="bx bx-printer me-1"></i> Cetak Jadwal
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4 g-3">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small mb-1">Total Penguji</div>
                <div class="fw-bold fs-4 text-dark"><?= count($plotings) ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small mb-1">Sudah Terjadwal</div>
                <div class="fw-bold fs-4 text-success"><?= $count_terjadwal ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small mb-1">Belum Terjadwal</div>
                <div class="fw-bold fs-4 <?= $count_belum > 0 ? 'text-danger' : 'text-muted' ?>"><?= $count_belum ?></div>
            </div>
        </div>
    </div>
</div>

<?php if (empty($plotings)): ?>
<!-- Empty State -->
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <div class="mb-3">
            <i class="bx bx-calendar-x" style="font-size: 4rem; color: #c0c0c0;"></i>
        </div>
        <h5 class="text-muted mb-2">Belum Ada Penguji yang Di-plot</h5>
        <p class="text-muted mb-4" style="max-width:420px; margin:0 auto;">
            Jadwal ujian praktik dibuat berdasarkan <strong>Ploting Penguji</strong>.
            Tambahkan guru penguji beserta siswa yang diuji di halaman Ploting terlebih dahulu,
            lalu kembali ke halaman ini untuk mengatur jadwalnya.
        </p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="<?= base_url('admin/ploting.php') ?>" class="btn btn-primary">
                <i class="bx bx-git-repo-forked me-1"></i> Buka Ploting Penguji
            </a>
            <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                <i class="bx bx-refresh me-1"></i> Refresh
            </button>
        </div>
    </div>
</div>

<?php else: ?>

<?php if ($count_belum > 0): ?>
<div class="alert alert-warning d-flex align-items-center mb-4 py-2" style="font-size:.875rem;">
    <i class="bx bx-info-circle me-2 fs-5"></i>
    <span>Ada <strong><?= $count_belum ?> penguji</strong> yang belum memiliki jadwal. Klik tombol <strong>Atur</strong> pada baris yang berstatus <em>Belum</em> untuk menetapkan tanggal, jam, dan ruangan.</span>
</div>
<?php endif; ?>

<!-- Tabel Jadwal -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable" id="jadwalTable">
                <thead>
                    <tr>
                        <th>Guru Penguji</th>
                        <th>Mata Pelajaran</th>
                        <th>Siswa</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Ruangan</th>
                        <th>Status</th>
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
                        <td><span class="badge bg-label-secondary"><?= $p['jumlah_siswa'] ?> siswa</span></td>
                        <td>
                            <?php if ($p['tanggal']): ?>
                                <?php
                                    $tgl = new DateTime($p['tanggal']);
                                    $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][$tgl->format('w')];
                                    echo $hari . ', ' . $tgl->format('d/m/Y');
                                ?>
                            <?php else: ?>
                                <span class="text-muted fst-italic">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($p['jam_mulai']): ?>
                                <?= substr($p['jam_mulai'],0,5) ?> – <?= substr($p['jam_selesai'],0,5) ?>
                            <?php else: ?>
                                <span class="text-muted fst-italic">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $p['ruangan'] ? htmlspecialchars($p['ruangan']) : '<span class="text-muted fst-italic">—</span>' ?></td>
                        <td>
                            <?php if ($p['tanggal']): ?>
                                <span class="badge bg-label-success">Terjadwal</span>
                            <?php else: ?>
                                <span class="badge bg-label-danger">Belum</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-primary atur-btn"
                                        data-ploting-id="<?= $p['ploting_id'] ?>"
                                        data-guru="<?= htmlspecialchars($p['nama_guru']) ?>"
                                        data-mapel="<?= htmlspecialchars($p['nama_mapel']) ?>"
                                        data-tanggal="<?= $p['tanggal'] ?? '' ?>"
                                        data-jam-mulai="<?= substr($p['jam_mulai'] ?? '', 0, 5) ?>"
                                        data-jam-selesai="<?= substr($p['jam_selesai'] ?? '', 0, 5) ?>"
                                        data-ruangan="<?= htmlspecialchars($p['ruangan'] ?? '') ?>"
                                        data-keterangan="<?= htmlspecialchars($p['keterangan'] ?? '') ?>"
                                        title="Atur Jadwal">
                                    <i class="bx bx-calendar-edit me-1"></i>
                                    <?= $p['tanggal'] ? 'Edit' : 'Atur' ?>
                                </button>
                                <?php if ($p['tanggal']): ?>
                                <a href="cetak_daftar_hadir.php?ploting_id=<?= $p['ploting_id'] ?>" target="_blank"
                                   class="btn btn-sm btn-outline-success" title="Cetak Daftar Hadir">
                                    <i class="bx bx-list-check"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger hapus-btn"
                                        data-ploting-id="<?= $p['ploting_id'] ?>"
                                        data-guru="<?= htmlspecialchars($p['nama_guru']) ?>"
                                        title="Hapus Jadwal">
                                    <i class="bx bx-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Atur Jadwal -->
<div class="modal fade" id="aturJadwalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="aturJadwalForm">
            <input type="hidden" name="ploting_id" id="modal_ploting_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-calendar-edit me-2 text-primary"></i>
                        Atur Jadwal — <span id="modal_guru_label" class="fw-bold text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 mb-3" style="font-size:.85rem;">
                        <i class="bx bx-info-circle me-1"></i>
                        Mata Pelajaran: <strong id="modal_mapel_label"></strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Ujian <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" id="modal_tanggal" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" id="modal_jam_mulai" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_selesai" id="modal_jam_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="ruangan" id="modal_ruangan" class="form-control" placeholder="Contoh: Lab IPA, Kelas IX-A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan (Opsional)</label>
                        <textarea name="keterangan" id="modal_keterangan" class="form-control" rows="2" placeholder="Misal: Bawa alat praktik sendiri"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanJadwal">
                        <i class="bx bx-save me-1"></i> Simpan Jadwal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php endif; // end else (plotings not empty) ?>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Buka Modal Atur Jadwal ────────────────────────────────────────────
    document.querySelectorAll('.atur-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('modal_ploting_id').value   = this.dataset.plotingId;
            document.getElementById('modal_guru_label').innerText  = this.dataset.guru;
            document.getElementById('modal_mapel_label').innerText = this.dataset.mapel;
            document.getElementById('modal_tanggal').value       = this.dataset.tanggal    || '';
            document.getElementById('modal_jam_mulai').value     = this.dataset.jamMulai   || '';
            document.getElementById('modal_jam_selesai').value   = this.dataset.jamSelesai || '';
            document.getElementById('modal_ruangan').value       = this.dataset.ruangan    || '';
            document.getElementById('modal_keterangan').value    = this.dataset.keterangan || '';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('aturJadwalModal')).show();
        });
    });

    // ── Submit Jadwal ─────────────────────────────────────────────────────
    document.getElementById('aturJadwalForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnSimpanJadwal');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

        const fd = new FormData(this);
        fd.append('action', 'save');

        fetch('../ajax/jadwal_action.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-save me-1"></i> Simpan Jadwal';
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert('Gagal: ' + data.message);
                }
            });
    });

    // ── Hapus Jadwal ──────────────────────────────────────────────────────
    document.querySelectorAll('.hapus-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!confirm(`Hapus jadwal untuk "${this.dataset.guru}"?`)) return;
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('ploting_id', this.dataset.plotingId);
            fetch('../ajax/jadwal_action.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') location.reload();
                    else alert('Gagal: ' + data.message);
                });
        });
    });
});
</script>

<?php include_once __DIR__ . '/layout/footer.php'; ?>
