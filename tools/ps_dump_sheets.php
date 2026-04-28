<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$input='C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
$out='tools/ps_map_output.txt';
if(!file_exists($input)){
    echo "Input not found: {$input}\n"; exit(1);
}

$reader = IOFactory::createReaderForFile($input);
$sheetNames = $reader->listWorksheetNames($input);
$f=fopen($out,'w');
foreach($sheetNames as $i => $name){
    fwrite($f, "=== SHEET " . ($i+1) . " - {$name} ===\n");
    $spreadsheet = $reader->load($input);
    $sheet = $spreadsheet->getSheet($i);
    $maxRow = min(40, $sheet->getHighestRow());
    $maxCol = 36; // up to AJ
    for($r=1;$r<=$maxRow;$r++){
        $line = [];
        for($c=1;$c<=$maxCol;$c++){
            $colLetter = '';
            $n = $c;
            while($n > 0){
                $n--;
                $colLetter = chr(65 + ($n % 26)) . $colLetter;
                $n = intval($n/26);
            }
            $cell = $sheet->getCell($colLetter . $r);
            $val = $cell ? $cell->getCalculatedValue() : null;
            $line[] = $val;
        }
        fwrite($f, "R{$r}: " . json_encode($line, JSON_UNESCAPED_UNICODE) . "\n");
    }
    fwrite($f, "\n");
    // free memory
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
}
fclose($f);
echo "Wrote output to $out\n";
