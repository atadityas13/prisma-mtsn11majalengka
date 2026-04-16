<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::restrictTo('admin');

$db = new Database();
$ploting_id = $_GET['ploting_id'] ?? '';

// Fetch Ploting Info
$db->query("SELECT pp.id as ploting_id, pp.mapel_id, g.nama_lengkap as nama_guru, g.nip, m.nama_mapel, j.tanggal as jadwal_tanggal
            FROM ploting_penguji pp
            JOIN guru g ON pp.guru_id = g.id
            JOIN mapel m ON pp.mapel_id = m.id
            LEFT JOIN jadwal_praktik j ON j.ploting_id = pp.id
            WHERE pp.id = :pid");
$db->bind(':pid', $ploting_id);
$ploting = $db->single();

if (!$ploting) {
    die('Ploting tidak ditemukan.');
}

$jadwal_tanggal = $ploting['jadwal_tanggal'] ?? null;
$jadwal_tanggal_formatted = '';
if ($jadwal_tanggal) {
    $jadwal_tanggal_formatted = (new DateTime($jadwal_tanggal))->format('d/m/Y');
}

// Fetch Materis
$db->query("SELECT * FROM materi_penilaian WHERE mapel_id = :id ORDER BY id ASC");
$db->bind(':id', $ploting['mapel_id']);
$materis = $db->resultSet();

// Fetch ALL Aspects joined with Materi (LEFT JOIN to catch orphaned)
$db->query("SELECT a.*, m.nama_materi 
            FROM aspek_penilaian a 
            LEFT JOIN materi_penilaian m ON a.materi_id = m.id 
            WHERE a.mapel_id = :id 
            ORDER BY m.id ASC, a.id ASC");
$db->bind(':id', $ploting['mapel_id']);
$aspeks = $db->resultSet();

// Group aspects by Materi ID (use 0 for orphaned)
$grouped_aspeks = [];
foreach ($aspeks as $a) {
    $m_id = $a['materi_id'] ?? 0;
    $grouped_aspeks[$m_id][] = $a;
}

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
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.08);
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
            flex: 0 0 110px;
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

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            text-align: center;
            word-break: break-word;
            line-height: 1.2;
        }

        table.data-table td {
            min-height: 18px;
        }

        table.data-table th:first-child,
        table.data-table td:first-child {
            width: 30px;
        }

        table.data-table th:nth-child(2),
        table.data-table td:nth-child(2) {
            width: 35%;
            text-align: left;
            padding-left: 8px;
        }

        table.data-table th:nth-child(3),
        table.data-table td:nth-child(3) {
            width: 15%;
        }

        table.data-table th:nth-last-child(2),
        table.data-table td:nth-last-child(2) {
            width: 60px;
        }

        table.data-table th:last-child,
        table.data-table td:last-child {
            width: 120px;
        }

        /* Space for handwriting */
        .text-left {
            text-align: left !important;
        }

        .footer {
            margin-top: 20px;
        }

        .footer-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .keterangan-aspek {
            flex: 1 1 60%;
            min-width: 0;
            font-size: 10pt;
        }

        .keterangan-aspek ul {
            margin: 5px 0 0;
            padding-left: 20px;
        }

        .sig-box {
            flex: 0 0 35%;
            min-width: 180px;
            text-align: left;
            padding-left: 0;
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

        <div class="info-rows">
            <div class="info-column">
                <div class="info-item">
                    <span class="info-label">Mata Pelajaran</span>
                    <span class="info-separator">:</span>
                    <span class="info-value"><strong><?= htmlspecialchars($ploting['nama_mapel']) ?></strong></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tahun Ajaran</span>
                    <span class="info-separator">:</span>
                    <span class="info-value"><?= DEFAULT_YEAR ?></span>
                </div>
            </div>
            <div class="info-column">
                <div class="info-item">
                    <span class="info-label">Guru Penguji</span>
                    <span class="info-separator">:</span>
                    <span class="info-value"><strong><?= htmlspecialchars($ploting['nama_guru']) ?></strong></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal</span>
                    <span class="info-separator">:</span>
                    <span class="info-value">.......................... 2026</span>
                </div>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="3" width="30">No</th>
                    <th rowspan="3">Nama Siswa</th>
                    <th colspan="<?= count($aspeks) > 0 ? count($aspeks) : 3 ?>">Aspek Penilaian</th>
                    <th rowspan="3" width="60">Nilai Akhir</th>
                    <th rowspan="3" width="120">Keterangan</th>
                </tr>
                <tr>
                    <?php 
                    // Render headers for defined materials
                    if (count($aspeks) > 0):
                        if (!empty($materis)): 
                            foreach ($materis as $m): 
                                $m_aspect_count = count($grouped_aspeks[$m['id']] ?? []);
                                if ($m_aspect_count == 0) continue;
                            ?>
                                <th colspan="<?= $m_aspect_count ?>" style="font-size: 8pt;"><?= htmlspecialchars($m['nama_materi']) ?></th>
                            <?php endforeach; 
                        endif; 

                        // Render header for orphaned aspects (if any)
                        if (!empty($grouped_aspeks[0])): ?>
                            <th colspan="<?= count($grouped_aspeks[0]) ?>" style="font-size: 8pt;">Lain-lain</th>
                        <?php endif; 
                    else: ?>
                        <th colspan="3">Aspek Belum Diisi</th>
                    <?php endif; ?>
                </tr>
                <tr>
                    <?php if (count($aspeks) > 0): ?>
                        <?php foreach ($aspeks as $idx => $a): ?>
                            <th width="40">A<?= $idx + 1 ?></th>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <th width="40">A1</th>
                        <th width="40">A2</th>
                        <th width="40">A3</th>
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
                                    <td></td>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <td></td><td></td><td></td>
                            <?php endif; ?>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= 3 + (count($aspeks) > 0 ? count($aspeks) : 3) ?>"
                            style="padding: 20px; color: #888;">Belum ada data siswa di ploting ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer">
            <div class="footer-row">
                <?php if (count($aspeks) > 0): ?>
                <div class="keterangan-aspek">
                    <strong>Keterangan Aspek Penilaian:</strong>
                    <ul>
                        <?php foreach ($aspeks as $idx => $a): ?>
                        <li>A<?= $idx + 1 ?>: <?= htmlspecialchars($a['nama_aspek']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="sig-box">
                    <p>Cingambul, <?= $jadwal_tanggal_formatted ? htmlspecialchars($jadwal_tanggal_formatted) : date('d/m/Y') ?></p>
                    <p>Penguji,</p>
                    <div style="height: 70px;"></div>
                    <p><strong><?= htmlspecialchars($ploting['nama_guru']) ?></strong></p>
                    <p>NIP. <?= htmlspecialchars($ploting['nip']) ?></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>