{{-- Aviso de transicion entre secciones: tarjeta centrada estilo sticker retro
     (borde negro + sombra dura, sin glass en la tarjeta) sobre un fondo atenuado
     y difuminado para darle foco. Escucha los eventos nativos de wire:navigate:
     - livewire:navigate  -> se dispara ANTES de cambiar de pagina (se muestra)
     - livewire:navigated -> se dispara CUANDO ya cargo la pagina nueva (se oculta) --}}
<div
    x-data="{ visible: false }"
    x-on:livewire:navigate.window="visible = true"
    x-on:livewire:navigated.window="visible = false"
    x-show="visible"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display: none;"
    class="fixed inset-0 z-[60] flex items-center justify-center bg-terminal-950/40 backdrop-blur-sm"
    role="status"
    aria-live="polite"
>
    {{-- Tarjeta solida (sin blur propio): mismo lenguaje que .tarjeta del sistema --}}
    <div class="flex flex-col items-center gap-4 bg-white border-2 border-terminal-950 shadow-retro rounded-2xl px-10 py-8">
        {{-- Tres puntos con rebote escalonado, en los colores de la marca --}}
        <span class="flex items-center gap-2.5">
            <span class="w-4 h-4 rounded-full bg-brand-500 animate-bounce [animation-delay:-0.3s]"></span>
            <span class="w-4 h-4 rounded-full bg-cheddar-400 animate-bounce [animation-delay:-0.15s]"></span>
            <span class="w-4 h-4 rounded-full bg-exito-500 animate-bounce"></span>
        </span>

        <span class="font-mono text-sm uppercase tracking-wider text-terminal-950">Cargando<span class="logo-cursor">_</span></span>
    </div>
</div>
