<?php

namespace App\Livewire\Almacenista;

use App\Models\SpareRequest;
use App\Models\Log;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class SolicitarRepuestos extends Component
{
    public $cantidad;
    public $descripcion;
    public $detalles;

    public $successMessage;

    protected $rules = [
        'cantidad' => 'required|integer|min:1',
        'descripcion' => 'required|string|max:255',
        'detalles' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        // valores por defecto si es necesario
        $this->cantidad = 1;
    }

    public function submit()
    {
        $this->validate();

        $user = Auth::user();

        $spare = SpareRequest::create([
            'cantidad' => $this->cantidad,
            'descripcion' => $this->descripcion,
            'detalles' => $this->detalles,
            'almacenista_id' => $user->id,
        ]);

        // Registrar histórico
        Log::create([
            'accion' => 'crear_solicitud',
            'entidad' => 'spare_request',
            'entidad_id' => $spare->id,
            'descripcion' => 'Solicitud creada: ' . $this->descripcion,
            'user_id' => $user->id,
        ]);

        $this->successMessage = 'Solicitud creada correctamente (ID: ' . $spare->id . ').';

        // reset campos excepto cantidad por default
        $this->descripcion = '';
        $this->detalles = '';
        $this->cantidad = 1;

        // Emitir un evento Livewire v3 para notificaciones/actualizaciones front-end
        // (reemplaza dispatchBrowserEvent que puede no existir en esta versión).
        $this->dispatch('spare-request-created', ['id' => $spare->id]);
    }

    public function render()
    {
        $requests = SpareRequest::where('almacenista_id', Auth::id())
            ->where(function($q) {
                $q->where('tipo', '!=', 'eliminacion')
                  ->orWhereNull('tipo');
            })
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return view('livewire.almacenista.solicitar-repuestos', [
            'requests' => $requests,
            'almacenistaName' => Auth::user() ? Auth::user()->name : null,
        ]);
    }
}
