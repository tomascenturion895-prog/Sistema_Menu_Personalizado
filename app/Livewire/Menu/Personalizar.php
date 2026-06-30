<?php

namespace App\Livewire\Menu;

use App\Models\Ingrediente;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Personalizar extends Component
{
    // Tipos de ingrediente donde el cliente elige UNA sola opcion (radio buttons)
    private const TIPOS_UNICOS = ['pan', 'medallon', 'papas', 'bebida'];

    // Tipos de ingrediente donde el cliente puede elegir VARIAS opciones (checkboxes)
    private const TIPOS_MULTIPLES = ['topping', 'salsa', 'extra'];

    public Producto $producto;

    // Guarda la opcion elegida por cada tipo de eleccion unica: ej. ['pan' => 3, 'medallon' => 7]
    public array $seleccionUnica = [];

    // Guarda los ids de ingredientes marcados en las elecciones multiples (toppings, salsas, extras)
    public array $seleccionMultiple = [];

    public int $cantidad = 1;

    // Mensaje de error si el cliente intenta agregar al pedido sin completar una eleccion obligatoria
    public string $error = '';

    /**
     * mount() se ejecuta una sola vez, cuando se carga la pagina. Livewire ya resuelve
     * el modelo Producto automaticamente a partir del {producto} de la ruta (route model binding).
     */
    public function mount(Producto $producto): void
    {
        $this->producto = $producto->load('ingredientes');
    }

    /**
     * #[Computed] cachea el resultado durante el mismo request: si la vista lo usa varias
     * veces, no vuelve a agrupar la coleccion cada vez.
     */
    #[Computed]
    public function ingredientesPorTipo(): Collection
    {
        return $this->producto->ingredientes
            ->where('activo', true)
            // groupBy organiza la coleccion de ingredientes en sub-colecciones por su campo "tipo"
            ->groupBy('tipo');
    }

    #[Computed]
    public function precioUnitario(): float
    {
        $idsSeleccionados = [...array_values($this->seleccionUnica), ...$this->seleccionMultiple];

        $extras = Ingrediente::whereIn('id', $idsSeleccionados)->sum('precio_extra');

        return (float) $this->producto->precio + (float) $extras;
    }

    #[Computed]
    public function precioTotal(): float
    {
        return $this->precioUnitario * $this->cantidad;
    }

    public function incrementar(): void
    {
        $this->cantidad++;
    }

    public function decrementar(): void
    {
        if ($this->cantidad > 1) {
            $this->cantidad--;
        }
    }

    /**
     * Valida que cada grupo de eleccion unica disponible tenga una opcion marcada,
     * y si todo esta bien, guarda el item armado en el carrito (sesion) y vuelve al menu.
     */
    public function agregarAlPedido(): void
    {
        foreach ($this->ingredientesPorTipo as $tipo => $opciones) {
            if (in_array($tipo, self::TIPOS_UNICOS) && empty($this->seleccionUnica[$tipo])) {
                $this->error = 'Te falta elegir una opción de "'.$tipo.'".';

                return;
            }
        }

        $this->error = '';

        $idsSeleccionados = [...array_values($this->seleccionUnica), ...$this->seleccionMultiple];

        // El carrito vive en la sesion (no en la base de datos) hasta que el cliente confirme
        // el pedido en la pantalla de "Mi pedido" (eso se construye en la proxima rama)
        $carrito = Session::get('carrito', []);

        $carrito[] = [
            'producto_id' => $this->producto->id,
            'nombre' => $this->producto->nombre,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->precioUnitario,
            'ingredientes_elegidos' => $idsSeleccionados,
            'ingredientes_nombres' => Ingrediente::whereIn('id', $idsSeleccionados)->pluck('nombre')->all(),
        ];

        Session::put('carrito', $carrito);

        session()->flash('mensaje', "{$this->producto->nombre} se agregó a tu pedido.");

        $this->redirect(route('menu.index', absolute: false), navigate: true);
    }
}
