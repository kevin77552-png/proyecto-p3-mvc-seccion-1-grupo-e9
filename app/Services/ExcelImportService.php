<?php

namespace App\Services;

use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExcelImportService
{
    private const GAP_ROWS = 50;
    private const INVENTORY_CANDIDATES = [8, 14, 37];
    private const DEFAULT_IMPORT_DATE = '2025-01-01';
    // Expand trials to cover common sheet positions in legacy workbooks
    private const SHEET_TRIALS = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13];

    private int $lastRawRows = 0;
    private int $lastProcessedRows = 0;
    private int $lastInsertedRows = 0;
    private int $lastSkippedRows = 0;

    /**
     * Import an inventory spreadsheet using FastExcel.
     *
     * The spreadsheet has meaningful data beginning on row 8 (skip first 7 rows).
     * We search the first sheet and then a few following sheets if the first is empty.
     * Rows are normalized, validated, and persisted in bulk using batched upserts.
     * Each batch is written inside a DB transaction for consistency.
     *
     * @param  string  $filePath
     * @param  callable|null  $progress
     * @return \Illuminate\Support\Collection
     */
    public function importInventoryFromFastExcel(string $filePath, ?callable $progress = null): Collection
    {
        $fx = (new FastExcel())->withoutHeaders()->startRow(8);
        $rows = $this->loadFirstNonEmptySheet($fx, $filePath);

        $this->lastRawRows = $rows->count();
        $this->lastProcessedRows = 0;
        $this->lastInsertedRows = 0;
        $this->lastSkippedRows = 0;
        Log::info("FastExcel import processed {$this->lastRawRows} rows from {$filePath}");

        $processed = 0;
        $batch = [];
        $batchSize = 500;

        foreach ($rows as $rawRow) {
            $mapped = $this->mapRow($rawRow);
            if ($mapped === null) {
                continue;
            }

            $batch[] = array_merge($mapped, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (count($batch) >= $batchSize) {
                $this->flushBatch($batch);
            }

            $processed++;
            if ($progress) {
                $progress($processed);
            }
        }

        if (! empty($batch)) {
            $this->flushBatch($batch);
        }

        $this->lastProcessedRows = $processed;

        return $rows;
    }

    public function getLastImportStats(): array
    {
        return [
            'total_rows' => $this->lastRawRows,
            'processed_rows' => $this->lastProcessedRows,
            'inserted_rows' => $this->lastInsertedRows,
            'skipped_rows' => $this->lastSkippedRows,
        ];
    }

    private function loadFirstNonEmptySheet(FastExcel $fx, string $filePath): Collection
    {
        $best = $fx->import($filePath);
        $bestCount = $best->count();
        $bestValid = $this->countValidRows($best);
        $bestSheet = 1;

        foreach (self::SHEET_TRIALS as $sheetIndex) {
            $trial = (clone $fx)->sheet($sheetIndex)->import($filePath);
            $trialCount = $trial->count();
            $trialValid = $this->countValidRows($trial);

            if ($trialValid > $bestValid || ($trialValid === $bestValid && $trialCount > $bestCount)) {
                $best = $trial;
                $bestCount = $trialCount;
                $bestValid = $trialValid;
                $bestSheet = $sheetIndex;
            }
        }

        Log::info("FastExcel selected sheet {$bestSheet} with {$bestCount} rows ({$bestValid} valid) from {$filePath}");

        return $best;
    }

    private function countValidRows(Collection $rows): int
    {
        $valid = 0;

        foreach ($rows as $rawRow) {
            if ($this->mapRow($rawRow) !== null) {
                $valid++;
            }
        }

        return $valid;
    }

    private function mapRow($rawRow): ?array
    {
        if (is_array($rawRow)) {
            $rawRow = array_pad($rawRow, self::GAP_ROWS, null);
        }

        if (empty($rawRow) || ! is_array($rawRow)) {
            return null;
        }

        $sigicov = $this->normalizeNumber($rawRow[2] ?? null);
        if ($sigicov === null) {
            return null;
        }

        $description = $this->normalizeString($rawRow[3] ?? null);
        $unidad = $this->normalizeString($rawRow[4] ?? null);

        // Location fields: prefer the layout observed in current workbooks
        // Common layout: pasillo=9, estante=10, peldaño=11, fecha=12
        // Keep older fallbacks in case different templates are used.
        $pasillo = $this->normalizeString($rawRow[9] ?? $rawRow[5] ?? $rawRow[15] ?? null) ?? 'No Asignado';
        $estante = $this->normalizeString($rawRow[10] ?? $rawRow[6] ?? $rawRow[16] ?? null) ?? 'No Asignado';
        $peldano = $this->normalizeString($rawRow[11] ?? $rawRow[7] ?? $rawRow[17] ?? null) ?? 'No Asignado';

        // Fecha y quien realizó el registro (el valor tal cual de EA debe llegar a la BD)
        $fecha = $this->normalizeDate($rawRow[12] ?? $rawRow[9] ?? $rawRow[19] ?? $rawRow[20] ?? null);
        $realizadoPor = $this->normalizeString($rawRow[18] ?? $rawRow[17] ?? $rawRow[11] ?? $rawRow[10] ?? null) ?? 'No Asignado';

        $respAcc = $this->normalizeString($rawRow[7] ?? $rawRow[17] ?? $rawRow[18] ?? null);
        $inventario = $this->detectInventario($rawRow);

        return [
            'item_number' => $this->normalizeString($rawRow[1] ?? null),
            'sigicov' => (int) $sigicov,
            'descripcion' => $description,
            'unidad' => $unidad,
            'pasillo' => $pasillo,
            'estante' => $estante,
            'peldaño' => $peldano,
            'fecha' => $fecha,
            'realizado_por' => $realizadoPor,
            'inventario' => $inventario,
            'resp_accesorios' => $respAcc !== '' ? mb_substr($respAcc, 0, 250, 'UTF-8') : null,
        ];
    }

    private function normalizeString($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function normalizeDate($value): string
    {
        if ($value === null || $value === '') {
            return self::DEFAULT_IMPORT_DATE;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $value = trim((string) $value);
        if ($value === '') {
            return self::DEFAULT_IMPORT_DATE;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return self::DEFAULT_IMPORT_DATE;
        }
    }

    private function normalizeNumber($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = str_replace([',', ' '], ['', ''], (string) $value);
        if (! is_numeric($normalized)) {
            return null;
        }

        return strpos($normalized, '.') !== false ? (float) $normalized : (int) $normalized;
    }

    private function detectInventario(array $rawRow): int
    {
        foreach (self::INVENTORY_CANDIDATES as $idx) {
            if (isset($rawRow[$idx])) {
                $value = $this->normalizeNumber($rawRow[$idx]);
                if ($value !== null) {
                    return $value;
                }
            }
        }

        return 0;
    }

    private function flushBatch(array &$batch): void
    {
        if (empty($batch)) {
            return;
        }

        // Use insertOrIgnore so existing sigicov records are skipped and not updated.
        $sigs = array_values(array_filter(array_map(function ($r) {
            return $r['sigicov'] ?? null;
        }, $batch)));

        try {
            $before = 0;
            if (! empty($sigs)) {
                $before = DB::table('inventory_items')->whereIn('sigicov', $sigs)->count();
            }

            Log::info("Inserting batch of " . count($batch) . " rows, existing before: {$before}");

            DB::table('inventory_items')->insertOrIgnore($batch);

            $after = 0;
            if (! empty($sigs)) {
                $after = DB::table('inventory_items')->whereIn('sigicov', $sigs)->count();
            }

            $inserted = max(0, $after - $before);
            $skipped = count($batch) - $inserted;
            $this->lastInsertedRows += $inserted;
            $this->lastSkippedRows += $skipped;

            Log::info("InsertOrIgnore completed, existing after: {$after}, inserted: {$inserted}, skipped: {$skipped}");
        } catch (\Exception $e) {
            Log::error('InsertOrIgnore batch failed: ' . $e->getMessage());
            throw $e;
        }

        $batch = [];
    }
}
