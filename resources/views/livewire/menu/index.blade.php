{{-- Contenedor de pagina: las paginas Livewire definen su propio ancho maximo y padding --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-28">

    {{-- Notificacion inline: Alpine la oculta sola a los 5s o con el boton X --}}
    @if ($mensajeFlash)
        <div x-data
             x-init="setTimeout(() => $wire.ocultarMensajeFlash(), 5000)"
             class="mb-4 flex items-center justify-between gap-2 text-sm bg-exito-50 border border-exito-200 text-exito-700 rounded-md px-4 py-3">
            <span>{{ $mensajeFlash }}</span>
            <button type="button" wire:click="ocultarMensajeFlash"
                    class="text-exito-400 hover:text-exito-700 leading-none text-base"
                    aria-label="Cerrar">&times;</button>
        </div>
    @endif

    {{-- Encabezado del menu, siempre visible --}}
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <span class="eyebrow">// menú</span>
            <h2 class="font-display text-4xl sm:text-5xl uppercase text-terminal-900">Armá tu <span class="text-brand-500">burger</span></h2>
        </div>

        @unless ($this->debePreguntarPreferencia)
            <div class="text-sm text-gray-500">
                {{-- Las etiquetas de dieta viven en el modelo Categoria (unica fuente de verdad) --}}
                Estás viendo: <span class="font-medium text-gray-800">{{ \App\Models\Categoria::TIPOS_DIETA[$dieta] ?? 'Menú completo' }}</span>
                <button wire:click="cambiarPreferencia" class="ml-2 text-brand-600 hover:underline font-medium">Cambiar</button>
            </div>
        @endunless
    </div>

    {{-- Busqueda en vivo: .live.debounce.300ms espera 300ms desde la ultima tecla
         antes de consultar al servidor (evita una request por cada letra) --}}
    <div class="mb-6">
        <input type="search" wire:model.live.debounce.300ms="busqueda"
            placeholder="Buscar en el menú… (ej: garbanzos, doble, vegana)"
            class="w-full sm:max-w-md border-2 border-terminal-950 rounded-md shadow-retro-sm focus:border-brand-500 focus:ring-brand-500 text-sm"
            aria-label="Buscar productos en el menú">
    </div>

    {{-- ============ PREGUNTA DE PREFERENCIA (banner, no bloquea) ============
         El menu completo se ve abajo desde el primer momento; responder solo lo filtra.
         Asi un visitante apurado puede scrollear la carta sin contestar nada. --}}
    @if ($this->debePreguntarPreferencia)
        <div class="bg-brand-500 border-2 border-terminal-950 rounded-xl shadow-retro p-6 mb-10">
            <p class="font-semibold text-white text-lg" style="text-shadow: 1px 1px 0 #14171b;">¿Qué estás buscando hoy?</p>
            <p class="text-terminal-950/80 font-medium mt-1 text-sm">Elegí una opción y filtramos el menú a tu medida — o seguí bajando y miralo completo.</p>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mt-4">
                @foreach ([
                    ['valor' => 'normal', 'titulo' => 'Soy fan de la carne'],
                    ['valor' => 'vegetariano', 'titulo' => 'Como vegetariano'],
                    ['valor' => 'vegano', 'titulo' => 'Como vegano'],
                    ['valor' => 'celiaco', 'titulo' => 'Necesito sin TACC'],
                    ['valor' => 'todos', 'titulo' => 'Quiero ver todo'],
                ] as $opcion)
                    <button wire:click="elegirPreferencia('{{ $opcion['valor'] }}')" wire:key="preferencia-{{ $opcion['valor'] }}"
                        class="btn-retro rounded-lg px-3 py-2.5 bg-white text-sm text-terminal-950">
                        {{ $opcion['titulo'] }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

        {{-- Navegacion de categorias fija al hacer scroll (estilo carta digital) --}}
        @if ($categorias->count() > 1)
            <nav class="sticky top-0 z-10 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 bg-white/95 backdrop-blur border-b border-gray-100 mb-8">
                <div class="flex gap-2 overflow-x-auto">
                    @foreach ($categorias as $categoria)
                        <a href="#categoria-{{ $categoria->id }}"
                            class="whitespace-nowrap px-4 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-terminal-950 hover:text-white transition">
                            {{ $categoria->nombre }}
                        </a>
                    @endforeach
                </div>
            </nav>
        @endif

        {{-- ============ PRODUCTOS POR CATEGORIA ============ --}}
        @forelse ($categorias as $categoria)
            <section class="mb-14 scroll-mt-20" id="categoria-{{ $categoria->id }}" wire:key="categoria-{{ $categoria->id }}">
                {{-- Encabezado de categoria estilo carta: titulo + linea guia hasta el punto de dieta --}}
                <div class="flex items-center gap-3 mb-1">
                    <h3 class="font-display text-2xl uppercase text-terminal-950">{{ $categoria->nombre }}</h3>
                    <span class="flex-1 border-t-2 border-terminal-950" aria-hidden="true"></span>
                    <x-punto-dieta :dieta="$categoria->tipo_dieta" class="w-3 h-3" />
                </div>

                @if ($categoria->descripcion)
                    <p class="text-sm text-gray-500 mb-2">{{ $categoria->descripcion }}</p>
                @endif

                {{-- Filas de carta editorial: sin cajas, separadas por lineas finas.
                     El espacio y la tipografia hacen la jerarquia, no los bordes --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 lg:gap-x-14">
                    @foreach ($categoria->productos as $producto)
                        <article class="flex gap-4 py-6 border-b border-gray-200" wire:key="producto-{{ $producto->id }}">
                            {{-- Miniatura: solo si el admin cargo foto (sin foto, la tipografia manda) --}}
                            @if ($producto->imagen)
                                <div class="w-32 h-32 shrink-0 rounded-xl overflow-hidden shadow-sm">
                                    <x-foto-producto :producto="$producto" alto="h-32" />
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline gap-2">
                                    <h4 class="font-semibold text-gray-900">{{ $producto->nombre }}</h4>
                                    {{-- Linea punteada que une nombre y precio, como en las cartas impresas --}}
                                    <span class="flex-1 border-b-2 border-dotted border-gray-300 min-w-4" aria-hidden="true"></span>
                                    <span class="precio whitespace-nowrap">@precio($producto->precio)</span>
                                </div>

                                @if ($producto->descripcion)
                                    <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">{{ $producto->descripcion }}</p>
                                @endif

                                <div class="flex flex-wrap gap-2 mt-3.5">
                                    {{-- Combo de la casa: se agrega tal cual. wire:loading da feedback inmediato --}}
                                    <button wire:click="agregarCombo({{ $producto->id }})"
                                        wire:loading.attr="disabled" wire:target="agregarCombo({{ $producto->id }})"
                                        class="text-sm font-semibold border-2 border-terminal-950 text-terminal-950 px-3.5 py-1.5 rounded-md hover:bg-terminal-950 hover:text-white transition disabled:opacity-50">
                                        <span wire:loading.remove wire:target="agregarCombo({{ $producto->id }})">Agregar como viene</span>
                                        <span wire:loading wire:target="agregarCombo({{ $producto->id }})">Agregando…</span>
                                    </button>

                                    <a href="{{ route('menu.personalizar', $producto) }}" wire:navigate
                                        class="text-sm font-semibold bg-brand-500 text-terminal-950 border-2 border-terminal-950 px-3.5 py-1.5 rounded-md hover:bg-brand-600 transition">
                                        Personalizar
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @empty
            {{-- Estado vacio: indica que hacer segun si fue por busqueda o por filtro --}}
            <div class="tarjeta p-10 text-center">
                @if ($busqueda !== '')
                    <p class="text-gray-700 font-medium">No encontramos nada para "{{ $busqueda }}"</p>
                    <p class="text-sm text-gray-500 mt-1">Probá con otra palabra o borrá la búsqueda.</p>
                    <button wire:click="$set('busqueda', '')" class="mt-4 text-sm font-semibold text-brand-600 hover:underline">
                        Borrar búsqueda
                    </button>
                @else
                    <p class="text-gray-700 font-medium">No hay opciones para esta preferencia todavía</p>
                    <p class="text-sm text-gray-500 mt-1">Probá con otra opción o mirá el menú completo.</p>
                    <button wire:click="cambiarPreferencia" class="mt-4 text-sm font-semibold text-brand-600 hover:underline">
                        Cambiar mi preferencia
                    </button>
                @endif
            </div>
        @endforelse

    {{-- Barra flotante del pedido en curso (componente compartido con Personalizar) --}}
    <x-barra-carrito />
</div>
