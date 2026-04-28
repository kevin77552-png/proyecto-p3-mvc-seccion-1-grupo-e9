<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Redirigir la raíz al login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');



require __DIR__.'/auth.php';

use App\Http\Controllers\InventoryImportController;

// rutas para el módulo de importación de inventario
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('inventory/import', [InventoryImportController::class, 'create'])
        ->name('inventory.import.create');
    Route::post('inventory/import', [InventoryImportController::class, 'store'])
        ->name('inventory.import.store');

    // progreso de importación para UI polling
    Route::get('inventory/import/progress/{token}', [InventoryImportController::class, 'progress'])
        ->name('inventory.import.progress');
});
