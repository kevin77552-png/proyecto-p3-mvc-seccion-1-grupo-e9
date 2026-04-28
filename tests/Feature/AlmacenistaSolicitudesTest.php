<?php

namespace Tests\Feature;

use App\Livewire\Almacenista\Solicitudes as SolicitudesComponent;
use App\Models\SpareRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AlmacenistaSolicitudesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function boton_confirmar_no_aparece_antes_de_aprobacion_y_no_se_puede_ejecutar()
    {
        $user = User::factory()->create(['role' => 'almacenista']);
        $this->actingAs($user);

        $req = SpareRequest::create([
            'cantidad' => 1,
            'descripcion' => 'Prueba',
            'almacenista_id' => $user->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($user)
            ->test(SolicitudesComponent::class)
            ->assertDontSee('Confirmar recibido')
            ->call('confirmReceived', $req->id);

        $this->assertDatabaseHas('spare_requests', [
            'id' => $req->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function boton_confirmar_aparece_despues_de_aprobacion_y_modifica_estado()
    {
        $user = User::factory()->create(['role' => 'almacenista']);
        $this->actingAs($user);

        $req = SpareRequest::create([
            'cantidad' => 1,
            'descripcion' => 'Prueba 2',
            'almacenista_id' => $user->id,
            'status' => 'approved',
        ]);

        Livewire::actingAs($user)
            ->test(SolicitudesComponent::class)
            ->assertSee('Confirmar recibido')
            ->call('confirmReceived', $req->id);

        $this->assertDatabaseHas('spare_requests', [
            'id' => $req->id,
            'status' => 'received',
        ]);
    }
}
