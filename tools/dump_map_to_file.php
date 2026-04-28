<?php
require __DIR__ . '/../vendor/autoload.php';
use Rap2hpoutre\FastExcel\FastExcel;

$input='C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
$out='tools/map_output.txt';
if(!file_exists($input)){
    echo "Input not found: {$input}\n"; exit(1);
}

$fx=new FastExcel();
$fx=$fx->withoutHeaders()->startRow(8);
$sheetNum=13;
$rows=(clone $fx)->sheet($sheetNum)->import($input);

$f=fopen($out,'w');
if(!$f){ echo "No pude abrir $out para escribir\n"; exit(1); }

$i=0; $limit=200;
foreach($rows as $idx=>$row){
    fwrite($f, "=== ROW {$idx} ===\n");
    fwrite($f, print_r($row, true) . "\n");
    $i++; if($i>=$limit) break;
}

fclose($f);
echo "Wrote output to $out\n";
