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

$hari_map  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$bulan_map = ['','Januari','Februari','Maret','April','Mei','Juni',
              'Juli','Agustus','September','Oktober','November','Desember'];

$bulan_ind_to_eng = [
    'Januari' => 'January',
    'Februari' => 'February',
    'Maret' => 'March',
    'April' => 'April',
    'Mei' => 'May',
    'Juni' => 'June',
    'Juli' => 'July',
    'Agustus' => 'August',
    'September' => 'September',
    'Oktober' => 'October',
    'November' => 'November',
    'Desember' => 'December'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Pengawas Asesmen — <?= SCHOOL_NAME ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; background: #f0f0f0; color: #000; }

        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        .page {
            width: calc(210mm - 16mm);
            min-height: calc(297mm - 16mm);
            padding: 8mm;
            margin: 0 auto 8mm;
            background: #fff;
            box-shadow: 0 0 8px rgba(0,0,0,.08);
            page-break-after: always;
        }
        .page:last-of-type { page-break-after: auto; }

        /* ── Kop ── */
        .kop { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 8px; }
        .kop img { height: 60px; }
        .kop-text { flex: 1; text-align: center; line-height: 1.1; }
        .kop-text h3 { font-size: 12pt; text-transform: uppercase; margin-bottom: 2px; }
        .kop-text h2 { font-size: 14pt; font-weight: 900; text-transform: uppercase; margin-bottom: 2px; }
        .kop-text p  { font-size: 8pt; }

        /* ── Judul ── */
        .judul { text-align: center; margin-bottom: 10px; }
        .judul h4 { font-size: 13pt; text-transform: uppercase; letter-spacing: 1px; margin: 0; }

        /* ── Tabel Hadir ── */
        table.hadir { width: 100%; border-collapse: collapse; font-size: 9pt; table-layout: fixed; }
        table.hadir th, table.hadir td { border: 1px solid #000; padding: 5px 6px; vertical-align: middle; }
        table.hadir thead th { background: #f0f0f0; text-align: center; font-weight: 700; }
        table.hadir tbody td.center { text-align: center; }
        table.hadir tbody tr { min-height: 24px; }
        .day-group { page-break-inside: avoid; break-inside: avoid; }
        table.hadir th:nth-child(1), table.hadir td:nth-child(1) { width: 32px; }
        table.hadir th:nth-child(2), table.hadir td:nth-child(2) { width: 140px; }
        table.hadir th:nth-child(3), table.hadir td:nth-child(3) { width: 55px; }
        table.hadir th:nth-child(4), table.hadir td:nth-child(4) { width: 48px; }
        table.hadir th:nth-child(5), table.hadir td:nth-child(5) { width: 42px; }
        table.hadir th:nth-child(6), table.hadir td:nth-child(6) { width: auto; }
        table.hadir th:nth-child(7), table.hadir td:nth-child(7) { width: 145px; }

        /* ── TTD ── */
        .ttd { margin-top: 18px; display: flex; justify-content: flex-end; }
        .ttd-box { width: 260px; text-align: left; }
        .ttd-box .ttd-space { height: 60px; }
        .ttd-box p { margin: 2px 0; font-size: 10pt; }

        /* ── Print ── */
        .no-print {
            position: fixed; top: 0; left: 0; right: 0;
            background: #333; color: #fff; padding: 10px;
            text-align: center; z-index: 999;
            font-family: Arial, sans-serif; font-size: 13px;
        }

        .no-print button {
            padding: 7px 18px; margin: 0 5px; border: none;
            border-radius: 4px; font-weight: bold; cursor: pointer;
        }
        .btn-print { background: #28a745; color: #fff; }
        .btn-back  { background: #6c757d; color: #fff; }

        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .page { margin: 0; box-shadow: none; width: auto; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button class="btn-print" onclick="window.print()">🖨️ Cetak Daftar Hadir</button>
    <button class="btn-back"  onclick="window.close()">✕ Tutup</button>
</div>

<?php
$all_entries = [];
foreach ($jadwal as $hari) {
    foreach ($hari['mapel'] as $mapel) {
        $all_entries[] = [
            'hari' => $hari['hari'],
            'tanggal' => $hari['tanggal'],
            'jam_ke' => $mapel['jam_ke'],
            'waktu' => $mapel['waktu'],
            'nama_mapel' => $mapel['nama'],
            'pengawas' => $mapel['pengawas']
        ];
    }
}

$session_no = 1;
?>
<div class="page">
    <!-- Kop Surat -->
    <div class="kop">
        <img src="<?= base_url('assets/img/logo-kemenag.png') ?>" alt="Logo Kemenag">
        <div class="kop-text">
            <h3>Kementerian Agama Republik Indonesia</h3>
            <h3>Kantor Kementerian Agama Kabupaten Majalengka</h3>
            <h2><?= SCHOOL_NAME ?></h2>
            <p>Kp. Sindanghurip Desa Maniis Kec. Cingambul Kab. Majalengka<br>
               Telp. (0233) 3600020 &nbsp;|&nbsp; mtsn11majalengka@gmail.com</p>
        </div>
        <img src="<?= base_url('assets/img/logo-mtsn11.png') ?>" alt="Logo MTsN 11">
    </div>

    <!-- Judul -->
    <div class="judul">
        <h4>Daftar Hadir Pengawas Asesmen</h4>
    </div>

    <!-- Tabel Hadir -->
    <table class="hadir">
        <thead>
            <tr>
                <th>Hari ke</th>
                <th>Hari / Tanggal</th>
                <th>Sesi</th>
                <th>Jam ke</th>
                <th>Ruang</th>
                <th>Nama Pengawas</th>
                <th>Tanda Tangan</th>
            </tr>
        </thead>
        <?php
        // Group sessions per day and keep hari/tanggal merged
        $days = [];
        foreach ($all_entries as $entry) {
            $day_key = $entry['hari'] . '|' . $entry['tanggal'];
            if (!isset($days[$day_key])) {
                $days[$day_key] = [
                    'hari' => $entry['hari'],
                    'tanggal' => $entry['tanggal'],
                    'sessions' => []
                ];
            }
            $days[$day_key]['sessions'][] = [
                'jam_ke' => $entry['jam_ke'],
                'waktu' => $entry['waktu'],
                'pengawas' => $entry['pengawas']
            ];
        }
        $day_number = 0;
        ?>
        <?php foreach ($days as $day): ?>
            <?php
            $day_number++;
            $day_rows = 0;
            foreach ($day['sessions'] as $session) {
                $day_rows += count($session['pengawas']);
            }
            $day_rows = max(1, $day_rows);
            $day_date = str_replace(array_keys($bulan_ind_to_eng), array_values($bulan_ind_to_eng), $day['tanggal']);
            $tgl_obj = new DateTime($day_date);
            $tgl_fmt = $day['hari'] . ', ' . $tgl_obj->format('d') . ' ' . $bulan_map[(int)$tgl_obj->format('m')] . ' ' . $tgl_obj->format('Y');
            $first_day_row = true;
            ?>
            <tbody class="day-group">
                <?php foreach ($day['sessions'] as $session_idx => $session): ?>
                    <?php
                    $pengawas_list = array_map(function($kode) use ($gurus) {
                        return isset($gurus[$kode]) ? $gurus[$kode] : $kode;
                    }, $session['pengawas']);
                    $session_rows = count($pengawas_list);
                    $jam_label = htmlspecialchars(trim(explode('(', $session['jam_ke'])[0]));
                    ?>
                    <?php foreach ($pengawas_list as $index => $nama): ?>
                        <tr>
                            <?php if ($index === 0 && $first_day_row): ?>
                                <td class="center" rowspan="<?= $day_rows ?>"><?= $day_number ?></td>
                                <td rowspan="<?= $day_rows ?>"><?= $tgl_fmt ?></td>
                                <?php $first_day_row = false; ?>
                            <?php endif; ?>
                            
                            <?php if ($index === 0): ?>
                                <?php if ($session_idx === 0): ?>
                                    <td class="center" rowspan="12">Sesi 1</td>
                                <?php elseif ($session_idx === 2): ?>
                                    <td class="center" rowspan="12">Sesi 2</td>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if ($index === 0): ?>
                                <td class="center" rowspan="<?= $session_rows ?>"><?= $jam_label ?></td>
                            <?php endif; ?>
                            
                            <td class="center"><?= $index + 1 ?></td>
                            
                            <td><?= htmlspecialchars($nama) ?></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        <?php endforeach; ?>
    </table>

    <div class="ttd">
        <div class="ttd-box">
            <p>Majalengka, <?= date('d') ?> <?= ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][(int) date('m')] ?> <?= date('Y') ?></p>
            <p>Plt. Kepala Madrasah</p>
            <div class="ttd-space"></div>
            <p><strong><u>H. Dede Apip Mustopa, S.Ag.</u></strong></p>
            <p>NIP. 196801171992031002</p>
        </div>
    </div>
</div>

</body>
</html>