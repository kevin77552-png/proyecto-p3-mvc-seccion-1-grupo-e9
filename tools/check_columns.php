<?php

require __DIR__ . '/../vendor/autoload.php';

use Rap2hpoutre\FastExcel\FastExcel;

$file = 'C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if (! file_exists($file)) {
    echo "File not found: {$file}\n";
    exit(1);
}

$fx = new FastExcel();
$fx = $fx->withoutHeaders()->startRow(8);

$sheetNum = 13;

$rows = (clone $fx)->sheet($sheetNum)->import($file);

foreach ($rows as $i => $row) {
    // pad to 50
    if (is_array($row)) {
        $row = array_pad($row, 50, null);
    }
    $len = is_array($row) ? count($row) : 'non-array';
    echo "Row {$i} length={$len} ";
    if (array_key_exists(32, $row)) echo "[32]=" . var_export($row[32], true) . " ";
    if (array_key_exists(41, $row)) echo "[41]=" . var_export($row[41], true) . " ";
    echo "\n";
    if ($i > 10) break; // just inspect first few
}

exit(0);
