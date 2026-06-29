<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario administrador de prueba, para validar el middleware EsAdmin y el panel admin
        User::factory()->create([
            'name' => 'Admin Capa8Burger',
            'email' => 'admin@capa8burger.com',
            'rol' => 'admin',
        ]);

        // Usuario cliente de prueba, para validar el flujo normal de compra
        User::factory()->create([
            'name' => 'Cliente Prueba',
            'email' => 'cliente@capa8burger.com',
            'rol' => 'cliente',
        ]);
    }
}
