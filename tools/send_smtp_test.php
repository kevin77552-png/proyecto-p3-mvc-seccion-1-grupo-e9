<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Asegurarnos que las Facades pueden usarse
Illuminate\Support\Facades\Facade::setFacadeApplication($app);

use Illuminate\Support\Facades\Mail;

$to = 'kevinjohan2306@gmail.com';
try {
    Mail::raw('Prueba SMTP desde InventarioSITSSA (script)', function ($m) use ($to) {
        $m->to($to)->subject('Prueba SMTP - InventarioSITSSA');
    });
    echo "MAIL_OK\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "TYPE: " . get_class($e) . "\n";
    echo $e->getTraceAsString() . "\n";
}


