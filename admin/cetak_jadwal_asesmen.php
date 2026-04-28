<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';
Auth::restrictTo('admin');

$jadwal = [
    [
        'hari' => 'Senin',
        'tanggal' => '04 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.00 – 08.30', 'nama' => 'Sejarah Kebudayaan Islam', 'is_break' => false],
            ['jam_ke' => '-', 'waktu' => '08.30 – 08.45', 'nama' => 'ISTIRAHAT', 'is_break' => true],
            ['jam_ke' => '2', 'waktu' => '08.45 – 10.15', 'nama' => 'Bahasa Arab', 'is_break' => false]
        ]
    ],
    [
        'hari' => 'Selasa',
        'tanggal' => '05 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.00 – 08.30', 'nama' => 'Bahasa Sunda', 'is_break' => false],
            ['jam_ke' => '-', 'waktu' => '08.30 – 08.45', 'nama' => 'ISTIRAHAT', 'is_break' => true],
            ['jam_ke' => '2', 'waktu' => '08.45 – 10.15', 'nama' => 'Matematika', 'is_break' => false],
            ['jam_ke' => '-', 'waktu' => '10.15 – 10.30', 'nama' => 'ISTIRAHAT', 'is_break' => true],
            ['jam_ke' => '3', 'waktu' => '10.30 – 12.00', 'nama' => 'Akidah-Akhlak', 'is_break' => false]
        ]
    ],
    [
        'hari' => 'Rabu',
        'tanggal' => '06 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.00 – 08.30', 'nama' => 'Ilmu Pengetahuan Alam', 'is_break' => false],
            ['jam_ke' => '-', 'waktu' => '08.30 – 08.45', 'nama' => 'ISTIRAHAT', 'is_break' => true],
            ['jam_ke' => '2', 'waktu' => '08.45 – 10.15', 'nama' => 'Fiqih', 'is_break' => false],
            ['jam_ke' => '-', 'waktu' => '10.15 – 10.30', 'nama' => 'ISTIRAHAT', 'is_break' => true],
            ['jam_ke' => '3', 'waktu' => '10.30 – 12.00', 'nama' => 'Ilmu Pengetahuan Sosial', 'is_break' => false]
        ]
    ],
    [
        'hari' => 'Kamis',
        'tanggal' => '07 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.00 – 08.30', 'nama' => 'Al-qur\'an-Hadits', 'is_break' => false],
            ['jam_ke' => '-', 'waktu' => '08.30 – 08.45', 'nama' => 'ISTIRAHAT', 'is_break' => true],
            ['jam_ke' => '2', 'waktu' => '08.45 – 10.15', 'nama' => 'Bahasa Inggris', 'is_break' => false],
            ['jam_ke' => '-', 'waktu' => '10.15 – 10.30', 'nama' => 'ISTIRAHAT', 'is_break' => true],
            ['jam_ke' => '3', 'waktu' => '10.30 – 12.00', 'nama' => 'Seni Budaya', 'is_break' => false]
        ]
    ],
    [
        'hari' => 'Jum\'at',
        'tanggal' => '08 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.00 – 08.30', 'nama' => 'Pendidikan Pancasila', 'is_break' => false],
            ['jam_ke' => '-', 'waktu' => '08.30 – 08.45', 'nama' => 'ISTIRAHAT', 'is_break' => true],
            ['jam_ke' => '2', 'waktu' => '08.45 – 10.15', 'nama' => 'Bahasa Indonesia', 'is_break' => false]
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
            font-size: 12pt;
            background: #f0f0f0;
            color: #000;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        .page {
            width: calc(210mm - 20mm);
            padding: 10mm;
            margin: 0 auto 10mm;
            background: #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.08);
            min-height: 297mm;
        }

        @media print {
            .page {
                width: auto;
                min-height: auto;
                margin: 0;
                box-shadow: none;
            }
        }

        /* ── Kop Surat ── */
        .kop {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
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
            font-size: 11pt;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .kop-text h2 {
            font-size: 16pt;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .kop-text p {
            font-size: 10pt;
        }

        /* ── Judul ── */
        .judul {
            text-align: center;
            margin-bottom: 20px;
        }

        .judul h4 {
            font-size: 14pt;
            text-transform: uppercase;
            text-decoration: underline;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;
        }

        .judul p {
            font-size: 12pt;
            color: #333;
            margin-top: 5px;
        }

        /* ── Tabel ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12pt;
            table-layout: fixed;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px 10px;
            vertical-align: middle;
            word-break: break-word;
        }

        table thead th {
            background: #e9ecef;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        table tbody td {
            min-height: 30px;
        }

        .center {
            text-align: center;
        }
        
        .break-row td {
            background: #f8f9fa;
            font-style: italic;
            text-align: center;
        }

        /* Lebar Kolom */
        table thead th:nth-child(1) { width: 30%; } /* Hari, Tanggal */
        table thead th:nth-child(2) { width: 15%; } /* Jam Ke */
        table thead th:nth-child(3) { width: 25%; } /* Waktu */
        table thead th:nth-child(4) { width: 30%; } /* Mata Pelajaran */

        /* ── Tanda Tangan ── */
        .ttd {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd-box p {
            margin: 3px 0;
            font-size: 12pt;
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
            padding: 8px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
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
                        <tr class="<?= $mapel['is_break'] ? 'break-row' : '' ?>">
                            <?php if ($first): ?>
                                <td rowspan="<?= $rowspan ?>" class="center">
                                    <strong><?= $hari['hari'] ?></strong>,<br>
                                    <?= $hari['tanggal'] ?>
                                </td>
                                <?php $first = false; ?>
                            <?php endif; ?>
                            
                            <?php if ($mapel['is_break']): ?>
                                <td colspan="3" class="center">
                                    <strong><?= $mapel['waktu'] ?> — <?= $mapel['nama'] ?></strong>
                                </td>
                            <?php else: ?>
                                <td class="center"><?= $mapel['jam_ke'] ?></td>
                                <td class="center"><?= $mapel['waktu'] ?></td>
                                <td><strong><?= $mapel['nama'] ?></strong></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <div class="ttd">
            <div class="ttd-box" style="width: 280px; text-align: left;">
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
