<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriaResource;
use App\Models\Categoria;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Endpoint publico de solo lectura: cualquiera puede consultar las categorias
 * del menu, igual que en la version web (no requiere login).
 */
class CategoriaController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CategoriaResource::collection(
            Categoria::where('activo', true)->orderBy('nombre')->get()
        );
    }
}
