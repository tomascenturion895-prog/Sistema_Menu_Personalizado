{{-- Mensaje flash de exito: se muestra una sola vez despues de una accion
     (agregar al carrito, confirmar, cancelar...). Lee session('mensaje') directo,
     asi cualquier vista lo incluye con <x-mensaje-flash /> sin repetir el markup. --}}
@if (session('mensaje'))
    <div {{ $attributes->merge(['class' => 'mb-6 px-4 py-3 bg-exito-100 border border-exito-500 text-exito-700 rounded-md text-sm']) }}>
        {{ session('mensaje') }}
    </div>
@endif
