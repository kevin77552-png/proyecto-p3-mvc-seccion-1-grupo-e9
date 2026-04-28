<?php

namespace App\Livewire\Gerente;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SpareRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;

class Solicitudes extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $perPage = 10;
    public $rejectingId = null;
    public $rejectReason = '';

    public function mount()
    {
        // restringir acceso adicional si es necesario
        if (!Auth::check() || Auth::user()->role !== 'gerente') {
            abort(403);
        }
    }

    public function render()
    {
        $requests = SpareRequest::with('almacenista')
            ->where(function($q){
                $q->whereNull('tipo')->orWhere('tipo', '<>', 'eliminacion');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.gerente.solicitudes', compact('requests'));
    }

    public function approve($id)
    {
        $req = SpareRequest::find($id);
        if (!$req) return session()->flash('error', 'Solicitud no encontrada.');

        // Crear una orden de compra a partir de la solicitud
        $order = \App\Models\PurchaseOrder::create([
            'cantidad' => $req->cantidad,
            'descripcion' => $req->descripcion,
            'detalles' => $req->detalles,
            // registrar el nombre del gerente que aprobó como 'pedido_por'
            'pedido_por' => Auth::check() ? Auth::user()->name : null,
            'status' => 'pendiente',
            'spare_request_id' => $req->id,
        ]);

        // Log creación de la orden
        Log::create([
            'accion' => 'crear_orden_desde_solicitud',
            'entidad' => 'purchase_order',
            'entidad_id' => $order->id,
            'descripcion' => 'Orden creada desde solicitud ID: ' . $req->id,
            'user_id' => Auth::id(),
        ]);

        // Log cambio de estado de la solicitud
        Log::create([
            'accion' => 'aprobar_solicitud',
            'entidad' => 'spare_request',
            'entidad_id' => $req->id,
            'descripcion' => 'Solicitud aprobada por gerente y vinculada a OC ID: ' . $order->id,
            'user_id' => Auth::id(),
        ]);
        $req->status = 'approved';
        $req->reject_reason = null;
        $req->save();

        session()->flash('message', 'Solicitud aprobada y orden de compra creada (ID: ' . $order->id . ').');
        // refrescar paginación
        $this->resetPage();
    }

    public function openReject($id)
    {
        $this->rejectingId = $id;
        $this->rejectReason = '';
    }

    public function cancelReject()
    {
        $this->rejectingId = null;
        $this->rejectReason = '';
    }

    public function submitReject()
    {
        $this->validate([
            'rejectReason' => 'required|string|max:1000',
        ]);

        $req = SpareRequest::find($this->rejectingId);
        if (!$req) {
            session()->flash('error', 'Solicitud no encontrada.');
            $this->cancelReject();
            return;
        }

        $req->status = 'rejected';
        $req->reject_reason = $this->rejectReason;
        $req->save();

        // Log rechazo
        Log::create([
            'accion' => 'rechazar_solicitud',
            'entidad' => 'spare_request',
            'entidad_id' => $req->id,
            'descripcion' => 'Solicitud rechazada: ' . $this->rejectReason,
            'user_id' => Auth::id(),
        ]);
        session()->flash('message', 'Solicitud rechazada.');
        $this->cancelReject();
        $this->resetPage();
    }
}
