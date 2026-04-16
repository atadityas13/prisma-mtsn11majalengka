<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::restrictTo('admin');

$db = new Database();
$kelas = $_GET['kelas'] ?? '';

// Fetch all Mapels
$db->query("SELECT * FROM mapel ORDER BY id ASC");
$mapels = $db->resultSet();

// Fetch students in this class
$db->query("SELECT * FROM siswa WHERE kelas = :kelas ORDER BY nama_lengkap ASC");
$db->bind(':kelas', $kelas);
$siswas = $db->resultSet();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rapor Praktik - Kelas <?= $kelas ?></title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            padding: 0;
            margin: 0;
            background: #eee;
            color: #000;
            min-height: 100vh;
        }

        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        .page {
            width: calc(210mm - 16mm);
            min-height: calc(297mm - 16mm);
            padding: 10mm 12mm;
            margin: 0 auto 8mm;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.08);
            page-break-after: always;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: middle;
            text-align: center;
            border: none !important;
        }

        .header-table h2,
        .header-table h3,
        .header-table p {
            margin: 2px 0;
        }

        .logo-left {
            width: 70px;
            text-align: left !important;
        }

        .logo-right {
            width: 70px;
            text-align: right !important;
        }

        .title {
            text-align: center;
            margin-bottom: -15px;
            text-transform: uppercase;
        }

        .profile {
            margin-bottom: 20px;
        }

        .profile table {
            width: 100%;
            border: none;
        }

        .profile table td {
            padding: 4px 0;
            vertical-align: top;
        }

        table.nilai {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            table-layout: fixed;
        }

        table.nilai th,
        table.nilai td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            word-break: break-word;
        }

        table.nilai th:first-child,
        table.nilai td:first-child {
            width: 30px;
        }

        table.nilai th:nth-child(2),
        table.nilai td:nth-child(2) {
            width: 30%;
        }

        table.nilai th:nth-child(3),
        table.nilai td:nth-child(3) {
            width: 90px;
        }

        table.nilai th:nth-child(4),
        table.nilai td:nth-child(4) {
            width: 130px;
        }

        table.nilai th:last-child,
        table.nilai td:last-child {
            width: auto;
            /* Take remaining space */
        }

        .text-center {
            text-align: center !important;
        }

        .footer {
            margin-top: 30px;
        }

        .sig-container {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .sig-box {
            width: 300px;
            text-align: left;
            padding-left: 20px;
        }

        .sig-box p {
            margin: 2px 0;
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

        @media print {
            .no-print {
                display: none;
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
    <div class="no-print"
        style="position: fixed; top: 0; left: 0; right: 0; background: #333; color: #fff; padding: 10px; text-align: center; z-index: 1000;">
        <button onclick="window.print()" style="padding: 5px 15px; cursor: pointer;">Cetak Rapor</button>
        <span style="margin-left: 20px;">Mencetak <?= count($siswas) ?> rapor untuk Kelas <?= $kelas ?></span>
    </div>

    <?php foreach ($siswas as $s): ?>
        <div class="page">
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

            <div class="title" style="text-decoration: underline;">
                <h3>HASIL UJIAN PRAKTIK SISWA</h3>
            </div>
            <div style="text-align: center; margin-bottom: 20px;">
                <h4>TAHUN AJARAN <?= DEFAULT_YEAR ?></h4>
            </div>

            <div class="profile">
                <table>
                    <tr>
                        <td width="160">NAMA SISWA</td>
                        <td width="20">:</td>
                        <td><strong><?= strtoupper($s['nama_lengkap']) ?></strong></td>
                    </tr>
                    <tr>
                        <td>NISN</td>
                        <td>:</td>
                        <td><?= $s['nisn'] ?></td>
                    </tr>
                    <tr>
                        <td>NOMOR PESERTA</td>
                        <td>:</td>
                        <td><?= $s['nomor_peserta'] ?></td>
                    </tr>
                    <tr>
                        <td>KELAS</td>
                        <td>:</td>
                        <td><?= $s['kelas'] ?></td>
                    </tr>
                </table>
            </div>

            <table class="nilai">
                <thead>
                    <tr style="background: #f0f0f0;">
                        <th width="40" class="text-center">NO</th>
                        <th>MATA PELAJARAN PRAKTIK</th>
                        <th width="100" class="text-center">NILAI AKHIR</th>
                        <th width="100" class="text-center">PREDIKAT</th>
                        <th width="180">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_nilai = 0;
                    $count_nilai = 0;
                    foreach ($mapels as $idx => $m):
                        // NEW LOGIC: Final Score = Average of Material Averages
                        // 1. Fetch all scores for this student and mapel, grouped by materi
                        $db->query("SELECT n.nilai_angka, a.materi_id 
                                    FROM nilai_praktik n
                                    JOIN aspek_penilaian a ON n.aspek_id = a.id
                                    WHERE n.siswa_id = :sid AND n.mapel_id = :mid");
                        $db->bind(':sid', $s['id']);
                        $db->bind(':mid', $m['id']);
                        $scores_raw = $db->resultSet();

                        $materi_groups = [];
                        foreach ($scores_raw as $sr) {
                            $m_id = $sr['materi_id'] ?? 0;
                            $materi_groups[$m_id][] = $sr['nilai_angka'];
                        }

                        $materi_avgs = [];
                        foreach ($materi_groups as $m_id => $vals) {
                            if (!empty($vals)) {
                                $materi_avgs[] = array_sum($vals) / count($vals);
                            }
                        }

                        // Get count of defined materials to use as base divisor
                        $db->query("SELECT COUNT(*) as c FROM materi_penilaian WHERE mapel_id = :mid");
                        $db->bind(':mid', $m['id']);
                        $defined_m_count = $db->single()['c'];

                        $score = null;
                        if (!empty($materi_avgs)) {
                            // Divisor is at least the number of defined materials
                            // Orphaned aspects groups (m_id=0) should also be counted if they exist
                            $divisor = max($defined_m_count, 1);
                            if (isset($materi_groups[0]) && $defined_m_count > 0) {
                                // If orphaned exists alongside defined materials, we treat orphaned as 1 extra group
                                $divisor += 1;
                            }
                            $score = array_sum($materi_avgs) / $divisor;
                        }

                        $predikat = '-';
                        $keterangan = '-';
                        if (!is_null($score)) {
                            if ($score >= 90) {
                                $predikat = 'A';
                                $keterangan = 'Sangat Baik';
                            } elseif ($score >= 80) {
                                $predikat = 'B';
                                $keterangan = 'Baik';
                            } elseif ($score >= 70) {
                                $predikat = 'C';
                                $keterangan = 'Cukup';
                            } elseif ($score >= 60) {
                                $predikat = 'D';
                                $keterangan = 'Kurang';
                            } elseif ($score >= 0) {
                                $predikat = 'E';
                                $keterangan = 'Mengulang';
                            }

                            $total_nilai += $score;
                            $count_nilai++;
                        }
                        ?>
                        <tr>
                            <td class="text-center"><?= $idx + 1 ?></td>
                            <td><?= $m['nama_mapel'] ?></td>
                            <td class="text-center"><?= !is_null($score) ? round($score, 2) : '-' ?></td>
                            <td class="text-center"><?= $predikat ?></td>
                            <td><?= $keterangan ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="background: #f0f0f0; font-weight: bold;">
                        <td colspan="2" class="text-center">RATA-RATA NILAI</td>
                        <td class="text-center"><?= ($count_nilai > 0) ? round($total_nilai / $count_nilai, 2) : '-' ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>

            <div class="sig-container">
                <div class="sig-box">
                    <p>Mengetahui,</p>
                    <p>Orang Tua/Wali Murid</p>
                    <br><br><br>
                    <p>.......................................</p>
                </div>
                <div class="sig-box">
                    <p>Cingambul, 16 April 2026</p>
                    <p>Plt. Kepala Madrasah,</p>
                    <div class="sig-overlay">
                        <img src="<?= base_url('assets/img/cap.png') ?>" class="img-cap">
                        <img src="<?= base_url('assets/img/ttd-kepala.png') ?>" class="img-ttd">
                    </div>
                    <p><strong><u>H. Dede Apip Mustopa</u></strong></p>
                    <p>NIP. 196801171992031002</p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</body>

</html>