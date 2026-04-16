<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$db = new Database();

// 1. Fetch Mapels, Materis and their Aspects
$db->query("SELECT * FROM mapel ORDER BY nama_mapel ASC");
$mapels = $db->resultSet();
foreach ($mapels as &$m) {
    $db->query("SELECT * FROM materi_penilaian WHERE mapel_id = :id ORDER BY id ASC");
    $db->bind(':id', $m['id']);
    $m['materis'] = $db->resultSet();
    
    $db->query("SELECT * FROM aspek_penilaian WHERE mapel_id = :mid ORDER BY materi_id ASC, id ASC");
    $db->bind(':mid', $m['id']);
    $all_aspeks = $db->resultSet();
    
    $m['grouped_aspeks'] = [];
    foreach ($all_aspeks as $a) {
        $mid = $a['materi_id'] ?? 0;
        $m['grouped_aspeks'][$mid][] = $a;
    }
}
unset($m);

// 2. Fetch All Students
$db->query("SELECT * FROM siswa ORDER BY kelas ASC, nama_lengkap ASC");
$siswas = $db->resultSet();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Rekap Nilai Praktik');

// Headers Static
$sheet->setCellValue('A1', 'NO')->mergeCells('A1:A3');
$sheet->setCellValue('B1', 'NISN')->mergeCells('B1:B3');
$sheet->setCellValue('C1', 'NO. PESERTA')->mergeCells('C1:C3');
$sheet->setCellValue('D1', 'NAMA LENGKAP')->mergeCells('D1:D3');
$sheet->setCellValue('E1', 'KELAS')->mergeCells('E1:E3');

$currentCol = 6; 
$aspect_col_map = [];
$materi_avg_col_map = [];
$mapel_avg_col_map = [];

foreach ($mapels as $m) {
    if (empty($m['grouped_aspeks'])) continue;
    $mapelStartCol = $currentCol;
    
    // 1. Defined Materis
    foreach ($m['materis'] as $m_idx => $mat) {
        $m_aspeks = $m['grouped_aspeks'][$mat['id']] ?? [];
        if (empty($m_aspeks)) continue;
        
        $matStartColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
        $colSpan = count($m_aspeks) + 1;
        $matEndColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol + $colSpan - 1);
        
        $sheet->setCellValue($matStartColStr . '2', 'M' . ($m_idx + 1))->mergeCells($matStartColStr . '2:' . $matEndColStr . '2');
        
        foreach ($m_aspeks as $a_idx => $a) {
            $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol + $a_idx);
            $sheet->setCellValue($colStr . '3', 'A' . ($a_idx + 1)); 
            $aspect_col_map[$m['id']][$mat['id']][$a['id']] = $colStr;
            $sheet->getColumnDimension($colStr)->setWidth(6);
        }
        
        $mAvgColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol + count($m_aspeks));
        $sheet->setCellValue($mAvgColStr . '3', 'M' . ($m_idx + 1) . '-RATA');
        $materi_avg_col_map[$m['id']][$mat['id']] = $mAvgColStr;
        $sheet->getColumnDimension($mAvgColStr)->setWidth(10);
        $currentCol += $colSpan;
    }
    
    // 2. Orphaned Aspects
    if (!empty($m['grouped_aspeks'][0])) {
        $o_aspeks = $m['grouped_aspeks'][0];
        $matStartColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
        $colSpan = count($o_aspeks) + 1;
        $matEndColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol + $colSpan - 1);
        
        $sheet->setCellValue($matStartColStr . '2', 'Lain-lain')->mergeCells($matStartColStr . '2:' . $matEndColStr . '2');
        foreach ($o_aspeks as $a_idx => $a) {
            $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol + $a_idx);
            $sheet->setCellValue($colStr . '3', 'A' . ($a_idx + 1)); 
            $aspect_col_map[$m['id']][0][$a['id']] = $colStr;
            $sheet->getColumnDimension($colStr)->setWidth(6);
        }
        $mAvgColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol + count($o_aspeks));
        $sheet->setCellValue($mAvgColStr . '3', 'L-RATA');
        $materi_avg_col_map[$m['id']][0] = $mAvgColStr;
        $sheet->getColumnDimension($mAvgColStr)->setWidth(10);
        $currentCol += $colSpan;
    }
    
    $mapelAvgColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
    $sheet->setCellValue($mapelAvgColStr . '2', 'FINAL')->setCellValue($mapelAvgColStr . '3', 'RATA');
    $mapel_avg_col_map[$m['id']] = $mapelAvgColStr;
    $sheet->getColumnDimension($mapelAvgColStr)->setWidth(10);
    
    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mapelStartCol) . '1', $m['nama_mapel']);
    $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mapelStartCol) . '1:' . $mapelAvgColStr . '1');
    $currentCol++;
}

// Grand Average
$grandAvgColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
$sheet->setCellValue($grandAvgColStr . '1', 'RATA AKHIR')->mergeCells($grandAvgColStr . '1:' . $grandAvgColStr . '3');
$sheet->getColumnDimension($grandAvgColStr)->setWidth(12);


