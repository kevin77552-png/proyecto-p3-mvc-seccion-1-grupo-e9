<div class="bg-white p-6 rounded shadow mx-auto">
    <h2 class="text-2xl font-bold text-green-800 mb-6 text-center">Egreso de Repuestos</h2>

    @if($success)
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ $success }}</div>
    @endif
    @if($error)
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ $error }}</div>
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
                               <th class="px-2 py-1">CANTIDAD DISPONIBLE</th>
                               <th class="px-2 py-1">SALIDA</th>
                           </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr class="{{ $loop->odd ? 'bg-white' : 'bg-blue-50' }} text-center">
                        <td class="px-2 py-1">{{ $item->sigicov }}</td>
                        <td class="px-2 py-1">{{ $item->descripcion }}</td>
                        <td class="px-2 py-1">{{ $item->pasillo }}</td>
                        <td class="px-2 py-1">{{ $item->estante }}</td>
                        <td class="px-2 py-1">{{ $item->peldaño }}</td>
                        <td class="px-2 py-1">{{ $item->inventario }}</td>
                        <td class="px-2 py-1">
                            <button wire:click="openSalidaModal({{ $item->id }})" class="text-red-600 hover:text-red-800" title="Salida">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </div>

        @if($showSalidaModal)
            <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white rounded p-6 w-96">
                    <h3 class="text-lg font-semibold mb-3">Salida de repuesto</h3>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Cantidad a retirar</label>
                        <input type="number" wire:model.defer="cantidad" min="1" class="w-full border rounded p-2" />
                        @error('cantidad') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Placa del vehículo</label>
                        <input type="text" wire:model.defer="vehiculo_placa" class="w-full border rounded p-2" placeholder="Ej: ABC123" />
                        @error('vehiculo_placa') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Descripción del vehículo</label>
                        <input type="text" wire:model.defer="vehiculo_descripcion" class="w-full border rounded p-2" placeholder="Ej: Camioneta blanca" />
                        @error('vehiculo_descripcion') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button wire:click.prevent="closeSalidaModal" class="px-3 py-1 rounded border">Cancelar</button>
                        <button wire:click.prevent="confirmSalida" class="px-3 py-1 rounded bg-green-600 text-white">Confirmar salida</button>
                    </div>
                </div>
            </div>
        @endif
        </table>
    </div>
</div>
