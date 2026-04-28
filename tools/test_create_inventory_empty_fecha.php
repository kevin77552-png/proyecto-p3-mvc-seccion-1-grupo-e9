<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $itemClass = App\Models\InventoryItem::class;

    $data = [
        'sigicov' => 'TEST' . rand(1000, 9999),
        'descripcion' => 'Prueba fecha vacía',
        'pasillo' => 'P2',
        'estante' => 'E2',
        'peldaño' => '2',
        'fecha' => '',
        'realizado_por' => 'script-test',
        'inventario' => '1',
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
