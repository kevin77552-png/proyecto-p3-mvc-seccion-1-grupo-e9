<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$updated = DB::table('purchase_orders')->where('status','rechazado')->update(['approved_at' => null, 'updated_at' => date('Y-m-d H:i:s')]);
echo "Updated rows: $updated\n";
