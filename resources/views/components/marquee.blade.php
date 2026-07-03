{{-- Cinta marquee de la marca: texto en loop infinito, como los carteles luminosos.
     El contenido esta duplicado a proposito: la animacion corre hasta -50%, asi cuando
     termina la primera mitad ya esta entrando la segunda (loop invisible).
     Se usa en la landing y en el layout de la app para mantener la identidad. --}}
<div {{ $attributes->merge(['class' => 'marquee bg-terminal-950 text-white py-2']) }} aria-hidden="true">
    <div class="marquee-contenido font-mono text-xs uppercase tracking-widest">
        @foreach (range(1, 2) as $repeticion)
            <span class="flex shrink-0">
                @foreach (['hamburguesas', '100% personalizables', 'clásicas', 'vegetarianas', 'veganas', 'sin tacc', 'papas', 'salsas', 'combos de la casa'] as $item)
                    <span class="px-4">{{ $item }}</span>
                    <span class="text-brand-500">●</span>
                @endforeach
            </span>
        @endforeach
    </div>
</div>
