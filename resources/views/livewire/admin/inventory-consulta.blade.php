<div class="bg-white p-6 rounded shadow mx-auto">
    <h2 class="text-2xl font-bold text-green-800 mb-6 text-center">Consulta de Repuestos</h2>
    <div class="mb-4 flex flex-wrap gap-2 items-center">
        <input type="text" wire:model="search" placeholder="Buscar por SIGICOV" class="border rounded px-2 py-1" />
        <input type="text" wire:model="search_pasillo" placeholder="Buscar por pasillo" class="border rounded px-2 py-1" />
        <input type="text" wire:model="search_estante" placeholder="Buscar por estante" class="border rounded px-2 py-1" />

        <!-- export controls -->
        <div class="ml-auto flex items-center gap-2">
            <select wire:model="exportFormat" class="border rounded px-2 py-1">
                <option value="csv">CSV</option>
                <option value="xlsx">Excel (.xlsx)</option>
            </select>
            <button wire:click="export" class="px-3 py-1 bg-green-600 text-white rounded text-sm">Exportar</button>
        </div>
    </div>
    @if(session()->has('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(auth()->user() && auth()->user()->role === 'almacenista')
        <div class="mb-4 text-sm text-gray-600">Modo: <strong>Almacenista</strong> — puedes solicitar eliminación de un repuesto desde la columna "Acciones".</div>
    @endif
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead>
                    <tr class="bg-gray-100">
                    <th class="px-2 py-1">SIGICOV</th>
                    <th class="px-2 py-1">DESCRIPCIÓN</th>
                    <th class="px-2 py-1">PASILLO</th>
                    <th class="px-2 py-1">ESTANTE</th>
                    <th class="px-2 py-1">PELDAÑO</th>
                    <th class="px-2 py-1">FECHA</th>
                    <th class="px-2 py-1">REALIZADO POR</th>
                    <th class="px-2 py-1">CANTIDAD DISPONIBLE</th>
                    <th class="px-2 py-1">RESP. ACCESORIOS</th>
                    <th class="px-2 py-1">TS</th>
                    <th class="px-2 py-1">TOTAL MES</th>
                    <th class="px-2 py-1">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="{{ $loop->odd ? 'bg-white' : 'bg-blue-50' }} text-center">
                        <td class="px-2 py-1">{{ $item->sigicov }}</td>
                        <td class="px-2 py-1">{{ $item->descripcion }}</td>
                        <td class="px-2 py-1">{{ $item->pasillo }}</td>
                        <td class="px-2 py-1">{{ $item->estante }}</td>
                        <td class="px-2 py-1">{{ $item->peldaño }}</td>
                        <td class="px-2 py-1">{{ $item->fecha }}</td>
                        <td class="px-2 py-1">{{ $item->realizado_por }}</td>
                        <td class="px-2 py-1">{{ $item->inventario }}</td>
                        <td class="px-2 py-1">{{ $item->resp_accesorios }}</td>
                        <td class="px-2 py-1 text-xs">{{ $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '' }}</td>
                        <td class="px-2 py-1">{{ $item->monthly_total ?? 0 }}</td>
                        <td class="px-2 py-1">
                            @if(auth()->user() && auth()->user()->role === 'almacenista')
                                <button wire:click.prevent="openDeletionModal({{ $item->id }})" class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700">Solicitar eliminación</button>
                            @else
                                —
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr><td colspan="12" class="text-center text-gray-400 py-2">Sin resultados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal simple para solicitar eliminación -->
    @if($showDeletionModal)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded p-6 w-96">
                <h3 class="text-lg font-semibold mb-3">Solicitud de eliminación</h3>
                <p class="text-sm text-gray-600 mb-3">Explique brevemente por qué se debe eliminar este repuesto:</p>
                <textarea wire:model="deletionReason" rows="3" class="w-full border rounded p-2" required></textarea>
                @error('deletionReason') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror

                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700">Cantidad a eliminar</label>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="number" min="1" max="{{ $deletionItemInventory }}" wire:model="deletionQuantity" class="border rounded px-2 py-1 w-28" required />
                        <div class="text-sm text-gray-600">Existencias actuales: <strong>{{ $deletionItemInventory }}</strong></div>
                    </div>
                    @error('deletionQuantity') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                </div>
                @error('deletionReason') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                    <div class="mt-4 flex justify-end gap-2">
                    <button wire:click.prevent="$set('showDeletionModal', false)" class="px-3 py-1 rounded border">Cancelar</button>
                    <button id="btn-submit-deletion" wire:click.prevent="submitDeletionRequest" class="px-3 py-1 rounded bg-green-600 text-white">Enviar solicitud</button>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('deletion-requested', function (e) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Enviado',
                        text: e.detail.message,
                        timer: 2200,
                        showConfirmButton: false,
                    });
                } else {
                    alert(e.detail.message);
                }
            });

            // Opcional: evitar enviar si la cantidad > inventario (protección cliente)
            const btn = document.getElementById('btn-submit-deletion');
            if (btn) {
                btn.addEventListener('click', function () {
                    const qtyInput = document.querySelector('input[wire\\:model\.defer="deletionQuantity"], input[wire\\:model="deletionQuantity"]');
                    const inv = parseInt('{{ $deletionItemInventory }}') || 0;
                    if (qtyInput) {
                        const v = parseInt(qtyInput.value) || 0;
                        if (v > inv) {
                            if (window.Swal) {
                                Swal.fire({icon:'error', title:'Error', text:'La cantidad excede el inventario actual.'});
                            } else {
                                alert('La cantidad excede el inventario actual.');
                            }
                            // stop immediate action; Livewire will still call, but user sees message
                        }
                    }
                });
            }
        });
    </script>
</div>
