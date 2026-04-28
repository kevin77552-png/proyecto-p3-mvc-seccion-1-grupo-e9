<?php
require __DIR__ . '/../vendor/autoload.php';
use Rap2hpoutre\FastExcel\FastExcel;
use PhpOffice\PhpSpreadsheet\IOFactory;

$input='C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
$out='tools/map_output.txt';
if(!file_exists($input)){
    echo "Input not found: {$input}\n"; exit(1);
}

$reader = IOFactory::createReaderForFile($input);
$sheetNames = $reader->listWorksheetNames($input);
$f=fopen($out,'w');
if(!$f){ echo "No pude abrir $out para escribir\n"; exit(1); }

foreach($sheetNames as $i => $name){
    fwrite($f, "=== SHEET " . ($i+1) . " - {$name} ===\n");
    $fx=new FastExcel();
    $fx=$fx->withoutHeaders()->startRow(8);
    try{
        $rows=(clone $fx)->sheet($name)->import($input);
    }catch(Exception $e){
        fwrite($f, "Error reading sheet {$name}: " . $e->getMessage() . "\n");
        continue;
    }
    $j=0;
    foreach($rows as $idx=>$row){
        fwrite($f, "--- ROW {$idx} ---\n");
        fwrite($f, print_r($row, true) . "\n");
        $j++; if($j>=100) break;
    }
}

fclose($f);
echo "Wrote output to $out\n";
