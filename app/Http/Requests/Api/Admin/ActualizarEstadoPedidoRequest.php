<?php

namespace App\Http\Requests\Api\Admin;

use App\Models\Pedido;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActualizarEstadoPedidoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Lista blanca contra Pedido::ESTADOS, igual que el panel admin Livewire
            'estado' => ['required', Rule::in(array_keys(Pedido::ESTADOS))],
        ];
    }
}
