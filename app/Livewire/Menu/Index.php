<?php

namespace App\Livewire\Menu;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    // #[Url] sincroniza esta propiedad con la URL (ej: /menu?dieta=vegano)
    #[Url]
    public string $dieta = 'todos';

    /**
     * Al cargar la pagina, si el cliente ya respondio la pregunta de preferencia
     * en una visita anterior, aplicamos su eleccion como filtro inicial.
     */
    public function mount(): void
    {
        if ($this->dieta === 'todos' && Session::has('preferencia_dieta')) {
            $this->dieta = Session::get('preferencia_dieta');
        }
    }

    public function render()
    {
        $categorias = Categoria::query()
            ->where('activo', true)
            ->when($this->dieta !== 'todos', function ($query) {
                $query->where('tipo_dieta', $this->dieta);
            })
            ->with(['productos' => fn ($query) => $query->where('activo', true)])
            ->orderBy('nombre')
            ->get()
            ->filter(fn (Categoria $categoria) => $categoria->productos->isNotEmpty());

        return view('livewire.menu.index', [
            'categorias' => $categorias,
        ]);
    }

    /**
     * Indica si hay que mostrar la pregunta inicial de preferencia:
     * solo la primera vez, hasta que el cliente elija una opcion.
     */
    #[Computed]
    public function debePreguntarPreferencia(): bool
    {
        return ! Session::has('preferencia_dieta');
    }

    /**
     * Total de items en el carrito, para la barra flotante de "ver mi pedido".
     */
    #[Computed]
    public function itemsEnCarrito(): int
    {
        return count(Session::get('carrito', []));
    }

    #[Computed]
    public function totalCarrito(): float
    {
        return collect(Session::get('carrito', []))
            ->sum(fn (array $item) => $item['precio_unitario'] * $item['cantidad']);
    }

    /**
     * Guarda la respuesta a la pregunta inicial ("¿que estas buscando hoy?")
     * y la aplica como filtro. Queda en sesion para las proximas visitas.
     */
    public function elegirPreferencia(string $dieta): void
    {
        Session::put('preferencia_dieta', $dieta);
        $this->dieta = $dieta;
    }

    /**
     * Permite volver a responder la pregunta de preferencia.
     */
    public function cambiarPreferencia(): void
    {
        Session::forget('preferencia_dieta');
        $this->dieta = 'todos';
    }

    public function filtrarPor(string $dieta): void
    {
        $this->dieta = $dieta;
        // Si cambia el filtro a mano, actualizamos tambien su preferencia guardada
        Session::put('preferencia_dieta', $dieta);
    }

    /**
     * Agrega un combo de la casa directo al carrito, tal como viene (sin personalizar).
     */
    public function agregarCombo(int $productoId): void
    {
        $producto = Producto::where('activo', true)->findOrFail($productoId);

        $carrito = Session::get('carrito', []);

        $carrito[] = [
            'producto_id' => $producto->id,
            'nombre' => $producto->nombre,
            'cantidad' => 1,
            'precio_unitario' => (float) $producto->precio,
            'ingredientes_elegidos' => [],
            'ingredientes_nombres' => ['Receta de la casa'],
        ];

        Session::put('carrito', $carrito);

        session()->flash('mensaje', "{$producto->nombre} se agregó a tu pedido.");
    }
}
