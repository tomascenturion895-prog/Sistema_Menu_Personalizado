<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

// Sidebar exclusivo del panel admin: reemplaza a la navbar del cliente
// (Menu/Carrito/Mis pedidos no le sirven de nada a un administrador, que
// solo entra a gestionar el negocio, no a comprar su propia hamburguesa).
new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect(route('home', absolute: false), navigate: true);
    }
}; ?>

<div x-data="{ open: false }">
    {{-- Barra superior SOLO en mobile: logo + boton para abrir el sidebar --}}
    <div class="lg:hidden flex items-center justify-between bg-terminal-950 text-white px-4 py-3 border-b-2 border-terminal-950">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="font-mono font-semibold">
            capa8<span class="text-brand-500">admin</span><span class="text-brand-500 logo-cursor">_</span>
        </a>

        <button @click="open = true" class="p-1.5 rounded-md border-2 border-white/20 text-white" aria-label="Abrir menú">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    {{-- Fondo atenuado detras del sidebar cuando esta abierto en mobile --}}
    <div x-show="open" x-transition.opacity @click="open = false"
        class="fixed inset-0 bg-terminal-950/60 z-40 lg:hidden" style="display: none;" aria-hidden="true"></div>

    {{-- Sidebar: fijo en desktop (siempre visible), deslizable en mobile --}}
    <aside
        :class="open ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-terminal-950 text-white flex flex-col transition-transform duration-200 ease-out lg:translate-x-0"
    >
        <div class="px-6 py-5 border-b border-terminal-800 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="font-mono font-semibold text-lg">
                capa8<span class="text-brand-500">admin</span><span class="text-brand-500 logo-cursor">_</span>
            </a>

            {{-- Boton para cerrar el sidebar, solo visible en mobile --}}
            <button @click="open = false" class="lg:hidden text-terminal-400 hover:text-white" aria-label="Cerrar menú">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-brand-500 text-terminal-950' : 'text-terminal-300 hover:bg-terminal-900 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.pedidos') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.pedidos') ? 'bg-brand-500 text-terminal-950' : 'text-terminal-300 hover:bg-terminal-900 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Pedidos
            </a>

            <a href="{{ route('admin.productos') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.productos') ? 'bg-brand-500 text-terminal-950' : 'text-terminal-300 hover:bg-terminal-900 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                </svg>
                Productos
            </a>

            <a href="{{ route('admin.categorias') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.categorias') ? 'bg-brand-500 text-terminal-950' : 'text-terminal-300 hover:bg-terminal-900 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                </svg>
                Categorías
            </a>

            <a href="{{ route('admin.ingredientes') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.ingredientes') ? 'bg-brand-500 text-terminal-950' : 'text-terminal-300 hover:bg-terminal-900 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                Ingredientes
            </a>
        </nav>

        {{-- Pie del sidebar: quien esta logueado, volver al sitio publico y cerrar sesion --}}
        <div class="px-3 py-4 border-t border-terminal-800 space-y-1">
            <a href="{{ route('home') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-terminal-300 hover:bg-terminal-900 hover:text-white transition">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Ver el sitio
            </a>

            <button wire:click="logout" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-terminal-300 hover:bg-terminal-900 hover:text-white transition">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
                Cerrar sesión
            </button>

            <div class="px-3 pt-3 mt-2 border-t border-terminal-800/60 text-xs font-mono text-terminal-500 truncate">
                {{ auth()->user()->nombre_completo }}
            </div>
        </div>
    </aside>
</div>
