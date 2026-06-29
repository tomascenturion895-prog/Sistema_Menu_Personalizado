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
        Schema::create('ingredientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['pan', 'medallon', 'topping', 'salsa', 'papas', 'bebida', 'extra']);
            $table->decimal('precio_extra', 8, 2)->default(0);
            $table->boolean('es_vegetariano')->default(false);
            $table->boolean('es_vegano')->default(false);
            $table->boolean('sin_gluten')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('ingrediente_producto', function (Blueprint $table) {
            $table->foreignId('ingrediente_id')->constrained('ingredientes')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->primary(['ingrediente_id', 'producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingrediente_producto');
        Schema::dropIfExists('ingredientes');
    }
};
