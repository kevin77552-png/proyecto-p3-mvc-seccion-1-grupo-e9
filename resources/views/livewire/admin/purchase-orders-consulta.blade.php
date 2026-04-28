<div x-data="{ showInvoice: false, invoiceUrl: '', invoiceExt: '' }" class="p-4 bg-white rounded shadow">
    <h2 class="text-lg font-semibold mb-4">Consultar Órdenes de compra</h2>

    <div class="space-y-3">
        @forelse($orders as $order)
            <div class="p-3 border rounded flex items-start justify-between bg-gray-50">
                <div>
                    <div class="font-semibold">#{{ $order->id }} - <span class="text-sm text-gray-600">{{ $order->pedido_por }}</span></div>
                    <div class="text-sm text-gray-800">Cantidad: {{ $order->cantidad }}</div>
                    <div class="text-sm text-gray-700">{{ $order->descripcion }}</div>
                    @if($order->detalles)
                        <div class="text-sm text-gray-600 mt-1">Detalles: {{ $order->detalles }}</div>
                    @endif
                </div>
                <div class="flex flex-col items-end">
                    @if($order->attachment)
                        @php
                            $url = Storage::url($order->attachment);
                            $ext = strtolower(pathinfo($order->attachment, PATHINFO_EXTENSION));
                        @endphp
                        <div>
                            <button @click="invoiceUrl='{{ asset($url) }}'; invoiceExt='{{ $ext }}'; showInvoice = true" class="inline-block px-2 py-1 rounded bg-indigo-600 text-white text-sm">Ver factura</button>
                        </div>
                    @else
                        <span class="text-xs text-gray-500">Sin factura</span>
                    @endif
                    <div class="text-xs text-gray-400 mt-2">@if($order->approved_at) Aprobado {{ $order->approved_at->locale('es')->diffForHumans() }} @elseif($order->status === 'en_proceso') Revisado {{ $order->updated_at->locale('es')->diffForHumans() }} @else Registrado {{ $order->created_at->locale('es')->diffForHumans() }} @endif</div>
                </div>
            </div>
        @empty
            <div class="text-gray-500">No hay órdenes registradas.</div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>

    {{-- Modal para ver factura --}}
    <div x-show="showInvoice" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
        <div class="bg-white rounded shadow-lg w-11/12 md:w-3/4 lg:w-2/3 max-h-[90vh] overflow-auto p-4">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-semibold">Factura</h4>
                <div class="flex items-center gap-2">
                    <a x-bind:href="invoiceUrl" x-bind:download="'factura.' + invoiceExt" target="_blank" rel="noopener noreferrer" class="px-2 py-1 rounded bg-green-600 text-white text-sm">Descargar</a>
                    <button @click="showInvoice = false" class="px-2 py-1 rounded bg-gray-200">Cerrar</button>
                </div>
            </div>
            <div class="flex justify-center">
                <template x-if="invoiceExt === 'pdf'">
                    <iframe :src="invoiceUrl" class="w-full h-[70vh]" frameborder="0"></iframe>
                </template>
                <template x-if="invoiceExt !== 'pdf'">
                    <img :src="invoiceUrl" alt="Factura" class="max-h-[80vh] w-auto" />
                </template>
            </div>
        </div>
    </div>
</div>
