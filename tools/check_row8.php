<?php
require __DIR__.'/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file='C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if(!file_exists($file)){
    echo "Archivo no existe\n";
    exit(1);
}

$reader = IOFactory::createReaderForFile($file);
$spreadsheet = $reader->load($file);
foreach($spreadsheet->getAllSheets() as $index => $sheet) {
    $name = $sheet->getTitle();
    echo "Sheet {$index}: {$name}\n";
    $row = 8;
    $maxCol = $sheet->getHighestColumn();
    $maxColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);
    for ($c = 1; $c <= min(20, $maxColIndex); $c++) {
        $cell = $sheet->getCellByColumnAndRow($c, $row)->getValue();
        echo "  col{$c}: " . var_export($cell, true) . "\n";
    }
    echo "---\n";
}
