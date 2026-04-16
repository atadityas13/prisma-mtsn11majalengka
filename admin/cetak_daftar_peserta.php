<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';
Auth::restrictTo('admin');

$db = new Database();

// Bisa cetak per ploting_id atau semua
$ploting_id = (int) ($_GET['ploting_id'] ?? 0);

if ($ploting_id) {
    $db->query("SELECT pp.id as ploting_id, m.nama_mapel, j.tanggal, j.ruangan
                FROM jadwal_praktik j
                JOIN ploting_penguji pp ON j.ploting_id = pp.id
                JOIN mapel m            ON pp.mapel_id   = m.id
                WHERE j.ploting_id = :pid");
    $db->bind(':pid', $ploting_id);
    $jadwals = $db->resultSet();
} else {
    $db->query("SELECT pp.id as ploting_id, m.nama_mapel, j.tanggal, j.ruangan
                FROM jadwal_praktik j
                JOIN ploting_penguji pp ON j.ploting_id = pp.id
                JOIN mapel m            ON pp.mapel_id   = m.id
                ORDER BY j.tanggal ASC, j.ruangan ASC");
    $jadwals = $db->resultSet();
}

$hari_map = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulan_map = [
    '',
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember'
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Peserta Ujian Praktik — <?= SCHOOL_NAME ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            background: #f0f0f0;
            color: #000;
        }

        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        .page {
            width: calc(210mm - 16mm);
            padding: 8mm;
            margin: 0 auto 8mm;
            background: #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, .08);
            page-break-after: always;
        }

        .page:last-of-type {
            page-break-after: auto;
        }

        /* ── Kop ── */
        .kop {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .kop img {
            height: 72px;
        }

        .kop-text {
            flex: 1;
            text-align: center;
            line-height: 1.45;
        }

        .kop-text h3 {
            font-size: 12pt;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .kop-text h2 {
            font-size: 14pt;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .kop-text p {
            font-size: 9pt;
        }

        /* ── Judul ── */
        .judul {
            text-align: center;
            margin-bottom: 14px;
            line-height: 1.3;
        }

        .judul h3 {
            font-size: 13pt;
            text-transform: uppercase;
            font-weight: 800;
            border-bottom: 1.5px solid #000;
            display: inline-block;
            padding-bottom: 2px;
        }

        .judul p {
            font-size: 11pt;
            font-weight: 700;
            margin-top: 2px;
        }

        /* ── Info ── */
        .info-bar {
            margin-bottom: 10px;
            font-weight: 700;
            font-size: 11pt;
        }

        /* ── Tabel Peserta ── */
        table.peserta {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
            table-layout: fixed;
        }

        table.peserta th,
        table.peserta td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }

        table.peserta thead th {
            background: #f0f0f0;
            text-align: center;
            font-weight: 700;
        }

        table.peserta tbody td.center {
            text-align: center;
        }

        table.peserta th:nth-child(1) {
            width: 40px;
        }

        table.peserta th:nth-child(2) {
            width: 150px;
        }

        table.peserta th:nth-child(3) {
            width: 120px;
        }

        table.peserta th:nth-child(4) {
            width: auto;
        }

        table.peserta th:nth-child(5) {
            width: 80px;
        }

        /* ── TTD Overlay ── */
        .ttd-container {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd-box {
            width: 300px;
            text-align: left;
        }

        .ttd-box p {
            margin: 2px 0;
            font-size: 10.5pt;
            position: relative;
            z-index: 5;
        }

        .sig-overlay {
            position: relative;
            height: 100px;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .img-cap {
            position: absolute;
            top: -30px;
            left: -100px;
            width: 150px;
            z-index: 2;
            opacity: 0.9;
        }

        .img-ttd {
            position: absolute;
            top: 0px;
            left: -20px;
            width: 200px;
            z-index: 3;
        }

        /* ── Print UI ── */
        .no-print {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
            z-index: 999;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .btn-print {
            background: #28a745;
            color: #fff;
            padding: 7px 18px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-back {
            background: #6c757d;
            color: #fff;
            padding: 7px 18px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            margin-left: 10px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .page {
                margin: 0;
                box-shadow: none;
                width: auto;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Daftar Peserta</button>
        <button class="btn-back" onclick="window.close()">✕ Tutup</button>
    </div>

    <?php if (empty($jadwals)): ?>
        <div class="page" style="display:flex; align-items:center; justify-content:center;">
            <div style="text-align:center; color:#888;">
                <p style="font-size:16pt;">⚠️</p>
                <p>Tidak ada data yang tersedia untuk dicetak.</p>
            </div>
        </div>
    <?php endif; ?>

    <?php foreach ($jadwals as $j):
        // List siswa for this ploting
        $db->query("SELECT s.nomor_peserta, s.nama_lengkap, s.kelas, s.nisn
                FROM ploting_siswa ps
                JOIN siswa s ON ps.siswa_id = s.id
                WHERE ps.ploting_id = :pid
                ORDER BY s.nomor_peserta ASC");
        $db->bind(':pid', $j['ploting_id']);
        $siswas = $db->resultSet();

        $tgl_obj = new DateTime($j['tanggal']);
        $tgl_fmt = $tgl_obj->format('d') . ' ' . $bulan_map[(int) $tgl_obj->format('m')] . ' ' . $tgl_obj->format('Y');
        ?>
        <div class="page">
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

            <div class="judul">
                <h3>DAFTAR PESERTA UJIAN PRAKTIK MTsN 11 MAJALENGKA</h3>
                <p>TAHUN PELAJARAN <?= DEFAULT_YEAR ?></p>
            </div>

            <div class="info-bar">
                RUANG : <?= htmlspecialchars($j['ruangan'] ?: '-') ?>
            </div>

            <table class="peserta">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Peserta</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($siswas)): ?>
                        <tr>
                            <td colspan="5" class="center" style="padding:15px; color:#888;">Belum ada peserta.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($siswas as $idx => $s): ?>
                            <tr>
                                <td class="center"><?= $idx + 1 ?></td>
                                <td class="center"><?= htmlspecialchars($s['nomor_peserta']) ?></td>
                                <td class="center"><?= htmlspecialchars($s['nisn']) ?></td>
                                <td><?= htmlspecialchars($s['nama_lengkap']) ?></td>
                                <td class="center"><?= htmlspecialchars($s['kelas']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="ttd-container">
                <div class="ttd-box">
                    <p>Cingambul, <?= $tgl_fmt ?></p>
                    <p>Plt. Kepala Madrasah,</p>
                    <div class="sig-overlay">
                        <img src="<?= base_url('assets/img/cap.png') ?>" class="img-cap">
                        <img src="<?= base_url('assets/img/ttd-kepala.png') ?>" class="img-ttd">
                    </div>
                    <p><strong><u>H. Dede Apip Mustopa, S.Ag.</u></strong></p>
                    <p>NIP. 196801171992031002</p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</body>

</html>