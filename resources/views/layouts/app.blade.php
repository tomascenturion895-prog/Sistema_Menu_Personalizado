<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- $titulo y $meta son slots OPCIONALES: si una vista no los pasa, caen al
             titulo generico. La pagina de inicio los usa para su propio SEO
             (description, Open Graph) sin necesitar un <head> propio duplicado. --}}
        <title>{{ $titulo ?? config('app.name', 'Capa8Burger') }}</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        {{ $meta ?? '' }}

        <!-- Fuentes: Figtree para texto general, JetBrains Mono para precios y acentos "de codigo" -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|jetbrains-mono:400,600|archivo-black:400&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    {{-- Fondo crema calido (brand-50), mas acorde a la marca que el gris default de Breeze --}}
    <body class="font-sans antialiased">
        <x-barra-carga />
        <x-loader-navegacion />

        <div class="min-h-screen bg-brand-50">
            {{-- Cinta de marca, la misma que abre la landing. Solo en las paginas del cliente:
                 en el back-office del admin la animacion permanente distrae del trabajo --}}
            @unless (request()->routeIs('admin.*'))
                <x-marquee />
            @endunless

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

            {{-- Footer con info del negocio: sin esto, un usuario logueado no tenia
                 forma de volver a ver direccion/horario/contacto. Se omite en el
                 admin (zona de trabajo) y en "home"/"dashboard" porque esa pagina
                 ya incluye su propia seccion completa de contacto y ubicacion --}}
            @unless (request()->routeIs('admin.*') || request()->routeIs('home') || request()->routeIs('dashboard'))
                <x-footer-sitio />
            @endunless
        </div>
    </body>
</html>
