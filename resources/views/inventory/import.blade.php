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
        <div class="mb-4 p-2 bg-yellow-100 text-yellow-800 rounded" id="progress-container">
            Progreso: <span id="progress">0</span> filas procesadas...
        </div>
        <script>
            (function(){
                var token = '{{ session('import_token') }}';
                function fetchProgress(){
                    fetch('{{ url('inventory/import/progress') }}/'+token)
                        .then(r=>r.json())
                        .then(data=>{
                            document.getElementById('progress').innerText = data.progress;
                        })
                        .catch(console.error);
                }
                // check each 5 segundos
                setInterval(fetchProgress, 5000);
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
</div>
@endsection
