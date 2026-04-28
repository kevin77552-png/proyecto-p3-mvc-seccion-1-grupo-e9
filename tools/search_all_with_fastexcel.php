<?php
require __DIR__.'/../vendor/autoload.php';
use Rap2hpoutre\FastExcel\FastExcel;

$file='C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if(!file_exists($file)) { echo "file missing\n"; exit(1); }

$fx=new FastExcel();
$fx=$fx->withoutHeaders();

for($s=1;$s<=20;$s++) {
    echo "Trying sheet {$s}\n";
    try {
        $rows=(clone $fx)->sheet($s)->import($file);
    } catch(Exception $e) {
        echo "  sheet {$s} read failed: " . $e->getMessage() . "\n";
        continue;
    }
    foreach($rows as $i=>$row) {
        if(is_array($row)){
            foreach($row as $idx=>$val){
                if(strval($val)==='12301'){
                    echo "Found 12301 on sheet {$s} row {$i} col {$idx}\n";
                    var_dump($row);
                    exit(0);
                }
            }
        }
    }
}
echo "value not found\n";
