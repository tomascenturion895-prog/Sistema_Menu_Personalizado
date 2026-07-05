<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreProductoRequest;
use App\Http\Requests\Api\Admin\UpdateProductoRequest;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * CRUD de productos por API, equivalente al panel admin Livewire
 * (App\Livewire\Admin\Productos). La foto NO se maneja aca: se sigue
 * cargando a mano desde el panel Livewire (WithFileUploads), la API solo
 * gestiona los datos y la relacion con ingredientes.
 */
class ProductoController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProductoResource::collection(
            Producto::with('categoria')->orderBy('nombre')->paginate(15)
        );
    }

    public function store(StoreProductoRequest $request): ProductoResource
    {
        $datos = $request->validated();
        $ingredientes = $datos['ingredientes'] ?? [];
        unset($datos['ingredientes']);

        $producto = Producto::create($datos);
        $producto->ingredientes()->sync($ingredientes);

        return new ProductoResource($producto->load('categoria', 'ingredientes'));
    }

    public function show(Producto $producto): ProductoResource
    {
        return new ProductoResource($producto->load('categoria', 'ingredientes'));
    }

    public function update(UpdateProductoRequest $request, Producto $producto): ProductoResource
    {
        $datos = $request->validated();

        // sync() solo se llama si mandaron la clave "ingredientes": si no la mandan,
        // el producto conserva los ingredientes que ya tenia (update parcial real)
        if (array_key_exists('ingredientes', $datos)) {
            $producto->ingredientes()->sync($datos['ingredientes']);
            unset($datos['ingredientes']);
        }

        $producto->update($datos);

        return new ProductoResource($producto->load('categoria', 'ingredientes'));
    }

    public function destroy(Producto $producto): Response
    {
        $producto->delete();

        return response()->noContent();
    }
}
