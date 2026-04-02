<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Condiciones, modalidad, sede, fecha/horario del evento y referencia de factura (bloque amarillo de la ficha).
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->text('condiciones_pago')->nullable();
            $table->string('modalidad', 255)->nullable();
            $table->string('sede', 255)->nullable();
            $table->date('fecha_evento')->nullable();
            $table->string('horario_evento', 120)->nullable();
            $table->string('factura_referencia', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'condiciones_pago',
                'modalidad',
                'sede',
                'fecha_evento',
                'horario_evento',
                'factura_referencia',
            ]);
        });
    }
};
