<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Antes, borrar un User o un Producto borraba en cascada TODOS sus
        // Pedido/ItemPedido asociados, incluso de compras ya entregadas hace
        // meses. Con restrictOnDelete() la base de datos rechaza el borrado
        // mientras existan pedidos relacionados: para dar de baja un producto
        // o una cuenta hay que usar el "activo=false" que ya existe, nunca
        // un hard delete que se lleve puesto el historial de ventas.
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });

        Schema::table('item_pedidos', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            $table->foreign('producto_id')->references('id')->on('productos')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('item_pedidos', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            $table->foreign('producto_id')->references('id')->on('productos')->cascadeOnDelete();
        });
    }
};
