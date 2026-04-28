<div class="bg-red-50 p-6 rounded shadow">
    <h2 class="text-2xl font-bold text-red-800 mb-4">Registro (Configuración)</h2>
    <div class="mb-4">
        @if(session('success'))
            <div class="mb-2 p-2 rounded bg-green-100 text-green-800 text-center font-semibold">{{ session('success') }}</div>
        @endif
        <label class="block text-sm font-medium">Límite de administradores activos</label>
        <div class="flex items-center gap-2 mt-2">
            <input type="number" wire:model="max_administradores" min="2" max="12" class="border rounded px-2 py-1 w-24" />
            <button wire:click.prevent="save" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">Guardar</button>
        </div>
        <p class="text-xs text-gray-500 mt-2">Rango permitido: 2 a 12. Si se reduce este número, los administradores adicionales se deshabilitarán automáticamente en orden de creación.</p>
    </div>
</br>
    <div class="bg-white p-4 rounded shadow mt-4">
        <h3 class="text-lg font-semibold mb-3">Historial reciente (acciones automáticas)</h3>
        @if(isset($recentLogs) && $recentLogs->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-600">
                            <th class="pb-2">Fecha</th>
                            <th class="pb-2">Acción</th>
                            <th class="pb-2">Usuario</th>
                            <th class="pb-2">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLogs as $log)
                            <tr class="border-t">
                                <td class="py-2 text-gray-600">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                <td class="py-2">
                                    @if($log->accion === 'habilitar')
                                        <span class="text-green-600 font-semibold">Habilitar</span>
                                    @elseif($log->accion === 'deshabilitar')
                                        <span class="text-red-600 font-semibold">Deshabilitar</span>
                                    @else
                                        <span class="text-gray-700">{{ ucfirst($log->accion) }}</span>
                                    @endif
                                </td>
                                <td class="py-2">{{ optional($log->user)->name ?? 'Sistema' }}</td>
                                <td class="py-2 text-gray-700">{{ $log->descripcion }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-gray-500">No hay acciones recientes.</div>
        @endif
    </div>
</div>
