<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->date('fecha_cumpleanos')->nullable()->after('email_activo')->comment('Fecha de cumpleaños para enviar felicitaciones al administrador');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('fecha_cumpleanos');
        });
    }
};
