<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Calle, RFC y correo guardados en la venta (relleno manual o copiados del contacto/empresa).
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('facturacion_calle_numero', 500)->nullable();
            $table->string('facturacion_rfc', 20)->nullable();
            $table->string('email_facturacion', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['facturacion_calle_numero', 'facturacion_rfc', 'email_facturacion']);
        });
    }
};
