<?php

namespace App\Patterns\Inventory;

interface InventoryExporter
{
    public function driver(): string;

    public function export(iterable $items, InventoryImportOptions $options): mixed;
}