<?php

namespace App\Livewire\Menu;

use App\Models\Categoria;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    // #[Url] sincroniza esta propiedad con la URL (ej: /menu?dieta=vegano).
    // Asi el filtro queda guardado si recargas la pagina o compartis el link.
    #[Url]
    public string $dieta = 'todos';

    public function render()
    {
        // Empezamos la consulta por Categoria (no por Producto) porque la vista
        // necesita agrupar los productos debajo del nombre de su categoria
        $categorias = Categoria::query()
            ->where('activo', true)
            ->when($this->dieta !== 'todos', function ($query) {
                $query->where('tipo_dieta', $this->dieta);
            })
            // Cargamos solo los productos activos de cada categoria (eager loading con filtro)
            ->with(['productos' => fn ($query) => $query->where('activo', true)])
            ->orderBy('nombre')
            ->get()
            // Ocultamos categorias que, despues del filtro, quedaron sin productos
            ->filter(fn (Categoria $categoria) => $categoria->productos->isNotEmpty());

        return view('livewire.menu.index', [
            'categorias' => $categorias,
        ]);
    }

    /**
     * Cambia el filtro de dieta activo. Se llama desde los botones de tabs en la vista.
     */
    public function filtrarPor(string $dieta): void
    {
        $this->dieta = $dieta;
    }
}
