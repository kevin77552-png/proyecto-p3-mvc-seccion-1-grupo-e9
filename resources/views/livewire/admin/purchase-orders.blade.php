<div x-data="{ showInvoice: false, invoiceUrl: '', invoiceExt: '' }" class="p-4 bg-white rounded shadow">
    <h2 class="text-lg font-semibold mb-4">Órdenes de compra</h2>

    <form wire:submit.prevent="createOrder" class="grid grid-cols-1 gap-3 mb-6">
        <div class="flex gap-2">
            <div class="w-24">
                <x-input-label for="cantidad" :value="'Cantidad'" />
                <x-text-input wire:model="cantidad" id="cantidad" name="cantidad" type="number" class="mt-1 block w-full" min="1" />
                <x-input-error :messages="$errors->get('cantidad')" class="mt-1" />
            </div>
            <div class="flex-1">
                <x-input-label for="descripcion" :value="'Descripción'" />
                <textarea wire:model="descripcion" id="descripcion" name="descripcion" class="block mt-1 w-full rounded border-gray-300" rows="2"></textarea>
                <x-input-error :messages="$errors->get('descripcion')" class="mt-1" />
            </div>
        </div>

        <div>
            <x-input-label for="pedido_por" :value="'Pedido realizado por'" />
            <div class="mt-1 block w-full rounded border-gray-200 bg-gray-100 text-gray-800 px-3 py-2">{{ auth()->user() ? auth()->user()->name : '' }}</div>
        </div>

        <div>
            <x-input-label for="detalles" :value="'Detalles (opcional)'" />
            <textarea wire:model="detalles" id="detalles" name="detalles" class="block mt-1 w-full rounded border-gray-300" rows="2"></textarea>
            <x-input-error :messages="$errors->get('detalles')" class="mt-1" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button type="submit">Crear pedido</x-primary-button>
            <div wire:loading wire:target="createOrder">Creando...</div>
        </div>
    </form>

    <div class="space-y-3">
        @forelse($orders as $order)
            <div class="p-3 border rounded flex items-start justify-between bg-gray-50">
                <div>
                    <div class="font-semibold">#{{ $order->id }} - <span class="text-sm text-gray-600">{{ $order->pedido_por }}</span></div>
                    <div class="text-sm text-gray-800">Cantidad: {{ $order->cantidad }}</div>
                    <div class="text-sm text-gray-700">{{ $order->descripcion }}</div>
                    <div class="text-xs mt-1">
                        Estado: 
                        @if($order->status === 'pendiente')
                            <span class="text-yellow-600 font-semibold">Pendiente</span>
                        @elseif($order->status === 'en_proceso')
                            <span class="text-blue-600 font-semibold">En proceso</span>
                        @else
                            <span class="text-green-600 font-semibold">{{ ucfirst($order->status) }}</span>
                        @endif
                    </div>
                    {{-- En la vista de creación no se muestra el botón "Ver factura" para las consultas rápidas --}}

                    {{-- Mostrar tiempo según estado --}}
                    @if($order->approved_at)
                        <div class="text-xs text-gray-400 mt-2">Aprobado {{ $order->approved_at->locale('es')->diffForHumans() }}</div>
                    @elseif($order->status === 'en_proceso' && $order->updated_at)
                        <div class="text-xs text-gray-400 mt-2">Revisado {{ $order->updated_at->locale('es')->diffForHumans() }}</div>
                    @else
                        <div class="text-xs text-gray-400 mt-2">Registrado {{ $order->created_at->locale('es')->diffForHumans() }}</div>
                    @endif
                </div>
                <div class="flex flex-col items-end">
                    @if($order->status === 'pendiente')
                        <span class="text-sm text-gray-600">Sin revisar</span>
                    @else
                        <span class="text-sm text-gray-600">Revisado</span>
                    @endif
                    <div class="text-xs text-gray-400">{{ $order->created_at->locale('es')->diffForHumans() }}</div>
                </div>
            </div>
        @empty
            <div class="text-gray-500">No hay órdenes registradas.</div>
        @endforelse
    
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
</div>
