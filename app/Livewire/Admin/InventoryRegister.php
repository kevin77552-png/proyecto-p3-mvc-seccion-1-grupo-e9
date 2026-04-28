<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;


class InventoryRegister extends Component
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

    public function mount()
    {
        $this->realizado_por = optional(Auth::user())->name ?: '';
    }

    public function hydrate()
    {
        $this->realizado_por = optional(Auth::user())->name ?: $this->realizado_por;
    }

    public function save()
    {
        $this->validate([
            'sigicov' => 'required|unique:inventory_items,sigicov',
            'descripcion' => 'required',
        ]);
        InventoryItem::create([
            'sigicov' => $this->sigicov,
            'descripcion' => $this->descripcion,
            'pasillo' => $this->pasillo,
            'estante' => $this->estante,
            'peldaño' => $this->peldaño,
            // Convert empty fecha to null to satisfy strict MySQL date handling
            'fecha' => $this->fecha ?: null,
            'realizado_por' => optional(Auth::user())->name ?: $this->realizado_por,
            'inventario' => $this->inventario,
            'resp_accesorios' => $this->resp_accesorios,
        ]);
        $this->reset([
            'sigicov','descripcion','pasillo','estante','peldaño','fecha','realizado_por','inventario','resp_accesorios'
        ]);
        // restore realizado_por to current user after reset
        $this->realizado_por = optional(Auth::user())->name ?: '';
    }

    public function render()
    {
        $items = InventoryItem::query()
            ->when($this->search, fn($q) => $q->where('sigicov', 'like', '%'.$this->search.'%'))
            ->when($this->search_pasillo, fn($q) => $q->where('pasillo', 'like', '%'.$this->search_pasillo.'%'))
            ->when($this->search_estante, fn($q) => $q->where('estante', 'like', '%'.$this->search_estante.'%'))
            ->orderBy('id','desc')
            ->get();
        return view('livewire.admin.inventory-register', compact('items'));
    }
}
