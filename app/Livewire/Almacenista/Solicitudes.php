<?php

namespace App\Livewire\Almacenista;

use Livewire\Component;
use App\Models\SpareRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Log;

class Solicitudes extends Component
{
    public function render()
    {
        $requests = SpareRequest::where('almacenista_id', Auth::id())
            ->where(function($q){
                $q->whereNull('tipo')->orWhere('tipo','<>','eliminacion');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.almacenista.solicitudes', compact('requests'));
    }

    /**
     * Confirmar que el repuesto fue recibido por el almacenista.
     */
    public function confirmReceived($id)
    {
        $req = SpareRequest::find($id);
        if (!$req) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Solicitud no encontrada.']);
            return;
        }

        if ($req->almacenista_id !== Auth::id()) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'No autorizado.']);
            return;
        }

        // Sólo permitir confirmar si la solicitud ya fue aprobada por el gerente
        if ($req->status !== 'approved') {
            $this->dispatch('notification', ['type' => 'info', 'message' => 'No se puede confirmar hasta que la solicitud sea aprobada.']);
            return;
        }

        $req->status = 'received';
        $req->save();

        // Log
        Log::create([
            'accion' => 'confirmar_recibido',
            'entidad' => 'spare_request',
            'entidad_id' => $req->id,
            'descripcion' => 'Solicitud marcada como recibida por almacenista',
            'user_id' => Auth::id(),
        ]);

        $this->dispatch('notification', ['type' => 'success', 'message' => 'Recibió correctamente el repuesto.']);
        $this->dispatch('refresh')->self();
    }

    /**
     * Añadir o actualizar un comentario a la solicitud.
     */
    public function addComment($id, $comment)
    {
        Validator::make(['comment' => $comment], [
            'comment' => 'nullable|string|max:2000'
        ])->validate();

        $req = SpareRequest::find($id);
        if (!$req) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Solicitud no encontrada.']);
            return;
        }

        if ($req->almacenista_id !== Auth::id()) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'No autorizado.']);
            return;
        }

        $req->comentario = $comment;
        $req->save();

        // Log comentario
        Log::create([
            'accion' => 'comentar_solicitud',
            'entidad' => 'spare_request',
            'entidad_id' => $req->id,
            'descripcion' => 'Comentario: ' . substr($comment, 0, 500),
            'user_id' => Auth::id(),
        ]);

        $this->dispatch('notification', ['type' => 'success', 'message' => 'Comentario guardado.']);
        $this->dispatch('refresh')->self();
    }
}
