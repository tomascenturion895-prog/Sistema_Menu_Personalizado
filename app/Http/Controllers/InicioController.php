<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Services\Geocodificador;
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
    public function __invoke(Request $request, Geocodificador $geocodificador): View
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

            // Coordenadas reales de la direccion del negocio, para el mapa del footer.
            // Geocodificador es reusable: el dia que otra pantalla necesite
            // coordenadas (mapa del admin, distancia de envio, etc.) no duplica
            // la llamada a Nominatim ni el manejo de errores
            'ubicacion' => $geocodificador->ubicar(config('negocio.direccion').', '.config('negocio.ciudad')),
        ]);
    }
}
