<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campos de facturación que aparecen en la ficha final.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('colonia_cp', 255)->nullable()->after('notas');
            $table->string('regimen_fiscal', 255)->nullable()->after('colonia_cp');
            $table->string('forma_pago', 100)->nullable()->after('regimen_fiscal');
            $table->string('uso_cfdi', 100)->nullable()->after('forma_pago');
            $table->string('orden_compra', 100)->nullable()->after('uso_cfdi');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['colonia_cp', 'regimen_fiscal', 'forma_pago', 'uso_cfdi', 'orden_compra']);
        });
    }
};
