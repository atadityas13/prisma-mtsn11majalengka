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
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; background: #eee; color: #000; }

        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        .page {
            width: calc(297mm - 16mm);
            padding: 8mm;
            margin: 0 auto 8mm;
            background: white;
            box-shadow: 0 0 5px rgba(0,0,0,0.08);
        }

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
        .judul h4 { font-size: 14pt; text-transform: uppercase; text-decoration: underline; font-weight: 700; margin: 0; }
        .judul p  { font-size: 11pt; margin-top: 5px; }

        /* ── Tabel ── */
        table.data-table { width: 100%; border-collapse: collapse; font-size: 10.5pt; table-layout: fixed; margin-bottom: 25px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 8px 10px; vertical-align: middle; word-break: break-word; }
        table.data-table thead th { background: #f9f9f9; text-align: center; font-weight: 700; text-transform: uppercase; }
        
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }

        /* Columns */
        table.data-table th:nth-child(1) { width: 40px; }
        table.data-table th:nth-child(2) { width: 22%; }
        table.data-table th:nth-child(3) { width: 22%; }
        table.data-table th:nth-child(4) { width: 18%; }
        table.data-table th:nth-child(5) { width: 120px; }
        table.data-table th:nth-child(6) { width: auto; }

        /* ── Tanda Tangan ── */
        .footer { margin-top: 30px; display: flex; justify-content: flex-end; }
        .ttd-box { width: 260px; text-align: left; }
        .ttd-space { height: 65px; }
        .ttd-box p { margin: 2px 0; font-size: 11pt; }

        /* ── Print / No-print ── */
        .no-print {
            position: fixed; top: 0; left: 0; right: 0;
            background: #333; color: #fff; padding: 12px;
            text-align: center; z-index: 1000;
        }
        .no-print button {
            padding: 8px 16px; margin: 0 5px; border: none; border-radius: 4px;
            font-weight: bold; cursor: pointer;
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
    <button class="btn-print" onclick="window.print()">🖨️ Cetak Jadwal</button>
    <button class="btn-back"  onclick="window.close()">✕ Tutup</button>
</div>

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
        <h4>Jadwal Ujian Praktik</h4>
        <p>Tahun Pelajaran <?= DEFAULT_YEAR ?></p>
    </div>

    <!-- Tabel Jadwal -->
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Guru Penguji</th>
                <th>Hari / Tanggal</th>
                <th>Waktu</th>
                <th>Ruangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($jadwals)): ?>
            <tr>
                <td colspan="6" class="center" style="padding:20px; color:#888;">
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
                    <td style="font-weight: 500; font-size: 11pt;"><?= htmlspecialchars($j['nama_mapel']) ?></td>
                    <td><?= htmlspecialchars($j['nama_guru']) ?></td>
                    <td class="nowrap"><?= $hari ?>, <?= $tgl->format('d/m/Y') ?></td>
                    <td class="center nowrap"><?= substr($j['jam_mulai'],0,5) ?> – <?= substr($j['jam_selesai'],0,5) ?> WIB</td>
                    <td><?= htmlspecialchars($j['ruangan']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="footer">
        <div class="ttd-box">
            <p>Cingambul, <?= date('d') ?> <?= ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][(int)date('m')] ?> <?= date('Y') ?></p>
            <p>Plt. Kepala Madrasah,</p>
            <div class="ttd-space"></div>
            <p><strong><u>H. Dede Apip Mustopa, S.Ag.</u></strong></p>
            <p>NIP. 196801171992031002</p>
        </div>
    </div>
</div>

</body>
</html>
