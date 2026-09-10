<?php

namespace App\Patterns\Inventory;

final class FastExcelImporterFactory extends InventoryImporterFactory
{
    protected function createImporter(\Closure $handler): InventoryImporter
    {
        return new CallbackInventoryImporter('fast-excel', $handler);
    }
}