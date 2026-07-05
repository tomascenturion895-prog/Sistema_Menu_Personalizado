{{-- Punto de color que identifica el tipo de dieta (verde vegano, amarillo sin TACC...).
     El mapa usa los nombres de clase COMPLETOS a proposito: Tailwind solo compila las
     clases que encuentra escritas literalmente, no las armadas por concatenacion. --}}
@props(['dieta'])

@php
    $color = match ($dieta) {
        'normal' => 'bg-dieta-normal',
        'vegetariano' => 'bg-dieta-vegetariano',
        'vegano' => 'bg-dieta-vegano',
        'celiaco' => 'bg-dieta-celiaco',
        default => 'bg-gray-300',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-block w-2 h-2 rounded-full border border-terminal-950 {$color}"]) }}></span>
