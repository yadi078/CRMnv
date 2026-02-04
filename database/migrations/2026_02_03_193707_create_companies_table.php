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
     * Tabla principal para almacenar información de empresas/prospectos.
     * Incluye validación de unicidad para RFC y nombre comercial.
     * Sistema de semáforo (verde, amarillo, rojo) para seguimiento de estatus.
     * Sistema de aprobación para registros creados por usuarios normales.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            
            // Información básica de la empresa
            $table->string('nombre_comercial')->unique()->comment('Nombre comercial único para validación de duplicados');
            $table->string('rfc', 13)->unique()->comment('RFC único con validación en backend');
            $table->text('sector')->nullable()->comment('Sector o giro empresarial (permite múltiples sectores)');
            $table->string('municipio')->nullable()->comment('Municipio para segmentación geográfica');
            $table->string('estado')->nullable()->comment('Estado para segmentación geográfica');
            
            // Asignación y seguimiento
            $table->string('ejecutivo_asignado')->nullable()->comment('Vendedor o ejecutivo asignado a la cuenta');
            
            // Datos fiscales
            $table->text('datos_fiscales')->nullable()->comment('Información completa para facturación');
            
            // Sistema de semáforo para seguimiento
            $table->enum('status_color', ['verde', 'amarillo', 'rojo'])->default('amarillo')->comment('Sistema visual de semáforo basado en última actividad');
            
            // Sistema de aprobación
            $table->enum('approval_status', ['pendiente', 'aprobado'])->default('pendiente')->comment('Estado de aprobación: pendiente para usuarios normales, aprobado por admin');
            
            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('Usuario que creó el registro');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->comment('Administrador que aprobó el registro');
            $table->timestamp('approved_at')->nullable()->comment('Fecha de aprobación');
            
            $table->timestamps();
            $table->softDeletes()->comment('Borrado suave para mantener historial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
