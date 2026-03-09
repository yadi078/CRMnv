<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cambia status_color de semáforo (verde/amarillo/rojo) a estados de prospecto:
     * seguimiento, interesado, si_le_interesa_nos_llaman_o_no_compro, vendido, no_estaba
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // 1. Ampliar enum para incluir nuevos valores (manteniendo los antiguos temporalmente)
            DB::statement("ALTER TABLE companies MODIFY COLUMN status_color ENUM(
                'verde', 'amarillo', 'rojo',
                'seguimiento', 'interesado', 'si_le_interesa_nos_llaman_o_no_compro', 'vendido', 'no_estaba'
            ) DEFAULT 'amarillo'");
        }

        // 2. Actualizar datos existentes (común para MySQL y SQLite)
        DB::table('companies')->where('status_color', 'verde')->update(['status_color' => 'seguimiento']);
        DB::table('companies')->where('status_color', 'amarillo')->update(['status_color' => 'si_le_interesa_nos_llaman_o_no_compro']);
        DB::table('companies')->where('status_color', 'rojo')->update(['status_color' => 'interesado']);

        if ($driver === 'mysql') {
            // 3. Reducir enum solo a los nuevos valores
            DB::statement("ALTER TABLE companies MODIFY COLUMN status_color ENUM(
                'seguimiento',
                'interesado',
                'si_le_interesa_nos_llaman_o_no_compro',
                'vendido',
                'no_estaba'
            ) DEFAULT 'seguimiento'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // Revertir a valores antiguos
        DB::table('companies')->whereIn('status_color', ['seguimiento'])->update(['status_color' => 'verde']);
        DB::table('companies')->whereIn('status_color', ['si_le_interesa_nos_llaman_o_no_compro'])->update(['status_color' => 'amarillo']);
        DB::table('companies')->whereIn('status_color', ['interesado', 'vendido', 'no_estaba'])->update(['status_color' => 'rojo']);

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE companies MODIFY COLUMN status_color ENUM('verde', 'amarillo', 'rojo') DEFAULT 'amarillo'");
        }
    }
};
