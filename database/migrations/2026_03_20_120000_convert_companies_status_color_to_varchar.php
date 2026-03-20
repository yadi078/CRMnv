<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Evita SQLSTATE 1265: MySQL trunca valores como "seguimiento" si status_color sigue siendo
 * ENUM('verde','amarillo','rojo') (p. ej. migración 2026_02_25 no aplicada en esta BD).
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'mysql') {
            return;
        }

        // Valores legacy del semáforo → estados de prospecto
        DB::table('companies')->where('status_color', 'verde')->update(['status_color' => 'seguimiento']);
        DB::table('companies')->where('status_color', 'amarillo')->update(['status_color' => 'si_le_interesa_nos_llaman_o_no_compro']);
        DB::table('companies')->where('status_color', 'rojo')->update(['status_color' => 'interesado']);

        DB::statement("ALTER TABLE companies MODIFY COLUMN status_color VARCHAR(50) NOT NULL DEFAULT 'seguimiento'");
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'mysql') {
            return;
        }

        DB::table('companies')->where('status_color', 'seguimiento')->update(['status_color' => 'verde']);
        DB::table('companies')->where('status_color', 'si_le_interesa_nos_llaman_o_no_compro')->update(['status_color' => 'amarillo']);
        DB::table('companies')->whereIn('status_color', ['interesado', 'vendido', 'no_estaba'])->update(['status_color' => 'rojo']);

        DB::statement("ALTER TABLE companies MODIFY COLUMN status_color ENUM('verde', 'amarillo', 'rojo') NOT NULL DEFAULT 'amarillo'");
    }
};
