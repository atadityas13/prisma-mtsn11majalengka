<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';
Auth::restrictTo('admin');

$db = new Database();

// Ambil semua jadwal yang sudah diisi, urut mapel → tanggal → jam
$db->query("SELECT pp.id as ploting_id,
                   g.nama_lengkap  as nama_guru,
                   g.nip,
                   m.nama_mapel,
                   COUNT(ps.id)    as jumlah_siswa,
                   j.tanggal,
                   j.jam_mulai,
                   j.jam_selesai,
                   j.ruangan,
                   j.keterangan
            FROM jadwal_praktik j
            JOIN ploting_penguji pp ON j.ploting_id  = pp.id
            JOIN guru g             ON pp.guru_id     = g.id
            JOIN mapel m            ON pp.mapel_id    = m.id
            LEFT JOIN ploting_siswa ps ON ps.ploting_id = pp.id
            GROUP BY pp.id, g.nama_lengkap, g.nip, m.nama_mapel,
                     j.tanggal, j.jam_mulai, j.jam_selesai, j.ruangan, j.keterangan
            ORDER BY j.tanggal ASC, j.jam_mulai ASC, m.nama_mapel ASC");
$jadwals = $db->resultSet();

$hari_map = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Ujian Praktik — <?= SCHOOL_NAME ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; background: #f0f0f0; color: #000; }

        @page {
            size: A4 portrait;
            margin: 8mm;
        }
        .page {
            width: calc(210mm - 16mm);
            padding: 8mm;
            margin: 0 auto 8mm;
            background: #fff;
            box-shadow: 0 0 8px rgba(0,0,0,0.08);
        }

        /* ── Kop Surat ── */
        .kop { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 18px; }
        .kop img { height: 75px; }
        .kop-text { flex: 1; text-align: center; line-height: 1.4; }
        .kop-text h3 { font-size: 13pt; text-transform: uppercase; margin-bottom: 2px; }
        .kop-text h4 { font-size: 10pt; text-transform: uppercase; margin-bottom: 2px; }
        .kop-text h2 { font-size: 15pt; font-weight: 900; text-transform: uppercase; margin-bottom: 2px; }
        .kop-text p  { font-size: 9pt; }

        /* ── Judul ── */
        .judul { text-align: center; margin-bottom: 16px; }
        .judul h4 { font-size: 13pt; text-transform: uppercase; text-decoration: underline; font-weight: 700; letter-spacing: 1px; margin: 0; }
        .judul p  { font-size: 10pt; color: #333; margin-top: 3px; }

        /* ── Tabel ── */
        table { width: 100%; border-collapse: collapse; font-size: 10pt; table-layout: fixed; }
        table th, table td { border: 1px solid #000; padding: 5px 7px; vertical-align: middle; word-break: break-word; }
        table thead th { background: #f0f0f0; text-align: center; font-weight: 700; }
        table tbody td { min-height: 24px; }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }

        table thead th:first-child { width: 30px; }
        table thead th:nth-child(4) { width: 50px; }
        table thead th:nth-child(5) { width: 110px; }
        table thead th:nth-child(6) { width: 90px; }

        /* ── Tanda Tangan ── */
        .ttd { margin-top: 30px; display: flex; justify-content: flex-end; }
        .ttd-box { width: 260px; text-align: left; }
        .ttd-box .ttd-space { height: 65px; }
        .ttd-box p { margin: 2px 0; font-size: 10.5pt; }

        /* ── Print / No-print ── */
        .no-print {
            position: fixed; top: 0; left: 0; right: 0;
            background: #333; color: #fff; padding: 10px;
            text-align: center; z-index: 999;
            font-family: Arial, sans-serif;
        }
        .no-print button {
            padding: 7px 18px; margin: 0 5px; border: none; border-radius: 4px;
            font-weight: bold; cursor: pointer; font-size: 13px;
        }
        .no-print .btn-print { background: #28a745; color: #fff; }
        .no-print .btn-back  { background: #6c757d; color: #fff; }

        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .page { margin: 0; box-shadow: none; width: auto; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button class="btn-print" onclick="window.print()">🖨️ Cetak Jadwal</button>
    <button class="btn-back"  onclick="window.close()">✕ Tutup</button>
</div>

<div class="page">
    <!-- Kop Surat -->
    <div class="kop">
        <img src="<?= base_url('assets/img/logo-kemenag.png') ?>" alt="Logo Kemenag">
        <div class="kop-text">
            <h3>Kementerian Agama Republik Indonesia</h3>
            <h4>Kantor Kementerian Agama Kabupaten Majalengka</h4>
            <h2><?= SCHOOL_NAME ?></h2>
            <p>Kp. Sindanghurip Desa Maniis Kec. Cingambul Kab. Majalengka<br>
               Telp. (0233) 3600020 &nbsp;|&nbsp; email: mtsn11majalengka@gmail.com</p>
        </div>
        <img src="<?= base_url('assets/img/logo-mtsn11.png') ?>" alt="Logo MTsN 11">
    </div>

    <!-- Judul -->
    <div class="judul">
        <h4>Jadwal Ujian Praktik</h4>
        <p>Tahun Pelajaran <?= DEFAULT_YEAR ?></p>
    </div>

    <!-- Tabel Jadwal -->
    <table>
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th>Mata Pelajaran</th>
                <th>Guru Penguji</th>
                <th class="center" style="width:50px;">Siswa</th>
                <th style="width:110px;">Hari / Tanggal</th>
                <th style="width:90px;">Jam</th>
                <th>Ruangan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($jadwals)): ?>
            <tr>
                <td colspan="8" class="center" style="padding:20px; color:#888;">
                    Belum ada jadwal yang diatur.
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($jadwals as $i => $j): ?>
                <?php
                    $tgl  = new DateTime($j['tanggal']);
                    $hari = $hari_map[$tgl->format('w')];
                ?>
                <tr>
                    <td class="center"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($j['nama_mapel']) ?></td>
                    <td><?= htmlspecialchars($j['nama_guru']) ?></td>
                    <td class="center"><?= $j['jumlah_siswa'] ?></td>
                    <td class="nowrap"><?= $hari ?>, <?= $tgl->format('d/m/Y') ?></td>
                    <td class="center nowrap"><?= substr($j['jam_mulai'],0,5) ?> – <?= substr($j['jam_selesai'],0,5) ?></td>
                    <td><?= htmlspecialchars($j['ruangan']) ?></td>
                    <td><?= htmlspecialchars($j['keterangan'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="ttd">
        <div class="ttd-box">
            <p>Cingambul, <?= date('d') ?> <?= ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][(int)date('m')] ?> <?= date('Y') ?></p>
            <p>Plt. Kepala Madrasah,</p>
            <div class="ttd-space"></div>
            <p><strong><u>H. Dede Apip Mustopa, S.Ag.</u></strong></p>
            <p>NIP. 196801171992031002<p>
        </div>
    </div>
</div>

</body>
</html>
