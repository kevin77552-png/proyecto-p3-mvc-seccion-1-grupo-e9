<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                // Exigir al menos una letra mayúscula y un carácter especial además de las reglas por defecto
                'password' => ['required', 'string', Password::defaults(), 'confirmed', 'regex:/[A-Z]/', 'regex:/[^A-Za-z0-9]/'],
            ], [
                'password.regex' => 'La contraseña debe contener al menos una letra mayúscula y un carácter especial.',
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
    <h2 class="text-lg font-medium text-gray-900">
            Actualizar contraseña
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Asegúrate de usar una contraseña larga y segura para proteger tu cuenta.
        </p>
    </header>

    <form wire:submit="updatePassword" class="space-y-6">
        <div>
            <x-input-label for="update_password_current_password" :value="'Contraseña actual'" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <span class="material-icons text-gray-400">lock</span>
                </span>
                <x-text-input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full pl-10" autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="'Nueva contraseña'" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <span class="material-icons text-gray-400">vpn_key</span>
                </span>
                <x-text-input wire:model="password" id="update_password_password" name="password" type="password" class="mt-1 block w-full pl-10" autocomplete="new-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="'Confirmar contraseña'" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <span class="material-icons text-gray-400">check_circle</span>
                </span>
                <x-text-input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full pl-10" autocomplete="new-password" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Guardar</x-primary-button>

            <x-action-message class="me-3" on="password-updated">
                Guardado.
            </x-action-message>
        </div>
    </form>
</section>
