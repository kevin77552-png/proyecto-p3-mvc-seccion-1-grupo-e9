<?php
require __DIR__.'/../vendor/autoload.php';
use Rap2hpoutre\FastExcel\FastExcel;

$file = 'C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if (!file_exists($file)) {
    echo "Archivo no encontrado\n";
    exit(1);
}

// Get sheet count via PhpSpreadsheet reader
use PhpOffice\PhpSpreadsheet\IOFactory;
$reader = IOFactory::createReaderForFile($file);
$sheetnames = $reader->listWorksheetNames($file);

$search = 'Conj. de turbocompresor';
foreach ($sheetnames as $idx => $name) {
    echo "Searching in sheet {$idx} ({$name})\n";
    $fx = (new FastExcel())->withoutHeaders()->startRow(1);
    $rows = $fx->sheet($idx+1)->import($file);
    foreach ($rows as $rnum => $row) {
        foreach ($row as $cell) {
            if (is_string($cell) && stripos($cell, $search) !== false) {
                echo "Found substring in sheet {$idx} row {$rnum}: ";
                print_r($row);
                break 3;
            }
        }
    }
}
