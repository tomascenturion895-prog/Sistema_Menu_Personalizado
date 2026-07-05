<?php

namespace App\Livewire\Concerns;

/**
 * Livewire trae su propia vista de paginacion por defecto (en ingles, con
 * estilo generico) e IGNORA Paginator::defaultView() de Laravel para los
 * componentes que usan WithPagination. Este trait le dice a Livewire que
 * use la vista propia del sistema (resources/views/vendor/pagination/capa8),
 * la misma que ya usan las paginas que no son Livewire (ej. Mis pedidos).
 */
trait UsaPaginacionPropia
{
    public function paginationView(): string
    {
        return 'vendor.pagination.capa8';
    }
}
