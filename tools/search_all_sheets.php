<?php
require __DIR__ . '/../vendor/autoload.php';
use Rap2hpoutre\FastExcel\FastExcel;

$file='C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if(!file_exists($file)){ echo "file not found\n"; exit(1); }

$fx=new FastExcel();
$fx=$fx->withoutHeaders();
$sheetCount = 0;

// Determine sheet count via PhpSpreadsheet quickly
$reader=\PhpOffice\PhpSpreadsheet\IOFactory::load($file);
$sheetCount=$reader->getSheetCount();

for($s=1;$s<=$sheetCount;$s++){
    echo "Sheet #{$s}:\n";
    $rows=(clone $fx)->sheet($s)->import($file);
    foreach($rows as $i=>$row){
        if(is_array($row)){
            foreach($row as $idx=>$val){
                if(strval($val)==='12301'){
                    echo "  Found on sheet {$s} row index {$i} col {$idx}\n";
                    var_dump($row);
                    exit(0);
                }
            }
        }
    }
}

echo "value not found in any sheet\n";
