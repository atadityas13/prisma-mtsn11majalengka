<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';
Auth::restrictTo('admin');

$gurus = [
    'SJ' => 'Drs. H. SIROJUDIN, M.S.I.',
    'AS' => 'Drs. H. AJI SUHARDI',
    'AY' => 'H. ASEP S. YASIN, S.Ag., M.Pd.',
    'AI' => 'ASEP IDRIS SAEPUDIN, S.Ag.',
    'DS' => 'H. DEDEN SETIADIN, S.Pd.',
    'NS' => 'NANA SUPRIATNA, S.Ag.',
    'TE' => 'TETI SUMIATI, S.Ag.,M.Pd.I',
    'EN' => 'Hj.ELIN NURLINA, S.Pd.',
    'TS' => 'Hj.TIN SUMARTINI, S.Pd.',
    'SR' => 'SRI RAHAYU, S.Pd.',
    'WA' => 'Hj. Wiwin W. Aziz, S.Pd.',
    'YO' => 'YENI OKTAVIA, S.Pd.',
    'TM' => 'TETI MULYATI, S.Pd.',
    'RM' => 'RIYAN MARDIYANA, S.Pd.',
    'ZN' => 'ZENNY VIRGIAN, S.Pd.',
    'WK' => 'WAKHIDATUL KHOERUNNISA, S.Pd.',
    'MS' => 'MAMAN SUPRATMAN, S.Sos.',
    'NR' => 'NORA RISMAYANTI, S.Pd.',
    'SA' => 'SRI APRINIAWATI, S.Pd.',
    'NK' => 'NANANG KOSWARA, S.Pd.',
    'DJ' => 'DJAFAR SHIDIQ M., S.Pd.',
    'AL' => 'ALKAMIL, S.Pd.I',
    'ED' => 'ENDANG MA\'SUM, S.Pd.',
    'DD' => 'DIDIN SOBARUDIN, S.Ag.',
    'KK' => 'KOKOM KOMARIYAH, S.Pd',
    'EM' => 'EUIS MARYAMAH, S.Pd',
    'IK' => 'IKA KARLINA, S.Pd.',
    'JP' => 'ENDANG JAYA P., S.Ag.',
    'RF' => 'WINDA RAHMA FAUZIAH, S.Pd.',
    'EV' => 'ELVA ELVINASARI, S.Sos.',
    'IQ' => 'M. IQBAL ASHABY SUJUD, S.Pd.',
    'IR' => 'IRMA RISMAWATI, S.Pd.',
    'JM' => 'JIHAN MUSTIKA'
];

