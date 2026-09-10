<?php

namespace App\Patterns\Inventory;

interface InventoryImporter
{
    public function driver(): string;

    public function import(string $filePath, InventoryImportOptions $options): mixed;
}