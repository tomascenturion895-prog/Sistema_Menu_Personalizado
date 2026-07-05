{{-- Pagina publica con el perfil de cada desarrollador del proyecto
     (requisito de la consigna: "Perfiles de cada uno de los desarrolladores").
     Usa x-app-layout: misma navbar y footer que el resto del sitio. --}}
<x-app-layout>
    <x-slot name="titulo">Equipo — Capa8Burger</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <span class="eyebrow">// equipo</span>
        <h1 class="font-display text-4xl sm:text-5xl uppercase text-terminal-950">Quiénes lo hicimos</h1>
        <p class="text-gray-600 mt-3 max-w-xl">
            Capa8Burger es un proyecto de la Tecnicatura Universitaria en Programación (UTN),
            hecho por este equipo.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">
            @foreach ($desarrolladores as $persona)
                <div class="tarjeta shadow-retro-sm p-6 flex flex-col items-center text-center">
                    <x-avatar-iniciales :nombre="$persona['nombre']" />

                    <h2 class="font-semibold text-terminal-950 mt-4">{{ $persona['nombre'] }}</h2>

                    {{-- Rol como badge, mismo componente visual que los estados de pedido --}}
                    <span class="badge bg-brand-100 text-brand-800 mt-2">{{ $persona['rol'] }}</span>

                    <div class="flex items-center gap-3 mt-5">
                        {{-- GitHub: si todavia no se cargo la URL real, se muestra "apagado"
                             (sin href) en vez de armar un link roto apuntando a ningun lado --}}
                        @if ($persona['github'])
                            <a href="{{ $persona['github'] }}" target="_blank" rel="noopener"
                                class="text-terminal-600 hover:text-terminal-950 transition" aria-label="GitHub de {{ $persona['nombre'] }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.73.5.5 5.73.5 12c0 5.09 3.29 9.4 7.86 10.93.57.1.78-.25.78-.55v-1.94c-3.2.7-3.88-1.54-3.88-1.54-.53-1.33-1.29-1.69-1.29-1.69-1.05-.72.08-.7.08-.7 1.17.08 1.78 1.2 1.78 1.2 1.03 1.77 2.71 1.26 3.37.96.1-.75.4-1.26.73-1.55-2.56-.29-5.26-1.28-5.26-5.7 0-1.26.45-2.29 1.19-3.1-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.18 1.18a11 11 0 015.79 0c2.2-1.49 3.17-1.18 3.17-1.18.64 1.59.24 2.76.12 3.05.74.81 1.18 1.84 1.18 3.1 0 4.43-2.7 5.4-5.28 5.68.42.36.78 1.08.78 2.17v3.22c0 .3.21.66.79.55A10.5 10.5 0 0023.5 12C23.5 5.73 18.27.5 12 .5Z"/></svg>
                            </a>
                        @else
                            <span class="text-gray-300" aria-hidden="true" title="Todavía no cargado">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.73.5.5 5.73.5 12c0 5.09 3.29 9.4 7.86 10.93.57.1.78-.25.78-.55v-1.94c-3.2.7-3.88-1.54-3.88-1.54-.53-1.33-1.29-1.69-1.29-1.69-1.05-.72.08-.7.08-.7 1.17.08 1.78 1.2 1.78 1.2 1.03 1.77 2.71 1.26 3.37.96.1-.75.4-1.26.73-1.55-2.56-.29-5.26-1.28-5.26-5.7 0-1.26.45-2.29 1.19-3.1-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.18 1.18a11 11 0 015.79 0c2.2-1.49 3.17-1.18 3.17-1.18.64 1.59.24 2.76.12 3.05.74.81 1.18 1.84 1.18 3.1 0 4.43-2.7 5.4-5.28 5.68.42.36.78 1.08.78 2.17v3.22c0 .3.21.66.79.55A10.5 10.5 0 0023.5 12C23.5 5.73 18.27.5 12 .5Z"/></svg>
                            </span>
                        @endif

                        @if ($persona['linkedin'])
                            <a href="{{ $persona['linkedin'] }}" target="_blank" rel="noopener"
                                class="text-terminal-600 hover:text-terminal-950 transition" aria-label="LinkedIn de {{ $persona['nombre'] }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.45 20.45h-3.55v-5.57c0-1.33-.02-3.03-1.85-3.03-1.85 0-2.14 1.45-2.14 2.94v5.66H9.36V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.38-1.85 3.62 0 4.28 2.38 4.28 5.48v6.26ZM5.34 7.43a2.06 2.06 0 110-4.12 2.06 2.06 0 010 4.12ZM7.12 20.45H3.56V9h3.56v11.45Z"/></svg>
                            </a>
                        @else
                            <span class="text-gray-300" aria-hidden="true" title="Todavía no cargado">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.45 20.45h-3.55v-5.57c0-1.33-.02-3.03-1.85-3.03-1.85 0-2.14 1.45-2.14 2.94v5.66H9.36V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.38-1.85 3.62 0 4.28 2.38 4.28 5.48v6.26ZM5.34 7.43a2.06 2.06 0 110-4.12 2.06 2.06 0 010 4.12ZM7.12 20.45H3.56V9h3.56v11.45Z"/></svg>
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
