<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->dispatch('login-success');
        // La redirección se hará desde JS tras el SweetAlert
    }
}; ?>

<div>
    <script>
        function setupLoginSuccessSwal() {
            Livewire.on('login-success', () => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Bienvenido!',
                    text: 'Has iniciado sesión correctamente.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "{{ route('dashboard') }}";
                });
            });
        }
        document.addEventListener('livewire:init', setupLoginSuccessSwal);
        document.addEventListener('livewire:load', setupLoginSuccessSwal);
        document.addEventListener('livewire:navigated', setupLoginSuccessSwal);
    </script>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <span class="material-icons text-gray-400">email</span>
                </span>
                <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full pl-10 rounded-lg" type="email" name="email" required autofocus autocomplete="username" maxlength="120" />
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4" x-data="{ show: false }">
            <x-input-label for="password" :value="__('Contraseña')" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <span class="material-icons text-gray-400">lock</span>
                </span>
                <input wire:model="form.password" id="password" class="block mt-1 w-full pl-10 pr-10 rounded-lg"
                       :type="show ? 'text' : 'password'"
                       name="password"
                       required autocomplete="current-password" minlength="8" maxlength="64" />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none" tabindex="-1" :aria-label="show ? 'Ocultar contraseña' : 'Mostrar contraseña'">
                    <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.253-3.722m2.649-2.26A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.043 5.222M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me 
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Recuérdame') }}</span>
            </label>
        </div>-->

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Iniciar sesión') }}
            </x-primary-button>
        </div>
    </form>
</div>
