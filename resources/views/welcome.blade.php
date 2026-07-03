<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Capa8Burger — Tu burger, tus reglas</title>

        {{-- Fuentes de la marca: Archivo Black (display), Figtree (texto), JetBrains Mono (acentos) --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|jetbrains-mono:400,600|archivo-black:400&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-terminal-950 text-white">

        {{-- ============ NAVBAR ============ --}}
        <nav class="border-b border-terminal-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <x-application-logo class="text-xl text-white" />

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm font-semibold bg-brand-500 hover:bg-brand-600 rounded-md transition">
                            Ir a mi cuenta
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-terminal-300 hover:text-white transition">
                            Iniciar sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold bg-brand-500 hover:bg-brand-600 rounded-md transition">
                                Registrarme
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </nav>

        {{-- ============ HERO ============ --}}
        <header class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                {{-- Columna izquierda: mensaje principal --}}
                <div>
                    <p class="font-mono text-sm text-brand-400 mb-4">$ capa8burger --init</p>

                    <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl leading-none uppercase">
                        Tu burger,<br>
                        <span class="text-brand-500">tus reglas</span>
                    </h1>

                    <p class="text-terminal-300 text-lg mt-6 max-w-md leading-relaxed">
                        Cada hamburguesa se construye por capas, y acá las definís todas vos:
                        el pan, los medallones, los toppings, las salsas y el acompañamiento.
                    </p>

                    <div class="flex flex-wrap gap-3 mt-8">
                        <a href="{{ Route::has('register') ? route('register') : route('login') }}"
                            class="px-6 py-3 bg-brand-500 hover:bg-brand-600 text-white font-semibold rounded-md transition">
                            Pedí la tuya →
                        </a>
                        <a href="{{ route('login') }}"
                            class="px-6 py-3 border border-terminal-700 hover:border-terminal-500 text-terminal-200 font-semibold rounded-md transition">
                            Ver el menú
                        </a>
                    </div>

                    {{-- Dietas disponibles, con su punto de color identificatorio --}}
                    <div class="flex flex-wrap gap-4 mt-10 text-sm text-terminal-400">
                        <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-dieta-normal"></span> Clásicas</span>
                        <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-dieta-vegetariano"></span> Vegetarianas</span>
                        <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-dieta-vegano"></span> Veganas</span>
                        <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-dieta-celiaco"></span> Sin TACC</span>
                    </div>
                </div>

                {{-- Columna derecha: la hamburguesa de 8 capas con su "stack" listado al costado --}}
                <div class="grid grid-cols-[1fr_auto] gap-6 items-center max-w-lg mx-auto w-full">
                    <x-burger-capas class="max-w-xs mx-auto" />

                    {{-- Las capas listadas como si fueran un archivo de configuracion --}}
                    <ol class="font-mono text-xs sm:text-sm text-terminal-400 space-y-2.5 select-none">
                        <li><span class="text-terminal-600">1:</span> pan_superior</li>
                        <li><span class="text-terminal-600">2:</span> <span class="text-cheddar-400">salsa</span></li>
                        <li><span class="text-terminal-600">3:</span> <span class="text-tomate-500">tomate</span></li>
                        <li><span class="text-terminal-600">4:</span> <span class="text-dieta-vegetariano">lechuga</span></li>
                        <li><span class="text-terminal-600">5:</span> <span class="text-cheddar-400">cheddar</span></li>
                        <li><span class="text-terminal-600">6:</span> medallon</li>
                        <li><span class="text-terminal-600">7:</span> <span class="text-purple-400">cebolla</span></li>
                        <li><span class="text-terminal-600">8:</span> pan_inferior</li>
                    </ol>
                </div>
            </div>

            {{-- El chiste de la marca, como comentario de codigo --}}
            <p class="font-mono text-sm text-terminal-500 mt-12 text-center">
                // en el modelo OSI la capa 8 es el usuario. acá, la capa 8 <span class="text-brand-400">sos vos</span>.
            </p>
        </header>

        {{-- ============ COMO FUNCIONA ============ --}}
        <section class="border-t border-terminal-800 bg-terminal-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <span class="eyebrow">// cómo funciona</span>
                <h2 class="font-display text-3xl uppercase mb-10">En 3 pasos</h2>

                {{-- Numeracion justificada: es la secuencia real que el cliente sigue en orden --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <p class="font-mono text-brand-500 text-sm mb-2">paso_1</p>
                        <h3 class="font-semibold text-lg text-white">Elegí la base</h3>
                        <p class="text-terminal-400 mt-2 text-sm leading-relaxed">
                            Arrancá con una de nuestras hamburguesas del menú, filtrado por tu dieta:
                            clásica, vegetariana, vegana o sin TACC.
                        </p>
                    </div>
                    <div>
                        <p class="font-mono text-brand-500 text-sm mb-2">paso_2</p>
                        <h3 class="font-semibold text-lg text-white">Personalizá cada capa</h3>
                        <p class="text-terminal-400 mt-2 text-sm leading-relaxed">
                            Cambiá el pan, sumá medallones, elegí toppings y salsas, y completá
                            con papas y bebida. El precio se actualiza en el momento.
                        </p>
                    </div>
                    <div>
                        <p class="font-mono text-brand-500 text-sm mb-2">paso_3</p>
                        <h3 class="font-semibold text-lg text-white">Confirmá y listo</h3>
                        <p class="text-terminal-400 mt-2 text-sm leading-relaxed">
                            Revisá tu pedido, dejanos observaciones para la cocina y confirmá.
                            Nosotros nos encargamos del deploy... digo, de la parrilla.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ FOOTER ============ --}}
        <footer class="border-t border-terminal-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <x-application-logo class="text-base text-white" />
                <p class="font-mono text-xs text-terminal-500">© {{ date('Y') }} Capa8Burger — hecho con Laravel + Livewire</p>
            </div>
        </footer>
    </body>
</html>
