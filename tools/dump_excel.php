<?php

require __DIR__ . '/../vendor/autoload.php';

use Rap2hpoutre\FastExcel\FastExcel;

// file path from user
$file = 'C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';

if (! file_exists($file)) {
    echo "File not found: {$file}\n";
    exit(1);
}

// load and dump rows using same logic as service
$fx = new FastExcel();
// user requested no headers (alternative to withoutHeaders())
$fx = $fx->withoutHeaders()->startRow(8);

// first try by name
$sheetName = 'JUNIO 2025 (2)';
$sheetNum = 13; // 1-based guess

function dumpRows($collection) {
    echo "-- dumping ";
    echo get_class($collection) . " rows count=" . $collection->count() . "\n";
    foreach ($collection as $i => $row) {
        echo "row {$i}: ";
        var_dump($row);
    }
}

try {
    $rows = (clone $fx)->sheet($sheetName)->import($file);
    echo "Read " . $rows->count() . " rows with sheet name {$sheetName}\n";
    if ($rows->count() > 0) {
        echo "-- first returned row (row 8 of sheet) --\n";
        var_dump($rows->first());
    }
    // dump all for debugging if needed
    dumpRows($rows);
} catch (\Throwable $e) {
    echo "name read failed: " . $e->getMessage() . "\n";
}

try {
    $rows2 = (clone $fx)->sheet($sheetNum)->import($file);
    echo "Read " . $rows2->count() . " rows with sheet number {$sheetNum}\n";
    dumpRows($rows2);
} catch (\Throwable $e) {
    echo "number read failed: " . $e->getMessage() . "\n";
}

exit(0);
