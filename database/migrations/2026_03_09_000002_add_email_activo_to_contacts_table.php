<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Agrega el flag para activar/desactivar el correo del contacto.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->boolean('email_activo')
                ->default(true)
                ->after('email')
                ->comment('Si es falso, el correo no se muestra en fichas ni listados');
        });

        // Para contactos existentes, dejamos el correo activado por defecto.
        DB::table('contacts')->update(['email_activo' => true]);
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('email_activo');
        });
    }
};

