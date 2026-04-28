<?php
require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file='C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';

if(!file_exists($file)){
    echo "file not found\n"; exit(1);
}
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getSheet(12); // zero-based 12 -> 13th sheet
// get row 8
$row=8;
$maxCol=$sheet->getHighestColumn();
$maxColIndex=\PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);

for($c=1;$c<=$maxColIndex;$c++){
    $cell = $sheet->getCellByColumnAndRow($c, $row)->getValue();
    echo "col{$c}: ".var_export($cell,true)."\n";
}
