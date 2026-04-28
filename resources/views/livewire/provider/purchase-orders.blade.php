<div x-data="{ showInvoice: false, invoiceUrl: '', invoiceExt: '' }" class="p-4 bg-white rounded shadow">
    <h2 class="text-lg font-semibold mb-4">Órdenes de compra recibidas</h2>

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
                    <div class="text-xs mt-1">
                        Estado:
                        @if($order->status === 'pendiente')
                            <span class="text-yellow-600 font-semibold">Pendiente</span>
                        @elseif($order->status === 'en_proceso')
                            <span class="text-blue-600 font-semibold">En proceso</span>
                        @elseif($order->status === 'aprobado')
                            <span class="text-green-600 font-semibold">Aprobada</span>
                        @elseif($order->status === 'factura_subida')
                            <span class="text-indigo-600 font-semibold">Factura subida (pendiente revisión)</span>
                        @elseif($order->status === 'rechazado')
                            <span class="text-red-600 font-semibold">Rechazada</span>
                        @else
                            <span class="text-gray-700 font-semibold">{{ ucfirst($order->status) }}</span>
                        @endif
                    </div>
                    @if($order->approved_at)
                        <div class="text-xs text-gray-400 mt-2">Factura subida {{ $order->approved_at->locale('es')->diffForHumans() }}</div>
                    @elseif($order->status === 'en_proceso' && $order->updated_at)
                        <div class="text-xs text-gray-400 mt-2">Revisado {{ $order->updated_at->locale('es')->diffForHumans() }}</div>
                    @elseif($order->status === 'rechazado')
                        <div class="text-xs text-red-600 mt-2">Rechazada {{ $order->updated_at ? $order->updated_at->locale('es')->diffForHumans() : '' }}</div>
                    @else
                        <div class="text-xs text-gray-400 mt-2">Registrado {{ $order->created_at->locale('es')->diffForHumans() }}</div>
                    @endif

                    @if($order->status === 'rechazado' && $order->reject_reason)
                        <div class="mt-2 text-sm text-red-700">Motivo: {{ $order->reject_reason }}</div>
                    @endif
                </div>

                <div class="flex flex-col items-end">
                    @if($order->status === 'pendiente')
                        <button wire:click.prevent="markAsSeen({{ $order->id }})" class="px-3 py-1 rounded bg-blue-600 text-white">Marcar como visto</button>
                    @elseif($order->status === 'en_proceso')
                        <div class="text-sm text-gray-600">Revisado</div>
                    @elseif($order->status === 'aprobado')
                        <div class="text-sm text-green-600 font-semibold">Aprobada</div>
                    @elseif($order->status === 'factura_subida')
                        <div class="text-sm text-indigo-600 font-semibold">Factura subida (pendiente revisión)</div>
                    @else
                        <div class="text-sm text-gray-600">{{ ucfirst($order->status) }}</div>
                    @endif

                    <div class="mt-2">
                        @if($order->status !== 'aprobado' && ! $order->attachment)
                            <button wire:click.prevent="openUploadModal({{ $order->id }})" class="px-2 py-1 rounded bg-green-600 text-white text-sm">Subir factura</button>
                        @endif

                        {{-- Si la orden fue rechazada y ya existe una factura, permitir re-subir/reemplazar --}}
                        @if($order->status === 'rechazado')
                            <button wire:click.prevent="openUploadModal({{ $order->id }})" class="px-2 py-1 rounded bg-yellow-600 text-white text-sm">Re-subir factura</button>
                        @endif

                        @if($order->attachment && auth()->user() && in_array(auth()->user()->role, ['gerente','administrador']))
                            @php
                                $url = Storage::url($order->attachment);
                                $ext = strtolower(pathinfo($order->attachment, PATHINFO_EXTENSION));
                            @endphp
                            <div>
                                <button @click="invoiceUrl='{{ asset($url) }}'; invoiceExt='{{ $ext }}'; showInvoice = true" class="inline-block px-2 py-1 rounded bg-indigo-600 text-white text-sm">Ver factura</button>
                            </div>
                        @endif
                    </div>

                    <div class="text-xs text-gray-400 mt-2">{{ $order->created_at->locale('es')->diffForHumans() }}</div>
                </div>
            </div>
        @empty
            <div class="text-gray-500">No hay órdenes registradas.</div>
        @endforelse
    </div>

    {{-- Modal de subida: se muestra cuando $uploadingOrderId no es null --}}
    @if($uploadingOrderId)
        @php $modalOrder = \App\Models\PurchaseOrder::find($uploadingOrderId); @endphp
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-lg">
                <h4 class="text-lg font-bold mb-4">Aprobar factura - Pedido #{{ $uploadingOrderId }}</h4>
                @if(session('error'))
                    <div class="mb-2 p-2 rounded bg-red-100 text-red-800 text-center font-semibold">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="mb-2 p-2 rounded bg-green-100 text-green-800 text-center font-semibold">{{ session('success') }}</div>
                @endif
                <div class="mb-4">
                    <div class="text-sm text-gray-600 mb-2">Pedido por: {{ $modalOrder?->pedido_por ?? 'N/A' }}</div>
                    <input type="file" wire:model="attachments.{{ $uploadingOrderId }}" accept=".pdf,image/png,image/jpeg" />
                    <x-input-error :messages="$errors->get('attachments.' . $uploadingOrderId)" class="mt-2" />
                    <div wire:loading wire:target="attachments.{{ $uploadingOrderId }}" class="text-xs text-gray-500 mt-1">Cargando archivo...</div>
                    @if($modalOrder && $modalOrder->attachment)
                        <div class="mt-2">
                            <a href="{{ Storage::url($modalOrder->attachment) }}" target="_blank" class="text-sm text-indigo-600 underline">Ver factura actual</a>
                        </div>
                    @endif
                </div>
                <div class="flex justify-end gap-2">
                    <button wire:click.prevent="closeUploadModal" class="px-3 py-1 rounded bg-gray-300 hover:bg-gray-400">Cancelar</button>

                    @if($modalOrder && $modalOrder->attachment)
                        {{-- Si ya existe una factura, pedir confirmación antes de reemplazar --}}
                        <button @click.prevent="Swal.fire({
                            title: 'Confirmar reemplazo',
                            text: 'Se reemplazará la factura existente (si la hay). ¿Deseas continuar?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, reemplazar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // call Livewire method to upload (attachments are bound via wire:model)
                                $wire.uploadInvoice({{ $uploadingOrderId }});
                            }
                        })" class="px-3 py-1 rounded bg-green-600 text-white">Aprobar</button>
                    @else
                        {{-- Primera subida: no pedir confirmación --}}
                        <button wire:click.prevent="uploadInvoice({{ $uploadingOrderId }})" class="px-3 py-1 rounded bg-green-600 text-white">Aprobar</button>
                    @endif
                </div>
            </div>
        </div>
    @endif

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
