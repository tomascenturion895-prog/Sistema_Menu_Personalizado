{{-- Boton de accion destructiva (eliminar): usa el token "tomate" de la paleta --}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-tomate-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-tomate-700 active:bg-tomate-700 focus:outline-none focus:ring-2 focus:ring-tomate-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
