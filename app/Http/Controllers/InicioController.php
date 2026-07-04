<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Contracts\View\View;

/**
 * Controlador de la landing publica (la portada del sitio).
 *
 * Es "invokable": tiene un solo metodo __invoke() porque atiende una sola pagina.
 * En el patron MVC, este es el punto C: recibe la request, consulta el Modelo
 * (Producto) y le entrega los datos a la Vista (welcome.blade.php).
 */
class InicioController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            // Seleccion de productos destacados que la landing muestra como "las mas pedidas"
            'destacados' => Producto::where('activo', true)
                ->with('categoria')
                ->take(3)
                ->get(),
        ]);
    }
}
