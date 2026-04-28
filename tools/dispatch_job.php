<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Imports\InventoryImport;
use Illuminate\Support\Str;

$token = (string) Str::uuid();
$path = $argv[1] ?? 'C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
InventoryImport::dispatch($path, false, $token);
echo "dispatched token={$token}\n";
