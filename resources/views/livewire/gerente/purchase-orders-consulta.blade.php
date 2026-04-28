<div x-data="{ showInvoice: false, invoiceUrl: '', invoiceExt: '', invoiceOrderId: null, invoiceStatus: '' }" class="p-4 bg-white rounded shadow">
    <h3 class="text-lg font-semibold mb-4">Consulta de Órdenes de compra</h3>

    <div class="space-y-3">
        @forelse($orders as $order)
            <div class="p-3 border rounded flex items-start justify-between bg-gray-50">
                <div>
                    <div class="font-semibold">#{{ $order->id }} - <span class="text-sm text-gray-600">{{ $order->pedido_por }}</span></div>
                    {{-- Estado visible --}}
                    @php
                        $badgeText = 'Pendiente';
                        $badgeBg = 'bg-yellow-50';
                        $badgeBorder = 'border-yellow-300';
                        $badgeTextColor = 'text-yellow-800';
                        $icon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"></path></svg>';
                        $title = '';
                        if ($order->status === 'aprobado') {
                            $badgeText = 'Aprobada';
                            $badgeBg = 'bg-green-50';
                            $badgeBorder = 'border-green-300';
                            $badgeTextColor = 'text-green-800';
                            $icon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                            $title = 'Aprobada: ' . ($order->approved_at ? $order->approved_at->format('d/m/Y H:i') : '');
                        } elseif ($order->status === 'por_revisar') {
                            $badgeText = 'Por revisar';
                            $badgeBg = 'bg-amber-50';
                            $badgeBorder = 'border-amber-300';
                            $badgeTextColor = 'text-amber-800';
                            $icon = '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>';
                            $title = 'Por revisar: ' . ($order->updated_at ? $order->updated_at->format('d/m/Y H:i') : '');
                        } elseif ($order->status === 'factura_subida') {
                            $badgeText = 'Factura subida';
                            $badgeBg = 'bg-indigo-50';
                            $badgeBorder = 'border-indigo-300';
                            $badgeTextColor = 'text-indigo-800';
                            $icon = '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>';
                            $title = 'Factura subida: ' . ($order->updated_at ? $order->updated_at->format('d/m/Y H:i') : '');
                        } elseif ($order->status === 'en_proceso') {
                            
                            $badgeText = 'En proceso';
                            $badgeBg = 'bg-blue-50';
                            $badgeBorder = 'border-blue-300';
                            $badgeTextColor = 'text-blue-800';
                            $icon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path></svg>';
                            $title = 'Última revisión: ' . ($order->updated_at ? $order->updated_at->format('d/m/Y H:i') : '');
                        } elseif ($order->status === 'rechazado') {
                            $badgeText = 'Rechazada';
                            $badgeBg = 'bg-red-50';
                            $badgeBorder = 'border-red-300';
                            $badgeTextColor = 'text-red-800';
                            $icon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                            $title = 'Rechazada: ' . ($order->updated_at ? $order->updated_at->format('d/m/Y H:i') : '');
                        } elseif ($order->status === 'completado') {
                            $badgeText = 'Completada';
                            $badgeBg = 'bg-gray-100';
                            $badgeBorder = 'border-gray-300';
                            $badgeTextColor = 'text-gray-800';
                            $icon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                            $title = 'Completada: ' . ($order->updated_at ? $order->updated_at->format('d/m/Y H:i') : '');
                        } else {
                            $title = 'Registrada: ' . ($order->created_at ? $order->created_at->format('d/m/Y H:i') : '');
                        }
                    @endphp
                    <div class="mt-2">
                        <span title="{{ $title }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold rounded-full shadow-sm border {{ $badgeBg }} {{ $badgeBorder }} {{ $badgeTextColor }}"  role="status" aria-label="Estado: {{ $badgeText }}">
                            {!! $icon !!}
                            <span class="leading-none">{{ $badgeText }}</span>
                        </span>
                    </div>
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
                            <button @click="invoiceUrl='{{ asset($url) }}'; invoiceExt='{{ $ext }}'; invoiceOrderId={{ $order->id }}; invoiceStatus='{{ $order->status }}'; showInvoice = true; $wire.markAsPorRevisar({{ $order->id }})" class="inline-block px-2 py-1 rounded bg-indigo-600 text-white text-sm">Ver factura</button>
                        </div>
                    @else
                        <span class="text-xs text-gray-500">Sin factura</span>
                    @endif
                    <div class="text-xs text-gray-400 mt-2">
                        @if($order->status === 'aprobado')
                            Aprobado {{ $order->approved_at ? $order->approved_at->locale('es')->diffForHumans() : '' }}
                        @elseif($order->status === 'por_revisar')
                            Por revisar {{ $order->updated_at ? $order->updated_at->locale('es')->diffForHumans() : '' }}
                        @elseif($order->status === 'factura_subida')
                            Factura subida {{ $order->updated_at ? $order->updated_at->locale('es')->diffForHumans() : $order->created_at->locale('es')->diffForHumans() }}
                        @elseif($order->status === 'en_proceso')
                            Revisado {{ $order->updated_at ? $order->updated_at->locale('es')->diffForHumans() : '' }}
                        @elseif($order->status === 'rechazado')
                            Rechazada {{ $order->updated_at ? $order->updated_at->locale('es')->diffForHumans() : '' }}
                        @else
                            Registrado {{ $order->created_at->locale('es')->diffForHumans() }}
                        @endif
                    </div>

                    @if($order->status === 'rechazado' && $order->reject_reason)
                        <div class="mt-2 text-sm text-red-700">Motivo: {{ $order->reject_reason }}</div>
                    @endif
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
    <div x-show="showInvoice" x-cloak x-init="Livewire.on('orderApproved', id => { if (invoiceOrderId == id) showInvoice = false }); Livewire.on('orderRejected', id => { if (invoiceOrderId == id) showInvoice = false })" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
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
                            <div class="mt-4 flex justify-end gap-2">
                                <button x-show="invoiceOrderId" x-bind:disabled="!invoiceOrderId || invoiceStatus === 'aprobado'" x-bind:class="invoiceStatus === 'aprobado' ? 'opacity-50 cursor-not-allowed' : ''" @click.prevent="Swal.fire({
                                    title: 'Confirmación',
                                    text: '¿Revisó la factura correctamente y desea proceder a aprobarla?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Sí, aprobar',
                                    cancelButtonText: 'Cancelar'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // immediately close modal to hide buttons while server processes
                                        showInvoice = false;
                                        invoiceOrderId = null;
                                        $wire.approve(invoiceOrderId)
                                    }
                                })" class="px-3 py-1 rounded bg-green-600 text-white">Aprobar orden</button>

                                <button x-show="invoiceOrderId" x-bind:disabled="!invoiceOrderId || invoiceStatus === 'aprobado'" x-bind:class="invoiceStatus === 'aprobado' ? 'opacity-50 cursor-not-allowed' : ''" @click.prevent="Swal.fire({
                                    title: 'Rechazar orden',
                                    text: 'Indica el motivo del rechazo:',
                                    input: 'textarea',
                                    inputPlaceholder: 'Escribe el motivo aquí... (requerido)',
                                    inputAttributes: {
                                        'aria-label': 'Motivo del rechazo'
                                    },
                                    showCancelButton: true,
                                    confirmButtonText: 'Confirmar rechazo',
                                    cancelButtonText: 'Cancelar',
                                    preConfirm: (value) => {
                                        if (!value || !value.trim()) {
                                            Swal.showValidationMessage('El motivo es requerido');
                                        }
                                        return value;
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        const reason = result.value;
                                        // immediately close modal to hide buttons while server processes
                                        showInvoice = false;
                                        invoiceOrderId = null;
                                        $wire.reject(invoiceOrderId, reason);
                                    }
                                })" class="px-3 py-1 rounded bg-red-600 text-white">Rechazar orden</button>
                            </div>
        </div>
    </div>
</div>
