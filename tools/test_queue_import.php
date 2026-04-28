<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Imports\InventoryImport;
use App\Services\ExcelImportService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

// bootstrap laravel
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// allow passing path as first argument
$file = $argv[1] ?? 'storage/app/imports/test_import.xlsx';
if (!file_exists($file)) {
    echo "Necesitas un archivo de prueba en $file\n";
    exit(1);
}

$token = (string) Str::uuid();

// dispatch the job synchronously for testing
$job = new InventoryImport($file, false, $token);
// run handle manually
$service = app(ExcelImportService::class);
$job->handle($service);

echo "Cache progress: " . Cache::get("inventory_import_progress_$token") . "\n";
