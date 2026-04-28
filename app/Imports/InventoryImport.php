<?php

namespace App\Imports;

use App\Services\ExcelImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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

        // init cache count
        Cache::put($this->cacheKey(), 0);
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
        $service->importInventoryFromFastExcel($this->filePath, function ($count) {
            Cache::put($this->cacheKey(), $count);
        });
    }
}
