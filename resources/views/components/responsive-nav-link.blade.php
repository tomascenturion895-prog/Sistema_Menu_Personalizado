@props(['active'])

@php
// Version movil del link de navegacion, adaptada a la navbar oscura
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand-500 text-start text-base font-medium text-white bg-terminal-800 focus:outline-none focus:bg-terminal-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-terminal-300 hover:text-white hover:bg-terminal-800 hover:border-terminal-600 focus:outline-none focus:text-white focus:bg-terminal-800 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
