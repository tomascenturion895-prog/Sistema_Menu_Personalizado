<?php

// Datos reales del local, centralizados en un solo lugar. Antes la direccion y el
// telefono estaban escritos a mano en 2 puntos distintos de welcome.blade.php;
// ahora cualquier vista (landing, footer del area logueada, etc.) lee de aca,
// asi un dato no puede quedar desactualizado en un lugar y actualizado en otro.
return [
    'nombre' => 'Capa8Burger',
    'direccion' => 'Av. 25 de Mayo 625',
    'ciudad' => 'Formosa, Formosa, Argentina',
    'horario_dias' => 'Martes a domingo',
    'horario_horas' => '19:30 — 00:30 hs',
    'telefono' => '(0362) 400-8080',
    'email' => 'hola@capa8burger.com',
    'instagram' => '@capa8burger',
];
