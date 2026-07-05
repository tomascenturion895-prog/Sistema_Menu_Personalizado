<?php

namespace App\Http\Requests\Api;

use App\Livewire\Menu\Personalizar;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida el checkout por API: un array de items (producto + cantidad +
 * ingredientes elegidos), equivalente al carrito de sesion que arma la
 * version web antes de confirmar el pedido.
 */
class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // El usuario ya llega autenticado por auth:sanctum; cualquier cliente
        // logueado puede confirmar SU propio pedido
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1', 'max:'.Personalizar::MAX_CANTIDAD],
            'items.*.ingredientes_elegidos' => ['nullable', 'array'],
            'items.*.ingredientes_elegidos.*' => ['integer', 'exists:ingredientes,id'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
