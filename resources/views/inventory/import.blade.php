@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h2 class="text-xl font-semibold mb-4">Importar inventario</h2>

    @if(session('message'))
        <div class="mb-4 p-2 bg-green-100 text-green-800 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if(session('import_token'))
        <div class="mb-4 p-4 bg-yellow-100 text-yellow-800 rounded" id="progress-container">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm">Progreso: <span id="progress">0</span> / <span id="total-rows">?</span> filas</div>
                <div id="progress-status" class="font-semibold text-sm"></div>
            </div>

            <div class="mt-3 w-full h-4 bg-gray-200 rounded overflow-hidden">
                <div id="progress-bar" class="h-full bg-blue-600 transition-all duration-300" style="width: 0%"></div>
            </div>

            <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <form action="{{ route('inventory.import.cancel', session('import_token')) }}" method="POST" onsubmit="return confirm('¿Deseas cancelar esta importación pendiente?');">
                    @csrf
                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-sm">Cancelar importación</button>
                </form>
                <div class="text-xs text-gray-700" id="percentage-text">0%</div>
            </div>
            <div class="mt-2 text-sm text-gray-700">
                Filas omitidas: <span id="skipped-rows">0</span>
            </div>
        </div>
        <script>
            (function(){
                var token = '{{ session('import_token') }}';
                var intervalId;

                function updateProgressBar(progress, total) {
                    var percent = 0;
                    if (total && total > 0) {
                        percent = Math.min(100, Math.round((progress / total) * 100));
                    }
                    document.getElementById('progress-bar').style.width = percent + '%';
                    document.getElementById('percentage-text').innerText = percent + '%';
                }

                function updateStatus(data) {
                    var current = data.imported_rows ?? data.progress ?? 0;
                    var total = data.total_rows || 0;

                    document.getElementById('progress').innerText = current;
                    document.getElementById('total-rows').innerText = total > 0 ? total : '?';
                    document.getElementById('skipped-rows').innerText = data.skipped_rows ?? 0;
                    updateProgressBar(current, total);

                    if (data.finished) {
                        document.getElementById('progress-status').innerText = 'Importación completada.';
                        document.getElementById('progress-container').className = 'mb-4 p-4 bg-green-100 text-green-800 rounded';
                        if (intervalId) {
                            clearInterval(intervalId);
                        }
                    } else if (data.status === 'failed') {
                        document.getElementById('progress-status').innerText = 'Error en la importación.';
                        document.getElementById('progress-container').className = 'mb-4 p-4 bg-red-100 text-red-800 rounded';
                        if (intervalId) {
                            clearInterval(intervalId);
                        }
                    } else {
                        document.getElementById('progress-status').innerText = 'Importando...';
                    }
                }

                function fetchProgress(){
                    fetch('{{ url('inventory/import/progress') }}/'+token)
                        .then(function(response){ return response.json(); })
                        .then(updateStatus)
                        .catch(console.error);
                }

                intervalId = setInterval(fetchProgress, 3000);
                fetchProgress();
            })();
        </script>
    @endif

    <form action="{{ route('inventory.import.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700" for="file">Archivo</label>
            <input id="file" name="file" type="file" required class="mt-1 block w-full" />
            @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Subir y procesar</button>
    </form>

    @if(isset($historyList) && $historyList->isNotEmpty())
        <div class="mt-8">
            <h3 class="text-lg font-semibold mb-3">Historial de importaciones</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100 text-left text-sm text-gray-700">
                            <th class="px-3 py-2">Fecha</th>
                            <th class="px-3 py-2">Archivo</th>
                            <th class="px-3 py-2">Estado</th>
                            <th class="px-3 py-2">Filas detectadas</th>
                            <th class="px-3 py-2">Filas importadas</th>
                            <th class="px-3 py-2">Progreso</th>
                            <th class="px-3 py-2">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historyList as $history)
                            <tr class="border-t border-gray-200 text-sm text-gray-700">
                                <td class="px-3 py-2">{{ optional($history->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="px-3 py-2">{{ $history->file_name }}</td>
                                <td class="px-3 py-2">{{ ucfirst($history->status) }}</td>
                                <td class="px-3 py-2">{{ $history->total_rows ?? 'N/A' }}</td>
                                <td class="px-3 py-2">{{ $history->imported_rows ?? 'N/A' }}</td>
                                <td class="px-3 py-2">{{ $history->skipped_rows ?? 0 }}</td>
                                <td class="px-3 py-2">{{ $history->progress ?? 0 }}</td>
                                <td class="px-3 py-2">
                                    @if(in_array($history->status, ['pending', 'processing']))
                                        <form action="{{ route('inventory.import.cancel', $history->token) }}" method="POST" onsubmit="return confirm('¿Deseas cancelar esta importación pendiente?');">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded text-xs">Cancelar</button>
                                        </form>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
