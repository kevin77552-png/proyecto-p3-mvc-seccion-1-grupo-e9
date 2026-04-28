<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Imports\InventoryImport;

$imp = new InventoryImport(false, null, null, 'tokentest');
// construct a row similar to C8... using zero-based indexes
$row = [];
$row[1] = '=B7+1';
$row[2] = 12301;
$row[3] = 'Conj. de turbocompresor';
$row[4] = '0001';
$row[5] = 'PREVENTIVO';
$row[6] = 'TODAS LAS UND.';
$row[7] = 'TODAS LAS UND.';
$row[8] = ' ';
$row[9] = 'P-P';
$row[10] = 1;
$row[11] = 'C';
$row[12] = 45838;
$row[13] = 'EA';
$row[14] = 1; // inventory present here

$mapped = $imp->map($row);
print_r($mapped);
