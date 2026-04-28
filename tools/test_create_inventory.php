<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap the application like artisan does
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Use the model
    $itemClass = App\Models\InventoryItem::class;

    $data = [
        'sigicov' => 'TEST' . rand(1000, 9999),
        'descripcion' => 'Registro de prueba desde script',
        'pasillo' => 'P1',
        'estante' => 'E1',
        'peldaño' => '1',
        'fecha' => date('Y-m-d'),
        'realizado_por' => 'script-test',
        'inventario' => '5',
        'resp_accesorios' => null,
    ];

    $item = $itemClass::create($data);

    echo "CREATED OK: ID={$item->id}\n";
    print_r($item->toArray());
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}

return 0;
