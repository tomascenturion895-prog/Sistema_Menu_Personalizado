{{-- Foto del producto con fallback de marca: si el admin no cargo imagen,
     se muestra la hamburguesa de 8 capas sobre el crema de la marca.
     Se usa en el menu, en los destacados de la landing y en personalizar.

     "imagen" guarda la ruta COMPLETA desde public/ (no solo el nombre de
     archivo), por eso alcanza con asset() directo sin armar el prefijo a mano.
     Puede venir de dos origenes, sin que a este componente le importe cual:
     - "storage/productos/x.jpg": subida por el admin (Livewire WithFileUploads)
     - "images/productos/x.jpg": foto puesta a mano en public/images/productos,
       para tener fotos reales commiteadas en el repo sin pasar por el panel --}}
@props(['producto', 'alto' => 'h-40'])

@if ($producto->imagen)
    <img src="{{ asset($producto->imagen) }}"
        alt="Foto de {{ $producto->nombre }}"
        class="w-full {{ $alto }} object-cover">
@else
    <div class="w-full {{ $alto }} bg-brand-50 flex items-center justify-center py-3" aria-hidden="true">
        <x-burger-capas class="h-full w-auto" />
    </div>
@endif
