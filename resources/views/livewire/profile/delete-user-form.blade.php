<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function disableUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        $user = Auth::user();
        $user->activo = false;
        $user->save();
        $logout($user);
        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Eliminar cuenta
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente. Antes de eliminar tu cuenta, descarga cualquier información que desees conservar.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-disable')"
    >Deshabilitar cuenta</x-danger-button>

    <x-modal name="confirm-user-disable" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="disableUser" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                ¿Estás seguro de que deseas deshabilitar tu cuenta?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Tu cuenta será deshabilitada y no podrás acceder hasta que un administrador la reactive. Ingresa tu contraseña para confirmar que deseas deshabilitar tu cuenta.
            </p>

            <div>
                <x-input-label for="password" value="Contraseña" class="sr-only" />

                <x-text-input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Contraseña"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    Deshabilitar cuenta
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
