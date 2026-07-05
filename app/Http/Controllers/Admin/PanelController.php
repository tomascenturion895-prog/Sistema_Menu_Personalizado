<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Ingrediente;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Contracts\View\View;

/**
 * Controlador del dashboard del administrador.
 *
 * Antes esta logica vivia en un closure dentro de routes/web.php;
 * moverla aca respeta el patron MVC (las rutas rutean, no consultan).
 */
class PanelController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'totalCategorias' => Categoria::count(),
            'totalProductos' => Producto::count(),
            'totalIngredientes' => Ingrediente::count(),
            // Pedidos que requieren atencion de la cocina (aun no entregados ni cancelados)
            'pedidosPendientes' => Pedido::whereIn('estado', ['pendiente', 'confirmado', 'en_preparacion'])->count(),
        ]);
    }
}
