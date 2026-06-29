<?php

namespace App\Livewire\Concerns;

/**
 * Centraliza el patron para abrir/cerrar el componente <x-modal> de Breeze
 * desde un componente Livewire.
 *
 * El modal de Breeze (resources/views/components/modal.blade.php) usa Alpine.js
 * y escucha EVENTOS DE NAVEGADOR (open-modal / close-modal), no una propiedad
 * publica de Livewire. Esto es asi porque Alpine guarda su propio estado interno
 * (x-data="{ show: ... }") y, si lo controlaramos con una propiedad publica del
 * componente, ese estado de Alpine no se actualizaria de forma confiable despues
 * del primer render (es una limitacion conocida al mezclar Livewire 3 + Alpine).
 *
 * Por eso, en lugar de escribir `$this->dispatch('open-modal', 'nombre')` a mano
 * en cada componente, usamos este trait para no repetir el mismo detalle tecnico
 * en cada CRUD nuevo.
 */
trait InteractsWithModals
{
    /**
     * Dispara el evento que el <x-modal name="$name"> esta escuchando para mostrarse.
     */
    public function openModal(string $name): void
    {
        $this->dispatch('open-modal', $name);
    }

    /**
     * Dispara el evento que el <x-modal name="$name"> esta escuchando para ocultarse.
     */
    public function closeModal(string $name): void
    {
        $this->dispatch('close-modal', $name);
    }
}
