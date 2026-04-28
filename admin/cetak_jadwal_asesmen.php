<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';
Auth::restrictTo('admin');

$jadwal = [
    [
        'hari' => 'Senin',
        'tanggal' => '04 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '08.00 – 09.30', 'nama' => 'Sejarah Kebudayaan Islam'],
            ['jam_ke' => '2', 'waktu' => '10.00 – 11.30', 'nama' => 'Bahasa Arab']
        ]
    ],
    [
        'hari' => 'Selasa',
        'tanggal' => '05 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.30 – 09.00', 'nama' => 'Bahasa Sunda'],
            ['jam_ke' => '2', 'waktu' => '09.15 – 10.45', 'nama' => 'Matematika'],
            ['jam_ke' => '3', 'waktu' => '11.00 – 12.30', 'nama' => 'Akidah-Akhlak']
        ]
    ],
    [
        'hari' => 'Rabu',
        'tanggal' => '06 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.30 – 09.00', 'nama' => 'Ilmu Pengetahuan Alam'],
            ['jam_ke' => '2', 'waktu' => '09.15 – 10.45', 'nama' => 'Fiqih'],
            ['jam_ke' => '3', 'waktu' => '11.00 – 12.30', 'nama' => 'Ilmu Pengetahuan Sosial']
        ]
    ],
    [
        'hari' => 'Kamis',
        'tanggal' => '07 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.30 – 09.00', 'nama' => 'Al-qur\'an-Hadits'],
            ['jam_ke' => '2', 'waktu' => '09.15 – 10.45', 'nama' => 'Bahasa Inggris'],
            ['jam_ke' => '3', 'waktu' => '11.00 – 12.30', 'nama' => 'Seni Budaya']
        ]
    ],
    [
        'hari' => 'Jum\'at',
        'tanggal' => '08 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.15 – 08.45', 'nama' => 'Pendidikan Pancasila'],
            ['jam_ke' => '2', 'waktu' => '09.00 – 10.30', 'nama' => 'Bahasa Indonesia']
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Jadwal Asesmen Akhir Madrasah — <?= SCHOOL_NAME ?></title>
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
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.08);
            min-height: calc(297mm - 16mm);
        }

        @media print {
            .page {
                width: calc(210mm - 16mm);
                box-shadow: none;
                margin: 0 auto;
                min-height: auto;
            }
        }

        /* ── Kop Surat ── */
        .kop {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .kop img {
            height: 75px;
        }

        .kop-text {
            flex: 1;
            text-align: center;
            line-height: 1.4;
        }

        .kop-text h3 {
            font-size: 13pt;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .kop-text h4 {
            font-size: 10pt;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .kop-text h2 {
            font-size: 15pt;
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
            margin-bottom: 10px;
        }

        .judul h4 {
            font-size: 13pt;
            text-transform: uppercase;
            text-decoration: underline;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;
        }

        .judul p {
            font-size: 14pt;
            text-transform: uppercase;
            color: #333;
            margin-top: 3px;
        }

        /* ── Tabel ── */
        table {
            margin: 0 auto 20px auto;
            border-collapse: collapse;
            font-size: 11pt;
            table-layout: auto;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px 20px;
            vertical-align: middle;
            word-break: break-word;
        }

        table thead th {
            background: #f0f0f0;
            text-align: center;
            font-weight: 700;
        }

        table tbody td {
            min-height: 24px;
        }

        .center {
            text-align: center;
        }

        .nowrap {
            white-space: nowrap;
        }

        /* ── Tanda Tangan ── */
        .ttd {
            margin-top: 15px;
            display: flex;
            justify-content: flex-end;
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
            left: -25px;
            width: 200px;
            z-index: 3;
        }

        /* ── Print / No-print ── */
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
        }

        .no-print button {
            padding: 7px 18px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-size: 13px;
        }

        .no-print .btn-print {
            background: #28a745;
            color: #fff;
        }

        .no-print .btn-back {
            background: #6c757d;
            color: #fff;
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
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Jadwal Asesmen</button>
        <button class="btn-back" onclick="window.close()">✕ Tutup</button>
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
            <h4>Jadwal Asesmen Akhir Madrasah</h4>
            <p>Tahun Pelajaran <?= DEFAULT_YEAR ?></p>
        </div>

        <!-- Tabel Jadwal -->
        <table>
            <thead>
                <tr>
                    <th>Hari, Tanggal</th>
                    <th>Jam Ke</th>
                    <th>Waktu</th>
                    <th>Mata Pelajaran</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jadwal as $hari): ?>
                    <?php
                    $rowspan = count($hari['mapel']);
                    $first = true;
                    ?>
                    <?php foreach ($hari['mapel'] as $mapel): ?>
                        <tr>
                            <?php if ($first): ?>
                                <td rowspan="<?= $rowspan ?>" class="center nowrap" style="vertical-align: middle;">
                                    <?= $hari['hari'] ?>, <?= $hari['tanggal'] ?>
                                </td>
                                <?php $first = false; ?>
                            <?php endif; ?>

                            <td class="center"><?= $mapel['jam_ke'] ?></td>
                            <td class="center nowrap"><?= $mapel['waktu'] ?></td>
                            <td><?= $mapel['nama'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <div class="ttd">
            <div class="ttd-box" style="width: 260px; text-align: left;">
                <p>Cingambul, <?= date('d') ?>
                    <?= ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][(int) date('m')] ?>
                    <?= date('Y') ?>
                </p>
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

</body>

</html>