<div class="bg-white p-6 rounded shadow mx-auto">
    <h2 class="text-2xl font-bold text-green-800 mb-4 text-center">Histórico de Movimientos y Usuarios</h2>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block text-xs font-semibold">Entidad</label>
            <select class="w-full mt-1 text-sm" wire:model="entityFilter">
                <option value="">— Todas —</option>
                @foreach($entities as $e)
                    <option value="{{ $e }}">{{ $e }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold">Usuario</label>
            <select class="w-full mt-1 text-sm" wire:model="userFilter">
                <option value="">— Todos —</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold">Desde</label>
            <input type="date" class="w-full mt-1 text-sm" wire:model="dateFrom" />
        </div>
        <div>
            <label class="block text-xs font-semibold">Hasta</label>
            <input type="date" class="w-full mt-1 text-sm" wire:model="dateTo" />
        </div>
    </div>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Fecha</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Acción</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Entidad</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Descripción</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Usuario</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($logs as $log)
                    @php
                        $actionLabel = ucfirst(str_replace('_', ' ', $log->accion));
                        $actionClass = 'bg-gray-100 text-gray-800';
                        if (str_contains($log->accion, 'rechaz')) $actionClass = 'bg-red-100 text-red-800';
                        elseif (str_contains($log->accion, 'aprobar')) $actionClass = 'bg-green-100 text-green-800';
                        elseif (str_contains($log->accion, 'crear') || str_contains($log->accion, 'subir')) $actionClass = 'bg-blue-100 text-blue-800';
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-left text-xs text-gray-500"><div class="font-mono">{{ $log->created_at->format('Y-m-d H:i') }}</div></td>
                        <td class="px-4 py-3 text-left">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $actionClass }}">{{ $actionLabel }}</span>
                        </td>
                        <td class="px-4 py-3 text-left text-gray-700">
                            @if($log->entidad === 'inventory_item')
                                @php $item = \App\Models\InventoryItem::find($log->entidad_id); @endphp
                                @if($item)
                                    <div class="text-sm font-medium">{{ $item->sigicov }} — <span class="text-gray-600">{{ \Illuminate\Support\Str::limit($item->descripcion, 60) }}</span></div>
                                @else
                                    <div class="text-sm text-gray-500">Repuesto eliminado</div>
                                @endif
                            @elseif($log->entidad === 'purchase_order')
                                @php $po = \App\Models\PurchaseOrder::find($log->entidad_id); @endphp
                                @if($po)
                                    <div class="text-sm font-medium">OC #{{ $po->id }} — <span class="text-gray-600">{{ \Illuminate\Support\Str::limit($po->descripcion, 60) }}</span></div>
                                    <div class="mt-1"><span class="inline-block text-xs px-2 py-0.5 rounded {{ $po->status === 'aprobado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $po->status }}</span></div>
                                @else
                                    <div class="text-sm text-gray-500">OC eliminada (ID: {{ $log->entidad_id }})</div>
                                @endif
                            @elseif($log->entidad === 'spare_request' || $log->entidad === 'solicitud')
                                @php $sr = \App\Models\SpareRequest::find($log->entidad_id); @endphp
                                @if($sr)
                                    <div class="text-sm font-medium">Solicitud #{{ $sr->id }} — <span class="text-gray-600">{{ \Illuminate\Support\Str::limit($sr->descripcion, 60) }}</span></div>
                                    <div class="mt-1"><span class="inline-block text-xs px-2 py-0.5 rounded {{ $sr->status === 'aprobada' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $sr->status }}</span></div>
                                @else
                                    <div class="text-sm text-gray-500">Solicitud eliminada (ID: {{ $log->entidad_id }})</div>
                                @endif
                            @elseif($log->entidad === 'user' || $log->entidad === 'users')
                                @php $u = \App\Models\User::find($log->entidad_id); @endphp
                                @if($u)
                                    <div class="text-sm font-medium">{{ $u->name }}</div>
                                    @if(isset($u->role)) <div class="mt-1"><span class="inline-block text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-800">{{ $u->role }}</span></div> @endif
                                @else
                                    <div class="text-sm text-gray-500">Usuario eliminado (ID: {{ $log->entidad_id }})</div>
                                @endif
                            @else
                                <div class="text-sm text-gray-700">{{ $log->entidad }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-left align-top">
                            @php
                                $descRaw = $log->descripcion ?? '';
                                $items = preg_split('/\s*,\s*/', $descRaw);
                                $rendered = [];
                                foreach ($items as $item) {
                                    $t = trim($item);
                                    if (preg_match("/^([^:]+):\s*'([^']*)'\s*=>\s*'([^']*)'$/", $t, $m)) {
                                        $field = e(trim($m[1]));
                                        $old = e($m[2]);
                                        $new = e($m[3]);
                                        $rendered[] = "<div class='text-left'><span class='font-medium'>{$field}:</span> <span class='inline-block bg-red-50 text-red-700 px-1 rounded'>{$old}</span> <span class='px-1 text-gray-500'>→</span> <span class='inline-block bg-green-50 text-green-700 px-1 rounded'>{$new}</span></div>";
                                    } else {
                                        $rendered[] = '<div class="text-left">'.nl2br(e($t)).'</div>';
                                    }
                                }
                                $descriptionHtml = implode('', $rendered);
                            @endphp
                            <div class="max-h-32 overflow-y-auto text-sm text-gray-700 space-y-1">{!! $descriptionHtml !!}</div>
                        </td>
                        <td class="px-4 py-3 text-left text-sm text-gray-700">
                            @if($log->user)
                                <div class="font-medium">{{ $log->user->name }}</div>
                                @if(isset($log->user->role)) <div class="mt-1"><span class="inline-block text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-800">{{ $log->user->role }}</span></div> @endif
                            @else
                                <div class="text-sm text-gray-500">ID: {{ $log->user_id }}</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-gray-400 py-2">Sin registros</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