$row = 4;
foreach ($siswas as $idx => $s) {
    $sheet->setCellValue('A' . $row, $idx + 1)->setCellValue('B' . $row, $s['nisn'])->setCellValue('C' . $row, $s['nomor_peserta'])->setCellValue('D' . $row, $s['nama_lengkap'])->setCellValue('E' . $row, $s['kelas']);
    $db->query("SELECT aspek_id, nilai_angka FROM nilai_praktik WHERE siswa_id = :sid");
    $db->bind(':sid', $s['id']);
    $score_res = $db->resultSet();
    $score_map = []; foreach ($score_res as $sr) { $score_map[$sr['aspek_id']] = $sr['nilai_angka']; }
    $grand_total_scores = [];
    foreach ($mapels as $m) {
        if (empty($m['grouped_aspeks'])) continue;
        $m_avgs = [];
        foreach ($m['grouped_aspeks'] as $mid => $a_list) {
            $sum = 0; $count = 0;
            foreach ($a_list as $a) {
                if (isset($score_map[$a['id']])) {
                    $sheet->setCellValue($aspect_col_map[$m['id']][$mid][$a['id']] . $row, $score_map[$a['id']]);
                    $sum += $score_map[$a['id']]; $count++;
                }
            }
            if ($count > 0) {
                $avg = $sum / count($a_list);
                $sheet->setCellValue($materi_avg_col_map[$m['id']][$mid] . $row, round($avg, 2));
                $m_avgs[] = $avg;
            }
        }
        if (!empty($m_avgs)) {
            $div = count($m['materis']); if (isset($m['grouped_aspeks'][0]) && $div > 0) $div++;
            $final_avg = array_sum($m_avgs) / max($div, 1);
            $sheet->setCellValue($mapel_avg_col_map[$m['id']] . $row, round($final_avg, 2));
            $grand_total_scores[] = $final_avg;
        }
    }
    if (!empty($grand_total_scores)) {
        $sheet->setCellValue($grandAvgColStr . $row, round(array_sum($grand_total_scores) / count($grand_total_scores), 2));
    }
    $row++;
}

// Global Styling
$sheet->getStyle('A1:' . $grandAvgColStr . '3')->getFont()->setBold(true);
$sheet->getStyle('A1:' . $grandAvgColStr . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle('A1:' . $grandAvgColStr . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
$sheet->freezePane('F4');

// Legend Sheet
$legendSheet = $spreadsheet->createSheet();
$legendSheet->setTitle('Keterangan');
$legendSheet->setCellValue('A1', 'KETERANGAN KODE MATERI DAN ASPEK PENILAIAN')->mergeCells('A1:C1');
$legendSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$legendSheet->setCellValue('A3', 'MATA PELAJARAN')->setCellValue('B3', 'KODE')->setCellValue('C3', 'NAMA MATERI / ASPEK');
$legendSheet->getStyle('A3:C3')->getFont()->setBold(true);
$lRow = 4;
foreach ($mapels as $m) {
    if (empty($m['grouped_aspeks'])) continue;
    $legendSheet->setCellValue('A' . $lRow, $m['nama_mapel'])->getStyle('A' . $lRow)->getFont()->setBold(true);
    foreach ($m['materis'] as $m_idx => $mat) {
        $m_aspeks = $m['grouped_aspeks'][$mat['id']] ?? []; if (empty($m_aspeks)) continue;
        $legendSheet->setCellValue('B' . $lRow, 'M' . ($m_idx + 1))->setCellValue('C' . $lRow, $mat['nama_materi'])->getStyle('B' . $lRow . ':C' . $lRow)->getFont()->setBold(true);
        $lRow++;
        foreach ($m_aspeks as $a_idx => $a) {
            $legendSheet->setCellValue('B' . $lRow, 'A' . ($a_idx + 1))->setCellValue('C' . $lRow, $a['nama_aspek']);
            $lRow++;
        }
    }
    if (!empty($m['grouped_aspeks'][0])) {
        $legendSheet->setCellValue('B' . $lRow, 'Lain-lain')->getStyle('B' . $lRow)->getFont()->setBold(true); $lRow++;
        foreach ($m['grouped_aspeks'][0] as $a_idx => $a) {
            $legendSheet->setCellValue('B' . $lRow, 'A' . ($a_idx + 1))->setCellValue('C' . $lRow, $a['nama_aspek']);
            $lRow++;
        }
    }
    $lRow++;
}
$legendSheet->getColumnDimension('A')->setAutoSize(true); $legendSheet->getColumnDimension('C')->setAutoSize(true);
$spreadsheet->setActiveSheetIndex(0);

// Identity Widths
$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(28);
$sheet->getColumnDimension('D')->setWidth(40);
$sheet->getColumnDimension('E')->setWidth(10);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Rekap_Nilai_Lengkap_' . date('Ymd_His') . '.xlsx"');
$writer = new Xlsx($spreadsheet); $writer->save('php://output');
exit;
