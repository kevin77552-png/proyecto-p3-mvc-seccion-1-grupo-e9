<?php

namespace App\Patterns\Inventory;

final class FastExcelTransferFactory implements InventoryTransferFactory
{
    public function importer(\Closure $handler): InventoryImporter
    {
        return (new FastExcelImporterFactory())->make($handler);
    }

    public function exporter(\Closure $handler): InventoryExporter
    {
        return new CallbackInventoryExporter('fast-excel', $handler);
    }
}