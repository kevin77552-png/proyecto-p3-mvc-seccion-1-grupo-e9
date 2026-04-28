<div class="bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold text-purple-800 mb-4">Reportes operativos</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-red-50 border border-red-200 rounded">
            <div class="text-sm text-red-600">Solicitudes pendientes</div>
            <div class="text-2xl font-bold mt-2">{{ $pendingRequests->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">Solicitudes sin aprobar</div>
        </div>

        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded">
            <div class="text-sm text-yellow-600">Órdenes sin factura</div>
            <div class="text-2xl font-bold mt-2">{{ $openOrdersMissingInvoice->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">OC aprobadas sin adjunto</div>
        </div>

        <div class="p-4 bg-indigo-50 border border-indigo-200 rounded">
            <div class="text-sm text-indigo-600">Órdenes con factura subida</div>
            <div class="text-2xl font-bold mt-2">{{ $ordersWithInvoiceUploaded->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">Requieren revisión</div>
        </div>
    </div>

    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Rechazos frecuentes (solicitudes)</h3>
        @if($rejectionsFromRequests->isEmpty())
            <div class="text-sm text-gray-500">No se encontraron rechazos recientes.</div>
        @else
            <ul class="list-disc pl-6 text-sm">
                @foreach($rejectionsFromRequests as $r)
                    <li>{{ $r->reject_reason }} — {{ $r->total }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Rechazos frecuentes (órdenes)</h3>
        @if($rejectionsFromOrders->isEmpty())
            <div class="text-sm text-gray-500">No se encontraron rechazos en órdenes.</div>
        @else
            <ul class="list-disc pl-6 text-sm">
                @foreach($rejectionsFromOrders as $r)
                    <li>{{ $r->reject_reason }} — {{ $r->total }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-semibold mb-2">Tiempos promedio</h3>
            @if($avgApprovalHours)
                <div class="text-sm text-gray-700">Tiempo promedio aprobación: <span class="font-bold">{{ $avgApprovalHours }} horas</span></div>
            @else
                <div class="text-sm text-gray-500">No hay suficientes datos de aprobación.</div>
            @endif
        </div>

        <div>
            <h3 class="text-lg font-semibold mb-2">Stock crítico / bajo</h3>
            @if($criticalStock->isEmpty() && $lowStock->isEmpty())
                <div class="text-sm text-gray-500">No hay datos de stock numéricos disponibles. (Revise columnas `inventario`, `stock_minimo`, `reorder_point` en `inventory_items`.)</div>
            @else
                @if($criticalStock->isNotEmpty())
                    <div class="text-sm text-red-600 font-semibold mb-1">Repuestos en Nivel Crítico</div>
                    <ul class="text-sm list-disc pl-6 mb-2">
                        @foreach($criticalStock as $it)
                            <li>{{ $it->descripcion }} — {{ $it->inventario }} (mínimo: {{ $it->stock_minimo }})</li>
                        @endforeach
                    </ul>
                @endif
                @if($lowStock->isNotEmpty())
                    <div class="text-sm text-yellow-600 font-semibold mb-1">Bajo stock</div>
                    <ul class="text-sm list-disc pl-6">
                        @foreach($lowStock as $it)
                            <li>{{ $it->descripcion }} — {{ $it->inventario }} (reorder: {{ $it->reorder_point }})</li>
                        @endforeach
                    </ul>
                @endif
            @endif
        </div>
    </div>

    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Detalles recientes</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 border rounded">
                <h4 class="font-semibold mb-2">Solicitudes pendientes (últimas 10)</h4>
                <ul class="text-sm list-disc pl-6">
                    @foreach($pendingRequests->take(10) as $p)
                        <li>{{ $p->descripcion }} — {{ $p->cantidad }} — {{ $p->created_at->diffForHumans() }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="p-4 border rounded">
                <h4 class="font-semibold mb-2">Órdenes sin factura (últimas 10)</h4>
                <ul class="text-sm list-disc pl-6">
                    @foreach($openOrdersMissingInvoice->take(10) as $o)
                        <li>OC #{{ $o->id }} — {{ $o->descripcion }} — Creada {{ $o->created_at->diffForHumans() }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
