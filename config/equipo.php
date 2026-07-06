<?php

// Perfiles del equipo de desarrollo, centralizados en un solo lugar (mismo
// criterio que config/negocio.php): la vista "Equipo" lee de aca, asi
// actualizar un rol o sumar un link no requiere tocar ningun Blade.
//
// github/linkedin en null: todavia no se cargaron las URLs reales. La vista
// se encarga de mostrar el icono "apagado" en vez de armar un link roto.
return [
    [
        'nombre' => 'Centurion Tomas Emanuel',
        'rol' => 'Backend',
        'foto' => 'Integrantes/Tomas.png',
        'foto_posicion' => 'object-top',
        'github' => 'https://github.com/tomascenturion895-prog',
        'linkedin' => 'https://www.linkedin.com/in/tomás-centurión-449a592b3?utm_source=share_via&utm_content=profile&utm_medium=member_android',
    ],
    [
        'nombre' => 'Benitez Apolo Salomon',
        'rol' => 'Frontend',
        'foto' => 'Integrantes/Apolo.png',
        'foto_posicion' => 'object-top',
        'github' => 'https://github.com/apolobenitez65-prog',
        'linkedin' => 'https://linkedin.com/in/apolo-benitez-a3b0053ab',
    ],
    [
        'nombre' => 'Benitez Antonia Dolores',
        'rol' => 'Base de datos',
        'foto' => 'Integrantes/Antonia.png',
        'foto_posicion' => 'object-center',
        'github' => 'https://github.com/antodbenitez97',
        'linkedin' => null,
    ],
];
