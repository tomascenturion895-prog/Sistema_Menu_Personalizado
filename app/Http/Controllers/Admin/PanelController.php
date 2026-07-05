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
        // Un pedido cancelado no es una venta real: se excluye de todas las
        // metricas de facturacion (clone porque el query builder es mutable,
        // cada metrica necesita partir de la misma base sin arrastrar los
        // where() de la metrica anterior)
        $pedidosVendidos = Pedido::where('estado', '!=', 'cancelado');

        // Cancelaciones DE HOY: al admin le sirve saber que paso en el dia que
        // abre el dashboard, no un acumulado historico que nunca baja.
        // updated_at es el momento en que se cancelo (un pedido cancelado ya
        // no vuelve a cambiar de estado, ver Pedido::esCancelable())
        $pedidosCanceladosHoy = Pedido::where('estado', 'cancelado')->whereDate('updated_at', today())->count();
        $totalPedidosHoy = Pedido::whereDate('created_at', today())->count();

        return view('admin.dashboard', [
            'totalCategorias' => Categoria::count(),
            'totalProductos' => Producto::count(),
            'totalIngredientes' => Ingrediente::count(),
            // Pedidos que requieren atencion de la cocina (aun no entregados ni cancelados)
            'pedidosPendientes' => Pedido::whereIn('estado', ['pendiente', 'confirmado', 'en_preparacion'])->count(),

            // Metricas de ventas: lo que de verdad le importa al dueño del negocio
            'ventasTotales' => (clone $pedidosVendidos)->sum('total'),
            'ventasHoy' => (clone $pedidosVendidos)->whereDate('created_at', today())->sum('total'),
            'ventasSemana' => (clone $pedidosVendidos)->where('created_at', '>=', now()->startOfWeek())->sum('total'),
            'ticketPromedio' => (clone $pedidosVendidos)->avg('total') ?? 0,

            // Cancelaciones de HOY: cuanto se esta perdiendo/rechazando en el dia
            'pedidosCanceladosHoy' => $pedidosCanceladosHoy,
            'tasaCancelacionHoy' => $totalPedidosHoy > 0 ? round($pedidosCanceladosHoy / $totalPedidosHoy * 100, 1) : 0.0,
        ]);
    }
}
