<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Controlador de la pagina de inicio: es UNA sola vista servida en dos rutas
 * ("/" para visitantes y "/inicio" para logueados), asi el contenido nunca
 * cambia entre estar logueado o no. En el patron MVC, este es el punto C:
 * recibe la request, consulta el Modelo (Producto, Pedido) y le entrega los
 * datos a la Vista (inicio.blade.php).
 */
class InicioController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('inicio', [
            // Seleccion de productos destacados que se muestran como "las mas pedidas"
            'destacados' => Producto::where('activo', true)
                ->with('categoria')
                ->take(3)
                ->get(),

            // Solo existe si hay un usuario logueado (operador ?-> evita el error
            // "call to a member function on null" cuando el visitante es un guest)
            'ultimoPedido' => $request->user()?->pedidos()->latest()->first(),
        ]);
    }
}
