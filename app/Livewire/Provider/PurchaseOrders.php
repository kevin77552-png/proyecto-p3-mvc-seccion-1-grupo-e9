<?php

namespace App\Livewire\Provider;

use App\Models\PurchaseOrder;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class PurchaseOrders extends Component
{
    use WithFileUploads;
    public $orders;
    // attachments is an associative array: attachments[orderId] => UploadedFile
    public $attachments = [];
    // id del pedido actualmente abierto en el modal de subida
    public $uploadingOrderId = null;

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        // Mostrar los pedidos realizados por el administrador (status cualquiera)
        $this->orders = PurchaseOrder::orderBy('created_at', 'desc')->get();
    }

    public function markAsSeen($id)
    {
        $order = PurchaseOrder::find($id);
        if (! $order) {
            return;
        }

        // El proveedor marca como visto: cambiar estado a 'en_proceso'
        $order->update(['status' => 'en_proceso']);

        // Log
        Log::create([
            'accion' => 'marcar_visto',
            'entidad' => 'purchase_order',
            'entidad_id' => $order->id,
            'descripcion' => 'Proveedor marcó la orden como vista',
            'user_id' => Auth::id(),
        ]);

        $this->loadOrders();
    }

    /**
     * Upload an invoice/attachment for a purchase order (provider uploads).
     */
    public function uploadInvoice($id)
    {
        if (! isset($this->attachments[$id])) {
            session()->flash('error', 'Seleccione un archivo antes de subir.');
            return;
        }

        $file = $this->attachments[$id];

        // Validación: pdf, jpg, jpeg, png, máximo 3 MB (3072 KB)
        $this->validate([
            "attachments.$id" => 'file|mimes:pdf,jpg,jpeg,png|max:3072',
        ], [
            "attachments.$id.max" => 'El archivo no puede superar los 3 MB.',
            "attachments.$id.mimes" => 'Formato no permitido. Use pdf, jpg, jpeg o png.',
        ]);

        $order = PurchaseOrder::find($id);
        if (! $order) {
            session()->flash('error', 'Orden no encontrada.');
            return;
        }

        $path = $file->store('invoices', 'public');

        // If there was a previous attachment, copy it into a replaced/ folder for audit,
        // then delete the original to avoid leaving it in the root invoices folder.
        try {
            if ($order->attachment && Storage::disk('public')->exists($order->attachment)) {
                $backupDir = 'invoices/replaced/' . $order->id;
                Storage::disk('public')->makeDirectory($backupDir);
                $baseName = basename($order->attachment);
                $backupPath = $backupDir . '/' . time() . '_' . $baseName;
                // copy original to backup and then delete original
                Storage::disk('public')->copy($order->attachment, $backupPath);
                Storage::disk('public')->delete($order->attachment);
            }
        } catch (\Exception $e) {
            // ignore file copy/delete issues, continue to save new attachment
        }
        // Intentar guardar la ruta en la columna `attachment` si existe.
        try {
            if (Schema::hasColumn('purchase_orders', 'attachment')) {
                // Guardar la ruta y marcar la orden como 'factura_subida' cuando el proveedor sube la factura.
                // La aprobación final la realiza el gerente y cambiará el estado a 'aprobado'.
                $order->update([
                    'attachment' => $path,
                    'status' => 'factura_subida',
                    'approved_at' => null,
                ]);
            
                // Log subida de factura
                Log::create([
                    'accion' => 'subir_factura',
                    'entidad' => 'purchase_order',
                    'entidad_id' => $order->id,
                    'descripcion' => 'Proveedor subió/reesubió factura: ' . $path,
                    'user_id' => Auth::id(),
                ]);
            } else {
                // La migración no se ejecutó; informar al usuario y no lanzar excepción SQL
                session()->flash('error', 'La columna "attachment" no existe en la base de datos. Ejecute: php artisan migrate');
            }
        } catch (QueryException $e) {
            // Capturar cualquier error SQL y mostrar mensaje amigable
            session()->flash('error', 'Error al guardar la factura en la base de datos. ' . $e->getMessage());
        }

        // Limpiar input para ese pedido
        unset($this->attachments[$id]);

        // Cerrar modal
        $this->uploadingOrderId = null;

        // Si no hubo error previo, indicar éxito
        if (! session()->get('error')) {
            session()->flash('success', 'Factura subida correctamente.');
        }

        $this->loadOrders();
    }

    public function openUploadModal($id)
    {
        $this->uploadingOrderId = $id;
    }

    public function closeUploadModal()
    {
        $this->uploadingOrderId = null;
    }

    public function render()
    {
        return view('livewire.provider.purchase-orders');
    }
}
