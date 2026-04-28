<?php

// Script para revisar spare_requests tipo 'eliminacion' y generar un CSV con problemas detectados.

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SpareRequest;
use App\Models\InventoryItem;

$rows = [];
$headers = [
    'id','tipo','cantidad','inventory_item_id','inventory_item_exists','almacenista_id','almacenista_name','status','reject_reason','created_at','updated_at'
];

$requests = SpareRequest::where('tipo','eliminacion')->orderBy('id')->get();

$total = $requests->count();
$nullCantidad = 0;
$nullItemId = 0;
$brokenRef = 0;

foreach ($requests as $r) {
    $exists = null;
    if (is_null($r->inventory_item_id)) {
        $exists = 'NULL';
        $nullItemId++;
    } else {
        $item = InventoryItem::withTrashed()->find($r->inventory_item_id);
        $exists = $item ? 'YES' : 'NO';
        if (!$item) $brokenRef++;
    }

    if (is_null($r->cantidad)) $nullCantidad++;

    $rows[] = [
        $r->id,
        $r->tipo,
        $r->cantidad,
        $r->inventory_item_id,
        $exists,
        $r->almacenista_id,
        optional($r->almacenista)->name,
        $r->status,
        $r->reject_reason,
        $r->created_at,
        $r->updated_at,
    ];
}

$storagePath = storage_path('app/spare_requests_review.csv');

$fp = fopen($storagePath, 'w');
if (!$fp) {
    echo "No se pudo crear el archivo CSV en: {$storagePath}\n";
    exit(1);
}

fputcsv($fp, $headers);
foreach ($rows as $row) {
    // Normalizar valores a strings
    $row = array_map(function ($v) { return is_null($v) ? '' : (string) $v; }, $row);
    fputcsv($fp, $row);
}
fclose($fp);

echo "Revisión completada.\n";
echo "Total solicitudes tipo 'eliminacion': {$total}\n";
echo "- Con cantidad NULL: {$nullCantidad}\n";
echo "- Con inventory_item_id NULL: {$nullItemId}\n";
echo "- Con referencias rotas a inventory_items: {$brokenRef}\n";
echo "CSV generado en: {$storagePath}\n";

exit(0);
