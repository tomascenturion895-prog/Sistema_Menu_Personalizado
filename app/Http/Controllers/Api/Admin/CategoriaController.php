<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreCategoriaRequest;
use App\Http\Requests\Api\Admin\UpdateCategoriaRequest;
use App\Http\Resources\CategoriaResource;
use App\Models\Categoria;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * CRUD de categorias por API, equivalente al panel admin Livewire
 * (App\Livewire\Admin\Categorias) pero pensado para consumo externo.
 * Protegido con auth:sanctum + admin en routes/api.php.
 */
class CategoriaController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CategoriaResource::collection(Categoria::orderBy('nombre')->paginate(15));
    }

    public function store(StoreCategoriaRequest $request): CategoriaResource
    {
        $categoria = Categoria::create($request->validated());

        return new CategoriaResource($categoria);
    }

    public function show(Categoria $categoria): CategoriaResource
    {
        return new CategoriaResource($categoria);
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria): CategoriaResource
    {
        $categoria->update($request->validated());

        return new CategoriaResource($categoria);
    }

    public function destroy(Categoria $categoria): Response
    {
        $categoria->delete();

        return response()->noContent();
    }
}
