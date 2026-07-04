{{-- Barra de carga global: reacciona a CUALQUIER request de Livewire (clicks con
     wire:navigate, acciones de formularios, polls) sin JS propio, porque wire:loading
     sin wire:target escucha toda la actividad de la pagina.
     Se incluye una sola vez en cada layout raiz (app y guest). --}}
<div wire:loading.delay.class="w-full" wire:loading.delay.remove.class="w-0"
    class="fixed top-0 left-0 h-1 bg-brand-500 z-50 transition-all duration-300 ease-out w-0"
    role="progressbar" aria-label="Cargando"></div>
