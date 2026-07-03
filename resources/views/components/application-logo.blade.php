{{-- Logo de Capa8Burger: texto estilo terminal con cursor parpadeante.
     Reemplaza el SVG default de Laravel. El guion bajo animado es el "sello" de la marca:
     hamburgueseria + programacion (la capa 8 del modelo OSI es el usuario). --}}
<span {{ $attributes->merge(['class' => 'font-mono font-semibold select-none']) }}>capa8<span class="text-brand-500">burger</span><span class="text-brand-500 logo-cursor">_</span></span>
