<?php
require __DIR__.'/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file='C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if(!file_exists($file)){
    echo "no file\n"; exit(1);
}
$reader = IOFactory::createReaderForFile($file);
$reader->setReadDataOnly(true);
// load only the first sheet by name or index to save memory
$sheetNames = $reader->listWorksheetNames($file);
$firstSheet = $sheetNames[0] ?? null;
if($firstSheet){
    $reader->setLoadSheetsOnly($firstSheet);
}
$spreadsheet = $reader->load($file);
$sheet = $spreadsheet->getSheet(0);
$start = 5;
$end = 9;
$maxCol = $sheet->getHighestColumn();
for($r = $start; $r <= $end; $r++){
    $row = $sheet->rangeToArray('A'.$r.':'.$maxCol.$r)[0];
    echo "Excel row {$r}: ";
    print_r($row);
}
