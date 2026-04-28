<?php
require __DIR__ . '/..\vendor\autoload.php';
use Illuminate\Support\Facades\DB;

// bootstrap application for facades
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rows = DB::table('failed_jobs')->get();
foreach ($rows as $r) {
    echo "ID: {$r->id}\n";
    echo "Connection: {$r->connection}\n";
    echo "Queue: {$r->queue}\n";
    echo "Exception: {$r->exception}\n";
    echo "Payload: {$r->payload}\n";
    echo "-------------------------\n";
}
