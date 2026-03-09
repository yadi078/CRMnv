<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campos para ficha de registro del cliente (facturación / datos fiscales).
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('razon_social', 255)->nullable()->after('estado');
            $table->string('nombre_comercial', 255)->nullable()->after('razon_social');
            $table->string('calle_numero', 500)->nullable()->after('nombre_comercial');
            $table->string('colonia_cp', 255)->nullable()->after('calle_numero');
            $table->string('rfc', 20)->nullable()->after('colonia_cp');
            $table->string('regimen_fiscal', 255)->nullable()->after('rfc');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['razon_social', 'nombre_comercial', 'calle_numero', 'colonia_cp', 'rfc', 'regimen_fiscal']);
        });
    }
};
