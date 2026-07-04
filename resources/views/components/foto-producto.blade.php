{{-- Foto del producto con fallback de marca: si el admin no cargo imagen,
     se muestra la hamburguesa de 8 capas sobre el crema de la marca.
     Se usa en el menu, en los destacados de la landing y en personalizar. --}}
@props(['producto', 'alto' => 'h-40'])

@if ($producto->imagen)
    <img src="{{ asset('storage/'.$producto->imagen) }}"
        alt="Foto de {{ $producto->nombre }}"
        class="w-full {{ $alto }} object-cover">
@else
    <div class="w-full {{ $alto }} bg-brand-50 flex items-center justify-center py-3" aria-hidden="true">
        <x-burger-capas class="h-full w-auto" />
    </div>
@endif
