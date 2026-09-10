<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImportHistory;
use App\Services\ExcelImportService;
use Illuminate\Support\Facades\DB;
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
        $historyList = ImportHistory::where('user_id', auth()->id())
            ->latest()
            ->limit(10)
            ->get();

        return view('inventory.import', compact('historyList'));
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
            // generate a token for tracking
            $token = (string) \Illuminate\Support\Str::uuid();

            $history = ImportHistory::create([
                'user_id' => auth()->id(),
                'token' => $token,
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_path' => $path,
                'status' => 'pending',
                'progress' => 0,
                'total_rows' => 0,
                'imported_rows' => 0,
                'started_at' => now(),
            ]);

            // Queue the import job so a queue worker can process it asynchronously.
            // Ensure your `queue` driver is configured and a worker is running.
            InventoryImport::dispatch($fullPath, false, $token);
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
            $redirect = route('inventory.import.create') . '#import-section';
            return redirect($redirect)->withErrors(['file' => 'Error al procesar el archivo: ' . $e->getMessage()]);
        }

        $redirect = route('inventory.import.create') . '#import-section';
        return redirect($redirect)
            ->with('message', 'Import encolado correctamente. Un worker procesará el archivo en segundo plano.')
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
        $history = ImportHistory::where('token', $token)->first();

        if ($history) {
            return response()->json([
                'progress' => $history->progress,
                'finished' => $history->status === 'completed',
                'total_rows' => $history->total_rows,
                'imported_rows' => $history->imported_rows,
                'skipped_rows' => $history->skipped_rows,
                'status' => $history->status,
                'error' => $history->error,
            ]);
        }

        $key = "inventory_import_progress_{$token}";
        $state = \Illuminate\Support\Facades\Cache::get($key, [
            'progress' => 0,
            'finished' => false,
        ]);

        return response()->json([
            'progress' => $state['progress'] ?? 0,
            'finished' => $state['finished'] ?? false,
        ]);
    }

    /**
     * Cancelar una importación encolada antes de que se ejecute.
     *
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(string $token)
    {
        $history = ImportHistory::where('token', $token)->first();

        if ($history && in_array($history->status, ['completed', 'failed', 'cancelled'])) {
            return redirect()->route('inventory.import.create')
                ->with('message', 'No se puede cancelar una importación que ya fue completada o fallida.');
        }

        $deleted = DB::table('jobs')
            ->where('payload', 'like', '%' . $token . '%')
            ->delete();

        if ($history && $deleted) {
            $history->update([
                'status' => 'cancelled',
                'finished_at' => now(),
            ]);
        }

        $message = $deleted
            ? 'Importación cancelada correctamente.'
            : 'No se encontró un job pendiente para cancelar. Puede que ya se esté procesando o ya haya finalizado.';

        return redirect()->route('inventory.import.create')
            ->with('message', $message);
    }
}
