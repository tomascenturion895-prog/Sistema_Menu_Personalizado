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

        {{-- ============ NAVBAR ============
             Blanca con borde negro: corta con el marquee (negro) y el hero (naranja),
             asi el logo y el CTA "Registrarme" naranja resaltan de verdad --}}
        <nav class="bg-white border-b-2 border-terminal-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <x-application-logo class="text-xl text-terminal-950" />

                <div class="flex items-center gap-3">
                    {{-- El menu es publico: cualquiera puede mirarlo sin registrarse --}}
                    <a href="{{ route('menu.index') }}" class="px-3 py-2 text-sm font-semibold text-terminal-600 hover:text-terminal-950 hover:underline underline-offset-4">
                        Menú
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-retro px-4 py-2 text-sm bg-brand-500 text-white">
                            Ir a mi cuenta
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-semibold text-terminal-600 hover:text-terminal-950 hover:underline underline-offset-4">
                            Iniciar sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-retro px-4 py-2 text-sm bg-brand-500 text-white">
                                Registrarme
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </nav>

        {{-- ============ 1. HERO: quienes somos en una frase y que hacer primero ============ --}}
        <header class="relative overflow-hidden border-b-2 border-terminal-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <div>
                    <p class="font-mono text-sm font-semibold mb-4">$ hamburguesería en Resistencia, Chaco</p>

                    <h1 class="font-display text-6xl sm:text-7xl lg:text-8xl leading-[0.9] uppercase">
                        Tu burger,<br>
                        <span class="text-white" style="text-shadow: 3px 3px 0 #14171b;">tus reglas</span>
                    </h1>

                    <p class="text-terminal-950/80 text-lg mt-6 max-w-md leading-relaxed font-medium">
                        Probá nuestros combos de la casa, o armá tu hamburguesa desde cero:
                        pan, medallones, toppings, salsas y acompañamiento. Vos decidís todo.
                    </p>

                    <div class="flex flex-wrap gap-4 mt-8">
                        {{-- La accion principal es VER EL MENU: es publico, sin registro --}}
                        <a href="{{ route('menu.index') }}"
                            class="btn-retro shadow-retro px-7 py-3.5 bg-terminal-950 text-white">
                            Ver el menú →
                        </a>
                        @guest
                            <a href="{{ Route::has('register') ? route('register') : route('login') }}"
                                class="btn-retro shadow-retro px-7 py-3.5 bg-white">
                                Crear mi cuenta
                            </a>
                        @endguest
                    </div>

                    {{-- Dietas disponibles, con su punto de color identificatorio --}}
                    <div class="flex flex-wrap gap-4 mt-10 text-sm font-medium text-terminal-950/80">
                        <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-dieta-normal border border-terminal-950"></span> Clásicas</span>
                        <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-dieta-vegetariano border border-terminal-950"></span> Vegetarianas</span>
                        <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-dieta-vegano border border-terminal-950"></span> Veganas</span>
                        <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-dieta-celiaco border border-terminal-950"></span> Sin TACC</span>
                    </div>
                </div>

                {{-- La hamburguesa de 8 capas con el sticker de promo --}}
                <div class="relative max-w-md mx-auto w-full">
                    <div class="tarjeta shadow-retro p-8">
                        <x-burger-capas class="max-w-[280px] mx-auto" />
                        <p class="font-mono text-xs text-center text-terminal-500 mt-4">// 8 capas. la última la ponés vos.</p>
                    </div>

                    <div class="absolute -top-5 -right-3 sm:-right-6 rotate-6 bg-cheddar-400 border-2 border-terminal-950 rounded-lg shadow-retro-sm px-4 py-2.5 text-center">
                        <span class="block font-display text-lg leading-none uppercase">100%</span>
                        <span class="block font-mono text-[10px] uppercase tracking-wide mt-0.5">personalizable</span>
                    </div>
                </div>
            </div>
        </header>

        {{-- ============ 2. DESTACADOS: productos reales del menu, lo primero que se vende ============ --}}
        <section class="bg-brand-50 border-b-2 border-terminal-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                    <div>
                        <span class="font-mono text-sm font-semibold text-brand-600">// las más pedidas</span>
                        <h2 class="font-display text-4xl sm:text-5xl uppercase mt-1">Combos de la casa</h2>
                    </div>

                    <a href="{{ route('menu.index') }}" class="font-semibold text-terminal-950 underline underline-offset-4 hover:text-brand-600">
                        Ver el menú completo →
                    </a>
                </div>

                @if ($destacados->isEmpty())
                    {{-- Estado vacio: la landing funciona aunque todavia no haya productos cargados --}}
                    <p class="text-terminal-700">Muy pronto vas a ver acá nuestras hamburguesas destacadas.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($destacados as $producto)
                            <div class="tarjeta shadow-retro p-6 flex flex-col">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-terminal-500">
                                    <span class="w-2 h-2 rounded-full bg-dieta-{{ $producto->categoria->tipo_dieta }} border border-terminal-950"></span>
                                    {{ $producto->categoria->nombre }}
                                </span>

                                <h3 class="font-display text-xl uppercase mt-2">{{ $producto->nombre }}</h3>

                                @if ($producto->descripcion)
                                    <p class="text-sm text-terminal-700 mt-2 flex-1 leading-relaxed">{{ $producto->descripcion }}</p>
                                @endif

                                <div class="mt-5 pt-4 border-t-2 border-gray-100 flex items-center justify-between">
                                    <span class="precio text-xl">${{ number_format($producto->precio, 0, ',', '.') }}</span>

                                    <a href="{{ route('menu.personalizar', $producto) }}" class="btn-retro px-4 py-2 bg-brand-500 text-white text-sm">
                                        La quiero
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- ============ 3. COMO FUNCIONA ============ --}}
        <section class="bg-white border-b-2 border-terminal-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <span class="font-mono text-sm font-semibold text-brand-600">// cómo funciona</span>
                <h2 class="font-display text-4xl sm:text-5xl uppercase mt-1 mb-10">En 3 pasos</h2>

                {{-- Numeracion justificada: es la secuencia real que el cliente sigue en orden --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="tarjeta shadow-retro p-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 font-display bg-brand-500 text-white border-2 border-terminal-950 rounded-md">1</span>
                        <h3 class="font-display text-xl uppercase mt-4">Mirá el menú</h3>
                        <p class="text-terminal-700 mt-2 text-sm leading-relaxed">
                            Sin registrarte. Contanos qué buscás (carne, veggie, vegano o sin TACC)
                            y te mostramos solo lo que va con vos.
                        </p>
                    </div>
                    <div class="tarjeta shadow-retro p-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 font-display bg-brand-500 text-white border-2 border-terminal-950 rounded-md">2</span>
                        <h3 class="font-display text-xl uppercase mt-4">Elegí o personalizá</h3>
                        <p class="text-terminal-700 mt-2 text-sm leading-relaxed">
                            Pedí un combo de la casa tal como viene, o armá el tuyo capa por capa:
                            hasta 5 toppings, 3 salsas, papas y bebida. El precio se actualiza al instante.
                        </p>
                    </div>
                    <div class="tarjeta shadow-retro p-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 font-display bg-brand-500 text-white border-2 border-terminal-950 rounded-md">3</span>
                        <h3 class="font-display text-xl uppercase mt-4">Creá tu cuenta y pedí</h3>
                        <p class="text-terminal-700 mt-2 text-sm leading-relaxed">
                            Al confirmar tu pedido te pedimos registrarte (una sola vez).
                            Después seguís el estado hasta que esté lista para retirar.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ 4. QUIENES SOMOS ============ --}}
        <section class="bg-brand-50 border-b-2 border-terminal-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                <div>
                    <span class="font-mono text-sm font-semibold text-brand-600">// quiénes somos</span>
                    <h2 class="font-display text-4xl sm:text-5xl uppercase mt-1 mb-6">Cocina y código</h2>

                    <p class="text-terminal-700 leading-relaxed">
                        Capa8Burger nace de dos pasiones: las hamburguesas a la parrilla y la programación.
                        En el modelo OSI, la "capa 8" es el usuario — y acá el usuario manda:
                        cada hamburguesa se construye por capas y todas las decisiones son tuyas.
                    </p>
                    <p class="text-terminal-700 leading-relaxed mt-4">
                        Trabajamos con opciones reales para todas las dietas: medallones de carne,
                        de garbanzos, de lentejas y plant based, pan común, integral y sin TACC.
                        Nadie se queda sin su burger.
                    </p>
                </div>

                <div class="tarjeta shadow-retro p-8">
                    <p class="font-mono text-sm text-terminal-500 leading-loose">
                        <span class="text-brand-600">$</span> nuestra_receta<br>
                        &gt; ingredientes_frescos: <span class="text-exito-700">true</span><br>
                        &gt; medallones_a_la_parrilla: <span class="text-exito-700">true</span><br>
                        &gt; opciones_para_todos: <span class="text-exito-700">true</span><br>
                        &gt; decisiones_del_cliente: <span class="text-brand-600">100%</span><br>
                        <span class="text-brand-600">$</span> <span class="logo-cursor">_</span>
                    </p>
                </div>
            </div>
        </section>

        {{-- ============ 5. CONTACTO Y UBICACION ============ --}}
        <section class="bg-white border-b-2 border-terminal-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <span class="font-mono text-sm font-semibold text-brand-600">// dónde encontrarnos</span>
                <h2 class="font-display text-4xl sm:text-5xl uppercase mt-1 mb-10">Vení a probarla</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="tarjeta shadow-retro-sm p-6">
                        <h3 class="font-semibold text-terminal-950">Ubicación</h3>
                        <p class="text-terminal-700 text-sm mt-2 leading-relaxed">
                            Av. Sarmiento 1234<br>
                            Resistencia, Chaco<br>
                            Argentina
                        </p>
                    </div>

                    <div class="tarjeta shadow-retro-sm p-6">
                        <h3 class="font-semibold text-terminal-950">Horarios</h3>
                        <p class="text-terminal-700 text-sm mt-2 leading-relaxed">
                            Martes a domingo<br>
                            19:30 — 00:30 hs<br>
                            <span class="font-mono text-xs text-terminal-500">// lunes deploy... digo, cerrado</span>
                        </p>
                    </div>

                    <div class="tarjeta shadow-retro-sm p-6">
                        <h3 class="font-semibold text-terminal-950">Contacto</h3>
                        <p class="text-terminal-700 text-sm mt-2 leading-relaxed">
                            Tel: (0362) 400-8080<br>
                            hola@capa8burger.com<br>
                            Instagram: @capa8burger
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ 6. CTA FINAL + FOOTER ============ --}}
        <section class="bg-terminal-950 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
                <p class="font-mono text-sm text-brand-400">// en el modelo OSI la capa 8 es el usuario</p>
                <h2 class="font-display text-4xl sm:text-6xl uppercase mt-3">Acá, la capa 8 <span class="text-brand-500">sos vos</span></h2>

                <a href="{{ route('menu.index') }}"
                    class="inline-block mt-8 px-8 py-4 bg-brand-500 text-white font-semibold rounded-md border-2 border-white/20 hover:bg-brand-600 transition">
                    Ver el menú →
                </a>
            </div>

            {{-- Wordmark gigante recortado, como cierre visual de la pagina --}}
            <div class="overflow-hidden select-none" aria-hidden="true">
                <p class="font-display uppercase text-center text-[17vw] leading-[0.75] text-terminal-900 -mb-[4vw]">capa8</p>
            </div>

            <div class="border-t border-terminal-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <x-application-logo class="text-base text-white" />
                    <p class="font-mono text-xs text-terminal-500">© {{ date('Y') }} Capa8Burger — Av. Sarmiento 1234, Resistencia, Chaco — hecho con Laravel + Livewire</p>
                </div>
            </div>
        </section>
    </body>
</html>
