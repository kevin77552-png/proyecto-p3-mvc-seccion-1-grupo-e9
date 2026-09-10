# Patrones creacionales para inventario

Este módulo concentra una configuración reutilizable para importación y exportación. No reemplaza el flujo actual: permite conectarlo gradualmente mediante callbacks.

## Patrones incluidos

- **Builder:** `InventoryImportOptionsBuilder` construye opciones legibles y valida `startRow` y `chunkSize`.
- **Prototype:** `InventoryImportOptions::copy()` crea una configuración independiente a partir de una plantilla.
- **Factory Method:** `InventoryImporterFactory` define `make()` y deja que cada fábrica concreta cree su importador.
- **Abstract Factory:** `InventoryTransferFactory` crea la familia relacionada de importador y exportador para un driver.
- **Singleton:** `InventoryTransferManager::instance()` mantiene un único punto de acceso a las familias disponibles.

## Uso

```php
$options = (new InventoryImportOptionsBuilder())
    ->startRow(8)
    ->sheet(2)
    ->chunkSize(500)
    ->build();

$manager = InventoryTransferManager::instance();
$factory = $manager->factory('fast-excel');

$importer = $factory->importer(
    fn (string $path, InventoryImportOptions $options) =>
        app(ExcelImportService::class)->importInventoryFromFastExcel($path)
);

$result = $importer->import($filePath, $options);
```

La implementación concreta del callback puede incorporar progresivamente `sheet`, `startRow` y `chunkSize` al servicio existente.