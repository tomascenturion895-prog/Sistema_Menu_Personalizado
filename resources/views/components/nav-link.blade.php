@props(['active'])

@php
// Link de la navbar (fondo naranja de marca): el activo es una pildora blanca con
// borde negro y sombra dura (el mismo lenguaje retro de la landing y el login);
// los inactivos son texto oscuro que gana borde al pasar el mouse
$classes = ($active ?? false)
            ? 'inline-flex items-center px-4 py-1.5 bg-white border-2 border-terminal-950 rounded-full shadow-retro-sm text-sm font-semibold text-terminal-950 transition'
            : 'inline-flex items-center px-4 py-1.5 border-2 border-transparent rounded-full text-sm font-semibold text-terminal-950/70 hover:text-terminal-950 hover:border-terminal-950 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
