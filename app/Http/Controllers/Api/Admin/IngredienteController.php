<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreIngredienteRequest;
use App\Http\Requests\Api\Admin\UpdateIngredienteRequest;
use App\Http\Resources\IngredienteResource;
use App\Models\Ingrediente;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * CRUD de ingredientes por API, equivalente al panel admin Livewire
 * (App\Livewire\Admin\Ingredientes).
 */
class IngredienteController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return IngredienteResource::collection(
            Ingrediente::orderBy('tipo')->orderBy('nombre')->paginate(15)
        );
    }

    public function store(StoreIngredienteRequest $request): IngredienteResource
    {
        $ingrediente = Ingrediente::create($request->validated());

        return new IngredienteResource($ingrediente);
    }

    public function show(Ingrediente $ingrediente): IngredienteResource
    {
        return new IngredienteResource($ingrediente);
    }

    public function update(UpdateIngredienteRequest $request, Ingrediente $ingrediente): IngredienteResource
    {
        $ingrediente->update($request->validated());

        return new IngredienteResource($ingrediente);
    }

    public function destroy(Ingrediente $ingrediente): Response
    {
        $ingrediente->delete();

        return response()->noContent();
    }
}
