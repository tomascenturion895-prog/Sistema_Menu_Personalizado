{{-- Pagina de inicio UNICA: se sirve tanto en "/" (publica) como en "/inicio"
     (autenticada) para que un visitante y un cliente logueado vean EXACTAMENTE
     lo mismo. Antes eran dos vistas distintas (landing vs dashboard personal)
     con navbar y contenido diferentes; unificarlas evita esa desorientacion.
     Usa x-app-layout: la MISMA navbar de toda la app, sin una barra propia. --}}
<x-app-layout>
    <x-slot name="titulo">Capa8Burger — Tu burger, tus reglas</x-slot>
    <x-slot name="meta">
        <meta name="description" content="Hamburguesería en {{ config('negocio.ciudad') }}. Combos de la casa o armá tu hamburguesa 100% personalizada: opciones clásicas, vegetarianas, veganas y sin TACC.">
        <meta property="og:title" content="Capa8Burger — Tu burger, tus reglas">
        <meta property="og:description" content="Combos de la casa o armá tu hamburguesa capa por capa. Opciones para todas las dietas.">
        <meta property="og:type" content="website">
    </x-slot>

    {{-- ============ HERO ============
         Fondo brand-500 (el mismo naranja ya suavizado que usa el panel de
         preferencia de /menu): el hero recupera color propio y distinto de la
         seccion "las mas pedidas" de abajo (que usa brand-50, mucho mas clara),
         asi no pierde jerarquia. Como el fondo ya es naranja, "tus reglas" pasa
         a blanco (si fuera brand-500 sobre brand-500 desaparecería). --}}
    <header class="relative overflow-hidden border-b-2 border-terminal-950 bg-brand-500 text-terminal-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">

            <div>
                <p class="font-mono text-sm font-semibold mb-4">$ hamburguesería en {{ config('negocio.ciudad') }}</p>

                <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl leading-[0.95] uppercase">
                    Tu burger,<br>
                    <span class="text-white" style="text-shadow: 1px 1px 0 #14171b;">tus reglas</span>
                </h1>

                <p class="text-terminal-950/80 text-lg mt-5 max-w-md leading-relaxed font-medium">
                    Probá nuestros combos de la casa, o armá tu hamburguesa desde cero:
                    pan, medallones, toppings, salsas y acompañamiento. Vos decidís todo.
                </p>

                {{-- Lo mejor del dashboard de cliente: el estado del ultimo pedido,
                     mostrado como un dato contextual (no como una seccion nueva) --}}
                @auth
                    @if ($ultimoPedido)
                        <a href="{{ route('cliente.pedidos.show', $ultimoPedido) }}" wire:navigate
                            class="btn-retro inline-flex mt-6 px-4 py-2 bg-white text-sm">
                            Tu último pedido {{ $ultimoPedido->numero }} está
                            <span class="font-semibold text-brand-600 ml-1">{{ strtolower(\App\Models\Pedido::ESTADOS[$ultimoPedido->estado] ?? $ultimoPedido->estado) }}</span>
                            <span class="ml-1">→</span>
                        </a>
                    @endif
                @endauth

                <div class="flex flex-wrap gap-4 mt-6">
                    {{-- La accion principal es VER EL MENU: es publico, sin registro --}}
                    <a href="{{ route('menu.index') }}"
                        class="btn-retro shadow-retro px-7 py-3.5 bg-terminal-950 text-white">
                        Ver el menú →
                    </a>
                    {{-- Solo a un visitante sin cuenta se le ofrece registrarse aca --}}
                    @guest
                        <a href="{{ Route::has('register') ? route('register') : route('login') }}"
                            class="btn-retro shadow-retro px-7 py-3.5 bg-white">
                            Crear mi cuenta
                        </a>
                    @endguest
                </div>

                {{-- Dietas disponibles, con su punto de color identificatorio --}}
                <div class="flex flex-wrap gap-4 mt-8 text-sm font-medium text-terminal-950/80">
                    @foreach (['normal' => 'Clásicas', 'vegetariano' => 'Vegetarianas', 'vegano' => 'Veganas', 'celiaco' => 'Sin TACC'] as $dieta => $etiqueta)
                        <span class="inline-flex items-center gap-2"><x-punto-dieta :dieta="$dieta" /> {{ $etiqueta }}</span>
                    @endforeach
                </div>
            </div>

            {{-- La hamburguesa de 8 capas con el sticker de promo --}}
            <div class="relative max-w-sm mx-auto w-full">
                <div class="tarjeta shadow-retro p-6">
                    <x-burger-capas class="max-w-[240px] mx-auto" />
                    <p class="font-mono text-xs text-center text-terminal-500 mt-4">// 8 capas. la última la ponés vos.</p>
                </div>

                <div class="absolute -top-5 -right-3 sm:-right-6 rotate-6 bg-cheddar-400 border-2 border-terminal-950 rounded-lg shadow-retro-sm px-4 py-2.5 text-center">
                    <span class="block font-display text-lg leading-none uppercase">100%</span>
                    <span class="block font-mono text-[10px] uppercase tracking-wide mt-0.5">personalizable</span>
                </div>
            </div>
        </div>
    </header>

    {{-- ============ DESTACADOS: productos reales del menu ============ --}}
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
                {{-- Estado vacio: la pagina funciona aunque todavia no haya productos cargados --}}
                <p class="text-terminal-700">Muy pronto vas a ver acá nuestras hamburguesas destacadas.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($destacados as $producto)
                        <div class="tarjeta shadow-retro overflow-hidden flex flex-col">
                            <x-foto-producto :producto="$producto" />

                            <div class="p-6 flex flex-col flex-1">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-terminal-500">
                                    <x-punto-dieta :dieta="$producto->categoria->tipo_dieta" />
                                    {{ $producto->categoria->nombre }}
                                </span>

                                <h3 class="font-display text-xl uppercase mt-2">{{ $producto->nombre }}</h3>

                                @if ($producto->descripcion)
                                    <p class="text-sm text-terminal-700 mt-2 flex-1 leading-relaxed">{{ $producto->descripcion }}</p>
                                @endif

                                <div class="mt-5 pt-4 border-t-2 border-gray-100 flex items-center justify-between">
                                    <span class="precio text-xl">@precio($producto->precio)</span>

                                    <a href="{{ route('menu.personalizar', $producto) }}" class="btn-retro px-4 py-2 bg-brand-500 text-terminal-950 text-sm">
                                        La quiero
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ============ COMO FUNCIONA ============ --}}
    <section class="bg-white border-b-2 border-terminal-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <span class="font-mono text-sm font-semibold text-brand-600">// cómo funciona</span>
            <h2 class="font-display text-4xl sm:text-5xl uppercase mt-1 mb-10">En 3 pasos</h2>

            {{-- Columnas editoriales con regla superior: el numero grande ordena la secuencia,
                 el espacio en blanco separa — sin encajonar cada paso --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-10 gap-y-8">
                @foreach ([
                    ['numero' => '1', 'titulo' => 'Mirá el menú', 'detalle' => 'Sin registrarte. Contanos qué buscás (carne, veggie, vegano o sin TACC) y te mostramos solo lo que va con vos.'],
                    ['numero' => '2', 'titulo' => 'Elegí o personalizá', 'detalle' => 'Pedí un combo de la casa tal como viene, o armá el tuyo capa por capa: hasta 5 toppings, 3 salsas, papas y bebida. El precio se actualiza al instante.'],
                    ['numero' => '3', 'titulo' => 'Creá tu cuenta y pedí', 'detalle' => 'Al confirmar tu pedido te pedimos registrarte (una sola vez). Después seguís el estado hasta que esté lista para retirar.'],
                ] as $paso)
                    <div class="border-t-2 border-terminal-950 pt-5">
                        <span class="font-display text-5xl text-brand-500" aria-hidden="true">{{ $paso['numero'] }}</span>
                        <h3 class="font-display text-xl uppercase mt-3">{{ $paso['titulo'] }}</h3>
                        <p class="text-terminal-700 mt-2 text-sm leading-relaxed">{{ $paso['detalle'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ QUIENES SOMOS ============ --}}
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

                <a href="{{ route('equipo') }}" wire:navigate class="inline-block font-semibold text-terminal-950 underline underline-offset-4 hover:text-brand-600 mt-4">
                    Conocé al equipo que lo hizo →
                </a>
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

    {{-- ============ CONTACTO Y UBICACION ============
         id="contacto": permite enlazar directo a esta seccion desde el footer
         compartido de las demas paginas autenticadas (route('home').'#contacto') --}}
    <section id="contacto" class="bg-white border-b-2 border-terminal-950 scroll-mt-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <span class="font-mono text-sm font-semibold text-brand-600">// dónde encontrarnos</span>
            <h2 class="font-display text-4xl sm:text-5xl uppercase mt-1 mb-10">Vení a probarla</h2>

            {{-- Datos en columnas editoriales (regla superior, sin cajas);
                 el mapa es el unico elemento enmarcado, porque es contenido embebido --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10 items-start">
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-1 lg:grid-cols-3 gap-x-8 gap-y-6">
                    <div class="border-t-2 border-terminal-950 pt-4">
                        <h3 class="font-semibold text-terminal-950">Ubicación</h3>
                        <p class="text-terminal-700 text-sm mt-2 leading-relaxed">
                            {{ config('negocio.direccion') }}<br>
                            {{ config('negocio.ciudad') }}
                        </p>
                    </div>

                    <div class="border-t-2 border-terminal-950 pt-4">
                        <h3 class="font-semibold text-terminal-950">Horarios</h3>
                        <p class="text-terminal-700 text-sm mt-2 leading-relaxed">
                            {{ config('negocio.horario_dias') }}<br>
                            {{ config('negocio.horario_horas') }}<br>
                            <span class="font-mono text-xs text-terminal-500">// lunes: deploy</span>
                        </p>
                    </div>

                    <div class="border-t-2 border-terminal-950 pt-4">
                        <h3 class="font-semibold text-terminal-950">Contacto</h3>
                        <p class="text-terminal-700 text-sm mt-2 leading-relaxed">
                            Tel: {{ config('negocio.telefono') }}<br>
                            {{ config('negocio.email') }}<br>
                            Instagram: {{ config('negocio.instagram') }}
                        </p>
                    </div>
                </div>

                {{-- Mapa embebido de OpenStreetMap (no requiere API key). Las coordenadas
                     salen de geocodificar la direccion real via Nominatim (ver
                     InicioController@ubicarDireccionDelNegocio), no estan hardcodeadas:
                     si la direccion del negocio cambia, el mapa se actualiza solo --}}
                @if ($ubicacion)
                    <iframe
                        title="Mapa de la ubicación de {{ config('negocio.nombre') }} en {{ config('negocio.ciudad') }}"
                        src="https://www.openstreetmap.org/export/embed.html?bbox={{ $ubicacion['boundingbox'][2] }}%2C{{ $ubicacion['boundingbox'][0] }}%2C{{ $ubicacion['boundingbox'][3] }}%2C{{ $ubicacion['boundingbox'][1] }}&layer=mapnik&marker={{ $ubicacion['lat'] }}%2C{{ $ubicacion['lon'] }}"
                        class="w-full h-56 rounded-xl border-2 border-terminal-950 shadow-retro-sm"
                        loading="lazy"></iframe>
                @else
                    {{-- Si Nominatim no responde (o la direccion no se pudo ubicar),
                         se muestra un aviso en vez de dejar un iframe roto --}}
                    <div class="w-full h-56 rounded-xl border-2 border-terminal-950 shadow-retro-sm bg-brand-50 flex items-center justify-center text-center px-6">
                        <p class="text-sm text-terminal-600">
                            No pudimos cargar el mapa en este momento.<br>
                            Encontranos en {{ config('negocio.direccion') }}, {{ config('negocio.ciudad') }}.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ============ CTA FINAL + CIERRE DE MARCA ============ --}}
    <section class="bg-terminal-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <p class="font-mono text-sm text-brand-400">// en el modelo OSI la capa 8 es el usuario</p>
            <h2 class="font-display text-4xl sm:text-6xl uppercase mt-3">Acá, la capa 8 <span class="text-brand-500">sos vos</span></h2>

            <a href="{{ route('menu.index') }}"
                class="inline-block mt-8 px-8 py-4 bg-brand-500 text-terminal-950 font-semibold rounded-md border-2 border-white/20 hover:bg-brand-600 transition">
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
                <p class="font-mono text-xs text-terminal-500">© {{ date('Y') }} {{ config('negocio.nombre') }} — {{ config('negocio.direccion') }}, {{ config('negocio.ciudad') }} — hecho con Laravel + Livewire</p>
            </div>
        </div>
    </section>
</x-app-layout>
