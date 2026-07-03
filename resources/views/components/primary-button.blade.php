{{-- Boton principal del sistema: naranja de marca con borde negro y sombra dura (estilo retro).
     Al hacer hover se "hunde": se desplaza y pierde la sombra. Como todas las vistas usan este
     componente, cambiar esto cambia todos los botones principales a la vez. --}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-retro px-4 py-2 bg-brand-500 text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
