<?php

namespace App\Patterns\Inventory;

final class InventoryImportOptions
{
    public function __construct(
        public readonly string $format = 'xlsx',
        public readonly int $startRow = 8,
        public readonly ?int $sheet = null,
        public readonly int $chunkSize = 500,
        public readonly bool $withHeaders = false,
    ) {
    }

    public function copy(array $changes = []): self
    {
        return new self(
            $changes['format'] ?? $this->format,
            $changes['startRow'] ?? $this->startRow,
            $changes['sheet'] ?? $this->sheet,
            $changes['chunkSize'] ?? $this->chunkSize,
            $changes['withHeaders'] ?? $this->withHeaders,
        );
    }
}