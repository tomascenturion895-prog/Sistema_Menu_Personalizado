<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\ConMensajeFlash;
use App\Livewire\Concerns\UsaPaginacionPropia;
use App\Models\Pedido;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Pedidos extends Component
{
    use ConMensajeFlash;
    use UsaPaginacionPropia;
    use WithPagination;

    // Filtro por estado, sincronizado con la URL (ej: /admin/pedidos?estado=pendiente)
    #[Url]
    public string $estado = 'todos';

    public function render()
    {
        return view('livewire.admin.pedidos', [
            // Eager loading anidado: user (quien pidio) + items con su producto.
            // 'items.producto' carga la relacion del item Y la del producto de cada item
            'pedidos' => Pedido::with(['user', 'items.producto'])
                ->when($this->estado !== 'todos', fn ($query) => $query->where('estado', $this->estado))
                ->latest() // equivale a orderBy('created_at', 'desc'): los mas nuevos primero
                ->paginate(10),
        ]);
    }

    public function filtrarPor(string $estado): void
    {
        $this->estado = $estado;
        $this->resetPage();
    }

    /**
     * Avanza el pedido al estado siguiente del flujo de preparacion.
     */
    public function cambiarEstado(int $pedidoId, string $nuevoEstado): void
    {
        // Validamos contra la lista blanca de estados del modelo para evitar valores arbitrarios
        if (! array_key_exists($nuevoEstado, Pedido::ESTADOS)) {
            return;
        }

        $pedido = Pedido::findOrFail($pedidoId);

        // Ademas de ser un estado valido, tiene que ser una transicion valida
        // desde el estado actual (ej. no se puede "revivir" uno cancelado)
        if (! $pedido->puedeTransicionarA($nuevoEstado)) {
            $this->mostrarMensajeFlash('Ese cambio de estado no es válido para este pedido.');

            return;
        }

        $pedido->update(['estado' => $nuevoEstado]);
    }
}
