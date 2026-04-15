<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';
Auth::restrictTo('admin');

$db = new Database();

// Bisa cetak 1 penguji (ploting_id) atau semua yang sudah terjadwal
$ploting_id = (int)($_GET['ploting_id'] ?? 0);

if ($ploting_id) {
    // Cetak 1 penguji
    $db->query("SELECT pp.id as ploting_id,
                       g.nama_lengkap  as nama_guru,
                       g.nip,
                       m.nama_mapel,
                       j.tanggal, j.jam_mulai, j.jam_selesai, j.ruangan, j.keterangan
                FROM jadwal_praktik j
                JOIN ploting_penguji pp ON j.ploting_id = pp.id
                JOIN guru g             ON pp.guru_id    = g.id
                JOIN mapel m            ON pp.mapel_id   = m.id
                WHERE j.ploting_id = :pid");
    $db->bind(':pid', $ploting_id);
    $jadwals = $db->resultSet();
} else {
    // Cetak semua yang sudah terjadwal
    $db->query("SELECT pp.id as ploting_id,
                       g.nama_lengkap  as nama_guru,
                       g.nip,
                       m.nama_mapel,
                       j.tanggal, j.jam_mulai, j.jam_selesai, j.ruangan, j.keterangan
                FROM jadwal_praktik j
                JOIN ploting_penguji pp ON j.ploting_id = pp.id
                JOIN guru g             ON pp.guru_id    = g.id
                JOIN mapel m            ON pp.mapel_id   = m.id
                ORDER BY m.nama_mapel ASC, j.tanggal ASC, j.jam_mulai ASC");
    $jadwals = $db->resultSet();
}

$hari_map  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$bulan_map = ['','Januari','Februari','Maret','April','Mei','Juni',
              'Juli','Agustus','September','Oktober','November','Desember'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Ujian Praktik — <?= SCHOOL_NAME ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; background: #f0f0f0; }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 20mm;
            margin: 10mm auto;
            background: #fff;
            box-shadow: 0 0 8px rgba(0,0,0,.15);
            page-break-after: always;
        }
        .page:last-of-type { page-break-after: auto; }

        /* ── Kop ── */
        .kop { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 14px; }
        .kop img { height: 72px; }
        .kop-text { flex: 1; text-align: center; line-height: 1.45; }
        .kop-text h3 { font-size: 12pt; text-transform: uppercase; }
        .kop-text h2 { font-size: 14pt; font-weight: 900; text-transform: uppercase; }
        .kop-text p  { font-size: 9pt; }

        /* ── Judul ── */
        .judul { text-align: center; margin-bottom: 14px; }
        .judul h4 { font-size: 13pt; text-transform: uppercase; text-decoration: underline; font-weight: 700; letter-spacing: .5px; }

        /* ── Info Grid ── */
        .info-table { width: 100%; margin-bottom: 14px; border-collapse: collapse; font-size: 10.5pt; }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .info-table td:nth-child(2) { width: 12px; text-align: center; }
        .info-label { width: 150px; }

        /* ── Tabel Hadir ── */
        table.hadir { width: 100%; border-collapse: collapse; font-size: 10pt; table-layout: fixed; }
        table.hadir th, table.hadir td { border: 1px solid #000; padding: 5px 7px; vertical-align: middle; }
        table.hadir thead th { background: #f0f0f0; text-align: center; font-weight: 700; }
        table.hadir tbody td.center { text-align: center; }
        table.hadir tbody tr { height: 32px; } /* ruang tanda tangan */
        table.hadir th:nth-child(2), table.hadir td:nth-child(2) {
            width: 140px;
            white-space: nowrap;
            overflow: hidden;
        }
        table.hadir th:nth-child(3), table.hadir td:nth-child(3) {
            width: auto;
        }
        table.hadir th:nth-child(4) { width: 55px; }
        table.hadir th:nth-child(5) { width: 90px; }

        /* ── TTD ── */
        .ttd { margin-top: 24px; display: flex; justify-content: space-between; }
        .ttd-box { width: 240px; text-align: center; }
        .ttd-box .ttd-space { height: 65px; }
        .ttd-box p { margin: 2px 0; font-size: 10.5pt; }

        /* ── Print ── */
        .no-print {
            position: fixed; top: 0; left: 0; right: 0;
            background: #333; color: #fff; padding: 10px;
            text-align: center; z-index: 999;
            font-family: Arial, sans-serif; font-size: 13px;
        }
        .no-print button {
            padding: 7px 18px; margin: 0 5px; border: none;
            border-radius: 4px; font-weight: bold; cursor: pointer;
        }
        .btn-print { background: #28a745; color: #fff; }
        .btn-back  { background: #6c757d; color: #fff; }

        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .page { margin: 0; box-shadow: none; width: 100%; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button class="btn-print" onclick="window.print()">🖨️ Cetak Daftar Hadir</button>
    <button class="btn-back"  onclick="window.close()">✕ Tutup</button>
</div>

<?php if (empty($jadwals)): ?>
<div class="page" style="display:flex; align-items:center; justify-content:center;">
    <div style="text-align:center; color:#888;">
        <p style="font-size:16pt;">⚠️</p>
        <p>Tidak ada jadwal yang tersedia untuk dicetak.</p>
        <p style="font-size:9pt; margin-top:8px;">Atur jadwal penguji terlebih dahulu di halaman Jadwal Praktik.</p>
    </div>
</div>
<?php endif; ?>

<?php foreach ($jadwals as $j):
    // Ambil daftar siswa untuk ploting ini
    $db->query("SELECT s.nomor_peserta, s.nama_lengkap, s.kelas, s.nisn
                FROM ploting_siswa ps
                JOIN siswa s ON ps.siswa_id = s.id
                WHERE ps.ploting_id = :pid
                ORDER BY s.nomor_peserta ASC");
    $db->bind(':pid', $j['ploting_id']);
    $siswas = $db->resultSet();

    $tgl_obj  = new DateTime($j['tanggal']);
    $hari     = $hari_map[$tgl_obj->format('w')];
    $tgl_fmt  = $hari . ', ' . $tgl_obj->format('d') . ' ' . $bulan_map[(int)$tgl_obj->format('m')] . ' ' . $tgl_obj->format('Y');
    $jam_fmt  = substr($j['jam_mulai'],0,5) . ' – ' . substr($j['jam_selesai'],0,5) . ' WIB';
?>
<div class="page">
    <!-- Kop Surat -->
    <div class="kop">
        <img src="<?= base_url('assets/img/logo-kemenag.png') ?>" alt="Logo Kemenag">
        <div class="kop-text">
            <h3>Kementerian Agama Republik Indonesia</h3>
            <h3>Kantor Kementerian Agama Kabupaten Majalengka</h3>
            <h2><?= SCHOOL_NAME ?></h2>
            <p>Kp. Sindanghurip Desa Maniis Kec. Cingambul Kab. Majalengka<br>
               Telp. (0233) 3600020 &nbsp;|&nbsp; mtsn11majalengka@gmail.com</p>
        </div>
        <img src="<?= base_url('assets/img/logo-mtsn11.png') ?>" alt="Logo MTsN 11">
    </div>

    <!-- Judul -->
    <div class="judul">
        <h4>Daftar Hadir Ujian Praktik</h4>
    </div>

    <!-- Info -->
    <table class="info-table">
        <tr>
            <td class="info-label">Mata Pelajaran</td>
            <td>:</td>
            <td><strong><?= htmlspecialchars($j['nama_mapel']) ?></strong></td>
            <td class="info-label" style="padding-left:24px;">Tanggal</td>
            <td>:</td>
            <td><?= $tgl_fmt ?></td>
        </tr>
        <tr>
            <td class="info-label">Guru Penguji</td>
            <td>:</td>
            <td><?= htmlspecialchars($j['nama_guru']) ?></td>
            <td class="info-label" style="padding-left:24px;">Waktu</td>
            <td>:</td>
            <td><?= $jam_fmt ?></td>
        </tr>
        <tr>
            <td class="info-label">Tahun Pelajaran</td>
            <td>:</td>
            <td><?= DEFAULT_YEAR ?></td>
            <td class="info-label" style="padding-left:24px;">Ruangan</td>
            <td>:</td>
            <td><?= htmlspecialchars($j['ruangan']) ?></td>
        </tr>
        <?php if ($j['keterangan']): ?>
        <tr>
            <td class="info-label">Keterangan</td>
            <td>:</td>
            <td colspan="4" style="font-style:italic;"><?= htmlspecialchars($j['keterangan']) ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <!-- Tabel Daftar Hadir -->
    <table class="hadir">
        <thead>
            <tr>
                <th style="width:32px;">No</th>
                <th style="width:140px;">No. Peserta</th>
                <th>Nama Siswa</th>
                <th style="width:55px;">Kelas</th>
                <th style="width:90px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($siswas)): ?>
            <tr>
                <td colspan="5" class="center" style="color:#888; font-style:italic; padding:12px;">
                    Belum ada siswa yang di-assign ke penguji ini.
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($siswas as $i => $s): ?>
                <tr>
                    <td class="center"><?= $i + 1 ?></td>
                    <td class="center"><?= htmlspecialchars($s['nomor_peserta'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($s['nama_lengkap']) ?></td>
                    <td class="center"><?= htmlspecialchars($s['kelas']) ?></td>
                    <td></td><!-- kolom tanda tangan -->
                </tr>
                <?php endforeach; ?>
                <!-- Baris kosong extra jika siswa sedikit -->
                <?php for ($x = count($siswas); $x < 5; $x++): ?>
                <tr><td class="center"><?= $x + 1 ?></td><td></td><td></td><td></td><td></td></tr>
                <?php endfor; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="ttd">
        <div class="ttd-box" style="text-align:left;">
            <p>Mengetahui,</p>
            <p>Kepala Madrasah</p>
            <div class="ttd-space"></div>
            <p><strong><u>.................................................</u></strong></p>
            <p>NIP. ..........................................</p>
        </div>
        <div class="ttd-box" style="text-align:left;">
            <p>Cingambul, <?= $tgl_obj->format('d') . ' ' . $bulan_map[(int)$tgl_obj->format('m')] . ' ' . $tgl_obj->format('Y') ?></p>
            <p>Guru Penguji,</p>
            <div class="ttd-space"></div>
            <p><strong><u><?= htmlspecialchars($j['nama_guru']) ?></u></strong></p>
            <p>NIP. <?= htmlspecialchars($j['nip']) ?></p>
        </div>
    </div>
</div>
<?php endforeach; ?>

</body>
</html>
