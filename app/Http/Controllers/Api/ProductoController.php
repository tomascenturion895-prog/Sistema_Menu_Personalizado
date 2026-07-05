<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Endpoint publico de solo lectura: el menu se puede consultar sin login,
 * igual que /menu en la version web (el login recien se exige al comprar).
 */
class ProductoController extends Controller
{
    /**
     * Lista los productos activos, con los mismos filtros que el menu web:
     * por dieta (tipo_dieta de la categoria), por categoria puntual y por
     * texto libre (nombre o descripcion).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $productos = Producto::query()
            ->where('activo', true)
            ->with('categoria')
            ->when($request->filled('categoria_id'), fn ($query) => $query->where('categoria_id', $request->integer('categoria_id')))
            ->when($request->filled('dieta'), fn ($query) => $query->whereHas('categoria', fn ($query) => $query->where('tipo_dieta', $request->string('dieta'))))
            ->when($request->filled('busqueda'), function ($query) use ($request) {
                $busqueda = $request->string('busqueda');
                $query->where(function ($query) use ($busqueda) {
                    $query->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(12);

        return ProductoResource::collection($productos);
    }

    /**
     * Detalle de un producto puntual, con sus ingredientes disponibles para personalizar.
     */
    public function show(Producto $producto): ProductoResource
    {
        // Un producto desactivado no es distinto de "no existe" para un visitante publico
        abort_unless($producto->activo, 404);

        return new ProductoResource($producto->load('categoria', 'ingredientes'));
    }
}
