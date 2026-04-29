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
        'tanggal' => '04 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.30 – 09.00', 'nama' => 'Sejarah Kebudayaan Islam', 'pengawas' => ['JP', 'TM', 'IQ', 'SA', 'YO', 'NR']],
            ['jam_ke' => '2', 'waktu' => '09.15 – 10.45', 'nama' => 'Bahasa Arab', 'pengawas' => ['RM', 'TE', 'AY', 'IQ', 'SR', 'EM']]
        ]
    ],
    [
        'hari' => 'Selasa',
        'tanggal' => '05 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.30 – 09.00', 'nama' => 'Bahasa Sunda', 'pengawas' => ['EM', 'SJ', 'ZN', 'DJ', 'WK', 'NS']],
            ['jam_ke' => '2', 'waktu' => '09.15 – 10.45', 'nama' => 'Matematika', 'pengawas' => ['ZN', 'RF', 'DS', 'AS', 'MS', 'EN']],
            ['jam_ke' => '3', 'waktu' => '11.00 – 12.30', 'nama' => 'Akidah-Akhlak', 'pengawas' => ['KK', 'AY', 'IK', 'AI', 'IR', 'AS']]
        ]
    ],
    [
        'hari' => 'Rabu',
        'tanggal' => '06 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.30 – 09.00', 'nama' => 'Ilmu Pengetahuan Alam', 'pengawas' => ['SR', 'YO', 'NR', 'NK', 'EN', 'RF']],
            ['jam_ke' => '2', 'waktu' => '09.15 – 10.45', 'nama' => 'Fiqih', 'pengawas' => ['SJ', 'TS', 'SA', 'DD', 'JP', 'DJ']],
            ['jam_ke' => '3', 'waktu' => '11.00 – 12.30', 'nama' => 'Ilmu Pengetahuan Sosial', 'pengawas' => ['DJ', 'WK', 'TM', 'RM', 'JM', 'TE']]
        ]
    ],
    [
        'hari' => 'Kamis',
        'tanggal' => '07 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.30 – 09.00', 'nama' => 'Al-qur\'an-Hadits', 'pengawas' => ['ED', 'AL', 'RM', 'NS', 'AY', 'DS']],
            ['jam_ke' => '2', 'waktu' => '09.15 – 10.45', 'nama' => 'Bahasa Inggris', 'pengawas' => ['EV', 'AL', 'KK', 'AI', 'IK', 'NK']],
            ['jam_ke' => '3', 'waktu' => '11.00 – 12.30', 'nama' => 'Seni Budaya', 'pengawas' => ['AL', 'ED', 'TS', 'SA', 'NK', 'MS']]
        ]
    ],
    [
        'hari' => 'Jum\'at',
        'tanggal' => '08 Mei 2026',
        'mapel' => [
            ['jam_ke' => '1', 'waktu' => '07.00 – 08.30', 'nama' => 'Pendidikan Pancasila', 'pengawas' => ['MS', 'DD', 'WK', 'IQ', 'RF', 'ZN']],
            ['jam_ke' => '2', 'waktu' => '08.45 – 10.15', 'nama' => 'Bahasa Indonesia', 'pengawas' => ['EV', 'ED', 'JM', 'YO', 'NR', 'IR']]
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
            font-size: 10pt;
            background: #f0f0f0;
            color: #000;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        .page {
            width: calc(297mm - 20mm);
            padding: 10mm;
            margin: 0 auto 10mm;
            background: #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.08);
            min-height: calc(210mm - 20mm);
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
            padding-bottom: 5px;
            margin-bottom: 5px;
            /* Kurangi margin bawah */
        }

        .kop img {
            height: 60px;
            /* Sedikit dikecilkan agar hemat ruang */
        }

        .kop-text {
            flex: 1;
            text-align: center;
            line-height: 1.1;
        }

        .kop-text h3 {
            font-size: 12pt;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .kop-text h4 {
            font-size: 10pt;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .kop-text h2 {
            font-size: 14pt;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .kop-text p {
            font-size: 8pt;
        }

        /* ── Judul ── */
        .judul {
            text-align: center;
            margin-bottom: 10px;
        }

        .judul h4 {
            font-size: 12pt;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;
        }

        .judul p {
            font-size: 10pt;
            color: #333;
            margin-top: 2px;
        }

        /* ── Layout Tabel & Legenda ── */
        .content-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            /* Jarak aman */
            width: 100%;
        }

        .main-table-container {
            flex: 1;
            /* Memastikan tabel utama mengisi semua ruang sisa */
            min-width: 0;
            /* Penting untuk mencegah tabel meluap dari flex */
        }

        .legend-container {
            flex: 0 0 auto;
        }

        /* ── Tabel Utama ── */
        table {
            border-collapse: collapse;
            table-layout: fixed;
            /* Mencegah kolom melar tanpa kendali yang menyebabkan overlap */
        }

        .main-table {
            width: 100%;
            font-size: 9pt;
            /* Teks diperkecil agar pas dengan kolom */
        }

        table th,
        table td {
            border: 1px solid #000;
            vertical-align: middle;
        }

        .main-table th,
        .main-table td {
            padding: 5px 6px;
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
            width: max-content;
            font-size: 7pt;
        }

        .legend-table th,
        .legend-table td {
            padding: 2px 4px;
        }

        /* ── Tanda Tangan ── */
        .ttd-container {
            display: flex;
            justify-content: flex-end;
            margin-top: -15px;
            /* Naikkan tanda tangan ke atas sedikit */
            width: 100%;
        }

        .ttd-box {
            width: 250px;
            text-align: left;
        }

        .ttd-box p {
            margin: 1px 0;
            font-size: 10pt;
            position: relative;
            z-index: 5;
        }

        .sig-overlay {
            position: relative;
            height: 70px;
            margin-top: 0;
            margin-bottom: 5px;
        }

        .img-cap {
            position: absolute;
            top: -20px;
            left: -70px;
            width: 110px;
            z-index: 2;
            opacity: 0.9;
        }

        .img-ttd {
            position: absolute;
            top: -10px;
            left: -15px;
            width: 130px;
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
            <h4>JADWAL PENGAWAS ASESMEN AKHIR MADRASAH</h4>
            <h4>TAHUN AJARAN <?= DEFAULT_YEAR ?></h4>
        </div>

        <div class="content-wrapper">
            <!-- Tabel Utama -->
            <div class="main-table-container">
                <table class="main-table">
                    <colgroup>
                        <!-- Mengatur lebar persentase agar tabel pas 100% dan tidak tumpang tindih -->
                        <col style="width: 17%;">
                        <col style="width: 5%;">
                        <col style="width: 13%;">
                        <col style="width: 23%;">
                        <col style="width: 7%;">
                        <col style="width: 7%;">
                        <col style="width: 7%;">
                        <col style="width: 7%;">
                        <col style="width: 7%;">
                        <col style="width: 7%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th rowspan="2">Hari, Tanggal</th>
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
                            <?php foreach ($hari['mapel'] as $mapel): ?>
                                <tr>
                                    <?php if ($first): ?>
                                        <td rowspan="<?= $rowspan ?>" class="center nowrap">
                                            <?= $hari['hari'] ?>, <?= $hari['tanggal'] ?>
                                        </td>
                                        <?php $first = false; ?>
                                    <?php endif; ?>

                                    <td class="center"><?= $mapel['jam_ke'] ?></td>
                                    <td class="center nowrap"><?= $mapel['waktu'] ?></td>
                                    <td class="nowrap"><?= $mapel['nama'] ?></td>
                                    <?php foreach ($mapel['pengawas'] as $p): ?>
                                        <td class="center nowrap"><strong><?= $p ?></strong></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tabel Legenda Kode Guru -->
            <div class="legend-container">
                <table class="legend-table">
                    <thead>
                        <tr>
                            <th colspan="4" style="background:#ddd; font-size:8pt;">DAFTAR KODE PENGAWAS</th>
                        </tr>
                        <tr>
                            <th class="nowrap" style="width:25px;">Kode</th>
                            <th class="nowrap">Nama Guru</th>
                            <th class="nowrap" style="width:25px;">Kode</th>
                            <th class="nowrap">Nama Guru</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $keys = array_keys($gurus);
                        $half = ceil(count($keys) / 2);
                        for ($i = 0; $i < $half; $i++):
                            $k1 = $keys[$i];
                            $n1 = $gurus[$k1];
                            $k2 = isset($keys[$i + $half]) ? $keys[$i + $half] : '';
                            $n2 = isset($keys[$i + $half]) ? $gurus[$keys[$i + $half]] : '';
                            ?>
                            <tr>
                                <td class="center nowrap"><strong><?= $k1 ?></strong></td>
                                <td class="nowrap"><?= $n1 ?></td>
                                <td class="center nowrap"><strong><?= $k2 ?></strong></td>
                                <td class="nowrap"><?= $n2 ?></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
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

    </div>

</body>

</html>