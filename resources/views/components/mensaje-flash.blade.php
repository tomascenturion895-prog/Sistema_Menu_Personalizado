{{-- Mensaje flash de exito: se muestra una sola vez despues de una accion
     (agregar al carrito, confirmar, cancelar...). Lee session('mensaje') directo,
     asi cualquier vista lo incluye con <x-mensaje-flash /> sin repetir el markup.
     Se cierra solo a los 5s, o antes con el boton de cerrar: antes se quedaba
     fijo en pantalla hasta refrescar o cambiar de vista. --}}
@if (session('mensaje'))
    <div
        x-data="{ visible: true }"
        x-show="visible"
        x-init="setTimeout(() => visible = false, 5000)"
        x-transition
        {{ $attributes->merge(['class' => 'mb-6 px-4 py-3 bg-exito-100 border border-exito-500 text-exito-700 rounded-md text-sm flex items-center justify-between gap-4']) }}>
        <span>{{ session('mensaje') }}</span>
        <button type="button" @click="visible = false" class="shrink-0 text-exito-700 hover:text-exito-900" aria-label="Cerrar mensaje">
            &times;
        </button>
    </div>
@endif
