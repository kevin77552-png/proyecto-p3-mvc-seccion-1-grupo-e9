<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Cache;

$token = $argv[1] ?? 'testtoken';
echo "cache_progress=" . Cache::get("inventory_import_progress_{$token}") . "\n";
