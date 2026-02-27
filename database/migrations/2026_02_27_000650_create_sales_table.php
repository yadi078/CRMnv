<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Tabla de historial de ventas / servicios adquiridos por empresa.
     * Registro de cursos y servicios vendidos para mantener el historial comercial.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')
                ->comment('Empresa que adquirió el servicio o curso');
            $table->string('nombre_servicio')->comment('Nombre del curso o servicio vendido');
            $table->date('fecha_venta')->comment('Fecha en que se realizó la venta');
            $table->decimal('monto', 12, 2)->nullable()->comment('Monto de la venta (opcional)');
            $table->unsignedInteger('participantes')->nullable()->comment('Número de participantes (opcional)');
            $table->text('notas')->nullable()->comment('Observaciones o notas adicionales');

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')
                ->comment('Usuario que registró la venta');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
