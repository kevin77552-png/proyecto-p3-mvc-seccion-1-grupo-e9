<?php
require __DIR__ . '/../vendor/autoload.php';
use Rap2hpoutre\FastExcel\FastExcel;

$file='C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
// read header row 7 separately
$fxh = (new FastExcel())->withoutHeaders()->startRow(7);
$sheetNum = $argv[1] ?? 1; // allow sheet number as first CLI arg (1-based)
$sheetNum = (int) $sheetNum;
if ($sheetNum < 1) {
    $sheetNum = 1;
}
$headerRows = (clone $fxh)->sheet($sheetNum)->import($file);
$header = [];
foreach ($headerRows as $i => $row) {
    // only the first row
    $header = $row;
    break;
}

$fx = new FastExcel();
$fx = $fx->withoutHeaders()->startRow(8);
$rows = (clone $fx)->sheet($sheetNum)->import($file);
$count = 0;

ob_start();
echo "Header (row 7): ";
var_dump($header);
foreach ($rows as $i => $row) {
    echo "row {$i}: ";
    var_dump($row);
    $count++;
    if ($count >= 200) break;
}
$out = ob_get_clean();
file_put_contents(__DIR__ . '/map_output.txt', $out);
echo "Wrote output to tools/map_output.txt\n";
// explicit output
$out = "Header (row 7):\n" . print_r($header, true) . "\n";
$count = 0;
foreach ($rows as $i => $row) {
    $out .= "row {$i}: \n";
    $out .= print_r($row, true) . "\n";
    $count++;
    if ($count >= 200) break;
}
file_put_contents(__DIR__ . '/map_output.txt', $out);
echo "Wrote output to tools/map_output.txt\n";
