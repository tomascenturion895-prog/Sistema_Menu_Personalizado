<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>💰</text></svg>">
    <!-- Fuentes: Figtree para texto general, JetBrains Mono para precios y acentos "de codigo" -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|jetbrains-mono:400,600|archivo-black:400&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{-- Fondo naranja de marca con la tarjeta blanca estilo cartel retro (borde negro + sombra dura) --}}

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-brand-500 px-4">
        <div>
            <a href="/" wire:navigate>
                <x-application-logo variant="onbrand" class="text-2xl text-terminal-950" />
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white border-2 border-terminal-950 shadow-retro overflow-hidden rounded-xl">
            {{ $slot }}
        </div>

        <p class="font-mono text-xs text-terminal-950/70 mt-6 mb-8">// tu burger, tus reglas</p>
    </div>
</body>

</html>