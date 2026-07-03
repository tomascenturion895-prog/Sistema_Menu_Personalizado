{{-- Boton de accion destructiva (eliminar): rojo tomate con el mismo tratamiento retro --}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-retro px-4 py-2 bg-tomate-500 text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-tomate-500 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
