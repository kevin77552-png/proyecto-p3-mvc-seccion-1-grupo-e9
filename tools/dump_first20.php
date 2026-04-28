<?php
require __DIR__ . '/../vendor/autoload.php';
use Rap2hpoutre\FastExcel\FastExcel;

$file='C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
$fx=new FastExcel();
$fx=$fx->withoutHeaders();
$sheetNum=13;
$rows=(clone $fx)->sheet($sheetNum)->import($file);
$count=0;
foreach($rows as $i=>$row){
    echo "row {$i}: ";
    var_dump($row);
    $count++;
    if($count>=20) break;
}
