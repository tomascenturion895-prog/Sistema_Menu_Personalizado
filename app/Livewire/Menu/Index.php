<?php

namespace App\Livewire\Menu;

use App\Livewire\Concerns\ConMensajeFlash;
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
    use ConMensajeFlash;

    // #[Url] sincroniza esta propiedad con la URL (ej: /menu?dieta=vegano)
    #[Url]
    public string $dieta = 'todos';

    // Texto de busqueda: filtra productos por nombre o descripcion en vivo
    #[Url]
    public string $busqueda = '';

    /**
     * Al cargar la pagina, si el cliente ya respondio la pregunta de preferencia
     * en una visita anterior, aplicamos su eleccion como filtro inicial.
     */
    public function mount(): void
    {
        // Recupera mensajes flasheados desde un redirect (ej. PedidoController::repetir)
        $this->recuperarMensajeFlash();

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
            ->with(['productos' => function ($query) {
                $query->where('activo', true)
                    // Busqueda por texto: matchea el nombre O la descripcion del producto
                    ->when($this->busqueda !== '', function ($query) {
                        $query->where(function ($query) {
                            $query->where('nombre', 'like', "%{$this->busqueda}%")
                                ->orWhere('descripcion', 'like', "%{$this->busqueda}%");
                        });
                    });
            }])
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

    /**
     * Agrega un combo de la casa directo al carrito, tal como viene (sin personalizar).
     * Agregar al carrito es libre para cualquiera, incluso sin cuenta: el login
     * se exige recien al confirmar el pedido (MiPedido@confirmarPedido).
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

        // Avisa a la navbar (y a quien escuche) que el carrito cambio,
        // para que el contador de "Mi pedido" se actualice al instante
        $this->dispatch('carrito-actualizado');

        $this->mostrarMensajeFlash("{$producto->nombre} se agregó a tu pedido.");
    }
}
