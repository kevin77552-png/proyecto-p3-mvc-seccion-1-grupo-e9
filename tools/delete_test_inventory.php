<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    $items = InventoryItem::where('sigicov', 'like', 'TEST%')
        ->orWhere('realizado_por', 'script-test')
        ->get();

    $ids = $items->pluck('id')->all();

    if (empty($ids)) {
        echo "No se encontraron registros de prueba.\n";
        DB::rollBack();
        exit(0);
    }

    foreach ($items as $item) {
        echo "Deleting ID={$item->id} sigicov={$item->sigicov} descripcion={$item->descripcion}\n";
        $item->delete();
    }

    DB::commit();
    echo "Borrados: " . count($ids) . " registros.\n";
} catch (Throwable $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}

return 0;
