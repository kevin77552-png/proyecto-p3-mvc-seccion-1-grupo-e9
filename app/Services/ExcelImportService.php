<?php

namespace App\Services;

use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ExcelImportService
{
    /**
     * Import an inventory spreadsheet using FastExcel.
     *
     * The spreadsheet is known to have:
     *  - meaningful data beginning on row 8 (skip first 7 rows)
     *  - the worksheet we want is either named "JUNIO 2025 (2)" or at index 12
     *  - columns are zero‑based in the $row array provided by FastExcel
     *
     * We filter out any row where the "ID SISTEMA SIGICOV" (column 1) is not
     * numeric or is empty, and we trim text fields and cast stock to float.
     *
     * @param  string  $filePath  absolute path to the uploaded spreadsheet
     * @return void
     */
    /**
     * Import an inventory spreadsheet using FastExcel.
     *
     * Returns the collection of cleaned rows that were processed (useful for
     * testing or logging). A count is automatically written to the log.
     *
     * @param  string  $filePath
     * @return \Illuminate\Support\Collection
     */
    public function importInventoryFromFastExcel(string $filePath, ?callable $progress = null): Collection
    {
        // prepare FastExcel instance: disable headers and start on row 8
        $fx = new FastExcel();
        $fx = $fx->withoutHeaders()->startRow(8);

        // import first sheet by default
        $rows = $fx->import($filePath);

        // if first sheet came back empty, try a few subsequent sheets
        if ($rows->isEmpty()) {
            for ($i = 2; $i <= 5; $i++) {
                $trial = (clone $fx)->sheet($i)->import($filePath);
                if ($trial->isNotEmpty()) {
                    $rows = $trial;
                    break;
                }
            }
        }

        $count = $rows->count();
        Log::info("FastExcel import processed {$count} rows from {$filePath}");

        // persist the rows – iterate manually so podamos notificar progreso
        $processed = 0;
        foreach ($rows as $rawRow) {
            if (is_array($rawRow)) {
                $rawRow = array_pad($rawRow, 50, null);
            }

            if (empty($rawRow)) {
                continue;
            }

            // mapping similar to InventoryImport::map
            $itemNum = $rawRow[1] ?? null;
            $sigicov = $rawRow[2] ?? null;
            if ($sigicov === null || ! is_numeric($sigicov)) {
                continue;
            }
            $description = trim((string) ($rawRow[3] ?? ''));
            $unidad = trim((string) ($rawRow[4] ?? ''));
            $respAcc = trim((string) ($rawRow[7] ?? $rawRow[17] ?? $rawRow[18] ?? ''));

            // inventario detection
            $inventario = null;
            foreach ([8, 14, 37] as $idx) {
                if (isset($rawRow[$idx]) && is_numeric(str_replace([',', ' '], '', $rawRow[$idx]))) {
                    $inventario = $rawRow[$idx];
                    break;
                }
            }
            if ($inventario === '' || $inventario === null || (is_string($inventario) && strtolower($inventario) === 'nan')) {
                $inventario = 0;
            } elseif (is_numeric($inventario)) {
                $inventario = strpos((string) $inventario, '.') !== false ? (float) $inventario : (int) $inventario;
            } else {
                $inventario = 0;
            }

            \App\Models\InventoryItem::updateOrCreate(
                ['sigicov' => (int) $sigicov],
                [
                    'descripcion' => $description,
                    'resp_accesorios' => $respAcc !== '' ? substr((string) $respAcc, 0, 250) : null,
                    'inventario' => $inventario,
                ],
            );

            $processed++;
            if ($progress) {
                $progress($processed);
            }
        }

        return $rows;
    }
}
