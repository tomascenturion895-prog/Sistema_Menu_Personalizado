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
    <body class="font-sans antialiased bg-brand-500 text-terminal-950">

        {{-- Cinta marquee superior (componente compartido con el layout de la app) --}}
        <x-marquee />

        {{-- ============ NAVBAR ============ --}}
        <nav class="border-b-2 border-terminal-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <x-application-logo variant="onbrand" class="text-xl text-terminal-950" />

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="btn-retro px-4 py-2 text-sm bg-white">
                            Ir a mi cuenta
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold hover:underline underline-offset-4">
                            Iniciar sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="btn-retro px-4 py-2 text-sm bg-white">
                                Registrarme
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </nav>

        {{-- ============ HERO ============ --}}
        <header class="relative overflow-hidden border-b-2 border-terminal-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                {{-- Columna izquierda: mensaje principal --}}
                <div>
                    <p class="font-mono text-sm font-semibold mb-4">$ sumate a la revolución del sabor</p>

                    <h1 class="font-display text-6xl sm:text-7xl lg:text-8xl leading-[0.9] uppercase">
                        Tu burger,<br>
                        <span class="text-white" style="text-shadow: 3px 3px 0 #14171b;">tus reglas</span>
                    </h1>

                    <p class="text-terminal-950/80 text-lg mt-6 max-w-md leading-relaxed font-medium">
                        Combos de la casa o armada desde cero, capa por capa:
                        pan, medallones, toppings, salsas y acompañamiento. Vos decidís todo.
                    </p>

                    <div class="flex flex-wrap gap-4 mt-8">
                        <a href="{{ Route::has('register') ? route('register') : route('login') }}"
                            class="btn-retro shadow-retro px-7 py-3.5 bg-terminal-950 text-white">
                            Pedí la tuya →
                        </a>
                        <a href="{{ route('login') }}"
                            class="btn-retro shadow-retro px-7 py-3.5 bg-white">
                            Ver el menú
                        </a>
                    </div>
                </div>

                {{-- Columna derecha: la hamburguesa de 8 capas con el sticker de promo --}}
                <div class="relative max-w-md mx-auto w-full">
                    {{-- Panel blanco tipo cartel, con la hamburguesa adentro --}}
                    <div class="tarjeta shadow-retro p-8">
                        <x-burger-capas class="max-w-[280px] mx-auto" />
                        <p class="font-mono text-xs text-center text-terminal-500 mt-4">// 8 capas. la última la ponés vos.</p>
                    </div>

                    {{-- Sticker rotado, como los carteles de oferta de los locales --}}
                    <div class="absolute -top-5 -right-3 sm:-right-6 rotate-6 bg-cheddar-400 border-2 border-terminal-950 rounded-lg shadow-retro-sm px-4 py-2.5 text-center">
                        <span class="block font-display text-lg leading-none uppercase">100%</span>
                        <span class="block font-mono text-[10px] uppercase tracking-wide mt-0.5">personalizable</span>
                    </div>
                </div>
            </div>
        </header>

        {{-- ============ COMO FUNCIONA ============ --}}
        <section class="bg-brand-50 border-b-2 border-terminal-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <span class="font-mono text-sm font-semibold text-brand-600">// cómo funciona</span>
                <h2 class="font-display text-4xl sm:text-5xl uppercase mt-1 mb-10">En 3 pasos</h2>

                {{-- Numeracion justificada: es la secuencia real que el cliente sigue en orden --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="tarjeta shadow-retro p-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 font-display bg-brand-500 text-white border-2 border-terminal-950 rounded-md">1</span>
                        <h3 class="font-display text-xl uppercase mt-4">Elegí la base</h3>
                        <p class="text-terminal-700 mt-2 text-sm leading-relaxed">
                            Contanos qué buscás (carne, veggie, vegano o sin TACC) y te mostramos
                            solo lo que va con vos: combos de la casa listos para pedir.
                        </p>
                    </div>
                    <div class="tarjeta shadow-retro p-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 font-display bg-brand-500 text-white border-2 border-terminal-950 rounded-md">2</span>
                        <h3 class="font-display text-xl uppercase mt-4">Personalizá las capas</h3>
                        <p class="text-terminal-700 mt-2 text-sm leading-relaxed">
                            Cambiá el pan, sumá medallones, elegí hasta 5 toppings y 3 salsas,
                            y completá con papas y bebida. El precio se actualiza al instante.
                        </p>
                    </div>
                    <div class="tarjeta shadow-retro p-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 font-display bg-brand-500 text-white border-2 border-terminal-950 rounded-md">3</span>
                        <h3 class="font-display text-xl uppercase mt-4">Confirmá y listo</h3>
                        <p class="text-terminal-700 mt-2 text-sm leading-relaxed">
                            Revisá tu pedido, dejale una nota a la cocina si querés,
                            y seguí el estado hasta que esté lista para retirar.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ DIETAS ============ --}}
        <section class="bg-white border-b-2 border-terminal-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <div class="grid grid-cols-1 lg:grid-cols-[auto_1fr] gap-8 items-center">
                    <h2 class="font-display text-3xl sm:text-4xl uppercase max-w-xs leading-tight">Hay una burger para vos</h2>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ([
                            ['nombre' => 'Clásicas', 'detalle' => 'Carne a la parrilla', 'color' => 'bg-dieta-normal'],
                            ['nombre' => 'Vegetarianas', 'detalle' => 'Sin carne, con sabor', 'color' => 'bg-dieta-vegetariano'],
                            ['nombre' => 'Veganas', 'detalle' => '100% plantas', 'color' => 'bg-dieta-vegano'],
                            ['nombre' => 'Sin TACC', 'detalle' => 'Aptas celíacos', 'color' => 'bg-dieta-celiaco'],
                        ] as $dieta)
                            <div class="border-2 border-terminal-950 rounded-xl p-4 shadow-retro-sm">
                                <span class="block w-3 h-3 rounded-full {{ $dieta['color'] }} border border-terminal-950"></span>
                                <p class="font-semibold mt-2">{{ $dieta['nombre'] }}</p>
                                <p class="text-sm text-terminal-500">{{ $dieta['detalle'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ CTA FINAL ============ --}}
        <section class="bg-terminal-950 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
                <p class="font-mono text-sm text-brand-400">// en el modelo OSI la capa 8 es el usuario</p>
                <h2 class="font-display text-4xl sm:text-6xl uppercase mt-3">Acá, la capa 8 <span class="text-brand-500">sos vos</span></h2>

                <a href="{{ Route::has('register') ? route('register') : route('login') }}"
                    class="inline-block mt-8 px-8 py-4 bg-brand-500 text-white font-semibold rounded-md border-2 border-white/20 hover:bg-brand-600 transition">
                    Crear mi cuenta →
                </a>
            </div>

            {{-- Wordmark gigante recortado, como cierre visual de la pagina --}}
            <div class="overflow-hidden select-none" aria-hidden="true">
                <p class="font-display uppercase text-center text-[17vw] leading-[0.75] text-terminal-900 -mb-[4vw]">capa8</p>
            </div>

            <div class="border-t border-terminal-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <x-application-logo class="text-base text-white" />
                    <p class="font-mono text-xs text-terminal-500">© {{ date('Y') }} Capa8Burger — hecho con Laravel + Livewire</p>
                </div>
            </div>
        </section>
    </body>
</html>
