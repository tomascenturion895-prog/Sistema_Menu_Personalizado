<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\InteractsWithModals;
use App\Models\Ingrediente;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Ingredientes extends Component
{
    use InteractsWithModals;
    use WithPagination;

    #[Validate('required|string|max:255')]
    public string $nombre = '';

    #[Validate('required|in:pan,medallon,topping,salsa,papas,bebida,extra')]
    public string $tipo = 'topping';

    #[Validate('required|numeric|min:0')]
    public string $precio_extra = '0';

    #[Validate('boolean')]
    public bool $es_vegetariano = false;

    #[Validate('boolean')]
    public bool $es_vegano = false;

    #[Validate('boolean')]
    public bool $sin_gluten = false;

    #[Validate('boolean')]
    public bool $activo = true;

    public ?int $ingredienteId = null;

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
        $this->reset(['nombre', 'tipo', 'precio_extra', 'es_vegetariano', 'es_vegano', 'sin_gluten', 'activo', 'ingredienteId']);
        $this->tipo = 'topping';
        $this->precio_extra = '0';
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
        Ingrediente::findOrFail($this->ingredienteAEliminar)->delete();
        $this->ingredienteAEliminar = null;
        $this->closeModal('ingrediente-confirmar-eliminar');
    }
}