$jadwal = [
    [
        'hari' => 'Senin',
        'tanggal' => '25 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1 (Sesi 1)', 'waktu' => '08.00 – 09.00', 'nama' => 'Sejarah Kebudayaan Islam (Sesi 1)', 'pengawas' => ['RF', 'EV', 'JM', 'RM', 'TS', 'ED']],
            ['jam_ke' => '2 (Sesi 1)', 'waktu' => '09.15 – 10.15', 'nama' => 'Bahasa Arab (Sesi 1)', 'pengawas' => ['MS', 'ZN', 'AI', 'IK', 'IR', 'NS']],
            ['jam_ke' => '1 (Sesi 2)', 'waktu' => '10.45 – 11.45', 'nama' => 'Sejarah Kebudayaan Islam (Sesi 2)', 'pengawas' => ['WA', 'AY', 'EM', 'JP', 'YO', 'AL']],
            ['jam_ke' => '2 (Sesi 2)', 'waktu' => '12.00 – 13.00', 'nama' => 'Bahasa Arab (Sesi 2)', 'pengawas' => ['EN', 'SJ', 'SA', 'TE', 'NR', 'SR']]
        ]
    ],
    [
        'hari' => 'Selasa',
        'tanggal' => '26 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1 (Sesi 1)', 'waktu' => '07.30 – 08.30', 'nama' => 'Matematika (Sesi 1)', 'pengawas' => ['IQ', 'NK', 'KK', 'AS', 'DS', 'WK']],
            ['jam_ke' => '2 (Sesi 1)', 'waktu' => '08.45 – 09.45', 'nama' => 'Penjaskes (Sesi 1)', 'pengawas' => ['DJ', 'DD', 'TM', 'ZN', 'NK', 'ED']],
            ['jam_ke' => '1 (Sesi 2)', 'waktu' => '10.15 – 11.15', 'nama' => 'Matematika (Sesi 2)', 'pengawas' => ['JM', 'SA', 'AY', 'AI', 'NS', 'TS']],
            ['jam_ke' => '2 (Sesi 2)', 'waktu' => '11.30 – 12.30', 'nama' => 'Penjaskes (Sesi 2)', 'pengawas' => ['MS', 'SR', 'EM', 'DS', 'DD', 'AS']]
        ]
    ],
    [
        'hari' => "Jum'at",
        'tanggal' => '29 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1 (Sesi 1)', 'waktu' => '07.00 – 08.00', 'nama' => 'IPA (Sesi 1)', 'pengawas' => ['IR', 'JP', 'YO', 'DJ', 'IQ', 'EV']],
            ['jam_ke' => '2 (Sesi 1)', 'waktu' => '08.15 – 09.15', 'nama' => 'Seni Budaya (Sesi 1)', 'pengawas' => ['WA', 'KK', 'IK', 'NR', 'WK', 'TE']],
            ['jam_ke' => '1 (Sesi 2)', 'waktu' => '09.45 – 10.45', 'nama' => 'IPA (Sesi 2)', 'pengawas' => ['RM', 'AL', 'TM', 'SJ', 'RF', 'EN']],
            ['jam_ke' => '2 (Sesi 2)', 'waktu' => '11.00 – 12.00', 'nama' => 'Seni Budaya (Sesi 2)', 'pengawas' => ['KK', 'AL', 'JP', 'NS', 'IK', 'SA']]
        ]
    ],
    [
        'hari' => 'Selasa',
        'tanggal' => '02 Juni 2026',
        'mapel' => [
            ['jam_ke' => '1 (Sesi 1)', 'waktu' => '07.30 – 08.30', 'nama' => 'Fiqih (Sesi 1)', 'pengawas' => ['SJ', 'NK', 'TE', 'AY', 'DS', 'AI']],
            ['jam_ke' => '2 (Sesi 1)', 'waktu' => '08.45 – 09.45', 'nama' => 'Bahasa Inggris (Sesi 1)', 'pengawas' => ['ZN', 'IQ', 'IR', 'JM', 'EM', 'EV']],
            ['jam_ke' => '1 (Sesi 2)', 'waktu' => '10.15 – 11.15', 'nama' => 'Fiqih (Sesi 2)', 'pengawas' => ['TM', 'WA', 'DJ', 'NR', 'AS', 'YO']],
            ['jam_ke' => '2 (Sesi 2)', 'waktu' => '11.30 – 12.30', 'nama' => 'Bahasa Inggris (Sesi 2)', 'pengawas' => ['SR', 'DD', 'MS', 'WK', 'EN', 'RF']]
        ]
    ],
    [
        'hari' => 'Rabu',
        'tanggal' => '03 Juni 2026',
        'mapel' => [
            ['jam_ke' => '1 (Sesi 1)', 'waktu' => '07.30 – 08.30', 'nama' => 'Pendidikan Pancasila (Sesi 1)', 'pengawas' => ['RM', 'ED', 'TS', 'TE', 'JP', 'WA']],
            ['jam_ke' => '2 (Sesi 1)', 'waktu' => '08.45 – 09.45', 'nama' => 'Akidah-Akhlak (Sesi 1)', 'pengawas' => ['DJ', 'RM', 'JM', 'AY', 'KK', 'EN']],
            ['jam_ke' => '1 (Sesi 2)', 'waktu' => '10.15 – 11.15', 'nama' => 'Pendidikan Pancasila (Sesi 2)', 'pengawas' => ['IQ', 'SA', 'MS', 'EM', 'DS', 'NS']],
            ['jam_ke' => '2 (Sesi 2)', 'waktu' => '11.30 – 12.30', 'nama' => 'Akidah-Akhlak (Sesi 2)', 'pengawas' => ['AS', 'ED', 'NR', 'TS', 'AI', 'ZN']]
        ]
    ],
    [
        'hari' => 'Kamis',
        'tanggal' => '04 Juni 2026',
        'mapel' => [
            ['jam_ke' => '1 (Sesi 1)', 'waktu' => '07.30 – 08.30', 'nama' => 'Bahasa Sunda (Sesi 1)', 'pengawas' => ['NK', 'DD', 'SJ', 'IR', 'TM', 'YO']],
            ['jam_ke' => '2 (Sesi 1)', 'waktu' => '08.45 – 09.45', 'nama' => "Alqur'an-Hadits (Sesi 1)", 'pengawas' => ['WK', 'SR', 'EV', 'IK', 'RF', 'AL']],
            ['jam_ke' => '1 (Sesi 2)', 'waktu' => '10.15 – 11.15', 'nama' => 'Bahasa Sunda (Sesi 2)', 'pengawas' => ['EM', 'AY', 'TM', 'MS', 'NK', 'KK']],
            ['jam_ke' => '2 (Sesi 2)', 'waktu' => '11.30 – 12.30', 'nama' => "Alqur'an-Hadits (Sesi 2)", 'pengawas' => ['ED', 'SA', 'RM', 'RF', 'TS', 'IK']]
        ]
    ],
    [
        'hari' => "Jum'at",
        'tanggal' => '05 Juni 2026',
        'mapel' => [
            ['jam_ke' => '1 (Sesi 1)', 'waktu' => '07.00 – 08.00', 'nama' => 'Informatika (Sesi 1)', 'pengawas' => ['ZN', 'DD', 'IQ', 'DS', 'EV', 'SJ']],
            ['jam_ke' => '2 (Sesi 1)', 'waktu' => '08.15 – 09.15', 'nama' => 'Bahasa Indonesia (Sesi 1)', 'pengawas' => ['JP', 'DJ', 'EN', 'AS', 'YO', 'WK']],
            ['jam_ke' => '1 (Sesi 2)', 'waktu' => '09.45 – 10.45', 'nama' => 'Informatika (Sesi 2)', 'pengawas' => ['SR', 'IR', 'TE', 'AL', 'AI', 'JM']],
            ['jam_ke' => '2 (Sesi 2)', 'waktu' => '11.00 – 12.00', 'nama' => 'Bahasa Indonesia (Sesi 2)', 'pengawas' => ['NS', 'WA', 'NR', 'IR', 'KK', 'SR']]
        ]
    ]
];

