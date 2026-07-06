<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $titulo ?? 'Panel Admin — '.config('app.name', 'Capa8Burger') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fuentes: Figtree para texto general, JetBrains Mono para precios y acentos "de codigo" -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|jetbrains-mono:400,600|archivo-black:400&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    {{-- Sin marquee, sin navbar del cliente, sin footer del sitio: el admin entra
         SOLO a gestionar el negocio, no a navegar el menu como un cliente. --}}
    <body class="font-sans antialiased">
        <x-barra-carga />
        <x-loader-navegacion />

        <div class="min-h-screen bg-brand-50">
            <livewire:layout.admin-sidebar />

            {{-- lg:pl-64 deja el espacio exacto del ancho del sidebar fijo (w-64) --}}
            <div class="flex-1 min-w-0 lg:pl-64">
                @if (isset($header))
                    <header class="bg-white border-b-2 border-terminal-950">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
