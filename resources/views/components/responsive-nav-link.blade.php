@props(['active'])

@php
// Version movil del link de navegacion, sobre el fondo blanco del menu desplegable
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2.5 bg-brand-500 border-2 border-terminal-950 rounded-lg shadow-retro-sm text-start text-base font-semibold text-white transition'
            : 'block w-full ps-3 pe-4 py-2.5 border-2 border-transparent rounded-lg text-start text-base font-semibold text-terminal-600 hover:text-terminal-950 hover:bg-brand-50 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
