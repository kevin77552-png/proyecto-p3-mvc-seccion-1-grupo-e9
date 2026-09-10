<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Token CSRF para proteger el formulario -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} | Login</title>

        <!-- Fuente principal e iconos -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        <!-- Assets compilados con Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-100">
        <!-- Vista de login: formulario simple, sin lógica embebida -->
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Logo que redirige al login -->
            <div>
                <a href="{{ route('login') }}">
                    <x-application-logo class="w-20 h-20 fill-current text-white" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 shadow-md overflow-hidden sm:rounded-lg" style="background-color: #102857;">
                <div class="mb-6 text-center text-white">
                    <h1 class="text-2xl font-semibold">Iniciar sesión</h1>
                    <p class="text-sm mt-1">Accede a tu cuenta con correo y contraseña.</p>
                </div>

                <!-- Muestra errores de validación si los hay -->
                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4" onsubmit="showLoginSuccessAlert();">
                    @csrf

                    <!-- Campo de email: repite el valor si falla la validación -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-white">Correo electrónico</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>

                    <!-- Campo de contraseña -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-white">Contraseña</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10" />
                            <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none" tabindex="-1" aria-label="Mostrar contraseña">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Checkbox de "recuérdame" y link para recuperar contraseña -->
                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('remember') ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-white">Recuérdame</span>
                        </label>

                        <a href="{{ route('password.request') }}" class="text-sm text-white hover:text-gray-200">¿Olvidaste tu contraseña?</a>
                    </div>

                    <!-- Botón de envío -->
                    <div>
                        <button type="submit" class="w-full inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Iniciar sesión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <script>
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            button.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
            button.querySelector('svg').innerHTML = isPassword
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.253-3.722m2.649-2.26A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.043 5.222M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        }

        function showLoginSuccessAlert() {
            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Bienvenido!',
                    text: 'Has iniciado sesión correctamente.',
                    timer: 1800,
                    showConfirmButton: false,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                });
            }
        }
    </script>
    </body>
</html>
