<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$input='C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if(!file_exists($input)){
    echo "Input not found: {$input}\n"; exit(1);
}

$reader = IOFactory::createReaderForFile($input);
$sheetNames = $reader->listWorksheetNames($input);
foreach($sheetNames as $i => $name){
    $idx = $i + 1;
    echo "Sheet {$idx}: {$name}\n";
}
