<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Corrige errata frecuente en ciudad (importaciones) para filtros y listados.
     */
    public function up(): void
    {
        foreach (['contacts', 'companies'] as $table) {
            DB::table($table)
                ->where('municipio', 'Aguascaliebtes')
                ->update(['municipio' => 'Aguascalientes']);
        }
    }

    public function down(): void
    {
        // Sin reversión automática: podría afectar registros ya correctos.
    }
};
