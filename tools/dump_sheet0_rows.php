<?php
require __DIR__.'/../vendor/autoload.php';
use Rap2hpoutre\FastExcel\FastExcel;

$file = 'C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
$fx = (new FastExcel())->withoutHeaders()->startRow(1);
$rows = $fx->sheet(1)->import($file);
$count=0;
foreach($rows as $i=>$row){
    $excelRow = $i + 1; // since startRow was 1
    echo "sheet0 index {$i} (Excel row {$excelRow}): ";
    print_r($row);
    $count++;
    if($count>=12) break;
}
