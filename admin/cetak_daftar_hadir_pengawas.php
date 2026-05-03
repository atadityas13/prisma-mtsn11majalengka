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
            padding: 8mm;
            margin: 0 auto 8mm;
            background: #fff;
            box-shadow: 0 0 8px rgba(0,0,0,.08);
            page-break-after: always;
        }
        .page:last-of-type { page-break-after: auto; }

        /* ── Kop ── */
        .kop { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 14px; }
        .kop img { height: 72px; }
        .kop-text { flex: 1; text-align: center; line-height: 1.45; }
        .kop-text h3 { font-size: 12pt; text-transform: uppercase; margin-bottom: 2px; }
        .kop-text h2 { font-size: 14pt; font-weight: 900; text-transform: uppercase; margin-bottom: 2px; }
        .kop-text p  { font-size: 9pt; }

        /* ── Judul ── */
        .judul { text-align: center; margin-bottom: 14px; }
        .judul h4 { font-size: 13pt; text-transform: uppercase; text-decoration: underline; font-weight: 700; letter-spacing: .5px; margin: 0; }

        /* ── Info Grid ── */
        .info-table { width: 100%; margin-bottom: 14px; border-collapse: collapse; font-size: 10.5pt; }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .info-table td:nth-child(2) { width: 12px; text-align: center; }
        .info-label { width: 150px; }

        /* ── Tabel Hadir ── */
        table.hadir { width: 100%; border-collapse: collapse; font-size: 10pt; table-layout: fixed; }
        table.hadir th, table.hadir td { border: 1px solid #000; padding: 6px 7px; vertical-align: middle; }
        table.hadir thead th { background: #f0f0f0; text-align: center; font-weight: 700; }
        table.hadir tbody td.center { text-align: center; }
        table.hadir tbody tr { min-height: 26px; }
        table.hadir th:nth-child(1), table.hadir td:nth-child(1) { width: 28px; }
        table.hadir th:nth-child(2), table.hadir td:nth-child(2) { width: 140px; }
        table.hadir th:nth-child(3), table.hadir td:nth-child(3) { width: 50px; }
        table.hadir th:nth-child(4), table.hadir td:nth-child(4) { width: 48px; }
        table.hadir th:nth-child(5), table.hadir td:nth-child(5) { width: auto; }
        table.hadir th:nth-child(6), table.hadir td:nth-child(6) { width: 130px; }

        /* ── TTD ── */
        .ttd { margin-top: 24px; display: flex; justify-content: space-between; }
        .ttd-box { width: 240px; text-align: center; }
        .ttd-box .ttd-space { height: 65px; }
        .ttd-box p { margin: 2px 0; font-size: 10.5pt; }

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

foreach ($all_entries as $entry):
    $pengawas_list = array_map(function($kode) use ($gurus) {
        return isset($gurus[$kode]) ? $gurus[$kode] : $kode;
    }, $entry['pengawas']);

    $entry['tanggal'] = str_replace(array_keys($bulan_ind_to_eng), array_values($bulan_ind_to_eng), $entry['tanggal']);
    $tgl_obj = new DateTime($entry['tanggal']);
    $hari     = $entry['hari'];
    $tgl_fmt  = $hari . ', ' . $tgl_obj->format('d') . ' ' . $bulan_map[(int)$tgl_obj->format('m')] . ' ' . $tgl_obj->format('Y');
    $jam_fmt  = $entry['waktu'] . ' WIB';
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

    <!-- Info -->
    <table class="info-table">
        <tr>
            <td class="info-label">Mata Pelajaran</td>
            <td>:</td>
            <td><strong><?= htmlspecialchars($entry['nama_mapel']) ?></strong></td>
            <td class="info-label" style="padding-left:24px;">Tanggal</td>
            <td>:</td>
            <td><?= $tgl_fmt ?></td>
        </tr>
        <tr>
            <td class="info-label">Jam Ke</td>
            <td>:</td>
            <td><?= htmlspecialchars($entry['jam_ke']) ?></td>
            <td class="info-label" style="padding-left:24px;">Waktu</td>
            <td>:</td>
            <td><?= $jam_fmt ?></td>
        </tr>
        <tr>
            <td class="info-label">Tahun Pelajaran</td>
            <td>:</td>
            <td><?= DEFAULT_YEAR ?></td>
            <td class="info-label" style="padding-left:24px;"></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <!-- Tabel Hadir -->
    <table class="hadir">
        <thead>
            <tr>
                <th>No</th>
                <th>Hari / Tanggal</th>
                <th>Jam</th>
                <th>Ruang</th>
                <th>Nama Pengawas</th>
                <th>Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $room_count = count($pengawas_list); ?>
            <?php for ($i = 0; $i < $room_count; $i++): ?>
                <tr>
                    <?php if ($i === 0): ?>
                        <td class="center" rowspan="<?= $room_count ?>">1</td>
                        <td class="center" rowspan="<?= $room_count ?>"><?= $tgl_fmt ?></td>
                        <td class="center" rowspan="<?= $room_count ?>"><?= htmlspecialchars($entry['jam_ke']) ?></td>
                    <?php endif; ?>
                    <td class="center"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($pengawas_list[$i]) ?></td>
                    <td></td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- TTD -->
    <div class="ttd">
        <div class="ttd-box">
            <p>Mengetahui,</p>
            <p>Kepala Sekolah</p>
            <div class="ttd-space"></div>
            <p><strong>Drs. H. AJI SUHARDI</strong></p>
            <p>NIP. 19651231 199003 1 012</p>
        </div>
        <div class="ttd-box">
            <p>Majalengka, <?= $tgl_fmt ?></p>
            <p>Koordinator Asesmen</p>
            <div class="ttd-space"></div>
            <p><strong>................................</strong></p>
        </div>
    </div>
</div>
<?php endforeach; ?>

</body>
</html>