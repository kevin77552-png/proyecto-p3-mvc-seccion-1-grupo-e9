<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Rap2hpoutre\FastExcel\FastExcel;

// Bootstrap the Laravel application so we can resolve services
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/** @var \App\Services\ExcelImportService $service */
$service = $app->make(\App\Services\ExcelImportService::class);

$importsDir = storage_path('app/imports');
if (! file_exists($importsDir)) {
    mkdir($importsDir, 0755, true);
}

$filename = $importsDir . DIRECTORY_SEPARATOR . 'test_import.xlsx';

$spreadsheet = new Spreadsheet();
// create 13 sheets (index 0..12); by default one sheet exists
for ($i = 0; $i < 13; $i++) {
    if ($i === 0) {
        $sheet = $spreadsheet->getActiveSheet();
    } else {
        $sheet = $spreadsheet->createSheet();
    }

    // name sheet 12 (index 12) later
    if ($i === 12) {
        $sheet->setTitle('JUNIO 2025 (2)');

        // helper to convert 1-based column index to letter
        $colLetter = function (int $n) {
            $letters = '';
            while ($n > 0) {
                $mod = ($n - 1) % 26;
                $letters = chr(65 + $mod) . $letters;
                $n = (int)(($n - $mod) / 26);
            }
            return $letters;
        };

        // Write 7 decorative header rows
        for ($r = 1; $r <= 7; $r++) {
            $sheet->setCellValue($colLetter(1) . $r, "Header row {$r}");
        }

        // Data starts at row 8
        $row = 8;
        // Good row
        $sheet->setCellValue($colLetter(2) . $row, '1001'); // index 1 -> col 2 (B)
        $sheet->setCellValue($colLetter(3) . $row, 'Repuesto A'); // index 2 -> col 3 (C)
        $sheet->setCellValue($colLetter(18) . $row, 'CODE-A'); // index 17 -> col 18 (R)
        $sheet->setCellValue($colLetter(33) . $row, '12.5'); // index 32 -> col 33 (AF)

        // Bad row (non-numeric ID)
        $row++;
        $sheet->setCellValue($colLetter(2) . $row, 'N/A');
        $sheet->setCellValue($colLetter(3) . $row, 'Basura');
        $sheet->setCellValue($colLetter(18) . $row, 'CODE-B');
        $sheet->setCellValue($colLetter(33) . $row, '5');

        // Another good row with formatted stock
        $row++;
        $sheet->setCellValue($colLetter(2) . $row, '2002');
        $sheet->setCellValue($colLetter(3) . $row, 'Repuesto C');
        $sheet->setCellValue($colLetter(18) . $row, 'CODE-C');
        $sheet->setCellValue($colLetter(33) . $row, '1,234.56');
    } else {
        $sheet->setTitle('Sheet' . ($i + 1));
    }
}

$writer = new Xlsx($spreadsheet);
$writer->save($filename);

echo "Test file written to: {$filename}\n";

// Inspect sheet names using PhpSpreadsheet
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
$sheetCount = $reader->getSheetCount();
echo "Workbook sheet count: {$sheetCount}\n";
foreach ($reader->getSheetNames() as $idx => $name) {
    echo "  [{$idx}] {$name}\n";
}

// Run import
try {
    echo "\n-- Debug: read file with FastExcel->sheet(12)->import() --\n";
    $fx = new FastExcel();
    $fx->sheet(12);
    $raw = $fx->import($filename);
    echo "Rows returned by FastExcel import() for sheet(12): " . $raw->count() . "\n";

    echo "\n-- Debug: read file with FastExcel->sheet(13)->import() --\n";
    $fx2 = new FastExcel();
    $fx2->sheet(13);
    $raw2 = $fx2->import($filename);
    echo "Rows returned by FastExcel import() for sheet(13): " . $raw2->count() . "\n";

    echo "\n-- Debug: run import WITH callback to inspect rows and keys --\n";
    $rowCounter = 0;
    $fx3 = new FastExcel();
    $fx3->sheet(13);
    $collected = $fx3->import($filename, function (array $row) use (&$rowCounter) {
        $rowCounter++;
        echo "Callback row #{$rowCounter}: ";
        var_dump($row);
        // return raw row so import collects them
        return $row;
    });
    echo "Collected by callback: " . $collected->count() . "\n";
    $i = 0;
    foreach ($raw as $r) {
        if ($i++ >= 10) break;
        var_dump($r);
    }

    echo "\n-- Now run service import (current configuration) --\n";
    $rows = $service->importInventoryFromFastExcel($filename);
    echo "Import completed, processed rows: " . $rows->count() . "\n";
} catch (Exception $e) {
    echo "Import failed: " . $e->getMessage() . "\n";
}

return 0;
