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
        Schema::table('users', function (Blueprint $table) {
            // Nullable a nivel de columna porque ya existen usuarios (admin/cliente de
            // prueba) sin estos datos. La obligatoriedad real se exige en la validacion
            // del formulario de registro, no en la base de datos.
            $table->string('apellido')->nullable()->after('name');
            $table->string('telefono')->nullable()->after('email');
            $table->date('fecha_nacimiento')->nullable()->after('telefono');

            // Guarda CUANDO acepto los terminos (no un simple boolean), para tener
            // registro de la fecha de aceptacion si alguna vez hay que auditarlo
            $table->timestamp('terminos_aceptados_en')->nullable()->after('fecha_nacimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['apellido', 'telefono', 'fecha_nacimiento', 'terminos_aceptados_en']);
        });
    }
};
