<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
$reader = IOFactory::createReaderForFile($file);
$reader->setReadDataOnly(true);
$ss = $reader->load($file);
$sheet = $ss->getSheet(0);
$cols = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());
for ($c = 1; $c <= $cols; $c++) {
    $coord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c) . '8';
    $val = $sheet->getCell($coord)->getValue();
    echo "$coord => " . json_encode($val, JSON_UNESCAPED_UNICODE) . "\n";
}
