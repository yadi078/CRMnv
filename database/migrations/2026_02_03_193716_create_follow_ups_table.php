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
     * Run the migrations.
     * 
     * Tabla de seguimiento y alertas para empresas y contactos.
     * Sistema de bitácora de notas y alarmas programadas.
     * Permite programar llamadas, reuniones y cierres.
     */
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            
            // Relaciones (puede ser a empresa o contacto)
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade')->comment('Empresa relacionada (opcional)');
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->onDelete('cascade')->comment('Contacto relacionado (opcional)');
            
            // Tipo de acción programada
            $table->enum('tipo_accion', ['llamada', 'reunión', 'cierre'])->comment('Tipo de acción a realizar');
            
            // Fecha de alarma/programación
            $table->dateTime('fecha_alarma')->comment('Fecha y hora programada para la acción');
            
            // Bitácora de notas
            $table->longText('bitacora_notas')->nullable()->comment('Notas y observaciones del seguimiento');
            
            // Estado del seguimiento
            $table->boolean('completado')->default(false)->comment('Indica si la acción fue completada');
            $table->timestamp('completado_at')->nullable()->comment('Fecha de completado');
            
            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('Usuario que creó el seguimiento');
            $table->foreignId('asignado_a')->nullable()->constrained('users')->onDelete('set null')->comment('Usuario asignado para realizar la acción');
            
            $table->timestamps();
            $table->softDeletes()->comment('Borrado suave para mantener historial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
