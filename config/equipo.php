<?php

// Perfiles del equipo de desarrollo, centralizados en un solo lugar (mismo
// criterio que config/negocio.php): la vista "Equipo" lee de aca, asi
// actualizar un rol o sumar un link no requiere tocar ningun Blade.
//
// github/linkedin en null: todavia no se cargaron las URLs reales. La vista
// se encarga de mostrar el icono "apagado" en vez de armar un link roto.
return [
    [
        'nombre' => 'Centurion Tomas',
        'rol' => 'Backend',
        'github' => null,
        'linkedin' => null,
    ],
    [
        'nombre' => 'Benitez Apolo',
        'rol' => 'Frontend',
        'github' => null,
        'linkedin' => null,
    ],
    [
        'nombre' => 'Benitez Antonia',
        'rol' => 'Base de datos',
        'github' => null,
        'linkedin' => null,
    ],
];