$selected_gurus = ['' => ''];
$is_cetak_per_guru = false;
if (isset($_GET['guru']) && $_GET['guru'] !== '') {
    if ($_GET['guru'] === 'all') {
        $selected_gurus = $gurus;
        $is_cetak_per_guru = true;
    } elseif (isset($gurus[$_GET['guru']])) {
        $selected_gurus = [$_GET['guru'] => $gurus[$_GET['guru']]];
        $is_cetak_per_guru = true;
    }
}
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
            font-size: 10pt;
            background: #f0f0f0;
            color: #000;
        }

        @page {
            size: A4 portrait;
            margin: 4mm 5mm;
        }

        .page {
            width: calc(210mm - 10mm);
            padding: 4mm 5mm;
            margin: 0 auto 4mm;
            background: #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.08);
            min-height: calc(297mm - 8mm);
            position: relative;
        }

        @media print {
            .page {
                width: auto;
                box-shadow: none;
                margin: 0;
                min-height: auto;
                padding: 0;
            }
        }

        /* ── Kop Surat ── */
        .kop {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }

        .kop img {
            height: 58px;
            /* Diperbesar agar proporsional */
        }

        .kop-text {
            flex: 1;
            text-align: center;
            line-height: 1.15;
        }

        .kop-text h3 {
            font-size: 11.5pt;
            text-transform: uppercase;
            margin-bottom: 1px;
        }

        .kop-text h4 {
            font-size: 9.5pt;
            text-transform: uppercase;
            margin-bottom: 1px;
        }

        .kop-text h2 {
            font-size: 14pt;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 1px;
        }

        .kop-text p {
            font-size: 8pt;
        }

        /* ── Judul ── */
        .judul {
            text-align: center;
            margin-bottom: 8px;
        }

        .judul h4 {
            font-size: 11.5pt;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0;
            line-height: 1.2;
        }

        .judul p {
            font-size: 9.5pt;
            color: #333;
            margin-top: 2px;
        }

        /* ── Layout Tabel & Legenda ── */
        .content-wrapper {
            display: block;
            width: 100%;
        }

        .main-table-container {
            width: 100%;
            margin-bottom: 8px;
        }

        .legend-container {
            width: 100%;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        /* ── Tabel Utama ── */
        table {
            border-collapse: collapse;
            table-layout: fixed;
            /* Mencegah kolom melar tanpa kendali yang menyebabkan overlap */
        }

        .main-table {
            width: 100%;
            font-size: 8.5pt;
            /* Teks diperjelas agar pas dengan kolom */
            line-height: 1.2;
        }

        table th,
        table td {
            border: 1px solid #000;
            vertical-align: middle;
        }

        .main-table th,
        .main-table td {
            padding: 2.5px 3px; /* Padding ditingkatkan agar lebih lega */
        }

        table thead th {
            background: #f0f0f0;
            text-align: center;
            font-weight: 700;
        }

        .center {
            text-align: center;
        }

        .nowrap {
            white-space: nowrap;
        }

        /* ── Legenda ── */
        .legend-table {
            width: 100%;
            font-size: 7.2pt;
            line-height: 1.2;
        }

        .legend-table th,
        .legend-table td {
            padding: 2px 3px; /* Padding nyaman untuk dibaca */
            vertical-align: middle;
        }

        /* ── Tanda Tangan ── */
        .ttd-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 8px; /* Ruang pisah dari legenda */
            width: 100%;
        }

        .ttd-box {
            width: 230px;
            text-align: left;
        }

        .ttd-box p {
            margin: 1.5px 0;
            font-size: 9.5pt;
            position: relative;
            z-index: 5;
            line-height: 1.2;
        }

        .sig-overlay {
            position: relative;
            height: 55px;
            margin-top: 2px;
            margin-bottom: 2px;
        }

        .img-cap {
            position: absolute;
            top: -20px;
            left: -65px;
            width: 100px;
            z-index: 2;
            opacity: 0.9;
        }

        .img-ttd {
            position: absolute;
            top: -10px;
            left: -10px;
            width: 120px;
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

        .highlight-cell {
            background-color: #90EE90 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .footer-guru {
            position: absolute;
            bottom: 15px;
            left: 20px;
            font-size: 10pt;
            font-style: italic;
            font-weight: bold;
            color: #333;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .page {
                page-break-after: always;
            }
            .page:last-of-type {
                page-break-after: auto;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <form method="GET" style="display:inline-block; margin-right:15px;">
            <select name="guru" onchange="this.form.submit()" style="padding: 6px; border-radius: 4px; font-weight: bold;">
                <option value="">-- Cetak Jadwal Umum --</option>
                <option value="all" <?= (isset($_GET['guru']) && $_GET['guru'] == 'all') ? 'selected' : '' ?>>Cetak Semua Guru (<?= count($gurus) ?> Halaman)</option>
                <?php foreach ($gurus as $k => $n): ?>
                    <option value="<?= $k ?>" <?= (isset($_GET['guru']) && $_GET['guru'] == $k) ? 'selected' : '' ?>>Cetak Guru <?= $k ?> - <?= $n ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Jadwal Asesmen</button>
        <button class="btn-back" onclick="window.close()">✕ Tutup</button>
    </div>

    <?php foreach ($selected_gurus as $active_guru_code => $active_guru_name): ?>
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
            <h4>JADWAL SUMATIF AKHIR TAHUN</h4>
            <h4>SEMESTER II (GENAP)</h4>
            <h4>TAHUN AJARAN <?= DEFAULT_YEAR ?></h4>
        </div>

        <div class="content-wrapper">
            <!-- Tabel Utama -->
            <div class="main-table-container">
                <table class="main-table">
                    <colgroup>
                        <!-- Mengatur lebar persentase agar tabel pas 100% dan tidak tumpang tindih -->
                        <col style="width: 16%;">
                        <col style="width: 7%;">
                        <col style="width: 4%;">
                        <col style="width: 13%;">
                        <col style="width: 24%;">
                        <col style="width: 6%;">
                        <col style="width: 6%;">
                        <col style="width: 6%;">
                        <col style="width: 6%;">
                        <col style="width: 6%;">
                        <col style="width: 6%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th rowspan="2">Hari, Tanggal</th>
                            <th rowspan="2">Sesi</th>
                            <th rowspan="2">Jam</th>
                            <th rowspan="2">Waktu</th>
                            <th rowspan="2">Mata Pelajaran</th>
                            <th colspan="6">Ruang Pengawas</th>
                        </tr>
                        <tr>
                            <th>01</th>
                            <th>02</th>
                            <th>03</th>
                            <th>04</th>
                            <th>05</th>
                            <th>06</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jadwal as $hari): ?>
                            <?php
                            $rowspan = count($hari['mapel']);
                            $first = true;
                            ?>
                            <?php foreach ($hari['mapel'] as $mapel_idx => $mapel): ?>
                                <tr>
                                    <?php if ($first): ?>
                                        <td rowspan="<?= $rowspan ?>" class="center nowrap">
                                            <?= $hari['hari'] ?>, <?= $hari['tanggal'] ?>
                                        </td>
                                        <?php $first = false; ?>
                                    <?php endif; ?>

                                    <?php if ($mapel_idx === 0): ?>
                                        <td rowspan="2" class="center nowrap">Sesi 1</td>
                                    <?php elseif ($mapel_idx === 2): ?>
                                        <td rowspan="2" class="center nowrap">Sesi 2</td>
                                    <?php endif; ?>

                                    <td class="center"><?= trim(explode('(', $mapel['jam_ke'])[0]) ?></td>
                                    <td class="center nowrap"><?= $mapel['waktu'] ?></td>
                                    <td class="nowrap"><?= str_replace([' (Sesi 1)', ' (Sesi 2)'], '', $mapel['nama']) ?></td>
                                    <?php 
                                    // Tampilkan pengawas 1 per ruang
                                    for ($ruang = 0; $ruang < 6; $ruang++):
                                        $p = isset($mapel['pengawas'][$ruang]) ? $mapel['pengawas'][$ruang] : '';
                                        
                                        $hl_class = '';
                                        if ($is_cetak_per_guru && $p === $active_guru_code) {
                                            $hl_class = 'highlight-cell';
                                        }
                                    ?>
                                        <td class="center nowrap <?= $hl_class ?>"><strong><?= $p ?></strong></td>
                                    <?php endfor; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Legenda Kode Guru -->
        <div class="legend-container">
            <table class="legend-table" style="width: 100%;">
                <colgroup>
                    <col style="width: 5%;">
                    <col style="width: 20%;">
                    <col style="width: 5%;">
                    <col style="width: 20%;">
                    <col style="width: 5%;">
                    <col style="width: 20%;">
                    <col style="width: 5%;">
                    <col style="width: 20%;">
                </colgroup>
                <thead>
                    <tr>
                        <th colspan="8" style="background:#ddd; font-size:8pt; padding:3px;">DAFTAR KODE PENGAWAS</th>
                    </tr>
                    <tr>
                        <th class="center">Kode</th>
                        <th class="center">Nama Guru</th>
                        <th class="center">Kode</th>
                        <th class="center">Nama Guru</th>
                        <th class="center">Kode</th>
                        <th class="center">Nama Guru</th>
                        <th class="center">Kode</th>
                        <th class="center">Nama Guru</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $keys = array_keys($gurus);
                    $cols = 4;
                    $rows_count = ceil(count($keys) / $cols);
                    for ($i = 0; $i < $rows_count; $i++):
                        ?>
                        <tr>
                            <?php for ($c = 0; $c < $cols; $c++): 
                                $idx = $i + $c * $rows_count;
                                $k = isset($keys[$idx]) ? $keys[$idx] : '';
                                $n = $k ? $gurus[$k] : '';
                                $hl = ($is_cetak_per_guru && $k === $active_guru_code) ? 'highlight-cell' : '';
                            ?>
                                <td class="center nowrap <?= $hl ?>"><strong><?= $k ?></strong></td>
                                <td class="<?= $hl ?>"><?= $n ?></td>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <!-- Tanda Tangan -->
        <div class="ttd-container">
            <div class="ttd-box">
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

        <?php if ($is_cetak_per_guru): ?>
            <div class="footer-guru">
                Dicetak untuk guru <?= $active_guru_code ?> : <?= $active_guru_name ?>
            </div>
        <?php endif; ?>

    </div>
    <?php endforeach; ?>

</body>

</html>