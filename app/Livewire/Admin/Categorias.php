<?php

namespace App\Livewire\Admin;

use App\Models\Categoria;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

// El atributo #[Layout] envuelve la vista de este componente con layouts.app
// (el mismo layout que usa el dashboard de Breeze), para que se vea el menu de navegacion
#[Layout('layouts.app')]
class Categorias extends Component
{
    // WithPagination agrega el metodo paginate() y mantiene la pagina actual en la URL
    use WithPagination;

    // Campos del formulario. El atributo #[Validate] define las reglas de validacion
    // directamente sobre la propiedad, y Livewire las aplica automaticamente con $this->validate()
    #[Validate('required|string|max:255')]
    public string $nombre = '';

    #[Validate('nullable|string|max:1000')]
    public string $descripcion = '';

    #[Validate('required|in:normal,vegetariano,vegano,celiaco')]
    public string $tipo_dieta = 'normal';

    #[Validate('boolean')]
    public bool $activo = true;

    // Guarda el id de la categoria que se esta editando (null = modo "crear")
    public ?int $categoriaId = null;

    // Guarda el id de la categoria pendiente de eliminar (para el modal de confirmacion)
    public ?int $categoriaAEliminar = null;

    /**
     * render() se ejecuta en cada actualizacion del componente (cada click, cada input).
     * Acá traemos la lista actualizada de categorias paginada.
     */
    public function render()
    {
        return view('livewire.admin.categorias', [
            'categorias' => Categoria::orderBy('nombre')->paginate(8),
        ]);
    }

    /**
     * Abre el modal vacio, listo para crear una categoria nueva.
     */
    public function abrirModalCrear(): void
    {
        $this->resetValidation();
        $this->reset(['nombre', 'descripcion', 'tipo_dieta', 'activo', 'categoriaId']);
        $this->activo = true;

        // dispatch() envia un evento de navegador que el componente <x-modal name="categoria-form">
        // escucha con x-on:open-modal.window para mostrarse
        $this->dispatch('open-modal', 'categoria-form');
    }

    /**
     * Carga los datos de una categoria existente en el formulario y abre el modal.
     */
    public function abrirModalEditar(int $id): void
    {
        $categoria = Categoria::findOrFail($id);

        $this->categoriaId = $categoria->id;
        $this->nombre = $categoria->nombre;
        $this->descripcion = (string) $categoria->descripcion;
        $this->tipo_dieta = $categoria->tipo_dieta;
        $this->activo = $categoria->activo;

        $this->resetValidation();
        $this->dispatch('open-modal', 'categoria-form');
    }

    /**
     * Valida el formulario y crea o actualiza la categoria segun corresponda.
     */
    public function guardar(): void
    {
        // $this->validate() lee las reglas de los atributos #[Validate] de arriba
        // y lanza una excepcion automatica si algo no cumple, mostrando el error en la vista
        $datos = $this->validate();

        Categoria::updateOrCreate(
            ['id' => $this->categoriaId],
            $datos
        );

        // Cerramos el modal del formulario disparando el evento "close-modal"
        $this->dispatch('close-modal', 'categoria-form');
        $this->resetPage();
    }

    /**
     * Pide confirmacion antes de eliminar (abre un modal de confirmacion aparte).
     */
    public function confirmarEliminar(int $id): void
    {
        $this->categoriaAEliminar = $id;
        $this->dispatch('open-modal', 'categoria-confirmar-eliminar');
    }

    /**
     * Elimina la categoria confirmada.
     */
    public function eliminar(): void
    {
        Categoria::findOrFail($this->categoriaAEliminar)->delete();
        $this->categoriaAEliminar = null;
        $this->dispatch('close-modal', 'categoria-confirmar-eliminar');
    }
}
