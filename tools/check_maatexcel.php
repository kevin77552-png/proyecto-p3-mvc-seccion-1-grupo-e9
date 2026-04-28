<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;

$file = 'C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if (!file_exists($file)) {
    echo "no existe $file\n";
    exit(1);
}

$collections = Excel::toArray([], $file);
echo "Sheets count: " . count($collections) . "\n";
foreach ($collections as $i => $sheet) {
    echo "-- sheet {$i} rows: " . count($sheet) . "\n";
    // dump rows 4..12
    for ($r = 4; $r <= 12 && $r < count($sheet); $r++) {
        echo "R{$r}: ";
        print_r($sheet[$r]);
    }
    echo "\n";
}
