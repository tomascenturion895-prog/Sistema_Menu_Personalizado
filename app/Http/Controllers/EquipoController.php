<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Controlador de la pagina publica "Equipo": muestra el perfil de cada
 * desarrollador (requisito de la consigna del proyecto). Es invokable
 * porque atiende una sola pagina de solo lectura, sin logica de negocio:
 * el "Modelo" aca es config/equipo.php en vez de una tabla de la base
 * de datos, porque son datos fijos del equipo, no contenido administrable.
 */
class EquipoController extends Controller
{
    public function __invoke(): View
    {
        return view('equipo', [
            'desarrolladores' => config('equipo'),
        ]);
    }
}
