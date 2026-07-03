{{-- Contenedor de pagina: las paginas Livewire definen su propio ancho maximo y padding --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-28">

    {{-- Mensaje flash (dura una sola request) --}}
    @if (session('mensaje'))
        <div class="mb-6 px-4 py-3 bg-exito-100 border border-exito-500 text-exito-700 rounded-md text-sm">
            {{ session('mensaje') }}
        </div>
    @endif

    {{-- ============ PREGUNTA INICIAL DE PREFERENCIA ============
         Se muestra solo la primera vez. La respuesta filtra el menu para no
         ofrecerle carne a un vegano ni opciones veganas a un fan de la carne. --}}
    @if ($this->debePreguntarPreferencia)
        {{-- Panel naranja estilo cartel, con las opciones como tarjetas blancas "hundibles" --}}
        <div class="bg-brand-500 border-2 border-terminal-950 rounded-xl shadow-retro p-8 sm:p-10 mb-10">
            <p class="font-mono text-sm font-semibold text-terminal-950 mb-1">// antes de empezar</p>
            <h2 class="font-display text-3xl sm:text-4xl uppercase text-white" style="text-shadow: 2px 2px 0 #14171b;">¿Qué estás buscando hoy?</h2>
            <p class="text-terminal-950/80 font-medium mt-2 text-sm">Con tu respuesta armamos el menú a tu medida. Podés cambiarla cuando quieras.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-6">
                @foreach ([
                    ['valor' => 'normal', 'titulo' => 'Soy fan de la carne', 'detalle' => 'Hamburguesas clásicas'],
                    ['valor' => 'vegetariano', 'titulo' => 'Como vegetariano', 'detalle' => 'Sin carne, con lácteos y huevo'],
                    ['valor' => 'vegano', 'titulo' => 'Como vegano', 'detalle' => '100% a base de plantas'],
                    ['valor' => 'celiaco', 'titulo' => 'Necesito sin TACC', 'detalle' => 'Apto para celíacos'],
                    ['valor' => 'todos', 'titulo' => 'Quiero ver todo', 'detalle' => 'El menú completo'],
                ] as $opcion)
                    <button wire:click="elegirPreferencia('{{ $opcion['valor'] }}')" wire:key="preferencia-{{ $opcion['valor'] }}"
                        class="btn-retro rounded-lg px-4 py-3.5 bg-white justify-start text-left">
                        <span class="block font-semibold text-terminal-950 text-sm">{{ $opcion['titulo'] }}</span>
                        <span class="block text-terminal-500 text-xs mt-0.5">{{ $opcion['detalle'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    @else
        {{-- Encabezado + indicador de la preferencia activa --}}
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="eyebrow">// menú</span>
                <h2 class="font-display text-4xl sm:text-5xl uppercase text-terminal-900">Armá tu <span class="text-brand-500">burger</span></h2>
            </div>

            <div class="text-sm text-gray-500">
                {{-- Las etiquetas de dieta viven en el modelo Categoria (unica fuente de verdad) --}}
                Estás viendo: <span class="font-medium text-gray-800">{{ \App\Models\Categoria::TIPOS_DIETA[$dieta] ?? 'Menú completo' }}</span>
                <button wire:click="cambiarPreferencia" class="ml-2 text-brand-600 hover:underline font-medium">Cambiar</button>
            </div>
        </div>

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
            <section class="mb-12 scroll-mt-20" id="categoria-{{ $categoria->id }}" wire:key="categoria-{{ $categoria->id }}">
                <div class="mb-1">
                    <h3 class="text-xl font-semibold text-gray-900">{{ $categoria->nombre }}</h3>
                </div>

                @if ($categoria->descripcion)
                    <p class="text-sm text-gray-500 mb-5">{{ $categoria->descripcion }}</p>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($categoria->productos as $producto)
                        {{-- Tarjeta de producto estilo cartel retro: borde negro y sombra dura --}}
                        <div class="tarjeta tarjeta-hover p-5 flex flex-col justify-between" wire:key="producto-{{ $producto->id }}">
                            <div>
                                <div class="flex items-start justify-between gap-4">
                                    <h4 class="font-semibold text-gray-900">{{ $producto->nombre }}</h4>
                                    <span class="precio whitespace-nowrap">${{ number_format($producto->precio, 0, ',', '.') }}</span>
                                </div>

                                @if ($producto->descripcion)
                                    <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ $producto->descripcion }}</p>
                                @endif
                            </div>

                            <div class="flex gap-2 mt-4 pt-4 border-t-2 border-gray-100">
                                {{-- Combo de la casa: se agrega tal cual, sin pasar por la personalizacion --}}
                                <button wire:click="agregarCombo({{ $producto->id }})"
                                    class="flex-1 text-sm font-semibold border-2 border-terminal-950 text-terminal-950 px-4 py-2 rounded-md hover:bg-terminal-950 hover:text-white transition">
                                    Agregar como viene
                                </button>

                                <a href="{{ route('menu.personalizar', $producto) }}" wire:navigate
                                    class="flex-1 text-center text-sm font-semibold bg-brand-500 text-white border-2 border-terminal-950 px-4 py-2 rounded-md hover:bg-brand-600 transition">
                                    Personalizar
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            {{-- Estado vacio: indica que hacer, no solo que no hay nada --}}
            <div class="tarjeta p-10 text-center">
                <p class="text-gray-700 font-medium">No hay opciones para esta preferencia todavía</p>
                <p class="text-sm text-gray-500 mt-1">Probá con otra opción o mirá el menú completo.</p>
                <button wire:click="cambiarPreferencia" class="mt-4 text-sm font-semibold text-brand-600 hover:underline">
                    Cambiar mi preferencia
                </button>
            </div>
        @endforelse
    @endif

    {{-- ============ BARRA FLOTANTE DEL PEDIDO ============
         Aparece abajo cuando hay items en el carrito, como en las cartas digitales --}}
    @if ($this->itemsEnCarrito > 0)
        <div class="fixed bottom-0 inset-x-0 z-20 bg-terminal-950 border-t-2 border-terminal-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">
                <div class="text-sm text-terminal-300">
                    <span class="font-mono font-semibold text-white">{{ $this->itemsEnCarrito }}</span>
                    {{ $this->itemsEnCarrito === 1 ? 'producto' : 'productos' }}
                    · <span class="precio text-white">${{ number_format($this->totalCarrito, 0, ',', '.') }}</span>
                </div>

                <a href="{{ route('menu.mi-pedido') }}" wire:navigate
                    class="px-5 py-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold rounded-md transition">
                    Ver mi pedido →
                </a>
            </div>
        </div>
    @endif
</div>
