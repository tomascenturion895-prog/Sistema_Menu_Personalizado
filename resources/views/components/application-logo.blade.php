{{-- Logo de Capa8Burger: texto estilo terminal con cursor parpadeante.
     El prop "variant" permite usarlo sobre fondos de distinto color:
     - default: el acento "burger" va en naranja de marca (fondos claros u oscuros neutros)
     - onbrand: el acento va en blanco (para fondos naranjas, donde el acento naranja no se veria) --}}
@props(['variant' => 'default'])

@php
    $acento = $variant === 'onbrand' ? 'text-white' : 'text-brand-500';
@endphp

<span {{ $attributes->merge(['class' => 'font-mono font-semibold select-none']) }}>capa8<span class="{{ $acento }}">burger</span><span class="{{ $acento }} logo-cursor">_</span></span>
