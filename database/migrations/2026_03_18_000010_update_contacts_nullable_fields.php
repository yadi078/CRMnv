<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hacer opcionales la mayoría de campos de contactos.
     * Solo se mantiene obligatorio: company_id y nombre_completo.
     */
    public function up(): void
    {
        // Importante: evitar Doctrine. Usamos ALTER TABLE directo.
        // 1) Quitar UNIQUE del email (si existe)
        try {
            DB::statement('ALTER TABLE contacts DROP INDEX contacts_email_unique');
        } catch (\Throwable $e) {
            // Si el índice ya no existe, ignoramos.
        }

        // 2) Hacer email nullable
        DB::statement('ALTER TABLE contacts MODIFY email VARCHAR(255) NULL');

        // 3) Asegurar que varios campos de contacto sean nullable
        DB::statement('ALTER TABLE contacts MODIFY puesto_de_trabajo VARCHAR(255) NULL');
        DB::statement('ALTER TABLE contacts MODIFY departamento VARCHAR(255) NULL');
        DB::statement('ALTER TABLE contacts MODIFY celular VARCHAR(255) NULL');
        DB::statement('ALTER TABLE contacts MODIFY telefono VARCHAR(255) NULL');
        DB::statement('ALTER TABLE contacts MODIFY extension VARCHAR(255) NULL');
        DB::statement('ALTER TABLE contacts MODIFY municipio VARCHAR(255) NULL');
        DB::statement('ALTER TABLE contacts MODIFY estado VARCHAR(255) NULL');
    }

    /**
     * Revertir cambios principales (en lo posible).
     */
    public function down(): void
    {
        // Revertir es opcional. Para evitar errores por diferencias de índices,
        // dejamos down vacío.
    }
};

