<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\InventoryItem;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;


class InventoryEgreso extends Component
{
    public $item_id = '';
    public $cantidad = '';
    public $motivo = '';
    public $success = '';
    public $error = '';

    // Nuevas propiedades para el modal
    public $showSalidaModal = false;
    public $vehiculo_placa = '';
    public $vehiculo_descripcion = '';

    public function mount()
    {
        // Simulación: deberías cargar los vehículos reales desde la base de datos
        $this->vehiculos = \App\Models\Vehiculo::orderBy('placa')->get();
    }

    public function openSalidaModal($itemId)
    {
        $this->item_id = $itemId;
        $this->cantidad = '';
        $this->vehiculo_placa = '';
        $this->vehiculo_descripcion = '';
        $this->showSalidaModal = true;
    }

    public function closeSalidaModal()
    {
        $this->showSalidaModal = false;
        $this->item_id = '';
        $this->cantidad = '';
        $this->vehiculo_placa = '';
        $this->vehiculo_descripcion = '';
    }

    public function confirmSalida()
    {
        $this->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'cantidad' => 'required|integer|min:1',
            'vehiculo_placa' => 'required|string|max:20',
            'vehiculo_descripcion' => 'required|string|max:100',
        ]);
        $item = InventoryItem::find($this->item_id);
        if ($item && $item->inventario >= $this->cantidad) {
            $item->inventario -= $this->cantidad;
            $item->save();
            // Registrar log de salida
            Log::create([
                'accion' => 'salida',
                'entidad' => 'inventory_item',
                'entidad_id' => $item->id,
                'descripcion' => 'Salida de ' . $this->cantidad . ' unidades para vehículo: Placa ' . $this->vehiculo_placa . ', Descripción: ' . $this->vehiculo_descripcion,
                'user_id' => Auth::id() ?? 0,
            ]);
            $this->success = 'Salida realizada correctamente.';
            $this->error = '';
            $this->closeSalidaModal();
        } else {
            $this->error = 'Cantidad insuficiente en inventario.';
            $this->success = '';
        }
    }

    public function render()
    {
        // Mostrar por ID descendente (últimos inserts primero)
        $items = InventoryItem::orderBy('id','desc')->get();
        return view('livewire.admin.inventory-egreso', [
            'items' => $items,
            'showSalidaModal' => $this->showSalidaModal,
        ]);
    }
}
