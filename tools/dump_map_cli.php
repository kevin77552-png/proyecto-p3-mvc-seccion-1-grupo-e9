<?php
require __DIR__ . '/../vendor/autoload.php';
use Rap2hpoutre\FastExcel\FastExcel;

$file='C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if(!file_exists($file)){
    echo "File not found: {$file}\n"; exit(1);
}

$fx=new FastExcel();
$fx=$fx->withoutHeaders()->startRow(8);
$sheetNum=13;
$rows=(clone $fx)->sheet($sheetNum)->import($file);

$limit=200; $i=0;
foreach($rows as $idx=>$row){
    echo "=== ROW {$idx} ===\n";
    echo print_r($row, true) . "\n";
    $i++;
    if($i>=$limit) break;
}

echo "Done.\n";
