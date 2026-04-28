<?php

namespace App\Livewire\Gerente;

use Livewire\Component;
use App\Models\PurchaseOrder;

class PurchaseOrders extends Component
{
    public $orders;

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        // Últimas 3 órdenes más recientes
        $this->orders = PurchaseOrder::orderBy('created_at', 'desc')->take(3)->get();
    }

    public function render()
    {
        return view('livewire.gerente.purchase-orders');
    }
}
