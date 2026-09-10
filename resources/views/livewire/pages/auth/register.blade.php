<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'almacenista';
    public bool $success = false;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'in:administrador,superadministrador,gerente,proveedor,almacenista'],
            // Exigir al menos una letra mayúscula y un carácter especial además de las reglas por defecto
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults(), 'regex:/[A-Z]/', 'regex:/[^A-Za-z0-9]/'],
        ], [
            'password.regex' => 'La contraseña debe contener al menos una letra mayúscula y un carácter especial.',
        ]);

        if (in_array($validated['role'], ['administrador', 'superadministrador'], true) && !(auth()->user() && auth()->user()->role === 'superadministrador')) {
            $this->addError('role', 'No tienes permiso para asignar el rol seleccionado.');
            return;
        }

        // Limitar por configuración el número máximo de administradores activos
        if ($validated['role'] === 'administrador') {
            $limit = (int) \App\Models\Setting::getValue('max_administradores', 2);
            $adminCount = User::where('role', 'administrador')->where('activo', true)->count();
            if ($adminCount >= $limit) {
                $this->addError('role', "No se pueden crear más de {$limit} usuarios con rol administrador activos.");
                return;
            }
        }

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        $this->success = true;
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'role']);
    }
}; ?>

<div>
    @if($success)
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="mb-4 p-2 rounded bg-green-100 text-green-800 text-center font-semibold">
            Usuario registrado correctamente.
        </div>
    @endif
    <!-- El formulario completo ya está abajo, este bloque se elimina -->
    <script>
        document.addEventListener('livewire:init', function() {
            Livewire.on('register-success', function() {
                var msg = document.getElementById('register-success-message');
                msg.textContent = 'Usuario registrado correctamente.';
                msg.style.display = 'block';
                setTimeout(function() { msg.style.display = 'none'; }, 2500);
            });
        });
    </script>
    <form wire:submit="register" class="max-w-3xl mx-auto bg-white p-8 rounded shadow-md">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nombre')" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <span class="material-icons text-gray-400">person</span>
                </span>
                <x-text-input wire:model="name" id="name" class="block mt-1 w-full pl-10" type="text" name="name" required autofocus autocomplete="name" maxlength="50" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <span class="material-icons text-gray-400">email</span>
                </span>
                <x-text-input wire:model="email" id="email" class="block mt-1 w-full pl-10" type="email" name="email" required autocomplete="username" maxlength="80" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Rol -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Rol')" />
            <select wire:model="role" id="role" name="role" class="block mt-1 w-full rounded border-gray-300">
                @if(auth()->user() && auth()->user()->role === 'superadministrador')
                    <option value="administrador">Administrador</option>
                    <option value="superadministrador">Superadministrador</option>
                @endif
                <option value="gerente">Gerente</option>
                <option value="proveedor">Proveedor</option>
                <option value="almacenista">Almacenista</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4" x-data="{ show: false }">
            <x-input-label for="password" :value="__('Contraseña')" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <span class="material-icons text-gray-400">vpn_key</span>
                </span>
                <input wire:model="password" id="password" class="block mt-1 w-full pl-10 pr-10 rounded-lg"
              :type="show ? 'text' : 'password'"
              name="password"
              required autocomplete="new-password" maxlength="70" />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 focus:outline-none" tabindex="-1">
                    <span class="material-icons" x-text="show ? 'visibility_off' : 'visibility'"></span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4" x-data="{ show: false }">
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <span class="material-icons text-gray-400">check_circle</span>
                </span>
                <input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full pl-10 pr-10 rounded-lg"
              :type="show ? 'text' : 'password'"
              name="password_confirmation" required autocomplete="new-password" maxlength="70" />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 focus:outline-none" tabindex="-1">
                    <span class="material-icons" x-text="show ? 'visibility_off' : 'visibility'"></span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            @guest
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                    {{ __('¿Ya tienes una cuenta?') }}
                </a>
            @endguest
            <x-primary-button class="ms-4">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>
    </form>
</div>
