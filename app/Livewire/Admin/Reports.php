<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SpareRequest;
use App\Models\PurchaseOrder;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class Reports extends Component
{
    public function render()
    {
        // Solicitudes pendientes
        $pendingRequests = SpareRequest::whereIn('status', ['pending', 'por_revisar'])->orderBy('created_at', 'asc')->get();

        // Órdenes sin factura (attachment is null)
        $openOrdersMissingInvoice = PurchaseOrder::whereIn('status', ['aprobado', 'por_revisar', 'en_proceso'])
            ->whereNull('attachment')
            ->orderBy('created_at', 'desc')
            ->get();

        // Órdenes con factura subida
        $ordersWithInvoiceUploaded = PurchaseOrder::where('status', 'factura_subida')->orderBy('updated_at','desc')->get();

        // Rechazos frecuentes (por motivo) - from spare_requests and purchase_orders
        $rejectionsFromRequests = SpareRequest::whereNotNull('reject_reason')
            ->select('reject_reason', DB::raw('count(*) as total'))
            ->groupBy('reject_reason')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $rejectionsFromOrders = PurchaseOrder::whereNotNull('reject_reason')
            ->select('reject_reason', DB::raw('count(*) as total'))
            ->groupBy('reject_reason')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Lead time approximated: avg time between created_at and approved_at
        $approvalTimes = PurchaseOrder::whereNotNull('approved_at')
            ->whereColumn('approved_at', '>=', 'created_at')
            ->get()
            ->map(function ($o) {
                return Carbon::parse($o->approved_at)->diffInHours($o->created_at);
            });

        $avgApprovalHours = $approvalTimes->count() ? round($approvalTimes->avg(), 1) : null;

        // Attempt to calculate critical / low stock if columns exist
        $criticalStock = collect();
        $lowStock = collect();
        if (Schema::hasTable('inventory_items')) {
            // check for numeric 'inventario' and an optional 'stock_minimo' column
            if (Schema::hasColumn('inventory_items', 'inventario') && Schema::hasColumn('inventory_items', 'stock_minimo')) {
                $criticalStock = InventoryItem::whereRaw('CAST(inventario AS SIGNED) <= CAST(stock_minimo AS SIGNED)')
                    ->orderByRaw('CAST(inventario AS SIGNED) - CAST(stock_minimo AS SIGNED) ASC')
                    ->get();

                $lowStock = InventoryItem::whereRaw('CAST(inventario AS SIGNED) > CAST(stock_minimo AS SIGNED)')
                    ->whereRaw('CAST(inventario AS SIGNED) <= CAST(reorder_point AS SIGNED)')
                    ->orderByRaw('CAST(inventario AS SIGNED) - CAST(reorder_point AS SIGNED) ASC')
                    ->get();
            }
        }

        return view('livewire.admin.reports', compact(
            'pendingRequests',
            'openOrdersMissingInvoice',
            'ordersWithInvoiceUploaded',
            'rejectionsFromRequests',
            'rejectionsFromOrders',
            'avgApprovalHours',
            'criticalStock',
            'lowStock'
        ));
    }
}
