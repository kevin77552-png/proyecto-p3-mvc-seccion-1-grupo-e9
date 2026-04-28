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
    foreach([1,2,3,4,5,6,7,8,9,10] as $start){
        fwrite($f, "-- startRow={$start} --\n");
        $fx=new FastExcel();
        $fx=$fx->withoutHeaders()->startRow($start);
        try{
            $rows=(clone $fx)->sheet($name)->import($input);
        }catch(Exception $e){
            fwrite($f, "Error reading sheet {$name} start {$start}: " . $e->getMessage() . "\n");
            continue;
        }
        $j=0;
        foreach($rows as $idx=>$row){
            fwrite($f, "ROW {$idx}: " . substr(print_r($row, true),0,400) . "\n");
            $j++; if($j>=20) break;
        }
        fwrite($f, "\n");
    }
}

fclose($f);
echo "Wrote output to $out\n";
