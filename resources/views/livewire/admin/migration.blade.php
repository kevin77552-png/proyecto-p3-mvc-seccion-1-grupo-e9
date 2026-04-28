<div>
    <div>
        @if(session('message'))
            <div class="mb-4 p-2 bg-green-100 text-green-800 rounded">{{ session('message') }}</div>
        @endif
    </div>
    <div class="bg-white p-6 rounded shadow max-w-7xl mx-auto mt-6">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div id="import-section" class="border rounded p-4">
            <h3 class="font-semibold mb-2">Importar</h3>
            <p class="text-sm text-gray-600">Selecciona un archivo CSV o Excel con el formato indicado.</p>

            <form id="import-form" action="{{ route('inventory.import.store') }}#import-section" method="POST" enctype="multipart/form-data" onsubmit="if(!confirm('¿Confirmas iniciar la importación? Esto puede tardar y bloqueará la página hasta completar.')){return false;} document.getElementById('import-overlay').classList.remove('hidden');">
                @csrf

                <div class="mt-4">
                    <input id="file" name="file" type="file" required class="mt-1 block w-full" />
                    @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Importar</button>
                </div>
            </form>

            <div id="import-overlay" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded shadow text-center">
                    <div class="loader mb-4">Procesando importación…</div>
                    <div class="w-64 h-2 bg-gray-200 rounded overflow-hidden">
                        <div class="h-2 bg-blue-600 animate-pulse" style="width:60%"></div>
                    </div>
                </div>
            </div>


        </div>

        <div class="border rounded p-4">
            <h3 class="font-semibold mb-2">Exportar</h3>
            <p class="text-sm text-gray-600">Exporta los registros actuales a CSV o Excel.</p>

            <div class="mt-4 flex items-center gap-2">
                <select wire:model="exportFormat" class="border rounded px-2 py-1">
                    <option value="csv">CSV</option>
                    <option value="xlsx">Excel (.xlsx)</option>
                </select>

                <button onclick="if(!confirm('¿Confirmas exportar los datos?')){event.stopImmediatePropagation(); event.preventDefault();}" wire:click="export" class="px-4 py-2 bg-green-600 text-white rounded">Exportar</button>

                <div wire:loading.flex wire:target="export" class="items-center gap-2">
                    <div class="w-40 h-2 bg-gray-200 rounded overflow-hidden">
                        <div class="h-2 bg-green-500 animate-pulse" style="width:50%"></div>
                    </div>
                    <span class="text-sm text-gray-600">Generando archivo…</span>
                </div>
            </div>
        </div>
    </div>

{{-- script to auto-scroll to alert when page loads --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var msg = document.querySelector('.mb-4');
        if (msg) {
            msg.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
@endpush
