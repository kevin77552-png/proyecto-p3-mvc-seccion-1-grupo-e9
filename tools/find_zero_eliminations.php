<?php

// Script para listar spare_requests tipo 'eliminacion' con cantidad == 0
// Genera CSV en storage/app/spare_requests_zero_eliminations.csv

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SpareRequest;
use App\Models\InventoryItem;

$rows = [];
$headers = [
    'id','tipo','cantidad','inventory_item_id','inventory_item_exists','inventory_inventario','almacenista_id','almacenista_name','status','reject_reason','created_at','updated_at'
];

$requests = SpareRequest::where('tipo','eliminacion')
    ->where(function($q){
        $q->where('cantidad', 0)->orWhere('cantidad', '0');
    })
    ->orderBy('id')
    ->get();

$total = $requests->count();

foreach ($requests as $r) {
    $exists = 'NO';
    $inventario = '';
    if (!is_null($r->inventory_item_id)) {
        $item = InventoryItem::withTrashed()->find($r->inventory_item_id);
        if ($item) {
            $exists = 'YES';
            $inventario = (string) $item->inventario;
        }
    }

    $rows[] = [
        $r->id,
        $r->tipo,
        $r->cantidad,
        $r->inventory_item_id,
        $exists,
        $inventario,
        $r->almacenista_id,
        optional($r->almacenista)->name,
        $r->status,
        $r->reject_reason,
        $r->created_at,
        $r->updated_at,
    ];
}

$storagePath = storage_path('app/spare_requests_zero_eliminations.csv');

$fp = fopen($storagePath, 'w');
if (!$fp) {
    echo "No se pudo crear el archivo CSV en: {$storagePath}\n";
    exit(1);
}

fputcsv($fp, $headers);
foreach ($rows as $row) {
    $row = array_map(function ($v) { return is_null($v) ? '' : (string) $v; }, $row);
    fputcsv($fp, $row);
}
fclose($fp);

echo "Revisión completada.\n";
echo "Total solicitudes tipo 'eliminacion' con cantidad == 0: {$total}\n";
echo "CSV generado en: {$storagePath}\n";

exit(0);
