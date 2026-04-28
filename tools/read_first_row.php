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

$sheetName = 'JUNIO 2025 (2)';
$sheetNum = 13;

echo "Attempting by name...\n";
try {
    (clone $fx)->sheet($sheetName)->import($file, function(array $row) {
        echo "First row received:\n";
        var_dump($row);
        exit(0);
    });
    echo "No rows returned by name?\n";
} catch (\Throwable $e) {
    echo "Name read failed: " . $e->getMessage() . "\n";
}

echo "Attempting by numeric sheet...\n";
try {
    (clone $fx)->sheet($sheetNum)->import($file, function(array $row) {
        echo "First row received (by index):\n";
        var_dump($row);
        exit(0);
    });
    echo "No rows returned by index?\n";
} catch (\Throwable $e) {
    echo "Index read failed: " . $e->getMessage() . "\n";
}

echo "Reached end without any rows.\n";
