<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->string('extension', 20)->nullable()->after('description');
            $table->string('nombre_cliente')->nullable()->after('extension');
            $table->string('empresa')->nullable()->after('nombre_cliente');
            $table->string('correo_electronico')->nullable()->after('empresa');
            $table->string('numero_telefonico', 50)->nullable()->after('correo_electronico');
            $table->string('area')->nullable()->after('numero_telefonico');
            $table->string('puesto_trabajo')->nullable()->after('area');
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropColumn([
                'extension',
                'nombre_cliente',
                'empresa',
                'correo_electronico',
                'numero_telefonico',
                'area',
                'puesto_trabajo',
            ]);
        });
    }
};
