<div class="bg-white p-6 rounded shadow max-w-7xl mx-auto">
   

    @if(session()->has('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto">
            <div class="mb-4 flex items-center justify-start gap-2">
                <div class="flex items-center gap-2">
                    <button wire:click="$set('statusFilter','todos')" class="px-3 py-1 rounded {{ $statusFilter === 'todos' ? 'bg-indigo-600 text-white' : 'bg-gray-100' }}">Todos</button>
                    <button wire:click="$set('statusFilter','por_aprobar')" class="px-3 py-1 rounded {{ $statusFilter === 'por_aprobar' ? 'bg-indigo-600 text-white' : 'bg-gray-100' }}">Por aprobar</button>
                    <button wire:click="$set('statusFilter','aprobada')" class="px-3 py-1 rounded {{ $statusFilter === 'aprobada' ? 'bg-indigo-600 text-white' : 'bg-gray-100' }}">Aprobadas</button>
                    <button wire:click="$set('statusFilter','rechazada')" class="px-3 py-1 rounded {{ $statusFilter === 'rechazada' ? 'bg-indigo-600 text-white' : 'bg-gray-100' }}">Rechazadas</button>
                </div>
            </div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead>
                <tr class="bg-gray-50 text-xs text-gray-600 uppercase">
                    <th class="px-6 py-3">Repuesto</th>
                    <th class="px-6 py-3">Cantidad solicitada</th>
                    <th class="px-6 py-3">Inventario actual</th>
                    <th class="px-6 py-3">Solicitante</th>
                    <th class="px-6 py-3">Estado</th>
                    <th class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($requests as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">{{ optional($r->inventoryItem)->sigicov }} - {{ optional($r->inventoryItem)->descripcion }}</td>
                        <td class="px-6 py-4 text-sm">{{ $r->cantidad ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">{{ optional($r->inventoryItem)->inventario ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">{{ optional($r->almacenista)->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($r->status === 'aprobada')
                                <span class="inline-block text-xs px-2 py-0.5 rounded bg-green-100 text-green-800">Aprobada</span>
                            @elseif($r->status === 'rechazada')
                                <span class="inline-block text-xs px-2 py-0.5 rounded bg-red-100 text-red-800">Rechazada</span>
                            @else
                                <span class="inline-block text-xs px-2 py-0.5 rounded bg-yellow-100 text-yellow-800">Por revisar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($r->status !== 'aprobada' && $r->status !== 'rechazada')
                                <button
                                    wire:click="approve({{ $r->id }})"
                                    class="bg-green-500 text-white px-3 py-1 rounded"
                                    @if($processingId === $r->id) disabled @endif
                                    wire:loading.attr="disabled"
                                    wire:target="approve"
                                >
                                    Aprobar
                                </button>
                                <button
                                    wire:click="openRejectModal({{ $r->id }})"
                                    class="bg-red-500 text-white px-3 py-1 rounded"
                                    @if($processingId === $r->id) disabled @endif
                                    wire:loading.attr="disabled"
                                    wire:target="openRejectModal"
                                >
                                    Rechazar
                                </button>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-gray-400 py-4">No hay solicitudes de eliminación</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal rechazar -->
    @if($showRejectModal)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded p-6 w-96">
                <h3 class="text-lg font-semibold mb-3">Rechazar solicitud</h3>
                <textarea wire:model.defer="rejectReason" rows="4" class="w-full border rounded p-2" placeholder="Motivo del rechazo"></textarea>
                @error('rejectReason') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                    <div class="mt-4 flex justify-end gap-2">
                    <button wire:click.prevent="cancelReject" class="px-3 py-1 rounded border">Cancelar</button>
                    <button
                        wire:click.prevent="confirmReject"
                        class="px-3 py-1 rounded bg-red-600 text-white"
                        wire:loading.attr="disabled"
                        wire:target="confirmReject"
                    >
                        Confirmar rechazo
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
