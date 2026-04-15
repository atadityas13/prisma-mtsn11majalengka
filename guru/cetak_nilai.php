<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::restrictTo('guru');

$db = new Database();
$guru_id = $_SESSION['guru_id'];
$mapel_id = $_SESSION['mapel_id'];
$kelas = $_GET['kelas'] ?? '';

// Fetch Mapel Info
$db->query("SELECT * FROM mapel WHERE id = :id");
$db->bind(':id', $mapel_id);
$mapel = $db->single();

// Fetch All Aspects for this mapel (Global)
$db->query("SELECT * FROM aspek_penilaian WHERE mapel_id = :mid ORDER BY id ASC");
$db->bind(':mid', $mapel_id);
$aspeks = $db->resultSet();

// Fetch Students assigned to this guru via ploting_siswa
$db->query("SELECT s.* FROM siswa s
            JOIN ploting_siswa ps  ON ps.siswa_id = s.id
            JOIN ploting_penguji pp ON ps.ploting_id = pp.id
            WHERE pp.guru_id = :gid AND pp.mapel_id = :mid
            ORDER BY s.nomor_peserta ASC");
$db->bind(':gid', $guru_id);
$db->bind(':mid', $mapel_id);
$siswas = $db->resultSet();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Nilai - <?= $mapel['nama_mapel'] ?> - <?= $kelas ?></title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 11pt; padding: 0; margin: 0; background: #eee; color: #000; }
        @page { size: A4 portrait; margin: 8mm; }
        .page { width: calc(210mm - 16mm); padding: 8mm; margin: 0 auto 8mm; background: white; box-shadow: 0 0 5px rgba(0,0,0,0.08); }
        .header-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header-table td { vertical-align: middle; text-align: center; border: none !important; }
        .logo-left, .logo-right { width: 70px; }
        .logo-left { text-align: left !important; }
        .logo-right { text-align: right !important; }
        .title { text-align: center; margin-bottom: 20px; }
        .title h4 { margin: 0; text-transform: uppercase; text-decoration: underline; }
        .info-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 5px 6px; text-align: center; word-break: break-word; }
        table.data-table td { min-height: 25px; }
        table.data-table th:first-child, table.data-table td:first-child { width: 30px; }
        table.data-table th:nth-child(2), table.data-table td:nth-child(2) { width: 36%; text-align: left; padding-left: 8px; }
        table.data-table th:nth-child(3), table.data-table td:nth-child(3) { width: 60px; }
        table.data-table th:last-child, table.data-table td:last-child { width: 80px; }
        .text-left { text-align: left !important; }
        .footer { margin-top: 40px; }
        .sig-container { display: flex; justify-content: flex-end; }
        .sig-box { width: 300px; text-align: left; padding-left: 50px; }
        .sig-box p { margin: 2px 0; }
        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
            .page { margin: 0; box-shadow: none; width: auto; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 0; left: 0; right: 0; background: #333; color: #fff; padding: 12px; text-align: center; z-index: 1000;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer; background: #28a745; color: #fff; border: none; border-radius: 4px; font-weight: bold;">🖨️ CETAK DAFTAR NILAI</button>
        <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer; background: #6c757d; color: #fff; border: none; border-radius: 4px; margin-left:10px;">TUTUP</button>
    </div>

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

        <div class="title">
            <h4>DAFTAR NILAI UJIAN PRAKTIK</h4>
        </div>

        <table class="info-table">
            <tr>
                <td width="150">Penguji</td>
                <td width="10">:</td>
                <td><strong><?= $_SESSION['nama_lengkap'] ?></strong></td>
            </tr>
            <tr>
                <td>Mata Uji Praktik</td>
                <td>:</td>
                <td><strong><?= $mapel['nama_mapel'] ?></strong></td>
            </tr>
            <tr>
                <td>Tahun Ajaran</td>
                <td>:</td>
                <td><?= DEFAULT_YEAR ?></td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" width="30">No</th>
                    <th rowspan="2">Nama Siswa</th>
                    <th rowspan="2" width="60">Kelas</th>
                    <th colspan="<?= count($aspeks) > 0 ? count($aspeks) : 1 ?>">Aspek Penilaian</th>
                    <th rowspan="2" width="80">Rata-rata</th>
                </tr>
                <tr>
                    <?php if (count($aspeks) > 0): ?>
                        <?php foreach($aspeks as $a): ?>
                            <th width="70" style="font-size: 9pt;"><?= $a['nama_aspek'] ?></th>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <th>-</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (count($siswas) > 0): ?>
                    <?php foreach ($siswas as $idx => $s): 
                        $total_row = 0;
                        $count_row = 0;
                        
                        // Fetch scores
                        $db->query("SELECT aspek_id, nilai_angka FROM nilai_praktik WHERE siswa_id = :sid AND guru_id = :gid AND mapel_id = :mid");
                        $db->bind(':sid', $s['id']);
                        $db->bind(':gid', $guru_id);
                        $db->bind(':mid', $mapel_id);
                        $scores = $db->resultSet();
                        $score_map = [];
                        foreach ($scores as $scr) { $score_map[$scr['aspek_id']] = $scr['nilai_angka']; }
                    ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td class="text-left" style="padding-left: 10px;"><?= $s['nama_lengkap'] ?></td>
                            <td><?= $s['kelas'] ?></td>
                            <?php if (count($aspeks) > 0): ?>
                                <?php foreach ($aspeks as $a): 
                                    $n = $score_map[$a['id']] ?? null;
                                    if (!is_null($n)) { $total_row += $n; $count_row++; }
                                ?>
                                    <td><?= !is_null($n) ? round($n, 2) : '-' ?></td>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <td>-</td>
                            <?php endif; ?>
                            <td style="font-weight: bold;">
                                <?= ($count_row > 0) ? round($total_row / $count_row, 2) : '-' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= 4 + (count($aspeks) > 0 ? count($aspeks) : 1) ?>" style="padding: 20px; color: #888;">Belum ada data siswa yang diploting untuk Anda.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (count($siswas) == 0): ?>
            <div class="alert alert-warning text-center">Belum ada data siswa yang diploting untuk Anda.</div>
        <?php endif; ?>

        <div class="footer">
            <div class="sig-container">
                <div class="sig-box">
                    <p>Cingambul, .......................... <?= date('Y') ?></p>
                    <p>Penguji,</p>
                    <div style="height: 70px;"></div>
                    <p><strong><?= $_SESSION['nama_lengkap'] ?></strong></p>
                    <p>NIP. <?= $_SESSION['username'] ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
