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
        // Estas 3 columnas se filtran todo el tiempo (dashboard, bandeja de
        // pedidos del admin, menu publico) y no tenian indice propio
        Schema::table('pedidos', function (Blueprint $table) {
            $table->index('estado');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->index('activo');
        });

        Schema::table('ingredientes', function (Blueprint $table) {
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropIndex(['estado']);
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex(['activo']);
        });

        Schema::table('ingredientes', function (Blueprint $table) {
            $table->dropIndex(['activo']);
        });
    }
};
