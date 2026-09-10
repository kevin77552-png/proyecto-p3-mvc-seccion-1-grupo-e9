<?php

namespace App\Patterns\Inventory;

final class CallbackInventoryImporter implements InventoryImporter
{
    public function __construct(
        private readonly string $name,
        private readonly \Closure $handler,
    ) {
    }

    public function driver(): string
    {
        return $this->name;
    }

    public function import(string $filePath, InventoryImportOptions $options): mixed
    {
        return ($this->handler)($filePath, $options);
    }
}