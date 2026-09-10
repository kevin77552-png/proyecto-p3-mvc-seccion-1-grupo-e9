<?php

namespace App\Patterns\Inventory;

final class InventoryImportOptionsBuilder
{
    private string $format = 'xlsx';
    private int $startRow = 8;
    private ?int $sheet = null;
    private int $chunkSize = 500;
    private bool $withHeaders = false;

    public function format(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function startRow(int $startRow): self
    {
        $this->startRow = $startRow;
        return $this;
    }

    public function sheet(?int $sheet): self
    {
        $this->sheet = $sheet;
        return $this;
    }

    public function chunkSize(int $chunkSize): self
    {
        $this->chunkSize = $chunkSize;
        return $this;
    }

    public function withHeaders(bool $withHeaders = true): self
    {
        $this->withHeaders = $withHeaders;
        return $this;
    }

    public function build(): InventoryImportOptions
    {
        if ($this->startRow < 1 || $this->chunkSize < 1) {
            throw new \InvalidArgumentException('startRow y chunkSize deben ser mayores que cero.');
        }

        return new InventoryImportOptions(
            $this->format,
            $this->startRow,
            $this->sheet,
            $this->chunkSize,
            $this->withHeaders,
        );
    }
}