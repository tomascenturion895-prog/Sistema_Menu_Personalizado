<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Ingrediente;
use App\Models\Producto;
use Illuminate\Database\Seeder;

/**
 * Carga datos realistas de la hamburgueseria para demostrar el sistema:
 * categorias por dieta, ingredientes de cada tipo y productos con sus
 * ingredientes disponibles ya asociados.
 *
 * A proposito NO usa WithoutModelEvents: los ingredientes de aca abajo pasan
 * por Ingrediente::updateOrCreate(), y necesitamos que el hook booted()/saving()
 * (stock 0 => activo false) siga funcionando si algun ingrediente nuevo se
 * carga sin stock por error.
 */
class DatosDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ------------------------------------------------------------
        // INGREDIENTES, agrupados por tipo.
        // updateOrCreate evita duplicados si el seeder se corre dos veces:
        // busca por nombre+tipo, y si ya existe solo actualiza el resto.
        // ------------------------------------------------------------
        $ingredientes = [
            // Panes
            ['nombre' => 'Pan clásico', 'tipo' => 'pan', 'precio_extra' => 0, 'es_vegetariano' => true, 'es_vegano' => false, 'sin_gluten' => false],
            ['nombre' => 'Pan integral', 'tipo' => 'pan', 'precio_extra' => 300, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => false],
            ['nombre' => 'Pan sin TACC', 'tipo' => 'pan', 'precio_extra' => 500, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],

            // Medallones (la "cantidad de medallones" se elige como opcion: simple, doble o triple)
            ['nombre' => 'Simple (1 medallón de carne)', 'tipo' => 'medallon', 'precio_extra' => 0, 'es_vegetariano' => false, 'es_vegano' => false, 'sin_gluten' => true],
            ['nombre' => 'Doble (2 medallones de carne)', 'tipo' => 'medallon', 'precio_extra' => 1200, 'es_vegetariano' => false, 'es_vegano' => false, 'sin_gluten' => true],
            ['nombre' => 'Triple (3 medallones de carne)', 'tipo' => 'medallon', 'precio_extra' => 2200, 'es_vegetariano' => false, 'es_vegano' => false, 'sin_gluten' => true],
            ['nombre' => 'Medallón de garbanzos', 'tipo' => 'medallon', 'precio_extra' => 0, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],
            ['nombre' => 'Medallón de lentejas', 'tipo' => 'medallon', 'precio_extra' => 0, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],
            ['nombre' => 'NotBurger (plant based)', 'tipo' => 'medallon', 'precio_extra' => 800, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],

            // Toppings
            ['nombre' => 'Lechuga', 'tipo' => 'topping', 'precio_extra' => 0, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],
            ['nombre' => 'Tomate', 'tipo' => 'topping', 'precio_extra' => 0, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],
            ['nombre' => 'Cebolla morada', 'tipo' => 'topping', 'precio_extra' => 0, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],
            ['nombre' => 'Pepino', 'tipo' => 'topping', 'precio_extra' => 0, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],
            ['nombre' => 'Panceta crocante', 'tipo' => 'topping', 'precio_extra' => 700, 'es_vegetariano' => false, 'es_vegano' => false, 'sin_gluten' => true],
            ['nombre' => 'Queso cheddar', 'tipo' => 'topping', 'precio_extra' => 500, 'es_vegetariano' => true, 'es_vegano' => false, 'sin_gluten' => true],
            ['nombre' => 'Queso vegano', 'tipo' => 'topping', 'precio_extra' => 600, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],
            ['nombre' => 'Huevo frito', 'tipo' => 'topping', 'precio_extra' => 400, 'es_vegetariano' => true, 'es_vegano' => false, 'sin_gluten' => true],

            // Salsas
            ['nombre' => 'Mayonesa', 'tipo' => 'salsa', 'precio_extra' => 0, 'es_vegetariano' => true, 'es_vegano' => false, 'sin_gluten' => true],
            ['nombre' => 'Ketchup', 'tipo' => 'salsa', 'precio_extra' => 0, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],
            ['nombre' => 'Barbacoa', 'tipo' => 'salsa', 'precio_extra' => 200, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],
            ['nombre' => 'Alioli', 'tipo' => 'salsa', 'precio_extra' => 200, 'es_vegetariano' => true, 'es_vegano' => false, 'sin_gluten' => true],
            ['nombre' => 'Salsa Capa8 (secreta)', 'tipo' => 'salsa', 'precio_extra' => 300, 'es_vegetariano' => true, 'es_vegano' => false, 'sin_gluten' => true],

            // Papas
            ['nombre' => 'Papas tradicionales', 'tipo' => 'papas', 'precio_extra' => 0, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],
            ['nombre' => 'Papas con cheddar', 'tipo' => 'papas', 'precio_extra' => 900, 'es_vegetariano' => true, 'es_vegano' => false, 'sin_gluten' => true],
            ['nombre' => 'Papas con cheddar y panceta', 'tipo' => 'papas', 'precio_extra' => 1400, 'es_vegetariano' => false, 'es_vegano' => false, 'sin_gluten' => true],

            // Bebidas
            ['nombre' => 'Gaseosa cola', 'tipo' => 'bebida', 'precio_extra' => 800, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],
            ['nombre' => 'Agua mineral', 'tipo' => 'bebida', 'precio_extra' => 600, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],
            ['nombre' => 'Limonada casera', 'tipo' => 'bebida', 'precio_extra' => 900, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => true],
            ['nombre' => 'Cerveza artesanal', 'tipo' => 'bebida', 'precio_extra' => 1500, 'es_vegetariano' => true, 'es_vegano' => true, 'sin_gluten' => false],
        ];

        foreach ($ingredientes as $datos) {
            Ingrediente::updateOrCreate(
                ['nombre' => $datos['nombre'], 'tipo' => $datos['tipo']],
                // Stock de demostracion (50 unidades) salvo que el ingrediente ya
                // especifique el suyo propio: evita repetir "stock" en las 26 lineas de arriba
                [...$datos, 'stock' => $datos['stock'] ?? 50, 'activo' => true]
            );
        }

        // ------------------------------------------------------------
        // CATEGORIAS por tipo de dieta
        // ------------------------------------------------------------
        $clasicas = Categoria::updateOrCreate(
            ['nombre' => 'Clásicas'],
            ['descripcion' => 'Nuestras hamburguesas de carne de siempre.', 'tipo_dieta' => 'normal', 'activo' => true]
        );

        $vegetarianas = Categoria::updateOrCreate(
            ['nombre' => 'Vegetarianas'],
            ['descripcion' => 'Sin carne, con todo el sabor.', 'tipo_dieta' => 'vegetariano', 'activo' => true]
        );

        $veganas = Categoria::updateOrCreate(
            ['nombre' => 'Veganas'],
            ['descripcion' => '100% a base de plantas.', 'tipo_dieta' => 'vegano', 'activo' => true]
        );

        $sinTacc = Categoria::updateOrCreate(
            ['nombre' => 'Sin TACC'],
            ['descripcion' => 'Aptas para celíacos, con pan sin gluten.', 'tipo_dieta' => 'celiaco', 'activo' => true]
        );

        // ------------------------------------------------------------
        // PRODUCTOS con sus ingredientes disponibles.
        // La clave del array es el nombre del producto; los valores definen
        // su categoria, precio base y que tipos de ingrediente puede personalizar.
        // ------------------------------------------------------------
        $productos = [
            'La Clásica Capa8' => [
                'categoria' => $clasicas,
                'descripcion' => 'Carne, cheddar, lechuga, tomate y salsa Capa8. El commit inicial.',
                'precio' => 7500,
                'imagen' => 'la-clasica-capa8.jpg',
                // whereIn filtra los ingredientes aptos: esta acepta cualquier ingrediente no vegano
                'ingredientes' => Ingrediente::whereIn('tipo', ['pan', 'medallon', 'topping', 'salsa', 'papas', 'bebida'])
                    ->where(fn ($q) => $q->where('tipo', '!=', 'medallon')->orWhere('es_vegetariano', false))
                    ->pluck('id'),
            ],
            'Doble Deploy' => [
                'categoria' => $clasicas,
                'descripcion' => 'Doble medallón, doble cheddar, panceta. Para producción.',
                'precio' => 9800,
                'imagen' => 'doble-deploy.jpg',
                'ingredientes' => Ingrediente::whereIn('tipo', ['pan', 'medallon', 'topping', 'salsa', 'papas', 'bebida'])
                    ->where(fn ($q) => $q->where('tipo', '!=', 'medallon')->orWhere('es_vegetariano', false))
                    ->pluck('id'),
            ],
            'Veggie Refactor' => [
                'categoria' => $vegetarianas,
                'descripcion' => 'Medallón de garbanzos o lentejas, queso y huevo. Código limpio.',
                'precio' => 7200,
                'imagen' => 'veggie-refactor.jpg',
                // Solo ingredientes vegetarianos
                'ingredientes' => Ingrediente::where('es_vegetariano', true)->pluck('id'),
            ],
            'Vegan Mode On' => [
                'categoria' => $veganas,
                'descripcion' => 'NotBurger plant based con queso vegano. Sin dependencias animales.',
                'precio' => 8500,
                'imagen' => 'vegan-mode-on.jpg',
                // Solo ingredientes veganos
                'ingredientes' => Ingrediente::where('es_vegano', true)->pluck('id'),
            ],
            'Sin Gluten Sin Bugs' => [
                'categoria' => $sinTacc,
                'descripcion' => 'Pan sin TACC y medallón a elección. Testeada al 100%.',
                'precio' => 8200,
                'imagen' => 'sin-gluten-sin-bugs.jpg',
                // Solo ingredientes sin gluten
                'ingredientes' => Ingrediente::where('sin_gluten', true)->pluck('id'),
            ],
        ];

        foreach ($productos as $nombre => $datos) {
            // La foto es opcional: si el archivo todavia no existe en
            // public/images/productos (se van agregando de a poco, a mano),
            // el producto queda sin imagen y usa el fallback de marca de
            // <x-foto-producto>. Apenas se agrega el archivo y se re-corre el
            // seeder, la foto aparece sola, sin tocar una linea de codigo mas.
            $rutaImagen = 'images/productos/'.$datos['imagen'];
            $tieneFoto = file_exists(public_path($rutaImagen));

            $producto = Producto::updateOrCreate(
                ['nombre' => $nombre],
                [
                    'categoria_id' => $datos['categoria']->id,
                    'descripcion' => $datos['descripcion'],
                    'precio' => $datos['precio'],
                    'activo' => true,
                    'imagen' => $tieneFoto ? $rutaImagen : null,
                ]
            );

            // sync() asocia los ingredientes disponibles en la tabla pivot ingrediente_producto
            $producto->ingredientes()->sync($datos['ingredientes']);
        }
    }
}
