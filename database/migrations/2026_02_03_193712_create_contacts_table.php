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
     * Tabla de contactos vinculados a empresas.
     * Cada contacto pertenece obligatoriamente a una empresa.
     * Permite generar PDF de Ficha de Inscripción automáticamente.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            
            // Relación con empresa (obligatoria)
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->comment('Empresa a la que pertenece el contacto');
            
            // Información personal
            $table->string('nombre_completo')->comment('Nombre completo del contacto');
            $table->string('puesto_de_trabajo')->nullable()->comment('Puesto o cargo del contacto');
            $table->string('departamento')->nullable()->comment('Departamento al que pertenece');
            
            // Información de contacto
            $table->string('celular')->nullable()->comment('Número de celular');
            $table->string('extension')->nullable()->comment('Extensión telefónica');
            $table->string('email')->unique()->comment('Email único del contacto');
            
            // Ubicación (puede diferir de la empresa)
            $table->string('municipio')->nullable()->comment('Municipio del contacto');
            $table->string('estado')->nullable()->comment('Estado del contacto');
            
            // Información adicional
            $table->text('notas')->nullable()->comment('Notas adicionales sobre el contacto');
            
            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('Usuario que creó el registro');
            
            $table->timestamps();
            $table->softDeletes()->comment('Borrado suave para mantener historial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
