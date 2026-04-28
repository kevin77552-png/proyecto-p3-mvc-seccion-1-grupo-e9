<div class="bg-white p-6 rounded shadow max-w-7xl mx-auto mt-6">
    

    @if($successMessage)
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ $successMessage }}</div>
    @endif

    <form wire:submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
        <div>
            <label class="block text-sm font-medium text-gray-700">Cantidad</label>
            <input wire:model.defer="cantidad" type="number" min="1" class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
            @error('cantidad') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Descripción</label>
            <input wire:model.defer="descripcion" type="text" class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
            @error('descripcion') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="md:col-span-3">
            <label class="block text-sm font-medium text-gray-700">Detalles (opcional)</label>
            <textarea wire:model.defer="detalles" rows="4" class="mt-1 block w-full rounded border-gray-300 shadow-sm"></textarea>
            @error('detalles') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="md:col-span-3 flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-600">Solicitud realizada por:</div>
                <div class="text-base font-semibold">{{ $almacenistaName ?? '—' }}</div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Crear solicitud</button>
            </div>
        </div>
    </form>

    <hr class="my-6" />

    <h3 class="text-lg font-semibold text-gray-700 mb-3">Solicitudes recientes</h3>
    @if($requests->isEmpty())
        <div class="text-gray-500">No hay solicitudes registradas todavía.</div>
    @else
        <ul class="space-y-2">
            @foreach($requests as $req)
                <li class="border border-gray-200 rounded p-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-semibold">{{ $req->descripcion }}</div>
                            <div class="text-sm text-gray-600">Cantidad: {{ $req->cantidad }}</div>
                            @if($req->detalles)
                                <div class="text-sm mt-1">{{ $req->detalles }}</div>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500">{{ $req->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
