@props(['active'])

@php
// Link del menu de navegacion (navbar oscura): el activo se subraya con el naranja
// de marca y queda en blanco; los inactivos en gris claro de la paleta terminal
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-brand-500 text-sm font-medium leading-5 text-white focus:outline-none focus:border-brand-300 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-terminal-300 hover:text-white hover:border-terminal-600 focus:outline-none focus:text-white focus:border-terminal-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
