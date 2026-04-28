<?php

namespace App\Livewire\Gerente;

use Livewire\Component;
use App\Models\SpareRequest;
use App\Models\InventoryItem;
use App\Models\Log;

class Eliminaciones extends Component
{
    public $showRejectModal = false;
    public $rejectId = null;
    public $rejectReason = '';
    public $processingId = null;
    public $statusFilter = 'todos';

    public function render()
    {
        $requests = SpareRequest::query()
            ->where('tipo', 'eliminacion')
            ->with(['inventoryItem','almacenista'])
            ->when($this->statusFilter && $this->statusFilter !== 'todos', function($q) {
                if ($this->statusFilter === 'por_aprobar') {
                    $q->whereNotIn('status', ['aprobada', 'rechazada']);
                } else {
                    $q->where('status', $this->statusFilter);
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.gerente.eliminaciones', [
            'requests' => $requests,
        ]);
    }

    public function approve($id)
    {
        $this->processingId = $id;

        $sr = SpareRequest::find($id);
        if (!$sr) return session()->flash('error', 'Solicitud no encontrada.');
        $item = $sr->inventoryItem;
        if ($item) {
            $oldInv = (int) $item->inventario;
            $toRemove = (int) $sr->cantidad;
            $newInv = max(0, $oldInv - $toRemove);

            // Actualizar inventario
            $item->inventario = $newInv;
            $item->save();

            // Log del ajuste de inventario
            Log::create([
                'accion' => 'ajustar_inventario_por_eliminacion',
                'entidad' => 'inventory_item',
                'entidad_id' => $item->id,
                'descripcion' => "Solicitud {$sr->id}: inventario {$oldInv} => {$newInv} (se removieron {$toRemove})",
                'user_id' => auth()->id(),
            ]);
        }

        $sr->status = 'aprobada';
        $sr->save();

        Log::create([
            'accion' => 'aprobar_eliminacion',
            'entidad' => 'spare_request',
            'entidad_id' => $sr->id,
            'descripcion' => 'Solicitud de eliminacion aprobada por gerente',
            'user_id' => auth()->id(),
        ]);

        session()->flash('success', 'Solicitud aprobada y cantidad restada del inventario.');

        $this->processingId = null;
    }

    public function openRejectModal($id)
    {
        $this->rejectId = $id;
        $this->rejectReason = '';
        $this->showRejectModal = true;
        $this->processingId = $id;
    }

    public function cancelReject()
    {
        $this->showRejectModal = false;
        $this->rejectId = null;
        $this->rejectReason = '';
        $this->processingId = null;
    }

    public function confirmReject()
    {
        $this->validate(['rejectReason' => 'required|string|min:5']);

        $sr = SpareRequest::find($this->rejectId);
        if (!$sr) return session()->flash('error', 'Solicitud no encontrada.');

        $sr->status = 'rechazada';
        $sr->reject_reason = $this->rejectReason;
        $sr->save();

        Log::create([
            'accion' => 'rechazar_eliminacion',
            'entidad' => 'spare_request',
            'entidad_id' => $sr->id,
            'descripcion' => "Motivo: {$this->rejectReason}",
            'user_id' => auth()->id(),
        ]);

        $this->showRejectModal = false;
        $this->rejectId = null;
        $this->rejectReason = '';
        $this->processingId = null;

        session()->flash('success', 'Solicitud rechazada.');
    }
}
