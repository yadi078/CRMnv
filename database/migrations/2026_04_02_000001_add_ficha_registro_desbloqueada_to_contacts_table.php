<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indica si el usuario ya desplegó la sección «Generar ficha de registro» en editar contacto.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->boolean('ficha_registro_desbloqueada')->default(false)->after('regimen_fiscal');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('ficha_registro_desbloqueada');
        });
    }
};
