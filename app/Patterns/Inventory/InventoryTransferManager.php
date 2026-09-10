<?php

namespace App\Patterns\Inventory;

final class InventoryTransferManager
{
    private static ?self $instance = null;

    private function __construct()
    {
    }

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    public function factory(string $driver = 'fast-excel'): InventoryTransferFactory
    {
        return match ($driver) {
            'fast-excel' => new FastExcelTransferFactory(),
            default => throw new \InvalidArgumentException("Driver no soportado: {$driver}"),
        };
    }
}