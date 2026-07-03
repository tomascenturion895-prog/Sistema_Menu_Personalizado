@props(['active'])

@php
// Version movil del link de navegacion, sobre el fondo naranja del menu desplegable
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2.5 bg-white border-2 border-terminal-950 rounded-lg shadow-retro-sm text-start text-base font-semibold text-terminal-950 transition'
            : 'block w-full ps-3 pe-4 py-2.5 border-2 border-transparent rounded-lg text-start text-base font-semibold text-terminal-950/70 hover:text-terminal-950 hover:bg-white/40 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
