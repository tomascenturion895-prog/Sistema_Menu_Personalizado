{{-- Pagina publica y estatica: no necesita controlador ni modelo, por eso se
     registra con Route::view (mismo criterio que la ruta "perfil"). El registro
     enlaza aca desde el checkbox de "Acepto los terminos y condiciones". --}}
<x-app-layout>
    <x-slot name="titulo">Términos y condiciones — Capa8Burger</x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <span class="eyebrow">// legal</span>
        <h1 class="font-display text-4xl sm:text-5xl uppercase text-terminal-950">Términos y condiciones</h1>
        <p class="text-gray-500 text-sm mt-2">Última actualización: {{ now()->translatedFormat('d \d\e F \d\e Y') }}</p>

        <div class="mt-8">
            <x-terminos-contenido />
        </div>
    </div>
</x-app-layout>
