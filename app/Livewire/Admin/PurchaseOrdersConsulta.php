<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseOrder;

class PurchaseOrdersConsulta extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $perPage = 8;

    public function render()
    {
        $orders = PurchaseOrder::orderBy('created_at', 'desc')->paginate($this->perPage);
        return view('livewire.admin.purchase-orders-consulta', compact('orders'));
    }
}
