<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\InteractsWithModals;
use App\Models\Categoria;
use App\Models\Ingrediente;
use App\Models\Producto;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Productos extends Component
{
    use InteractsWithModals;

    // WithFileUploads habilita subir archivos via wire:model (la foto del producto)
    use WithFileUploads;
    use WithPagination;

    #[Validate('required|exists:categorias,id')]
    public ?int $categoria_id = null;

    #[Validate('required|string|max:255')]
    public string $nombre = '';

    #[Validate('nullable|string|max:1000')]
    public string $descripcion = '';

    #[Validate('required|numeric|min:0')]
    public string $precio = '';

    #[Validate('boolean')]
    public bool $activo = true;

    // Foto nueva subida desde el formulario (archivo temporal de Livewire).
    // Es opcional: si no se sube nada, el producto conserva su imagen actual
    #[Validate('nullable|image|max:2048')]
    public $foto = null;

    // Ruta de la imagen que el producto ya tiene guardada (para mostrarla al editar)
    public ?string $imagenActual = null;

    // Array con los IDs de los ingredientes marcados en los checkboxes del formulario.
    // 'exists:ingredientes,id' valida cada elemento del array contra la tabla ingredientes
    #[Validate('array')]
    public array $ingredientesSeleccionados = [];

    public ?int $productoId = null;

    public ?int $productoAEliminar = null;

    public function render()
    {
        return view('livewire.admin.productos', [
            // with('categoria') evita el problema N+1: en vez de una consulta SQL por cada
            // fila para buscar su categoria, trae todas las categorias relacionadas de una sola vez
            'productos' => Producto::with('categoria')->orderBy('nombre')->paginate(8),
            'categorias' => Categoria::orderBy('nombre')->get(),
            'ingredientes' => Ingrediente::orderBy('tipo')->orderBy('nombre')->get(),
        ]);
    }

    public function abrirModalCrear(): void
    {
        $this->resetValidation();
        $this->reset(['categoria_id', 'nombre', 'descripcion', 'precio', 'activo', 'foto', 'imagenActual', 'ingredientesSeleccionados', 'productoId']);
        $this->activo = true;

        $this->openModal('producto-form');
    }

    public function abrirModalEditar(int $id): void
    {
        $producto = Producto::with('ingredientes')->findOrFail($id);

        $this->productoId = $producto->id;
        $this->categoria_id = $producto->categoria_id;
        $this->nombre = $producto->nombre;
        $this->descripcion = (string) $producto->descripcion;
        $this->precio = (string) $producto->precio;
        $this->activo = $producto->activo;
        $this->foto = null;
        $this->imagenActual = $producto->imagen;
        // pluck('id') saca solo los IDs de la coleccion de ingredientes ya asociados
        $this->ingredientesSeleccionados = $producto->ingredientes->pluck('id')->toArray();

        $this->resetValidation();
        $this->openModal('producto-form');
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        // Separamos los ingredientes del resto de los datos: no son una columna de la tabla
        // productos, sino una relacion muchos a muchos que se guarda aparte con sync()
        $ingredientes = $datos['ingredientesSeleccionados'];
        unset($datos['ingredientesSeleccionados'], $datos['foto']);

        // Si se subio una foto nueva, se guarda en storage/app/public/productos. Se
        // persiste la ruta COMPLETA desde public/ (prefijo "storage/"), asi el
        // componente que la muestra (x-foto-producto) hace un asset() directo sin
        // importar si la foto vino del panel o se puso a mano en public/images.
        // Si no se subio nada, el producto conserva la imagen que ya tenia
        if ($this->foto) {
            $datos['imagen'] = 'storage/'.$this->foto->store('productos', 'public');
        }

        $producto = Producto::updateOrCreate(['id' => $this->productoId], $datos);

        // sync() reemplaza la lista completa de ingredientes asociados por la nueva seleccion:
        // agrega los que faltan en la tabla pivot y quita los que ya no estan marcados
        $producto->ingredientes()->sync($ingredientes);

        $this->closeModal('producto-form');
        $this->resetPage();
    }

    public function confirmarEliminar(int $id): void
    {
        $this->productoAEliminar = $id;
        $this->openModal('producto-confirmar-eliminar');
    }

    public function eliminar(): void
    {
        Producto::findOrFail($this->productoAEliminar)->delete();
        $this->productoAEliminar = null;
        $this->closeModal('producto-confirmar-eliminar');
    }
}
