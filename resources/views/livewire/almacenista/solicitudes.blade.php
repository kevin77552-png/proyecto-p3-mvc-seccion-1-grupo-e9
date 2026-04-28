<div class="bg-white p-6 rounded shadow max-w-6xl mx-auto">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Solicitudes de repuestos</h3>

    @if($requests->isEmpty())
        <div class="text-gray-600">No has realizado solicitudes todavía.</div>
    @else
        <div class="space-y-4">
            @foreach($requests as $req)
                <div class="border border-gray-200 rounded p-4 flex items-start justify-between {{ $loop->odd ? 'bg-white' : 'bg-blue-50' }}">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="font-semibold text-gray-800">Cantidad:</span>
                            <span class="text-gray-700">{{ $req->cantidad }}</span>
                            <span class="ml-4 font-semibold text-gray-800">Creada:</span>
                            <span class="text-gray-600">{{ optional($req->created_at)->diffForHumans() }}</span>
                        </div>

                        @if($req->comentario)
                            <div class="mt-2 text-sm text-gray-700">
                                <strong>Comentario:</strong> {{ $req->comentario }}
                            </div>
                        @endif

                        <div class="mt-2">
                            <div class="text-gray-700">{{ $req->descripcion }}</div>
                        </div>

                        @if($req->detalles)
                            <div class="mt-2">
                                <div class="font-semibold">Detalles</div>
                                <div class="text-gray-600">{{ $req->detalles }}</div>
                            </div>
                        @endif

                        <div class="mt-3">
                            @if($req->status === 'pending')
                                <span class="inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Pendiente</span>
                            @elseif($req->status === 'approved')
                                <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded">Aprobada</span>
                            @elseif($req->status === 'rejected')
                                <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded">Rechazada</span>
                                @if($req->reject_reason)
                                    <div class="text-sm text-red-700 mt-1">Motivo: {{ $req->reject_reason }}</div>
                                @endif
                            @elseif($req->status === 'received')
                                <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded">Recibido</span>
                            @endif
                        </div>
                    </div>

                    <div class="text-right">
                        @if($req->purchaseOrder)
                            <div class="text-sm text-gray-600 mb-2">Pedido generado</div>
                            <div class="font-semibold text-indigo-700">OC #{{ $req->purchaseOrder->id }}</div>
                        @else
                            <div class="text-sm text-gray-500">Sin pedido asociado</div>
                        @endif

                        {{-- sólo mostrar el botón de confirmación cuando el gerente ya aprobó la solicitud --}}
                        @if($req->status === 'approved')
                            <div class="mt-3 flex flex-col items-end gap-2">
                                <button
                                    @click.prevent="Swal.fire({
                                        title: 'Confirmación',
                                        text: '¿Confirmar que recibiste el repuesto?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Sí, confirmar',
                                        cancelButtonText: 'Cancelar'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.confirmReceived({{ $req->id }});
                                        }
                                    })"
                                    class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700"
                                >
                                    Confirmar recibido
                                </button>

                                <button
                                    @click.prevent="Swal.fire({
                                        title: 'Agregar comentario',
                                        input: 'textarea',
                                        inputPlaceholder: 'Escribe tu comentario...',
                                        showCancelButton: true,
                                        confirmButtonText: 'Guardar',
                                        cancelButtonText: 'Cancelar',
                                        preConfirm: (value) => {
                                            if (!value || !value.trim()) {
                                                Swal.showValidationMessage('El comentario no puede estar vacío');
                                            }
                                            return value;
                                        }
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.addComment({{ $req->id }}, result.value);
                                        }
                                    })"
                                    class="inline-block bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300 text-sm"
                                >
                                    Comentar
                                </button>
                            </div>
                        @elseif($req->status === 'received')
                            <div class="mt-3 text-sm text-green-700">Has confirmado la recepción</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Listener para mostrar notificaciones (SweetAlert si está disponible) -->
<script>
    document.addEventListener('livewire:load', function () {
        if (typeof Livewire !== 'undefined' && Livewire.on) {
            Livewire.on('notification', function (payload) {
                try {
                    var type = payload.type || 'info';
                    var message = payload.message || '';
                    if (window.Swal && typeof Swal.fire === 'function') {
                        var icon = type === 'success' ? 'success' : (type === 'error' ? 'error' : 'info');
                        Swal.fire({
                            icon: icon,
                            title: (icon === 'success' ? 'Confirmado' : (icon === 'error' ? 'Error' : 'Información')),
                            text: message,
                            timer: 3000,
                            toast: false
                        });
                    } else {
                        // Fallback simple
                        alert(message);
                    }
                } catch (e) {
                    console.error('Error mostrando notificación:', e);
                }
            });
        }
    });
</script>
