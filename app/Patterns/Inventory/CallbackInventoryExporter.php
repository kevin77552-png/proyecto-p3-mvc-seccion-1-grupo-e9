<?php

namespace App\Patterns\Inventory;

final class CallbackInventoryExporter implements InventoryExporter
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

    public function export(iterable $items, InventoryImportOptions $options): mixed
    {
        return ($this->handler)($items, $options);
    }
}