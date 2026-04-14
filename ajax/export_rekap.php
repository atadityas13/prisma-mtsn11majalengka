<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$db = new Database();

// 1. Fetch Mapels and their Aspects
$db->query("SELECT * FROM mapel ORDER BY nama_mapel ASC");
$mapels = $db->resultSet();
$mapel_aspects = [];
foreach ($mapels as &$m) {
    $db->query("SELECT * FROM aspek_penilaian WHERE mapel_id = :id ORDER BY id ASC");
    $db->bind(':id', $m['id']);
    $m['aspects'] = $db->resultSet();
}

// 2. Fetch All Students
$db->query("SELECT * FROM siswa ORDER BY kelas ASC, nama_lengkap ASC");
$siswas = $db->resultSet();

$spreadsheet = new Spreadsheet();

// --- SHEET 1: REKAP NILAI ---
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Rekap Nilai Praktik');

// Headers Static
$sheet->setCellValue('A1', 'NO');
$sheet->mergeCells('A1:A2');
$sheet->setCellValue('B1', 'NISN');
$sheet->mergeCells('B1:B2');
$sheet->setCellValue('C1', 'NO. PESERTA');
$sheet->mergeCells('C1:C2');
$sheet->setCellValue('D1', 'NAMA LENGKAP');
$sheet->mergeCells('D1:D2');
$sheet->setCellValue('E1', 'KELAS');
$sheet->mergeCells('E1:E2');

// Dynamic Headers for Mapels
$currentCol = 6; // Column 'F' starts here
$mapel_col_start = [];

foreach ($mapels as $m) {
    $startColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
    $aspectCount = count($m['aspects']);
    
    // We need columns for each aspect + 1 for subject average
    $colSpan = ($aspectCount > 0 ? $aspectCount : 1) + 1;
    $endColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol + $colSpan - 1);
    
    // Header Row 1: Subject Name (Merged)
    $sheet->setCellValue($startColStr . '1', $m['nama_mapel']);
    $sheet->mergeCells($startColStr . '1:' . $endColStr . '1');
    
    // Header Row 2: Aspects (A1, A2...) + Avg
    if ($aspectCount > 0) {
        foreach ($m['aspects'] as $idx => $a) {
            $aspectLabel = 'A' . ($idx + 1);
            $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol + $idx);
            $sheet->setCellValue($colStr . '2', $aspectLabel);
            $aspect_col_map[$m['id']][$a['id']] = $colStr;
        }
    } else {
        $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
        $sheet->setCellValue($colStr . '2', '-');
    }
    
    // Final column per subject is "Rata-rata"
    $avgColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol + $colSpan - 1);
    $sheet->setCellValue($avgColStr . '2', 'RATA');
    $mapel_avg_col_map[$m['id']] = $avgColStr;
    
    $currentCol += $colSpan;
}

// Grand Average Header
$grandAvgColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
$sheet->setCellValue($grandAvgColStr . '1', 'RATA-RATA AKHIR');
$sheet->mergeCells($grandAvgColStr . '1:' . $grandAvgColStr . '2');

// Data Population
$row = 3;
foreach ($siswas as $idx => $s) {
    $sheet->setCellValue('A' . $row, $idx + 1);
    $sheet->setCellValue('B' . $row, $s['nisn']);
    $sheet->setCellValue('C' . $row, $s['nomor_peserta']);
    $sheet->setCellValue('D' . $row, $s['nama_lengkap']);
    $sheet->setCellValue('E' . $row, $s['kelas']);
    
    $grand_total_sum = 0;
    $grand_total_count = 0;
    
    foreach ($mapels as $m) {
        $mapel_sum = 0;
        $mapel_count = 0;
        
        // Fetch scores for this student and mapel
        $db->query("SELECT aspek_id, nilai_angka FROM nilai_praktik WHERE siswa_id = :sid AND mapel_id = :mid");
        $db->bind(':sid', $s['id']);
        $db->bind(':mid', $m['id']);
        $scores = $db->resultSet();
        $score_map = [];
        foreach ($scores as $sc) { $score_map[$sc['aspek_id']] = $sc['nilai_angka']; }
        
        // Fill aspect scores
        foreach ($m['aspects'] as $a) {
            if (isset($score_map[$a['id']])) {
                $colStr = $aspect_col_map[$m['id']][$a['id']];
                $sheet->setCellValue($colStr . $row, $score_map[$a['id']]);
                $mapel_sum += $score_map[$a['id']];
                $mapel_count++;
            }
        }
        
        // Subject Average
        if ($mapel_count > 0) {
            $avg = $mapel_sum / count($m['aspects']); // Divided by total aspects for fairness
            $sheet->setCellValue($mapel_avg_col_map[$m['id']] . $row, round($avg, 2));
            $grand_total_sum += $avg;
            $grand_total_count++;
        }
    }
    
    // Grand Total Average
    if (count($mapels) > 0) {
        $sheet->setCellValue($grandAvgColStr . $row, round($grand_total_sum / count($mapels), 2));
    }
    
    $row++;
}

// Styling Sheet 1
$lastCol = $grandAvgColStr;
$sheet->getStyle('A1:' . $lastCol . '2')->getFont()->setBold(true);
$sheet->getStyle('A1:' . $lastCol . '2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:' . $lastCol . '2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle('A1:' . $lastCol . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
$sheet->freezePane('F3');

// --- SHEET 2: KETERANGAN ASPEK ---
$legendSheet = $spreadsheet->createSheet();
$legendSheet->setTitle('Keterangan Aspek');

$legendSheet->setCellValue('A1', 'KETERANGAN KODE ASPEK PENILAIAN');
$legendSheet->mergeCells('A1:D1');
$legendSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

$legendSheet->setCellValue('A3', 'NO');
$legendSheet->setCellValue('B3', 'MATA PELAJARAN');
$legendSheet->setCellValue('C3', 'KODE');
$legendSheet->setCellValue('D3', 'NAMA ASPEK / KRITERIA');
$legendSheet->getStyle('A3:D3')->getFont()->setBold(true);

$lIdx = 1;
$lRow = 4;
foreach ($mapels as $m) {
    if (count($m['aspects']) > 0) {
        foreach ($m['aspects'] as $aIdx => $a) {
            $legendSheet->setCellValue('A' . $lRow, $lIdx++);
            $legendSheet->setCellValue('B' . $lRow, $m['nama_mapel']);
            $legendSheet->setCellValue('C' . $lRow, 'A' . ($aIdx + 1));
            $legendSheet->setCellValue('D' . $lRow, $a['nama_aspek']);
            $lRow++;
        }
    }
}
$legendSheet->getStyle('A3:D' . ($lRow-1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
$legendSheet->getColumnDimension('B')->setAutoSize(true);
$legendSheet->getColumnDimension('D')->setAutoSize(true);

// Finalize
$spreadsheet->setActiveSheetIndex(0);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Rekap_Nilai_PRISMA_' . date('Ymd_His') . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
