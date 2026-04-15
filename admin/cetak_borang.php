<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::restrictTo('admin');

$db = new Database();
$ploting_id = $_GET['ploting_id'] ?? '';

// Fetch Ploting Info
$db->query("SELECT pp.id as ploting_id, pp.mapel_id, g.nama_lengkap as nama_guru, g.nip, m.nama_mapel
            FROM ploting_penguji pp
            JOIN guru g ON pp.guru_id = g.id
            JOIN mapel m ON pp.mapel_id = m.id
            WHERE pp.id = :pid");
$db->bind(':pid', $ploting_id);
$ploting = $db->single();

if (!$ploting) {
    die('Ploting tidak ditemukan.');
}

// Fetch Aspects for this Mapel
// NOTE: We take aspects from any guru who handles this mapel for simplicity in the blank form
$db->query("SELECT * FROM aspek_penilaian WHERE mapel_id = :id GROUP BY nama_aspek ORDER BY id ASC");
$db->bind(':id', $ploting['mapel_id']);
$aspeks = $db->resultSet();

// Fetch Students for this ploting
$db->query("SELECT s.* FROM ploting_siswa ps
            JOIN siswa s ON ps.siswa_id = s.id
            WHERE ps.ploting_id = :pid
            ORDER BY s.nama_lengkap ASC");
$db->bind(':pid', $ploting_id);
$siswas = $db->resultSet();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Borang Penilaian - <?= htmlspecialchars($ploting['nama_mapel']) ?> - <?= htmlspecialchars($ploting['nama_guru']) ?></title>
    <style>body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            padding: 0;
            margin: 0;
            background: #eee;
        }

        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        .page {
            width: auto;
            max-width: 210mm;
            min-height: 297mm;
            padding: 15mm 15mm;
            margin: 10mm auto;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
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
            margin-bottom: 20px;
        }

        .title h4 {
            margin: 0;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            text-align: center;
        }

        table.data-table td {
            height: 25px;
        }

        /* Space for handwriting */
        .text-left {
            text-align: left !important;
        }

        .footer {
            margin-top: 40px;
        }

        .sig-container {
            display: flex;
            justify-content: flex-end;
        }

        .sig-box {
            width: 250px;
            text-align: center;
        }

        .sig-box p {
            margin: 2px 0;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
                padding: 0;
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
        style="position: fixed; top: 0; left: 0; right: 0; background: #333; color: #fff; padding: 12px; text-align: center; z-index: 1000;">
        <button onclick="window.print()"
            style="padding: 8px 16px; cursor: pointer; background: #28a745; color: #fff; border: none; border-radius: 4px; font-weight: bold;">🖨️
            CETAK BORANG</button>
        <button onclick="window.close()"
            style="padding: 8px 16px; cursor: pointer; background: #6c757d; color: #fff; border: none; border-radius: 4px; margin-left:10px;">TUTUP</button>
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
            <h4>BORANG PENILAIAN UJIAN PRAKTIK</h4>
        </div>

        <table class="info-table">
            <tr>
                <td width="120">Mata Pelajaran</td>
                <td width="15">:</td>
                <td width="300"><strong><?= htmlspecialchars($ploting['nama_mapel']) ?></strong></td>
                <td width="100">Guru Penguji</td>
                <td width="15">:</td>
                <td><strong><?= htmlspecialchars($ploting['nama_guru']) ?></strong></td>
            </tr>
            <tr>
                <td>Tahun Ajaran</td>
                <td>:</td>
                <td><?= DEFAULT_YEAR ?></td>
                <td>Tanggal</td>
                <td>:</td>
                <td>.......................... 2026</td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" width="30">No</th>
                    <th rowspan="2">Nama Siswa</th>
                    <th colspan="<?= count($aspeks) > 0 ? count($aspeks) : 3 ?>">Aspek Penilaian</th>
                    <th rowspan="2" width="100">Nilai Akhir</th>
                </tr>
                <tr>
                    <?php if (count($aspeks) > 0): ?>
                        <?php foreach ($aspeks as $a): ?>
                            <th width="70"><?= $a['nama_aspek'] ?></th>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <th width="70">Aspek 1</th>
                        <th width="70">Aspek 2</th>
                        <th width="70">Aspek 3</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (count($siswas) > 0): ?>
                    <?php foreach ($siswas as $idx => $s): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td class="text-left" style="padding-left: 10px;"><?= $s['nama_lengkap'] ?></td>
                            <?php if (count($aspeks) > 0): ?>
                                <?php foreach ($aspeks as $a): ?>
                                    <td></td><?php endforeach; ?>
                            <?php else: ?>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php endif; ?>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= 3 + (count($aspeks) > 0 ? count($aspeks) : 3) ?>"
                            style="padding: 20px; color: #888;">Belum ada data siswa di kelas ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer">
            <div class="sig-container">
                <div class="sig-box">
                    <p>Cingambul, .......................... <?= date('Y') ?></p>
                    <p>Penguji,</p>
                    <div style="height: 70px;"></div>
                    <p><strong>( ________________________ )</strong></p>
                    <p>NIP. ........................................</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>