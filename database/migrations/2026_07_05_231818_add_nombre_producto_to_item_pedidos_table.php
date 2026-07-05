<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Snapshot del nombre del producto AL MOMENTO DE LA COMPRA, igual que ya
        // se hace con precio_unitario e ingredientes_elegidos: si el admin
        // renombra un producto despues, el historial de pedidos viejos no
        // deberia mostrar retroactivamente el nombre nuevo.
        Schema::table('item_pedidos', function (Blueprint $table) {
            $table->string('nombre_producto')->nullable()->after('producto_id');
        });

        // Backfill de los pedidos que ya existian antes de esta columna: se usa
        // el nombre ACTUAL del producto como mejor aproximacion disponible.
        // Se hace fila por fila (no un UPDATE...JOIN) porque esa sintaxis no es
        // portable entre motores (funciona en MySQL, no en SQLite que usan los tests)
        $nombresPorProducto = DB::table('productos')->pluck('nombre', 'id');

        DB::table('item_pedidos')->select('id', 'producto_id')->orderBy('id')->chunk(200, function ($items) use ($nombresPorProducto): void {
            foreach ($items as $item) {
                DB::table('item_pedidos')
                    ->where('id', $item->id)
                    ->update(['nombre_producto' => $nombresPorProducto->get($item->producto_id, 'Producto eliminado')]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_pedidos', function (Blueprint $table) {
            $table->dropColumn('nombre_producto');
        });
    }
};
