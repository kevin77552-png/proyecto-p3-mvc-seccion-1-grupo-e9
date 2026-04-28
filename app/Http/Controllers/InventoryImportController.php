<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExcelImportService;
use Illuminate\Support\Facades\Log;
use App\Imports\InventoryImport;
use Illuminate\Support\Facades\Storage;

class InventoryImportController extends Controller
{
    protected ExcelImportService $importService;

    public function __construct(ExcelImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Show the upload form (you could reuse the existing migration view or
     * create a new blade for this controller).
     */
    public function create()
    {
        return view('inventory.import');
    }

    /**
     * Handle the uploaded file and pass it to the service for processing.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,ods',
        ]);
        // ensure a valid uploaded file
        if (! $request->hasFile('file') || ! $request->file('file')->isValid()) {
            $redirect = route('inventory.import.create') . '#import-section';
            return redirect($redirect)->withErrors(['file' => 'No se recibió un archivo válido.']);
        }

        // store the uploaded file temporarily (use filesystem disk path)
        $path = $request->file('file')->store('imports');
        // get the absolute path according to the disk configuration
        $fullPath = Storage::disk(config('filesystems.default'))->path($path);

        if (! file_exists($fullPath)) {
            Log::error('Uploaded file missing after store: ' . $fullPath);
            $redirect = route('inventory.import.create') . '#import-section';
            return redirect($redirect)->withErrors(['file' => 'El archivo no pudo ser guardado en el servidor. Verifica `upload_max_filesize` y `post_max_size`.']);
        }

        try {
            // generate a token so we can track progress later
            $token = (string) \Illuminate\Support\Str::uuid();

            // dispatch our custom import job (uses FastExcel)
            InventoryImport::dispatch($fullPath, false, $token);
        } catch (\Exception $e) {
            Log::error('Import queue failed: ' . $e->getMessage());
            $redirect = route('inventory.import.create') . '#import-section';
            return redirect($redirect)->withErrors(['file' => 'Error al encolar el archivo para importación: ' . $e->getMessage()]);
        }

        $redirect = route('inventory.import.create') . '#import-section';
        return redirect($redirect)
            ->with('message', 'Import encolado: el procesamiento se realizará en segundo plano.')
            ->with('import_token', $token);
    }

    /**
     * Devuelve el progreso actual de una importación encolada.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function progress(string $token)
    {
        $key = "inventory_import_progress_{$token}";
        $progress = \Illuminate\Support\Facades\Cache::get($key, 0);
        return response()->json(['progress' => $progress]);
    }
}
