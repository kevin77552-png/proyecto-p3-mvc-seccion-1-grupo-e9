<?php

namespace App\Livewire\Gerente;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;

class PurchaseOrdersConsulta extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $perPage = 6;
    // propiedades para rechazo
    public $rejectReason = '';


    public function render()
    {
        $orders = PurchaseOrder::orderBy('created_at', 'desc')->paginate($this->perPage);
        return view('livewire.gerente.purchase-orders-consulta', compact('orders'));
    }

    /**
     * Approve a purchase order (mark as 'aprobado' and set approved_at).
     * Only allow users with role 'gerente'.
     *
     * @param int $id
     * @return void
     */
    public function approve($id)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'gerente') {
            // Use Livewire v3 event API (dispatch) instead of dispatchBrowserEvent
            $this->dispatch('notification', ['type' => 'error', 'message' => 'No autorizado']);
            return;
        }

        $order = PurchaseOrder::find($id);
        if (! $order) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Orden no encontrada']);
            return;
        }

        $order->status = 'aprobado';
        $order->approved_at = now();
        $order->save();

        // Log aprobación
        Log::create([
            'accion' => 'aprobar_orden',
            'entidad' => 'purchase_order',
            'entidad_id' => $order->id,
            'descripcion' => 'Orden aprobada por gerente',
            'user_id' => $user->id,
        ]);
    // notify front-end and refresh via Livewire events (Livewire v3 API)
    $this->dispatch('orderApproved', $id);
    $this->dispatch('notification', ['type' => 'success', 'message' => 'Orden aprobada correctamente']);
    // mark the event as "self" so only this component instance receives it
    $this->dispatch('refresh')->self();
    }

    public function refresh()
    {
        // noop: trigger re-render
    }

    /**
     * Mark an order as 'por_revisar' when the gerente opens the invoice modal.
     *
     * @param int $id
     * @return void
     */
    public function markAsPorRevisar($id)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'gerente') {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'No autorizado']);
            return;
        }

        $order = PurchaseOrder::find($id);
        if (! $order) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Orden no encontrada']);
            return;
        }

        // Only change to por_revisar if invoice exists and it's not already aprobado/rechazado/por_revisar
        if ($order->attachment && ! in_array($order->status, ['aprobado', 'rechazado', 'por_revisar'])) {
            $order->status = 'por_revisar';
            $order->save();
            $this->dispatch('notification', ['type' => 'info', 'message' => 'Orden marcada como por revisar']);
            $this->dispatch('refresh')->self();
            // Log cambio a por_revisar
            Log::create([
                'accion' => 'marcar_por_revisar',
                'entidad' => 'purchase_order',
                'entidad_id' => $order->id,
                'descripcion' => 'Gerente abrió la factura para revisión',
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Reject a purchase order with a reason.
     *
     * @param int $id
     * @param string|null $reason
     * @return void
     */
    public function reject($id, $reason = null)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'gerente') {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'No autorizado']);
            return;
        }

        $order = PurchaseOrder::find($id);
        if (! $order) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Orden no encontrada']);
            return;
        }

        $reason = trim($reason ?? $this->rejectReason ?? '');
        if ($reason === '') {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Debe ingresar un motivo para el rechazo']);
            return;
        }

        // Use a direct query update to ensure approved_at is set to NULL in DB
        PurchaseOrder::where('id', $order->id)->update([
            'status' => 'rechazado',
            'reject_reason' => $reason,
            'approved_at' => null,
            'updated_at' => now(),
        ]);

        // Log rechazo
        Log::create([
            'accion' => 'rechazar_orden',
            'entidad' => 'purchase_order',
            'entidad_id' => $order->id,
            'descripcion' => 'Orden rechazada: ' . $reason,
            'user_id' => $user->id,
        ]);
        $this->dispatch('orderRejected', $id);
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Orden rechazada correctamente']);
        $this->dispatch('refresh')->self();

        // limpiar campo local
        $this->rejectReason = '';
    }
}
