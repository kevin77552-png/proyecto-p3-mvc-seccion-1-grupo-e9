<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\InventoryItem;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InventoryIngreso extends Component
{
    public $sigicov = '';
    public $descripcion = '';
    public $pasillo = '';
    public $estante = '';
    public $peldaño = '';
    public $fecha = '';
    public $realizado_por = '';
    public $inventario = '';
    public $resp_accesorios = '';

    public $search = '';
    public $search_pasillo = '';
    public $search_estante = '';
    public $showSaved = false;
    public $savedMessage = '';

    public function mount()
    {
        $this->realizado_por = isset(Auth::user()->name) ? mb_strtoupper(Auth::user()->name, 'UTF-8') : '';
        $this->fecha = Carbon::now()->toDateString();
    }

    public function save()
    {
        $this->validate([
            'sigicov' => 'required|unique:inventory_items,sigicov',
            'descripcion' => 'required',
            'pasillo' => 'required',
            'estante' => 'required',
            'peldaño' => 'required',
            'inventario' => 'required|integer|min:1',
            'resp_accesorios' => 'required',
        ],
        [
            'required' => 'El campo :attribute es obligatorio.',
            'sigicov.unique' => 'El SIGICOV ya está registrado.',
        ],
        [
            'sigicov' => 'SIGICOV',
            'descripcion' => 'descripción',
            'pasillo' => 'pasillo',
            'estante' => 'estante',
            'peldaño' => 'peldaño',
            'fecha' => 'fecha',
            'realizado_por' => 'realizado por',
            'inventario' => 'inventario',
            'resp_accesorios' => 'responsable de accesorios',
        ]);
        // Force fecha on the server to the current date for security (ignore any client-side value)
        $nowDate = Carbon::now()->toDateString();

        $item = InventoryItem::create([
            'sigicov' => $this->sigicov,
            'descripcion' => $this->descripcion,
            'pasillo' => $this->pasillo,
            'estante' => $this->estante,
            'peldaño' => $this->peldaño,
            // MySQL strict mode rejects empty string for DATE columns — convert empty strings to null
            'fecha' => $nowDate,
            'realizado_por' => Auth::user()->name ?? $this->realizado_por,
            'inventario' => $this->inventario,
            'resp_accesorios' => $this->resp_accesorios,
        ]);
        Log::create([
            'accion' => 'ingreso',
            'entidad' => 'inventory_item',
            'entidad_id' => $item->id,
            'descripcion' => 'Ingreso de repuesto: ' . $item->sigicov . ' - ' . $item->descripcion,
            'user_id' => Auth::id() ?? 0,
        ]);
        $this->reset([
            'sigicov','descripcion','pasillo','estante','peldaño','fecha','realizado_por','inventario','resp_accesorios'
        ]);
        // After reset, re-fill realizado_por and fecha with current values so the readonly fields show defaults
        $this->realizado_por = isset(Auth::user()->name) ? mb_strtoupper(Auth::user()->name, 'UTF-8') : '';
        $this->fecha = Carbon::now()->toDateString();
        // Use Livewire-bound properties to signal the frontend (Alpine) to show a transient notification
        $this->savedMessage = 'Repuesto guardado correctamente.';
        $this->showSaved = true;
    }

    public function render()
    {
        // Mostrar solo los 4 registros más recientes en el formulario de ingreso
        $items = InventoryItem::query()
            ->when($this->search, fn($q) => $q->where('sigicov', 'like', '%'.$this->search.'%'))
            ->when($this->search_pasillo, fn($q) => $q->where('pasillo', 'like', '%'.$this->search_pasillo.'%'))
            ->when($this->search_estante, fn($q) => $q->where('estante', 'like', '%'.$this->search_estante.'%'))
            ->orderBy('id','desc')
            ->limit(4)
            ->get();
        return view('livewire.admin.inventory-ingreso', compact('items'));
    }
}
