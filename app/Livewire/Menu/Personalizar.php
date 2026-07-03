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
    // Tipos de ingrediente donde el cliente elige UNA sola opcion (radio buttons).
    // Esto ya impone logica real: no se pueden pedir 30 panes ni 50 medallones,
    // porque el pan es uno solo y la cantidad de medallones es una opcion (simple/doble/triple)
    private const TIPOS_UNICOS = ['pan', 'medallon', 'papas', 'bebida'];

    // Tope de selecciones por cada tipo multiple, como en una hamburgueseria real
    public const LIMITES_MULTIPLES = ['topping' => 5, 'salsa' => 3, 'extra' => 3];

    // Maximo de unidades del mismo producto por item del pedido
    public const MAX_CANTIDAD = 10;

    // Etiquetas en espanol para cada tipo, usadas en la vista y en los mensajes de error
    public const ETIQUETAS = [
        'pan' => 'Tipo de pan',
        'medallon' => 'Medallones',
        'papas' => 'Papas fritas',
        'bebida' => 'Bebida',
        'topping' => 'Toppings',
        'salsa' => 'Salsas',
        'extra' => 'Extras',
    ];

    public Producto $producto;

    // Guarda la opcion elegida por cada tipo de eleccion unica: ej. ['pan' => 3, 'medallon' => 7]
    public array $seleccionUnica = [];

    // Guarda los ids de ingredientes marcados en las elecciones multiples (toppings, salsas, extras)
    public array $seleccionMultiple = [];

    public int $cantidad = 1;

    // Mensaje de error si el cliente intenta agregar sin completar una eleccion o supera un limite
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

    /**
     * Cuantas opciones hay marcadas de un tipo multiple (para el contador "2 de 5" de la vista).
     */
    public function contarSeleccionadosDeTipo(string $tipo): int
    {
        return $this->producto->ingredientes
            ->whereIn('id', $this->seleccionMultiple)
            ->where('tipo', $tipo)
            ->count();
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

    /**
     * Hook de Livewire: se ejecuta automaticamente cada vez que cambia seleccionMultiple.
     * Si el cliente supera el tope de un tipo (ej: mas de 5 toppings), deshacemos
     * la ultima marca y le avisamos. Asi el limite se aplica en el momento.
     */
    public function updatedSeleccionMultiple(): void
    {
        foreach (self::LIMITES_MULTIPLES as $tipo => $limite) {
            if ($this->contarSeleccionadosDeTipo($tipo) > $limite) {
                // array_pop saca el ultimo elemento agregado (la marca que excedio el tope)
                array_pop($this->seleccionMultiple);
                $this->error = 'Podés elegir hasta '.$limite.' de "'.self::ETIQUETAS[$tipo].'".';

                return;
            }
        }

        $this->error = '';
    }

    public function incrementar(): void
    {
        // Logica real: nadie pide mas de 10 unidades iguales en un mismo item
        if ($this->cantidad < self::MAX_CANTIDAD) {
            $this->cantidad++;
        }
    }

    public function decrementar(): void
    {
        if ($this->cantidad > 1) {
            $this->cantidad--;
        }
    }

    /**
     * Valida todas las reglas del negocio y, si esta todo bien, guarda el item
     * armado en el carrito (sesion) y vuelve al menu.
     */
    public function agregarAlPedido(): void
    {
        // Regla 1: cada grupo de eleccion unica disponible debe tener una opcion marcada
        foreach ($this->ingredientesPorTipo as $tipo => $opciones) {
            if (in_array($tipo, self::TIPOS_UNICOS) && empty($this->seleccionUnica[$tipo])) {
                $this->error = 'Te falta elegir una opción de "'.self::ETIQUETAS[$tipo].'".';

                return;
            }
        }

        // Regla 2: los topes de los tipos multiples (por si el navegador salteo el hook)
        foreach (self::LIMITES_MULTIPLES as $tipo => $limite) {
            if ($this->contarSeleccionadosDeTipo($tipo) > $limite) {
                $this->error = 'Podés elegir hasta '.$limite.' de "'.self::ETIQUETAS[$tipo].'".';

                return;
            }
        }

        // Regla 3 (seguridad): solo se aceptan ingredientes que realmente pertenecen
        // a este producto. Un request manipulado no puede meter ingredientes ajenos.
        $idsPermitidos = $this->producto->ingredientes->pluck('id')->all();
        $idsSeleccionados = [...array_values($this->seleccionUnica), ...$this->seleccionMultiple];
        $idsSeleccionados = array_values(array_intersect($idsSeleccionados, $idsPermitidos));

        // Regla 4: la cantidad queda acotada entre 1 y el maximo permitido
        $this->cantidad = max(1, min($this->cantidad, self::MAX_CANTIDAD));

        $this->error = '';

        // El carrito vive en la sesion (no en la base de datos) hasta que el cliente
        // confirme el pedido en la pantalla de "Mi pedido"
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
