<div class="bg-white p-6 rounded shadow max-w-7xl mx-auto mt-6">
    <h2 class="text-2xl font-bold text-indigo-800 mb-4">Solicitudes de Repuestos</h2>

    @if($requests->isEmpty())
        <div class="text-gray-500">No hay solicitudes registradas.</div>
    @else
        @if(session()->has('message'))
            <div class="mb-4 bg-green-100 text-green-800 p-3 rounded">{{ session('message') }}</div>
        @endif
        @if(session()->has('error'))
            <div class="mb-4 bg-red-100 text-red-800 p-3 rounded">{{ session('error') }}</div>
        @endif
        <div class="mb-4 flex items-center justify-between">
            <div class="text-sm text-gray-600">Mostrando {{ $requests->firstItem() }} - {{ $requests->lastItem() }} de {{ $requests->total() }} solicitudes</div>
            <div>
                <label class="text-sm text-gray-600 mr-2">Por página:</label>
                <select wire:model="perPage" class="rounded border-gray-300 shadow-sm">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                </select>
            </div>
        </div>

        <ul class="space-y-3">
            @foreach($requests as $req)
                <li class="border border-gray-200 rounded p-4">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div class="font-semibold text-gray-800">{{ $req->descripcion }}</div>
                                <div class="text-sm text-gray-500">{{ $req->created_at->format('d/m/Y H:i') }}</div>
                            </div>

                            <div class="mt-2 text-sm text-gray-700">Cantidad: <span class="font-medium">{{ $req->cantidad }}</span></div>

                            @php
                                $badgeText = ucfirst($req->status ?? 'pending');
                                $badgeBg = 'bg-gray-100';
                                $badgeBorder = 'border-gray-300';
                                $badgeTextColor = 'text-gray-800';
                                $icon = '';
                                if ($req->status === 'approved') {
                                    $badgeText = 'Aprobado';
                                    $badgeBg = 'bg-green-50';
                                    $badgeBorder = 'border-green-300';
                                    $badgeTextColor = 'text-green-800';
                                    $icon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                                } elseif ($req->status === 'pending') {
                                    $badgeText = 'Pendiente';
                                    $badgeBg = 'bg-yellow-50';
                                    $badgeBorder = 'border-yellow-300';
                                    $badgeTextColor = 'text-yellow-800';
                                    $icon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>';
                                } elseif ($req->status === 'rejected') {
                                    $badgeText = 'Rechazada';
                                    $badgeBg = 'bg-red-50';
                                    $badgeBorder = 'border-red-300';
                                    $badgeTextColor = 'text-red-800';
                                    $icon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
                                }
                            @endphp
                            <div class="mt-2">
                                <span title="Estado: {{ $badgeText }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold rounded-full shadow-sm border {{ $badgeBg }} {{ $badgeBorder }} {{ $badgeTextColor }}" role="status" aria-label="Estado: {{ $badgeText }}">
                                    {!! $icon !!}
                                    <span class="leading-none">{{ $badgeText }}</span>
                                </span>
                            </div>

                            @if($req->reject_reason)
                                <div class="mt-2 text-sm text-red-600">Motivo rechazo: {{ $req->reject_reason }}</div>
                            @endif

                            @if($req->detalles)
                                <div class="mt-2 text-sm text-gray-600">{{ $req->detalles }}</div>
                            @endif

                            @if($req->comentario)
                                <div class="mt-2 text-sm text-gray-700">
                                    <strong>Comentario (almacenista):</strong> {{ $req->comentario }}
                                </div>
                            @endif
                        </div>

                        <div class="w-48 text-right">
                            <div class="text-sm text-gray-500">Almacenista</div>
                            <div class="font-medium">{{ $req->almacenista?->name ?? '—' }}</div>
                            <div class="mt-3 flex flex-col items-end gap-2">
                                @if($req->status === 'pending')
                                    <button wire:click="approve({{ $req->id }})" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Aprobar</button>
                                    <button wire:click="openReject({{ $req->id }})" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">Rechazar</button>
                                @else
                                    <span class="text-sm text-gray-500">Acciones cerradas</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    @endif

    <!-- Modal de rechazo -->
    @if($rejectingId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded shadow-lg w-full max-w-2xl p-6">
                <h3 class="text-lg font-semibold mb-3">Rechazar solicitud</h3>
                <div class="mb-3 text-sm text-gray-600">Por favor indica el motivo del rechazo (obligatorio).</div>
                <textarea wire:model.defer="rejectReason" rows="4" class="w-full rounded border-gray-300 p-2"></textarea>
                @error('rejectReason') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror

                <div class="mt-4 flex justify-end gap-2">
                    <button wire:click="cancelReject" class="px-4 py-2 rounded border">Cancelar</button>
                    <button wire:click="submitReject" class="px-4 py-2 rounded bg-red-600 text-white">Enviar rechazo</button>
                </div>
            </div>
        </div>
    @endif
</div>
