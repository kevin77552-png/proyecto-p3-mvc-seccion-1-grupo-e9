<?php
require __DIR__.'/../vendor/autoload.php';
use Rap2hpoutre\FastExcel\FastExcel;

$file = 'C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if (!file_exists($file)) {
    echo "Archivo no encontrado\n";
    exit(1);
}

// list sheet names using PhpSpreadsheet (fast small read)
use PhpOffice\PhpSpreadsheet\IOFactory;
$reader = IOFactory::createReaderForFile($file);
$sheetnames = $reader->listWorksheetNames($file);
foreach ($sheetnames as $idx => $name) {
    echo "Sheet {$idx} -> {$name}\n";
}

foreach ($sheetnames as $idx => $name) {
    echo "\nReading sheet {$idx} ({$name}) row 8:\n";
    $fx = (new FastExcel())->withoutHeaders()->startRow(8);
    // set sheet by index (1-based)
    $rows = $fx->sheet($idx+1)->import($file);
    $i = 0;
    foreach ($rows as $row) {
        echo "row8: ";
        print_r($row);
        break;
    }
}
