<?php
/**
 * Generate & Download Template Excel untuk Import Data Siswa
 * Menggunakan PhpSpreadsheet (sudah ada di vendor/)
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../vendor/autoload.php';

Auth::restrictTo('admin');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Data Siswa');

// ── Header Row ──────────────────────────────────────────────────────────────
$headers = ['NISN', 'Nama Lengkap', 'Nomor Peserta', 'Jenis Kelamin (L/P)', 'Kelas'];
$cols    = ['A', 'B', 'C', 'D', 'E'];

foreach ($cols as $i => $col) {
    $sheet->setCellValue($col . '1', $headers[$i]);
}

// Style header: background biru, teks putih, bold, tengah
$headerStyle = [
    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4154f1']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
];
$sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(22);

// ── Contoh Data (3 baris) ───────────────────────────────────────────────────
$examples = [
    ['1234567890', 'Ahmad Budi Santoso', '001', 'L', 'IX-A'],
    ['0987654321', 'Siti Nurhaliza',     '002', 'P', 'IX-B'],
    ['1122334455', 'Rizki Maulana',      '003', 'L', 'IX-A'],
];

foreach ($examples as $rowIdx => $row) {
    $phpRow = $rowIdx + 2;
    foreach ($cols as $colIdx => $col) {
        $sheet->setCellValue($col . $phpRow, $row[$colIdx]);
    }
    // Style baris contoh: latar kuning muda
    $sheet->getStyle("A{$phpRow}:E{$phpRow}")->applyFromArray([
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFF9E6']],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
    ]);
}

// ── Baris keterangan di bawah data ──────────────────────────────────────────
$noteRow = count($examples) + 3;
$sheet->setCellValue('A' . $noteRow, '* Hapus baris contoh (baris 2-4) sebelum mengupload');
$sheet->getStyle('A' . $noteRow)->applyFromArray([
    'font'      => ['italic' => true, 'color' => ['rgb' => 'FF0000'], 'size' => 9],
]);
$sheet->mergeCells("A{$noteRow}:E{$noteRow}");

$sheet->setCellValue('A' . ($noteRow + 1), '* Kolom Jenis Kelamin hanya boleh diisi dengan huruf L atau P');
$sheet->getStyle('A' . ($noteRow + 1))->applyFromArray([
    'font'      => ['italic' => true, 'color' => ['rgb' => 'AA0000'], 'size' => 9],
]);
$sheet->mergeCells('A' . ($noteRow + 1) . ':E' . ($noteRow + 1));

// ── Auto Width ───────────────────────────────────────────────────────────────
$sheet->getColumnDimension('A')->setWidth(16);
$sheet->getColumnDimension('B')->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(18);
$sheet->getColumnDimension('D')->setWidth(22);
$sheet->getColumnDimension('E')->setWidth(12);

// ── Freeze header row ───────────────────────────────────────────────────────
$sheet->freezePane('A2');

// ── Output ke browser ───────────────────────────────────────────────────────
$filename = 'template_import_siswa.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
