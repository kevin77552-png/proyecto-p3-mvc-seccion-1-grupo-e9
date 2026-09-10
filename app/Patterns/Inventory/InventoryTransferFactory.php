<?php

namespace App\Patterns\Inventory;

interface InventoryTransferFactory
{
    public function importer(\Closure $handler): InventoryImporter;

    public function exporter(\Closure $handler): InventoryExporter;
}