<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fuentes: Figtree para texto general, JetBrains Mono para precios y acentos "de codigo" -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|jetbrains-mono:400,600|archivo-black:400&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    {{-- Fondo crema calido (brand-50), mas acorde a la marca que el gris default de Breeze --}}
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-brand-50">
            {{-- Cinta de marca, la misma que abre la landing: mantiene la identidad dentro de la app --}}
            <x-marquee />

            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white border-b-2 border-terminal-950">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
