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
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; background: #eee; color: #000; }

        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        .page {
            width: calc(210mm - 16mm);
            padding: 8mm;
            margin: 0 auto 8mm;
            background: #fff;
            box-shadow: 0 0 5px rgba(0,0,0,.08);
            page-break-after: always;
        }
        .page:last-of-type { page-break-after: auto; }

        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .header-table td {
            vertical-align: middle;
            text-align: center;
            border: none !important;
        }

        .logo-left, .logo-right { width: 70px; }
        .logo-left { text-align: left !important; }
        .logo-right { text-align: right !important; }

        /* ── Judul ── */
        .judul { text-align: center; margin-bottom: 20px; }
        .judul h4 { font-size: 13pt; text-transform: uppercase; text-decoration: underline; font-weight: 700; margin: 0; }

        /* ── Info Grid ── */
        .info-rows {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 24px;
            margin-bottom: 15px;
        }

        .info-column {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            min-width: 0;
        }

        .info-label {
            flex: 0 0 120px;
            font-weight: 600;
            white-space: nowrap;
        }

        .info-separator {
            width: 10px;
            flex: 0 0 auto;
        }

        .info-value {
            flex: 1 1 auto;
            min-width: 0;
            word-break: break-word;
        }

        /* ── Tabel Hadir ── */
        table.hadir { width: 100%; border-collapse: collapse; font-size: 10pt; table-layout: fixed; margin-bottom: 20px; }
        table.hadir th, table.hadir td { border: 1px solid #000; padding: 6px 7px; vertical-align: middle; text-align: center; }
        table.hadir thead th { background: #f9f9f9; font-weight: 700; }
        table.hadir td.left { text-align: left; padding-left: 10px; }
        
        table.hadir th:nth-child(1), table.hadir td:nth-child(1) { width: 35px; }
        table.hadir th:nth-child(2), table.hadir td:nth-child(2) { width: 130px; }
        table.hadir th:nth-child(3), table.hadir td:nth-child(3) { width: auto; }
        table.hadir th:nth-child(4), table.hadir td:nth-child(4) { width: 60px; }
        table.hadir th:nth-child(5), table.hadir td:nth-child(5) { width: 100px; }

        /* ── TTD ── */
        .footer { margin-top: 24px; }
        .ttd-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; }
        .ttd-box { width: 220px; text-align: left; }
        .ttd-space { height: 60px; }
        .ttd-box p { margin: 2px 0; font-size: 10.5pt; }

        /* ── Print ── */
        .no-print {
            position: fixed; top: 0; left: 0; right: 0;
            background: #333; color: #fff; padding: 12px;
            text-align: center; z-index: 1000;
        }
        .no-print button {
            padding: 8px 16px; margin: 0 5px; border: none;
            border-radius: 4px; font-weight: bold; cursor: pointer;
        }
        .btn-print { background: #28a745; color: #fff; }
        .btn-back  { background: #6c757d; color: #fff; }

        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .page { margin: 0; box-shadow: none; width: auto; }
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
    <table class="header-table">
        <tr>
            <td class="logo-left">
                <img src="<?= base_url('assets/img/logo-kemenag.png') ?>" height="70" alt="Logo Kemenag">
            </td>
            <td>
                <h4 style="margin:0; font-weight: bold;">KEMENTERIAN AGAMA REPUBLIK INDONESIA</h4>
                <h4 style="margin:0; font-weight: bold;">KANTOR KEMENTERIAN AGAMA KABUPATEN MAJALENGKA</h4>
                <h3 style="margin:5px 0; font-weight: bold; text-transform: uppercase;"><?= SCHOOL_NAME ?></h3>
                <p style="font-size: 10pt; margin:0;">Kp. Sindanghurip Desa Maniis Kec. Cingambul Kab. Majalengka
                    <br> Telp. (0233) 3600020 email: mtsn11majalengka@gmail.com
                </p>
            </td>
            <td class="logo-right">
                <img src="<?= base_url('assets/img/logo-mtsn11.png') ?>" height="70" alt="Logo MTsN 11">
            </td>
        </tr>
    </table>

    <!-- Judul -->
    <div class="judul">
        <h4>Daftar Hadir Ujian Praktik</h4>
    </div>

    <!-- Info -->
    <div class="info-rows">
        <div class="info-column">
            <div class="info-item">
                <span class="info-label">Mata Pelajaran</span>
                <span class="info-separator">:</span>
                <span class="info-value"><strong><?= htmlspecialchars($j['nama_mapel']) ?></strong></span>
            </div>
            <div class="info-item">
                <span class="info-label">Guru Penguji</span>
                <span class="info-separator">:</span>
                <span class="info-value"><?= htmlspecialchars($j['nama_guru']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Tahun Pelajaran</span>
                <span class="info-separator">:</span>
                <span class="info-value"><?= DEFAULT_YEAR ?></span>
            </div>
        </div>
        <div class="info-column">
            <div class="info-item">
                <span class="info-label">Hari/Tanggal</span>
                <span class="info-separator">:</span>
                <span class="info-value"><?= $tgl_fmt ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Waktu</span>
                <span class="info-separator">:</span>
                <span class="info-value"><?= $jam_fmt ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Ruangan</span>
                <span class="info-separator">:</span>
                <span class="info-value"><?= htmlspecialchars($j['ruangan']) ?></span>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Hadir -->
    <table class="hadir">
        <thead>
            <tr>
                <th>No</th>
                <th>No. Peserta</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Tanda Tangan</th>
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
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($s['nomor_peserta'] ?? '-') ?></td>
                    <td class="left"><?= htmlspecialchars($s['nama_lengkap']) ?></td>
                    <td><?= htmlspecialchars($s['kelas']) ?></td>
                    <td></td><!-- kolom tanda tangan -->
                </tr>
                <?php endforeach; ?>
                <!-- Baris kosong extra jika siswa sedikit -->
                <?php if (count($siswas) < 10): 
                    for ($x = count($siswas); $x < 10; $x++): ?>
                    <tr><td><?= $x + 1 ?></td><td></td><td></td><td></td><td></td></tr>
                <?php endfor; endif; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="footer">
        <div class="ttd-row">
            <div class="ttd-box">
                <p>Mengetahui,</p>
                <p>Plt. Kepala Madrasah,</p>
                <div class="ttd-space"></div>
                <p><strong><u>H. Dede Apip Mustopa, S.Ag.</u></strong></p>
                <p>NIP. 196801171992031002</p>
            </div>
            <div class="ttd-box">
                <p>Cingambul, <?= $tgl_obj->format('d') . ' ' . $bulan_map[(int)$tgl_obj->format('m')] . ' ' . $tgl_obj->format('Y') ?></p>
                <p>Guru Penguji,</p>
                <div class="ttd-space"></div>
                <p><strong><u><?= htmlspecialchars($j['nama_guru']) ?></u></strong></p>
                <p>NIP. <?= htmlspecialchars($j['nip']) ?></p>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

</body>
</html>
