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
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <!-- Card del formulario -->
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 shadow-md overflow-hidden sm:rounded-lg" style="background-color: #233861;">
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

                <!-- Formulario de login -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Campo de email: repite el valor si falla la validación -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-white">Correo electrónico</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>

                    <!-- Campo de contraseña -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-white">Contraseña</label>
                        <input id="password" name="password" type="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
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
    </body>
</html>
