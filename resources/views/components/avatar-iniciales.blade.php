{{-- Avatar placeholder con las iniciales del nombre, mismo rol que <x-foto-producto>
     cuando todavia no hay una imagen real cargada: se reemplaza por una foto de
     verdad el dia que el desarrollador la suba, sin tocar el resto de la vista. --}}
@props(['nombre', 'clase' => 'w-24 h-24'])

@php
    // Toma la primera letra de las primeras dos palabras del nombre (ej: "Centurion Tomas" -> "CT")
    $iniciales = collect(explode(' ', trim($nombre)))
        ->filter()
        ->map(fn (string $palabra) => mb_strtoupper(mb_substr($palabra, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<div class="{{ $clase }} rounded-full bg-terminal-950 border-2 border-terminal-950 flex items-center justify-center shrink-0">
    <span class="font-display text-brand-500 text-2xl">{{ $iniciales }}</span>
</div>
