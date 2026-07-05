<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\InteractsWithModals;
use App\Livewire\Concerns\UsaPaginacionPropia;
use App\Models\Ingrediente;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Ingredientes extends Component
{
    use InteractsWithModals;
    use UsaPaginacionPropia;
    use WithPagination;

    #[Validate('required|string|max:255')]
    public string $nombre = '';

    // La regla de este campo vive en rules() (abajo) porque necesita leer
    // Ingrediente::TIPOS, y los atributos #[Validate] solo aceptan constantes
    public string $tipo = 'topping';

    #[Validate('required|numeric|min:0')]
    public string $precio_extra = '0';

    #[Validate('required|integer|min:0')]
    public string $stock = '0';

    #[Validate('boolean')]
    public bool $es_vegetariano = false;

    #[Validate('boolean')]
    public bool $es_vegano = false;

    #[Validate('boolean')]
    public bool $sin_gluten = false;

    #[Validate('boolean')]
    public bool $activo = true;

    public ?int $ingredienteId = null;

    /**
     * Reglas dinamicas: Livewire las combina con los atributos #[Validate].
     * El tipo se valida contra la lista del modelo (unica fuente de verdad).
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in(array_keys(Ingrediente::TIPOS))],
        ];
    }

    public ?int $ingredienteAEliminar = null;

    public function render()
    {
        return view('livewire.admin.ingredientes', [
            'ingredientes' => Ingrediente::orderBy('tipo')->orderBy('nombre')->paginate(8),
        ]);
    }

    public function abrirModalCrear(): void
    {
        $this->resetValidation();
        $this->reset(['nombre', 'tipo', 'precio_extra', 'stock', 'es_vegetariano', 'es_vegano', 'sin_gluten', 'activo', 'ingredienteId']);
        $this->tipo = 'topping';
        $this->precio_extra = '0';
        $this->stock = '0';
        $this->activo = true;

        $this->openModal('ingrediente-form');
    }

    public function abrirModalEditar(int $id): void
    {
        $ingrediente = Ingrediente::findOrFail($id);

        $this->ingredienteId = $ingrediente->id;
        $this->nombre = $ingrediente->nombre;
        $this->tipo = $ingrediente->tipo;
        $this->precio_extra = (string) $ingrediente->precio_extra;
        $this->stock = (string) $ingrediente->stock;
        $this->es_vegetariano = $ingrediente->es_vegetariano;
        $this->es_vegano = $ingrediente->es_vegano;
        $this->sin_gluten = $ingrediente->sin_gluten;
        $this->activo = $ingrediente->activo;

        $this->resetValidation();
        $this->openModal('ingrediente-form');
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        Ingrediente::updateOrCreate(['id' => $this->ingredienteId], $datos);

        $this->closeModal('ingrediente-form');
        $this->resetPage();
    }

    public function confirmarEliminar(int $id): void
    {
        $this->ingredienteAEliminar = $id;
        $this->openModal('ingrediente-confirmar-eliminar');
    }

    public function eliminar(): void
    {
        // findOrFail sobre un id ya borrado (doble click en "Eliminar") lanza
        // ModelNotFoundException: se ignora en vez de mostrar una pantalla de error
        try {
            Ingrediente::findOrFail($this->ingredienteAEliminar)->delete();
        } catch (ModelNotFoundException) {
            //
        }

        $this->ingredienteAEliminar = null;
        $this->closeModal('ingrediente-confirmar-eliminar');
    }
}
