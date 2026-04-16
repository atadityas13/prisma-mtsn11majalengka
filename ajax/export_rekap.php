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
    // Fetch Materis
    $db->query("SELECT * FROM materi_penilaian WHERE mapel_id = :id ORDER BY id ASC");
    $db->bind(':id', $m['id']);
    $m['materis'] = $db->resultSet();
    
    foreach ($m['materis'] as &$mat) {
        $db->query("SELECT * FROM aspek_penilaian WHERE materi_id = :id ORDER BY id ASC");
        $db->bind(':id', $mat['id']);
        $mat['aspects'] = $db->resultSet();
    }
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
$sheet->mergeCells('A1:A3');
$sheet->setCellValue('B1', 'NISN');
$sheet->mergeCells('B1:B3');
$sheet->setCellValue('C1', 'NO. PESERTA');
$sheet->mergeCells('C1:C3');
$sheet->setCellValue('D1', 'NAMA LENGKAP');
$sheet->mergeCells('D1:D3');
$sheet->setCellValue('E1', 'KELAS');
$sheet->mergeCells('E1:E3');

// Dynamic Headers for Mapels
$currentCol = 6; // Column 'F' starts here
$aspect_col_map = [];
$materi_avg_col_map = [];
$mapel_avg_col_map = [];

foreach ($mapels as $m) {
    if (empty($m['materis'])) continue;
    
    $mapelStartCol = $currentCol;
    
    foreach ($m['materis'] as $mat) {
        $matStartColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
        $aspectCount = count($mat['aspects']);
        
        // Cols per Materi: aspects + 1 for Materi Avg
        $colSpan = ($aspectCount > 0 ? $aspectCount : 1) + 1;
        $matEndColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol + $colSpan - 1);
        
        // Header Row 2: Materi Name
        $sheet->setCellValue($matStartColStr . '2', $mat['nama_materi']);
        $sheet->mergeCells($matStartColStr . '2:' . $matEndColStr . '2');
        
        // Header Row 3: Aspects
        if ($aspectCount > 0) {
            foreach ($mat['aspects'] as $idx => $a) {
                $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol + $idx);
                $sheet->setCellValue($colStr . '3', 'A' . ($a['id'])); // Use ID or sequence? I'll use A1, A2 global seq later
                $aspect_col_map[$m['id']][$a['id']] = $colStr;
            }
        }
        
        // Materi Average Column
        $mAvgColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol + $colSpan - 1);
        $sheet->setCellValue($mAvgColStr . '3', 'M-RATA');
        $materi_avg_col_map[$m['id']][$mat['id']] = $mAvgColStr;
        
        $currentCol += $colSpan;
    }
    
    // Final column per subject is "FINAL RATA"
    $mapelAvgColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
    $sheet->setCellValue($mapelAvgColStr . '2', 'FINAL');
    $sheet->setCellValue($mapelAvgColStr . '3', 'RATA');
    $sheet->mergeCells($mapelAvgColStr . '2:' . $mapelAvgColStr . '2'); // Already 2 rows high because of row 1 header
    $mapel_avg_col_map[$m['id']] = $mapelAvgColStr;
    
    // Merge Mapel Header
    $mapelStartColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mapelStartCol);
    $mapelEndColStr = $mapelAvgColStr;
    $sheet->setCellValue($mapelStartColStr . '1', $m['nama_mapel']);
    $sheet->mergeCells($mapelStartColStr . '1:' . $mapelEndColStr . '1');
    
    $currentCol++;
}

// Grand Average Header
$grandAvgColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
$sheet->setCellValue($grandAvgColStr . '1', 'RATA AKHIR');
$sheet->mergeCells($grandAvgColStr . '1:' . $grandAvgColStr . '3');

// Data Population
$row = 4;
foreach ($siswas as $idx => $s) {
    $sheet->setCellValue('A' . $row, $idx + 1);
    $sheet->setCellValue('B' . $row, $s['nisn']);
    $sheet->setCellValue('C' . $row, $s['nomor_peserta']);
    $sheet->setCellValue('D' . $row, $s['nama_lengkap']);
    $sheet->setCellValue('E' . $row, $s['kelas']);
    
    // Pre-fetch all scores for student
    $db->query("SELECT aspek_id, nilai_angka FROM nilai_praktik WHERE siswa_id = :sid");
    $db->bind(':sid', $s['id']);
    $score_res = $db->resultSet();
    $score_map = [];
    foreach ($score_res as $sr) { $score_map[$sr['aspek_id']] = $sr['nilai_angka']; }
    
    $grand_total_scores = [];
    
    foreach ($mapels as $m) {
        if (empty($m['materis'])) continue;
        
        $materi_avgs = [];
        foreach ($m['materis'] as $mat) {
            $m_sum = 0;
            $m_count = 0;
            foreach ($mat['aspects'] as $a) {
                if (isset($score_map[$a['id']])) {
                    $sheet->setCellValue($aspect_col_map[$m['id']][$a['id']] . $row, $score_map[$a['id']]);
                    $m_sum += $score_map[$a['id']];
                    $m_count++;
                }
            }
            
            if ($m_count > 0) {
                $m_avg = $m_sum / count($mat['aspects']);
                $sheet->setCellValue($materi_avg_col_map[$m['id']][$mat['id']] . $row, round($m_avg, 2));
                $materi_avgs[] = $m_avg;
            }
        }
        
        if (!empty($materi_avgs)) {
            $mapel_avg = array_sum($materi_avgs) / count($m['materis']);
            $sheet->setCellValue($mapel_avg_col_map[$m['id']] . $row, round($mapel_avg, 2));
            $grand_total_scores[] = $mapel_avg;
        }
    }
    
    if (!empty($grand_total_scores)) {
        $sheet->setCellValue($grandAvgColStr . $row, round(array_sum($grand_total_scores) / count($mapels), 2));
    }
    
    $row++;
}

// Styling Sheet 1
$lastCol = $grandAvgColStr;
$sheet->getStyle('A1:' . $lastCol . '3')->getFont()->setBold(true);
$sheet->getStyle('A1:' . $lastCol . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:' . $lastCol . '3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle('A1:' . $lastCol . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
$sheet->freezePane('F4');

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
