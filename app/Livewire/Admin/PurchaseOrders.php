<?php

namespace App\Livewire\Admin;

use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Models\Log;

class PurchaseOrders extends Component
{
    public $cantidad = 1;
    public $descripcion = '';
    public $pedido_por = '';
    public $detalles = '';

    public $orders;

    protected $rules = [
        'cantidad' => 'required|integer|min:1',
        'descripcion' => 'required|string|max:1000',
        'pedido_por' => 'nullable|string|max:255',
        'detalles' => 'nullable|string|max:2000',
    ];

    public function mount()
    {
        $this->loadOrders();
        if (Auth::check()) {
            $this->pedido_por = Auth::user()->name;
        }
    }

    public function loadOrders()
    {
        // Mostrar solo las 2 órdenes más recientes en la vista de creación
        $this->orders = PurchaseOrder::orderBy('created_at', 'desc')->take(2)->get();
    }

    public function createOrder()
    {
        $validated = $this->validate();

        $order = PurchaseOrder::create([
            'cantidad' => $validated['cantidad'],
            'descripcion' => $validated['descripcion'],
            // Forzar el nombre del usuario autenticado como 'pedido_por'
            'pedido_por' => Auth::check() ? Auth::user()->name : null,
            'detalles' => $validated['detalles'] ?? null,
            'status' => 'pendiente',
        ]);

    $this->reset(['cantidad', 'descripcion', 'detalles']);
        $this->loadOrders();

    // Emitir evento para el frontend usando el helper `dispatch` disponible en este proyecto
    $this->dispatch('order-created');

        // Log creación de orden
        Log::create([
            'accion' => 'crear_orden',
            'entidad' => 'purchase_order',
            'entidad_id' => $order->id,
            'descripcion' => 'Orden creada manualmente: ' . $order->descripcion,
            'user_id' => Auth::id(),
        ]);
    }

    public function markAsSeen($id)
    {
        $order = PurchaseOrder::find($id);
        if (! $order) {
            return;
        }

        $order->update(['status' => 'en_proceso']);

        $this->loadOrders();
    }

    public function render()
    {
        return view('livewire.admin.purchase-orders');
    }
}
