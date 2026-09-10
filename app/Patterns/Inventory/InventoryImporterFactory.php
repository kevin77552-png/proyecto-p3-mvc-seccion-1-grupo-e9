<?php

namespace App\Patterns\Inventory;

abstract class InventoryImporterFactory
{
    final public function make(\Closure $handler): InventoryImporter
    {
        return $this->createImporter($handler);
    }

    abstract protected function createImporter(\Closure $handler): InventoryImporter;
}