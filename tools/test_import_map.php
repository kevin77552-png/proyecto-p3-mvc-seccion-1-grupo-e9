<?php
require __DIR__.'/../vendor/autoload.php';
use App\Imports\InventoryImport;

$import = new InventoryImport(true);

// construct a row similar to the one we encountered
$row = [
    null,             // A
    1,                // B item number
    12301,            // C sigicov
    'Conj. de turbocompresor', // D descripcion
    '0001',           // E unidad
    'PREVENTIVO',
    'TODAS LAS UND.',
    'TODAS LAS UND.',
    null,
    'P-P',
    1,
    'C',
    new DateTimeImmutable('2025-06-30'),
    'EA',
    1,
    null,
    null,
    null,
    '1118-00967',     // index18 code
    // we can leave blanks until index37
];
// pad to 40 elements
$row = array_pad($row, 40, null);

$result = $import->map($row);
print_r($result);
