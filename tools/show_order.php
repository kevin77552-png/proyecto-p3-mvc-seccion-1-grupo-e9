<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$id = $argv[1] ?? 5;
$order = DB::table('purchase_orders')->where('id', $id)->first();

echo json_encode($order, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;