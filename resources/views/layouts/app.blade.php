<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <livewire:layout.navigation />
            <div x-data="{ open: true }" class="flex relative">
                <div :class="open ? 'block' : 'hidden'" class="transition-all duration-300 ease-in-out" style="will-change: width;">
                    <livewire:sidebar />
                </div>
                <!-- Botón siempre visible -->
                <button @click="open = !open" class="fixed top-6 left-4 z-50 bg-gray-200 text-gray-700 rounded-full p-1 shadow hover:bg-gray-300 focus:outline-none transition-transform duration-300"
                    x-show="!open"
                    style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div class="flex-1">
                    <!-- Page Heading -->
                    @if (isset($header))
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endif
                    <!-- Page Content -->
                    <main>
                        <livewire:dynamic-module />
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
