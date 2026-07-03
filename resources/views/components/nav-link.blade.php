@props(['active'])

@php
// Link de la navbar (fondo blanco): el activo es una pildora naranja de marca con
// borde negro y sombra dura; los inactivos son texto gris que gana borde al hover
$classes = ($active ?? false)
            ? 'inline-flex items-center px-4 py-1.5 bg-brand-500 border-2 border-terminal-950 rounded-full shadow-retro-sm text-sm font-semibold text-white transition'
            : 'inline-flex items-center px-4 py-1.5 border-2 border-transparent rounded-full text-sm font-semibold text-terminal-600 hover:text-terminal-950 hover:border-terminal-950 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
