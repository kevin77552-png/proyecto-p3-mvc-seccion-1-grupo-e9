<?php

namespace App\Imports;

use App\Services\ExcelImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\ImportHistory;

class InventoryImport implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * Ruta absoluta del fichero subido.
     *
     * @var string
     */
    protected $filePath;

    /**
     * Token único para identificar esta importación en caché/progreso.
     *
     * @var string
     */
    protected $token;

    /**

     *
     * @var bool
     */
    protected $dryRun;

    public function __construct(string $filePath, bool $dryRun = false, string $token = null)
    {
        $this->filePath = $filePath;
        $this->dryRun = $dryRun;
        $this->token = $token ?: (string) Str::uuid();

        // init cache progress state
        Cache::put($this->cacheKey(), [
            'progress' => 0,
            'finished' => false,
        ]);
    }

    protected function cacheKey(): string
    {
        return "inventory_import_progress_{$this->token}";
    }

    /**
     * Execute the import job using FastExcel service.
     */
    public function handle(ExcelImportService $service)
    {
        $history = ImportHistory::where('token', $this->token)->first();
        if ($history) {
            $history->update(['status' => 'processing']);
        }

        $finalCount = 0;

        try {
            $service->importInventoryFromFastExcel($this->filePath, function ($count) use (&$finalCount, $history) {
                $finalCount = $count;
                Cache::put($this->cacheKey(), [
                    'progress' => $count,
                    'finished' => false,
                ]);

                if ($history) {
                    $history->update(['progress' => $count]);
                }
            });

            $stats = $service->getLastImportStats();

            if ($history) {
                $history->update([
                    'status' => 'completed',
                    'progress' => $finalCount,
                    'imported_rows' => $stats['inserted_rows'] ?? $finalCount,
                    'skipped_rows' => $stats['skipped_rows'] ?? 0,
                    'total_rows' => $stats['total_rows'],
                    'finished_at' => now(),
                ]);
            }

            Cache::put($this->cacheKey(), [
                'progress' => $finalCount,
                'finished' => true,
            ]);
        } catch (\Throwable $e) {
            $stats = $service->getLastImportStats();

            if ($history) {
                $history->update([
                    'status' => 'failed',
                    'progress' => $finalCount,
                    'imported_rows' => $stats['inserted_rows'] ?? $finalCount,
                    'skipped_rows' => $stats['skipped_rows'] ?? 0,
                    'total_rows' => $stats['total_rows'],
                    'error' => $e->getMessage(),
                    'finished_at' => now(),
                ]);
            }

            Cache::put($this->cacheKey(), [
                'progress' => $finalCount,
                'finished' => true,
            ]);

            throw $e;
        }
    }
}
