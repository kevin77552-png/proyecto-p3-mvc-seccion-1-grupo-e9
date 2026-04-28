<div class="bg-white p-6 rounded shadow mx-auto">
     <h2 class="text-2xl font-bold text-green-800 mb-6 text-center">Ingreso de Repuestos</h2>
    <div x-data="{ show: @entangle('showSaved'), message: @entangle('savedMessage') }" x-effect="if(show){ setTimeout(()=> show = false, 4000) }" class="mb-2">
        <div x-show="show" x-transition class="mb-4 p-3 rounded bg-green-100 text-green-800 text-center font-semibold" x-text="message"></div>
    </div>
    <form x-data @submit.prevent="
        if (window.Swal && typeof Swal.fire === 'function') {
            Swal.fire({
                title: 'Confirmar registro',
                text: '¿Confirmas el registro del repuesto?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then((result) => { if (result.isConfirmed) { $wire.save() } });
        } else {
            if (confirm('¿Confirmas el registro del repuesto?')) { $wire.save() }
        }
    " class="space-y-4 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold mb-1">SISTEMA SIGICOV</label>
                <input type="text" wire:model="sigicov" class="w-full border rounded px-2 py-1" placeholder="Solo caracteres numéricos" oninput="this.value = this.value.toUpperCase()">
                @error('sigicov') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold mb-1">DESCRIPCIÓN DE REPUESTO O ACCESORIO</label>
                <input type="text" wire:model="descripcion" class="w-full border rounded px-2 py-1" placeholder="Conj. de turbocompresor" oninput="this.value = this.value.toUpperCase()">
                @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold mb-1">PASILLO</label>
                <input type="text" wire:model="pasillo" class="w-full border rounded px-2 py-1" placeholder="P-P" required oninput="this.value = this.value.toUpperCase()">
                @error('pasillo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">ESTANTE</label>
                <input type="text" wire:model="estante" class="w-full border rounded px-2 py-1" placeholder="2" required oninput="this.value = this.value.toUpperCase()">
                @error('estante') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">PELDAÑO</label>
                <input type="text" wire:model="peldaño" class="w-full border rounded px-2 py-1" placeholder="C" required oninput="this.value = this.value.toUpperCase()">
                @error('peldaño') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">FECHA</label>
                <input type="date" wire:model="fecha" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">REALIZADO POR</label>
                <input type="text" wire:model="realizado_por" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold mb-1">INVENTARIO</label>
                <input type="number" min="1" wire:model="inventario" class="w-full border rounded px-2 py-1" placeholder="1" required>
                @error('inventario') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">RESP. ACCESORIOS</label>
                <input type="text" wire:model="resp_accesorios" class="w-full border rounded px-2 py-1" placeholder="1118-00967" required oninput="this.value = this.value.toUpperCase()">
                @error('resp_accesorios') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <button type="submit" wire:loading.attr="disabled" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center">
                <svg wire:loading class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span wire:loading.remove>Registrar</span>
                <span wire:loading>Registrando...</span>
            </button>
        </div>
    </form>

    <div class="mb-4 flex flex-wrap gap-2 items-center">
        <input type="text" wire:model="search" placeholder="Buscar por SIGICOV" class="border rounded px-2 py-1" />
        <input type="text" wire:model="search_pasillo" placeholder="Buscar por pasillo" class="border rounded px-2 py-1" />
        <input type="text" wire:model="search_estante" placeholder="Buscar por estante" class="border rounded px-2 py-1" />
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead>
                    <tr class="bg-gray-100">
                    <th class="px-2 py-1">SIGICOV</th>
                    <th class="px-2 py-1">DESCRIPCIÓN</th>
                    <!-- columna Almacén eliminada -->
                    <th class="px-2 py-1">PASILLO</th>
                    <th class="px-2 py-1">ESTANTE</th>
                    <th class="px-2 py-1">PELDAÑO</th>
                    <th class="px-2 py-1">FECHA</th>
                    <th class="px-2 py-1">REALIZADO POR</th>
                    <th class="px-2 py-1">CANTIDAD DISPONIBLE</th>
                    <th class="px-2 py-1">RESP. ACCESORIOS</th>
                    <th class="px-2 py-1">TS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="{{ $loop->odd ? 'bg-white' : 'bg-blue-50' }} text-center">
                        <td class="px-2 py-1">{{ $item->sigicov }}</td>
                        <td class="px-2 py-1">{{ $item->descripcion }}</td>
                        <!-- columna almacen eliminada -->
                        <td class="px-2 py-1">{{ $item->pasillo }}</td>
                        <td class="px-2 py-1">{{ $item->estante }}</td>
                        <td class="px-2 py-1">{{ $item->peldaño }}</td>
                        <td class="px-2 py-1">{{ $item->fecha }}</td>
                        <td class="px-2 py-1">{{ $item->realizado_por }}</td>
                        <td class="px-2 py-1">{{ $item->inventario }}</td>
                        <td class="px-2 py-1">{{ $item->resp_accesorios }}</td>
                        <td class="px-2 py-1 text-xs">{{ $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '' }}</td>
                        <!-- columna Total Inv. Junio eliminada -->
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-gray-400 py-2">Sin resultados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
