{{-- Boton secundario (cancelar, acciones neutras): blanco con borde negro, sin relleno de color --}}
<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-retro px-4 py-2 bg-white text-xs text-terminal-950 uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
