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
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            padding: 0;
            margin: 0;
            background: #eee;
            color: #000;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        .page {
            width: calc(210mm - 20mm);
            padding: 10mm 15mm;
            margin: 0 auto 10mm;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.08);
            page-break-after: always;
        }
        .page:last-of-type { page-break-after: auto; }

        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        .header-table td {
            vertical-align: middle;
            text-align: center;
            border: none !important;
        }

        .logo-left, .logo-right { width: 75px; }
        .logo-left { text-align: left !important; }
        .logo-right { text-align: right !important; }

        .title {
            text-align: center;
            margin-bottom: 20px;
        }

        .title h3 {
            margin: 0;
            font-size: 14pt;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .profile {
            margin-bottom: 25px;
        }

        .profile table {
            width: 100%;
            border: none;
        }

        .profile table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 11pt;
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
            padding: 8px 10px;
            text-align: left;
            word-break: break-word;
        }

        table.nilai th {
            background: #f9f9f9;
            text-align: center;
            font-weight: 700;
            text-transform: uppercase;
        }

        .text-center { text-align: center !important; }

        .footer {
            margin-top: 40px;
        }

        .sig-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .sig-box {
            width: 250px;
            text-align: left;
        }

        .sig-box p {
            margin: 2px 0;
        }

        .sig-space {
            height: 70px;
        }

        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
            .page { margin: 0; box-shadow: none; width: auto; }
        }

        .btn-print {
            padding: 8px 16px;
            cursor: pointer;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="no-print"
        style="position: fixed; top: 0; left: 0; right: 0; background: #333; color: #fff; padding: 12px; text-align: center; z-index: 1000;">
        <button class="btn-print" onclick="window.print()">🖨️ CETAK RAPOR</button>
        <button class="btn-print" style="margin-left: 10px; background: #6c757d;" onclick="window.close()">TUTUP</button>
        <span style="margin-left: 20px;">Mencetak <?= count($siswas) ?> rapor untuk Kelas <?= $kelas ?></span>
    </div>

    <?php 
    $bulan_map = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    foreach ($siswas as $s): ?>
        <div class="page">
            <!-- Kop Surat -->
            <table class="header-table">
                <tr>
                    <td class="logo-left">
                        <img src="<?= base_url('assets/img/logo-kemenag.png') ?>" height="75" alt="Logo Kemenag">
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
                        <img src="<?= base_url('assets/img/logo-mtsn11.png') ?>" height="75" alt="Logo MTsN 11">
                    </td>
                </tr>
            </table>

            <div class="title">
                <h3>HASIL UJIAN PRAKTIK SISWA</h3>
                <p><strong>TAHUN AJARAN <?= DEFAULT_YEAR ?></strong></p>
            </div>

            <div class="profile">
                <table>
                    <tr>
                        <td width="160">Nama Siswa</td>
                        <td width="20">:</td>
                        <td><strong><?= strtoupper($s['nama_lengkap']) ?></strong></td>
                    </tr>
                    <tr>
                        <td>NISN</td>
                        <td>:</td>
                        <td><?= $s['nisn'] ?></td>
                    </tr>
                    <tr>
                        <td>Nomor Peserta</td>
                        <td>:</td>
                        <td><?= $s['nomor_peserta'] ?></td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>:</td>
                        <td><?= $s['kelas'] ?></td>
                    </tr>
                </table>
            </div>

            <table class="nilai">
                <thead>
                    <tr>
                        <th width="40" class="text-center">No</th>
                        <th>Mata Pelajaran Praktik</th>
                        <th width="110" class="text-center">Nilai Akhir</th>
                        <th width="130" class="text-center">Predikat</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_nilai = 0;
                    $count_nilai = 0;
                    foreach ($mapels as $idx => $m):
                        // NEW LOGIC: Final Score = Average of Material Averages
                        $db->query("SELECT id FROM materi_penilaian WHERE mapel_id = :mid");
                        $db->bind(':mid', $m['id']);
                        $subject_materis = $db->resultSet();
                        
                        $materi_avgs = [];
                        foreach ($subject_materis as $sm) {
                            $db->query("SELECT AVG(nilai_angka) as avg_m FROM nilai_praktik 
                                        WHERE siswa_id = :sid AND mapel_id = :mid 
                                        AND aspek_id IN (SELECT id FROM aspek_penilaian WHERE materi_id = :m_id)");
                            $db->bind(':sid', $s['id']);
                            $db->bind(':mid', $m['id']);
                            $db->bind(':m_id', $sm['id']);
                            $m_res = $db->single();
                            if (!is_null($m_res['avg_m'])) {
                                $materi_avgs[] = $m_res['avg_m'];
                            }
                        }

                        $total_m_count = count($subject_materis);
                        $score = ($total_m_count > 0 && count($materi_avgs) > 0) ? array_sum($materi_avgs) / $total_m_count : null;

                        $predikat = '-';
                        if (!is_null($score)) {
                            if ($score >= 90)
                                $predikat = 'A (Sangat Baik)';
                            elseif ($score >= 80)
                                $predikat = 'B (Baik)';
                            elseif ($score >= 70)
                                $predikat = 'C (Cukup)';
                            elseif ($score >= 0)
                                $predikat = 'D (Kurang)';
                            
                            $total_nilai += $score;
                            $count_nilai++;
                        }
                        ?>
                        <tr>
                            <td class="text-center"><?= $idx + 1 ?></td>
                            <td style="font-weight: 500;"><?= htmlspecialchars($m['nama_mapel']) ?></td>
                            <td class="text-center" style="font-weight: bold; font-size: 11pt;"><?= !is_null($score) ? round($score, 2) : '-' ?></td>
                            <td class="text-center"><?= $predikat ?></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="background: #f9f9f9; font-weight: bold;">
                        <td colspan="2" class="text-center">RATA-RATA NILAI</td>
                        <td class="text-center"><?= ($count_nilai > 0) ? round($total_nilai / $count_nilai, 2) : '-' ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>

            <div class="footer">
                <div class="sig-container">
                    <div class="sig-box">
                        <p>Mengetahui,</p>
                        <p>Orang Tua/Wali Murid,</p>
                        <div class="sig-space"></div>
                        <p>.......................................</p>
                    </div>
                    <div class="sig-box">
                        <p>Cingambul, <?= date('d') ?> <?= $bulan_map[(int)date('m')] ?> <?= date('Y') ?></p>
                        <p>Plt. Kepala Madrasah,</p>
                        <div class="sig-space"></div>
                        <p><strong><u>H. Dede Apip Mustopa, S.Ag.</u></strong></p>
                        <p>NIP. 196801171992031002</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</body>

</html>