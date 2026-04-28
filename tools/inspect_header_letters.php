<?php
require __DIR__.'/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file='C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if(!file_exists($file)){
    echo "no file\n"; exit(1);
}
$reader=IOFactory::createReaderForFile($file);
$spreadsheet=$reader->load($file);
$sheet=$spreadsheet->getSheet(0);
$headerRow = 7;
$maxCol = $sheet->getHighestColumn();
$maxIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);
for($c=1;$c<=$maxIdx;$c++){
    $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
    $value = $sheet->getCellByColumnAndRow($c,$headerRow)->getValue();
    $idx = $c - 1;
    echo "{$letter} (idx{$idx}): ";
    var_export($value);
    echo "\n";
}

echo "\nRow 5 contents:\n";
$row=5;
for($c=1;$c<=$maxIdx;$c++){
    $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
    $value=$sheet->getCellByColumnAndRow($c,$row)->getValue();
    $idx = $c - 1;
    echo "{$letter} (idx{$idx}): "; var_export($value); echo "\n";
}
