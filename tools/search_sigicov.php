<?php

require __DIR__ . '/../vendor/autoload.php';
use Rap2hpoutre\FastExcel\FastExcel;

$file = 'C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
$fx = new FastExcel();
$fx = $fx->withoutHeaders()->startRow(1);
$sheetNum=13;
$found=false;
$rows=(clone $fx)->sheet($sheetNum)->import($file);
foreach($rows as $i=>$row){
    if(is_array($row)){
        foreach($row as $idx=>$val){
            if(strval($val)==='12301'){
                echo "Found value 12301 at row index {$i} column {$idx}\n";
                var_dump($row);
                $found=true;
                break 2;
            }
        }
    }
}
if(!$found) echo "Value 12301 not found\n";
