<?php

namespace Tests\Unit;

use App\Patterns\Inventory\FastExcelImporterFactory;
use App\Patterns\Inventory\InventoryImportOptions;
use App\Patterns\Inventory\InventoryImportOptionsBuilder;
use App\Patterns\Inventory\InventoryTransferManager;
use PHPUnit\Framework\TestCase;

class InventoryPatternsTest extends TestCase
{
    public function test_builder_creates_valid_import_options(): void
    {
        $options = (new InventoryImportOptionsBuilder())
            ->format('xlsx')->startRow(8)->sheet(2)->chunkSize(1000)->withHeaders()->build();

        $this->assertSame(8, $options->startRow);
        $this->assertSame(2, $options->sheet);
        $this->assertTrue($options->withHeaders);
    }

    public function test_prototype_copies_options_without_mutating_template(): void
    {
        $template = (new InventoryImportOptionsBuilder())->startRow(8)->build();
        $copy = $template->copy(['startRow' => 12]);

        $this->assertSame(8, $template->startRow);
        $this->assertSame(12, $copy->startRow);
    }

    public function test_factory_method_creates_fast_excel_importer(): void
    {
        $importer = (new FastExcelImporterFactory())->make(
            static fn (string $path, InventoryImportOptions $options): string => $path
        );

        $this->assertSame('fast-excel', $importer->driver());
        $this->assertSame('inventario.xlsx', $importer->import('inventario.xlsx', new InventoryImportOptions()));
    }

    public function test_abstract_factory_creates_related_importer_and_exporter(): void
    {
        $factory = InventoryTransferManager::instance()->factory();

        $this->assertSame('fast-excel', $factory->importer(static fn (): null => null)->driver());
        $this->assertSame('fast-excel', $factory->exporter(static fn (): null => null)->driver());
    }

    public function test_manager_is_a_singleton(): void
    {
        $this->assertSame(InventoryTransferManager::instance(), InventoryTransferManager::instance());
    }
}